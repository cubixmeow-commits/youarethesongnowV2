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
        if (!Config::getBool('gates.ai_providers_enabled')) {
            return new DevelopmentCreativeAdapter();
        }

        $groq = new GroqCreativeAdapter();
        if ($groq->isAvailable()) {
            return $groq;
        }

        $gemini = new GeminiCreativeAdapter();
        if ($gemini->isAvailable()) {
            return $gemini;
        }

        return new DevelopmentCreativeAdapter();
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
        if ($primary->name() !== 'deterministic-development-image') {
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
        ];
    }
}
