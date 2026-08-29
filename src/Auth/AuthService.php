<?php

declare(strict_types=1);

namespace Yatsn\Auth;

use Yatsn\Mail\Mailer;
use Yatsn\Support\Audit;
use Yatsn\Support\Config;
use Yatsn\Support\Database;

final class AuthService
{
    public static function requestMagicLink(string $email): void
    {
        $email = strtolower(trim($email));
        // Neutral response always; only send when account exists and is active.
        $user = Database::one(
            'SELECT * FROM users WHERE email = :email AND deleted_at IS NULL',
            ['email' => $email]
        );
        if ($user === null || !in_array($user['account_state'], ['active', 'grace', 'inactive'], true)) {
            return;
        }

        $raw = opaque_id(32);
        $now = now_utc();
        $expires = gmdate('Y-m-d\TH:i:s\Z', time() + 15 * 60);
        Database::exec(
            'INSERT INTO auth_tokens (public_id, user_id, purpose, token_hash, expires_at, created_at)
             VALUES (:pid, :uid, \'magic_link\', :th, :exp, :c)',
            [
                'pid' => opaque_id(),
                'uid' => $user['id'],
                'th' => hash('sha256', $raw),
                'exp' => $expires,
                'c' => $now,
            ]
        );

        $url = app_url('/sign-in/complete?token=' . urlencode($raw));
        Mailer::send(
            $email,
            'Your sign-in link',
            "Use this one-time link to sign in:\n{$url}\n\nIt expires in 15 minutes."
        );
        Audit::record((int) $user['id'], 'auth.magic_link_requested', 'user', $user['public_id']);
    }

    public static function completeMagicLink(string $rawToken): array
    {
        $token = Database::one(
            'SELECT * FROM auth_tokens WHERE token_hash = :th AND purpose = \'magic_link\'',
            ['th' => hash('sha256', $rawToken)]
        );
        if ($token === null || $token['used_at'] !== null) {
            throw new \RuntimeException('token_invalid');
        }
        if ($token['expires_at'] < now_utc()) {
            throw new \RuntimeException('token_expired');
        }

        $user = Database::one('SELECT * FROM users WHERE id = :id AND deleted_at IS NULL', ['id' => $token['user_id']]);
        if ($user === null) {
            throw new \RuntimeException('token_invalid');
        }

        Database::exec('UPDATE auth_tokens SET used_at = :u WHERE id = :id', ['u' => now_utc(), 'id' => $token['id']]);
        $session = SessionService::create((int) $user['id'], (int) $user['security_version']);
        Audit::record((int) $user['id'], 'auth.magic_link_completed', 'user', $user['public_id']);
        \Yatsn\Auth\AccountService::markRecentAuth((int) $user['id']);

        return [
            'user' => InvitationService::publicUser($user),
            'session' => [
                'csrfToken' => $session['csrfToken'],
                'expiresAt' => $session['expiresAt'],
            ],
        ];
    }

    public static function passwordSignIn(string $email, string $password): array
    {
        $email = strtolower(trim($email));
        $user = Database::one('SELECT * FROM users WHERE email = :email AND deleted_at IS NULL', ['email' => $email]);
        if ($user === null || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            throw new \RuntimeException('invalid_credentials');
        }
        if (!in_array($user['account_state'], ['active', 'grace', 'inactive'], true)) {
            throw new \RuntimeException('invalid_credentials');
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            Database::exec(
                'UPDATE users SET password_hash = :h, updated_at = :u WHERE id = :id',
                ['h' => password_hash($password, PASSWORD_DEFAULT), 'u' => now_utc(), 'id' => $user['id']]
            );
        }

        $session = SessionService::create((int) $user['id'], (int) $user['security_version']);
        Audit::record((int) $user['id'], 'auth.password_signin', 'user', $user['public_id']);
        \Yatsn\Auth\AccountService::markRecentAuth((int) $user['id']);

        return [
            'user' => InvitationService::publicUser($user),
            'session' => [
                'csrfToken' => $session['csrfToken'],
                'expiresAt' => $session['expiresAt'],
            ],
        ];
    }

    public static function setPassword(int $userId, string $password): void
    {
        if (strlen($password) < 10) {
            throw new \InvalidArgumentException('password_too_short');
        }
        Database::exec(
            'UPDATE users SET password_hash = :h, updated_at = :u WHERE id = :id',
            ['h' => password_hash($password, PASSWORD_DEFAULT), 'u' => now_utc(), 'id' => $userId]
        );
        Audit::record($userId, 'auth.password_set', 'user', (string) $userId);
    }

    public static function seedOwner(): array
    {
        $email = strtolower(trim((string) Config::get('owner.email')));
        $password = (string) Config::get('owner.password');
        $name = (string) Config::get('owner.display_name', 'Owner');
        if ($email === '' || $password === '') {
            throw new \RuntimeException('OWNER_EMAIL and OWNER_PASSWORD must be set in .env');
        }

        $existing = Database::one('SELECT * FROM users WHERE email = :email', ['email' => $email]);
        $now = now_utc();
        if ($existing !== null) {
            Database::exec(
                'UPDATE users SET role = \'owner\', account_state = \'active\', commercial_access = \'complimentaryReviewer\',
                    membership_status = \'complimentary\', display_name = :name, password_hash = :h,
                    terms_accepted_at = COALESCE(terms_accepted_at, :t), privacy_accepted_at = COALESCE(privacy_accepted_at, :t),
                    onboarding_completed_at = COALESCE(onboarding_completed_at, :t), updated_at = :u
                 WHERE id = :id',
                [
                    'name' => $name,
                    'h' => password_hash($password, PASSWORD_DEFAULT),
                    't' => $now,
                    'u' => $now,
                    'id' => $existing['id'],
                ]
            );
            $userId = (int) $existing['id'];
            $publicId = $existing['public_id'];
        } else {
            $publicId = opaque_id();
            Database::exec(
                'INSERT INTO users (public_id, email, display_name, role, commercial_access, account_state, password_hash,
                    terms_accepted_at, privacy_accepted_at, onboarding_completed_at, membership_status, created_at, updated_at)
                 VALUES (:pid, :email, :name, \'owner\', \'complimentaryReviewer\', \'active\', :h, :t, :t, :t, \'complimentary\', :c, :u)',
                [
                    'pid' => $publicId,
                    'email' => $email,
                    'name' => $name,
                    'h' => password_hash($password, PASSWORD_DEFAULT),
                    't' => $now,
                    'c' => $now,
                    'u' => $now,
                ]
            );
            $userId = (int) Database::lastInsertId();
        }

        // Ensure owner has development credits once.
        $balance = \Yatsn\Credits\CreditService::balance($userId);
        if ($balance < Config::getInt('credits.development_monthly')) {
            \Yatsn\Credits\CreditService::grant(
                $userId,
                Config::getInt('credits.development_monthly') - $balance,
                'Owner development credit top-up',
                'owner-seed-credits-' . $publicId
            );
        }

        Audit::record($userId, 'owner.seeded', 'user', $publicId);
        return ['id' => $publicId, 'email' => $email];
    }
}
