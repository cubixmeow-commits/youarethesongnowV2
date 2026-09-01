<?php

declare(strict_types=1);

namespace Yatsn\Generation;

use Yatsn\AI\AdapterFactory;
use Yatsn\Credits\CreditService;
use Yatsn\Portraits\PortraitService;
use Yatsn\Storage\LocalStorage;
use Yatsn\Styles\StyleService;
use Yatsn\Support\Audit;
use Yatsn\Support\Config;
use Yatsn\Support\Database;
use Yatsn\Support\GateService;
use Yatsn\Support\Security;

final class GenerationJobService
{
    public static function submit(int $userId, string $draftPublicId, string $idempotencyKey): array
    {
        if ($idempotencyKey === '') {
            throw new \InvalidArgumentException('idempotency_required');
        }

        $existing = Database::one(
            'SELECT * FROM generation_jobs WHERE user_id = :uid AND idempotency_key = :k',
            ['uid' => $userId, 'k' => $idempotencyKey]
        );
        if ($existing !== null) {
            return self::public($existing, $userId);
        }

        $active = Database::one(
            'SELECT id FROM generation_jobs WHERE user_id = :uid AND status IN (\'queued\', \'generating\') LIMIT 1',
            ['uid' => $userId]
        );
        if ($active !== null) {
            throw new \RuntimeException('active_job_exists');
        }

        $draft = DraftService::findOwned($userId, $draftPublicId);
        if ($draft === null) {
            throw new \RuntimeException('not_found');
        }
        $ready = DraftService::validateReady($draft, $userId);
        if (!$ready['ok']) {
            throw new \RuntimeException('draft_not_ready');
        }
        if (DraftService::requiresMembership($userId)) {
            throw new \RuntimeException('membership_required');
        }
        GateService::refreshDiskGate();
        GateService::assertGenerationAllowed();

        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user && in_array($user['account_state'], ['grace', 'inactive', 'restricted'], true) && $user['role'] !== 'owner') {
            throw new \RuntimeException('generation_blocked');
        }

        $lookup = Database::one('SELECT * FROM song_lookups WHERE id = :id', ['id' => $draft['song_lookup_id']]);
        $style = Database::one('SELECT * FROM styles WHERE id = :id', ['id' => $draft['style_id']]);
        $portraitIds = json_decode((string) $draft['portrait_ids_json'], true) ?: [];
        $creditCost = CreditService::priceForQuality($draft['quality']);
        $jobPublicId = opaque_id();
        $derivedAnalysis = json_decode((string) ($lookup['derived_analysis_json'] ?? ''), true);

        $snapshot = [
            'draftId' => $draft['public_id'],
            'artist' => $lookup['artist_text'],
            'title' => $lookup['title_text'],
            'lookupState' => $lookup['state'],
            'classification' => $lookup['classification'],
            'styleId' => $style['public_id'],
            'styleName' => $style['name'],
            'styleKey' => $style['style_key'],
            'quality' => $draft['quality'],
            'orientation' => $draft['orientation'],
            'noTextInImage' => (bool) $draft['no_text_in_image'],
            'specialInstructions' => $draft['special_instructions'],
            'portraitIds' => $portraitIds,
            'portraitCount' => count($portraitIds),
        ];
        if (is_array($derivedAnalysis) && $derivedAnalysis !== []) {
            $snapshot['derivedSongAnalysis'] = $derivedAnalysis;
            $snapshot['songAnalysisBasis'] = (string) ($lookup['analysis_basis'] ?? 'v1-model-analysis');
            $snapshot['songAnalysisProvider'] = (string) ($lookup['analysis_provider'] ?? 'gemini-search');
        }

