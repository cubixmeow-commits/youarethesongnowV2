<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

/**
 * Generates a small set of customer-facing visual directions from already-derived Song DNA.
 * No portraits or raw lyrics are sent to this call. The returned style id is an internal bridge
 * to the existing Build 1 generator so the new UX can be tested before the prompt compiler is rebuilt.
 *
 * Uses the same Gemini API key / live-call gates as GeminiLyricsResearchService. The Explore model
 * defaults to the proven general GEMINI_MODEL; GEMINI_EXPLORE_MODEL is an optional override.
 */
final class GeminiExploreService
{
    private static string $lastDiagnostic = '';

    /** @var null|callable(string,array,array,int):array */
    private static $transport = null;

    /** @param array<string,mixed> $songDna @param array<int,array<string,mixed>> $styles */
    public static function directions(array $songDna, array $styles): array
    {
        self::$lastDiagnostic = '';

        $gateFailure = self::availabilityFailure();
        if ($gateFailure !== null) {
            self::rememberDiagnostic($gateFailure);
            self::logDiagnostic($gateFailure);
            throw new \RuntimeException('gemini_unavailable');
        }

        if ($songDna === [] || trim((string) ($songDna['essence'] ?? '')) === '') {
            self::rememberDiagnostic('song-dna-required');
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
            self::rememberDiagnostic('styles-unavailable');
            throw new \RuntimeException('styles_unavailable');
        }

        $primaryModel = self::resolveModel();
        $fallbackModel = self::fallbackModel($primaryModel);
        $safeDna = self::safeSongDna($songDna);
        $prompt = self::buildPrompt($safeDna, $catalog);
        $schema = self::responseSchema(array_keys($allowed));
        $timeout = Config::getInt('ai.text_timeout_seconds', 45);
        $headers = ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')];

        $modelsToTry = [$primaryModel];
        if ($fallbackModel !== null) {
            $modelsToTry[] = $fallbackModel;
        }

        $lastProviderError = '';
        $usedModel = $primaryModel;
        $response = null;

        foreach ($modelsToTry as $index => $model) {
            $usedModel = $model;
            $url = self::generateContentUrl($model);
            try {
                $response = self::postGenerateContent($url, $prompt, $schema, $headers, $timeout, true);
                $lastProviderError = '';
                if ($index > 0) {
                    self::logDiagnostic('provider-model-fallback-used', [
                        'model' => $model,
                        'fallbackFrom' => $primaryModel,
                    ]);
                }
                break;
            } catch (\RuntimeException $e) {
                $lastProviderError = $e->getMessage();

                if ($lastProviderError === 'provider_http_400') {
                    try {
                        // Compatibility fallback: JSON mode without a schema.
                        $response = self::postGenerateContent($url, $prompt, null, $headers, $timeout, false);
                        $lastProviderError = '';
                        self::logDiagnostic('provider-structured-output-fallback', ['model' => $model]);
                        break;
                    } catch (\RuntimeException $schemaFallbackError) {
                        $lastProviderError = $schemaFallbackError->getMessage();
                    }
                }

                // gemini-2.5-* returns HTTP 404 for many API keys ("no longer available to new users").
                // Retry once with the proven general GEMINI_MODEL when the explore override failed.
                if ($lastProviderError === 'provider_http_404' && $index === 0 && $fallbackModel !== null) {
                    self::logDiagnostic('provider-model-unavailable', [
                        'model' => $model,
                        'fallbackModel' => $fallbackModel,
                        'httpStatus' => 404,
                    ]);
                    continue;
                }

                $diagnostic = self::safeFailureStatus($lastProviderError);
                self::rememberDiagnostic($diagnostic, $model);
                self::logDiagnostic($diagnostic, [
                    'model' => $model,
                    'httpStatus' => self::httpStatusFromProviderError($lastProviderError),
                ]);
                throw new \RuntimeException($lastProviderError);
            }
        }

        if (!is_array($response)) {
            $diagnostic = self::safeFailureStatus($lastProviderError !== '' ? $lastProviderError : 'provider_empty_response');
            self::rememberDiagnostic($diagnostic, $usedModel);
            self::logDiagnostic($diagnostic, ['model' => $usedModel]);
            throw new \RuntimeException($lastProviderError !== '' ? $lastProviderError : 'provider_empty_response');
        }

        try {
            $decoded = GeminiCreativeAdapter::decodeResponse($response);
        } catch (\RuntimeException $e) {
            $code = $e->getMessage();
            $diagnostic = $code === 'gemini_generation_blocked'
                ? 'provider-generation-blocked'
                : 'provider-invalid-json';
            self::rememberDiagnostic($diagnostic, $usedModel);
            self::logDiagnostic($diagnostic, ['model' => $usedModel]);
            throw new \RuntimeException($code === 'gemini_generation_blocked' ? $code : 'gemini_explore_incomplete');
        }

        $out = self::normalizeDirections(
            is_array($decoded['directions'] ?? null) ? $decoded['directions'] : [],
            $allowed
        );

        if (count($out) < 3) {
            self::rememberDiagnostic('provider-incomplete-output', $usedModel);
            self::logDiagnostic('provider-incomplete-output', ['model' => $usedModel, 'accepted' => count($out)]);
            throw new \RuntimeException('gemini_explore_incomplete');
        }

        self::rememberDiagnostic('ok', $usedModel);
        return [
            'model' => $usedModel,
            'directions' => array_slice($out, 0, 3),
        ];
    }

