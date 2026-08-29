<?php

declare(strict_types=1);

use Yatsn\Support\Config;
use Yatsn\Support\View;

function env_value(string $key, ?string $default = null): ?string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return (string) $value;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function opaque_id(int $bytes = 16): string
{
    return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
}

function now_utc(): string
{
    return gmdate('Y-m-d\TH:i:s\Z');
}

function view(string $template, array $data = []): string
{
    return View::render($template, $data);
}

function redirect(string $path, int $status = 302): never
{
    header('Location: ' . $path, true, $status);
    exit;
}

function is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    $proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
    return strtolower((string) $proto) === 'https';
}

function app_url(string $path = ''): string
{
    $base = rtrim(Config::get('app.url', 'http://127.0.0.1:8080'), '/');
    if ($path === '') {
        return $base;
    }
    return $base . '/' . ltrim($path, '/');
}