        Database::begin();
        try {
            $reservation = CreditService::reserve(
                $userId,
                $creditCost,
                $jobPublicId,
                'reserve-' . $idempotencyKey
            );

            Database::exec(
                'INSERT INTO generation_jobs (
                    public_id, user_id, draft_id, status, quality, orientation, style_id, no_text_in_image,
                    special_instructions, credit_cost, reservation_ledger_id, snapshot_json, progress_stage,
                    idempotency_key, created_at, updated_at
                 ) VALUES (
                    :pid, :uid, :did, \'queued\', :q, :o, :sid, :nt, :sp, :cost, :rid, :snap, :stage, :idem, :c, :u
                 )',
                [
                    'pid' => $jobPublicId,
                    'uid' => $userId,
                    'did' => $draft['id'],
                    'q' => $draft['quality'],
                    'o' => $draft['orientation'],
                    'sid' => $draft['style_id'],
                    'nt' => (int) $draft['no_text_in_image'],
                    'sp' => $draft['special_instructions'],
                    'cost' => $creditCost,
                    'rid' => $reservation['_internalId'],
                    'snap' => json_encode($snapshot, JSON_THROW_ON_ERROR),
                    'stage' => 'Finding the heart of your song',
                    'idem' => $idempotencyKey,
                    'c' => now_utc(),
                    'u' => now_utc(),
                ]
            );
            DraftService::lock((int) $draft['id']);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        Audit::record($userId, 'generation.submitted', 'generation_job', $jobPublicId);
        $row = Database::one('SELECT * FROM generation_jobs WHERE public_id = :pid', ['pid' => $jobPublicId]);
        return self::public($row, $userId);
    }

    public static function getOwned(int $userId, string $publicId): array
    {
        $row = Database::one(
            'SELECT * FROM generation_jobs WHERE public_id = :pid AND user_id = :uid',
            ['pid' => $publicId, 'uid' => $userId]
        );
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        return self::public($row, $userId);
    }

    public static function listOwned(int $userId): array
    {
        $rows = Database::all(
            'SELECT * FROM generation_jobs WHERE user_id = :uid ORDER BY id DESC LIMIT 50',
            ['uid' => $userId]
        );
        return array_map(static fn(array $r): array => self::public($r, $userId), $rows);
    }

    public static function processNext(): ?array
    {
        $lockToken = opaque_id();
        if (!self::acquireWorkerLock($lockToken)) {
            return ['skipped' => true, 'reason' => 'lock_held'];
        }

        try {
            self::requeueExpiredLeases();
            $job = Database::one(
                'SELECT * FROM generation_jobs WHERE status = \'queued\' ORDER BY id ASC LIMIT 1'
            );
            if ($job === null) {
                return ['processed' => false];
            }

            // Covers the bounded text, image-generation, and image-download timeouts.
            $lease = gmdate('Y-m-d\TH:i:s\Z', time() + 240);
            $claimed = Database::exec(
                'UPDATE generation_jobs SET status = \'generating\', worker_token = :wt, lease_expires_at = :lease,
                    progress_stage = :stage, attempt_count = attempt_count + 1, updated_at = :u
                 WHERE id = :id AND status = \'queued\'',
                [
                    'wt' => $lockToken,
                    'lease' => $lease,
                    'stage' => 'Building your cinematic world',
                    'u' => now_utc(),
                    'id' => $job['id'],
                ]
            );
            if ($claimed === 0) {
                return ['processed' => false];
            }

            $job = Database::one('SELECT * FROM generation_jobs WHERE id = :id', ['id' => $job['id']]);
            return self::runJob($job);
        } finally {
            self::releaseWorkerLock($lockToken);
        }
    }

    private static function runJob(array $job): array
    {
        $snapshot = json_decode((string) $job['snapshot_json'], true) ?: [];
        $attemptPublicId = opaque_id();
        $snapshot['userId'] = (int) $job['user_id'];
        $creativeChain = AdapterFactory::creativeRetryChain();
        $creative = $creativeChain[0];
        $imageChain = AdapterFactory::imageRetryChain();
        $primaryImage = $imageChain[0];

        Database::exec(
            'INSERT INTO generation_attempts (public_id, job_id, attempt_number, adapter_name, status, started_at)
             VALUES (:pid, :jid, :n, :a, \'running\', :s)',
            [
                'pid' => $attemptPublicId,
                'jid' => $job['id'],
                'n' => (int) $job['attempt_count'],
                'a' => $creative->name() . '+' . $primaryImage->name(),
                's' => now_utc(),
            ]
        );

        try {
            // Force-fail path for tests: special instructions containing FORCE_FAIL_FINAL
            if (str_contains((string) ($snapshot['specialInstructions'] ?? ''), 'FORCE_FAIL_FINAL')) {
                throw new \RuntimeException('forced_provider_failure');
            }
            $forceRetryOnce = str_contains((string) ($snapshot['specialInstructions'] ?? ''), 'FORCE_FAIL');

            Database::exec(
                'UPDATE generation_jobs SET progress_stage = :s, updated_at = :u WHERE id = :id',
                ['s' => 'Bringing you into the scene', 'u' => now_utc(), 'id' => $job['id']]
            );

            $package = null;
            $usedCreative = null;
            $creativeError = null;
            foreach ($creativeChain as $index => $creativeAdapter) {
                try {
                    $package = $creativeAdapter->buildPackage($snapshot);
                    $usedCreative = $creativeAdapter->name();
                    break;
                } catch (\Throwable $e) {
                    $creativeError = $e;
                    Database::exec(
                        'INSERT INTO generation_attempts (public_id, job_id, attempt_number, adapter_name, status, error_class, started_at, finished_at)
                         VALUES (:pid, :jid, :n, :a, \'failed\', :e, :s, :f)',
                        [
                            'pid' => opaque_id(),
                            'jid' => $job['id'],
                            'n' => ((int) $job['attempt_count']) + $index + 1,
                            'a' => $creativeAdapter->name(),
                            'e' => Security::redact($e->getMessage()),
                            's' => now_utc(),
                            'f' => now_utc(),
                        ]
                    );
                }
            }
            if ($package === null) {
                throw $creativeError ?? new \RuntimeException('creative_provider_failure');
            }
            Database::exec(
                'UPDATE generation_attempts SET adapter_name = :a WHERE public_id = :pid',
                ['a' => ($usedCreative ?? $creative->name()) . '+' . $primaryImage->name(), 'pid' => $attemptPublicId]
            );
            $creativeCost = max(0, (int) ($package['costCents'] ?? 0));
            if ($creativeCost > 0) {
                Database::exec(
                    'INSERT INTO provider_costs (public_id, user_id, job_public_id, adapter_name, stage, cost_cents, created_at)
                     VALUES (:pid, :uid, :job, :a, \'creative\', :cents, :c)',
                    [
                        'pid' => opaque_id(),
                        'uid' => (int) $job['user_id'],
                        'job' => $job['public_id'],
                        'a' => $usedCreative ?? $creative->name(),
                        'cents' => $creativeCost,
                        'c' => now_utc(),
                    ]
                );
                GateService::assertGenerationAllowed();
            }
            $planningTrace = \Yatsn\CreativeEngine\VisualNarrative\VisualNarrativePlanningService::sanitizedTrace($package);
            Database::exec(
                'INSERT INTO song_dna_artifacts (public_id, job_id, schema_version, dna_json, narrative_json, portrait_plan_json, stylemap_json, compiled_prompt_safe, planning_trace_json, created_at)
                 VALUES (:pid, :jid, :sv, :dna, :nar, :pp, :sm, :prompt, :plan, :c)',
                [
                    'pid' => opaque_id(),
                    'jid' => $job['id'],
                    'sv' => $package['dna']['schemaVersion'],
                    'dna' => json_encode($package['dna'], JSON_THROW_ON_ERROR),
                    'nar' => json_encode($package['narrative'], JSON_THROW_ON_ERROR),
                    'pp' => json_encode($package['portraitPlan'], JSON_THROW_ON_ERROR),
                    'sm' => json_encode($package['styleMap'], JSON_THROW_ON_ERROR),
                    'prompt' => Security::redact($package['compiledPromptSafe']),
                    'plan' => $planningTrace !== [] ? json_encode($planningTrace, JSON_THROW_ON_ERROR) : null,
                    'c' => now_utc(),
                ]
            );

            Database::exec(
                'UPDATE generation_jobs SET progress_stage = :s, updated_at = :u WHERE id = :id',
                ['s' => 'Adding the final details', 'u' => now_utc(), 'id' => $job['id']]
            );

            $image = null;
            $usedAdapter = null;
            $lastError = null;
            foreach ($imageChain as $index => $imageAdapter) {
                try {
                    if ($forceRetryOnce && $index === 0 && count($imageChain) > 1) {
                        throw new \RuntimeException('forced_provider_retry');
                    }
                    if ($forceRetryOnce && count($imageChain) === 1 && str_contains((string) ($snapshot['specialInstructions'] ?? ''), 'FORCE_FAIL')) {
                        // Single-adapter environments: treat FORCE_FAIL as final failure for credit-release tests.
                        throw new \RuntimeException('forced_provider_failure');
                    }
                    $image = $imageAdapter->generate($package, $snapshot);
                    $usedAdapter = $imageAdapter->name();
                    break;
                } catch (\Throwable $e) {
                    $lastError = $e;
                    Database::exec(
                        'INSERT INTO generation_attempts (public_id, job_id, attempt_number, adapter_name, status, error_class, started_at, finished_at)
                         VALUES (:pid, :jid, :n, :a, \'failed\', :e, :s, :f)',
                        [
                            'pid' => opaque_id(),
                            'jid' => $job['id'],
                            'n' => ((int) $job['attempt_count']) + $index + 1,
                            'a' => $imageAdapter->name(),
                            'e' => Security::redact($e->getMessage()),
                            's' => now_utc(),
                            'f' => now_utc(),
                        ]
                    );
                }
            }
            if ($image === null) {
                throw $lastError ?? new \RuntimeException('provider_failure');
            }

            $imagePublicId = opaque_id();
            $userId = (int) $job['user_id'];
            $storageKey = 'images/' . $userId . '/' . $imagePublicId . '.jpg';
            $displayKey = 'images/' . $userId . '/' . $imagePublicId . '_display.jpg';
            $thumbKey = 'images/' . $userId . '/' . $imagePublicId . '_thumb.jpg';
            LocalStorage::put($storageKey, $image['bytes']);
            LocalStorage::put($displayKey, $image['bytes']);
            LocalStorage::put($thumbKey, self::makeThumb($image['bytes']));

            Database::begin();
            try {
                Database::exec(
                    'INSERT INTO generated_images (
                        public_id, user_id, job_id, storage_key, display_key, thumb_key, mime_type,
                        width, height, byte_size, artist_label, title_label, style_name, orientation, quality, created_at
                     ) VALUES (
                        :pid, :uid, :jid, :sk, :dk, :tk, :mime, :w, :h, :b, :artist, :title, :style, :o, :q, :c
                     )',
                    [
                        'pid' => $imagePublicId,
                        'uid' => $userId,
                        'jid' => $job['id'],
                        'sk' => $storageKey,
                        'dk' => $displayKey,
                        'tk' => $thumbKey,
                        'mime' => $image['mime'],
                        'w' => $image['width'],
                        'h' => $image['height'],
                        'b' => strlen($image['bytes']),
                        'artist' => $snapshot['artist'],
                        'title' => $snapshot['title'],
                        'style' => $snapshot['styleName'],
                        'o' => $snapshot['orientation'],
                        'q' => $snapshot['quality'],
                        'c' => now_utc(),
                    ]
                );
                $imageRowId = (int) Database::lastInsertId();
                CreditService::capture(
                    $userId,
                    (int) $job['credit_cost'],
                    $job['public_id'],
                    'capture-' . $job['idempotency_key']
                );
                Database::exec(
                    'UPDATE generation_jobs SET status = \'completed\', generated_image_id = :gid, progress_stage = NULL,
                        completed_at = :c, updated_at = :u, worker_token = NULL, lease_expires_at = NULL
                     WHERE id = :id',
                    ['gid' => $imageRowId, 'c' => now_utc(), 'u' => now_utc(), 'id' => $job['id']]
                );
                Database::exec(
                    'UPDATE generation_attempts SET status = \'succeeded\', cost_cents = :cost, finished_at = :f
                     WHERE public_id = :pid',
                    ['cost' => $image['costCents'], 'f' => now_utc(), 'pid' => $attemptPublicId]
                );
                Database::exec(
                    'INSERT INTO provider_costs (public_id, user_id, job_public_id, adapter_name, stage, cost_cents, created_at)
                     VALUES (:pid, :uid, :job, :a, \'image\', :cents, :c)',
                    [
                        'pid' => opaque_id(),
                        'uid' => $userId,
                        'job' => $job['public_id'],
                        'a' => $usedAdapter ?? $primaryImage->name(),
                        'cents' => $image['costCents'],
                        'c' => now_utc(),
                    ]
                );
                Database::commit();
            } catch (\Throwable $e) {
                Database::rollBack();
                throw $e;
            }

            return ['processed' => true, 'jobId' => $job['public_id'], 'status' => 'completed'];
        } catch (\Throwable $e) {
            self::failJob($job, $attemptPublicId, 'provider_failure');
            return ['processed' => true, 'jobId' => $job['public_id'], 'status' => 'failed', 'error' => Security::redact($e->getMessage())];
        }
    }

    public static function recreateDraftFromImage(int $userId, string $imagePublicId): array
    {
        $image = Database::one(
            'SELECT gi.*, gj.snapshot_json, gj.draft_id
             FROM generated_images gi
             INNER JOIN generation_jobs gj ON gj.id = gi.job_id
             WHERE gi.public_id = :pid AND gi.user_id = :uid AND gi.deleted_at IS NULL',
            ['pid' => $imagePublicId, 'uid' => $userId]
        );
        if ($image === null) {
            throw new \RuntimeException('not_found');
        }
        $snapshot = json_decode((string) $image['snapshot_json'], true) ?: [];
        $lookup = SongLookupService::create(
            $userId,
            (string) ($snapshot['artist'] ?? $image['artist_label']),
            (string) ($snapshot['title'] ?? $image['title_label'])
        );
        $styleId = $snapshot['styleId'] ?? null;
        if (!$styleId) {
            $style = Database::one('SELECT public_id FROM styles WHERE name = :n LIMIT 1', ['n' => $image['style_name']]);
            $styleId = $style['public_id'] ?? null;
        }
        return DraftService::create($userId, [
            'songLookupId' => $lookup['id'],
            'portraitIds' => $snapshot['portraitIds'] ?? [],
            'styleId' => $styleId,
            'quality' => $snapshot['quality'] ?? $image['quality'],
            'orientation' => $snapshot['orientation'] ?? $image['orientation'],
            'noTextInImage' => !empty($snapshot['noTextInImage']),
            'specialInstructions' => $snapshot['specialInstructions'] ?? null,
        ]);
    }

    private static function failJob(array $job, string $attemptPublicId, string $code): void
    {
        Database::begin();
        try {
            if (!(int) $job['credits_released']) {
                CreditService::release(
                    (int) $job['user_id'],
                    (int) $job['credit_cost'],
                    $job['public_id'],
                    'release-' . $job['idempotency_key']
                );
            }
            Database::exec(
                'UPDATE generation_jobs SET status = \'failed\', failure_code = :code, credits_released = 1,
                    progress_stage = NULL, completed_at = :c, updated_at = :u, worker_token = NULL, lease_expires_at = NULL
                 WHERE id = :id',
                ['code' => $code, 'c' => now_utc(), 'u' => now_utc(), 'id' => $job['id']]
            );
            Database::exec(
                'UPDATE generation_attempts SET status = \'failed\', error_class = :e, finished_at = :f WHERE public_id = :pid',
                ['e' => $code, 'f' => now_utc(), 'pid' => $attemptPublicId]
            );
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }
        Audit::record((int) $job['user_id'], 'generation.failed', 'generation_job', $job['public_id'], $code);
    }

    private static function acquireWorkerLock(string $token): bool
    {
        $now = now_utc();
        $existing = Database::one('SELECT * FROM worker_locks WHERE name = \'generation\'');
        if ($existing !== null && $existing['expires_at'] > $now) {
            return false;
        }
        $expires = gmdate('Y-m-d\TH:i:s\Z', time() + 240);
        if ($existing === null) {
            Database::exec(
                'INSERT INTO worker_locks (name, owner_token, expires_at, updated_at) VALUES (\'generation\', :t, :e, :u)',
                ['t' => $token, 'e' => $expires, 'u' => $now]
            );
            return true;
        }
        $updated = Database::exec(
            'UPDATE worker_locks SET owner_token = :t, expires_at = :e, updated_at = :u
             WHERE name = \'generation\' AND (expires_at <= :now OR owner_token = :t)',
            ['t' => $token, 'e' => $expires, 'u' => $now, 'now' => $now]
        );
        return $updated > 0;
    }

    private static function releaseWorkerLock(string $token): void
    {
        Database::exec(
            'DELETE FROM worker_locks WHERE name = \'generation\' AND owner_token = :t',
            ['t' => $token]
        );
    }

    private static function requeueExpiredLeases(): void
    {
        Database::exec(
            'UPDATE generation_jobs SET status = \'queued\', worker_token = NULL, lease_expires_at = NULL, updated_at = :u
             WHERE status = \'generating\' AND lease_expires_at IS NOT NULL AND lease_expires_at < :now',
            ['u' => now_utc(), 'now' => now_utc()]
        );
    }

    private static function makeThumb(string $jpeg): string
    {
        $src = @imagecreatefromstring($jpeg);
        if (!$src instanceof \GdImage) {
            return $jpeg;
        }
        $w = imagesx($src);
        $h = imagesy($src);
        $max = 320;
        $scale = min(1.0, $max / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $dst = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        ob_start();
        imagejpeg($dst, null, 85);
        $out = (string) ob_get_clean();
        return $out;
    }

    public static function public(array $job, int $userId): array
    {
        $imageId = null;
        if ($job['generated_image_id']) {
            $img = Database::one('SELECT public_id FROM generated_images WHERE id = :id', ['id' => $job['generated_image_id']]);
            $imageId = $img['public_id'] ?? null;
        }
        $out = [
            'id' => $job['public_id'],
            'status' => $job['status'],
            'progressStage' => $job['progress_stage'],
            'quality' => $job['quality'],
            'orientation' => $job['orientation'],
            'creditCost' => (int) $job['credit_cost'],
            'generatedImageId' => $imageId,
            'failureCode' => $job['failure_code'],
            'creditsReturned' => (bool) $job['credits_released'] && $job['status'] === 'failed',
            'createdAt' => $job['created_at'],
            'updatedAt' => $job['updated_at'],
            'completedAt' => $job['completed_at'],
        ];
        if ($job['status'] === 'failed' && $job['credits_released']) {
            $out['message'] = 'We could not deliver a usable image. Your credits were returned. You can try again.';
        }
        return $out;
    }
}
