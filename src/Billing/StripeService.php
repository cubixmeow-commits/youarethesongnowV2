<?php

declare(strict_types=1);

namespace Yatsn\Billing;

use Yatsn\Credits\CreditService;
use Yatsn\Support\Audit;
use Yatsn\Support\Config;
use Yatsn\Support\Database;
use Yatsn\Support\Security;

final class StripeService
{
    public static function membership(int $userId): array
    {
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        return [
            'status' => $user['membership_status'] ?? 'none',
            'commercialAccess' => $user['commercial_access'] ?? 'none',
            'periodEnd' => $user['membership_period_end'],
            'priceCents' => 2000,
            'currency' => 'USD',
            'productName' => 'You Are The Song Now Membership',
            'statementDescriptor' => 'YOU ARE THE SONG',
            'monthlyCredits' => Config::getInt('credits.development_monthly'),
            'livePaymentsEnabled' => Config::getBool('gates.allow_live_payments'),
            'stripeMode' => Config::get('stripe.mode'),
            'checkoutReady' => self::checkoutReady(),
            'portalReady' => self::portalReady(),
            'adapter' => self::checkoutReady() ? 'stripe-test-checkout' : 'stripe-unavailable',
        ];
    }

    public static function checkoutReady(): bool
    {
        if (Config::getBool('gates.allow_live_payments')) {
            return false;
        }
        if (Config::get('stripe.mode') !== 'test') {
            return false;
        }
        $secret = (string) Config::get('stripe.secret_key');
        $price = (string) Config::get('stripe.price_id');
        return $secret !== '' && str_starts_with($secret, 'sk_test_') && $price !== '';
    }

    public static function portalReady(): bool
    {
        return self::checkoutReady();
    }

    public static function webhookReady(): bool
    {
        $secret = (string) Config::get('stripe.webhook_secret');
        return $secret !== '' && str_starts_with($secret, 'whsec_');
    }

    public static function createCheckoutSession(int $userId, string $draftId, string $idempotencyKey): array
    {
        if (Config::getBool('gates.allow_live_payments')) {
            throw new \RuntimeException('live_payments_gated');
        }
        if (!self::checkoutReady()) {
            throw new \RuntimeException('stripe_not_configured');
        }

        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null) {
            throw new \RuntimeException('not_found');
        }

