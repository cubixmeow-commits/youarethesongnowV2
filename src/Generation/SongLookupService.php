<?php

declare(strict_types=1);

namespace Yatsn\Generation;

use Yatsn\Support\Database;
use Yatsn\Support\RateLimiter;

final class SongLookupService
{
    /** Deterministic development catalog. No lyrics stored or returned. */
    private const FIXTURES = [
        ['artist' => 'Public Domain Demo', 'title' => 'Amazing Grace', 'confidence' => 0.96, 'state' => 'found'],
        ['artist' => 'Owner Test Band', 'title' => 'Midnight Harbor', 'confidence' => 0.91, 'state' => 'found'],
        ['artist' => 'Owner Test Band', 'title' => 'Paper Lanterns', 'confidence' => 0.88, 'state' => 'fallbackFound'],
        ['artist' => 'Celebration Ensemble', 'title' => 'First Dance Light', 'confidence' => 0.9, 'state' => 'found'],
    ];

    public static function create(int $userId, string $artist, string $title): array
    {
        $artist = trim($artist);
        $title = trim($title);
        if ($artist === '' || $title === '') {
            throw new \InvalidArgumentException('song_required');
        }

        // Rate-limit check: 10 / 10 minutes, 50 / day, and per-IP 10 / 10 minutes.
        RateLimiter::hit('song_lookup_user_10m', 'user:' . $userId, 10, 600);
        RateLimiter::hit('song_lookup_user_day', 'user:' . $userId, 50, 86400);
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
        RateLimiter::hit('song_lookup_ip_10m', 'ip:' . $ip, 10, 600);

        $recent = Database::one(
            'SELECT COUNT(*) AS c FROM song_lookups WHERE user_id = :uid AND created_at >= :since',
            ['uid' => $userId, 'since' => gmdate('Y-m-d\TH:i:s\Z', time() - 600)]
        );
        if ((int) ($recent['c'] ?? 0) >= 10) {
            throw new \RuntimeException('rate_limited');
        }

        $publicId = opaque_id();
        $now = now_utc();
        Database::exec(
            'INSERT INTO song_lookups (public_id, user_id, artist_text, title_text, state, created_at, updated_at)
             VALUES (:pid, :uid, :a, :t, \'searching\', :c, :u)',
            ['pid' => $publicId, 'uid' => $userId, 'a' => $artist, 't' => $title, 'c' => $now, 'u' => $now]
        );

        $match = self::resolve($artist, $title);
        Database::exec(
            'UPDATE song_lookups SET state = :state, match_confidence = :conf, source_label = :src,
                classification = :cls, updated_at = :u WHERE public_id = :pid',
            [
                'state' => $match['state'],
                'conf' => $match['confidence'],
                'src' => $match['source'],
                'cls' => $match['classification'],
                'u' => now_utc(),
                'pid' => $publicId,
            ]
        );

        $row = Database::one('SELECT * FROM song_lookups WHERE public_id = :pid', ['pid' => $publicId]);
        return self::public($row);
    }

    public static function findOwned(int $userId, string $publicId): ?array
    {
        return Database::one(
            'SELECT * FROM song_lookups WHERE public_id = :pid AND user_id = :uid',
            ['pid' => $publicId, 'uid' => $userId]
        );
    }

    public static function public(?array $row): array
    {
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        return [
            'id' => $row['public_id'],
            'artist' => $row['artist_text'],
            'title' => $row['title_text'],
            'state' => $row['state'],
            'classification' => $row['classification'],
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
        ];
    }

    private static function resolve(string $artist, string $title): array
    {
        $a = strtolower($artist);
        $t = strtolower($title);
        foreach (self::FIXTURES as $fixture) {
            if (strtolower($fixture['artist']) === $a && strtolower($fixture['title']) === $t) {
                return [
                    'state' => $fixture['state'],
                    'confidence' => $fixture['confidence'],
                    'source' => 'development-fixture',
                    'classification' => $fixture['state'] === 'fallbackFound'
                        ? 'inspired_by_available_information'
                        : 'themes_and_feeling',
                ];
            }
        }

        // Fuzzy development match: if both strings are reasonably long, treat as fallbackFound.
        if (strlen($artist) >= 3 && strlen($title) >= 3) {
            return [
                'state' => 'fallbackFound',
                'confidence' => 0.62,
                'source' => 'development-context',
                'classification' => 'inspired_by_available_information',
            ];
        }

        return [
            'state' => 'notFound',
            'confidence' => 0.0,
            'source' => 'none',
            'classification' => 'not_found',
        ];
    }
}
