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
            'prunaai/p-image-edit' => $this->portraitEditInput($package, $snapshot, $aspect),
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
        $normalized = FalImageAdapter::normalizeImage(
            $download['bytes'],
            $this->name() . ':' . $model,
            Config::getInt('ai.replicate_image_cost_cents', 1)
        );
        if (!self::aspectMatches((int) $normalized['width'], (int) $normalized['height'], $aspect)) {
            throw new \RuntimeException('replicate_output_aspect_mismatch');
        }
        return $normalized;
    }

    /** @param array<string, mixed> $prediction */
    public static function outputUrl(array $prediction): string
    {
        $output = $prediction['output'] ?? null;
        return is_array($output) ? (string) ($output[0] ?? '') : (string) $output;
    }

    /** @return array<string, mixed> */
    private function portraitEditInput(array $package, array $snapshot, string $aspect): array
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
        return [
            'images' => $images,
            'prompt' => self::compactPortraitEditPrompt($package, $snapshot, count($images)),
            // Pruna recommends disabling turbo for complicated multi-image edits.
            'turbo' => false,
            'aspect_ratio' => $aspect,
            'disable_safety_checker' => false,
        ];
    }

    /**
     * P-Image-Edit responds best to a concise imperative edit instruction, not
     * the complete provider-neutral prompt. Keep the canonical package intact
     * while compiling only its highest-value controls for this provider.
     *
     * @param array<string, mixed> $package
     * @param array<string, mixed> $snapshot
     */
    public static function compactPortraitEditPrompt(array $package, array $snapshot, int $imageCount): string
    {
        $dna = is_array($package['dna'] ?? null) ? $package['dna'] : [];
        $narrative = is_array($package['narrative'] ?? null) ? $package['narrative'] : [];
        $style = is_array($package['styleMap'] ?? null) ? $package['styleMap'] : [];
        $line = static function (mixed $value, int $max = 260): string {
            if (is_array($value)) {
                $value = implode(', ', array_filter(array_map('strval', $value)));
            }
            $value = preg_replace('/\s+/u', ' ', trim(is_scalar($value) ? (string) $value : '')) ?? '';
            return substr($value, 0, $max);
        };
        $environment = is_array($dna['environment'] ?? null) ? $dna['environment'] : [];
        $identity = $imageCount >= 2
            ? 'Using the distinct people from image 1 and image 2, replace both source photos with one unified artwork. Preserve both recognizable faces separately and make them equal, prominent protagonists. Show both people waist-up or full-body in the foreground or near middle ground, with unobstructed, naturally lit faces large enough to recognize at gallery size. Never merge, swap, duplicate, average, omit, silhouette, or push either person into the distant background.'
            : 'Using the person from image 1, replace the entire source photo with one unified artwork. Preserve their recognizable face and make them the sole prominent protagonist. Show them waist-up or full-body in the foreground or near middle ground, front-facing or three-quarter view, with an unobstructed, naturally lit face large enough to recognize at gallery size. Do not add or substitute another main person, reduce them to a silhouette, or push them into the distant background.';
        $textRule = !empty($snapshot['noTextInImage'])
            ? 'Generate no readable text of any kind: no words, letters, signs, captions, error messages, logos, signatures, or watermarks.'
            : 'Do not generate lyrics, song or performer names, album text, logos, brands, or copyrighted phrases.';
        $quality = match ((string) ($snapshot['quality'] ?? 'medium')) {
            'high' => 'Finish as premium poster-ready artwork with excellent facial fidelity, dimensional materials, rich dynamic range, and coherent fine detail.',
            'low' => 'Prioritize identity, scene clarity, composition, and style over micro-detail.',
            default => 'Finish as refined production artwork with strong facial fidelity, dimensional lighting, coherent materials, and clean detail.',
        };

        $prompt = implode("\n", array_filter([
            'P-IMAGE-EDIT INSTRUCTION',
            $identity,
            $textRule,
            'Do not show the source photo as an inset, collage, frame, card, screen, or visible reference. Change its background, crop, pose, lighting, and clothing to belong naturally in the new world.',
            'Create this exact original dramatic moment: ' . $line($narrative['moment'] ?? ($dna['originalVisualMoment'] ?? ''), 520),
            'Re-stage that moment around the referenced person or people. Their face, emotion, pose, and action must carry the story; the environment supports them and must not visually overwhelm or hide them.',
            'Emotional meaning: ' . $line($dna['essence'] ?? '', 320),
            'Themes and mood: ' . $line(array_merge(
                is_array($dna['themes'] ?? null) ? $dna['themes'] : [],
                is_array($dna['mood'] ?? null) ? $dna['mood'] : []
            ), 360),
            'Build a dimensional foreground, middle ground, and background. Environment: ' . $line(array_merge(
                is_array($environment['settingTypes'] ?? null) ? $environment['settingTypes'] : [],
                is_array($environment['weather'] ?? null) ? $environment['weather'] : [],
                is_array($environment['spatialCharacter'] ?? null) ? $environment['spatialCharacter'] : []
            ), 360),
            'Visual direction: palette ' . $line($dna['palette'] ?? [], 220) . '; lighting ' . $line($dna['lighting'] ?? [], 220) . '; camera and composition ' . $line(array_merge(
                is_array($dna['camera'] ?? null) ? $dna['camera'] : [],
                is_array($dna['composition'] ?? null) ? $dna['composition'] : []
            ), 300) . '.',
            'Dominant selected style: ' . $line($style['styleName'] ?? ($narrative['styleLead'] ?? ''), 120) . '. ' . $line($style['medium'] ?? '', 240) . '. ' . $line($style['color'] ?? '', 200) . '. ' . $line($style['lighting'] ?? '', 200) . '. ' . $line($style['surface'] ?? '', 180) . '.',
            $quality,
            'Return exactly one finished original scene. Identity fidelity is the first acceptance requirement: every referenced face must be clearly recognizable, prominent, naturally integrated, and visibly driving the story. Reject any composition where a required person is tiny, distant, obscured, back-facing, or only a silhouette.',
        ]));

        return substr($prompt, 0, 3800);
    }

    public static function aspectMatches(int $width, int $height, string $aspect): bool
    {
        if ($width <= 0 || $height <= 0) {
            return false;
        }
        $expected = match ($aspect) {
            '3:4' => 0.75,
            '4:3' => 4 / 3,
            default => 1.0,
        };
        return abs(($width / $height) - $expected) <= 0.12;
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
