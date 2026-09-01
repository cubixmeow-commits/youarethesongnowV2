#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Compare legacy, deterministic-fallback, and canonical structured prompts.
 * Optional live image A/B when GEMINI_IMAGE_LIVE_CALLS=true and credentials exist.
 *
 * Usage: php bin/compare-visual-narrative-prompts.php
 */

$root = dirname(__DIR__);
require $root . '/app/bootstrap.php';
require $root . '/tests/fixtures/visual-narrative-fixtures.php';

use Yatsn\AI\CreativePackageBuilder;
use Yatsn\Support\Config;

Config::boot($root);
$outDir = $root . '/design/review/round-012';
if (!is_dir($outDir)) {
    mkdir($outDir, 0770, true);
}

$snapshot = [
    'title' => 'Fixture Song',
    'artist' => 'Fixture Artist',
    'portraitCount' => 1,
    'styleName' => 'Cinematic Realism',
    'styleKey' => 'photoreal_cinema',
    'quality' => 'medium',
    'orientation' => 'square',
    'noTextInImage' => true,
    'specialInstructions' => '',
];

$report = [
    'version' => 'round-012.1',
    'promptLevelOnly' => true,
    'imageLevelEvaluation' => 'not_run',
    'fixtures' => [],
];

foreach (visual_narrative_fixtures() as $key => $analysis) {
    putenv('VISUAL_NARRATIVE_LEGACY_COMPILER=true');
    $_ENV['VISUAL_NARRATIVE_LEGACY_COMPILER'] = 'true';
    Config::boot($root);
    $legacy = CreativePackageBuilder::build($analysis, $snapshot, 'compare-legacy');

    putenv('VISUAL_NARRATIVE_LEGACY_COMPILER=false');
    $_ENV['VISUAL_NARRATIVE_LEGACY_COMPILER'] = 'false';
    Config::boot($root);
    $planned = CreativePackageBuilder::build($analysis, $snapshot, 'compare-planned');
    $trace = is_array($planned['visualPlanning'] ?? null) ? $planned['visualPlanning'] : [];

    $report['fixtures'][$key] = [
        'plannerSource' => $trace['plannerSource'] ?? null,
        'selectedDirectionType' => $trace['selectedDirectionType'] ?? null,
        'legacyLength' => strlen((string) $legacy['compiledPromptSafe']),
        'canonicalLength' => strlen((string) $planned['compiledPromptSafe']),
        'directionTitles' => array_map(
            static fn(array $d): string => (string) ($d['title'] ?? ''),
            is_array($trace['directions'] ?? null) ? $trace['directions'] : []
        ),
    ];
}

file_put_contents(
    $outDir . '/prompt-comparison.json',
    json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
);

echo "Wrote {$outDir}/prompt-comparison.json\n";
echo "Image A/B: set AI_PROVIDERS_ENABLED=true, GEMINI_IMAGE_LIVE_CALLS=true, and run controlled generations manually.\n";
