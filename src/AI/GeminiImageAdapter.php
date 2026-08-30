<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Portraits\PortraitService;
use Yatsn\Storage\LocalStorage;
use Yatsn\Support\Config;

/**
 * Native Gemini multimodal image adapter.
 * One generateContent call: text instructions + attached portrait inline images.
 * Does not re-run Song DNA / lyrics search; uses the locked creative package only.
 */
final class GeminiImageAdapter implements ImageAdapterInterface
{
    public function name(): string
    {
        return 'gemini-image';
    }

    public function isAvailable(): bool
    {
        return Config::getBool('gates.ai_providers_enabled')
            && Config::getBool('ai.gemini_image_live_calls')
            && (string) Config::get('ai.gemini_api_key') !== '';
    }

    public function generate(array $package, array $snapshot): array
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('gemini_image_unavailable');
        }
        $model = (string) Config::get('ai.gemini_image_model', 'gemini-3.1-flash-image');
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $model)) {
            throw new \RuntimeException('gemini_image_model_invalid');
        }

        $portraits = self::loadOwnedPortraits($snapshot);
        $payload = self::buildRequestPayload($package, $snapshot, $portraits);
        $response = ProviderHttp::postJson(
            'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent',
            $payload,
            ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')],
            Config::getInt('ai.image_timeout_seconds', 85)
        );

        $extracted = self::extractInlineImage($response);
        $normalized = FalImageAdapter::normalizeImage(
            $extracted['bytes'],
            $this->name() . ':' . $model,
            Config::getInt('ai.gemini_image_cost_cents', 7)
        );
        $aspect = self::aspectRatioForOrientation((string) ($snapshot['orientation'] ?? 'square'));
        if (!ReplicateImageAdapter::aspectMatches((int) $normalized['width'], (int) $normalized['height'], $aspect)) {
            throw new \RuntimeException('gemini_image_aspect_mismatch');
        }
        return $normalized;
    }

    /**
     * @param array<string, mixed> $package
     * @param array<string, mixed> $snapshot
     * @param list<array{mime:string,bytes:string}> $portraits
     * @return array<string, mixed>
     */
    public static function buildRequestPayload(array $package, array $snapshot, array $portraits): array
    {
        if ($portraits === []) {
            throw new \RuntimeException('gemini_image_portrait_required');
        }
        $parts = [
            ['text' => self::buildImagePrompt($package, $snapshot, count($portraits))],
        ];
        foreach ($portraits as $portrait) {
            $parts[] = self::inlineImagePart($portrait['bytes'], $portrait['mime']);
        }

        return [
            'contents' => [[
                'role' => 'user',
                'parts' => $parts,
            ]],
            'generationConfig' => [
                'responseModalities' => ['IMAGE'],
                'imageConfig' => [
                    'aspectRatio' => self::aspectRatioForOrientation((string) ($snapshot['orientation'] ?? 'square')),
                    'imageSize' => (string) Config::get('ai.gemini_image_size', '1K'),
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $package
     * @param array<string, mixed> $snapshot
     */
    public static function buildImagePrompt(array $package, array $snapshot, int $portraitCount): string
    {
        $dna = is_array($package['dna'] ?? null) ? $package['dna'] : [];
        $narrative = is_array($package['narrative'] ?? null) ? $package['narrative'] : [];
        $style = is_array($package['styleMap'] ?? null) ? $package['styleMap'] : [];
        $compiled = trim((string) ($package['compiledPromptSafe'] ?? ''));
        $special = trim((string) ($snapshot['specialInstructions'] ?? ''));
        $noText = !empty($snapshot['noTextInImage']);
        $styleName = (string) ($style['styleName'] ?? ($narrative['styleLead'] ?? ($snapshot['styleName'] ?? 'Selected style')));

        $join = static function (mixed $value, int $max = 8): string {
            if (!is_array($value)) {
                $text = trim(is_scalar($value) ? (string) $value : '');
                return $text === '' ? '' : substr($text, 0, 240);
            }
            $parts = [];
            foreach (array_slice($value, 0, $max) as $item) {
                if (is_array($item)) {
                    $item = implode(' ', array_filter(array_map('strval', $item)));
                }
                $item = trim((string) $item);
                if ($item !== '') {
                    $parts[] = $item;
                }
            }
            return implode(', ', $parts);
        };

        if ($portraitCount >= 2) {
            $portraitDirective = <<<'TXT'
CRITICAL — TWO ATTACHED PORTRAITS ARE YOUR TWO MAIN CHARACTERS.
The first attached image is CHARACTER 1. The second attached image is CHARACTER 2.
Match each person's exact facial features, bone structure, skin tone, hair, and apparent age separately.
Both people must be unmistakable principal subjects: clearly visible, properly lit, waist-up or full-body, and integral to the composition.
Create dynamic interaction between them. Never merge, swap, average, omit, silhouette, or shrink either person into the distant background.
Compose the artwork around these two people. Do not invent a scenic world and treat the portraits as optional references.
TXT;
        } else {
            $portraitDirective = <<<'TXT'
CRITICAL — THE ATTACHED PORTRAIT IS YOUR PRIMARY CHARACTER.
Match their exact facial features, bone structure, skin tone, hair, and apparent age.
They must be the unmistakable central subject of the image: clearly visible, properly lit, waist-up or three-quarter or full-body, actively engaged with the environment.
Favor a close, medium, or three-quarter character composition so the face remains identifiable at gallery size.
Do not depict a tiny figure, silhouette, distant figure, ghost, statue, or generic stand-in.
Compose the artwork around this person. Do not invent a scenic world and treat the portrait as an optional reference.
Transform clothing, pose, lighting, and artistic treatment to fit the Song DNA and selected style while preserving identity.
TXT;
        }

        $textPolicy = $noText
            ? 'NO visible text of any kind: no words, letters, signs, captions, lyrics, titles, logos, signatures, or watermarks.'
            : 'Text is optional. If present it must be newly invented and non-copyrighted. Never render lyrics, song titles, artist or band names, album text, logos, trademarks, or provider marks.';

        $essence = $join($dna['essence'] ?? '', 1);
        $moment = $join($narrative['moment'] ?? ($dna['originalVisualMoment'] ?? ''), 1);
        $themes = $join($dna['themes'] ?? [], 6);
        $mood = $join($dna['mood'] ?? [], 6);
        $symbols = $join($dna['symbols'] ?? [], 5);
        $palette = $join($dna['palette'] ?? [], 6);
        $lighting = $join($dna['lighting'] ?? [], 5);
        $camera = $join($dna['camera'] ?? [], 5);
        $composition = $join($dna['composition'] ?? [], 5);
        $texture = $join($dna['texture'] ?? [], 5);
        $environment = '';
        if (is_array($dna['environment'] ?? null)) {
            $environment = $join(array_merge(
                is_array($dna['environment']['settingTypes'] ?? null) ? $dna['environment']['settingTypes'] : [],
                is_array($dna['environment']['weather'] ?? null) ? $dna['environment']['weather'] : [],
                is_array($dna['environment']['spatialCharacter'] ?? null) ? $dna['environment']['spatialCharacter'] : [],
                isset($dna['environment']['eraAtmosphere']) ? [(string) $dna['environment']['eraAtmosphere']] : []
            ), 8);
        }

        $styleBlock = implode("\n", array_filter([
            'Selected style: ' . $styleName,
            isset($style['medium']) ? 'MEDIUM: ' . $join($style['medium'], 1) : null,
            isset($style['color']) ? 'COLOR: ' . $join($style['color'], 1) : null,
            isset($style['lighting']) ? 'LIGHTING: ' . $join($style['lighting'], 1) : null,
            isset($style['surface']) ? 'SURFACE: ' . $join($style['surface'], 1) : null,
            isset($style['composition']) ? 'COMPOSITION: ' . $join($style['composition'], 1) : null,
            isset($style['mood']) ? 'MOOD: ' . $join($style['mood'], 1) : null,
            isset($style['atmosphere']) ? 'ATMOSPHERE: ' . $join($style['atmosphere'], 1) : null,
            isset($style['avoid']) ? 'AVOID: ' . $join($style['avoid'], 1) : null,
        ]));

        $aspect = self::aspectRatioForOrientation((string) ($snapshot['orientation'] ?? 'square'));
        $aspectLine = $aspect . '. Frame the final image to a true ' . $aspect . ' canvas.';

        $prompt = <<<PROMPT
MISSION: Create one finished cinematic artwork in which the attached uploaded person or people are the recognizable central subjects of an original song-inspired world.

{$portraitDirective}

═══════════════════════════════════════════════════════════════
SONG DNA (already approved — do not re-analyze lyrics or invent a different song meaning)
═══════════════════════════════════════════════════════════════
Essence: {$essence}
Narrative moment: {$moment}
Themes: {$themes}
Mood: {$mood}
Symbols: {$symbols}
Environment: {$environment}
Palette: {$palette}
Lighting: {$lighting}
Camera: {$camera}
Composition: {$composition}
Surface and texture: {$texture}

═══════════════════════════════════════════════════════════════
CHARACTER & SCENE
═══════════════════════════════════════════════════════════════
Build a lived-in three-dimensional space with clear foreground, middle ground, and background.
The required people occupy the emotional and visual center. The environment supports them and must not hide or overwhelm them.
Use motivated lighting so every required face stays readable. Imply motion through pose, fabric, particles, weather, or environmental interaction.
AVOID: flat backdrops, passport/studio framing, empty voids, tiny distant figures, silhouettes, and optional/token people.

═══════════════════════════════════════════════════════════════
CURATED STYLE (dominant aesthetic)
═══════════════════════════════════════════════════════════════
{$styleBlock}
Let the selected style govern medium, craft, surface, color behavior, and finish. Preserve Song DNA meaning without weakening identity or style.

═══════════════════════════════════════════════════════════════
TECHNICAL AND SAFETY
═══════════════════════════════════════════════════════════════
Aspect ratio target: {$aspectLine}
{$textPolicy}
Create a completely original composition. Do not reproduce lyrics, artist names, song titles, album art, logos, trademarks, music-video frames, merchandise, or recognizable band imagery.
Do not add other identifiable real people or celebrity likenesses.
No graphic violence or explicit sexual content.

OUTPUT GOAL: One cohesive, instantly readable image. Priorities: (1) recognizable uploaded identity for every required person, (2) Song DNA emotional truth, (3) selected-style strength, (4) cinematic composition and atmosphere.
PROMPT;

        if ($special !== '') {
            $prompt .= "\n\nUSER CUSTOMIZATION — INTEGRATE WITH CARE\n"
                . "Integrate the user's directions only if they harmonize with identity, Song DNA, style, orientation, and safety. Soft guidance only; adapt conflicts or omit them.\n"
                . "User directions:\n" . substr($special, 0, 300);
        }

        // Prefer the full V1-derived compiled package when present; keep the
        // identity-critical framing above it so Gemini cannot ignore portraits.
        if ($compiled !== '') {
            $prompt .= "\n\nCANONICAL CREATIVE PACKAGE (follow; identity rules above still dominate):\n" . $compiled;
        }

        return substr($prompt, 0, 12000);
    }

    /** @return array{inlineData: array{mimeType: string, data: string}} */
    public static function inlineImagePart(string $bytes, string $mime = 'image/jpeg'): array
    {
        $prepared = self::preparePortraitBytes($bytes, $mime);
        return [
            'inlineData' => [
                'mimeType' => $prepared['mime'],
                'data' => base64_encode($prepared['bytes']),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $response
     * @return array{bytes:string,mime:string}
     */
    public static function extractInlineImage(array $response): array
    {
        $finish = (string) ($response['candidates'][0]['finishReason'] ?? '');
        if ($finish !== '' && !in_array($finish, ['STOP', 'MAX_TOKENS'], true)) {
            throw new \RuntimeException('gemini_image_generation_blocked');
        }
        $parts = $response['candidates'][0]['content']['parts'] ?? null;
        if (!is_array($parts)) {
            throw new \RuntimeException('gemini_image_missing');
        }
        foreach ($parts as $part) {
            if (!is_array($part)) {
                continue;
            }
            $inline = $part['inlineData'] ?? ($part['inline_data'] ?? null);
            if (!is_array($inline)) {
                continue;
            }
            $mime = strtolower((string) ($inline['mimeType'] ?? ($inline['mime_type'] ?? 'image/png')));
            $data = (string) ($inline['data'] ?? '');
            if ($data === '') {
                continue;
            }
            $bytes = base64_decode($data, true);
            if (!is_string($bytes) || $bytes === '') {
                throw new \RuntimeException('gemini_image_decode_failed');
            }
            if (@imagecreatefromstring($bytes) === false) {
                throw new \RuntimeException('gemini_image_invalid');
            }
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                $mime = 'image/png';
            }
            return ['bytes' => $bytes, 'mime' => $mime];
        }
        throw new \RuntimeException('gemini_image_missing');
    }

    public static function aspectRatioForOrientation(string $orientation): string
    {
        return match ($orientation) {
            'portrait' => '3:4',
            'landscape' => '4:3',
            default => '1:1',
        };
    }

    /** Count inline portrait parts in a request payload (for tests). */
    public static function countInlineImageParts(array $payload): int
    {
        $parts = $payload['contents'][0]['parts'] ?? [];
        if (!is_array($parts)) {
            return 0;
        }
        $count = 0;
        foreach ($parts as $part) {
            if (is_array($part) && isset($part['inlineData']['data']) && is_string($part['inlineData']['data']) && $part['inlineData']['data'] !== '') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @param array<string, mixed> $snapshot
     * @return list<array{mime:string,bytes:string}>
     */
    private static function loadOwnedPortraits(array $snapshot): array
    {
        $userId = (int) ($snapshot['userId'] ?? 0);
        if ($userId <= 0) {
            throw new \RuntimeException('gemini_image_missing_user_context');
        }
        $portraitIds = array_slice(is_array($snapshot['portraitIds'] ?? null) ? $snapshot['portraitIds'] : [], 0, 2);
        if ($portraitIds === []) {
            throw new \RuntimeException('gemini_image_portrait_required');
        }
        $out = [];
        foreach ($portraitIds as $portraitId) {
            $row = PortraitService::findOwned($userId, (string) $portraitId);
            if ($row === null) {
                throw new \RuntimeException('gemini_image_portrait_not_found');
            }
            $bytes = LocalStorage::get((string) $row['storage_key']);
            $mime = (string) ($row['mime_type'] ?? 'image/jpeg');
            $out[] = self::preparePortraitBytes($bytes, $mime);
        }
        return $out;
    }

    /** @return array{mime:string,bytes:string} */
    private static function preparePortraitBytes(string $bytes, string $mime): array
    {
        $src = @imagecreatefromstring($bytes);
        if (!$src instanceof \GdImage) {
            throw new \RuntimeException('gemini_image_portrait_invalid');
        }
        $width = imagesx($src);
        $height = imagesy($src);
        $scale = min(1.0, 1536 / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));
        $dst = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $white);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        ob_start();
        imagejpeg($dst, null, 88);
        $jpeg = (string) ob_get_clean();
        if ($jpeg === '') {
            throw new \RuntimeException('gemini_image_portrait_invalid');
        }
        return ['mime' => 'image/jpeg', 'bytes' => $jpeg];
    }
}
