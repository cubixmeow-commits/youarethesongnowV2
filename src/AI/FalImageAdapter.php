<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

final class FalImageAdapter implements ImageAdapterInterface
{
    public function name(): string
    {
        return 'fal-image';
    }

    public function isAvailable(): bool
    {
        return Config::getBool('gates.ai_providers_enabled')
            && (string) Config::get('ai.fal_key') !== '';
    }

    public function generate(array $package, array $snapshot): array
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('fal_unavailable');
        }
        // Live fal calls remain opt-in to protect the development budget.
        if (!Config::getBool('ai.fal_live_calls')) {
            throw new \RuntimeException('fal_live_calls_not_enabled');
        }
        throw new \RuntimeException('fal_live_generation_not_wired_for_unattended_spend');
    }
}
