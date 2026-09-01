<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine\VisualNarrative;

use Yatsn\AI\StylePromptCatalog;

/**
 * Structured prompt compiler — scene contract first, style subordinate.
 */
final class StructuredPromptCompiler
{
    /**
     * @param array<string, mixed> $sceneContract
     * @param array<string, mixed> $package Existing creative package (dna, styleMap, portraitPlan)
     * @param array<string, mixed> $snapshot
     */
    public static function compile(array $sceneContract, array $package, array $snapshot): string
    {
        $dna = is_array($package['dna'] ?? null) ? $package['dna'] : [];
        $style = is_array($package['styleMap'] ?? null) ? $package['styleMap'] : [];
        $portraitCount = max(1, min(2, (int) ($snapshot['portraitCount'] ?? ($package['portraitPlan']['count'] ?? 1))));
        $styleName = (string) ($style['styleName'] ?? ($snapshot['styleName'] ?? 'Selected style'));
        $quality = in_array(($snapshot['quality'] ?? ''), ['low', 'medium', 'high'], true)
            ? (string) $snapshot['quality'] : 'medium';
        $orientation = in_array(($snapshot['orientation'] ?? ''), ['square', 'portrait', 'landscape'], true)
            ? (string) $snapshot['orientation'] : 'square';
        $noText = !empty($snapshot['noTextInImage']);
        $special = self::safeSpecial((string) ($snapshot['specialInstructions'] ?? ''));

        $portraitDirective = self::portraitDirective($portraitCount, $sceneContract);
        $qualityDirection = match ($quality) {
            'low' => 'Efficient concept-quality render: protect facial identity, narrative clarity, composition, and style before fine micro-detail.',
            'high' => 'Premium poster-ready render: exceptional facial fidelity, rich dynamic range, refined materials, atmospheric depth, and coherent micro-detail without artificial oversharpening.',
            default => 'Refined production render: strong facial fidelity, filmic or medium-appropriate finish, dimensional materials, controlled detail, and clean poster-scale readability.',
        };
        $orientationDirection = match ($orientation) {
            'portrait' => 'Portrait 3:4 composition. Use vertical depth, a strong full-height silhouette, and intentional space above and below the focal action.',
            'landscape' => 'Landscape 4:3 composition. Use lateral storytelling, environmental scale, and a clear left-to-right or diagonal visual path.',
            default => 'Square 1:1 composition. Build a strong central or balanced focal structure that remains readable as a gallery thumbnail.',
        };
        $textPolicy = $noText
            ? 'No letters, words, captions, signage, signatures, typographic marks, watermarks, or other readable text anywhere.'
            : 'Text is optional, never required. If used, it must be newly invented, generic or user-directed, visually integrated, and unrelated to lyrics, song titles, performers, albums, brands, or copyrighted phrases.';

        $styleMap = StylePromptCatalog::forKey(
            (string) ($style['styleKey'] ?? 'photoreal_cinema'),
            $styleName
        );

        $hierarchy = is_array($sceneContract['composition_hierarchy'] ?? null)
            ? implode(' → ', $sceneContract['composition_hierarchy']) : '';
        $negative = is_array($sceneContract['negative_constraints'] ?? null)
            ? implode('; ', $sceneContract['negative_constraints']) : '';

        $sections = [
            'MISSION',
            'Create one spectacular, premium, emotionally legible artwork from a validated Visual Scene Contract.',
            'Deliver one cohesive, instantly readable dramatic instant — not a generic portrait or mood collage.',
            '',
            'SEMANTIC SCENE PREMISE',
            (string) ($sceneContract['decisive_instant'] ?? ''),
            'Emotional truth: ' . (string) ($dna['essence'] ?? ''),
            'Viewer relationship: ' . (string) ($sceneContract['viewer_relationship'] ?? 'witness'),
            'POV owner: ' . (string) ($sceneContract['pov_owner'] ?? 'protagonist'),
            '',
            'DECISIVE INSTANT AND VISIBLE ACTION',
            'Action: ' . (string) ($sceneContract['visible_action'] ?? ''),
            'Relationship geometry: ' . (string) ($sceneContract['relationship_geometry'] ?? ''),
            'Off-screen tension: ' . (string) ($sceneContract['offscreen_tension'] ?? ''),
            '',
            'SUBJECT ROLES AND PORTRAIT INSTRUCTIONS',
            'Roles: ' . implode(', ', is_array($sceneContract['subject_roles'] ?? null) ? $sceneContract['subject_roles'] : []),
            $portraitDirective,
            (string) ($sceneContract['portrait_integration_plan'] ?? ''),
            '',
            'ENVIRONMENT AND SPATIAL RELATIONSHIPS',
            (string) ($sceneContract['environment'] ?? ''),
            'Build lived-in depth with foreground, middle ground, and background only when each layer serves the instant.',
            '',
            'PRIMARY SYMBOLISM',
            'Primary symbol: ' . (string) ($sceneContract['primary_symbol'] ?? ''),
            'Supporting detail: ' . (string) ($sceneContract['secondary_detail'] ?? ''),
            'Preserve ambiguity: ' . (string) ($sceneContract['ambiguity_to_preserve'] ?? ''),
            '',
            'CAMERA AND COMPOSITION',
            'Camera: ' . (string) ($sceneContract['camera_position'] ?? ''),
            'Shot scale: ' . (string) ($sceneContract['shot_scale'] ?? ''),
            'Lens: ' . (string) ($sceneContract['lens_behavior'] ?? ''),
            'Hierarchy: ' . $hierarchy,
            '',
            'LIGHTING, PALETTE, AND ATMOSPHERE',
            'Lighting: ' . (string) ($sceneContract['lighting_logic'] ?? ''),
            'Color: ' . (string) ($sceneContract['color_logic'] ?? ''),
            'Atmosphere: ' . (string) ($sceneContract['atmosphere'] ?? ''),
            '',
            'MOTION AND TEXTURE',
            'Motion: ' . (string) ($sceneContract['motion_state'] ?? ''),
            'Texture: ' . (string) ($sceneContract['texture'] ?? ''),
            '',
            'CURATED STYLEMAP — RENDERING LANGUAGE ONLY',
            'Selected style: ' . $styleName,
            StylePromptCatalog::compile($styleMap),
            'Style governs medium and finish. The Scene Contract governs meaning, staging, and symbolism.',
            '',
            'OUTPUT REQUIREMENTS',
            $orientationDirection,
            $qualityDirection,
            '',
            'USER DIRECTION',
            $special !== '' ? $special : 'No additional direction.',
            '',
            'TEXT POLICY',
            $textPolicy,
            '',
            'NEGATIVE CONSTRAINTS',
            $negative,
            'No graphic violence or explicit sexual content. No accidental extra people, duplicate faces, merged identities, malformed hands, or broken anatomy.',
            'Create an entirely original composition. Do not recreate lyrics, album art, music videos, logos, trademarks, or performer likenesses.',
            '',
            'OUTPUT PRIORITIES',
            '1. Recognizable identity for every required protagonist.',
            '2. One clear scene-specific emotional instant.',
            '3. Strong selected-style execution subordinate to scene meaning.',
            '4. Dimensional composition, atmosphere, and lighting.',
            '5. Originality, material coherence, and poster-scale readability.',
        ];

        return implode("\n", $sections);
    }

