<?php

declare(strict_types=1);

define('YATSN_ROOT', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    $prefix = 'Yatsn\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $path = YATSN_ROOT . '/src/' . $relative . '.php';
    if (is_file($path)) {
        require $path;
    }
});

require_once YATSN_ROOT . '/app/helpers.php';

use Yatsn\Support\Config;
use Yatsn\Support\Env;

Env::load(YATSN_ROOT . '/.env');
Config::boot(YATSN_ROOT);

date_default_timezone_set(Config::get('app.timezone', 'UTC'));

if (Config::getBool('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
}

set_exception_handler(static function (Throwable $e): void {
    $requestId = bin2hex(random_bytes(8));
    $logPath = Config::get('paths.log') . '/app.log';
    $message = sprintf(
        "[%s] request=%s %s: %s in %s:%d\n",
        gmdate('c'),
        $requestId,
        $e::class,
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
    @file_put_contents($logPath, $message, FILE_APPEND | LOCK_EX);

    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, $message);
        exit(1);
    }

    $wantsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
        || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');

    http_response_code(500);
    if ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode([
            'error' => [
                'code' => 'server_error',
                'message' => 'Something went wrong. Please try again.',
                'fields' => new stdClass(),
                'requestId' => $requestId,
                'retryAfterSeconds' => null,
            ],
        ], JSON_THROW_ON_ERROR);
        return;
    }

    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Error</title></head><body><p>Something went wrong. Please try again.</p></body></html>';
});
