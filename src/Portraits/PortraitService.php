<?php

declare(strict_types=1);

namespace Yatsn\Portraits;

use Yatsn\Storage\LocalStorage;
use Yatsn\Support\Database;

final class PortraitService
{
    private const MAX_ACTIVE = 10;
    private const MAX_BYTES = 20 * 1024 * 1024;

    public static function list(int $userId): array
    {
        $rows = Database::all(
            'SELECT * FROM portraits WHERE user_id = :uid AND deleted_at IS NULL ORDER BY id DESC',
            ['uid' => $userId]
        );
        return array_map([self::class, 'public'], $rows);
    }

    public static function upload(int $userId, array $file): array
    {
        $count = Database::one(
            'SELECT COUNT(*) AS c FROM portraits WHERE user_id = :uid AND deleted_at IS NULL',
            ['uid' => $userId]
        );
        if ((int) ($count['c'] ?? 0) >= self::MAX_ACTIVE) {
            throw new \RuntimeException('portrait_limit');
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('upload_failed');
        }
        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new \RuntimeException('upload_too_large');
        }

        $tmp = $file['tmp_name'];
        $info = @getimagesize($tmp);
        if ($info === false) {
            throw new \RuntimeException('invalid_image');
        }

        $mime = $info['mime'] ?? '';
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new \RuntimeException('invalid_image_type');
        }

        $binary = file_get_contents($tmp);
        if ($binary === false) {
            throw new \RuntimeException('upload_failed');
        }

        $image = self::decode($binary, $mime);
        if ($image === null) {
            throw new \RuntimeException('invalid_image');
        }
        $image = self::applyExifOrientation($tmp, $mime, $image);

        $normalized = self::normalizeAdaptive($image, 2048);
        $thumb = self::normalizeAdaptive($image, 320);

        $publicId = opaque_id();
        $storageKey = 'portraits/' . $userId . '/' . $publicId . '.jpg';
        $thumbKey = 'portraits/' . $userId . '/' . $publicId . '_thumb.jpg';

        LocalStorage::put($storageKey, self::encodeJpeg($normalized));
        LocalStorage::put($thumbKey, self::encodeJpeg($thumb));
        $width = imagesx($normalized);
        $height = imagesy($normalized);
        $bytes = strlen(self::encodeJpeg($normalized));

        // Delete original temp if still present (PHP usually handles uploaded tmp).
        if (is_file($tmp)) {
            @unlink($tmp);
        }

        Database::exec(
            'INSERT INTO portraits (public_id, user_id, storage_key, thumb_key, mime_type, width, height, byte_size, created_at)
             VALUES (:pid, :uid, :sk, :tk, \'image/jpeg\', :w, :h, :b, :c)',
            [
                'pid' => $publicId,
                'uid' => $userId,
                'sk' => $storageKey,
                'tk' => $thumbKey,
                'w' => $width,
                'h' => $height,
                'b' => $bytes,
                'c' => now_utc(),
            ]
        );

        $row = Database::one('SELECT * FROM portraits WHERE public_id = :pid', ['pid' => $publicId]);
        return self::public($row);
    }

    public static function findOwned(int $userId, string $publicId): ?array
    {
        return Database::one(
            'SELECT * FROM portraits WHERE public_id = :pid AND user_id = :uid AND deleted_at IS NULL',
            ['pid' => $publicId, 'uid' => $userId]
        );
    }

    public static function delete(int $userId, string $publicId): void
    {
        $row = self::findOwned($userId, $publicId);
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        LocalStorage::delete($row['storage_key']);
        LocalStorage::delete($row['thumb_key']);
        Database::exec(
            'UPDATE portraits SET deleted_at = :d WHERE id = :id',
            ['d' => now_utc(), 'id' => $row['id']]
        );
    }

    public static function content(int $userId, string $publicId, bool $thumb = false): array
    {
        $row = self::findOwned($userId, $publicId);
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        $key = $thumb ? $row['thumb_key'] : $row['storage_key'];
        return [
            'mime' => 'image/jpeg',
            'bytes' => LocalStorage::get($key),
            'filename' => basename($key),
        ];
    }

    public static function public(?array $row): array
    {
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        return [
            'id' => $row['public_id'],
            'width' => (int) $row['width'],
            'height' => (int) $row['height'],
            'byteSize' => (int) $row['byte_size'],
            'contentUrl' => '/api/v1/portraits/' . $row['public_id'] . '/content',
            'thumbnailUrl' => '/api/v1/portraits/' . $row['public_id'] . '/content?thumb=1',
            'createdAt' => $row['created_at'],
        ];
    }

    /** @return \GdImage|null */
    private static function decode(string $binary, string $mime)
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromstring($binary),
            'image/png' => @imagecreatefromstring($binary),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromstring($binary) : false,
            default => false,
        };
        return $image instanceof \GdImage ? $image : null;
    }

    /** @param \GdImage $source @return \GdImage */
    private static function normalizeAdaptive($source, int $maxEdge)
    {
        $w = imagesx($source);
        $h = imagesy($source);
        $scale = min(1.0, $maxEdge / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $dst = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dst, $source, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return $dst;
    }

    /** @param \GdImage $image @return \GdImage */
    private static function applyExifOrientation(string $path, string $mime, $image)
    {
        if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
            return $image;
        }
        $exif = @exif_read_data($path);
        if (!is_array($exif) || empty($exif['Orientation'])) {
            return $image;
        }
        return match ((int) $exif['Orientation']) {
            3 => imagerotate($image, 180, 0) ?: $image,
            6 => imagerotate($image, -90, 0) ?: $image,
            8 => imagerotate($image, 90, 0) ?: $image,
            default => $image,
        };
    }

    /** @param \GdImage $source @return \GdImage */
    private static function normalize($source, int $maxEdge)
    {
        return self::normalizeAdaptive($source, $maxEdge);
    }

    /** @param \GdImage $image */
    private static function encodeJpeg($image): string
    {
        ob_start();
        imagejpeg($image, null, 90);
        return (string) ob_get_clean();
    }
}
