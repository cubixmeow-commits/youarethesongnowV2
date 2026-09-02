<?php

declare(strict_types=1);

namespace Yatsn\Support;

/**
 * Deterministic, path-fingerprinted URLs for first-party frontend assets.
 *
 * Coupled bundles share one release id so the browser cannot mix stale app.js
 * with fresh explore.js after a deploy. Release ids are short public content
 * hashes — never paths, secrets, or env values.
 */
final class AssetRelease
{
    /** @var array<string, list<string>> */
    private const BUNDLES = [
        'core' => [
            'css/app.css',
            'js/app.js',
        ],
        'create' => [
            'css/app.css',
            'js/app.js',
            'js/explore.js',
            'js/song-search.js',
        ],
        'showcase' => [
            'css/app.css',
            'js/app.js',
            'js/showcase.js',
        ],
        'component-lab' => [
            'css/app.css',
            'js/app.js',
            'js/component-lab.js',
        ],
    ];

    public static function url(string $relativePath, string $bundle = 'core'): string
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        if (!preg_match('#^(css|js)/[A-Za-z0-9._-]+$#', $relativePath) === 1) {
            throw new \InvalidArgumentException('Unsupported asset path: ' . $relativePath);
        }

        return '/assets/r/' . self::releaseId($bundle) . '/' . $relativePath;
    }

    public static function releaseId(string $bundle): string
    {
        $paths = self::BUNDLES[$bundle] ?? self::BUNDLES['core'];
        $parts = [];
        foreach ($paths as $path) {
            $full = Config::root() . '/public/assets/' . $path;
            $parts[] = $path . ':' . self::fileDigest($full);
        }

        return substr(hash('sha256', implode("\n", $parts)), 0, 12);
    }

    /** @return list<string> */
    public static function bundlePaths(string $bundle): array
    {
        return self::BUNDLES[$bundle] ?? self::BUNDLES['core'];
    }

    public static function isVersionedAssetRequest(string $path): bool
    {
        return preg_match('#^/assets/r/[a-f0-9]{12}/(?:css|js)/[A-Za-z0-9._-]+$#', $path) === 1;
    }

    public static function resolveVersionedPath(string $path): ?string
    {
        if (!self::isVersionedAssetRequest($path)) {
            return null;
        }

        if (preg_match('#^/assets/r/[a-f0-9]{12}/((?:css|js)/[A-Za-z0-9._-]+)$#', $path, $matches) !== 1) {
            return null;
        }

        return '/assets/' . $matches[1];
    }

    public static function sendImmutableCacheHeaders(): void
    {
        if (headers_sent()) {
            return;
        }
        header('Cache-Control: public, max-age=31536000, immutable');
    }

    public static function sendHtmlNoStoreHeaders(): void
    {
        if (headers_sent()) {
            return;
        }
        header('Cache-Control: no-store');
    }

    private static function fileDigest(string $path): string
    {
        if (!is_file($path)) {
            return 'missing';
        }

        return hash_file('sha256', $path) ?: 'missing';
    }
}
