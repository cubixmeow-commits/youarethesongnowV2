<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Portraits\PortraitService;
use Yatsn\Storage\LocalStorage;
use Yatsn\Support\Config;

final class FalImageAdapter implements ImageAdapterInterface
{
    public function name(): string
    {
        return 'fal-image';
    }

    public function isAvailable(): bool
    {
        return Config::getBool('gates.ai_providers_enabled')
            && Config::getBool('ai.fal_live_calls')
            && (string) Config::get('ai.fal_key') !== '';
    }

    public function generate(array $package, array $snapshot): array
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('fal_unavailable');
        }
        $model = (string) Config::get('ai.fal_image_model', 'fal-ai/flux-pro/kontext/multi');
        if (!preg_match('#^[A-Za-z0-9._/-]+$#', $model)) {
            throw new \RuntimeException('fal_model_invalid');
        }
        $userId = (int) ($snapshot['userId'] ?? 0);
        if ($userId <= 0) {
            throw new \RuntimeException('fal_missing_user_context');
        }
        $portraitIds = array_slice(is_array($snapshot['portraitIds'] ?? null) ? $snapshot['portraitIds'] : [], 0, 2);
        if ($portraitIds === []) {
            throw new \RuntimeException('fal_portrait_required');
        }
        $imageUrls = [];
        foreach ($portraitIds as $portraitId) {
            $row = PortraitService::findOwned($userId, (string) $portraitId);
            if ($row === null) {
                throw new \RuntimeException('fal_portrait_not_found');
            }
            $bytes = LocalStorage::get((string) $row['storage_key']);
            $imageUrls[] = ReplicateImageAdapter::privatePortraitDataUri($bytes);
        }

        $orientation = (string) ($snapshot['orientation'] ?? 'square');
        $aspect = match ($orientation) {
            'portrait' => '3:4',
            'landscape' => '4:3',
            default => '1:1',
        };
        $prompt = ReplicateImageAdapter::compactPortraitEditPrompt($package, $snapshot, count($imageUrls));

        $response = ProviderHttp::postJson(
            'https://fal.run/' . $model,
            [
                'prompt' => $prompt,
                'image_urls' => $imageUrls,
                'aspect_ratio' => $aspect,
                'num_images' => 1,
                'output_format' => 'jpeg',
                'safety_tolerance' => '2',
                'guidance_scale' => 3.5,
                'enhance_prompt' => false,
                'sync_mode' => true,
            ],
            ['Authorization: Key ' . (string) Config::get('ai.fal_key')],
            Config::getInt('ai.image_timeout_seconds', 85)
        );

        foreach (($response['has_nsfw_concepts'] ?? []) as $flag) {
            if ($flag === true) {
                throw new \RuntimeException('fal_output_safety_rejected');
            }
        }
        $url = (string) ($response['images'][0]['url'] ?? '');
        if (str_starts_with($url, 'data:image/')) {
            $download = self::decodeDataUri($url);
        } else {
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));
            if ($url === '' || !($host === 'fal.media' || str_ends_with($host, '.fal.media') || $host === 'fal.ai' || str_ends_with($host, '.fal.ai'))) {
                throw new \RuntimeException('fal_output_url_invalid');
            }
            $download = ProviderHttp::getBinary($url, Config::getInt('ai.image_download_timeout_seconds', 30));
        }
        $normalized = self::normalizeImage(
            $download['bytes'],
            $this->name() . ':' . $model,
            Config::getInt('ai.fal_image_cost_cents', 4)
        );
        if (!ReplicateImageAdapter::aspectMatches((int) $normalized['width'], (int) $normalized['height'], $aspect)) {
            throw new \RuntimeException('fal_output_aspect_mismatch');
        }
        return $normalized;
    }

    /** @return array{adapter:string,mime:string,width:int,height:int,bytes:string,costCents:int} */
    public static function normalizeImage(string $bytes, string $adapter = 'fal-image', int $costCents = 4): array
    {
        $src = @imagecreatefromstring($bytes);
        if (!$src instanceof \GdImage) {
            throw new \RuntimeException('fal_output_image_invalid');
        }
        $width = imagesx($src);
        $height = imagesy($src);
        if ($width < 512 || $height < 512 || $width > 8192 || $height > 8192) {
            throw new \RuntimeException('fal_output_dimensions_invalid');
        }
        $dst = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $width, $height, $background);
        imagecopy($dst, $src, 0, 0, 0, 0, $width, $height);
        ob_start();
        imagejpeg($dst, null, 90);
        $jpeg = (string) ob_get_clean();
        if ($jpeg === '') {
            throw new \RuntimeException('fal_output_encode_failed');
        }
        return [
            'adapter' => $adapter,
            'mime' => 'image/jpeg',
            'width' => $width,
            'height' => $height,
            'bytes' => $jpeg,
            'costCents' => max(0, $costCents),
        ];
    }

    /** @return array{bytes:string,mime:string} */
    private static function decodeDataUri(string $uri): array
    {
        if (!preg_match('#^data:(image/(?:jpeg|png|webp));base64,([A-Za-z0-9+/=]+)$#', $uri, $matches)) {
            throw new \RuntimeException('fal_output_data_uri_invalid');
        }
        $bytes = base64_decode($matches[2], true);
        if (!is_string($bytes) || $bytes === '' || strlen($bytes) > 26214400) {
            throw new \RuntimeException('fal_output_data_uri_invalid');
        }
        return ['bytes' => $bytes, 'mime' => $matches[1]];
    }
}
