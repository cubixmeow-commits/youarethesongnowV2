<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Portraits\PortraitService;
use Yatsn\Storage\LocalStorage;
use Yatsn\Support\Config;

/**
 * Low-cost Replicate image adapter.
 * P-Image-Edit accepts private portrait references; FLUX Schnell remains text-only testing.
 */
final class ReplicateImageAdapter implements ImageAdapterInterface
{
    public function name(): string
    {
        return Config::get('ai.replicate_image_model') === 'prunaai/p-image-edit'
            ? 'replicate-p-image-edit'
            : 'replicate-flux-schnell-test-image';
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
        $model = (string) Config::get('ai.replicate_image_model', 'prunaai/p-image-edit');
        if (!preg_match('#^[A-Za-z0-9._-]+/[A-Za-z0-9._-]+$#', $model)) {
            throw new \RuntimeException('replicate_model_invalid');
        }
        [$owner, $name] = explode('/', $model, 2);
        $aspect = match ((string) ($snapshot['orientation'] ?? 'square')) {
            'portrait' => '3:4',
            'landscape' => '4:3',
            default => '1:1',
        };
        $prompt = (string) ($package['compiledPromptSafe'] ?? '');
        $input = match ($model) {
            'prunaai/p-image-edit' => $this->portraitEditInput($prompt, $snapshot, $aspect),
            'black-forest-labs/flux-schnell' => [
                'prompt' => implode("\n", [
                    $prompt,
                    'INTERNAL COMPOSITION TEST: No identity reference images are supplied to this model.',
                    'Use anonymous, entirely fictional protagonists. Do not imply that their faces represent the uploaded people.',
                    'Judge this render only for prompt interpretation, visual style, composition, lighting, and orientation.',
                ]),
                'go_fast' => true,
                'num_outputs' => 1,
                'num_inference_steps' => 4,
                'aspect_ratio' => $aspect,
                'output_format' => 'jpg',
                'output_quality' => 85,
                'disable_safety_checker' => false,
                'megapixels' => '1',
            ],
            default => throw new \RuntimeException('replicate_model_not_supported'),
        };

        $prediction = ProviderHttp::postJson(
            'https://api.replicate.com/v1/models/' . $owner . '/' . $name . '/predictions',
            ['input' => $input],
            [
                'Authorization: Bearer ' . (string) Config::get('ai.replicate_api_token'),
                'Prefer: wait=60',
                'Cancel-After: 75s',
            ],
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
        if (self::outputUrl($prediction) === '') {
            throw new \RuntimeException('replicate_prediction_incomplete');
        }
        $url = self::outputUrl($prediction);
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (!($host === 'replicate.delivery' || str_ends_with($host, '.replicate.delivery'))) {
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

    /** @return array<string, mixed> */
    private function portraitEditInput(string $prompt, array $snapshot, string $aspect): array
    {
        $userId = (int) ($snapshot['userId'] ?? 0);
        if ($userId <= 0) {
            throw new \RuntimeException('replicate_missing_user_context');
        }
        $portraitIds = array_slice(is_array($snapshot['portraitIds'] ?? null) ? $snapshot['portraitIds'] : [], 0, 2);
        if ($portraitIds === []) {
            throw new \RuntimeException('replicate_portrait_required');
        }
        $images = [];
        foreach ($portraitIds as $portraitId) {
            $row = PortraitService::findOwned($userId, (string) $portraitId);
            if ($row === null) {
                throw new \RuntimeException('replicate_portrait_not_found');
            }
            $images[] = self::privatePortraitDataUri(LocalStorage::get((string) $row['storage_key']));
        }
        $identity = count($images) === 1
            ? 'Image 1 is the sole protagonist identity reference. Preserve that person’s recognizable facial identity while creating the entirely new scene.'
            : 'Image 1 and image 2 are two different protagonist identity references. Include both people, preserve each recognizable face separately, and never merge or swap their identities.';
        return [
            'images' => $images,
            'prompt' => implode("\n", [$identity, $prompt]),
            // Pruna recommends disabling turbo for complicated multi-image edits.
            'turbo' => false,
            'aspect_ratio' => $aspect,
            'disable_safety_checker' => false,
        ];
    }

    /** Produces an ephemeral data URI under Replicate's recommended small-file ceiling. */
    public static function privatePortraitDataUri(string $bytes): string
    {
        $src = @imagecreatefromstring($bytes);
        if (!$src instanceof \GdImage) {
            throw new \RuntimeException('replicate_portrait_invalid');
        }
        $width = imagesx($src);
        $height = imagesy($src);
        $scale = min(1.0, 768 / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));
        $dst = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $white);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $jpeg = '';
        // The 256 KB ceiling applies to the complete Base64 data URL, not only
        // to the JPEG bytes. Keep enough headroom for Base64 expansion.
        $maxDataUriBytes = 245760;
        foreach ([84, 76, 68, 60, 52, 44] as $quality) {
            ob_start();
            imagejpeg($dst, null, $quality);
            $jpeg = (string) ob_get_clean();
            if ($jpeg !== '') {
                $dataUri = 'data:image/jpeg;base64,' . base64_encode($jpeg);
                if (strlen($dataUri) <= $maxDataUriBytes) {
                    return $dataUri;
                }
            }
        }

        // Large or noisy portraits may need smaller dimensions as well as lower
        // JPEG quality to stay within Replicate's inline-file limit.
        foreach ([640, 512] as $maxDimension) {
            $scale = min(1.0, $maxDimension / max($width, $height));
            $retryWidth = max(1, (int) round($width * $scale));
            $retryHeight = max(1, (int) round($height * $scale));
            $retry = imagecreatetruecolor($retryWidth, $retryHeight);
            $retryWhite = imagecolorallocate($retry, 255, 255, 255);
            imagefilledrectangle($retry, 0, 0, $retryWidth, $retryHeight, $retryWhite);
            imagecopyresampled($retry, $src, 0, 0, 0, 0, $retryWidth, $retryHeight, $width, $height);
            ob_start();
            imagejpeg($retry, null, 60);
            $jpeg = (string) ob_get_clean();
            imagedestroy($retry);
            $dataUri = $jpeg === '' ? '' : 'data:image/jpeg;base64,' . base64_encode($jpeg);
            if ($dataUri !== '' && strlen($dataUri) <= $maxDataUriBytes) {
                return $dataUri;
            }
        }

        if ($jpeg === '') {
            throw new \RuntimeException('replicate_portrait_invalid');
        }
        throw new \RuntimeException('replicate_portrait_too_large');
    }
}
