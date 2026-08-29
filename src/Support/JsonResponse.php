<?php

declare(strict_types=1);

namespace Yatsn\Support;

final class JsonResponse
{
    public static function send(mixed $payload, int $status = 200, array $headers = []): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function data(mixed $data, int $status = 200, ?array $meta = null): never
    {
        $payload = ['data' => $data];
        if ($meta !== null) {
            $payload['meta'] = $meta;
        }
        self::send($payload, $status);
    }

    public static function error(
        string $code,
        string $message,
        int $status = 400,
        array $fields = [],
        ?string $requestId = null,
        ?int $retryAfterSeconds = null
    ): never {
        self::send([
            'error' => [
                'code' => $code,
                'message' => $message,
                'fields' => $fields === [] ? new \stdClass() : $fields,
                'requestId' => $requestId ?? bin2hex(random_bytes(8)),
                'retryAfterSeconds' => $retryAfterSeconds,
            ],
        ], $status);
    }
}
