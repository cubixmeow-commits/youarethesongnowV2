<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

/**
 * Generates a small set of customer-facing visual directions from already-derived Song DNA.
 * No portraits or raw lyrics are sent to this call. The returned style id is an internal bridge
 * to the existing Build 1 generator so the new UX can be tested before the prompt compiler is rebuilt.
 */
final class GeminiExploreService
{
    /** @param array<string,mixed> $songDna @param array<int,array<string,mixed>> $styles */
    public static function directions(array $songDna, array $styles): array
    {
        if (!Config::getBool('gates.ai_providers_enabled')
            || !Config::getBool('ai.gemini_live_calls')
            || (string) Config::get('ai.gemini_api_key') === '') {
            throw new \RuntimeException('gemini_unavailable');
        }

        if ($songDna === [] || trim((string) ($songDna['essence'] ?? '')) === '') {
            throw new \InvalidArgumentException('song_dna_required');
        }

        $catalog = [];
        $allowed = [];
        foreach ($styles as $style) {
            if (!is_array($style)) {
                continue;
            }
            $id = (string) ($style['id'] ?? '');
            $name = (string) ($style['name'] ?? '');
            if ($id === '' || $name === '') {
                continue;
            }
            $allowed[$id] = $style;
            $catalog[] = [
                'id' => $id,
                'name' => $name,
                'description' => (string) ($style['description'] ?? ''),
                'category' => (string) ($style['category'] ?? ''),
            ];
        }
        if ($catalog === []) {
            throw new \RuntimeException('styles_unavailable');
        }

        // Flash-Lite currently has a Gemini Developer API free tier and is well suited to
        // this lightweight structured recommendation step.
        $model = (string) Config::get('ai.gemini_explore_model', 'gemini-2.5-flash-lite');
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $model)) {
            throw new \RuntimeException('gemini_model_invalid');
        }

        $safeDna = self::safeSongDna($songDna);
        $prompt = implode("\n", [
            'You are the visual-direction editor for You Are The Song Now.',
            'Create exactly three distinct visual directions for this already-derived Song DNA.',
            'The directions must feel specific to the emotional meaning, narrative, world, symbolism, and tension in this Song DNA.',
            'Do not quote lyrics, mention lyrics, name the artist, name the song, or refer to music videos.',
            'Do not expose image-model terminology. Write for a normal creative user.',
            'Each direction needs a short evocative name, a 1-2 sentence customer-facing description, and a concise promptHint that can be passed into the existing special-instructions field.',
            'For each direction, choose exactly one internal styleId from the supplied catalog. The internal style is only a compatibility bridge; do not use its catalog name as the direction name unless truly unavoidable.',
            'Rank the strongest overall recommendation first. Make the three directions meaningfully different from one another.',
            'SONG DNA:',
            json_encode($safeDna, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'INTERNAL STYLE CATALOG:',
            json_encode($catalog, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'Return only the requested structured JSON.',
        ]);

        $styleIds = array_keys($allowed);
        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['directions'],
            'properties' => [
                'directions' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 3,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['name', 'description', 'styleId', 'promptHint'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'styleId' => ['type' => 'string', 'enum' => $styleIds],
                            'promptHint' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];

        $response = ProviderHttp::postJson(
            'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent',
            [
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $prompt]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.85,
                    'maxOutputTokens' => 1200,
                    'responseFormat' => [
                        'text' => [
                            'mimeType' => 'application/json',
                            'schema' => $schema,
                        ],
                    ],
                ],
            ],
            ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')],
            Config::getInt('ai.text_timeout_seconds', 45)
        );

        $decoded = GeminiCreativeAdapter::decodeResponse($response);
        $directions = is_array($decoded['directions'] ?? null) ? $decoded['directions'] : [];
        $out = [];
        foreach ($directions as $direction) {
            if (!is_array($direction)) {
                continue;
            }
            $styleId = (string) ($direction['styleId'] ?? '');
            if (!isset($allowed[$styleId])) {
                continue;
            }
            $out[] = [
                'name' => self::line((string) ($direction['name'] ?? ''), 80),
                'description' => self::text((string) ($direction['description'] ?? ''), 320),
                'promptHint' => self::text((string) ($direction['promptHint'] ?? ''), 420),
                'styleId' => $styleId,
                'styleName' => (string) ($allowed[$styleId]['name'] ?? ''),
            ];
        }

        if (count($out) < 3) {
            throw new \RuntimeException('gemini_explore_incomplete');
        }

        return [
            'model' => $model,
            'directions' => array_slice($out, 0, 3),
        ];
    }

    /** @param array<string,mixed> $dna */
    private static function safeSongDna(array $dna): array
    {
        $keys = [
            'essence', 'emotionalArc', 'themes', 'relationshipDynamics', 'narrativeArchetype',
            'originalVisualMoment', 'symbols', 'visualMetaphors', 'mood', 'environment',
            'palette', 'lighting', 'camera', 'composition', 'motion', 'texture', 'subjectRoles',
        ];
        $out = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $dna)) {
                $out[$key] = $dna[$key];
            }
        }
        return $out;
    }

    private static function line(string $value, int $max): string
    {
        return mb_substr(trim((string) preg_replace('/\s+/', ' ', $value)), 0, $max);
    }

    private static function text(string $value, int $max): string
    {
        return mb_substr(trim($value), 0, $max);
    }
}
