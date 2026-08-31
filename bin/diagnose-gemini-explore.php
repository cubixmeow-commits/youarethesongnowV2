#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Safe Gemini Explore readiness / optional smoke check for private development hosts.
 *
 * Never prints API keys, prompts, Song DNA, portraits, or provider response bodies.
 *
 * Usage:
 *   php bin/diagnose-gemini-explore.php
 *   php bin/diagnose-gemini-explore.php --smoke
 */

$root = dirname(__DIR__);
require $root . '/app/bootstrap.php';

use Yatsn\AI\GeminiExploreService;
use Yatsn\AI\ProviderHttp;
use Yatsn\Support\Config;

$smoke = in_array('--smoke', $argv, true);
$readiness = GeminiExploreService::readiness();

echo "Gemini Explore readiness\n";
echo '  ready: ' . ($readiness['ready'] ? 'yes' : 'no') . "\n";
echo '  gate: ' . ($readiness['gate'] ?? 'ok') . "\n";
echo '  model: ' . ($readiness['model'] ?? 'n/a') . "\n";
echo '  fallbackModel: ' . ($readiness['fallbackModel'] ?? 'none') . "\n";
echo '  usesGeneralGeminiModel: ' . (!empty($readiness['usesGeneralGeminiModel']) ? 'yes' : 'no') . "\n";
echo '  endpoint: ' . ($readiness['endpoint'] ?? 'n/a') . "\n";
if (!empty($readiness['modelError'])) {
    echo '  modelError: ' . $readiness['modelError'] . "\n";
}

if (!$smoke) {
    echo "\nPass --smoke to make one minimal generateContent call with the configured key.\n";
    exit($readiness['ready'] ? 0 : 2);
}

if (!$readiness['ready']) {
    fwrite(STDERR, "Smoke skipped: Explore is not ready (see gate/modelError above).\n");
    exit(2);
}

$model = (string) $readiness['model'];
$url = GeminiExploreService::generateContentUrl($model);
$payload = [
    'contents' => [[
        'role' => 'user',
        'parts' => [['text' => 'Reply with JSON only: {"ok":true}']],
    ]],
    'generationConfig' => [
        'temperature' => 0,
        'maxOutputTokens' => 32,
        'responseMimeType' => 'application/json',
    ],
];

try {
    $response = ProviderHttp::postJson(
        $url,
        $payload,
        ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')],
        Config::getInt('ai.text_timeout_seconds', 45)
    );
    $finish = (string) ($response['candidates'][0]['finishReason'] ?? '');
    $hasText = isset($response['candidates'][0]['content']['parts'][0]['text']);
    echo "\nSmoke result\n";
    echo "  status: ok\n";
    echo '  model: ' . $model . "\n";
    echo '  finishReason: ' . ($finish !== '' ? $finish : 'n/a') . "\n";
    echo '  hasTextPart: ' . ($hasText ? 'yes' : 'no') . "\n";
    exit(0);
} catch (Throwable $e) {
    $code = $e->getMessage();
    echo "\nSmoke result\n";
    echo "  status: failed\n";
    echo '  model: ' . $model . "\n";
    echo '  diagnostic: ' . GeminiExploreService::safeFailureStatus($code) . "\n";
    echo '  providerCode: ' . $code . "\n";
    exit(1);
}
