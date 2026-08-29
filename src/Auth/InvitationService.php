<?php

declare(strict_types=1);

namespace Yatsn\Auth;

use Yatsn\Credits\CreditService;
use Yatsn\Mail\Mailer;
use Yatsn\Support\Audit;
use Yatsn\Support\Config;
use Yatsn\Support\Database;

final class InvitationService
{
    public static function create(int $ownerUserId, string $email, string $commercialAccess): array
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('invalid_email');
        }
        if (!in_array($commercialAccess, ['paidBeta', 'complimentaryReviewer'], true)) {
            throw new \InvalidArgumentException('invalid_access');
        }

        $existing = Database::one(
            'SELECT id FROM users WHERE email = :email AND deleted_at IS NULL AND account_state != \'invited\'',
            ['email' => $email]
        );
        if ($existing !== null) {
            throw new \RuntimeException('email_in_use');
        }

        $rawToken = opaque_id(32);
        $publicId = opaque_id();
        $now = now_utc();
        $expires = gmdate('Y-m-d\TH:i:s\Z', time() + 7 * 86400);

        Database::begin();
        try {
            $user = Database::one('SELECT * FROM users WHERE email = :email AND deleted_at IS NULL', ['email' => $email]);
            if ($user === null) {
                Database::exec(
                    'INSERT INTO users (public_id, email, display_name, role, commercial_access, account_state, created_at, updated_at)
                     VALUES (:pid, :email, :name, \'user\', :access, \'invited\', :c, :u)',
                    [
                        'pid' => opaque_id(),
                        'email' => $email,
                        'name' => strstr($email, '@', true) ?: 'Guest',
                        'access' => $commercialAccess,
                        'c' => $now,
                        'u' => $now,
                    ]
                );
                $userId = (int) Database::lastInsertId();
            } else {
                $userId = (int) $user['id'];
                Database::exec(
                    'UPDATE users SET commercial_access = :access, account_state = \'invited\', updated_at = :u WHERE id = :id',
                    ['access' => $commercialAccess, 'u' => $now, 'id' => $userId]
                );
            }

            Database::exec(
                'UPDATE invitations SET status = \'revoked\', revoked_at = :r WHERE email = :email AND status = \'pending\'',
                ['r' => $now, 'email' => $email]
            );

            Database::exec(
                'INSERT INTO invitations (public_id, email, commercial_access, token_hash, status, invited_by_user_id, expires_at, created_at)
                 VALUES (:pid, :email, :access, :th, \'pending\', :oid, :exp, :c)',
                [
                    'pid' => $publicId,
                    'email' => $email,
                    'access' => $commercialAccess,
                    'th' => hash('sha256', $rawToken),
                    'oid' => $ownerUserId,
                    'exp' => $expires,
                    'c' => $now,
                ]
            );
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        $activateUrl = app_url('/activate?token=' . urlencode($rawToken));
        Mailer::send(
            $email,
            'Your invitation to You Are The Song Now',
            "You have been invited to You Are The Song Now.\n\nActivate your account:\n{$activateUrl}\n\nThis link expires in seven days and can be used once."
        );

        Audit::record($ownerUserId, 'invitation.created', 'invitation', $publicId, null, [
            'email' => $email,
            'commercialAccess' => $commercialAccess,
        ]);

        return [
            'id' => $publicId,
            'email' => $email,
            'commercialAccess' => $commercialAccess,
            'expiresAt' => $expires,
            'activationToken' => Config::get('app.env') === 'development' ? $rawToken : null,
        ];
    }

    public static function activate(string $rawToken, string $displayName, bool $acceptTerms, bool $acceptPrivacy): array
    {
        if (!$acceptTerms || !$acceptPrivacy) {
            throw new \InvalidArgumentException('terms_required');
        }

        $invite = Database::one(
            'SELECT * FROM invitations WHERE token_hash = :th',
            ['th' => hash('sha256', $rawToken)]
        );
        if ($invite === null || $invite['status'] !== 'pending') {
            throw new \RuntimeException('invitation_invalid');
        }
        if ($invite['expires_at'] < now_utc()) {
            Database::exec('UPDATE invitations SET status = \'expired\' WHERE id = :id', ['id' => $invite['id']]);
            throw new \RuntimeException('invitation_expired');
        }

        $user = Database::one('SELECT * FROM users WHERE email = :email AND deleted_at IS NULL', ['email' => $invite['email']]);
        if ($user === null) {
            throw new \RuntimeException('invitation_invalid');
        }

        $now = now_utc();
        Database::begin();
        try {
            Database::exec(
                'UPDATE invitations SET status = \'accepted\', accepted_at = :a WHERE id = :id',
                ['a' => $now, 'id' => $invite['id']]
            );
            Database::exec(
                'UPDATE users SET display_name = :name, account_state = \'active\', commercial_access = :access,
                    terms_accepted_at = :t, privacy_accepted_at = :p, onboarding_completed_at = :o, updated_at = :u
                 WHERE id = :id',
                [
                    'name' => trim($displayName) !== '' ? trim($displayName) : $user['display_name'],
                    'access' => $invite['commercial_access'],
                    't' => $now,
                    'p' => $now,
                    'o' => $now,
                    'u' => $now,
                    'id' => $user['id'],
                ]
            );

            if ($invite['commercial_access'] === 'complimentaryReviewer') {
                CreditService::grant(
                    (int) $user['id'],
                    Config::getInt('credits.development_monthly'),
                    'Complimentary reviewer monthly credits',
                    'complimentary-grant-' . $invite['public_id']
                );
                Database::exec(
                    'UPDATE users SET membership_status = \'complimentary\' WHERE id = :id',
                    ['id' => $user['id']]
                );
            }

            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        $session = SessionService::create((int) $user['id'], (int) $user['security_version']);
        Audit::record((int) $user['id'], 'invitation.accepted', 'invitation', $invite['public_id']);
        AccountService::markRecentAuth((int) $user['id']);

        $fresh = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $user['id']]);
        return [
            'user' => self::publicUser($fresh),
            'session' => [
                'csrfToken' => $session['csrfToken'],
                'expiresAt' => $session['expiresAt'],
            ],
        ];
    }

    public static function listForOwner(): array
    {
        $rows = Database::all(
            'SELECT public_id, email, commercial_access, status, expires_at, accepted_at, created_at
             FROM invitations ORDER BY id DESC LIMIT 100'
        );
        return array_map(static fn(array $r): array => [
            'id' => $r['public_id'],
            'email' => $r['email'],
            'commercialAccess' => $r['commercial_access'],
            'status' => $r['status'],
            'expiresAt' => $r['expires_at'],
            'acceptedAt' => $r['accepted_at'],
            'createdAt' => $r['created_at'],
        ], $rows);
    }

    public static function revoke(string $publicId, int $ownerUserId): void
    {
        $invite = Database::one('SELECT * FROM invitations WHERE public_id = :pid', ['pid' => $publicId]);
        if ($invite === null) {
            throw new \RuntimeException('not_found');
        }
        Database::exec(
            'UPDATE invitations SET status = \'revoked\', revoked_at = :r WHERE id = :id AND status = \'pending\'',
            ['r' => now_utc(), 'id' => $invite['id']]
        );
        Audit::record($ownerUserId, 'invitation.revoked', 'invitation', $publicId);
    }

    public static function publicUser(?array $user): ?array
    {
        if ($user === null) {
            return null;
        }
        return [
            'id' => $user['public_id'],
            'email' => $user['email'],
            'displayName' => $user['display_name'],
            'role' => $user['role'],
            'commercialAccess' => $user['commercial_access'],
            'accountState' => $user['account_state'],
            'membershipStatus' => $user['membership_status'],
            'hasPassword' => !empty($user['password_hash']),
            'termsAcceptedAt' => $user['terms_accepted_at'],
            'privacyAcceptedAt' => $user['privacy_accepted_at'],
            'onboardingCompletedAt' => $user['onboarding_completed_at'],
        ];
    }
}
