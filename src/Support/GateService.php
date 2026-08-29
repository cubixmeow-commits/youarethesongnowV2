<?php

declare(strict_types=1);

namespace Yatsn\Support;

final class GateService
{
    public static function assertGenerationAllowed(): void
    {
        if (self::get('spend_paused') === '1') {
            throw new \RuntimeException('spend_paused');
        }
        if (self::get('disk_paused') === '1') {
            throw new \RuntimeException('disk_paused');
        }
        $spent = Database::one(
            'SELECT COALESCE(SUM(cost_cents), 0) AS c FROM provider_costs WHERE created_at >= :since',
            ['since' => gmdate('Y-m-01\T00:00:00\Z')]
        );
        $budget = Config::getInt('budget.monthly_ai_cents');
        if ((int) ($spent['c'] ?? 0) >= $budget) {
            self::set('spend_paused', '1');
            throw new \RuntimeException('spend_paused');
        }
    }

    public static function storageUsageRatio(): float
    {
        $root = (string) Config::get('paths.storage');
        $total = @disk_total_space($root);
        $free = @disk_free_space($root);
        if (!$total || $total <= 0 || $free === false) {
            return 0.0;
        }
        return 1 - ($free / $total);
    }

    public static function refreshDiskGate(): void
    {
        $ratio = self::storageUsageRatio();
        if ($ratio >= 0.90) {
            self::set('disk_paused', '1');
        }
    }

    public static function get(string $key, string $default = '0'): string
    {
        $row = Database::one('SELECT value FROM system_gates WHERE key = :k', ['k' => $key]);
        return $row['value'] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        Database::exec(
            'INSERT INTO system_gates (key, value, updated_at) VALUES (:k, :v, :u)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = excluded.updated_at',
            ['k' => $key, 'v' => $value, 'u' => now_utc()]
        );
    }

    public static function status(): array
    {
        return [
            'spendPaused' => self::get('spend_paused') === '1',
            'diskPaused' => self::get('disk_paused') === '1',
            'storageUsageRatio' => round(self::storageUsageRatio(), 4),
            'monthlySpendCents' => (int) (Database::one(
                'SELECT COALESCE(SUM(cost_cents), 0) AS c FROM provider_costs WHERE created_at >= :since',
                ['since' => gmdate('Y-m-01\T00:00:00\Z')]
            )['c'] ?? 0),
            'monthlyBudgetCents' => Config::getInt('budget.monthly_ai_cents'),
        ];
    }
}
