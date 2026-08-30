<?php

declare(strict_types=1);

namespace Yatsn\Generation;

use Yatsn\Credits\CreditService;
use Yatsn\Portraits\PortraitService;
use Yatsn\Styles\StyleService;
use Yatsn\Support\Config;
use Yatsn\Support\Database;

final class DraftService
{
    public static function create(int $userId, array $input): array
    {
        $now = now_utc();
        $publicId = opaque_id();
        Database::exec(
            'INSERT INTO creation_drafts (public_id, user_id, quality, orientation, no_text_in_image, created_at, updated_at)
             VALUES (:pid, :uid, :q, :o, 0, :c, :u)',
            [
                'pid' => $publicId,
                'uid' => $userId,
                'q' => 'medium',
                'o' => 'square',
                'c' => $now,
                'u' => $now,
            ]
        );
        if ($input !== []) {
            return self::update($userId, $publicId, $input);
        }
        $row = Database::one('SELECT * FROM creation_drafts WHERE public_id = :pid', ['pid' => $publicId]);
        return self::public($row, $userId);
    }

    public static function update(int $userId, string $publicId, array $input): array
    {
        $draft = self::findOwned($userId, $publicId);
        if ($draft === null) {
            throw new \RuntimeException('not_found');
        }
        if ($draft['locked_at'] !== null) {
            throw new \RuntimeException('draft_locked');
        }

        $songLookupId = $draft['song_lookup_id'];
        if (isset($input['songLookupId'])) {
            $lookup = SongLookupService::findOwned($userId, (string) $input['songLookupId']);
            if ($lookup === null) {
                throw new \InvalidArgumentException('invalid_song_lookup');
            }
            $songLookupId = (int) $lookup['id'];
        }

        $portraitIds = json_decode((string) $draft['portrait_ids_json'], true) ?: [];
        if (array_key_exists('portraitIds', $input)) {
            $portraitIds = [];
            $incoming = $input['portraitIds'];
            if (!is_array($incoming) || count($incoming) > 2) {
                throw new \InvalidArgumentException('invalid_portraits');
            }
            foreach ($incoming as $pid) {
                $portrait = PortraitService::findOwned($userId, (string) $pid);
                if ($portrait === null) {
                    throw new \InvalidArgumentException('invalid_portrait');
                }
                $portraitIds[] = $portrait['public_id'];
            }
        }

        $styleId = $draft['style_id'];
        if (array_key_exists('styleId', $input)) {
            if ($input['styleId'] === null || $input['styleId'] === '') {
                $styleId = null;
            } else {
                $style = StyleService::findByPublicId((string) $input['styleId']);
                if ($style === null || $style['status'] !== 'active') {
                    throw new \InvalidArgumentException('invalid_style');
                }
                $styleId = (int) $style['id'];
            }
        }

        $quality = $draft['quality'];
        if (isset($input['quality'])) {
            if (!in_array($input['quality'], ['low', 'medium', 'high'], true)) {
                throw new \InvalidArgumentException('invalid_quality');
            }
            $quality = $input['quality'];
        }

        $orientation = $draft['orientation'];
        if (isset($input['orientation'])) {
            if (!in_array($input['orientation'], ['square', 'portrait', 'landscape'], true)) {
                throw new \InvalidArgumentException('invalid_orientation');
            }
            $orientation = $input['orientation'];
        }

        $noText = (int) $draft['no_text_in_image'];
        if (array_key_exists('noTextInImage', $input)) {
            $noText = $input['noTextInImage'] ? 1 : 0;
        }

        $special = $draft['special_instructions'];
        if (array_key_exists('specialInstructions', $input)) {
            $special = $input['specialInstructions'];
            if ($special !== null) {
                $special = trim((string) $special);
                if (strlen($special) > 500) {
                    throw new \InvalidArgumentException('special_instructions_too_long');
                }
                if ($special === '') {
                    $special = null;
                }
            }
        }

        Database::exec(
            'UPDATE creation_drafts SET song_lookup_id = :sid, portrait_ids_json = :pids, style_id = :style,
                quality = :q, orientation = :o, no_text_in_image = :nt, special_instructions = :sp, updated_at = :u
             WHERE id = :id',
            [
                'sid' => $songLookupId,
                'pids' => json_encode(array_values($portraitIds), JSON_THROW_ON_ERROR),
                'style' => $styleId,
                'q' => $quality,
                'o' => $orientation,
                'nt' => $noText,
                'sp' => $special,
                'u' => now_utc(),
                'id' => $draft['id'],
            ]
        );

        $row = Database::one('SELECT * FROM creation_drafts WHERE id = :id', ['id' => $draft['id']]);
        return self::public($row, $userId);
    }

