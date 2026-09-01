<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine\VisualNarrative;

/**
 * Content-derived direction ranking. Creative role labels do not guarantee rank order.
 * Tie-break: higher overall_rank, then ascending direction id.
 */
final class DirectionRanker
{
    /** @var list<string> */
    private const BOILERPLATE_FRAGMENTS = [
        'after the turn',
        'symbolic reframing',
        'the decisive instant',
        'immediately beside the main beat',
        'a less literal but still dna-grounded',
        'a valid alternate emphasis',
        'the emotional pivot rendered as one clear',
    ];

    /**
     * @param list<array<string,mixed>> $directions
     * @param array<string, mixed> $dna
     * @return list<array<string,mixed>>
     */
    public static function rank(array $directions, array $dna, int $portraitCount): array
    {
        $premises = [];
        foreach ($directions as $direction) {
            $premises[] = strtolower((string) ($direction['scene_premise'] ?? ''));
        }

        $scored = [];
        foreach ($directions as $index => $direction) {
            $siblings = $premises;
            unset($siblings[$index]);
            $scores = self::scoreDirection($direction, $dna, $portraitCount, array_values($siblings));
            $direction['song_dna_fidelity'] = round($scores['song_dna_fidelity'], 3);
            $direction['narrative_coherence'] = round($scores['narrative_coherence'], 3);
            $direction['visual_distinctiveness'] = round($scores['visual_distinctiveness'], 3);
            $direction['information_budget_score'] = round($scores['information_budget'], 3);
            $direction['overall_rank'] = round($scores['overall_rank'], 3);
            $scored[] = VisualNarrativeContracts::sanitizeDirection($direction);
        }

        usort($scored, static function (array $a, array $b): int {
            $rank = ($b['overall_rank'] <=> $a['overall_rank']);
            if ($rank !== 0) {
                return $rank;
            }
            return strcmp((string) ($a['id'] ?? ''), (string) ($b['id'] ?? ''));
        });

        return $scored;
    }

    /**
     * @param array<string, mixed> $direction
     * @param list<string> $siblingPremises
     * @return array{
     *   song_dna_fidelity: float,
     *   narrative_coherence: float,
     *   visual_distinctiveness: float,
     *   information_budget: float,
     *   overall_rank: float
     * }
     */
    public static function scoreDirection(array $direction, array $dna, int $portraitCount, array $siblingPremises): array
    {
        $premise = strtolower((string) ($direction['scene_premise'] ?? ''));
        $focus = strtolower((string) ($direction['emotional_focus'] ?? ''));
        $title = strtolower((string) ($direction['title'] ?? ''));
        $combined = trim($premise . ' ' . $focus . ' ' . $title);

        $dnaTerms = self::dnaTerms($dna);
        $fidelity = self::termOverlapScore($combined, $dnaTerms);
        $modelHints = is_array($direction['score_hints'] ?? null) ? $direction['score_hints'] : [];

        $moment = strtolower((string) ($dna['originalVisualMoment'] ?? ''));
        if ($modelHints === [] && $moment !== '' && str_contains($premise, substr($moment, 0, min(40, strlen($moment))))) {
            $fidelity = min(1.0, $fidelity + 0.05);
        }

        $coherence = self::coherenceScore($premise, $focus);
        $distinctiveness = self::distinctivenessScore($premise, $siblingPremises);
        $portrait = self::portraitScore($direction, $dna, $portraitCount);
        $budget = self::informationBudgetScore($direction, $dna);
        $penalty = self::boilerplatePenalty($combined);

        if ($modelHints !== []) {
            $fidelity = self::blend((float) ($modelHints['song_dna_fidelity'] ?? $fidelity), $fidelity, 0.45);
            $coherence = self::blend((float) ($modelHints['narrative_coherence'] ?? $coherence), $coherence, 0.45);
            $distinctiveness = self::blend((float) ($modelHints['visual_distinctiveness'] ?? $distinctiveness), $distinctiveness, 0.45);
            $portrait = self::blend((float) ($modelHints['portrait_suitability'] ?? $portrait), $portrait, 0.45);
            $budget = self::blend((float) ($modelHints['information_budget'] ?? $budget), $budget, 0.45);
        }

        $computedOverall = ($fidelity * 0.30)
            + ($coherence * 0.25)
            + ($distinctiveness * 0.20)
            + ($portrait * 0.15)
            + ($budget * 0.10)
            - $penalty;

        if ($modelHints !== []) {
            $hintOverall = self::weightedOverall(
                (float) ($modelHints['song_dna_fidelity'] ?? $fidelity),
                (float) ($modelHints['narrative_coherence'] ?? $coherence),
                (float) ($modelHints['visual_distinctiveness'] ?? $distinctiveness),
                (float) ($modelHints['portrait_suitability'] ?? $portrait),
                (float) ($modelHints['information_budget'] ?? $budget),
                $penalty
            );
            $overall = ($hintOverall * 0.70) + ($computedOverall * 0.30);
        } else {
            $overall = $computedOverall;
        }

        return [
            'song_dna_fidelity' => max(0.0, min(1.0, $fidelity)),
            'narrative_coherence' => max(0.0, min(1.0, $coherence)),
            'visual_distinctiveness' => max(0.0, min(1.0, $distinctiveness)),
            'information_budget' => max(0.0, min(1.0, $budget)),
            'overall_rank' => max(0.0, min(1.0, $overall)),
        ];
    }

