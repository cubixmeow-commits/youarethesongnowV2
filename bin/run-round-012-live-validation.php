#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Round 012.2 Live POV Validation harness.
 *
 * Budget: max 5 Gemini planning calls + 4 Gemini image generations.
 * Defaults to dry-run. Pass --live to execute provider calls when credentials exist.
 *
 * Usage:
 *   php bin/run-round-012-live-validation.php
 *   php bin/run-round-012-live-validation.php --live
 *   php bin/run-round-012-live-validation.php --live --pass planning
 *   php bin/run-round-012-live-validation.php --live --pass images
 *
 * Never prints API keys, raw lyrics, portrait bytes, or unsanitized provider bodies.
 */

$root = dirname(__DIR__);
require $root . '/app/bootstrap.php';
require $root . '/tests/fixtures/visual-narrative-fixtures.php';

use Yatsn\AI\CreativePackageBuilder;
use Yatsn\AI\GeminiImageAdapter;
use Yatsn\Auth\AuthService;
use Yatsn\CreativeEngine\VisualNarrative\DirectionRanker;
use Yatsn\CreativeEngine\VisualNarrative\GeminiVisualNarrativePlanner;
use Yatsn\CreativeEngine\VisualNarrative\VisualNarrativeContracts;
use Yatsn\CreativeEngine\VisualNarrative\VisualNarrativePlanningService;
use Yatsn\Portraits\PortraitService;
use Yatsn\Support\Config;
use Yatsn\Support\Database;
use Yatsn\Support\Migrator;
use Yatsn\Styles\StyleService;

const MAX_PLANNING_CALLS = 5;
const MAX_IMAGE_CALLS = 4;
const IMAGE_FIXTURES = ['intimate_loss', 'kinetic_adventure'];

$live = in_array('--live', $argv, true);
$passFilter = 'all';
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--pass=')) {
        $passFilter = substr($arg, 6);
    }
}

$outDir = $root . '/design/review/round-012-live';
$privateDir = $root . '/var/tmp/round-012-live-private';
if (!is_dir($outDir)) {
    mkdir($outDir, 0770, true);
}
if (!is_dir($privateDir)) {
    mkdir($privateDir, 0770, true);
}

$report = [
    'version' => 'round-012.2',
    'mode' => $live ? 'live' : 'dry-run',
    'timestampUtc' => gmdate('c'),
    'budget' => [
        'maxPlanningCalls' => MAX_PLANNING_CALLS,
        'maxImageGenerations' => MAX_IMAGE_CALLS,
    ],
    'environmentFlags' => [
        'AI_PROVIDERS_ENABLED' => 'true',
        'GEMINI_LIVE_CALLS' => 'true',
        'GEMINI_IMAGE_LIVE_CALLS' => 'true',
        'VISUAL_NARRATIVE_PLANNING_ENABLED' => 'true',
        'VISUAL_NARRATIVE_PLANNING_LIVE_CALLS' => 'true',
        'VISUAL_NARRATIVE_LEGACY_COMPILER' => 'false',
    ],
    'readiness' => [],
    'blockers' => [],
    'planning' => null,
    'imageAb' => null,
    'acceptanceGate' => null,
];

