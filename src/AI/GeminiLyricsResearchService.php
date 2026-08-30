<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

/**
 * Development-only Song DNA analysis via the Gemini Interactions API.
 * Combines built-in Google Search with strict JSON Schema structured output.
 * store=false is mandatory so lyrics/search material are never retained by Gemini interaction state.
 */
final class GeminiLyricsResearchService
{
    private const INTERACTIONS_URL = 'https://generativelanguage.googleapis.com/v1beta/interactions';

    /** @return array<string, mixed> */
    public static function analyze(string $artist, string $title): array
    {
        $empty = self::emptyResult($artist, $title, 'disabled');
        if (!Config::getBool('development.gemini_lyrics_search')) {
            return $empty;
        }
        if (!Config::getBool('gates.ai_providers_enabled')
            || !Config::getBool('ai.gemini_live_calls')
            || (string) Config::get('ai.gemini_api_key') === '') {
            return self::emptyResult($artist, $title, 'gemini-unavailable');
        }

        $model = (string) Config::get('ai.gemini_model', 'gemini-3.6-flash');
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $model)) {
            return self::emptyResult($artist, $title, 'model-invalid');
        }

        $prompt = implode("\n", [
            'You are an expert musicologist, narrative analyst, and visual director translating music into original cinematic imagery.',
            'PRIVATE DEVELOPMENT SEARCH-AND-ANALYSIS TASK:',
            'Band/Artist: ' . self::singleLine($artist, 180),
            'Song: ' . self::singleLine($title, 180),
            'Use Google Search to locate and verify the exact artist and song before analyzing it.',
            'Analyze the complete emotional arc, narrative, themes, mood, symbols, relationships, setting cues, and visual metaphors.',
            'Use any located lyrics only inside this transient request. Never reproduce, quote, closely paraphrase, or return lyrics.',
            'Do not return the song title, artist name, album material, or music-video imagery inside the analysis object.',
            'Invent one original visual moment rather than illustrating a distinctive lyric sequence.',
            'Set lyricsLocated true only when the searched material supports analysis of the lyrics for this exact recording. Otherwise set it false.',
            'Return only the structured JSON object required by the response schema.',
        ]);

        $payload = self::interactionsRequestPayload($prompt, $model);
        $response = null;
        $lastError = '';
        $attempts = 0;
        $maxAttempts = 2; // initial call + at most one transient retry
        while ($attempts < $maxAttempts) {
            $attempts++;
            try {
                $response = ProviderHttp::postJson(
                    self::INTERACTIONS_URL,
                    $payload,
                    ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')],
                    Config::getInt('ai.text_timeout_seconds', 45)
                );
                $lastError = '';
                break;
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                if ($attempts < $maxAttempts && self::isTransientFailure($lastError)) {
                    continue;
                }
                return self::emptyResult($artist, $title, self::safeFailureStatus($lastError));
            }
        }
        if (!is_array($response)) {
            return self::emptyResult($artist, $title, self::safeFailureStatus($lastError !== '' ? $lastError : 'search-failed'));
        }

        $decoded = self::decodeStructuredOutput($response);
        $analysis = is_array($decoded['analysis'] ?? null) ? $decoded['analysis'] : [];
        $search = self::searchSummary($response);
        $lyricsLocated = !empty($decoded['lyricsLocated']);
        $hasAnalysis = self::hasUsableAnalysis($analysis);
        // Structured output guarantees form, not truth. Incomplete semantic content
        // cannot pretend to be lyric-grounded or usable Song DNA.
        $analyzed = $hasAnalysis;
        if ($lyricsLocated && !$hasAnalysis) {
            $lyricsLocated = false;
        }
        $analysisBasis = $analyzed
            ? ($lyricsLocated ? 'lyrics' : ($search['grounded'] ? 'song-context' : 'v1-model-analysis'))
            : null;

        return [
            'enabled' => true,
            'analyzed' => $analyzed,
            'lyricsLocated' => $lyricsLocated,
            'analysisBasis' => $analysisBasis,
            'matchedArtist' => self::singleLine((string) ($decoded['matchedArtist'] ?? $artist), 180),
            'matchedTitle' => self::singleLine((string) ($decoded['matchedTitle'] ?? $title), 180),
            'matchConfidence' => max(0.0, min(1.0, (float) ($decoded['matchConfidence'] ?? 0))),
            'verificationExcerpt' => '',
            'analysis' => $analyzed ? $analysis : null,
            'preview' => $analyzed ? self::preview($analysis) : null,
            'grounded' => $search['grounded'],
            'searchQueries' => $search['queries'],
            'sources' => $search['sources'],
            'status' => self::analysisStatus($decoded !== [], $search['grounded'], $lyricsLocated, $hasAnalysis),
            'providerAttempts' => $attempts,
        ];
    }

    /**
     * Current Gemini Interactions API request: Google Search + JSON Schema + store=false.
     *
     * @return array<string, mixed>
     */
    public static function interactionsRequestPayload(string $prompt, ?string $model = null): array
    {
        return [
            'model' => $model ?? (string) Config::get('ai.gemini_model', 'gemini-3.6-flash'),
            'input' => $prompt,
            'tools' => [['type' => 'google_search']],
            'response_format' => [
                'type' => 'text',
                'mime_type' => 'application/json',
                'schema' => self::responseEnvelopeSchema(),
            ],
            'store' => false,
        ];
    }

    /** @return array<string, mixed> */
    public static function responseEnvelopeSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => [
                'matchedArtist',
                'matchedTitle',
                'matchConfidence',
                'lyricsLocated',
                'analysis',
            ],
            'properties' => [
                'matchedArtist' => ['type' => 'string'],
                'matchedTitle' => ['type' => 'string'],
                'matchConfidence' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                'lyricsLocated' => ['type' => 'boolean'],
                'analysis' => CreativePackageBuilder::schema(),
            ],
        ];
    }

    public static function interactionsEndpoint(): string
    {
        return self::INTERACTIONS_URL;
    }

    public static function isTransientFailure(string $providerError): bool
    {
        return in_array($providerError, [
            'provider_timeout',
            'provider_http_429',
            'provider_http_500',
            'provider_http_502',
            'provider_http_503',
            'provider_http_504',
            'provider_network_error',
        ], true);
    }

    public static function safeFailureStatus(string $providerError): string
    {
        return match ($providerError) {
            'provider_http_400' => 'interactions-structured-search-rejected',
            'provider_http_401', 'provider_http_403' => 'provider-auth-or-permission-failed',
            'provider_http_404' => 'provider-model-unavailable',
            'provider_http_429' => 'provider-rate-limited',
            'provider_timeout' => 'provider-timeout',
            'provider_network_error', 'provider_empty_response' => 'provider-network-failed',
            'provider_http_500', 'provider_http_502', 'provider_http_503', 'provider_http_504' => 'provider-temporarily-unavailable',
            default => 'search-failed',
        };
    }

    public static function analysisStatus(bool $decoded, bool $grounded, bool $lyricsLocated, bool $hasAnalysis): string
    {
        if (!$decoded) {
            return 'grounded-response-unparseable';
        }
        if (!$hasAnalysis) {
            return 'grounded-analysis-incomplete';
        }
        if ($lyricsLocated) {
            return 'grounded-lyric-song-dna-ready';
        }
        return $grounded ? 'grounded-context-song-dna-ready' : 'v1-model-song-dna-ready';
    }

    /**
     * Parse structured Song DNA from an Interactions API response (steps schema),
     * with legacy outputs[] support for fixture compatibility.
     *
     * @param array<string, mixed> $response
     * @return array<string, mixed>
     */
    public static function decodeStructuredOutput(array $response): array
    {
        $text = self::extractOutputText($response);
        return self::decodeJsonTextFromString($text);
    }

    /**
     * @param array<string, mixed> $response
     * @deprecated Prefer decodeStructuredOutput for Interactions responses.
     * @return array<string, mixed>
     */
    public static function decodeJsonText(array $response): array
    {
        // Legacy generateContent fixture shape still used by older tests.
        if (isset($response['candidates'][0]['content']['parts'])) {
            $parts = $response['candidates'][0]['content']['parts'];
            $text = '';
            if (is_array($parts)) {
                foreach ($parts as $part) {
                    if (is_array($part) && isset($part['text'])) {
                        $text .= (string) $part['text'];
                    }
                }
            }
            return self::decodeJsonTextFromString($text);
        }
        return self::decodeStructuredOutput($response);
    }

    /** @param array<string, mixed> $response */
    public static function extractOutputText(array $response): string
    {
        if (is_string($response['output_text'] ?? null) && trim((string) $response['output_text']) !== '') {
            return trim((string) $response['output_text']);
        }

        $chunks = [];
        $steps = is_array($response['steps'] ?? null) ? $response['steps'] : [];
        foreach ($steps as $step) {
            if (!is_array($step)) {
                continue;
            }
            $type = (string) ($step['type'] ?? '');
            if ($type !== 'model_output' && $type !== 'text') {
                continue;
            }
            $content = $step['content'] ?? null;
            if (is_string($step['text'] ?? null)) {
                $chunks[] = (string) $step['text'];
            }
            if (is_array($content)) {
                foreach ($content as $block) {
                    if (is_array($block) && isset($block['text'])) {
                        $chunks[] = (string) $block['text'];
                    } elseif (is_string($block)) {
                        $chunks[] = $block;
                    }
                }
            }
        }
        if ($chunks !== []) {
            return trim(implode("\n", $chunks));
        }

        // Legacy Interactions outputs[] shape.
        $outputs = is_array($response['outputs'] ?? null) ? $response['outputs'] : [];
        foreach (array_reverse($outputs) as $output) {
            if (!is_array($output)) {
                continue;
            }
            if (isset($output['text']) && is_string($output['text']) && trim($output['text']) !== '') {
                return trim($output['text']);
            }
            if (($output['type'] ?? '') === 'text' && isset($output['text'])) {
                return trim((string) $output['text']);
            }
        }

        return '';
    }

    /**
     * Transient verification metadata for development inspection only.
     * Never persist retrieved page bodies or raw interaction payloads.
     *
     * @param array<string, mixed> $response
     * @return array{grounded:bool,queries:array<int,string>,sources:array<int,array<string,string>>}
     */
    public static function searchSummary(array $response): array
    {
        $queries = [];
        $sources = [];
        $timeline = [];
        if (is_array($response['steps'] ?? null)) {
            $timeline = $response['steps'];
        } elseif (is_array($response['outputs'] ?? null)) {
            $timeline = $response['outputs'];
        }

        foreach ($timeline as $item) {
            if (!is_array($item)) {
                continue;
            }
            $type = (string) ($item['type'] ?? '');
            if ($type === 'google_search_call') {
                $args = is_array($item['arguments'] ?? null) ? $item['arguments'] : [];
                foreach (is_array($args['queries'] ?? null) ? $args['queries'] : [] as $query) {
                    if (is_scalar($query)) {
                        $queries[] = self::singleLine((string) $query, 240);
                    }
                }
            }
            if ($type === 'model_output' || $type === 'text') {
                $content = is_array($item['content'] ?? null) ? $item['content'] : [];
                foreach ($content as $block) {
                    if (!is_array($block)) {
                        continue;
                    }
                    foreach (is_array($block['annotations'] ?? null) ? $block['annotations'] : [] as $annotation) {
                        if (!is_array($annotation)) {
                            continue;
                        }
                        $uri = (string) ($annotation['url'] ?? '');
                        if (!str_starts_with($uri, 'https://')) {
                            continue;
                        }
                        $sources[] = [
                            'title' => self::singleLine((string) ($annotation['title'] ?? 'Search source'), 180),
                            'url' => substr($uri, 0, 1000),
                        ];
                        if (count($sources) >= 5) {
                            break 3;
                        }
                    }
                }
            }
        }

        // Legacy generateContent groundingMetadata still recognized for fixtures.
        $metadata = $response['candidates'][0]['groundingMetadata'] ?? [];
        foreach (is_array($metadata['webSearchQueries'] ?? null) ? $metadata['webSearchQueries'] : [] as $query) {
            if (is_scalar($query)) {
                $queries[] = self::singleLine((string) $query, 240);
            }
        }
        foreach (is_array($metadata['groundingChunks'] ?? null) ? $metadata['groundingChunks'] : [] as $chunk) {
            $web = is_array($chunk) && is_array($chunk['web'] ?? null) ? $chunk['web'] : null;
            if ($web === null) {
                continue;
            }
            $uri = (string) ($web['uri'] ?? '');
            if (!str_starts_with($uri, 'https://')) {
                continue;
            }
            $sources[] = [
                'title' => self::singleLine((string) ($web['title'] ?? 'Search source'), 180),
                'url' => substr($uri, 0, 1000),
            ];
            if (count($sources) >= 5) {
                break;
            }
        }

        return [
            'grounded' => $queries !== [] || $sources !== [],
            'queries' => array_values(array_unique(array_filter($queries))),
            'sources' => array_slice($sources, 0, 5),
        ];
    }

    /** @param array<string, mixed> $analysis */
    public static function hasUsableAnalysis(array $analysis): bool
    {
        return trim((string) ($analysis['essence'] ?? '')) !== ''
            && trim((string) ($analysis['originalVisualMoment'] ?? '')) !== ''
            && is_array($analysis['themes'] ?? null)
            && ($analysis['themes'] ?? []) !== []
            && is_array($analysis['mood'] ?? null)
            && ($analysis['mood'] ?? []) !== [];
    }

    /** @param array<string, mixed> $analysis @return array<string, mixed> */
    private static function preview(array $analysis): array
    {
        $list = static fn(string $key): array => array_values(array_slice(
            array_filter(array_map('strval', is_array($analysis[$key] ?? null) ? $analysis[$key] : [])), 0, 6
        ));
        return [
            'essence' => self::singleLine((string) ($analysis['essence'] ?? ''), 360),
            'themes' => $list('themes'),
            'mood' => $list('mood'),
            'narrativeArchetype' => self::singleLine((string) ($analysis['narrativeArchetype'] ?? ''), 120),
            'originalVisualMoment' => self::singleLine((string) ($analysis['originalVisualMoment'] ?? ''), 500),
        ];
    }

    /** @return array<string, mixed> */
    private static function emptyResult(string $artist, string $title, string $status): array
    {
        return [
            'enabled' => Config::getBool('development.gemini_lyrics_search'),
            'analyzed' => false,
            'lyricsLocated' => false,
            'analysisBasis' => null,
            'matchedArtist' => self::singleLine($artist, 180),
            'matchedTitle' => self::singleLine($title, 180),
            'matchConfidence' => 0.0,
            'verificationExcerpt' => '',
            'analysis' => null,
            'preview' => null,
            'grounded' => false,
            'searchQueries' => [],
            'sources' => [],
            'status' => $status,
            'providerAttempts' => 0,
        ];
    }

    /** @return array<string, mixed> */
    private static function decodeJsonTextFromString(string $text): array
    {
        $text = trim($text);
        if ($text === '') {
            return [];
        }
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text) ?? $text;
        $text = preg_replace('/\s*```$/', '', $text) ?? $text;
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end < $start) {
            return [];
        }
        $decoded = json_decode(substr($text, $start, $end - $start + 1), true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function singleLine(string $value, int $max): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        return substr(trim($value), 0, $max);
    }
}
