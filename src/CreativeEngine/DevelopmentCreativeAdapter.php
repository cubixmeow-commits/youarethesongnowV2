<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine;

use Yatsn\AI\CreativeAdapterInterface;

/**
 * Deterministic development creative adapter.
 * Honestly identifies itself and never stores raw lyrics.
 */
final class DevelopmentCreativeAdapter implements CreativeAdapterInterface
{
    public function name(): string
    {
        return 'deterministic-development';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function buildPackage(array $snapshot): array
    {
        $styleName = (string) ($snapshot['styleName'] ?? 'Cinematic Realism');
        $orientation = (string) ($snapshot['orientation'] ?? 'square');
        $portraitCount = (int) ($snapshot['portraitCount'] ?? 1);
        $noText = !empty($snapshot['noTextInImage']);
        $special = trim((string) ($snapshot['specialInstructions'] ?? ''));

        $dna = [
            'schemaVersion' => 'song-dna-v2.0',
            'essence' => 'A private cinematic interpretation of shared feeling, built without lyric quotation.',
            'emotionalArc' => [
                'openingState' => 'quiet anticipation',
                'turningPoint' => 'recognition',
                'closingState' => 'warm resolve',
                'intensityPattern' => ['soft', 'rising', 'held'],
            ],
            'themes' => ['belonging', 'memory', 'devotion', 'night light'],
            'relationshipDynamics' => $portraitCount > 1 ? ['partners', 'shared gaze'] : ['solitary presence'],
            'narrativeArchetype' => 'remembrance',
            'originalVisualMoment' => 'Two silhouettes pause beneath a rain-lit window while warm stage light catches their faces.',
            'symbols' => [
                ['concept' => 'threshold', 'visualTranslation' => 'half-open doorway filled with amber light'],
                ['concept' => 'keepsake', 'visualTranslation' => 'folded letter resting on dark wood'],
            ],
            'visualMetaphors' => ['light as memory', 'weather as feeling'],
            'mood' => ['intimate', 'cinematic', 'tender'],
            'environment' => [
                'settingTypes' => ['interior night', 'city periphery'],
                'eraAtmosphere' => 'timeless contemporary',
                'weather' => ['soft rain'],
                'spatialCharacter' => ['shallow depth', 'framed doorway'],
            ],
            'palette' => ['warm paper', 'near black', 'vermilion accent'],
            'lighting' => ['single warm key', 'soft rim'],
            'camera' => ['35mm equivalent', 'eye-level'],
            'composition' => ['centered subjects', 'negative space left'],
            'motion' => ['still breath', 'falling rain'],
            'texture' => ['matte film grain', 'cloth'],
            'subjectRoles' => $portraitCount > 1 ? ['partners'] : ['seeker'],
            'ambiguities' => ['exact location undefined'],
            'confidence' => 0.84,
            'riskFlags' => [],
        ];

        $constraints = [
            'no_lyrics',
            'no_song_title_text',
            'no_band_name_text',
            'no_logos',
            'original_scene_only',
        ];
        if ($noText) {
            $constraints[] = 'no_readable_text';
        }

        $compiled = implode("\n", [
            'DEVELOPMENT ADAPTER: deterministic-development',
            'Create an original cinematic image.',
            'Moment: ' . $dna['originalVisualMoment'],
            'Style: ' . $styleName,
            'Orientation: ' . $orientation,
            'Portraits: ' . $portraitCount,
            'Special instructions (subordinate): ' . ($special !== '' ? $special : 'none'),
            'Constraints: ' . implode(', ', $constraints),
        ]);

        return [
            'adapter' => $this->name(),
            'dna' => $dna,
            'narrative' => [
                'moment' => $dna['originalVisualMoment'],
                'styleLead' => $styleName,
                'orientation' => $orientation,
            ],
            'portraitPlan' => [
                'count' => $portraitCount,
                'integration' => 'natural scene placement with preserved facial identity',
                'clothing' => 'scene-appropriate, not copied from source photo',
            ],
            'styleMap' => [
                'styleName' => $styleName,
                'medium' => 'cinematic still',
                'dominance' => 'curated style leads, song emotion supports',
            ],
            'compiledPromptSafe' => $compiled,
        ];
    }
}
