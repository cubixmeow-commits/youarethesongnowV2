<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

final class GeminiCreativeAdapter implements CreativeAdapterInterface
{
    public function name(): string
    {
        return 'gemini-creative';
    }

    public function isAvailable(): bool
    {
        return Config::getBool('gates.ai_providers_enabled')
            && Config::getBool('ai.gemini_live_calls')
            && (string) Config::get('ai.gemini_api_key') !== '';
    }

    public function buildPackage(array $snapshot): array
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('gemini_unavailable');
        }
        $model = (string) Config::get('ai.gemini_model', 'gemini-2.5-flash-lite');
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $model)) {
            throw new \RuntimeException('gemini_model_invalid');
        }
        // The cron worker repeats the grounded lookup because raw lyrics cannot
        // cross the request boundary without being stored. They remain in memory
        // only for this call and are discarded after Song DNA is built.
        $analysisSnapshot = $snapshot;
        $research = GeminiLyricsResearchService::lookup(
            (string) ($snapshot['artist'] ?? ''),
            (string) ($snapshot['title'] ?? '')
        );
        if (!empty($research['lyricsFound']) && is_string($research['lyrics'] ?? null)) {
            $analysisSnapshot['_developmentLyrics'] = $research['lyrics'];
            $analysisSnapshot['_developmentLyricsGrounded'] = !empty($research['grounded']);
        }

        $response = ProviderHttp::postJson(
            'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent',
            [
                'systemInstruction' => ['parts' => [['text' => CreativePackageBuilder::systemPrompt()]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => CreativePackageBuilder::userPrompt($analysisSnapshot)]]]],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2600,
                    'responseFormat' => [
                        'text' => [
                            'mimeType' => 'application/json',
                            'schema' => CreativePackageBuilder::schema(),
                        ],
                    ],
                ],
            ],
            ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')],
            Config::getInt('ai.text_timeout_seconds', 45)
        );

        return CreativePackageBuilder::build(
            self::decodeResponse($response),
            $snapshot,
            $this->name() . ':' . $model,
            Config::getInt('ai.gemini_text_cost_cents', 0)
        );
    }

    /** @param array<string, mixed> $response @return array<string, mixed> */
    public static function decodeResponse(array $response): array
    {
        $finish = (string) ($response['candidates'][0]['finishReason'] ?? '');
        if ($finish !== '' && !in_array($finish, ['STOP', 'MAX_TOKENS'], true)) {
            throw new \RuntimeException('gemini_generation_blocked');
        }
        $parts = $response['candidates'][0]['content']['parts'] ?? [];
        $text = '';
        if (is_array($parts)) {
            foreach ($parts as $part) {
                if (is_array($part) && isset($part['text'])) {
                    $text .= (string) $part['text'];
                }
            }
        }
        $decoded = json_decode(trim($text), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('gemini_invalid_creative_json');
        }
        return $decoded;
    }
}