    /** @param array<string, mixed> $dna @return list<string> */
    public static function dnaTerms(array $dna): array
    {
        $terms = [];
        foreach ([
            $dna['essence'] ?? '',
            $dna['originalVisualMoment'] ?? '',
            $dna['narrativeArchetype'] ?? '',
            self::flattenDnaText($dna['emotionalArc'] ?? null),
            self::flattenDnaText($dna['themes'] ?? null),
            self::flattenDnaText($dna['mood'] ?? null),
            self::flattenDnaText($dna['visualMetaphors'] ?? null),
            self::flattenDnaText($dna['subjectRoles'] ?? null),
            self::flattenDnaText($dna['relationshipDynamics'] ?? null),
        ] as $chunk) {
            foreach (self::tokenize((string) $chunk) as $token) {
                $terms[$token] = true;
            }
        }
        if (is_array($dna['symbols'] ?? null)) {
            foreach ($dna['symbols'] as $symbol) {
                if (!is_array($symbol)) {
                    continue;
                }
                foreach (self::tokenize((string) ($symbol['concept'] ?? '')) as $token) {
                    $terms[$token] = true;
                }
                foreach (self::tokenize((string) ($symbol['visualTranslation'] ?? '')) as $token) {
                    $terms[$token] = true;
                }
            }
        }
        if (is_array($dna['environment']['settingTypes'] ?? null)) {
            foreach ($dna['environment']['settingTypes'] as $setting) {
                foreach (self::tokenize((string) $setting) as $token) {
                    $terms[$token] = true;
                }
            }
        }
        return array_keys($terms);
    }

    /** @param list<string> $terms */
    private static function termOverlapScore(string $text, array $terms): float
    {
        if ($terms === []) {
            return 0.4;
        }
        $hits = 0;
        foreach ($terms as $term) {
            if (strlen($term) >= 4 && str_contains($text, $term)) {
                $hits++;
            }
        }
        return min(1.0, $hits / max(3, min(12, count($terms) * 0.35)));
    }

    private static function coherenceScore(string $premise, string $focus): float
    {
        $len = strlen($premise);
        if ($len < 35) {
            return 0.35;
        }
        if ($len > 420) {
            return 0.55;
        }
        $score = 0.65;
        if (preg_match('/\b(while|as|when|before|after|beneath|across|between|through)\b/i', $premise)) {
            $score += 0.1;
        }
        if ($focus !== '' && !str_contains(strtolower($focus), 'symbolic reframing')) {
            $score += 0.08;
        }
        return min(1.0, $score);
    }

