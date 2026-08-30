<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Support\Config;

/**
 * Low-cost, text-only internal testing adapter.
 * FLUX Schnell has no portrait/reference-image input and must not be treated as identity-preserving output.
 */
final class ReplicateImageAdapter implements ImageAdapterInterface
{
    public function name(): string
    {
        return 'replicate-flux-schnell-test-image';
    }

    public function isAvailable(): bool
    {
        return Config::getBool('gates.ai_providers_enabled')
            && Config::getBool('ai.replicate_live_calls')
            && (string) Config::get('ai.replicate_api_token') !== '';
    }

    public function generate(array $package, array $snapshot): array
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('replicate_unavailable');
        }
        $model = (string) Config::get('ai.replicate_image_model', 'black-forest-labs/flux-schnell');
        if (!preg_match('#^[A-Za-z0-9._-]+/[A-Za-z0-9._-]+$#', $model)) {
            throw new \RuntimeException('replicate_model_invalid');
        }
        [$owner, $name] = explode('/', $model, 2);
        $orientation = (string) ($snapshot['orientation'] ?? 'square');
        $aspect = match ($orientation) {
            'portrait' => '3:4',
            'landscape' => '4:3',
            default => '1:1',
        };
        $prompt = implode("\n", [
            (string) ($package['compiledPromptSafe'] ?? ''),
            'INTERNAL COMPOSITION TEST: No identity reference images are supplied to this model.',
            'Use anonymous, entirely fictional protagonists. Do not imply that their faces represent the uploaded people.',
            'Judge this render only for prompt interpretation, visual style, composition, lighting, and orientation.',
        ]);
        $headers = [
            'Authorization: Bearer ' . (string) Config::get('ai.replicate_api_token'),
            'Prefer: wait=60',
            'Cancel-After: 75s',
        ];
        $prediction = ProviderHttp::postJson(
            'https://api.replicate.com/v1/models/' . $owner . '/' . $name . '/predictions',
            [
                'input' => [
                    'prompt' => $prompt,
                    'go_fast' => true,
                    'num_outputs' => 1,
                    'num_inference_steps' => 4,
                    'aspect_ratio' => $aspect,
                    'output_format' => 'jpg',
                    'output_quality' => 85,
                    'disable_safety_checker' => false,
                    'megapixels' => '1',
                ],
            ],
            $headers,
            Config::getInt('ai.image_timeout_seconds', 85)
        );

        if (self::outputUrl($prediction) === '' && !in_array((string) ($prediction['status'] ?? ''), ['successful', 'succeeded'], true)) {
            $getUrl = (string) ($prediction['urls']['get'] ?? '');
            for ($attempt = 0; $attempt < 3 && $getUrl !== ''; $attempt++) {
                usleep(1000000);
                $prediction = ProviderHttp::getJson(
                    $getUrl,
                    ['Authorization: Bearer ' . (string) Config::get('ai.replicate_api_token')],
                    10
                );
                if (self::outputUrl($prediction) !== '' || in_array((string) ($prediction['status'] ?? ''), ['successful', 'succeeded'], true)) {
                    break;
                }
                if (in_array((string) ($prediction['status'] ?? ''), ['failed', 'canceled'], true)) {
                    throw new \RuntimeException('replicate_prediction_failed');
                }
            }
        }
        if (self::outputUrl($prediction) === '' && !in_array((string) ($prediction['status'] ?? ''), ['successful', 'succeeded'], true)) {
            throw new \RuntimeException('replicate_prediction_incomplete');
        }
        $url = self::outputUrl($prediction);
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($url === '' || !($host === 'replicate.delivery' || str_ends_with($host, '.replicate.delivery'))) {
            throw new \RuntimeException('replicate_output_url_invalid');
        }
        $download = ProviderHttp::getBinary($url, Config::getInt('ai.image_download_timeout_seconds', 30));
        return FalImageAdapter::normalizeImage(
            $download['bytes'],
            $this->name() . ':' . $model,
            Config::getInt('ai.replicate_image_cost_cents', 1)
        );
    }

    /** @param array<string, mixed> $prediction */
    public static function outputUrl(array $prediction): string
    {
        $output = $prediction['output'] ?? null;
        return is_array($output) ? (string) ($output[0] ?? '') : (string) $output;
    }
}
