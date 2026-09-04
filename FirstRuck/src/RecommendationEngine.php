<?php

declare(strict_types=1);

namespace FirstRuck;

final class RecommendationEngine
{
    /** @param array<string, mixed> $answers */
    public function buildProfile(array $answers): array
    {
        $comfortableMinutes = (int) ($answers['comfortable_minutes'] ?? 30);
        $weeklyMovement = (string) ($answers['weekly_movement'] ?? '1-2');
        $sessions = (int) ($answers['sessions_per_week'] ?? 2);
        $availableLoad = (string) ($answers['available_load'] ?? 'unweighted');

        $level = match (true) {
            $comfortableMinutes >= 60 && in_array($weeklyMovement, ['3-4', '5-plus'], true) => 'Ready to progress',
            $comfortableMinutes >= 40 => 'Steady beginner',
            default => 'Fresh start',
        };

        $sessionMinutes = max(25, min(50, (int) round($comfortableMinutes * 0.75)));
        $startingLoad = match ($availableLoad) {
            '15-lb' => 'Use 5–10 lb first',
            '10-lb' => 'Use 5 lb first',
            '5-lb' => 'Use up to 5 lb',
            default => 'Begin without added load',
        };

        return [
            'level' => $level,
            'session_minutes' => $sessionMinutes,
            'starting_load' => $startingLoad,
            'weekly_frequency' => sprintf('%d sessions each week', max(1, min(4, $sessions))),
            'terrain' => $this->terrainLabel((string) ($answers['hill_comfort'] ?? 'gentle')),
            'goal' => (string) ($answers['goal'] ?? 'general-fitness'),
            'coaching_note' => 'Build consistency first. Change distance, hills, or load one at a time.',
            'health_note' => 'First Ruck offers general fitness guidance, not medical advice. Stop if pain changes your movement.',
        ];
    }

    /**
     * @param array<string, mixed> $answers
     * @param array<int, array<string, mixed>> $trails
     * @return array<int, array<string, mixed>>
     */
    public function rank(array $answers, array $trails): array
    {
        $profile = $this->buildProfile($answers);
        $targetDistance = max(2.2, min(5.2, $profile['session_minutes'] / 11.5));
        $hillComfort = (string) ($answers['hill_comfort'] ?? 'gentle');
        $surfacePreference = (string) ($answers['surface'] ?? 'compacted');
        $routePreference = (string) ($answers['route_type'] ?? 'either');
        $settingPreference = (string) ($answers['setting'] ?? 'quiet');

        $targetElevation = match ($hillComfort) {
            'rolling' => 95,
            'steep' => 160,
            default => 45,
        };

        $ranked = [];
        foreach ($trails as $trail) {
            $score = 100.0;
            $score -= abs((float) $trail['distance_km'] - $targetDistance) * 9;
            $score -= abs((int) $trail['elevation_gain_m'] - $targetElevation) / 11;

            $reasons = [];
            if ((string) $trail['surface'] === $surfacePreference) {
                $score += 8;
                $reasons[] = 'Matches your preferred surface';
            }
            if ($routePreference === 'either' || (string) $trail['route_type'] === $routePreference) {
                $score += 5;
                $reasons[] = $trail['route_type'] === 'loop' ? 'Returns you to your starting point' : 'Easy to shorten with an early turnaround';
            }
            if ($settingPreference === 'shade' && (string) $trail['shade_level'] === 'high') {
                $score += 9;
                $reasons[] = 'More shade than the other nearby options';
            }

            if ($reasons === []) {
                $reasons[] = 'Close to your starting time and climbing range';
            }

            $trail['score'] = max(55, min(98, (int) round($score)));
            $trail['reasons'] = array_slice($reasons, 0, 2);
            $trail['estimated_minutes'] = max(20, (int) round((float) $trail['distance_km'] * 12.5 + (int) $trail['elevation_gain_m'] / 18));
            $trail['facilities'] = json_decode((string) $trail['facilities_json'], true, 512, JSON_THROW_ON_ERROR);
            $trail['geometry'] = json_decode((string) $trail['geometry_json'], true, 512, JSON_THROW_ON_ERROR);
            unset($trail['facilities_json'], $trail['geometry_json']);
            $ranked[] = $trail;
        }

        usort($ranked, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);
        return $ranked;
    }

    private function terrainLabel(string $hillComfort): string
    {
        return match ($hillComfort) {
            'rolling' => 'Gentle to rolling terrain',
            'steep' => 'Rolling terrain with controlled climbs',
            default => 'Mostly level, forgiving terrain',
        };
    }
}

