<?php

declare(strict_types=1);

namespace Yatsn\Storage;

use Yatsn\Support\Config;

final class LocalStorage
{
    public static function put(string $relativeKey, string $binary): string
    {
        $full = self::absolute($relativeKey);
        $dir = dirname($full);
        if (!is_dir($dir)) {
            mkdir($dir, 0770, true);
        }
        if (file_put_contents($full, $binary, LOCK_EX) === false) {
            throw new \RuntimeException('storage_write_failed');
        }
        return $relativeKey;
    }

    public static function get(string $relativeKey): string
    {
        $full = self::absolute($relativeKey);
        if (!is_file($full)) {
            throw new \RuntimeException('storage_missing');
        }
        $data = file_get_contents($full);
        if ($data === false) {
            throw new \RuntimeException('storage_read_failed');
        }
        return $data;
    }

    public static function delete(string $relativeKey): void
    {
        $full = self::absolute($relativeKey);
        if (is_file($full)) {
            unlink($full);
        }
    }

    public static function absolute(string $relativeKey): string
    {
        $relativeKey = ltrim(str_replace(['..', '\\'], ['', '/'], $relativeKey), '/');
        return rtrim((string) Config::get('paths.storage'), '/') . '/' . $relativeKey;
    }

    public static function exists(string $relativeKey): bool
    {
        return is_file(self::absolute($relativeKey));
    }
}
