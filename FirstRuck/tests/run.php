<?php

declare(strict_types=1);

use FirstRuck\Database;
use FirstRuck\RecommendationEngine;

require_once dirname(__DIR__) . '/src/Database.php';
require_once dirname(__DIR__) . '/src/RecommendationEngine.php';

$temporaryDatabase = sys_get_temp_dir() . '/first-ruck-test-' . bin2hex(random_bytes(6)) . '.sqlite';
$database = Database::connect($temporaryDatabase, dirname(__DIR__) . '/database/schema.sql');
$engine = new RecommendationEngine();
$answers = [
    'goal' => 'general-fitness',
    'weekly_movement' => '1-2',
    'comfortable_minutes' => '30',
    'equipment' => 'backpack',
    'available_load' => '10-lb',
    'surface' => 'paved',
    'hill_comfort' => 'gentle',
    'sessions_per_week' => '2',
    'route_type' => 'out-and-back',
    'setting' => 'facilities',
    'body_consideration' => 'none',
    'location_label' => 'Test area',
];

$profile = $engine->buildProfile($answers);
assert_same('Fresh start', $profile['level'], 'beginner profile level');
assert_same('Use 5 lb first', $profile['starting_load'], 'conservative starting load');

$trails = $database->query('SELECT * FROM trails')->fetchAll();
$ranked = $engine->rank($answers, $trails);
assert_same(4, count($ranked), 'all demo trails ranked');
assert_true($ranked[0]['score'] >= $ranked[1]['score'], 'routes sorted by score');
assert_true(isset($ranked[0]['geometry']), 'route geometry decoded');
assert_true(isset($ranked[0]['facilities']), 'facilities decoded');

@unlink($temporaryDatabase);
@unlink($temporaryDatabase . '-shm');
@unlink($temporaryDatabase . '-wal');
fwrite(STDOUT, "First Ruck tests passed.\n");

function assert_same(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(sprintf('%s failed: expected %s, got %s', $label, var_export($expected, true), var_export($actual, true)));
    }
}

function assert_true(bool $condition, string $label): void
{
    if (!$condition) {
        throw new RuntimeException($label . ' failed');
    }
}

