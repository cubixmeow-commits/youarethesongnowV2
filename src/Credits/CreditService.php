<?php

declare(strict_types=1);

namespace Yatsn\Credits;

use Yatsn\Support\Config;
use Yatsn\Support\Database;

final class CreditService
{
    public static function balance(int $userId): int
    {
        $row = Database::one(
            'SELECT balance_after FROM credit_ledger WHERE user_id = :uid ORDER BY id DESC LIMIT 1',
            ['uid' => $userId]
        );
        return $row ? (int) $row['balance_after'] : 0;
    }

    public static function grant(int $userId, int $amount, string $reason, ?string $idempotencyKey = null): array
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('amount_must_be_positive');
        }
        return self::append($userId, 'grant', $amount, $reason, null, $idempotencyKey);
    }

    public static function reserve(int $userId, int $amount, string $jobPublicId, string $idempotencyKey): array
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('amount_must_be_positive');
        }
        $balance = self::balance($userId);
        if ($balance < $amount) {
            throw new \RuntimeException('insufficient_credits');
        }
        return self::append($userId, 'reservation', -$amount, 'Generation reservation', $jobPublicId, $idempotencyKey);
    }

    public static function capture(int $userId, int $amount, string $jobPublicId, string $idempotencyKey): array
    {
        return self::append($userId, 'capture', 0, 'Generation capture', $jobPublicId, $idempotencyKey);
    }

    public static function release(int $userId, int $amount, string $jobPublicId, string $idempotencyKey): array
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('amount_must_be_positive');
        }
        return self::append($userId, 'release', $amount, 'Generation failure release', $jobPublicId, $idempotencyKey);
    }

    public static function adjust(int $userId, int $amount, string $reason, string $idempotencyKey): array
    {
        if ($amount === 0) {
            throw new \InvalidArgumentException('amount_nonzero');
        }
        return self::append($userId, 'adjustment', $amount, $reason, null, $idempotencyKey);
    }

    public static function priceForQuality(string $quality): int
    {
        return match ($quality) {
            'low' => Config::getInt('credits.low'),
            'high' => Config::getInt('credits.high'),
            default => Config::getInt('credits.medium'),
        };
    }

    public static function summary(int $userId): array
    {
        return [
            'balance' => self::balance($userId),
            'currency' => 'credits',
            'tierPrices' => [
                'low' => self::priceForQuality('low'),
                'medium' => self::priceForQuality('medium'),
                'high' => self::priceForQuality('high'),
            ],
            'monthlyAllowance' => Config::getInt('credits.development_monthly'),
        ];
    }

    public static function transactions(int $userId, int $limit = 50): array
    {
        $rows = Database::all(
            'SELECT public_id, type, amount, balance_after, related_job_public_id, reason, created_at
             FROM credit_ledger WHERE user_id = :uid ORDER BY id DESC LIMIT :lim',
            ['uid' => $userId, 'lim' => $limit]
        );
        // PDO may not bind LIMIT as named on all builds; fallback handled by casting.
        return array_map(static fn(array $r): array => [
            'id' => $r['public_id'],
            'type' => $r['type'],
            'amount' => (int) $r['amount'],
            'balanceAfter' => (int) $r['balance_after'],
            'relatedJobId' => $r['related_job_public_id'],
            'reason' => $r['reason'],
            'createdAt' => $r['created_at'],
        ], $rows);
    }

    private static function append(
        int $userId,
        string $type,
        int $amount,
        string $reason,
        ?string $relatedJobPublicId,
        ?string $idempotencyKey
    ): array {
        if ($idempotencyKey !== null) {
            $existing = Database::one(
                'SELECT * FROM credit_ledger WHERE user_id = :uid AND idempotency_key = :k',
                ['uid' => $userId, 'k' => $idempotencyKey]
            );
            if ($existing !== null) {
                return self::publicRow($existing);
            }
        }

        $current = self::balance($userId);
        $after = $current + $amount;
        if ($after < 0) {
            throw new \RuntimeException('insufficient_credits');
        }

        $publicId = opaque_id();
        Database::exec(
            'INSERT INTO credit_ledger (public_id, user_id, type, amount, balance_after, related_job_public_id, reason, idempotency_key, created_at)
             VALUES (:pid, :uid, :type, :amount, :after, :job, :reason, :idem, :c)',
            [
                'pid' => $publicId,
                'uid' => $userId,
                'type' => $type,
                'amount' => $amount,
                'after' => $after,
                'job' => $relatedJobPublicId,
                'reason' => $reason,
                'idem' => $idempotencyKey,
                'c' => now_utc(),
            ]
        );

        $row = Database::one('SELECT * FROM credit_ledger WHERE public_id = :pid', ['pid' => $publicId]);
        return self::publicRow($row);
    }

    private static function publicRow(?array $row): array
    {
        if ($row === null) {
            throw new \RuntimeException('ledger_row_missing');
        }
        return [
            'id' => $row['public_id'],
            'type' => $row['type'],
            'amount' => (int) $row['amount'],
            'balanceAfter' => (int) $row['balance_after'],
            'relatedJobId' => $row['related_job_public_id'],
            'reason' => $row['reason'],
            'createdAt' => $row['created_at'],
            '_internalId' => (int) $row['id'],
        ];
    }
}
