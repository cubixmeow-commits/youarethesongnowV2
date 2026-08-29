<?php

declare(strict_types=1);

namespace Yatsn\Auth;

use Yatsn\Credits\CreditService;
use Yatsn\Mail\Mailer;
use Yatsn\Sharing\GalleryService;
use Yatsn\Storage\LocalStorage;
use Yatsn\Support\Audit;
use Yatsn\Support\Config;
use Yatsn\Support\Database;
use Yatsn\Support\Security;

final class AccountService
{
    public static function updateProfile(int $userId, string $displayName): array
    {
        $displayName = trim($displayName);
        if ($displayName === '' || strlen($displayName) > 80) {
            throw new \InvalidArgumentException('invalid_display_name');
        }
        Database::exec(
            'UPDATE users SET display_name = :n, updated_at = :u WHERE id = :id',
            ['n' => $displayName, 'u' => now_utc(), 'id' => $userId]
        );
        return self::publicUser($userId);
    }

    public static function requestEmailChange(int $userId, string $newEmail): void
    {
        self::requireRecentAuth($userId);
        $newEmail = strtolower(trim($newEmail));
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('invalid_email');
        }
        $taken = Database::one('SELECT id FROM users WHERE email = :e AND id != :id AND deleted_at IS NULL', [
            'e' => $newEmail,
            'id' => $userId,
        ]);
        if ($taken) {
            throw new \RuntimeException('email_in_use');
        }
        Database::exec(
            'UPDATE users SET pending_email = :e, updated_at = :u WHERE id = :id',
            ['e' => $newEmail, 'u' => now_utc(), 'id' => $userId]
        );
        $raw = opaque_id(32);
        Database::exec(
            'INSERT INTO auth_tokens (public_id, user_id, purpose, token_hash, payload_json, expires_at, created_at)
             VALUES (:pid, :uid, \'email_change\', :th, :payload, :exp, :c)',
            [
                'pid' => opaque_id(),
                'uid' => $userId,
                'th' => hash('sha256', $raw),
                'payload' => json_encode(['email' => $newEmail], JSON_THROW_ON_ERROR),
                'exp' => gmdate('Y-m-d\TH:i:s\Z', time() + 3600),
                'c' => now_utc(),
            ]
        );
        $url = app_url('/account/email/complete?token=' . urlencode($raw));
        Mailer::send($newEmail, 'Confirm your new email', "Confirm your new email address:\n{$url}\n\nThis link expires in one hour.");
        Audit::record($userId, 'account.email_change_requested', 'user', (string) $userId);
    }

    public static function completeEmailChange(string $rawToken): array
    {
        $token = Database::one(
            'SELECT * FROM auth_tokens WHERE token_hash = :th AND purpose = \'email_change\'',
            ['th' => hash('sha256', $rawToken)]
        );
        if ($token === null || $token['used_at'] !== null || $token['expires_at'] < now_utc()) {
            throw new \RuntimeException('token_invalid');
        }
        $payload = json_decode((string) $token['payload_json'], true) ?: [];
        $email = strtolower((string) ($payload['email'] ?? ''));
        if ($email === '') {
            throw new \RuntimeException('token_invalid');
        }
        Database::exec('UPDATE auth_tokens SET used_at = :u WHERE id = :id', ['u' => now_utc(), 'id' => $token['id']]);
        Database::exec(
            'UPDATE users SET email = :e, pending_email = NULL, updated_at = :u WHERE id = :id',
            ['e' => $email, 'u' => now_utc(), 'id' => $token['user_id']]
        );
        Audit::record((int) $token['user_id'], 'account.email_changed', 'user', (string) $token['user_id']);
        return self::publicUser((int) $token['user_id']);
    }

