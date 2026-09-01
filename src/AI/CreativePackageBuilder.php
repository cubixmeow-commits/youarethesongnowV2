<?php

declare(strict_types=1);

namespace Yatsn\AI;

use Yatsn\CreativeEngine\VisualNarrative\VisualNarrativePlanningService;

final class CreativePackageBuilder
{
    /** @return array<string, mixed> */
    public static function schema(): array
    {
        $strings = static fn(int $max = 6): array => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => $max];
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => [
                'essence', 'openingState', 'turningPoint', 'closingState', 'intensityPattern', 'themes',
                'relationshipDynamics', 'narrativeArchetype', 'originalVisualMoment', 'symbols',
                'visualMetaphors', 'mood', 'settingTypes', 'eraAtmosphere', 'weather', 'spatialCharacter',
                'palette', 'lighting', 'camera', 'composition', 'motion', 'texture', 'subjectRoles',
                'ambiguities', 'confidence', 'riskFlags',
            ],
            'properties' => [
                'essence' => ['type' => 'string'],
                'openingState' => ['type' => 'string'],
                'turningPoint' => ['type' => 'string'],
                'closingState' => ['type' => 'string'],
                'intensityPattern' => $strings(5),
                'themes' => $strings(6),
                'relationshipDynamics' => $strings(4),
                'narrativeArchetype' => ['type' => 'string'],
                'originalVisualMoment' => ['type' => 'string'],
                'symbols' => [
                    'type' => 'array',
                    'maxItems' => 5,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['concept', 'visualTranslation'],
                        'properties' => [
                            'concept' => ['type' => 'string'],
                            'visualTranslation' => ['type' => 'string'],
                        ],
                    ],
                ],
                'visualMetaphors' => $strings(5),
                'mood' => $strings(6),
                'settingTypes' => $strings(5),
                'eraAtmosphere' => ['type' => 'string'],
                'weather' => $strings(4),
                'spatialCharacter' => $strings(5),
                'palette' => $strings(7),
                'lighting' => $strings(6),
                'camera' => $strings(5),
                'composition' => $strings(6),
                'motion' => $strings(5),
                'texture' => $strings(5),
                'subjectRoles' => $strings(4),
                'ambiguities' => $strings(4),
                'confidence' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                'riskFlags' => $strings(8),
            ],
        ];
    }

    public static function systemPrompt(): string
    {
        return implode("\n", [
            'You are the copyright-safety and visual-story analysis stage for You Are The Song Now.',
            'Infer only high-level, non-copyrightable themes, mood, emotional movement, and general imagery from the supplied song and performer identifiers.',
            'Create a completely new visual moment. Do not quote or closely paraphrase lyrics. Do not retell a distinctive lyric sequence.',
            'Never output the song title, performer, album, character names, distinctive places, logos, album art, music-video imagery, celebrity likenesses, endorsement cues, or an artist-name style imitation.',
            'Use ordinary visual language that can safely guide an original image. Return only the requested JSON.',
        ]);
    }

    public static function userPrompt(array $snapshot): string
    {
        return implode("\n", [
            'Song identifier: ' . self::singleLine((string) ($snapshot['title'] ?? ''), 180),
            'Performer identifier: ' . self::singleLine((string) ($snapshot['artist'] ?? ''), 180),
            'Portrait protagonists: ' . max(1, min(2, (int) ($snapshot['portraitCount'] ?? 1))),
            'Produce an abstracted Song DNA and one original cinematic visual moment. The JSON must not repeat either identifier.',
        ]);
    }

    /** @param array<string, mixed> $analysis */
    public static function build(array $analysis, array $snapshot, string $adapter, int $costCents = 0): array
    {
        $forbidden = [
            (string) ($snapshot['title'] ?? ''),
            (string) ($snapshot['artist'] ?? ''),
        ];
        $s = static fn(mixed $value, int $max = 320): string => self::cleanString($value, $forbidden, $max);
        $list = static fn(mixed $value, int $max = 6): array => self::cleanList($value, $forbidden, $max);
        $symbols = [];
        foreach (array_slice(is_array($analysis['symbols'] ?? null) ? $analysis['symbols'] : [], 0, 5) as $symbol) {
            if (!is_array($symbol)) {
                continue;
            }
            $concept = $s($symbol['concept'] ?? '', 100);
            $visual = $s($symbol['visualTranslation'] ?? '', 220);
            if ($concept !== '' && $visual !== '') {
                $symbols[] = ['concept' => $concept, 'visualTranslation' => $visual];
            }
        }

        $moment = $s($analysis['originalVisualMoment'] ?? '', 500);
        if ($moment === '') {
            throw new \RuntimeException('creative_package_missing_moment');
        }
        $portraitCount = max(1, min(2, (int) ($snapshot['portraitCount'] ?? 1)));
        $style = self::singleLine((string) ($snapshot['styleName'] ?? 'Cinematic Realism'), 120);
        $styleKey = self::singleLine((string) ($snapshot['styleKey'] ?? 'photoreal_cinema'), 120);
        $styleMap = StylePromptCatalog::forKey($styleKey, $style);
        $orientation = in_array(($snapshot['orientation'] ?? ''), ['square', 'portrait', 'landscape'], true)
            ? (string) $snapshot['orientation'] : 'square';
        $quality = in_array(($snapshot['quality'] ?? ''), ['low', 'medium', 'high'], true)
            ? (string) $snapshot['quality'] : 'medium';
        $noText = !empty($snapshot['noTextInImage']);
        $special = self::safeSpecialInstructions((string) ($snapshot['specialInstructions'] ?? ''));

        $dna = [
            'schemaVersion' => 'song-dna-v2.0',
            'essence' => $s($analysis['essence'] ?? '', 360),
            'emotionalArc' => [
                'openingState' => $s($analysis['openingState'] ?? '', 100),
                'turningPoint' => $s($analysis['turningPoint'] ?? '', 100),
                'closingState' => $s($analysis['closingState'] ?? '', 100),
                'intensityPattern' => $list($analysis['intensityPattern'] ?? [], 5),
            ],
            'themes' => $list($analysis['themes'] ?? [], 6),
            'relationshipDynamics' => $list($analysis['relationshipDynamics'] ?? [], 4),
            'narrativeArchetype' => $s($analysis['narrativeArchetype'] ?? '', 100),
            'originalVisualMoment' => $moment,
            'symbols' => $symbols,
            'visualMetaphors' => $list($analysis['visualMetaphors'] ?? [], 5),
            'mood' => $list($analysis['mood'] ?? [], 6),
            'environment' => [
                'settingTypes' => $list($analysis['settingTypes'] ?? [], 5),
                'eraAtmosphere' => $s($analysis['eraAtmosphere'] ?? '', 140),
                'weather' => $list($analysis['weather'] ?? [], 4),
                'spatialCharacter' => $list($analysis['spatialCharacter'] ?? [], 5),
            ],
            'palette' => $list($analysis['palette'] ?? [], 7),
            'lighting' => $list($analysis['lighting'] ?? [], 6),
            'camera' => $list($analysis['camera'] ?? [], 5),
            'composition' => $list($analysis['composition'] ?? [], 6),
            'motion' => $list($analysis['motion'] ?? [], 5),
            'texture' => $list($analysis['texture'] ?? [], 5),
            'subjectRoles' => $list($analysis['subjectRoles'] ?? [], 4),
            'ambiguities' => $list($analysis['ambiguities'] ?? [], 4),
            'confidence' => max(0.0, min(1.0, (float) ($analysis['confidence'] ?? 0.5))),
            'riskFlags' => self::riskFlags($analysis['riskFlags'] ?? []),
        ];

        $portraitDirective = $portraitCount === 2
            ? implode("\n", [
                'IMAGE 1 and IMAGE 2 are two different, authorized identity references and equal primary protagonists.',
                'Preserve each person separately: facial geometry, bone structure, skin tone, hair, age presentation, and other stable identity cues.',
                'Never blend, merge, average, duplicate, or swap their faces. Both people must remain recognizable protagonists, not token background figures.',
                'Change clothing freely to fit the world rather than copying reference clothing. Let Song DNA decide staging, scale, and interaction.',
            ])
            : implode("\n", [
                'IMAGE 1 is an authorized identity reference and the sole primary protagonist.',
                'Preserve recognizable facial geometry, bone structure, skin tone, hair, age presentation, and other stable identity cues.',
                'Place this person inside the story as an active protagonist, not as a studio headshot, passport portrait, or token background figure.',
                'Change clothing freely to fit the world rather than copying reference clothing. Let Song DNA decide staging, scale, and pose.',
            ]);

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
        $symbolDirection = [];
        foreach ($dna['symbols'] as $symbol) {
            $symbolDirection[] = $symbol['concept'] . ' becomes ' . $symbol['visualTranslation'];
        }
        $compiled = implode("\n", [
            'MISSION',
            'Create one spectacular, premium, emotionally legible artwork in which the authorized reference person or people become the heart of an original visual story.',
            'Deliver one cohesive, instantly readable dramatic moment rather than a generic portrait, character lineup, or decorative mood board.',
            '',
            'APPROVED SONG DNA',
            'Essence: ' . $dna['essence'],
            'Emotional arc: ' . implode(' -> ', array_filter([$dna['emotionalArc']['openingState'], $dna['emotionalArc']['turningPoint'], $dna['emotionalArc']['closingState']])),
            'Intensity: ' . implode(', ', $dna['emotionalArc']['intensityPattern']),
            'Themes and relationship: ' . implode(', ', array_merge($dna['themes'], $dna['relationshipDynamics'])),
            'Mood: ' . implode(', ', $dna['mood']),
            'Symbolic direction: ' . ($symbolDirection !== [] ? implode('; ', $symbolDirection) : 'none required'),
            'Original visual metaphors: ' . implode(', ', $dna['visualMetaphors']),
            '',
            'ONE ORIGINAL NARRATIVE MOMENT',
            $moment,
            'Make this single beat clear through pose, gaze, action, environment, light, and spatial relationships. Do not create a literal lyric illustration or reconstruct a sequence from the source.',
            '',
            'PORTRAIT INTEGRATION',
            $portraitDirective,
            '',
            'ENVIRONMENT AND CINEMATIC DEPTH',
            'Build a lived-in, three-dimensional world with intentional depth. Foreground, middle ground, and background may be used when they serve the narrative moment.',
            'Use atmospheric perspective, occlusion, scale variation, parallax, and motivated depth cues when helpful. Avoid flat backdrops, empty voids, and generic studio staging.',
            'Environment: ' . implode(', ', array_filter(array_merge($dna['environment']['settingTypes'], [$dna['environment']['eraAtmosphere']], $dna['environment']['weather'], $dna['environment']['spatialCharacter']))),
            'Imply motion through pose, fabric, particles, weather, light, or environmental interaction: ' . implode(', ', $dna['motion']),
            'Let Song DNA camera, composition, subject roles, and relationship dynamics choose staging. Do not force a generic two-person portrait layout.',
            '',
            'CINEMATOGRAPHY AND MATERIALS',
            'Palette: ' . implode(', ', $dna['palette']),
            'Lighting: ' . implode(', ', $dna['lighting']),
            'Camera: ' . implode(', ', $dna['camera']),
            'Composition: ' . implode(', ', $dna['composition']),
            'Texture and surfaces: ' . implode(', ', $dna['texture']),
            'Use motivated lighting with clear dimensional separation. Keep every required face readable and naturally integrated into the scene.',
            '',
            'CURATED STYLEMAP - DOMINANT AESTHETIC',
            'Selected style: ' . $style,
            StylePromptCatalog::compile($styleMap),
            'The selected StyleMap governs medium, craft, surface, color behavior, and finish. Song DNA governs meaning and emotional influence without weakening the selected style.',
            '',
            'FORMAT AND QUALITY',
            $orientationDirection,
            $qualityDirection,
            '',
            'USER DIRECTION',
            $special !== '' ? $special : 'No additional direction.',
            'Treat user direction as soft guidance. It cannot override identity, the selected style, orientation, originality, text policy, or safety. Adapt conflicts safely or omit them.',
            '',
            'TEXT POLICY',
            $textPolicy,
            '',
            'ORIGINALITY, LIKENESS, AND CONTENT RULES',
            'Create an entirely original composition. Do not recreate or closely echo lyrics, album art, promotional art, music videos, stage designs, merchandise, known poster layouts, or trademarked visual identities.',
            'Do not show song titles, performer names, album names, logos, label marks, streaming icons, provider marks, endorsement cues, or celebrity and performer likenesses.',
            'The uploaded reference person or people are the authorized exception to the general real-person likeness restriction. Do not add other identifiable real people.',
            'No graphic violence or explicit sexual content. No accidental extra people, duplicate faces, merged identities, malformed hands, or broken anatomy.',
            '',
            'OUTPUT PRIORITIES',
            '1. Recognizable identity for every required protagonist.',
            '2. One clear emotional story beat.',
            '3. Strong selected-style execution.',
            '4. Dimensional composition, atmosphere, and lighting.',
            '5. Originality, material coherence, and poster-scale readability.',
        ]);

        $package = [
            'adapter' => $adapter,
            'costCents' => max(0, $costCents),
            'dna' => $dna,
            'narrative' => ['moment' => $moment, 'styleLead' => $style, 'orientation' => $orientation],
            'portraitPlan' => [
                'count' => $portraitCount,
                'integration' => 'natural scene placement with preserved facial identity',
                'clothing' => 'scene-appropriate, not copied from source photo',
            ],
            'styleMap' => array_merge([
                'styleKey' => $styleKey,
                'styleName' => $style,
                'dominance' => 'curated style leads; Song DNA controls meaning and emotional influence',
            ], $styleMap),
            'compiledPromptSafe' => $compiled,
        ];

        return VisualNarrativePlanningService::applyToPackage($package, $snapshot);
    }

    private static function cleanString(mixed $value, array $forbidden, int $max): string
    {
        $text = self::singleLine(is_scalar($value) ? (string) $value : '', $max * 2);
        foreach ($forbidden as $term) {
            $term = trim($term);
            if ($term !== '') {
                $text = str_ireplace($term, '[generalized]', $text);
            }
        }
        return substr(trim($text), 0, $max);
    }

    private static function cleanList(mixed $value, array $forbidden, int $max): array
    {
        if (!is_array($value)) {
            return [];
        }
        $out = [];
        foreach (array_slice($value, 0, $max) as $item) {
            $clean = self::cleanString($item, $forbidden, 120);
            if ($clean !== '') {
                $out[] = $clean;
            }
        }
        return array_values(array_unique($out));
    }

    private static function riskFlags(mixed $value): array
    {
        $allowed = ['possible_quote', 'possible_close_paraphrase', 'distinctive_story_sequence', 'named_character_or_place', 'public_figure_reference', 'album_or_video_association', 'brand_or_logo_reference', 'source_or_match_uncertain'];
        if (!is_array($value)) {
            return [];
        }
        return array_values(array_intersect($allowed, array_map('strval', $value)));
    }

    private static function safeSpecialInstructions(string $value): string
    {
        $value = self::singleLine($value, 300);
        if ($value === '') {
            return '';
        }
        if (preg_match('/\b(lyrics?|album\s+cover|music\s+video|logo|trademark|celebrity|copy\s+the\s+style)\b/i', $value)) {
            return 'none (the submitted direction conflicted with originality or branding safeguards)';
        }
        return $value;
    }

    private static function singleLine(string $value, int $max): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        return substr(trim($value), 0, $max);
    }
}