    public static function lastDiagnostic(): string
    {
        return self::$lastDiagnostic;
    }

    /** @param null|callable(string,array,array,int):array $transport */
    public static function setTransportForTests(?callable $transport): void
    {
        self::$transport = $transport;
    }

    public static function resetDiagnosticsForTests(): void
    {
        self::$lastDiagnostic = '';
    }

    /**
     * Prefer GEMINI_EXPLORE_MODEL when set; otherwise reuse the proven Song DNA GEMINI_MODEL.
     * Never silently default to a hard-coded 2.5 Flash-Lite id that many keys can no longer call.
     */
    public static function resolveModel(): string
    {
        $override = trim((string) Config::get('ai.gemini_explore_model', ''));
        if ($override !== '') {
            if (!preg_match('/^[A-Za-z0-9._-]+$/', $override)) {
                throw new \RuntimeException('gemini_model_invalid');
            }
            return $override;
        }

        $model = trim((string) Config::get('ai.gemini_model', 'gemini-3.6-flash'));
        if ($model === '' || !preg_match('/^[A-Za-z0-9._-]+$/', $model)) {
            throw new \RuntimeException('gemini_model_invalid');
        }
        return $model;
    }

    public static function fallbackModel(string $primaryModel): ?string
    {
        $general = trim((string) Config::get('ai.gemini_model', 'gemini-3.6-flash'));
        if ($general === '' || !preg_match('/^[A-Za-z0-9._-]+$/', $general)) {
            return null;
        }
        if ($general === $primaryModel) {
            return null;
        }
        return $general;
    }

    public static function generateContentUrl(string $model): string
    {
        return 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent';
    }

    /** Distinct config-gate reasons for owner/dev diagnostics. */
    public static function availabilityFailure(): ?string
    {
        if (!Config::getBool('gates.ai_providers_enabled')) {
            return 'config-ai-providers-disabled';
        }
        if (!Config::getBool('ai.gemini_live_calls')) {
            return 'config-gemini-live-calls-disabled';
        }
        if ((string) Config::get('ai.gemini_api_key') === '') {
            return 'config-gemini-api-key-missing';
        }
        return null;
    }

    public static function safeFailureStatus(string $providerError): string
    {
        return match ($providerError) {
            'provider_http_400' => 'provider-malformed-request',
            'provider_http_401', 'provider_http_403' => 'provider-auth-or-permission-failed',
            'provider_http_404' => 'provider-model-unavailable',
            'provider_http_429' => 'provider-rate-limited',
            'provider_timeout' => 'provider-timeout',
            'provider_network_error', 'provider_empty_response' => 'provider-network-failed',
            'provider_http_500', 'provider_http_502', 'provider_http_503', 'provider_http_504' => 'provider-temporarily-unavailable',
            'gemini_generation_blocked' => 'provider-generation-blocked',
            'gemini_explore_incomplete', 'gemini_invalid_creative_json' => 'provider-incomplete-output',
            'gemini_model_invalid' => 'config-gemini-model-invalid',
            'styles_unavailable' => 'styles-unavailable',
            'gemini_unavailable' => self::$lastDiagnostic !== '' ? self::$lastDiagnostic : 'config-gemini-unavailable',
            default => 'explore-failed',
        };
    }

