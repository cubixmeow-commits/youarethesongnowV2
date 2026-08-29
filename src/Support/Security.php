<?php

declare(strict_types=1);

namespace Yatsn\Support;

final class Security
{
    public static function appKey(): string
    {
        $key = (string) Config::get('app.key', '');
        if ($key === '' || $key === 'replace-with-a-random-local-secret') {
            throw new \RuntimeException('APP_KEY must be set to a random local secret.');
        }
        return hash('sha256', $key, true);
    }

    public static function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, self::appKey());
    }

    public static function verify(string $payload, string $signature): bool
    {
        return hash_equals(self::sign($payload), $signature);
    }

    public static function encrypt(string $plaintext): string
    {
        $iv = random_bytes(16);
        $cipher = openssl_encrypt($plaintext, 'AES-256-CBC', self::appKey(), OPENSSL_RAW_DATA, $iv);
        if ($cipher === false) {
            throw new \RuntimeException('encrypt_failed');
        }
        return rtrim(strtr(base64_encode($iv . $cipher), '+/', '-_'), '=');
    }

    public static function decrypt(string $token): string
    {
        $raw = base64_decode(strtr($token, '-_', '+/'), true);
        if ($raw === false || strlen($raw) < 17) {
            throw new \RuntimeException('decrypt_failed');
        }
        $iv = substr($raw, 0, 16);
        $cipher = substr($raw, 16);
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', self::appKey(), OPENSSL_RAW_DATA, $iv);
        if ($plain === false) {
            throw new \RuntimeException('decrypt_failed');
        }
        return $plain;
    }

    public static function redact(string $message): string
    {
        $message = preg_replace('/Bearer\s+\S+/i', 'Bearer [redacted]', $message) ?? $message;
        $message = preg_replace('/(sk_(live|test)_)[A-Za-z0-9]+/', '$1[redacted]', $message) ?? $message;
        $message = preg_replace('/(whsec_)[A-Za-z0-9]+/', '$1[redacted]', $message) ?? $message;
        $message = preg_replace('/(token=)[^&\s]+/i', '$1[redacted]', $message) ?? $message;
        return $message;
    }

    public static function sendSecurityHeaders(bool $isHttps): void
    {
        if (headers_sent()) {
            return;
        }
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:; style-src 'self' 'unsafe-inline'; script-src 'self'; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
        if ($isHttps) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        header_remove('X-Powered-By');
    }
}
