<?php

declare(strict_types=1);

namespace Yatsn\AI;

/**
 * Minimal server-side HTTP client for AI providers.
 * Provider prompts, response bodies, credentials, and portrait bytes are never logged.
 */
final class ProviderHttp
{
    /** @param array<string, mixed> $payload @param list<string> $headers */
    public static function postJson(string $url, array $payload, array $headers, int $timeoutSeconds): array
    {
        $body = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => min(15, $timeoutSeconds),
            CURLOPT_TIMEOUT => $timeoutSeconds,
            CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/json', 'Accept: application/json'], $headers),
            CURLOPT_MAXREDIRS => 0,
        ]);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if (!is_string($response)) {
            throw new \RuntimeException($error !== '' ? 'provider_network_error' : 'provider_empty_response');
        }
        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException('provider_http_' . $status);
        }
        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('provider_invalid_json');
        }
        return $decoded;
    }

    /** @param list<string> $headers */
    public static function getJson(string $url, array $headers, int $timeoutSeconds): array
    {
        if (!str_starts_with($url, 'https://api.replicate.com/v1/predictions/')) {
            throw new \RuntimeException('provider_poll_url_invalid');
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => min(10, $timeoutSeconds),
            CURLOPT_TIMEOUT => $timeoutSeconds,
            CURLOPT_HTTPHEADER => array_merge(['Accept: application/json'], $headers),
        ]);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if (!is_string($response) || $status < 200 || $status >= 300) {
            throw new \RuntimeException('provider_poll_failed');
        }
        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('provider_invalid_json');
        }
        return $decoded;
    }

    /** @return array{bytes:string,mime:string} */
    public static function getBinary(string $url, int $timeoutSeconds, int $maxBytes = 26214400): array
    {
        if (!str_starts_with($url, 'https://')) {
            throw new \RuntimeException('provider_image_url_invalid');
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_CONNECTTIMEOUT => min(15, $timeoutSeconds),
            CURLOPT_TIMEOUT => $timeoutSeconds,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        ]);
        $bytes = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $mime = strtolower(trim((string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE)));
        curl_close($ch);
        if (!is_string($bytes) || $status < 200 || $status >= 300) {
            throw new \RuntimeException('provider_image_download_failed');
        }
        if (strlen($bytes) > $maxBytes) {
            throw new \RuntimeException('provider_image_too_large');
        }
        $mime = explode(';', $mime)[0];
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new \RuntimeException('provider_image_type_invalid');
        }
        return ['bytes' => $bytes, 'mime' => $mime];
    }
}