function bootValidationEnvironment(string $root): void
{
    $db = $root . '/var/tmp/round-012-live.sqlite';
    $storage = $root . '/var/tmp/round-012-live-storage';
    $log = $root . '/var/tmp/round-012-live-log';
    foreach ([$storage, $log, dirname($db), $storage . '/portraits', $storage . '/images', $storage . '/tmp'] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0770, true);
        }
    }
    if (is_file($db)) {
        unlink($db);
    }

    putenv('APP_ENV=development');
    putenv('APP_DEBUG=true');
    putenv('APP_URL=http://127.0.0.1:8080');
    putenv('APP_KEY=round-012-live-' . bin2hex(random_bytes(8)));
    putenv('DATABASE_PATH=' . $db);
    putenv('PRIVATE_STORAGE_PATH=' . $storage);
    putenv('LOG_PATH=' . $log);
    putenv('ALLOW_EXTERNAL_USERS=false');
    putenv('AI_PROVIDERS_ENABLED=true');
    putenv('GEMINI_LIVE_CALLS=true');
    putenv('GEMINI_IMAGE_LIVE_CALLS=true');
    putenv('VISUAL_NARRATIVE_PLANNING_ENABLED=true');
    putenv('VISUAL_NARRATIVE_PLANNING_LIVE_CALLS=true');
    putenv('VISUAL_NARRATIVE_LEGACY_COMPILER=false');
    putenv('AI_ALLOW_DETERMINISTIC_FALLBACK=false');
    putenv('OWNER_EMAIL=owner@round012live.test');
    putenv('OWNER_PASSWORD=round-012-live-owner');
    putenv('OWNER_DISPLAY_NAME=Round 012 Live Owner');
    $_ENV = array_merge($_ENV, [
        'APP_ENV' => 'development',
        'DATABASE_PATH' => $db,
        'PRIVATE_STORAGE_PATH' => $storage,
        'LOG_PATH' => $log,
        'AI_PROVIDERS_ENABLED' => 'true',
        'GEMINI_LIVE_CALLS' => 'true',
        'GEMINI_IMAGE_LIVE_CALLS' => 'true',
        'VISUAL_NARRATIVE_PLANNING_ENABLED' => 'true',
        'VISUAL_NARRATIVE_PLANNING_LIVE_CALLS' => 'true',
        'VISUAL_NARRATIVE_LEGACY_COMPILER' => 'false',
        'AI_ALLOW_DETERMINISTIC_FALLBACK' => 'false',
        'OWNER_EMAIL' => 'owner@round012live.test',
        'OWNER_PASSWORD' => 'round-012-live-owner',
        'OWNER_DISPLAY_NAME' => 'Round 012 Live Owner',
    ]);
    Config::boot($root);
    Config::ensureDirectories();
    Migrator::migrate();
    StyleService::seed();
    AuthService::seedOwner();
}

function makeSyntheticPortraitFixture(string $path): void
{
    $img = imagecreatetruecolor(512, 512);
    $bg = imagecolorallocate($img, 36, 42, 58);
    $skin = imagecolorallocate($img, 214, 186, 160);
    $hair = imagecolorallocate($img, 48, 36, 28);
    imagefilledrectangle($img, 0, 0, 512, 512, $bg);
    imagefilledellipse($img, 256, 250, 180, 220, $skin);
    imagefilledellipse($img, 256, 150, 200, 160, $hair);
    imagejpeg($img, $path, 92);
}

/** @return array{ok:bool,blockers:list<string>,details:array<string,mixed>} */
function readinessCheck(): array
{
    $blockers = [];
    if (!Config::getBool('gates.ai_providers_enabled')) {
        $blockers[] = 'AI_PROVIDERS_ENABLED is not true';
    }
    if ((string) Config::get('ai.gemini_api_key') === '') {
        $blockers[] = 'GEMINI_API_KEY is missing from the validation environment';
    }
    if (!Config::getBool('ai.gemini_live_calls')) {
        $blockers[] = 'GEMINI_LIVE_CALLS is not true';
    }
    if (!Config::getBool('ai.gemini_image_live_calls')) {
        $blockers[] = 'GEMINI_IMAGE_LIVE_CALLS is not true';
    }
    if (!Config::getBool('ai.visual_narrative_planning_live_calls')) {
        $blockers[] = 'VISUAL_NARRATIVE_PLANNING_LIVE_CALLS is not true';
    }
    if (GeminiVisualNarrativePlanner::availabilityFailure() !== null) {
        $blockers[] = 'GeminiVisualNarrativePlanner unavailable: ' . GeminiVisualNarrativePlanner::availabilityFailure();
    }
    if (!(new GeminiImageAdapter())->isAvailable()) {
        $blockers[] = 'GeminiImageAdapter unavailable for live image generation';
    }

    return [
        'ok' => $blockers === [],
        'blockers' => $blockers,
        'details' => [
            'planningModel' => GeminiVisualNarrativePlanner::resolveModel(),
            'imageModel' => (string) Config::get('ai.gemini_image_model', 'gemini-3.1-flash-image'),
            'planningTemplate' => GeminiVisualNarrativePlanner::PROMPT_TEMPLATE_VERSION,
            'geminiKeyPresent' => (string) Config::get('ai.gemini_api_key') !== '',
        ],
    ];
}

