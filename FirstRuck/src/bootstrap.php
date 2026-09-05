<?php

declare(strict_types=1);

use FirstRuck\Database;

define('FIRST_RUCK_ROOT', dirname(__DIR__));

// When FirstRuck is deployed inside YouAreTheSongNow, reuse its protected
// environment loader so shared provider credentials stay in the root .env.
// The integration is optional and FirstRuck remains independently deployable.
$sharedEnvLoader = dirname(FIRST_RUCK_ROOT) . '/src/Support/Env.php';
if (is_file($sharedEnvLoader)) {
    require_once $sharedEnvLoader;
    \Yatsn\Support\Env::load(dirname(FIRST_RUCK_ROOT) . '/.env');
}

require_once FIRST_RUCK_ROOT . '/src/Database.php';
require_once FIRST_RUCK_ROOT . '/src/RecommendationEngine.php';

if (PHP_SAPI !== 'cli' && session_status() !== PHP_SESSION_ACTIVE) {
    session_name('first_ruck_session');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function first_ruck_database(): PDO
{
    static $database = null;
    if (!$database instanceof PDO) {
        $database = Database::connect(
            FIRST_RUCK_ROOT . '/var/first_ruck.sqlite',
            FIRST_RUCK_ROOT . '/database/schema.sql'
        );
    }

    return $database;
}
