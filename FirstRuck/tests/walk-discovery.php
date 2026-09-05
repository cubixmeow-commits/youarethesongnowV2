<?php

declare(strict_types=1);

require __DIR__ . '/../src/Coaching/WalkDiscovery.php';

use FirstRuck\Coaching\WalkDiscovery;

function discovered(bool $condition, string $label): void
{
    if (!$condition) throw new RuntimeException($label);
    echo "PASS $label\n";
}

$captured = [];
$transport = function (string $url, array $payload, array $headers) use (&$captured): array {
    $captured = compact('url', 'payload', 'headers');
    return [
        'steps' => [[
            'type' => 'model_output',
            'content' => [[
                'text' => json_encode(['walks' => [[
                    'name' => 'Lake Los Carneros',
                    'locality' => 'Goleta, California',
                    'kind' => 'park-loop',
                ]]], JSON_THROW_ON_ERROR),
                'annotations' => [[
                    'title' => 'City of Goleta — Lake Los Carneros',
                    'url' => 'https://www.cityofgoleta.org/example',
                ]],
            ]],
        ]],
    ];
};
$service = new WalkDiscovery(['enabled' => true, 'geminiKey' => 'fixture', 'geminiModel' => 'gemini-test'], $transport);
$result = $service->discover('Goleta, CA 93117, United States', ['minutes' => 15, 'shape' => 'short-loop']);
discovered($result['mode'] === 'gemini-search', 'grounded discovery is accepted');
discovered($result['walks'][0]['name'] === 'Lake Los Carneros', 'named walk is normalized');
discovered(count($result['sources']) === 1, 'grounding source is retained');
discovered($captured['payload']['tools'] === [['type' => 'google_search']], 'Google Search grounding is required');
discovered($captured['payload']['store'] === false, 'Gemini interaction storage is disabled');
discovered(!str_contains($captured['payload']['input'], '34.428') && !str_contains($captured['payload']['input'], '-119.86'), 'exact GPS is absent from the prompt');

$ungrounded = new WalkDiscovery(['enabled' => true, 'geminiKey' => 'fixture', 'geminiModel' => 'gemini-test'],
    fn (): array => ['output_text' => json_encode(['walks' => [['name' => 'Invented Walk', 'locality' => 'Nowhere', 'kind' => 'walking-area']]])]);
discovered($ungrounded->discover('Goleta, CA', [])['walks'] === [], 'ungrounded suggestions are rejected');

