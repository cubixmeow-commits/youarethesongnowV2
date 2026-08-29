<?php

declare(strict_types=1);

namespace Yatsn\Auth;

use Yatsn\Support\Audit;
use Yatsn\Support\Config;
use Yatsn\Support\Database;

final class SessionService
{
    private const COOKIE = 'yatsn_session';
    private const DAYS = 30;

    public static function start(?array $user): void
    {
        // noop marker for clarity
    }

    public static function create(int $userId, int $securityVersion): array
    {
        $publicId = opaque_id();
        $rawToken = opaque_id(32);
        $csrf = opaque_id(24);
        $now = now_utc();
        $expires = gmdate('Y-m-d\TH:i:s\Z', time() + self::DAYS * 86400);

        Database::exec(
            'INSERT INTO sessions (public_id, user_id, token_hash, csrf_token, security_version, ip_hash, user_agent_hash, expires_at, last_seen_at, created_at)
             VALUES (:pid, :uid, :th, :csrf, :sv, :ip, :ua, :exp, :seen, :created)',
            [
                'pid' => $publicId,
                'uid' => $userId,
                'th' => hash('sha256', $rawToken),
                'csrf' => $csrf,
                'sv' => $securityVersion,
                'ip' => isset($_SERVER['REMOTE_ADDR']) ? hash('sha256', (string) $_SERVER['REMOTE_ADDR']) : null,
                'ua' => isset($_SERVER['HTTP_USER_AGENT']) ? hash('sha256', (string) $_SERVER['HTTP_USER_AGENT']) : null,
                'exp' => $expires,
                'seen' => $now,
                'created' => $now,
            ]
        );

        self::setCookie($rawToken, time() + self::DAYS * 86400);

        return [
            'publicId' => $publicId,
            'token' => $rawToken,
            'csrfToken' => $csrf,
            'expiresAt' => $expires,
        ];
    }

    public static function current(): ?array
    {
        $raw = $_COOKIE[self::COOKIE] ?? null;
        if (!$raw) {
            return null;
        }

        $row = Database::one(
            'SELECT s.*, u.public_id AS user_public_id, u.email, u.display_name, u.role, u.commercial_access,
                    u.account_state, u.security_version AS user_security_version, u.membership_status,
                    u.terms_accepted_at, u.privacy_accepted_at, u.onboarding_completed_at, u.password_hash
             FROM sessions s
             INNER JOIN users u ON u.id = s.user_id
             WHERE s.token_hash = :th AND s.revoked_at IS NULL AND u.deleted_at IS NULL',
            ['th' => hash('sha256', $raw)]
        );

        if ($row === null) {
            self::clearCookie();
            return null;
        }

        if ($row['expires_at'] < now_utc()) {
            self::revokeById((int) $row['id']);
            self::clearCookie();
            return null;
        }

        if ((int) $row['security_version'] !== (int) $row['user_security_version']) {
            self::revokeById((int) $row['id']);
            self::clearCookie();
            return null;
        }

        if (in_array($row['account_state'], ['deleted', 'deletionPending'], true)) {
            self::clearCookie();
            return null;
        }

        Database::exec(
            'UPDATE sessions SET last_seen_at = :seen WHERE id = :id',
            ['seen' => now_utc(), 'id' => $row['id']]
        );

        return $row;
    }

    public static function requireUser(): array
    {
        $session = self::current();
        if ($session === null) {
            return [];
        }
        return $session;
    }

    public static function revokeCurrent(): void
    {
        $raw = $_COOKIE[self::COOKIE] ?? null;
        if ($raw) {
            Database::exec(
                'UPDATE sessions SET revoked_at = :r WHERE token_hash = :th AND revoked_at IS NULL',
                ['r' => now_utc(), 'th' => hash('sha256', $raw)]
            );
        }
        self::clearCookie();
    }

    public static function revokeById(int $sessionId): void
    {
        Database::exec(
            'UPDATE sessions SET revoked_at = :r WHERE id = :id AND revoked_at IS NULL',
            ['r' => now_utc(), 'id' => $sessionId]
        );
    }

    public static function revokeAllForUser(int $userId): void
    {
        Database::exec(
            'UPDATE sessions SET revoked_at = :r WHERE user_id = :uid AND revoked_at IS NULL',
            ['r' => now_utc(), 'uid' => $userId]
        );
    }

    public static function verifyCsrf(?string $token): bool
    {
        $session = self::current();
        if ($session === null || $token === null || $token === '') {
            return false;
        }
        return hash_equals((string) $session['csrf_token'], $token);
    }

    private static function setCookie(string $rawToken, int $expires): void
    {
        if (!headers_sent()) {
            setcookie(self::COOKIE, $rawToken, [
                'expires' => $expires,
                'path' => '/',
                'secure' => is_https_request(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
        $_COOKIE[self::COOKIE] = $rawToken;
    }

    private static function clearCookie(): void
    {
        if (!headers_sent()) {
            setcookie(self::COOKIE, '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => is_https_request(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
        unset($_COOKIE[self::COOKIE]);
    }
}
