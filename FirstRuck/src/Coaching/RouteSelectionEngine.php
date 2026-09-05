<?php

declare(strict_types=1);

namespace FirstRuck\Coaching;

/**
 * Normalizes map-derived walking candidates, applies deterministic eligibility
 * and scoring, and optionally lets RouteCoach reorder only the eligible IDs.
 *
 * "factsVerified" means that the provider response, geometry, distance,
 * duration, source, and freshness passed structural checks. It never means
 * that access, surface, hills, crossings, closures, weather, or safety were
 * verified.
 */
final class RouteSelectionEngine
{
    private const UNKNOWN_FACTS = [
        'current access',
        'closures',
        'sidewalks and crossings',
        'surface',
        'hill suitability',
        'weather',
    ];

    public function __construct(private ?RouteCoach $coach = null) {}

    public function select(array $candidates, array $preferences): array
    {
        $targetMinutes = max(10, min(90, (int) ($preferences['minutes'] ?? 20)));
        $requestedShape = in_array($preferences['shape'] ?? '', ['out-back', 'short-loop'], true)
            ? (string) $preferences['shape']
            : 'out-back';
        $eligible = [];

        foreach (array_slice($candidates, 0, 6) as $candidate) {
            $route = $this->normalize($candidate, $targetMinutes, $requestedShape);
            if ($route !== null) {
                $eligible[] = $route;
            }
        }

        usort($eligible, static fn (array $a, array $b): int =>
            $b['baselineScore'] <=> $a['baselineScore'] ?: $a['durationSeconds'] <=> $b['durationSeconds']
        );
        $eligible = array_slice($eligible, 0, 3);

        $coached = $this->coach?->rank($eligible) ?? [
            'mode' => 'rules',
            'routes' => array_map(static fn (array $route): array => [
                'id' => $route['id'],
                'reasonCodes' => $route['reasonCodes'],
            ], $eligible),
        ];

        $byId = [];
        foreach ($eligible as $route) {
            $byId[$route['id']] = $route;
        }

        $ranked = [];
        foreach ($coached['routes'] ?? [] as $position => $selection) {
            $id = $selection['id'] ?? null;
            if (!is_string($id) || !isset($byId[$id])) {
                continue;
            }
            $route = $byId[$id];
            $codes = array_values(array_intersect($route['reasonCodes'], $selection['reasonCodes'] ?? []));
            $route['reasonCodes'] = $codes === [] ? $route['reasonCodes'] : $codes;
            $route['reasons'] = array_values(array_filter(array_map(
                static fn (string $code): string => RouteCoach::reasonText($code),
                $route['reasonCodes']
            )));
            $route['selectionRank'] = $position + 1;
            $route['selectionMode'] = (string) ($coached['mode'] ?? 'rules');
            $route['fitLabel'] = $position === 0 ? 'Best known fit' : 'Another candidate';
            $ranked[] = $route;
            unset($byId[$id]);
        }

        // A malformed or partial coaching result can never make a valid route
        // disappear. Append remaining candidates in their rules-based order.
        foreach ($eligible as $route) {
            if (!isset($byId[$route['id']])) {
                continue;
            }
            $route['reasons'] = array_values(array_filter(array_map(
                static fn (string $code): string => RouteCoach::reasonText($code),
                $route['reasonCodes']
            )));
            $route['selectionRank'] = count($ranked) + 1;
            $route['selectionMode'] = 'rules';
            $route['fitLabel'] = $ranked === [] ? 'Best known fit' : 'Another candidate';
            $ranked[] = $route;
        }

        return [
            'mode' => (string) ($coached['mode'] ?? 'rules'),
            'routes' => $ranked,
            'message' => $ranked === []
                ? 'No map-derived candidates passed the current time, freshness, and geometry checks.'
                : 'Candidates match known route facts only. Check every listed unknown before choosing.',
        ];
    }

    private function normalize(array $candidate, int $targetMinutes, string $requestedShape): ?array
    {
        $id = $candidate['id'] ?? null;
        $source = $candidate['source'] ?? null;
        $checkedAt = $candidate['checkedAt'] ?? null;
        $duration = $candidate['durationSeconds'] ?? null;
        $distance = $candidate['distanceMeters'] ?? null;
        $geometry = $candidate['geometry'] ?? null;

        if (!is_string($id) || !preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $id)
            || !is_string($source) || !str_starts_with($source, 'https://')
            || !is_int($checkedAt) || $checkedAt > time() || time() - $checkedAt > 86400
            || !is_numeric($duration) || (float) $duration <= 0
            || !is_numeric($distance) || (float) $distance <= 0
            || !$this->validGeometry($geometry)) {
            return null;
        }