/** @param array<string,mixed> $direction @return array<string,mixed> */
function sanitizeDirection(array $direction): array
{
    return [
        'id' => (string) ($direction['id'] ?? ''),
        'type' => (string) ($direction['type'] ?? ''),
        'title' => (string) ($direction['title'] ?? ''),
        'user_summary' => (string) ($direction['user_summary'] ?? ''),
        'scene_premise_excerpt' => substr((string) ($direction['scene_premise'] ?? ''), 0, 180),
        'scores' => [
            'overall_rank' => (float) ($direction['overall_rank'] ?? 0),
            'song_dna_fidelity' => (float) ($direction['song_dna_fidelity'] ?? 0),
            'narrative_coherence' => (float) ($direction['narrative_coherence'] ?? 0),
            'visual_distinctiveness' => (float) ($direction['visual_distinctiveness'] ?? 0),
            'information_budget_score' => (float) ($direction['information_budget_score'] ?? 0),
            'portrait_suitability' => (string) ($direction['portrait_suitability'] ?? ''),
        ],
    ];
}

/** @param list<array<string,mixed>> $directions */
function explainWinner(array $directions, array $selected): string
{
    if ($directions === []) {
        return 'no directions ranked';
    }
    $winnerId = (string) ($selected['id'] ?? '');
    $runnerUp = null;
    foreach ($directions as $direction) {
        if ((string) ($direction['id'] ?? '') !== $winnerId) {
            $runnerUp = $direction;
            break;
        }
    }
    if ($runnerUp === null) {
        return 'single candidate selected';
    }
    $parts = [];
    foreach (['overall_rank', 'song_dna_fidelity', 'narrative_coherence', 'visual_distinctiveness', 'information_budget_score'] as $metric) {
        $win = (float) ($selected[$metric] ?? 0);
        $run = (float) ($runnerUp[$metric] ?? 0);
        if ($win > $run + 0.01) {
            $parts[] = $metric . ' +' . round($win - $run, 3);
        }
    }
    if ($parts === []) {
        return 'tie-break on ascending direction id after equal weighted score';
    }
    return 'outscored runner-up on ' . implode(', ', $parts);
}

/** @return array{userId:int,portraitId:string} */
function ensureSyntheticPortrait(): array
{
    $owner = Database::one('SELECT * FROM users WHERE role = :r LIMIT 1', ['r' => 'owner']);
    if ($owner === null) {
        throw new RuntimeException('owner_missing');
    }
    $userId = (int) $owner['id'];
    $existing = Database::one(
        'SELECT public_id FROM portraits WHERE user_id = :uid AND deleted_at IS NULL ORDER BY id DESC LIMIT 1',
        ['uid' => $userId]
    );
    if (is_array($existing) && ($existing['public_id'] ?? '') !== '') {
        return ['userId' => $userId, 'portraitId' => (string) $existing['public_id']];
    }
    $tmp = (string) Config::get('paths.storage') . '/tmp/round-012-live-portrait.jpg';
    makeSyntheticPortraitFixture($tmp);
    $uploadTmp = (string) Config::get('paths.storage') . '/tmp/round-012-live-upload.jpg';
    copy($tmp, $uploadTmp);
    $portrait = PortraitService::upload($userId, [
        'tmp_name' => $uploadTmp,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($uploadTmp),
        'name' => 'round-012-live-portrait.jpg',
        'type' => 'image/jpeg',
    ]);
    return ['userId' => $userId, 'portraitId' => (string) $portrait['id']];
}

/** @return array<string,mixed> */
function baseSnapshot(int $userId, string $portraitId, int $portraitCount = 1): array
{
    return [
        'title' => 'Fixture Song',
        'artist' => 'Fixture Artist',
        'userId' => $userId,
        'portraitIds' => [$portraitId],
        'portraitCount' => $portraitCount,
        'styleName' => 'Cinematic Realism',
        'styleKey' => 'photoreal_cinema',
        'quality' => 'medium',
        'orientation' => 'square',
        'noTextInImage' => true,
        'specialInstructions' => '',
    ];
}

