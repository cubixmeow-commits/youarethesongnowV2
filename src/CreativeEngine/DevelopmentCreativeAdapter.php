<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine;

use Yatsn\AI\CreativeAdapterInterface;
use Yatsn\AI\CreativePackageBuilder;

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
        $portraitCount = (int) ($snapshot['portraitCount'] ?? 1);
        $analysis = [
            'essence' => 'A private cinematic interpretation of shared feeling, built without lyric quotation.',
            'openingState' => 'quiet anticipation',
            'turningPoint' => 'recognition',
            'closingState' => 'warm resolve',
            'intensityPattern' => ['soft', 'rising', 'held'],
            'themes' => ['belonging', 'memory', 'devotion', 'night light'],
            'relationshipDynamics' => $portraitCount > 1 ? ['partners', 'shared gaze'] : ['solitary presence'],
            'narrativeArchetype' => 'remembrance',
            'originalVisualMoment' => $portraitCount > 1
                ? 'Two people pause beneath a rain-lit window while warm stage light catches their faces and their shared gaze signals recognition.'
                : 'One person pauses beneath a rain-lit window as warm stage light catches their face and an open doorway invites the next step.',
            'symbols' => [
                ['concept' => 'threshold', 'visualTranslation' => 'half-open doorway filled with amber light'],
                ['concept' => 'keepsake', 'visualTranslation' => 'folded letter resting on dark wood'],
            ],
            'visualMetaphors' => ['light as memory', 'weather as feeling'],
            'mood' => ['intimate', 'cinematic', 'tender'],
            'settingTypes' => ['interior night', 'city periphery'],
            'eraAtmosphere' => 'timeless contemporary',
            'weather' => ['soft rain'],
            'spatialCharacter' => ['shallow depth', 'framed doorway'],
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
        return CreativePackageBuilder::build($analysis, $snapshot, $this->name(), 0);
    }
}
