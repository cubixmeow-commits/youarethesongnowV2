<?php

declare(strict_types=1);

$assets = [
    'app.css' => [
        'path' => __DIR__ . '/assets/app.css',
        'type' => 'text/css; charset=utf-8',
    ],
    'app.js' => [
        'path' => __DIR__ . '/assets/app.js',
        'type' => 'text/javascript; charset=utf-8',
    ],
];

$name = (string) ($_GET['file'] ?? '');
if (!isset($assets[$name]) || !is_file($assets[$name]['path'])) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Asset not found.';
    exit;
}

header('Content-Type: ' . $assets[$name]['type']);
header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');
readfile($assets[$name]['path']);

