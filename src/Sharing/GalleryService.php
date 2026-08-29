<?php

declare(strict_types=1);

namespace Yatsn\Sharing;

use Yatsn\Mail\Mailer;
use Yatsn\Storage\LocalStorage;
use Yatsn\Support\Audit;
use Yatsn\Support\Database;

final class GalleryService
{
    public static function list(int $userId): array
    {
        $rows = Database::all(
            'SELECT * FROM generated_images WHERE user_id = :uid AND deleted_at IS NULL ORDER BY id DESC LIMIT 100',
            ['uid' => $userId]
        );
        return array_map([self::class, 'public'], $rows);
    }

    public static function getOwned(int $userId, string $publicId): array
    {
        $row = Database::one(
            'SELECT * FROM generated_images WHERE public_id = :pid AND user_id = :uid AND deleted_at IS NULL',
            ['pid' => $publicId, 'uid' => $userId]
        );
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        return self::public($row, true);
    }

    public static function content(int $userId, string $publicId, string $variant = 'display'): array
    {
        $row = Database::one(
            'SELECT * FROM generated_images WHERE public_id = :pid AND user_id = :uid AND deleted_at IS NULL',
            ['pid' => $publicId, 'uid' => $userId]
        );
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }
        $key = match ($variant) {
            'full', 'download' => $row['storage_key'],
            'thumb' => $row['thumb_key'],
            default => $row['display_key'],
        };
        return [
            'mime' => $row['mime_type'],
            'bytes' => LocalStorage::get($key),
            'filename' => ($row['title_label'] ?: 'image') . '.jpg',
        ];
    }

    public static function delete(int $userId, string $publicId): void
    {
        $row = Database::one(
            'SELECT * FROM generated_images WHERE public_id = :pid AND user_id = :uid AND deleted_at IS NULL',
            ['pid' => $publicId, 'uid' => $userId]
        );
        if ($row === null) {
            throw new \RuntimeException('not_found');
        }

        Database::exec(
            'UPDATE image_shares SET revoked_at = :r WHERE image_id = :id AND revoked_at IS NULL',
            ['r' => now_utc(), 'id' => $row['id']]
        );
        LocalStorage::delete($row['storage_key']);
        LocalStorage::delete($row['display_key']);
        LocalStorage::delete($row['thumb_key']);
        Database::exec(
            'UPDATE generated_images SET deleted_at = :d WHERE id = :id',
            ['d' => now_utc(), 'id' => $row['id']]
        );
        Audit::record($userId, 'image.deleted', 'generated_image', $publicId);
    }

    public static function createLinkShare(int $userId, string $imagePublicId): array
    {
        $image = Database::one(
            'SELECT * FROM generated_images WHERE public_id = :pid AND user_id = :uid AND deleted_at IS NULL',
            ['pid' => $imagePublicId, 'uid' => $userId]
        );
        if ($image === null) {
            throw new \RuntimeException('not_found');
        }

        Database::exec(
            'UPDATE image_shares SET revoked_at = :r WHERE image_id = :id AND share_type = \'link\' AND revoked_at IS NULL',
            ['r' => now_utc(), 'id' => $image['id']]
        );

        $raw = opaque_id(32);
        $publicId = opaque_id();
        Database::exec(
            'INSERT INTO image_shares (public_id, image_id, user_id, share_type, token_hash, created_at)
             VALUES (:pid, :iid, :uid, \'link\', :th, :c)',
            [
                'pid' => $publicId,
                'iid' => $image['id'],
                'uid' => $userId,
                'th' => hash('sha256', $raw),
                'c' => now_utc(),
            ]
        );

        return [
            'id' => $publicId,
            'url' => app_url('/shared/' . $raw),
            'token' => $raw,
        ];
    }

    public static function revokeLinkShare(int $userId, string $imagePublicId): void
    {
        $image = Database::one(
            'SELECT * FROM generated_images WHERE public_id = :pid AND user_id = :uid AND deleted_at IS NULL',
            ['pid' => $imagePublicId, 'uid' => $userId]
        );
        if ($image === null) {
            throw new \RuntimeException('not_found');
        }
        Database::exec(
            'UPDATE image_shares SET revoked_at = :r WHERE image_id = :id AND share_type = \'link\' AND revoked_at IS NULL',
            ['r' => now_utc(), 'id' => $image['id']]
        );
    }

    public static function emailShare(int $userId, string $imagePublicId, string $recipient): array
    {
        $recipient = strtolower(trim($recipient));
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('invalid_email');
        }
        $image = Database::one(
            'SELECT * FROM generated_images WHERE public_id = :pid AND user_id = :uid AND deleted_at IS NULL',
            ['pid' => $imagePublicId, 'uid' => $userId]
        );
        if ($image === null) {
            throw new \RuntimeException('not_found');
        }

        $existing = Database::one(
            'SELECT id FROM image_shares WHERE image_id = :id AND share_type = \'email\' AND revoked_at IS NULL',
            ['id' => $image['id']]
        );
        if ($existing !== null) {
            throw new \RuntimeException('email_share_already_sent');
        }

        $raw = opaque_id(32);
        $publicId = opaque_id();
        $expires = gmdate('Y-m-d\TH:i:s\Z', time() + 7 * 86400);
        Database::exec(
            'INSERT INTO image_shares (public_id, image_id, user_id, share_type, token_hash, recipient_email, expires_at, created_at)
             VALUES (:pid, :iid, :uid, \'email\', :th, :email, :exp, :c)',
            [
                'pid' => $publicId,
                'iid' => $image['id'],
                'uid' => $userId,
                'th' => hash('sha256', $raw),
                'email' => $recipient,
                'exp' => $expires,
                'c' => now_utc(),
            ]
        );

        $url = app_url('/shared/' . $raw);
        Mailer::send(
            $recipient,
            'A shared image from You Are The Song Now',
            "Someone shared an image with you.\n\nView it here (expires in seven days):\n{$url}"
        );

        return ['id' => $publicId, 'expiresAt' => $expires];
    }

    public static function sharedByToken(string $rawToken): array
    {
        $share = Database::one(
            'SELECT s.*, i.public_id AS image_public_id, i.artist_label, i.title_label, i.style_name, i.orientation,
                    i.display_key, i.mime_type, i.deleted_at
             FROM image_shares s
             INNER JOIN generated_images i ON i.id = s.image_id
             WHERE s.token_hash = :th',
            ['th' => hash('sha256', $rawToken)]
        );
        if ($share === null || $share['revoked_at'] !== null || $share['deleted_at'] !== null) {
            throw new \RuntimeException('share_invalid');
        }
        if ($share['expires_at'] !== null && $share['expires_at'] < now_utc()) {
            throw new \RuntimeException('share_expired');
        }

        return [
            'image' => [
                'id' => $share['image_public_id'],
                'artist' => $share['artist_label'],
                'title' => $share['title_label'],
                'styleName' => $share['style_name'],
                'orientation' => $share['orientation'],
                'contentUrl' => '/api/v1/shared/images/' . urlencode($rawToken) . '/content',
                'downloadUrl' => '/api/v1/shared/images/' . urlencode($rawToken) . '/download',
            ],
        ];
    }

    public static function sharedContent(string $rawToken, bool $download = false): array
    {
        $share = Database::one(
            'SELECT s.*, i.storage_key, i.display_key, i.mime_type, i.title_label, i.deleted_at
             FROM image_shares s
             INNER JOIN generated_images i ON i.id = s.image_id
             WHERE s.token_hash = :th',
            ['th' => hash('sha256', $rawToken)]
        );
        if ($share === null || $share['revoked_at'] !== null || $share['deleted_at'] !== null) {
            throw new \RuntimeException('share_invalid');
        }
        if ($share['expires_at'] !== null && $share['expires_at'] < now_utc()) {
            throw new \RuntimeException('share_expired');
        }
        $key = $download ? $share['storage_key'] : $share['display_key'];
        return [
            'mime' => $share['mime_type'],
            'bytes' => LocalStorage::get($key),
            'filename' => ($share['title_label'] ?: 'image') . '.jpg',
        ];
    }

    public static function public(array $row, bool $detail = false): array
    {
        $share = Database::one(
            'SELECT public_id FROM image_shares WHERE image_id = :id AND share_type = \'link\' AND revoked_at IS NULL ORDER BY id DESC LIMIT 1',
            ['id' => $row['id']]
        );
        $out = [
            'id' => $row['public_id'],
            'artist' => $row['artist_label'],
            'title' => $row['title_label'],
            'styleName' => $row['style_name'],
            'orientation' => $row['orientation'],
            'quality' => $row['quality'],
            'thumbnailUrl' => '/api/v1/images/' . $row['public_id'] . '/content?variant=thumb',
            'contentUrl' => '/api/v1/images/' . $row['public_id'] . '/content',
            'downloadUrl' => '/api/v1/images/' . $row['public_id'] . '/download',
            'shareActive' => $share !== null,
            'createdAt' => $row['created_at'],
        ];
        if ($detail) {
            $out['width'] = (int) $row['width'];
            $out['height'] = (int) $row['height'];
        }
        return $out;
    }
}
