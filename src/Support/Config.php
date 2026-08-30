<?php

declare(strict_types=1);

namespace Yatsn\Support;

final class Config
{
    private static string $root = '';
    /** @var array<string, mixed> */
    private static array $values = [];

    public static function boot(string $root): void
    {
        self::$root = $root;
        $databasePath = self::resolvePath(env_value('DATABASE_PATH', 'var/data/yatsn.sqlite'));
        $storagePath = self::resolvePath(env_value('PRIVATE_STORAGE_PATH', 'var/storage'));
        $logPath = self::resolvePath(env_value('LOG_PATH', 'var/log'));

        self::$values = [
            'app' => [
                'env' => env_value('APP_ENV', 'development'),
                'debug' => self::toBool(env_value('APP_DEBUG', 'true')),
                'url' => rtrim(env_value('APP_URL', 'http://127.0.0.1:8080') ?? '', '/'),
                'key' => env_value('APP_KEY', ''),
                'timezone' => env_value('APP_TIMEZONE', 'UTC'),
            ],
            'paths' => [
                'root' => $root,
                'database' => $databasePath,
                'storage' => $storagePath,
                'log' => $logPath,
                'tmp' => $storagePath . '/tmp',
                'portraits' => $storagePath . '/portraits',
                'images' => $storagePath . '/images',
            ],
            'gates' => [
                'allow_external_users' => self::toBool(env_value('ALLOW_EXTERNAL_USERS', 'false')),
                'allow_public_registration' => self::toBool(env_value('ALLOW_PUBLIC_REGISTRATION', 'false')),
                'allow_live_payments' => self::toBool(env_value('ALLOW_LIVE_PAYMENTS', 'false')),
                'allow_protected_lyrics_commercial_use' => self::toBool(env_value('ALLOW_PROTECTED_LYRICS_COMMERCIAL_USE', 'false')),
                'ai_providers_enabled' => self::toBool(env_value('AI_PROVIDERS_ENABLED', 'false')),
            ],
            'credits' => [
                'development_monthly' => (int) (env_value('DEVELOPMENT_MONTHLY_CREDITS', '100') ?? '100'),
                'low' => (int) (env_value('LOW_QUALITY_CREDITS', '1') ?? '1'),
                'medium' => (int) (env_value('MEDIUM_QUALITY_CREDITS', '2') ?? '2'),
                'high' => (int) (env_value('HIGH_QUALITY_CREDITS', '3') ?? '3'),
            ],
            'budget' => [
                'monthly_ai_cents' => (int) (env_value('MONTHLY_AI_BUDGET_CENTS', '10000') ?? '10000'),
            ],
            'stripe' => [
                'mode' => env_value('STRIPE_MODE', 'test'),
                'secret_key' => env_value('STRIPE_SECRET_KEY', ''),
                'publishable_key' => env_value('STRIPE_PUBLISHABLE_KEY', ''),
                'webhook_secret' => env_value('STRIPE_WEBHOOK_SECRET', ''),
                'price_id' => env_value('STRIPE_PRICE_ID', ''),
            ],
            'mail' => [
                'transport' => env_value('MAIL_TRANSPORT', 'log'),
                'host' => env_value('MAIL_HOST', ''),
                'port' => (int) (env_value('MAIL_PORT', '465') ?? '465'),
                'encryption' => env_value('MAIL_ENCRYPTION', 'ssl'),
                'username' => env_value('MAIL_USERNAME', ''),
                'password' => env_value('MAIL_PASSWORD', ''),
                'from_address' => env_value('MAIL_FROM_ADDRESS', 'support@youarethesongnow.com'),
                'from_name' => env_value('MAIL_FROM_NAME', 'You Are The Song Now'),
            ],
            'ai' => [
                'groq_api_key' => env_value('GROQ_API_KEY', ''),
                'gemini_api_key' => env_value('GEMINI_API_KEY', ''),
                'fal_key' => env_value('FAL_KEY', ''),
                'replicate_api_token' => env_value('REPLICATE_API_TOKEN', ''),
                'creative_provider' => strtolower((string) env_value('CREATIVE_PROVIDER', 'auto')),
                'image_provider' => strtolower((string) env_value('IMAGE_PROVIDER', 'auto')),
                'gemini_model' => env_value('GEMINI_MODEL', 'gemini-2.5-flash-lite'),
                'groq_model' => env_value('GROQ_MODEL', 'openai/gpt-oss-20b'),
                'fal_image_model' => env_value('FAL_IMAGE_MODEL', 'fal-ai/flux-pro/kontext/multi'),
                'replicate_image_model' => env_value('REPLICATE_IMAGE_MODEL', 'black-forest-labs/flux-schnell'),
                'groq_live_calls' => self::toBool(env_value('GROQ_LIVE_CALLS', 'false')),
                'gemini_live_calls' => self::toBool(env_value('GEMINI_LIVE_CALLS', 'false')),
                'fal_live_calls' => self::toBool(env_value('FAL_LIVE_CALLS', 'false')),
                'replicate_live_calls' => self::toBool(env_value('REPLICATE_LIVE_CALLS', 'false')),
                'allow_deterministic_fallback' => self::toBool(env_value('AI_ALLOW_DETERMINISTIC_FALLBACK', 'false')),
                'text_timeout_seconds' => max(10, min(60, (int) (env_value('AI_TEXT_TIMEOUT_SECONDS', '45') ?? '45'))),
                'image_timeout_seconds' => max(30, min(100, (int) (env_value('AI_IMAGE_TIMEOUT_SECONDS', '85') ?? '85'))),
                'image_download_timeout_seconds' => max(10, min(45, (int) (env_value('AI_IMAGE_DOWNLOAD_TIMEOUT_SECONDS', '30') ?? '30'))),
                'gemini_text_cost_cents' => max(0, (int) (env_value('GEMINI_TEXT_COST_CENTS', '0') ?? '0')),
                'groq_text_cost_cents' => max(0, (int) (env_value('GROQ_TEXT_COST_CENTS', '1') ?? '1')),
                'fal_image_cost_cents' => max(0, (int) (env_value('FAL_IMAGE_COST_CENTS', '4') ?? '4')),
                // The database currently tracks whole cents. One cent is a conservative ceiling for a $0.003 Schnell image.
                'replicate_image_cost_cents' => max(0, (int) (env_value('REPLICATE_IMAGE_COST_CENTS', '1') ?? '1')),
            ],
            'owner' => [
                'email' => env_value('OWNER_EMAIL', ''),
                'password' => env_value('OWNER_PASSWORD', ''),
                'display_name' => env_value('OWNER_DISPLAY_NAME', 'Owner'),
            ],
        ];
    }