/** @param array<string,mixed> $dna */
function privacyCheck(array $dna, int $portraitCount): array
{
    $payload = GeminiVisualNarrativePlanner::requestPayload(
        GeminiVisualNarrativePlanner::safeDna(array_merge($dna, [
            'lyrics' => 'SECRET_LYRIC_SHOULD_NOT_APPEAR',
            'portraitBytes' => 'SECRET_PORTRAIT_BYTES',
        ])),
        $portraitCount,
        GeminiVisualNarrativePlanner::resolveModel()
    );
    $json = json_encode($payload, JSON_THROW_ON_ERROR);
    return [
        'rawLyricsAbsent' => !str_contains($json, 'SECRET_LYRIC_SHOULD_NOT_APPEAR'),
        'portraitBytesAbsent' => !str_contains($json, 'SECRET_PORTRAIT_BYTES') && !str_contains($json, 'portraitBytes'),
    ];
}

bootValidationEnvironment($root);
$readiness = readinessCheck();
$report['readiness'] = $readiness['details'];
$report['blockers'] = $readiness['blockers'];

$fixtures = visual_narrative_fixtures();
$planningResults = [
    'status' => $live ? 'pending' : 'dry-run',
    'planningCallsUsed' => 0,
    'fixtures' => [],
];
$imageResults = [
    'status' => $live ? 'pending' : 'dry-run',
    'imageGenerationsUsed' => 0,
    'sharedSettings' => [
        'quality' => 'medium',
        'orientation' => 'square',
        'noTextInImage' => true,
        'portraitCount' => 1,
        'styleKey' => 'photoreal_cinema',
    ],
    'comparisons' => [],
    'blindReveal' => [],
];

if (!$live) {
    foreach (array_keys($fixtures) as $fixtureKey) {
        $planningResults['fixtures'][$fixtureKey] = [
            'status' => 'dry-run',
            'wouldCall' => 'GeminiVisualNarrativePlanner::plan',
        ];
    }
    foreach (IMAGE_FIXTURES as $fixtureKey) {
        $imageResults['comparisons'][] = [
            'fixture' => $fixtureKey,
            'status' => 'dry-run',
            'variants' => ['A' => 'legacy compiler', 'B' => 'live POV + canonical prompt'],
        ];
    }
    $report['planning'] = $planningResults;
    $report['imageAb'] = $imageResults;
    $report['acceptanceGate'] = [
        'recommendation' => 'BLOCKED',
        'reason' => 'Dry-run only. Re-run with --live after GEMINI_API_KEY and authorized portrait are available in the validation environment.',
    ];
    writeArtifacts($outDir, $planningResults, $imageResults, $report);
    echo "Dry-run complete. Blockers: " . (count($readiness['blockers']) ? implode('; ', $readiness['blockers']) : 'none') . "\n";
    echo "Wrote sanitized artifacts to {$outDir}\n";
    exit($readiness['ok'] ? 0 : 2);
}

if (!$readiness['ok']) {
    $planningResults['status'] = 'blocked';
    $imageResults['status'] = 'blocked';
    $report['planning'] = $planningResults;
    $report['imageAb'] = $imageResults;
    $report['acceptanceGate'] = [
        'recommendation' => 'BLOCKED',
        'reason' => 'Live validation could not start: ' . implode('; ', $readiness['blockers']),
    ];
    writeArtifacts($outDir, $planningResults, $imageResults, $report);
    fwrite(STDERR, "Live validation blocked:\n - " . implode("\n - ", $readiness['blockers']) . "\n");
    exit(2);
}

