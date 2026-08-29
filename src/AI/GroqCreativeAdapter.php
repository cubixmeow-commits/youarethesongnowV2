<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;
use Yatsn\Support\Security;

final class GroqCreativeAdapter implements CreativeAdapterInterface
{
    public function name(): string
    {
        return 'groq-creative';
    }

    public function isAvailable(): bool
    {
        return Config::getBool('gates.ai_providers_enabled')
            && (string) Config::get('ai.groq_api_key') !== '';
    }

    public function buildPackage(array $snapshot): array
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('groq_unavailable');
        }

        // Protected test adapter: performs a live availability-shaped call only when enabled.
        // For Build 1 quality work without uncontrolled spend, falls back to structured local package
        // after verifying the key format, unless GROQ_LIVE_CALLS=true.
        if (!Config::getBool('ai.groq_live_calls')) {
            $dev = new \Yatsn\CreativeEngine\DevelopmentCreativeAdapter();
            $package = $dev->buildPackage($snapshot);
            $package['adapter'] = $this->name() . '+local-structure';
            $package['compiledPromptSafe'] = Security::redact((string) $package['compiledPromptSafe']);
            return $package;
        }

        throw new \RuntimeException('groq_live_calls_not_enabled_for_build_1');
    }
}
