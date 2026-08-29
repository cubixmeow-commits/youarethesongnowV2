<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;
use Yatsn\Support\Security;

final class GeminiCreativeAdapter implements CreativeAdapterInterface
{
    public function name(): string
    {
        return 'gemini-creative';
    }

    public function isAvailable(): bool
    {
        return Config::getBool('gates.ai_providers_enabled')
            && (string) Config::get('ai.gemini_api_key') !== '';
    }

    public function buildPackage(array $snapshot): array
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('gemini_unavailable');
        }
        if (!Config::getBool('ai.gemini_live_calls')) {
            $dev = new \Yatsn\CreativeEngine\DevelopmentCreativeAdapter();
            $package = $dev->buildPackage($snapshot);
            $package['adapter'] = $this->name() . '+local-structure';
            $package['compiledPromptSafe'] = Security::redact((string) $package['compiledPromptSafe']);
            return $package;
        }
        throw new \RuntimeException('gemini_live_calls_not_enabled_for_build_1');
    }
}