    /**
     * @param array<string, mixed> $sceneContract
     * @return list<array{key:string,content:string}>
     */
    public static function structuredSections(array $sceneContract, array $package, array $snapshot): array
    {
        $full = self::compile($sceneContract, $package, $snapshot);
        $sections = [];
        $currentKey = 'preamble';
        $buffer = [];
        foreach (explode("\n", $full) as $line) {
            if ($line !== '' && $line === strtoupper($line) && !str_contains($line, ':') && strlen($line) < 60) {
                if ($buffer !== []) {
                    $sections[] = ['key' => $currentKey, 'content' => trim(implode("\n", $buffer))];
                }
                $currentKey = strtolower(str_replace([' ', '—', '–'], '_', $line));
                $buffer = [];
                continue;
            }
            $buffer[] = $line;
        }
        if ($buffer !== []) {
            $sections[] = ['key' => $currentKey, 'content' => trim(implode("\n", $buffer))];
        }
        return $sections;
    }

    /** @param array<string, mixed> $sceneContract */
    private static function portraitDirective(int $portraitCount, array $sceneContract): string
    {
        if ($portraitCount >= 2) {
            return implode("\n", [
                'IMAGE 1 and IMAGE 2 are two different, authorized identity references and equal primary protagonists.',
                'Preserve each person separately. Never blend, merge, average, duplicate, or swap their faces.',
                'Staging follows the Scene Contract relationship geometry and portrait integration plan.',
            ]);
        }
        return implode("\n", [
            'IMAGE 1 is the authorized identity reference and sole primary protagonist.',
            'Preserve recognizable facial geometry, bone structure, skin tone, hair, and apparent age.',
            'Staging follows the Scene Contract — not a generic studio headshot.',
        ]);
    }

    private static function safeSpecial(string $value): string
    {
        $value = VisualNarrativeContracts::clip($value, 300);
        if ($value === '') {
            return '';
        }
        if (preg_match('/\b(lyrics?|album\s+cover|music\s+video|logo|trademark|celebrity|copy\s+the\s+style)\b/i', $value)) {
            return 'none (the submitted direction conflicted with originality or branding safeguards)';
        }
        return $value;
    }
}