        $payload = [
            'mode' => 'subscription',
            'success_url' => app_url('/create?checkout=success&draftId=' . urlencode($draftId)),
            'cancel_url' => app_url('/create?checkout=cancelled&draftId=' . urlencode($draftId)),
            'line_items' => [[
                'price' => Config::get('stripe.price_id'),
                'quantity' => 1,
            ]],
            'client_reference_id' => $user['public_id'],
            'customer' => [
                'draft_id' => $draftId,
                'user_public_id' => $user['public_id'],
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_public_id' => $user['public_id'],
                    'draft_id' => $draftId,
                ],
            ],
        ];
        if (!empty($user['stripe_customer_id'])) {
            $payload['customer'] = $user['stripe_customer_id'];
        } else {
            $payload['customer_email'] = $user['email'];
        }

        $response = self::stripeRequest('POST', 'https://api.stripe.com/v1/checkout/sessions', $payload, $idempotencyKey);
        if (empty($response['id']) || empty($response['url'])) {
            throw new \RuntimeException('stripe_checkout_failed');
        }

        Audit::record($userId, 'billing.checkout_created', 'user', $user['public_id'], null, [
            'sessionId' => $response['id'],
            'draftId' => $draftId,
        ]);

        return [
            'id' => $response['id'],
            'url' => $response['url'],
            'mode' => 'test',
            'draftId' => $draftId,
        ];
    }

    public static function createPortalSession(int $userId): array
    {
        if (!self::portalReady()) {
            throw new \RuntimeException('stripe_not_configured');
        }
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null || empty($user['stripe_customer_id'])) {
            throw new \RuntimeException('stripe_customer_missing');
        }
        $response = self::stripeRequest('POST', 'https://api.stripe.com/v1/billing_portal/sessions', [
            'customer' => $user['stripe_customer_id'],
            'return_url' => app_url('/account'),
        ]);
        if (empty($response['url'])) {
            throw new \RuntimeException('stripe_portal_failed');
        }
        return ['url' => $response['url']];
    }

    public static function grantDevelopmentMembership(int $userId, string $reason, string $idempotencyKey): array
    {
        if (Config::get('app.env') !== 'development') {
            throw new \RuntimeException('forbidden');
        }
        $user = Database::one('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user === null) {
            throw new \RuntimeException('not_found');
        }

        $existing = Database::one(
            'SELECT id FROM credit_ledger WHERE user_id = :uid AND idempotency_key = :k',
            ['uid' => $userId, 'k' => 'membership-grant-' . $idempotencyKey]
        );
        if ($existing !== null) {
            return self::membership($userId);
        }

        Database::exec(
            'UPDATE users SET membership_status = \'active\', commercial_access = CASE
                WHEN commercial_access = \'complimentaryReviewer\' THEN commercial_access ELSE \'paidBeta\' END,
                membership_period_end = :end, account_state = \'active\', updated_at = :u
             WHERE id = :id',
            [
                'end' => gmdate('Y-m-d\TH:i:s\Z', time() + 30 * 86400),
                'u' => now_utc(),
                'id' => $userId,
            ]
        );
        CreditService::grant(
            $userId,
            Config::getInt('credits.development_monthly'),
            $reason,
            'membership-grant-' . $idempotencyKey
        );
        Audit::record($userId, 'membership.development_granted', 'user', $user['public_id'], $reason);
        return self::membership($userId);
    }

    public static function handleWebhook(string $payload, ?string $signatureHeader): array
    {
        if (!self::webhookReady()) {
            throw new \RuntimeException('stripe_webhook_not_configured');
        }
        self::verifySignature($payload, $signatureHeader, (string) Config::get('stripe.webhook_secret'));

        $event = json_decode($payload, true);
        if (!is_array($event) || empty($event['id']) || empty($event['type'])) {
            throw new \RuntimeException('stripe_event_invalid');
        }

        $existing = Database::one('SELECT id FROM stripe_events WHERE event_id = :id', ['id' => $event['id']]);
        if ($existing !== null) {
            return ['duplicate' => true, 'eventId' => $event['id']];
        }

        // Store only safe metadata, not the full provider payload.
        $safe = [
            'id' => $event['id'],
            'type' => $event['type'],
            'created' => $event['created'] ?? null,
            'object' => $event['data']['object']['object'] ?? null,
            'customer' => $event['data']['object']['customer'] ?? null,
            'client_reference_id' => $event['data']['object']['client_reference_id'] ?? null,
            'subscription' => $event['data']['object']['subscription'] ?? null,
            'metadata' => $event['data']['object']['metadata'] ?? new \stdClass(),
        ];

        Database::exec(
            'INSERT INTO stripe_events (event_id, type, payload_json, processed_at) VALUES (:id, :type, :payload, :p)',
            [
                'id' => $event['id'],
                'type' => $event['type'],
                'payload' => json_encode($safe, JSON_THROW_ON_ERROR),
                'p' => now_utc(),
            ]
        );

        self::applyEvent($event);
        return ['received' => true, 'type' => $event['type'], 'eventId' => $event['id']];
    }

    public static function verifySignature(string $payload, ?string $header, string $secret, int $toleranceSeconds = 300): void
    {
        if ($header === null || $header === '') {
            throw new \RuntimeException('stripe_signature_missing');
        }
        $parts = [];
        foreach (explode(',', $header) as $item) {
            [$k, $v] = array_pad(explode('=', trim($item), 2), 2, null);
            if ($k === 't') {
                $parts['t'] = $v;
            }
            if ($k === 'v1') {
                $parts['v1'][] = $v;
            }
        }
        if (empty($parts['t']) || empty($parts['v1'])) {
            throw new \RuntimeException('stripe_signature_malformed');
        }
        if (abs(time() - (int) $parts['t']) > $toleranceSeconds) {
            throw new \RuntimeException('stripe_signature_expired');
        }
        $signed = $parts['t'] . '.' . $payload;
        $expected = hash_hmac('sha256', $signed, $secret);
        foreach ($parts['v1'] as $candidate) {
            if (hash_equals($expected, (string) $candidate)) {
                return;
            }
        }
        throw new \RuntimeException('stripe_signature_invalid');
    }

    /** Build a valid test signature header for automated tests. */
    public static function signPayloadForTests(string $payload, string $secret, ?int $timestamp = null): string
    {
        $t = $timestamp ?? time();
        $sig = hash_hmac('sha256', $t . '.' . $payload, $secret);
        return 't=' . $t . ',v1=' . $sig;
    }

    private static function applyEvent(array $event): void
    {
        $type = (string) $event['type'];
        $object = $event['data']['object'] ?? [];
        if (!is_array($object)) {
            return;
        }

        if ($type === 'checkout.session.completed') {
            $userPublicId = (string) ($object['client_reference_id'] ?? ($object['metadata']['user_public_id'] ?? ''));
            $user = $userPublicId !== ''
                ? Database::one('SELECT * FROM users WHERE public_id = :p AND deleted_at IS NULL', ['p' => $userPublicId])
                : null;
            if ($user === null) {
                return;
            }
            $customerId = (string) ($object['customer'] ?? '');
            if ($customerId !== '') {
                Database::exec(
                    'UPDATE users SET stripe_customer_id = :c, updated_at = :u WHERE id = :id',
                    ['c' => $customerId, 'u' => now_utc(), 'id' => $user['id']]
                );
            }
            self::activatePaidMembership((int) $user['id'], $user['public_id'], 'Stripe checkout completed', 'stripe-checkout-' . $event['id']);
            return;
        }

        if ($type === 'invoice.paid') {
            $customerId = (string) ($object['customer'] ?? '');
            if ($customerId === '') {
                return;
            }
            $user = Database::one('SELECT * FROM users WHERE stripe_customer_id = :c AND deleted_at IS NULL', ['c' => $customerId]);
            if ($user === null) {
                return;
            }
            self::activatePaidMembership((int) $user['id'], $user['public_id'], 'Stripe invoice paid', 'stripe-invoice-' . $event['id']);
        }
    }

    private static function activatePaidMembership(int $userId, string $publicId, string $reason, string $idempotencyKey): void
    {
        Database::exec(
            'UPDATE users SET membership_status = \'active\', commercial_access = CASE
                WHEN commercial_access = \'complimentaryReviewer\' THEN commercial_access ELSE \'paidBeta\' END,
                membership_period_end = :end, account_state = \'active\', updated_at = :u
             WHERE id = :id',
            [
                'end' => gmdate('Y-m-d\TH:i:s\Z', time() + 30 * 86400),
                'u' => now_utc(),
                'id' => $userId,
            ]
        );
        CreditService::grant(
            $userId,
            Config::getInt('credits.development_monthly'),
            $reason,
            'membership-grant-' . $idempotencyKey
        );
        Audit::record($userId, 'membership.stripe_activated', 'user', $publicId, $reason);
    }

    private static function stripeRequest(string $method, string $url, array $params, ?string $idempotencyKey = null): array
    {
        $secret = (string) Config::get('stripe.secret_key');
        $ch = curl_init($url);
        $headers = ['Authorization: Bearer ' . $secret];
        if ($idempotencyKey) {
            $headers[] = 'Idempotency-Key: ' . $idempotencyKey;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_TIMEOUT => 20,
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($body === false) {
            throw new \RuntimeException(Security::redact('stripe_http_error:' . $err));
        }
        $decoded = json_decode($body, true);
        if ($status >= 400 || !is_array($decoded)) {
            throw new \RuntimeException('stripe_request_failed');
        }
        return $decoded;
    }
}