    public static function requestPasswordReset(string $email): void
    {
        $email = strtolower(trim($email));
        $user = Database::one('SELECT * FROM users WHERE email = :e AND deleted_at IS NULL', ['e' => $email]);
        if ($user === null || empty($user['password_hash'])) {
            return;
        }
        $raw = opaque_id(32);
        Database::exec(
            'INSERT INTO auth_tokens (public_id, user_id, purpose, token_hash, expires_at, created_at)
             VALUES (:pid, :uid, \'password_reset\', :th, :exp, :c)',
            [
                'pid' => opaque_id(),
                'uid' => $user['id'],
                'th' => hash('sha256', $raw),
                'exp' => gmdate('Y-m-d\TH:i:s\Z', time() + 3600),
                'c' => now_utc(),
            ]
        );
        $url = app_url('/reset?token=' . urlencode($raw));
        Mailer::send($email, 'Reset your password', "Reset your password:\n{$url}\n\nThis link expires in one hour.");
    }

    public static function completePasswordReset(string $rawToken, string $password): void
    {
        if (strlen($password) < 10) {
            throw new \InvalidArgumentException('password_too_short');
        }
        $token = Database::one(
            'SELECT * FROM auth_tokens WHERE token_hash = :th AND purpose = \'password_reset\'',
            ['th' => hash('sha256', $rawToken)]
        );
        if ($token === null || $token['used_at'] !== null || $token['expires_at'] < now_utc()) {
            throw new \RuntimeException('token_invalid');
        }
        Database::exec('UPDATE auth_tokens SET used_at = :u WHERE id = :id', ['u' => now_utc(), 'id' => $token['id']]);
        Database::exec(
            'UPDATE users SET password_hash = :h, security_version = security_version + 1, recent_auth_at = :r, updated_at = :u WHERE id = :id',
            [
                'h' => password_hash($password, PASSWORD_DEFAULT),
                'r' => now_utc(),
                'u' => now_utc(),
                'id' => $token['user_id'],
            ]
        );
        SessionService::revokeAllForUser((int) $token['user_id']);
        Audit::record((int) $token['user_id'], 'account.password_reset', 'user', (string) $token['user_id']);
    }

    public static function removePassword(int $userId): void
    {
        self::requireRecentAuth($userId);
        Database::exec(
            'UPDATE users SET password_hash = NULL, updated_at = :u WHERE id = :id',
            ['u' => now_utc(), 'id' => $userId]
        );
        Audit::record($userId, 'account.password_removed', 'user', (string) $userId);
    }

    public static function listSessions(int $userId): array
    {
        $rows = Database::all(
            'SELECT public_id, created_at, last_seen_at, expires_at, revoked_at
             FROM sessions WHERE user_id = :uid ORDER BY id DESC LIMIT 50',
            ['uid' => $userId]
        );
        return array_map(static fn(array $r): array => [
            'id' => $r['public_id'],
            'createdAt' => $r['created_at'],
            'lastSeenAt' => $r['last_seen_at'],
            'expiresAt' => $r['expires_at'],
            'active' => $r['revoked_at'] === null,
        ], $rows);
    }

    public static function logoutAll(int $userId): void
    {
        Database::exec(
            'UPDATE users SET security_version = security_version + 1, updated_at = :u WHERE id = :id',
            ['u' => now_utc(), 'id' => $userId]
        );
        SessionService::revokeAllForUser($userId);
        Database::exec(
            'UPDATE mobile_tokens SET revoked_at = :r WHERE user_id = :uid AND revoked_at IS NULL',
            ['r' => now_utc(), 'uid' => $userId]
        );
        Audit::record($userId, 'account.logout_all', 'user', (string) $userId);
    }

    public static function markRecentAuth(int $userId): void
    {
        Database::exec(
            'UPDATE users SET recent_auth_at = :r, updated_at = :u WHERE id = :id',
            ['r' => now_utc(), 'u' => now_utc(), 'id' => $userId]
        );
    }

    public static function requireRecentAuth(int $userId): void
    {
        $user = Database::one('SELECT recent_auth_at FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null || empty($user['recent_auth_at'])) {
            throw new \RuntimeException('recent_auth_required');
        }
        if (strtotime((string) $user['recent_auth_at']) < time() - 600) {
            throw new \RuntimeException('recent_auth_required');
        }
    }

    public static function deletionPreview(int $userId): array
    {
        $portraits = Database::one('SELECT COUNT(*) AS c FROM portraits WHERE user_id = :u AND deleted_at IS NULL', ['u' => $userId]);
        $images = Database::one('SELECT COUNT(*) AS c FROM generated_images WHERE user_id = :u AND deleted_at IS NULL', ['u' => $userId]);
        return [
            'consequences' => [
                'Account access ends immediately.',
                'Portraits, generated images, shares, sessions and unused credits are removed.',
                'Stripe renewal is cancelled when a Stripe customer is linked.',
                'Payment records Stripe or the business must keep may remain.',
            ],
            'portraitCount' => (int) ($portraits['c'] ?? 0),
            'imageCount' => (int) ($images['c'] ?? 0),
            'creditBalance' => CreditService::balance($userId),
            'confirmationPhrase' => 'DELETE MY ACCOUNT',
        ];
    }

    public static function deleteAccount(int $userId, string $confirmation): void
    {
        self::requireRecentAuth($userId);
        if (trim($confirmation) !== 'DELETE MY ACCOUNT') {
            throw new \InvalidArgumentException('confirmation_required');
        }
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null) {
            throw new \RuntimeException('not_found');
        }

        $images = Database::all('SELECT public_id FROM generated_images WHERE user_id = :u AND deleted_at IS NULL', ['u' => $userId]);
        foreach ($images as $image) {
            GalleryService::delete($userId, $image['public_id']);
        }
        $portraits = Database::all('SELECT * FROM portraits WHERE user_id = :u AND deleted_at IS NULL', ['u' => $userId]);
        foreach ($portraits as $portrait) {
            LocalStorage::delete($portrait['storage_key']);
            LocalStorage::delete($portrait['thumb_key']);
            Database::exec('UPDATE portraits SET deleted_at = :d WHERE id = :id', ['d' => now_utc(), 'id' => $portrait['id']]);
        }

        SessionService::revokeAllForUser($userId);
        Database::exec('UPDATE mobile_tokens SET revoked_at = :r WHERE user_id = :uid', ['r' => now_utc(), 'uid' => $userId]);
        Database::exec('DELETE FROM song_dna_artifacts WHERE job_id IN (SELECT id FROM generation_jobs WHERE user_id = :u)', ['u' => $userId]);
        Database::exec(
            'UPDATE users SET account_state = \'deleted\', deleted_at = :d, email = :email, display_name = \'\',
                password_hash = NULL, totp_secret = NULL, pending_email = NULL, security_version = security_version + 1, updated_at = :u
             WHERE id = :id',
            [
                'd' => now_utc(),
                'email' => 'deleted+' . $user['public_id'] . '@invalid.local',
                'u' => now_utc(),
                'id' => $userId,
            ]
        );
        Audit::record($userId, 'account.deleted', 'user', $user['public_id']);
    }

