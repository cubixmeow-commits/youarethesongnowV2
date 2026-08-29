<?php

declare(strict_types=1);

namespace Yatsn\Support;

final class RateLimiter
{
    public static function hit(string $bucket, string $subject, int $limit, int $windowSeconds): void
    {
        $windowStart = gmdate('Y-m-d\TH:i:s\Z', (int) (floor(time() / $windowSeconds) * $windowSeconds));
        $row = Database::one(
            'SELECT * FROM rate_limits WHERE bucket = :b AND subject = :s AND window_start = :w',
            ['b' => $bucket, 's' => $subject, 'w' => $windowStart]
        );
        if ($row === null) {
            Database::exec(
                'INSERT INTO rate_limits (bucket, subject, window_start, count) VALUES (:b, :s, :w, 1)',
                ['b' => $bucket, 's' => $subject, 'w' => $windowStart]
            );
            return;
        }
        if ((int) $row['count'] >= $limit) {
            throw new \RuntimeException('rate_limited');
        }
        Database::exec(
            'UPDATE rate_limits SET count = count + 1 WHERE id = :id',
            ['id' => $row['id']]
        );
    }
}
