<?php

declare(strict_types=1);

namespace FirstRuck\Coaching;

/**
 * Uses Gemini Google Search grounding to discover named public walking areas.
 * It never returns route geometry or trusted coordinates; Geoapify must resolve
 * every name and build the actual pedestrian-network candidate afterward.
 */
final class WalkDiscovery
{
    private const URL = 'https://generativelanguage.googleapis.com/v1beta/interactions';

    public function __construct(private array $config = [], private ?\Closure $transport = null) {}

    public function discover(string $area, array $preferences): array
    {
        $fallback = ['mode' => 'unavailable', 'status' => 'disabled-or-unconfigured', 'walks' => [], 'sources' => []];
        $area = self::singleLine($area, 160);
        $key = (string) ($this->config['geminiKey'] ?? '');
        $model = (string) ($this->config['geminiModel'] ?? '');
        if (!($this->config['enabled'] ?? false) || strlen($area) < 2 || $key === ''
            || !preg_match('/^[A-Za-z0-9._-]+$/', $model)) {
            return $fallback;
        }

        $minutes = max(10, min(30, (int) ($preferences['minutes'] ?? 15)));
        $shape = ($preferences['shape'] ?? '') === 'short-loop' ? 'short loop' : 'out and back';
        $surface = self::singleLine((string) ($preferences['surface'] ?? 'either'), 40);
        $hills = self::singleLine((string) ($preferences['hillComfort'] ?? 'gentle'), 40);
        $prompt = implode("\n", [
            'Find up to five established, named public places for a beginner walking outing near this general area: ' . $area,
            'Prefer official park, city, county, university, land-trust, or trail-authority sources.',
            'Good candidates include named park loops, greenways, waterfront walks, nature preserves, and documented walking trails.',
            'Requested walking time: about ' . $minutes . ' minutes. Preferred shape: ' . $shape . '.',
            'Surface preference: ' . $surface . '. Hill comfort: ' . $hills . '.',
            'Do not invent a place, route, coordinate, access status, closure status, safety claim, distance, grade, or surface fact.',
            'Names are discovery leads only; another geographic provider will resolve them and calculate routes.',
            'Return only the required JSON. Order the strongest documented nearby candidates first.',
        ]);
        $payload = [
            'model' => $model,
            'input' => $prompt,
            'tools' => [['type' => 'google_search']],
            'response_format' => ['type' => 'text', 'mime_type' => 'application/json', 'schema' => self::schema()],
            'store' => false,
        ];

        try {
            $response = $this->transport
                ? ($this->transport)(self::URL, $payload, ['x-goog-api-key: ' . $key])
                : self::post($payload, $key);
            $sources = self::sources($response);
            if ($sources === []) return [...$fallback, 'status' => 'no-grounding-sources'];
            $decoded = self::decode($response);
            $walks = [];
            foreach (array_slice(is_array($decoded['walks'] ?? null) ? $decoded['walks'] : [], 0, 5) as $index => $walk) {
                if (!is_array($walk)) continue;
                $name = self::singleLine((string) ($walk['name'] ?? ''), 120);
                $locality = self::singleLine((string) ($walk['locality'] ?? $area), 120);
                $kind = (string) ($walk['kind'] ?? 'walking-area');
                if ($name === '' || !in_array($kind, ['park-loop','greenway','waterfront','nature-trail','walking-area'], true)) continue;
                $walks[] = ['name' => $name, 'locality' => $locality, 'kind' => $kind, 'discoveryRank' => $index + 1];
            }
            return $walks === []
                ? [...$fallback, 'status' => 'no-structured-walks']
                : ['mode' => 'gemini-search', 'status' => 'grounded-walks-found', 'walks' => $walks, 'sources' => $sources];
        } catch (\Throwable) {
            return [...$fallback, 'status' => 'provider-or-response-error'];
        }
    }

    public static function schema(): array
    {
        return ['type' => 'object', 'additionalProperties' => false, 'required' => ['walks'], 'properties' => [
            'walks' => ['type' => 'array', 'maxItems' => 5, 'items' => [
                'type' => 'object', 'additionalProperties' => false, 'required' => ['name','locality','kind'], 'properties' => [
                    'name' => ['type' => 'string'],
                    'locality' => ['type' => 'string'],
                    'kind' => ['type' => 'string', 'enum' => ['park-loop','greenway','waterfront','nature-trail','walking-area']],
                ],
            ]],
        ]];
    }

    private static function decode(array $response): array
    {
        $text = is_string($response['output_text'] ?? null) ? (string) $response['output_text'] : '';
        if (trim($text) === '') {
            foreach (is_array($response['steps'] ?? null) ? $response['steps'] : [] as $step) {
                if (!is_array($step) || !in_array($step['type'] ?? '', ['model_output','text'], true)) continue;
                if (is_string($step['text'] ?? null)) $text .= (string) $step['text'];
                foreach (is_array($step['content'] ?? null) ? $step['content'] : [] as $block) {
                    if (is_array($block) && is_string($block['text'] ?? null)) $text .= (string) $block['text'];
                }
            }
        }
        $text = trim($text);
        $start = strpos($text, '{'); $end = strrpos($text, '}');
        if ($start === false || $end === false || $end < $start) return [];
        $decoded = json_decode(substr($text, $start, $end - $start + 1), true, 16, JSON_THROW_ON_ERROR);
        return is_array($decoded) ? $decoded : [];
    }

    private static function sources(array $response): array
    {
        $sources = [];
        foreach (is_array($response['steps'] ?? null) ? $response['steps'] : [] as $step) {
            if (!is_array($step)) continue;
            foreach (is_array($step['content'] ?? null) ? $step['content'] : [] as $block) {
                if (!is_array($block)) continue;
                foreach (is_array($block['annotations'] ?? null) ? $block['annotations'] : [] as $annotation) {
                    if (!is_array($annotation)) continue;
                    $url = (string) ($annotation['url'] ?? '');
                    if (!str_starts_with($url, 'https://')) continue;
                    $sources[$url] = ['title' => self::singleLine((string) ($annotation['title'] ?? 'Walking source'), 160), 'url' => substr($url, 0, 1000)];
                    if (count($sources) >= 5) break 3;
                }
            }
        }
        return array_values($sources);
    }

    private static function post(array $payload, string $key): array
    {
        $curl = curl_init(self::URL);
        curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 20, CURLOPT_HTTPHEADER => ['Content-Type: application/json','Accept: application/json','x-goog-api-key: ' . $key]]);
        $body = curl_exec($curl); $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE); curl_close($curl);
        if (!is_string($body) || strlen($body) > 128000 || $status < 200 || $status >= 300) throw new \RuntimeException('walk_discovery_unavailable');
        return json_decode($body, true, 32, JSON_THROW_ON_ERROR);
    }

    private static function singleLine(string $value, int $max): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        return mb_substr(trim($value), 0, $max);
    }
}