    public static function root(): string
    {
        return self::$root;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $value = self::$values;
        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);
        return self::toBool($value);
    }

    public static function getInt(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    public static function all(): array
    {
        return self::$values;
    }

    public static function ensureDirectories(): void
    {
        foreach ([
            dirname((string) self::get('paths.database')),
            self::get('paths.storage'),
            self::get('paths.log'),
            self::get('paths.tmp'),
            self::get('paths.portraits'),
            self::get('paths.images'),
        ] as $dir) {
            if (!is_dir((string) $dir)) {
                mkdir((string) $dir, 0770, true);
            }
        }
    }

    public static function validateOrFail(): array
    {
        $issues = [];
        $key = (string) self::get('app.key', '');
        if ($key === '' || $key === 'replace-with-a-random-local-secret') {
            $issues[] = 'APP_KEY must be set to a random local secret.';
        }
        if (self::getBool('gates.allow_live_payments') && self::get('stripe.mode') !== 'live') {
            $issues[] = 'Live payments cannot be enabled unless STRIPE_MODE=live.';
        }
        if (self::getBool('gates.allow_live_payments')) {
            $issues[] = 'Live payments remain gated for Private Development Build 1.';
        }
        if (self::getBool('gates.allow_public_registration')) {
            $issues[] = 'Public registration remains gated for Private Development Build 1.';
        }
        if (self::getBool('gates.allow_external_users')) {
            $issues[] = 'External users remain gated for Private Development Build 1.';
        }
        return $issues;
    }

    public static function setupStatus(): array
    {
        $ai = \Yatsn\AI\AdapterFactory::runtimeStatus();
        $mail = \Yatsn\Mail\Mailer::transportStatus();
        $stripeCheckout = \Yatsn\Billing\StripeService::checkoutReady();
        $stripeWebhook = \Yatsn\Billing\StripeService::webhookReady();
        return [
            'environment' => self::get('app.env'),
            'externalUsers' => self::getBool('gates.allow_external_users'),
            'publicRegistration' => self::getBool('gates.allow_public_registration'),
            'livePayments' => self::getBool('gates.allow_live_payments'),
            'protectedLyricsCommercialUse' => self::getBool('gates.allow_protected_lyrics_commercial_use'),
            'aiProvidersEnabled' => self::getBool('gates.ai_providers_enabled'),
            'stripeMode' => self::get('stripe.mode'),
            'stripeCheckoutReady' => $stripeCheckout,
            'stripeWebhookReady' => $stripeWebhook,
            'stripeConfigured' => $stripeCheckout && $stripeWebhook,
            'stripeAdapter' => $stripeCheckout ? 'stripe-test-checkout' : 'stripe-unavailable',
            'mail' => $mail,
            'mailTransport' => $mail['activeTransport'],
            'smtpConfigured' => $mail['activeTransport'] === 'smtp',
            'groqConfigured' => $ai['groqKeyPresent'],
            'geminiConfigured' => $ai['geminiKeyPresent'],
            'falConfigured' => $ai['falKeyPresent'],
            'replicateConfigured' => $ai['replicateTokenPresent'],
            'ownerSeedConfigured' => self::get('owner.email') !== '',
            'developmentCredits' => self::getInt('credits.development_monthly'),
            'monthlyAiBudgetCents' => self::getInt('budget.monthly_ai_cents'),
            'creativeAdapter' => $ai['creativeAdapter'],
            'imageAdapter' => $ai['imageAdapter'],
            'ai' => $ai,
        ];
    }

    private static function resolvePath(string $path): string
    {
        if ($path[0] === '/' || preg_match('/^[A-Za-z]:\\\\/', $path) === 1) {
            return $path;
        }
        return self::$root . '/' . ltrim($path, '/');
    }

    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }
}
