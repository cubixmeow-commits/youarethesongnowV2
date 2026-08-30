<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

/**
 * Development-only V1-style Song DNA analysis with Google Search grounding.
 * Gemini analyzes lyrics internally and returns abstractions, not lyric text.
 */
final class GeminiLyricsResearchService
{
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

        $model = (string) Config::get('ai.gemini_model', 'gemini-2.5-flash-lite');
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $model)) {
            return self::emptyResult($artist, $title, 'model-invalid');
        }

        $schema = json_encode(CreativePackageBuilder::schema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $prompt = implode("\n", [
            'You are an expert musicologist, narrative analyst, and visual director translating music into original cinematic imagery.',
            'PRIVATE DEVELOPMENT SEARCH-AND-ANALYSIS TASK:',
            'Band/Artist: ' . self::singleLine($artist, 180),
            'Song: ' . self::singleLine($title, 180),
            'Use Google Search to locate and verify the actual lyrics for this exact artist and song before analyzing it.',
            'Use the lyrics only inside this transient request. Base the Song DNA on their complete emotional arc, narrative, themes, mood, symbols, relationships, setting cues, and visual metaphors.',
            'Do not reproduce, quote, closely paraphrase, or return the lyrics. Do not return the song title, artist name, album material, or music-video imagery inside the analysis object.',
            'Invent one original visual moment rather than illustrating a distinctive lyric sequence.',
            'Return only valid JSON with these keys:',
            'matchedArtist (string), matchedTitle (string), matchConfidence (number 0 to 1), lyricsLocated (boolean), analysis (object).',
            'The analysis object must follow this JSON schema: ' . $schema,
            'Set lyricsLocated true only if the search results let you analyze the lyrics for this exact recording. Otherwise set it false.',
        ]);

        try {
            $response = ProviderHttp::postJson(
                'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent',
                self::groundedRequestPayload($prompt),
                ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')],
                Config::getInt('ai.text_timeout_seconds', 45)
            );
        } catch (\Throwable $e) {
            $status = self::safeFailureStatus($e->getMessage());
            return self::emptyResult($artist, $title, $status);
        }

        $decoded = self::decodeJsonText($response);
        $analysis = is_array($decoded['analysis'] ?? null) ? $decoded['analysis'] : [];
        $grounding = self::groundingSummary($response);
        $lyricsLocated = !empty($decoded['lyricsLocated']);
        $hasAnalysis = self::hasUsableAnalysis($analysis);
        $analyzed = $grounding['grounded'] && $hasAnalysis;
        $analysisBasis = $analyzed ? ($lyricsLocated ? 'lyrics' : 'song-context') : null;
        return [
            'enabled' => true,
            'analyzed' => $analyzed,
            'lyricsLocated' => $lyricsLocated,
            'analysisBasis' => $analysisBasis,
            'matchedArtist' => self::singleLine((string) ($decoded['matchedArtist'] ?? $artist), 180),
            'matchedTitle' => self::singleLine((string) ($decoded['matchedTitle'] ?? $title), 180),
            'matchConfidence' => max(0.0, min(1.0, (float) ($decoded['matchConfidence'] ?? 0))),
            'verificationExcerpt' => self::shortExcerpt((string) ($decoded['verificationExcerpt'] ?? '')),
            'analysis' => $analyzed ? $analysis : null,
            'preview' => $analyzed ? self::preview($analysis) : null,
            'grounded' => $grounding['grounded'],
            'searchQueries' => $grounding['queries'],
            'sources' => $grounding['sources'],
            'status' => self::analysisStatus($decoded !== [], $grounding['grounded'], $lyricsLocated, $hasAnalysis),
        ];
    }

    /**
     * Google Search grounding and JSON response MIME mode cannot be combined on
     * the generateContent endpoint. The prompt still requests JSON, and the
     * tolerant decoder below handles plain and fenced JSON responses.
     *
     * @return array<string, mixed>
     */
    public static function groundedRequestPayload(string $prompt): array
    {
        return [
            'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
            'tools' => [['google_search' => (object) []]],
            'generationConfig' => [
                'temperature' => 0.65,
                'topP' => 0.95,
                'maxOutputTokens' => 4200,
            ],
        ];
    }

    public static function safeFailureStatus(string $providerError): string
    {
        return match ($providerError) {
            'provider_http_400' => 'grounding-request-rejected',
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
        if (!$grounded) {
            return 'search-not-grounded';
        }
        if (!$hasAnalysis) {
            return 'grounded-analysis-incomplete';
        }
        return $lyricsLocated ? 'grounded-lyric-song-dna-ready' : 'grounded-context-song-dna-ready';
    }

    /** @param array<string, mixed> $response @return array<string, mixed> */
    public static function decodeJsonText(array $response): array
    {
        $parts = $response['candidates'][0]['content']['parts'] ?? [];
        $text = '';
        if (is_array($parts)) {
            foreach ($parts as $part) {
                if (is_array($part) && isset($part['text'])) {
                    $text .= (string) $part['text'];
                }
            }
        }
        $text = trim($text);
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

    /** @param array<string, mixed> $response @return array{grounded:bool,queries:array<int,string>,sources:array<int,array<string,string>>} */
    private static function groundingSummary(array $response): array
    {
        $metadata = $response['candidates'][0]['groundingMetadata'] ?? [];
        $queries = [];
        foreach (is_array($metadata['webSearchQueries'] ?? null) ? $metadata['webSearchQueries'] : [] as $query) {
            if (is_scalar($query)) {
                $queries[] = self::singleLine((string) $query, 240);
            }
        }
        $sources = [];
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
            'sources' => $sources,
        ];
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

    /** @param array<string, mixed> $analysis */
    private static function hasUsableAnalysis(array $analysis): bool
    {
        return trim((string) ($analysis['essence'] ?? '')) !== ''
            && trim((string) ($analysis['originalVisualMoment'] ?? '')) !== ''
            && is_array($analysis['themes'] ?? null)
            && ($analysis['themes'] ?? []) !== []
            && is_array($analysis['mood'] ?? null)
            && ($analysis['mood'] ?? []) !== [];
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
        ];
    }

    private static function shortExcerpt(string $value): string
    {
        $value = self::singleLine($value, 90);
        $words = preg_split('/\s+/', $value) ?: [];
        return implode(' ', array_slice($words, 0, 12));
    }

    private static function singleLine(string $value, int $max): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        return substr(trim($value), 0, $max);
    }
}
