<?php

declare(strict_types=1);

// Router for PHP built-in server: serve existing files, otherwise front-controller.
require dirname(__DIR__) . '/app/bootstrap.php';

use Yatsn\Support\AssetRelease;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$resolved = AssetRelease::resolveVersionedPath($path);
if ($resolved !== null) {
    $file = __DIR__ . $resolved;
    if (is_file($file)) {
        AssetRelease::sendImmutableCacheHeaders();
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $types = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
        ];
        if (isset($types[$ext])) {
            header('Content-Type: ' . $types[$ext]);
        }
        readfile($file);
        return true;
    }
    http_response_code(404);
    echo 'Not found';
    return true;
}

$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    return false;
}
require __DIR__ . '/index.php';