    public static function summary(int $userId, string $publicId): array
    {
        $draft = self::findOwned($userId, $publicId);
        if ($draft === null) {
            throw new \RuntimeException('not_found');
        }
        $public = self::public($draft, $userId);
        $ready = self::validateReady($draft, $userId);
        $creditCost = CreditService::priceForQuality($draft['quality']);

        return [
            'headline' => 'Your cinematic world is ready to create',
            'draft' => $public,
            'ready' => $ready['ok'],
            'issues' => $ready['issues'],
            'creditCost' => $creditCost,
            'requiresMembership' => self::requiresMembership($userId),
        ];
    }

    public static function requiresMembership(int $userId): bool
    {
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null) {
            return true;
        }
        if ($user['role'] === 'owner') {
            return false;
        }
        if ($user['commercial_access'] === 'complimentaryReviewer' && $user['membership_status'] === 'complimentary') {
            return false;
        }
        return !in_array($user['membership_status'], ['active', 'complimentary'], true);
    }

    public static function validateReady(array $draft, int $userId): array
    {
        $issues = [];
        if ($draft['song_lookup_id'] === null) {
            $issues['songLookupId'] = 'Choose a song first.';
        } else {
            $lookup = Database::one('SELECT * FROM song_lookups WHERE id = :id', ['id' => $draft['song_lookup_id']]);
            if ($lookup === null || !in_array($lookup['state'], ['found', 'fallbackFound'], true)) {
                $issues['songLookupId'] = 'We could not find enough reliable information about that song. Check the artist and title, or choose another song. No generation credits were used.';
            } elseif (Config::getBool('development.gemini_lyrics_search')
                && trim((string) ($lookup['derived_analysis_json'] ?? '')) === '') {
                $issues['songLookupId'] = 'The song analysis did not complete. Search for the song again before generating an image.';
            }
        }

        $portraitIds = json_decode((string) $draft['portrait_ids_json'], true) ?: [];
        if (count($portraitIds) < 1) {
            $issues['portraitIds'] = 'Add one or two portraits.';
        }
        foreach ($portraitIds as $pid) {
            if (PortraitService::findOwned($userId, (string) $pid) === null) {
                $issues['portraitIds'] = 'One of the selected portraits is unavailable.';
            }
        }

        if ($draft['style_id'] === null) {
            $issues['styleId'] = 'Choose a style.';
        }

        return ['ok' => $issues === [], 'issues' => $issues];
    }

    public static function findOwned(int $userId, string $publicId): ?array
    {
        return Database::one(
            'SELECT * FROM creation_drafts WHERE public_id = :pid AND user_id = :uid',
            ['pid' => $publicId, 'uid' => $userId]
        );
    }

    public static function lock(int $draftId): void
    {
        Database::exec(
            'UPDATE creation_drafts SET locked_at = :l, updated_at = :u WHERE id = :id AND locked_at IS NULL',
            ['l' => now_utc(), 'u' => now_utc(), 'id' => $draftId]
        );
    }

    public static function public(?array $draft, int $userId): array
    {
        if ($draft === null) {
            throw new \RuntimeException('not_found');
        }
        $lookup = null;
        if ($draft['song_lookup_id']) {
            $row = Database::one('SELECT * FROM song_lookups WHERE id = :id', ['id' => $draft['song_lookup_id']]);
            $lookup = $row ? SongLookupService::public($row) : null;
        }
        $style = null;
        if ($draft['style_id']) {
            $row = Database::one('SELECT * FROM styles WHERE id = :id', ['id' => $draft['style_id']]);
            $style = $row ? StyleService::public($row) : null;
        }
        $portraitIds = json_decode((string) $draft['portrait_ids_json'], true) ?: [];
        $portraits = [];
        foreach ($portraitIds as $pid) {
            $p = PortraitService::findOwned($userId, (string) $pid);
            if ($p) {
                $portraits[] = PortraitService::public($p);
            }
        }

        return [
            'id' => $draft['public_id'],
            'songLookup' => $lookup,
            'portraits' => $portraits,
            'style' => $style,
            'quality' => $draft['quality'],
            'orientation' => $draft['orientation'],
            'noTextInImage' => (bool) $draft['no_text_in_image'],
            'specialInstructions' => $draft['special_instructions'],
            'locked' => $draft['locked_at'] !== null,
            'creditCost' => CreditService::priceForQuality($draft['quality']),
            'updatedAt' => $draft['updated_at'],
        ];
    }
}
