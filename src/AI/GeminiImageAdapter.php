<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\Portraits\PortraitService;
use Yatsn\Storage\LocalStorage;
use Yatsn\Support\Config;

/**
 * Native Gemini multimodal image adapter.
 * One generateContent call: text instructions + attached portrait inline images.
 * When structured planning succeeded, wraps the canonical compiled prompt only.
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
        $canonical = trim((string) ($package['compiledPromptSafe'] ?? ''));
        if (self::usesCanonicalCompiledPrompt($package, $canonical)) {
            return self::buildCanonicalImagePrompt($canonical, $snapshot, $portraitCount);
        }
        return self::buildLegacyImagePrompt($package, $snapshot, $portraitCount);
    }

    public static function usesCanonicalCompiledPrompt(array $package, string $canonical): bool
    {
        if ($canonical === '' || !str_contains($canonical, 'SEMANTIC SCENE PREMISE')) {
            return false;
        }
        $planning = is_array($package['visualPlanning'] ?? null) ? $package['visualPlanning'] : [];
        return ($planning['status'] ?? '') === 'success';
    }

    public static function buildCanonicalImagePrompt(string $canonical, array $snapshot, int $portraitCount): string
    {
        $identitySection = self::identitySection($portraitCount);
        $aspect = self::aspectRatioForOrientation((string) ($snapshot['orientation'] ?? 'square'));
        $aspectLine = $aspect . '. Frame the final image to a true ' . $aspect . ' canvas.';
        $noText = !empty($snapshot['noTextInImage']);
        $textPolicy = $noText
            ? 'NO visible text of any kind: no words, letters, signs, captions, lyrics, titles, logos, signatures, or watermarks.'
            : 'Text is optional. If present it must be newly invented and non-copyrighted.';

        $prompt = implode("\n", [
            'MISSION: Render one finished artwork from the canonical creative prompt below.',
            'Attached portrait images are authoritative identity references only.',
            '',
            $identitySection,
            '',
            '═══════════════════════════════════════════════════════════════',
            'CANONICAL CREATIVE PROMPT (semantic source of truth — do not contradict)',
            '═══════════════════════════════════════════════════════════════',
            $canonical,
            '',
            '═══════════════════════════════════════════════════════════════',
            'PROVIDER MODALITY REQUIREMENTS',
            '═══════════════════════════════════════════════════════════════',
            'Aspect ratio target: ' . $aspectLine,
            $textPolicy,
            'Do not add other identifiable real people or celebrity likenesses.',
            'No graphic violence or explicit sexual content.',
            'An image that omits any required uploaded person is unusable.',
        ]);

        return substr($prompt, 0, 12000);
    }

    /**
     * @param array<string, mixed> $package
     * @param array<string, mixed> $snapshot
     */
    public static function buildLegacyImagePrompt(array $package, array $snapshot, int $portraitCount): string
    {
        $dna = is_array($package['dna'] ?? null) ? $package['dna'] : [];
        $narrative = is_array($package['narrative'] ?? null) ? $package['narrative'] : [];
        $style = is_array($package['styleMap'] ?? null) ? $package['styleMap'] : [];
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

        $identitySection = self::identitySection($portraitCount);
        $textPolicy = $noText
            ? 'NO visible text of any kind: no words, letters, signs, captions, lyrics, titles, logos, signatures, or watermarks.'
            : 'Text is optional. If present it must be newly invented and non-copyrighted. Never render lyrics, song titles, artist or band names, album text, logos, trademarks, or provider marks.';

        $essence = $join($dna['essence'] ?? '', 1);
        $moment = $join($narrative['moment'] ?? ($dna['originalVisualMoment'] ?? ''), 1);
        $themes = $join($dna['themes'] ?? [], 6);
        $mood = $join($dna['mood'] ?? [], 6);
        $symbols = $join($dna['symbols'] ?? [], 5);
        $subjectRoles = $join($dna['subjectRoles'] ?? [], 4);
        $relationshipDynamics = $join($dna['relationshipDynamics'] ?? [], 4);
        $palette = $join($dna['palette'] ?? [], 6);
        $lighting = $join($dna['lighting'] ?? [], 5);
        $camera = $join($dna['camera'] ?? [], 5);
        $composition = $join($dna['composition'] ?? [], 5);
        $motion = $join($dna['motion'] ?? [], 5);
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
        $stagingPeople = $portraitCount >= 2 ? 'both required people' : 'the required person';

        $prompt = <<<PROMPT
MISSION: Create one finished cinematic artwork in which the attached uploaded person or people are the recognizable emotional center of an original song-inspired world.

{$identitySection}

═══════════════════════════════════════════════════════════════
SONG DNA (already approved — do not re-analyze lyrics or invent a different song meaning)
═══════════════════════════════════════════════════════════════
Essence: {$essence}
Original visual moment: {$moment}
Subject roles: {$subjectRoles}
Relationship dynamics: {$relationshipDynamics}
Themes: {$themes}
Mood: {$mood}
Symbols: {$symbols}
Environment: {$environment}
Camera: {$camera}
Composition: {$composition}
Motion: {$motion}
Palette: {$palette}
Lighting: {$lighting}
Surface and texture: {$texture}

═══════════════════════════════════════════════════════════════
NARRATIVE STAGING FREEDOM
═══════════════════════════════════════════════════════════════
Let the Song DNA fields above determine staging.
You have explicit creative freedom to choose where each person stands or sits; relative scale and distance; camera height and angle; whether subjects are together or spatially separated; pose, movement, gaze, and environmental interaction; and foreground, middle-ground, or deeper depth relationships.
Do not default to a generic two-person portrait layout unless that arrangement truly serves this specific narrative moment.
The chosen arrangement must serve the narrative moment. Avoid passport/studio framing and empty decorative scenery that treats people as optional.

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

OUTPUT GOAL: One cohesive, instantly readable image. Priorities: (1) recognizable uploaded identity for {$stagingPeople}, (2) Song DNA emotional truth and moment-specific staging, (3) selected-style strength, (4) cinematic atmosphere.
PROMPT;

        if ($special !== '') {
            $prompt .= "\n\nUSER CUSTOMIZATION — INTEGRATE WITH CARE\n"
                . "Integrate the user's directions only if they harmonize with identity, Song DNA, style, orientation, and safety. Soft guidance only; adapt conflicts or omit them.\n"
                . "User directions:\n" . substr($special, 0, 300);
        }

        return substr($prompt, 0, 12000);
    }

    private static function identitySection(int $portraitCount): string
    {
        if ($portraitCount >= 2) {
            return <<<'TXT'
IDENTITY — AUTHORITATIVE (do not repeat or override elsewhere)
The first attached image is CHARACTER 1. The second attached image is CHARACTER 2.
Every attached portrait must appear. Preserve each person separately: facial features, bone structure, skin tone, hair, and apparent age.
Never merge, swap, average, duplicate, or omit either identity.
Both people must be meaningful protagonists of the story, not token background figures, silhouettes, ghosts, statues, or generic stand-ins.
Faces must remain sufficiently visible and lit to recognize at gallery size.
“Central subject” means narratively and emotionally central — not that anyone must sit in the geometric center or closest to the camera.
Transform clothing, pose, lighting, and artistic treatment to fit the scene while preserving identity.
An image that omits any required person is unusable.
TXT;
        }

        return <<<'TXT'
IDENTITY — AUTHORITATIVE (do not repeat or override elsewhere)
The attached image is CHARACTER 1, the sole required uploaded person.
That person must appear and remain separately recognizable: facial features, bone structure, skin tone, hair, and apparent age.
Never merge, swap, average, duplicate, omit, or replace them with a generic stand-in, silhouette, ghost, or statue.
They must be a meaningful protagonist of the story, not a token background figure.
Their face must remain sufficiently visible and lit to recognize at gallery size.
“Central subject” means narratively and emotionally central — not that they must sit in the geometric center or closest to the camera.
Transform clothing, pose, lighting, and artistic treatment to fit the scene while preserving identity.
An image that omits the required person is unusable.
TXT;
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
