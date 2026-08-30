<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

/**
 * Development-only Google Search grounding for lyric verification.
 * Returned lyric text is request-memory only and must never be persisted.
 */
final class GeminiLyricsResearchService
{
    /** @return array<string, mixed> */
    public static function lookup(string $artist, string $title): array
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

        $prompt = implode("\n", [
            'PRIVATE INTERNAL DEVELOPMENT TEST. Use Google Search to locate the actual lyrics for the specified recording.',
            'Artist or band: ' . self::singleLine($artist, 180),
            'Song title: ' . self::singleLine($title, 180),
            'Confirm that the lyrics belong to this artist and title. Prefer a clearly identified lyrics page or artist-authorized source.',
            'Return only one valid JSON object with these keys:',
            'matchedArtist (string), matchedTitle (string), matchConfidence (number 0 to 1), lyricsFound (boolean), lyrics (string), verificationNote (string).',
            'For this private development inspection, put the lyric text you actually analyzed in lyrics. Do not invent missing lines.',
            'If reliable lyrics cannot be found, set lyricsFound false and lyrics to an empty string.',
        ]);

        try {
            $response = ProviderHttp::postJson(
                'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent',
                [
                    'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                    'tools' => [['google_search' => (object) []]],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 8192,
                    ],
                ],
                ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')],
                Config::getInt('ai.text_timeout_seconds', 45)
            );
        } catch (\Throwable $e) {
            return self::emptyResult($artist, $title, 'search-failed');
        }

        $decoded = self::decodeJsonText($response);
        $lyrics = self::cleanLyrics((string) ($decoded['lyrics'] ?? ''));
        $found = !empty($decoded['lyricsFound']) && $lyrics !== '';
        $grounding = self::groundingSummary($response);
        return [
            'enabled' => true,
            'lyricsFound' => $found,
            'matchedArtist' => self::singleLine((string) ($decoded['matchedArtist'] ?? $artist), 180),
            'matchedTitle' => self::singleLine((string) ($decoded['matchedTitle'] ?? $title), 180),
            'matchConfidence' => max(0.0, min(1.0, (float) ($decoded['matchConfidence'] ?? 0))),
            'lyrics' => $found ? $lyrics : null,
            'verificationNote' => self::singleLine((string) ($decoded['verificationNote'] ?? ''), 300),
            'grounded' => $grounding['grounded'],
            'searchQueries' => $grounding['queries'],
            'sources' => $grounding['sources'],
            'status' => $found ? 'lyrics-found' : 'lyrics-not-found',
        ];
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

    /** @return array<string, mixed> */
    private static function emptyResult(string $artist, string $title, string $status): array
    {
        return [
            'enabled' => Config::getBool('development.gemini_lyrics_search'),
            'lyricsFound' => false,
            'matchedArtist' => self::singleLine($artist, 180),
            'matchedTitle' => self::singleLine($title, 180),
            'matchConfidence' => 0.0,
            'lyrics' => null,
            'verificationNote' => '',
            'grounded' => false,
            'searchQueries' => [],
            'sources' => [],
            'status' => $status,
        ];
    }

    private static function cleanLyrics(string $lyrics): string
    {
        $lyrics = str_replace(["\r\n", "\r"], "\n", $lyrics);
        $lyrics = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $lyrics) ?? '';
        $lyrics = preg_replace('/\n{3,}/', "\n\n", $lyrics) ?? '';
        return substr(trim($lyrics), 0, 24000);
    }

    private static function singleLine(string $value, int $max): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        return substr(trim($value), 0, $max);
    }
}