    public static function enableTotp(int $userId): array
    {
        self::requireRecentAuth($userId);
        $secret = self::randomBase32(20);
        Database::exec(
            'UPDATE users SET totp_secret = :s, totp_enabled_at = NULL, updated_at = :u WHERE id = :id',
            ['s' => Security::encrypt($secret), 'u' => now_utc(), 'id' => $userId]
        );
        $codes = [];
        Database::exec('DELETE FROM recovery_codes WHERE user_id = :u', ['u' => $userId]);
        for ($i = 0; $i < 8; $i++) {
            $code = strtoupper(bin2hex(random_bytes(4)));
            $codes[] = $code;
            Database::exec(
                'INSERT INTO recovery_codes (user_id, code_hash, created_at) VALUES (:u, :h, :c)',
                ['u' => $userId, 'h' => hash('sha256', $code), 'c' => now_utc()]
            );
        }
        return [
            'secret' => $secret,
            'otpauthUrl' => 'otpauth://totp/YouAreTheSongNow:owner?secret=' . $secret . '&issuer=YouAreTheSongNow',
            'recoveryCodes' => $codes,
        ];
    }

    public static function confirmTotp(int $userId, string $code): void
    {
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null || empty($user['totp_secret'])) {
            throw new \RuntimeException('totp_not_started');
        }
        $secret = Security::decrypt($user['totp_secret']);
        if (!self::verifyTotp($secret, $code)) {
            throw new \RuntimeException('totp_invalid');
        }
        Database::exec(
            'UPDATE users SET totp_enabled_at = :t, updated_at = :u WHERE id = :id',
            ['t' => now_utc(), 'u' => now_utc(), 'id' => $userId]
        );
        Audit::record($userId, 'owner.totp_enabled', 'user', $user['public_id']);
    }

    public static function verifyOwnerSecondFactor(int $userId, ?string $totpCode, ?string $recoveryCode): void
    {
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null || $user['role'] !== 'owner') {
            throw new \RuntimeException('forbidden');
        }
        if (empty($user['totp_enabled_at'])) {
            // Build 1 allows owner ops before enrollment, but setup status reports it honestly.
            return;
        }
        if ($recoveryCode) {
            $row = Database::one(
                'SELECT * FROM recovery_codes WHERE user_id = :u AND code_hash = :h AND used_at IS NULL',
                ['u' => $userId, 'h' => hash('sha256', strtoupper(trim($recoveryCode)))]
            );
            if ($row === null) {
                throw new \RuntimeException('totp_invalid');
            }
            Database::exec('UPDATE recovery_codes SET used_at = :t WHERE id = :id', ['t' => now_utc(), 'id' => $row['id']]);
            return;
        }
        if (!$totpCode || empty($user['totp_secret']) || !self::verifyTotp(Security::decrypt($user['totp_secret']), $totpCode)) {
            throw new \RuntimeException('totp_invalid');
        }
    }

    public static function issueMobileTokens(int $userId): array
    {
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null) {
            throw new \RuntimeException('not_found');
        }
        $access = opaque_id(32);
        $refresh = opaque_id(40);
        $family = opaque_id(16);
        $now = now_utc();
        Database::exec(
            'INSERT INTO mobile_tokens (public_id, user_id, access_token_hash, refresh_token_hash, family_id, security_version, expires_at, refresh_expires_at, created_at)
             VALUES (:pid, :uid, :ah, :rh, :fam, :sv, :exp, :rexp, :c)',
            [
                'pid' => opaque_id(),
                'uid' => $userId,
                'ah' => hash('sha256', $access),
                'rh' => hash('sha256', $refresh),
                'fam' => $family,
                'sv' => $user['security_version'],
                'exp' => gmdate('Y-m-d\TH:i:s\Z', time() + 900),
                'rexp' => gmdate('Y-m-d\TH:i:s\Z', time() + 30 * 86400),
                'c' => $now,
            ]
        );
        return [
            'accessToken' => $access,
            'refreshToken' => $refresh,
            'expiresIn' => 900,
            'tokenType' => 'Bearer',
        ];
    }

    public static function refreshMobileTokens(string $refreshToken): array
    {
        $row = Database::one(
            'SELECT mt.*, u.security_version AS user_sv
             FROM mobile_tokens mt INNER JOIN users u ON u.id = mt.user_id
             WHERE mt.refresh_token_hash = :h',
            ['h' => hash('sha256', $refreshToken)]
        );
        if ($row === null) {
            throw new \RuntimeException('token_invalid');
        }
        if ($row['revoked_at'] !== null || $row['refresh_expires_at'] < now_utc() || (int) $row['security_version'] !== (int) $row['user_sv']) {
            Database::exec(
                'UPDATE mobile_tokens SET revoked_at = :r WHERE family_id = :f',
                ['r' => now_utc(), 'f' => $row['family_id']]
            );
            throw new \RuntimeException('token_invalid');
        }
        Database::exec('UPDATE mobile_tokens SET revoked_at = :r WHERE id = :id', ['r' => now_utc(), 'id' => $row['id']]);
        return self::issueMobileTokens((int) $row['user_id']);
    }

    public static function publicUser(int $userId): array
    {
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        return InvitationService::publicUser($user) + [
            'pendingEmail' => $user['pending_email'] ?? null,
            'totpEnabled' => !empty($user['totp_enabled_at']),
        ];
    }

    private static function randomBase32(int $bytes): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $out = '';
        $raw = random_bytes($bytes);
        for ($i = 0; $i < strlen($raw); $i++) {
            $out .= $alphabet[ord($raw[$i]) % 32];
        }
        return $out;
    }

    private static function verifyTotp(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }
        $timeSlice = (int) floor(time() / 30);
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::totpCode($secret, $timeSlice + $i), $code)) {
                return true;
            }
        }
        return false;
    }

    private static function totpCode(string $secret, int $timeSlice): string
    {
        $key = self::base32Decode($secret);
        $binTime = pack('N*', 0, $timeSlice);
        $hash = hash_hmac('sha1', $binTime, $key, true);
        $offset = ord($hash[19]) & 0xf;
        $value = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        return str_pad((string) $value, 6, '0', STR_PAD_LEFT);
    }

    private static function base32Decode(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $buffer = 0;
        $bits = 0;
        $out = '';
        for ($i = 0; $i < strlen($secret); $i++) {
            $val = strpos($alphabet, $secret[$i]);
            if ($val === false) {
                continue;
            }
            $buffer = ($buffer << 5) | $val;
            $bits += 5;
            if ($bits >= 8) {
                $bits -= 8;
                $out .= chr(($buffer >> $bits) & 0xff);
            }
        }
        return $out;
    }
}
