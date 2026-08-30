<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\CreativeEngine\DevelopmentCreativeAdapter;
use Yatsn\CreativeEngine\DevelopmentImageAdapter;
use Yatsn\Support\Config;

final class AdapterFactory
{
    public static function creative(): CreativeAdapterInterface
    {
        return self::creativeRetryChain()[0];
    }

    /** @return list<CreativeAdapterInterface> */
    public static function creativeRetryChain(): array
    {
        if (!Config::getBool('gates.ai_providers_enabled')) {
            return [new DevelopmentCreativeAdapter()];
        }
        $provider = (string) Config::get('ai.creative_provider', 'auto');
        $gemini = new GeminiCreativeAdapter();
        $groq = new GroqCreativeAdapter();
        $ordered = match ($provider) {
            'groq' => [$groq, $gemini],
            'deterministic' => [],
            default => [$gemini, $groq],
        };
        $chain = [];
        foreach ($ordered as $adapter) {
            if ($adapter->isAvailable()) {
                $chain[] = $adapter;
            }
        }
        if ($chain === [] || Config::getBool('ai.allow_deterministic_fallback')) {
            $chain[] = new DevelopmentCreativeAdapter();
        }
        return $chain;
    }

    public static function image(): ImageAdapterInterface
    {
        if (!Config::getBool('gates.ai_providers_enabled')) {
            return new DevelopmentImageAdapter();
        }

        $fal = new FalImageAdapter();
        if ($fal->isAvailable()) {
            return $fal;
        }

        return new DevelopmentImageAdapter();
    }

    /** @return list<ImageAdapterInterface> */
    public static function imageRetryChain(): array
    {
        $primary = self::image();
        $chain = [$primary];
        if ($primary->name() !== 'deterministic-development-image' && Config::getBool('ai.allow_deterministic_fallback')) {
            $chain[] = new DevelopmentImageAdapter();
        }
        return $chain;
    }

    public static function runtimeStatus(): array
    {
        $creative = self::creative();
        $image = self::image();
        return [
            'aiProvidersEnabled' => Config::getBool('gates.ai_providers_enabled'),
            'creativeAdapter' => $creative->name(),
            'creativeAdapterAvailable' => $creative->isAvailable(),
            'imageAdapter' => $image->name(),
            'imageAdapterAvailable' => $image->isAvailable(),
            'groqKeyPresent' => Config::get('ai.groq_api_key') !== '',
            'geminiKeyPresent' => Config::get('ai.gemini_api_key') !== '',
            'falKeyPresent' => Config::get('ai.fal_key') !== '',
            'creativeProviderPreference' => Config::get('ai.creative_provider'),
            'geminiLiveCalls' => Config::getBool('ai.gemini_live_calls'),
            'groqLiveCalls' => Config::getBool('ai.groq_live_calls'),
            'falLiveCalls' => Config::getBool('ai.fal_live_calls'),
            'deterministicFallbackAllowed' => Config::getBool('ai.allow_deterministic_fallback'),
            'geminiModel' => Config::get('ai.gemini_model'),
            'groqModel' => Config::get('ai.groq_model'),
            'falImageModel' => Config::get('ai.fal_image_model'),
        ];
    }
}
