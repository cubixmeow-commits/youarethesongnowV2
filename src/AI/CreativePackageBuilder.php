<?php

declare(strict_types=1);

namespace Yatsn\AI;

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
        $orientation = in_array(($snapshot['orientation'] ?? ''), ['square', 'portrait', 'landscape'], true)
            ? (string) $snapshot['orientation'] : 'square';
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

        $constraints = [
            'Create an entirely original scene; do not recreate lyrics, album art, promotional art, or a music video.',
            'Do not show performer likenesses, song titles, performer names, album names, logos, trademarks, or endorsement cues.',
            $noText ? 'No letters, words, captions, signage, watermarks, or other readable text anywhere.' : 'Any incidental text must be newly invented, generic, and unrelated to music titles, performers, lyrics, brands, or copyrighted phrases.',
        ];
        $compiled = implode("\n", [
            'Create one premium, emotionally legible image with the uploaded person or people as the protagonists.',
            'Original moment: ' . $moment,
            'Meaning and mood: ' . implode(', ', array_merge($dna['themes'], $dna['mood'])),
            'Environment: ' . implode(', ', array_merge($dna['environment']['settingTypes'], $dna['environment']['weather'], $dna['environment']['spatialCharacter'])),
            'Palette and lighting: ' . implode(', ', array_merge($dna['palette'], $dna['lighting'])),
            'Camera and composition: ' . implode(', ', array_merge($dna['camera'], $dna['composition'])),
            'Curated visual style (dominant): ' . $style,
            'Orientation: ' . $orientation,
            'Portrait direction: preserve recognizable facial identity for all ' . $portraitCount . ' reference subject(s); integrate them naturally; use scene-appropriate clothing rather than copying reference clothing.',
            'Optional user direction (subordinate to identity, originality, and safety): ' . ($special !== '' ? $special : 'none'),
            'Mandatory constraints: ' . implode(' ', $constraints),
        ]);

        return [
            'adapter' => $adapter,
            'costCents' => max(0, $costCents),
            'dna' => $dna,
            'narrative' => ['moment' => $moment, 'styleLead' => $style, 'orientation' => $orientation],
            'portraitPlan' => [
                'count' => $portraitCount,
                'integration' => 'natural scene placement with preserved facial identity',
                'clothing' => 'scene-appropriate, not copied from source photo',
            ],
            'styleMap' => ['styleName' => $style, 'medium' => 'premium visual artwork', 'dominance' => 'curated style leads; song emotion supports'],
            'compiledPromptSafe' => $compiled,
        ];
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
