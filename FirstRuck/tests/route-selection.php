<?php

declare(strict_types=1);

require __DIR__ . '/../src/Coaching/RouteCoach.php';
require __DIR__ . '/../src/Coaching/RouteSelectionEngine.php';

use FirstRuck\Coaching\RouteCoach;
use FirstRuck\Coaching\RouteSelectionEngine;

function selected(bool $condition, string $label): void
{
    if (!$condition) throw new RuntimeException($label);
    echo "PASS $label\n";
}

function candidate(string $id, int $seconds, string $shape = 'out-back'): array
{
    return [
        'id' => $id,
        'name' => $id,
        'source' => 'https://example.org/map-source',
        'checkedAt' => time(),
        'durationSeconds' => $seconds,
        'distanceMeters' => 1200,
        'distanceLabel' => '1.20 km',
        'shapeKey' => $shape,
        'shape' => $shape === 'short-loop' ? 'Circuit candidate' : 'Out and back',
        'geometry' => ['type' => 'LineString', 'coordinates' => [[-119.7, 34.4], [-119.69, 34.41]]],
        'unknowns' => [],
        'isDemo' => false,
    ];
}

$engine = new RouteSelectionEngine();
$result = $engine->select([candidate('near', 1180), candidate('short', 700)], ['minutes' => 20, 'shape' => 'out-back']);
selected($result['mode'] === 'rules', 'rules fallback is the default');
selected($result['routes'][0]['id'] === 'near', 'closest duration ranks first');
selected($result['routes'][0]['factsVerified'] === true, 'provider facts pass structural verification');
selected($result['routes'][0]['suitabilityVerified'] === false, 'selection never implies route suitability');
selected(in_array('current access', $result['routes'][0]['unknowns'], true), 'unknown access remains visible');
selected(in_array('pedestrian_network', $result['routes'][0]['reasonCodes'], true), 'only approved source-bound reasons are produced');

$named = candidate('named', 1200);
$named['discoveryMode'] = 'gemini-search';
$named['discoveryRank'] = 1;
$named['distanceFromSearchMeters'] = 1200;
$named['discoverySources'] = [['title' => 'City source', 'url' => 'https://example.org/walk']];
$namedResult = $engine->select([$named], ['minutes' => 20, 'shape' => 'out-back']);
selected(in_array('named_walk', $namedResult['routes'][0]['reasonCodes'], true), 'grounded named-place reason remains source-bound');
selected(in_array('nearby_start', $namedResult['routes'][0]['reasonCodes'], true), 'mapped proximity is an approved reason');
selected($namedResult['routes'][0]['discoverySources'][0]['url'] === 'https://example.org/walk', 'validated discovery citation reaches the UI');

$stale = candidate('stale', 1200);$stale['checkedAt'] = time() - 90000;
selected($engine->select([$stale], ['minutes' => 20])['routes'] === [], 'stale route is ineligible');

$badGeometry = candidate('bad', 1200);$badGeometry['geometry']['coordinates'] = [[999, 999]];
selected($engine->select([$badGeometry], ['minutes' => 20])['routes'] === [], 'invalid geometry is ineligible');

$response = ['routes' => [['id' => 'second', 'reasonCodes' => ['duration_match']], ['id' => 'first', 'reasonCodes' => ['duration_match']]]];
$transport = fn () => ['candidates' => [['content' => ['parts' => [['text' => json_encode($response)]]]]]];
$coach = new RouteCoach(['enabled' => true, 'geminiKey' => 'test', 'geminiModel' => 'test'], $transport);
$ai = (new RouteSelectionEngine($coach))->select([candidate('first', 1190), candidate('second', 1200)], ['minutes' => 20]);
selected($ai['mode'] === 'gemini' && $ai['routes'][0]['id'] === 'second', 'validated provider ordering is applied');
selected($ai['routes'][0]['reasons'] === ['Fits your prepared walking time.'], 'provider cannot add prose or reason codes');
