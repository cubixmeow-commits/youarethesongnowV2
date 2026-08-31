<?php

declare(strict_types=1);

namespace Yatsn\Support;

final class ShowcaseManifest
{
    private static ?array $cache = null;

    public static function load(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $path = YATSN_ROOT . '/public/assets/data/v1-showcase.json';
        if (!is_file($path)) {
            throw new \RuntimeException('V1 showcase manifest is missing.');
        }
        $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        self::$cache = $data;

        return $data;
    }

    public static function hero(): ?array
    {
        foreach (self::load()['items'] as $item) {
            if (!empty($item['featured'])) {
                return $item;
            }
        }

        return self::load()['items'][0] ?? null;
    }
}