    /** @param list<string> $siblingPremises */
    private static function distinctivenessScore(string $premise, array $siblingPremises): float
    {
        if ($premise === '') {
            return 0.2;
        }
        $tokens = self::tokenize($premise);
        if ($tokens === []) {
            return 0.2;
        }
        $lowestOverlap = 1.0;
        foreach ($siblingPremises as $sibling) {
            $siblingTokens = self::tokenize($sibling);
            if ($siblingTokens === []) {
                continue;
            }
            $shared = count(array_intersect($tokens, $siblingTokens));
            $overlap = $shared / max(1, count(array_unique(array_merge($tokens, $siblingTokens))));
            $lowestOverlap = min($lowestOverlap, $overlap);
        }
        return max(0.2, min(1.0, 1.0 - $lowestOverlap));
    }

    /** @param array<string, mixed> $direction */
    private static function portraitScore(array $direction, array $dna, int $portraitCount): float
    {
        $level = (string) ($direction['portrait_suitability'] ?? 'medium');
        $base = match ($level) {
            'high' => 0.88,
            'low' => 0.52,
            default => 0.72,
        };
        $premise = strtolower((string) ($direction['scene_premise'] ?? ''));
        if ($portraitCount >= 2) {
            if (str_contains($premise, 'two') || str_contains($premise, 'both') || str_contains($premise, 'shared')) {
                $base = min(1.0, $base + 0.06);
            }
            $roles = is_array($dna['subjectRoles'] ?? null) ? count($dna['subjectRoles']) : 0;
            if ($roles >= 2 && $level === 'low') {
                $base -= 0.08;
            }
        }
        return max(0.0, min(1.0, $base));
    }

    /** @param array<string, mixed> $direction */
    private static function informationBudgetScore(array $direction, array $dna): float
    {
        $text = strtolower((string) ($direction['scene_premise'] ?? ''));
        $symbolMentions = 0;
        if (is_array($dna['symbols'] ?? null)) {
            foreach ($dna['symbols'] as $symbol) {
                if (!is_array($symbol)) {
                    continue;
                }
                $concept = strtolower((string) ($symbol['concept'] ?? ''));
                if ($concept !== '' && str_contains($text, $concept)) {
                    $symbolMentions++;
                }
            }
        }
        $score = 1.0;
        if ($symbolMentions > 2) {
            $score -= 0.2;
        }
        if (substr_count($text, ' and ') > 3) {
            $score -= 0.15;
        }
        if (str_contains($text, ';') && substr_count($text, ';') > 1) {
            $score -= 0.1;
        }
        return max(0.0, min(1.0, $score));
    }

    private static function boilerplatePenalty(string $text): float
    {
        $penalty = 0.0;
        foreach (self::BOILERPLATE_FRAGMENTS as $fragment) {
            if (str_contains($text, $fragment)) {
                $penalty += 0.12;
            }
        }
        return min(0.45, $penalty);
    }

    private static function blend(float $hint, float $computed, float $hintWeight): float
    {
        $hint = max(0.0, min(1.0, $hint));
        return ($hint * $hintWeight) + ($computed * (1.0 - $hintWeight));
    }

    private static function weightedOverall(
        float $fidelity,
        float $coherence,
        float $distinctiveness,
        float $portrait,
        float $budget,
        float $penalty
    ): float {
        return ($fidelity * 0.30)
            + ($coherence * 0.25)
            + ($distinctiveness * 0.20)
            + ($portrait * 0.15)
            + ($budget * 0.10)
            - $penalty;
    }

    private static function flattenDnaText(mixed $value): string
    {
        if (is_string($value) || is_numeric($value)) {
            return (string) $value;
        }
        if (!is_array($value)) {
            return '';
        }
        $parts = [];
        foreach ($value as $item) {
            $text = self::flattenDnaText($item);
            if ($text !== '') {
                $parts[] = $text;
            }
        }
        return implode(' ', $parts);
    }

    /** @return list<string> */
    private static function tokenize(string $text): array
    {
        $text = strtolower(preg_replace('/[^a-z0-9\s]/', ' ', $text) ?? '');
        $parts = preg_split('/\s+/', $text) ?: [];
        $stop = ['the', 'and', 'with', 'from', 'that', 'this', 'into', 'their', 'they', 'them', 'one', 'two'];
        $out = [];
        foreach ($parts as $part) {
            if (strlen($part) < 4 || in_array($part, $stop, true)) {
                continue;
            }
            $out[] = $part;
        }
        return array_values(array_unique($out));
    }
}