        $durationSeconds = (int) round((float) $duration);
        $targetSeconds = $targetMinutes * 60;
        if ($durationSeconds < max(300, (int) round($targetSeconds * 0.45))
            || $durationSeconds > (int) round($targetSeconds * 1.15)) {
            return null;
        }

        $shape = (string) ($candidate['shapeKey'] ?? '');
        if (!in_array($shape, ['out-back', 'short-loop'], true)) {
            $shapeLabel = strtolower((string) ($candidate['shape'] ?? ''));
            $shape = str_contains($shapeLabel, 'circuit') || str_contains($shapeLabel, 'loop')
                ? 'short-loop'
                : 'out-back';
        }

        $reasonCodes = ['pedestrian_network'];
        $durationDifference = abs($durationSeconds - $targetSeconds) / $targetSeconds;
        if ($durationDifference <= 0.2) {
            $reasonCodes[] = 'duration_match';
        }
        if ($shape === $requestedShape) {
            $reasonCodes[] = 'shape_match';
        }
        if ($shape === 'out-back') {
            $reasonCodes[] = 'easy_return';
        }

        $discovered = ($candidate['discoveryMode'] ?? '') === 'gemini-search';
        $distanceFromSearch = is_numeric($candidate['distanceFromSearchMeters'] ?? null)
            ? max(0, (int) round((float) $candidate['distanceFromSearchMeters']))
            : null;
        if ($discovered) {
            $reasonCodes[] = 'named_walk';
        }
        if ($distanceFromSearch !== null && $distanceFromSearch <= 10000) {
            $reasonCodes[] = 'nearby_start';
        }

        $score = 80 - min(45, (int) round($durationDifference * 80));
        if ($shape === $requestedShape) {
            $score += 8;
        }
        if ($shape === 'out-back') {
            $score += 4;
        }
        if ($discovered) {
            $score += max(1, 8 - (2 * max(1, (int) ($candidate['discoveryRank'] ?? 3))));
        }
        if ($distanceFromSearch !== null && $distanceFromSearch <= 10000) {
            $score += max(1, 6 - (int) floor($distanceFromSearch / 2000));
        }

        $candidate['durationSeconds'] = $durationSeconds;
        $candidate['distanceMeters'] = (int) round((float) $distance);
        $candidate['shapeKey'] = $shape;
        $candidate['factsVerified'] = true;
        $candidate['comparisonEligible'] = true;
        $candidate['suitabilityVerified'] = false;
        $candidate['baselineScore'] = max(0, min(100, $score));
        $candidate['reasonCodes'] = array_values(array_unique($reasonCodes));
        $candidate['distanceFromSearchMeters'] = $distanceFromSearch;
        $candidate['discoverySources'] = $this->validSources($candidate['discoverySources'] ?? []);
        $candidate['unknowns'] = array_values(array_unique(array_merge(
            is_array($candidate['unknowns'] ?? null) ? $candidate['unknowns'] : [],
            self::UNKNOWN_FACTS
        )));
        $candidate['terrain'] = 'Surface, hills, access, and current conditions are not verified';
        return $candidate;
    }

    private function validSources(mixed $sources): array
    {
        if (!is_array($sources)) return [];
        $valid = [];
        foreach (array_slice($sources, 0, 5) as $source) {
            if (!is_array($source)) continue;
            $url = (string) ($source['url'] ?? '');
            $title = trim((string) ($source['title'] ?? 'Walking source'));
            if (!str_starts_with($url, 'https://') || strlen($url) > 1000) continue;
            $valid[] = ['title' => mb_substr($title, 0, 160), 'url' => $url];
        }
        return $valid;
    }

    private function validGeometry(mixed $geometry): bool
    {
        if (!is_array($geometry) || !in_array($geometry['type'] ?? '', ['LineString', 'MultiLineString'], true)) {
            return false;
        }
        $coordinates = $geometry['coordinates'] ?? null;
        if (!is_array($coordinates) || $coordinates === []) {
            return false;
        }
        $points = [];
        if (($geometry['type'] ?? '') === 'LineString') {
            $points = $coordinates;
        } else {
            foreach ($coordinates as $line) {
                if (!is_array($line)) {
                    return false;
                }
                foreach ($line as $point) {
                    $points[] = $point;
                }
            }
        }
        foreach ($points as $point) {
            if (!is_array($point) || count($point) < 2 || !is_numeric($point[0]) || !is_numeric($point[1])) {
                return false;
            }
            if (abs((float) $point[0]) > 180 || abs((float) $point[1]) > 85) {
                return false;
            }
        }
        return count($points) >= 2;
    }
}
