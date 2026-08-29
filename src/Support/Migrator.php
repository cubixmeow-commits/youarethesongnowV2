<?php

declare(strict_types=1);

namespace Yatsn\Support;

use RuntimeException;

final class Migrator
{
    public static function migrate(): array
    {
        $pdo = Database::pdo();
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                version TEXT PRIMARY KEY,
                applied_at TEXT NOT NULL
            )'
        );

        $dir = Config::root() . '/database/migrations';
        $files = glob($dir . '/*.sql') ?: [];
        sort($files);

        $applied = [];
        foreach ($files as $file) {
            $version = basename($file, '.sql');
            $exists = Database::one('SELECT version FROM schema_migrations WHERE version = :v', ['v' => $version]);
            if ($exists !== null) {
                continue;
            }

            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new RuntimeException('Unable to read migration: ' . $version);
            }

            Database::begin();
            try {
                $pdo->exec($sql);
                Database::exec(
                    'INSERT INTO schema_migrations (version, applied_at) VALUES (:v, :a)',
                    ['v' => $version, 'a' => now_utc()]
                );
                Database::commit();
                $applied[] = $version;
            } catch (\Throwable $e) {
                Database::rollBack();
                throw $e;
            }
        }

        return $applied;
    }

    public static function status(): array
    {
        $dir = Config::root() . '/database/migrations';
        $files = glob($dir . '/*.sql') ?: [];
        sort($files);
        $out = [];
        foreach ($files as $file) {
            $version = basename($file, '.sql');
            $exists = Database::one('SELECT applied_at FROM schema_migrations WHERE version = :v', ['v' => $version]);
            $out[] = [
                'version' => $version,
                'applied' => $exists !== null,
                'appliedAt' => $exists['applied_at'] ?? null,
            ];
        }
        return $out;
    }
}
