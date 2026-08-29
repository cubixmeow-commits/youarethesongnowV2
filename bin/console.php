#!/usr/bin/env php
<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use Yatsn\Auth\AuthService;
use Yatsn\Generation\GenerationJobService;
use Yatsn\Styles\StyleService;
use Yatsn\Support\Config;
use Yatsn\Support\Migrator;

Config::ensureDirectories();

$command = $argv[1] ?? 'help';

match ($command) {
    'migrate' => (function (): void {
        $applied = Migrator::migrate();
        echo 'Migrations applied: ' . (count($applied) ? implode(', ', $applied) : 'none (already up to date)') . PHP_EOL;
        foreach (Migrator::status() as $row) {
            echo sprintf(" - %s %s\n", $row['version'], $row['applied'] ? 'applied' : 'pending');
        }
    })(),
    'seed-styles' => (function (): void {
        Migrator::migrate();
        $n = StyleService::seed();
        echo "Styles seeded/updated. New rows: {$n}\n";
    })(),
    'seed-owner' => (function (): void {
        Migrator::migrate();
        StyleService::seed();
        $owner = AuthService::seedOwner();
        echo "Owner ready: {$owner['email']} ({$owner['id']})\n";
    })(),
    'worker' => (function (): void {
        Migrator::migrate();
        $result = GenerationJobService::processNext();
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    })(),
    'setup-status' => (function (): void {
        echo json_encode(Config::setupStatus(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        $issues = Config::validateOrFail();
        if ($issues) {
            echo "Configuration issues:\n";
            foreach ($issues as $issue) {
                echo " - {$issue}\n";
            }
            exit(1);
        }
    })(),
    default => (function (): void {
        echo <<<TXT
You Are The Song Now V2 — console

Commands:
  php bin/console.php migrate
  php bin/console.php seed-styles
  php bin/console.php seed-owner
  php bin/console.php worker
  php bin/console.php setup-status

TXT;
    })(),
};
