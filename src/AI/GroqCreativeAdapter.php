<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

final class GroqCreativeAdapter implements CreativeAdapterInterface
{
    public function name(): string
    {
        return 'groq-creative';
    }

    public function isAvailable(): bool
    {
        return Config::getBool('gates.ai_providers_enabled')
            && Config::getBool('ai.groq_live_calls')
            && (string) Config::get('ai.groq_api_key') !== '';
    }

    public function buildPackage(array $snapshot): array
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('groq_unavailable');
        }
        $model = (string) Config::get('ai.groq_model', 'openai/gpt-oss-20b');
        $response = ProviderHttp::postJson('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $model,
            'temperature' => 0.7,
            'max_completion_tokens' => 2600,
            'reasoning_effort' => 'low',
            'messages' => [
                ['role' => 'system', 'content' => CreativePackageBuilder::systemPrompt()],
                ['role' => 'user', 'content' => CreativePackageBuilder::userPrompt($snapshot)],
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => ['name' => 'yatsn_song_dna', 'strict' => true, 'schema' => CreativePackageBuilder::schema()],
            ],
        ], ['Authorization: Bearer ' . (string) Config::get('ai.groq_api_key')], Config::getInt('ai.text_timeout_seconds', 45));

        return CreativePackageBuilder::build(
            self::decodeResponse($response),
            $snapshot,
            $this->name() . ':' . $model,
            Config::getInt('ai.groq_text_cost_cents', 1)
        );
    }

    /** @param array<string, mixed> $response @return array<string, mixed> */
    public static function decodeResponse(array $response): array
    {
        $message = $response['choices'][0]['message'] ?? null;
        if (!is_array($message) || !empty($message['refusal'])) {
            throw new \RuntimeException('groq_generation_blocked');
        }
        $decoded = json_decode(trim((string) ($message['content'] ?? '')), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('groq_invalid_creative_json');
        }
        return $decoded;
    }
}
