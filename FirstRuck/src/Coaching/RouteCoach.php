<?php

declare(strict_types=1);

namespace FirstRuck\Coaching;

/** Server-only integration slice. No public endpoint or browser credentials. */
final class RouteCoach
{
    private int $calls = 0;
    private const REASONS = [
        'surface_match' => 'Matches your preferred surface.',
        'duration_match' => 'Fits your prepared walking time.',
        'easy_return' => 'The source identifies an early return option.',
        'hill_match' => 'Fits the elevation limit in your plan.',
        'shape_match' => 'Matches your preferred route shape.',
        'pedestrian_network' => 'Calculated on the mapping provider’s pedestrian network.',
        'named_walk' => 'Web search identified a named walking area near your search.',
        'nearby_start' => 'Its mapped start is near your searched area.',
    ];

    public function __construct(private array $config = [], private ?\Closure $transport = null) {}

    /** Candidates must already pass a trusted route service's hard filters.
     * Returns source-bound reason codes, never unverified provider prose or geometry.
     */
    public function rank(array $candidates): array
    {
        $safe = [];
        foreach (array_slice($candidates, 0, 3) as $route) {
            $factsVerified = ($route['factsVerified'] ?? $route['verified'] ?? false) === true;
            $comparisonEligible = ($route['comparisonEligible'] ?? $route['eligible'] ?? false) === true;
            if (!$factsVerified || !$comparisonEligible
                || !is_string($route['id'] ?? null) || !preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $route['id'])
                || !is_string($route['source'] ?? null) || !str_starts_with($route['source'], 'https://')
                || !is_int($route['checkedAt'] ?? null) || $route['checkedAt'] > time()
                || time() - $route['checkedAt'] > 86400) {
                continue;
            }
            $reasons = array_values(array_intersect(array_keys(self::REASONS), $route['reasonCodes'] ?? []));
            if ($reasons !== []) $safe[$route['id']] = [
                'id' => $route['id'],
                'baselineScore' => max(0, min(100, (int) ($route['baselineScore'] ?? 0))),
                'reasonCodes' => $reasons,
            ];
        }
        $fallback = ['mode' => 'rules', 'routes' => array_values($safe)];
        if ($safe === [] || !($this->config['enabled'] ?? false) || $this->calls >= 2) return $fallback;
        foreach (['gemini', 'groq'] as $provider) {
            $key = $this->config[$provider . 'Key'] ?? '';
            $model = $this->config[$provider . 'Model'] ?? '';
            if ($key === '' || $model === '' || $this->calls >= 2) continue;
            ++$this->calls;
            try {
                $payload = $this->payload($provider, $model, array_values($safe));
                $url = $provider === 'gemini'
                    ? 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent'
                    : 'https://api.groq.com/openai/v1/chat/completions';
                $headers = $provider === 'gemini' ? ['x-goog-api-key: ' . $key] : ['Authorization: Bearer ' . $key];
                $response = $this->transport ? ($this->transport)($url, $payload, $headers) : self::post($url, $payload, $headers);
                $text = $provider === 'gemini' ? ($response['candidates'][0]['content']['parts'][0]['text'] ?? '')
                    : ($response['choices'][0]['message']['content'] ?? '');
                $decoded = json_decode($text, true, 16, JSON_THROW_ON_ERROR);
                $rows = $decoded['routes'] ?? [];
                if (!is_array($rows) || count($rows) !== count($safe)) continue;
                $seen = [];
                foreach ($rows as $row) {
                    $id = $row['id'] ?? '';
                    $codes = $row['reasonCodes'] ?? null;
                    if (!is_string($id) || !isset($safe[$id]) || isset($seen[$id]) || !is_array($codes) || $codes === []
                        || array_diff($codes, $safe[$id]['reasonCodes']) !== []) throw new \RuntimeException('invalid_route_ranking');
                    $seen[$id] = ['id' => $id, 'reasonCodes' => array_values(array_unique($codes))];
                }
                return ['mode' => $provider, 'routes' => array_values($seen)];
            } catch (\Throwable) {
                // Never log provider payloads, credentials, or response bodies.
            }
        }
        return $fallback;
    }

    public static function reasonText(string $code): string { return self::REASONS[$code] ?? ''; }

    private function payload(string $provider, string $model, array $routes): array
    {
        $prompt = 'Order the supplied map-derived walking route IDs using only baselineScore and supplied fit reasonCodes. '
            . 'These are candidates, not verified-safe routes. Return every ID exactly once. '
            . 'Keep only supplied reasonCodes for each ID. No other facts, claims, advice, coordinates, or text. '
            . json_encode($routes, JSON_THROW_ON_ERROR);
        $schema = ['type' => 'object', 'properties' => ['routes' => ['type' => 'array', 'items' => [
            'type' => 'object', 'properties' => ['id' => ['type' => 'string'], 'reasonCodes' => ['type' => 'array', 'items' => ['type' => 'string']]],
            'required' => ['id', 'reasonCodes'], 'additionalProperties' => false,
        ]]], 'required' => ['routes'], 'additionalProperties' => false];
        if ($provider === 'gemini') return ['contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0, 'maxOutputTokens' => 500, 'responseMimeType' => 'application/json', 'responseJsonSchema' => $schema]];
        return ['model' => $model, 'messages' => [['role' => 'user', 'content' => $prompt]], 'temperature' => 0,
            'max_completion_tokens' => 500, 'response_format' => ['type' => 'json_schema', 'json_schema' => ['name' => 'route_ranking', 'strict' => true, 'schema' => $schema]]];
    }

    private static function post(string $url, array $payload, array $headers): array
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8, CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/json'], $headers)]);
        $body = curl_exec($curl); $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE); curl_close($curl);
        if (!is_string($body) || strlen($body) > 64000 || $status < 200 || $status >= 300) throw new \RuntimeException('route_provider_unavailable');
        return json_decode($body, true, 32, JSON_THROW_ON_ERROR);
    }
}