if ($passFilter === 'all' || $passFilter === 'planning') {
    foreach ($fixtures as $fixtureKey => $dna) {
        if ($planningResults['planningCallsUsed'] >= MAX_PLANNING_CALLS) {
            break;
        }
        $portraitCount = in_array($fixtureKey, ['ambiguous_relationship', 'kinetic_adventure'], true) ? 2 : 1;
        $snapshot = baseSnapshot(0, '', $portraitCount);
        $started = microtime(true);
        $entry = [
            'fixture' => $fixtureKey,
            'portraitCount' => $portraitCount,
            'privacy' => privacyCheck($dna, $portraitCount),
        ];
        try {
            $parsed = GeminiVisualNarrativePlanner::plan($dna, $snapshot);
            $planningResults['planningCallsUsed']++;
            $directions = is_array($parsed['directions'] ?? null) ? $parsed['directions'] : [];
            $selected = is_array($parsed['selectedDirection'] ?? null) ? $parsed['selectedDirection'] : [];
            $contract = is_array($parsed['sceneContract'] ?? null) ? $parsed['sceneContract'] : [];
            $boardValidation = VisualNarrativeContracts::validateBoard(is_array($parsed['board'] ?? null) ? $parsed['board'] : []);
            $contractValidation = VisualNarrativeContracts::validateSceneContract($contract);
            $entry['status'] = 'success';
            $entry['plannerSource'] = (string) ($parsed['plannerSource'] ?? '');
            $entry['planningModel'] = GeminiVisualNarrativePlanner::resolveModel();
            $entry['latencyMs'] = (int) round((microtime(true) - $started) * 1000);
            $entry['directions'] = array_map('sanitizeDirection', $directions);
            $entry['selectedDirectionId'] = (string) ($selected['id'] ?? '');
            $entry['selectedDirectionType'] = (string) ($selected['type'] ?? '');
            $entry['nonPrimaryWon'] = ($selected['type'] ?? 'primary') !== 'primary';
            $entry['winnerExplanation'] = explainWinner($directions, $selected);
            $entry['contractValidation'] = [
                'boardOk' => (bool) $boardValidation['ok'],
                'sceneOk' => (bool) $contractValidation['ok'],
                'boardRepaired' => !$boardValidation['ok'],
                'sceneRepaired' => !$contractValidation['ok'],
            ];
            $entry['fallback'] = false;
            $entry['errorClass'] = null;
        } catch (Throwable $e) {
            $entry['status'] = 'error';
            $entry['latencyMs'] = (int) round((microtime(true) - $started) * 1000);
            $entry['fallback'] = true;
            $entry['errorClass'] = $e->getMessage();
        }
        $planningResults['fixtures'][$fixtureKey] = $entry;
    }
    $planningResults['status'] = 'complete';
}

if ($passFilter === 'all' || $passFilter === 'images') {
    $portrait = ensureSyntheticPortrait();
    $sharedSnapshot = baseSnapshot($portrait['userId'], $portrait['portraitId'], 1);
    $blindMap = [];
    $labels = ['A', 'B'];
    foreach (IMAGE_FIXTURES as $fixtureKey) {
        if ($imageResults['imageGenerationsUsed'] + 2 > MAX_IMAGE_CALLS) {
            break;
        }
        $dna = $fixtures[$fixtureKey];
        $comparison = [
            'fixture' => $fixtureKey,
            'blindLabels' => [],
            'scores' => [],
        ];
        $variants = [
            ['pipeline' => 'legacy', 'legacyCompiler' => true, 'planningLive' => false],
            ['pipeline' => 'live_pov_canonical', 'legacyCompiler' => false, 'planningLive' => true],
        ];
        shuffle($variants);
        foreach ($variants as $index => $variant) {
            if ($imageResults['imageGenerationsUsed'] >= MAX_IMAGE_CALLS) {
                break;
            }
            $label = $labels[$index];
            $blindMap[$fixtureKey][$label] = $variant['pipeline'];
            $started = microtime(true);
            putenv('VISUAL_NARRATIVE_LEGACY_COMPILER=' . ($variant['legacyCompiler'] ? 'true' : 'false'));
            putenv('VISUAL_NARRATIVE_PLANNING_LIVE_CALLS=' . ($variant['planningLive'] ? 'true' : 'false'));
            $_ENV['VISUAL_NARRATIVE_LEGACY_COMPILER'] = $variant['legacyCompiler'] ? 'true' : 'false';
            $_ENV['VISUAL_NARRATIVE_PLANNING_LIVE_CALLS'] = $variant['planningLive'] ? 'true' : 'false';
            Config::boot($root);
            $entry = [
                'blindLabel' => $label,
                'pipeline' => $variant['pipeline'],
                'provider' => 'gemini-image',
                'model' => (string) Config::get('ai.gemini_image_model', 'gemini-3.1-flash-image'),
            ];
            try {
                $package = CreativePackageBuilder::build($dna, $sharedSnapshot, 'round-012-live-' . $fixtureKey . '-' . $label);
                $planning = is_array($package['visualPlanning'] ?? null) ? $package['visualPlanning'] : [];
                $adapter = new GeminiImageAdapter();
                $image = $adapter->generate($package, $sharedSnapshot);
                $imageResults['imageGenerationsUsed']++;
                $privatePath = $privateDir . '/' . $fixtureKey . '-' . $label . '.jpg';
                file_put_contents($privatePath, $image['bytes']);
                $entry['status'] = 'success';
                $entry['latencyMs'] = (int) round((microtime(true) - $started) * 1000);
                $entry['width'] = (int) ($image['width'] ?? 0);
                $entry['height'] = (int) ($image['height'] ?? 0);
                $entry['estimatedCostCents'] = (int) ($image['costCents'] ?? Config::getInt('ai.gemini_image_cost_cents', 7));
                $entry['fallback'] = (bool) ($planning['fallback'] ?? false);
                $entry['plannerSource'] = (string) ($planning['plannerSource'] ?? '');
                $entry['selectedDirectionType'] = (string) ($planning['selectedDirectionType'] ?? '');
                $entry['privateImageReference'] = $privatePath;
                $entry['committedToGit'] = false;
            } catch (Throwable $e) {
                $entry['status'] = 'error';
                $entry['latencyMs'] = (int) round((microtime(true) - $started) * 1000);
                $entry['errorClass'] = $e->getMessage();
                $entry['fallback'] = true;
            }
            $comparison['blindLabels'][$label] = [
                'status' => $entry['status'],
                'latencyMs' => $entry['latencyMs'],
                'width' => $entry['width'] ?? null,
                'height' => $entry['height'] ?? null,
                'estimatedCostCents' => $entry['estimatedCostCents'] ?? null,
                'scores' => null,
            ];
            $comparison['entries'][] = $entry;
        }
        $imageResults['comparisons'][] = $comparison;
    }
    $imageResults['blindReveal'] = $blindMap;
    $imageResults['status'] = 'complete';
}