    /**
     * Safe readiness snapshot for owner/dev smoke checks. Never includes keys or prompts.
     *
     * @return array<string,mixed>
     */
    public static function readiness(): array
    {
        $gate = self::availabilityFailure();
        $primary = null;
        $fallback = null;
        $modelError = null;
        try {
            $primary = self::resolveModel();
            $fallback = self::fallbackModel($primary);
        } catch (\Throwable $e) {
            $modelError = self::safeFailureStatus($e->getMessage());
        }

        return [
            'ready' => $gate === null && $modelError === null,
            'gate' => $gate,
            'model' => $primary,
            'fallbackModel' => $fallback,
            'modelError' => $modelError,
            'usesGeneralGeminiModel' => $primary !== null
                && $primary === (string) Config::get('ai.gemini_model', 'gemini-3.6-flash'),
            'endpoint' => $primary !== null ? self::generateContentUrl($primary) : null,
        ];
    }

    /**
     * @param array<string,mixed> $safeDna
     * @param array<int,array<string,mixed>> $catalog
     */
    public static function buildPrompt(array $safeDna, array $catalog): string
    {
        return implode("\n", [
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
            'Return only the requested JSON object.',
        ]);
    }

    /** @param list<string> $styleIds @return array<string,mixed> */
    public static function responseSchema(array $styleIds): array
    {
        return [
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
                            'styleId' => ['type' => 'string', 'enum' => array_values($styleIds)],
                            'promptHint' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string,mixed>|null $schema
     * @return array<string,mixed>
     */
    public static function requestPayload(string $prompt, ?array $schema): array
    {
        $generationConfig = [
            'temperature' => 0.85,
            'maxOutputTokens' => 1200,
            'responseMimeType' => 'application/json',
        ];
        if ($schema !== null) {
            // generateContent structured output: responseMimeType + responseJsonSchema.
            $generationConfig['responseJsonSchema'] = $schema;
        }

        return [
            'contents' => [[
                'role' => 'user',
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => $generationConfig,
        ];
    }

    /**
     * @param array<int,mixed> $directions
     * @param array<string,array<string,mixed>> $allowed
     * @return list<array<string,string>>
     */
    public static function normalizeDirections(array $directions, array $allowed): array
    {
        $out = [];
        foreach ($directions as $direction) {
            if (!is_array($direction)) {
                continue;
            }
            $styleId = (string) ($direction['styleId'] ?? '');
            if (!isset($allowed[$styleId])) {
                continue;
            }
            $name = self::line((string) ($direction['name'] ?? ''), 80);
            $description = self::text((string) ($direction['description'] ?? ''), 320);
            $promptHint = self::text((string) ($direction['promptHint'] ?? ''), 420);
            if ($name === '' || $description === '' || $promptHint === '') {
                continue;
            }
            $out[] = [
                'name' => $name,
                'description' => $description,
                'promptHint' => $promptHint,
                'styleId' => $styleId,
                'styleName' => (string) ($allowed[$styleId]['name'] ?? ''),
            ];
        }
        return $out;
    }

    /** @param array<string,mixed> $dna @return array<string,mixed> */
    public static function safeSongDna(array $dna): array
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

    /** @param array<string,mixed> $meta */
    private static function postGenerateContent(
        string $url,
        string $prompt,
        ?array $schema,
        array $headers,
        int $timeout,
        bool $withSchema
    ): array {
        $payload = self::requestPayload($prompt, $withSchema ? $schema : null);
        if (self::$transport !== null) {
            return (self::$transport)($url, $payload, $headers, $timeout);
        }
        return ProviderHttp::postJson($url, $payload, $headers, $timeout);
    }

    private static function rememberDiagnostic(string $status, ?string $model = null): void
    {
        self::$lastDiagnostic = $status;
        // Model is recorded only in the sanitized log, never in customer API codes.
        unset($model);
    }

    /** @param array<string,mixed> $meta */
    private static function logDiagnostic(string $status, array $meta = []): void
    {
        $safe = [
            'at' => gmdate('c'),
            'component' => 'gemini-explore',
            'status' => $status,
        ];
        foreach (['model', 'fallbackModel', 'fallbackFrom', 'httpStatus', 'accepted'] as $key) {
            if (array_key_exists($key, $meta) && $meta[$key] !== null && $meta[$key] !== '') {
                $safe[$key] = $meta[$key];
            }
        }
        // Never log API keys, prompts, Song DNA, style catalogs, or provider bodies.
        $path = (string) Config::get('paths.log') . '/ai-providers.log';
        @file_put_contents($path, json_encode($safe, JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND | LOCK_EX);
    }

    private static function httpStatusFromProviderError(string $error): ?int
    {
        if (preg_match('/^provider_http_(\d{3})$/', $error, $matches) === 1) {
            return (int) $matches[1];
        }
        return null;
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