$report['planning'] = $planningResults;
$report['imageAb'] = $imageResults;
$report['acceptanceGate'] = deriveAcceptanceGate($planningResults, $imageResults);
writeArtifacts($outDir, $planningResults, $imageResults, $report);
echo "Live validation complete.\n";
echo "Planning calls: {$planningResults['planningCallsUsed']}/" . MAX_PLANNING_CALLS . "\n";
echo "Image generations: {$imageResults['imageGenerationsUsed']}/" . MAX_IMAGE_CALLS . "\n";
echo "Recommendation: {$report['acceptanceGate']['recommendation']}\n";
echo "Wrote sanitized artifacts to {$outDir}\n";
exit(0);

/** @param array<string,mixed> $planning @param array<string,mixed> $images @return array<string,mixed> */
function deriveAcceptanceGate(array $planning, array $images): array
{
    if (($planning['status'] ?? '') === 'blocked' || ($images['status'] ?? '') === 'blocked') {
        return [
            'recommendation' => 'BLOCKED',
            'reason' => 'Provider credentials or portrait prerequisites missing.',
        ];
    }
    if (($planning['status'] ?? '') !== 'complete' || ($images['status'] ?? '') !== 'complete') {
        return [
            'recommendation' => 'BLOCKED',
            'reason' => 'One or both passes did not complete.',
        ];
    }
    return [
        'recommendation' => 'PENDING_HUMAN_SCORING',
        'reason' => 'Live calls succeeded. Image scores must be assigned by reviewing private references before ACCEPT / ACCEPT WITH TUNING / REJECT.',
    ];
}

/** @param array<string,mixed> $planning @param array<string,mixed> $images @param array<string,mixed> $report */
function writeArtifacts(string $outDir, array $planning, array $images, array $report): void
{
    file_put_contents(
        $outDir . '/planning-results.json',
        json_encode($planning, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
    );
    file_put_contents(
        $outDir . '/image-ab-results.json',
        json_encode($images, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
    );
    file_put_contents(
        $outDir . '/validation-run-summary.json',
        json_encode([
            'version' => $report['version'] ?? 'round-012.2',
            'mode' => $report['mode'] ?? 'unknown',
            'timestampUtc' => $report['timestampUtc'] ?? null,
            'readiness' => $report['readiness'] ?? [],
            'blockers' => $report['blockers'] ?? [],
            'acceptanceGate' => $report['acceptanceGate'] ?? null,
            'environmentFlags' => $report['environmentFlags'] ?? [],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
    );
}
