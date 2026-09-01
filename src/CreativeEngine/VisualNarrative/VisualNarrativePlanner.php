<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine\VisualNarrative;

/**
 * Deterministic Visual Narrative planner from normalized internal Song DNA.
 * Portrait image data is never used — only portrait count for suitability scoring.
 */
final class VisualNarrativePlanner
{
    /**
     * @param array<string, mixed> $dna Normalized song-dna-v2.0 from CreativePackageBuilder
     * @param array<string, mixed> $snapshot Job/draft snapshot (no portrait bytes)
     * @return array{
     *   board: array<string,mixed>,
     *   directions: list<array<string,mixed>>,
     *   selectedDirection: array<string,mixed>,
     *   sceneContract: array<string,mixed>
     * }
     */
    public static function plan(array $dna, array $snapshot): array
    {
        $portraitCount = max(1, min(2, (int) ($snapshot['portraitCount'] ?? 1)));
        $board = self::buildBoard($dna);
        $directions = self::buildDirections($dna, $board, $portraitCount);
        $ranked = DirectionRanker::rank($directions, $dna, $portraitCount);
        $selected = $ranked[0];
        $contract = self::compileSceneContract($dna, $board, $selected, $portraitCount);

        return [
            'board' => $board,
            'directions' => $ranked,
            'selectedDirection' => $selected,
            'sceneContract' => $contract,
        ];
    }

    /** @param array<string, mixed> $dna */
    public static function buildBoard(array $dna): array
    {
        $arc = is_array($dna['emotionalArc'] ?? null) ? $dna['emotionalArc'] : [];
        $environment = is_array($dna['environment'] ?? null) ? $dna['environment'] : [];
        $pivot = trim((string) ($arc['turningPoint'] ?? ''));
        if ($pivot === '') {
            $pivot = trim((string) ($dna['originalVisualMoment'] ?? ''));
        }

        $symbols = [];
        foreach (is_array($dna['symbols'] ?? null) ? $dna['symbols'] : [] as $symbol) {
            if (!is_array($symbol)) {
                continue;
            }
            $concept = trim((string) ($symbol['concept'] ?? ''));
            $visual = trim((string) ($symbol['visualTranslation'] ?? ''));
            if ($concept !== '' && $visual !== '') {
                $symbols[] = $concept . ': ' . $visual;
            }
        }

        $dnaBasis = array_values(array_filter(array_unique(array_merge(
            is_array($dna['themes'] ?? null) ? $dna['themes'] : [],
            is_array($dna['mood'] ?? null) ? $dna['mood'] : [],
            [$dna['narrativeArchetype'] ?? '', $dna['essence'] ?? '']
        ))));

        $board = VisualNarrativeContracts::sanitizeBoard([
            'version' => VisualNarrativeContracts::BOARD_VERSION,
            'song_dna_basis' => $dnaBasis,
            'emotional_pivot' => $pivot,
            'character_roles' => is_array($dna['subjectRoles'] ?? null) ? $dna['subjectRoles'] : [],
            'relationship_dynamic' => implode('; ', is_array($dna['relationshipDynamics'] ?? null) ? $dna['relationshipDynamics'] : []),
            'candidate_environments' => array_values(array_filter(array_merge(
                is_array($environment['settingTypes'] ?? null) ? $environment['settingTypes'] : [],
                is_array($environment['spatialCharacter'] ?? null) ? $environment['spatialCharacter'] : [],
                isset($environment['eraAtmosphere']) ? [(string) $environment['eraAtmosphere']] : []
            ))),
            'symbolic_artifacts' => $symbols,
            'physical_actions' => self::inferPhysicalActions($dna),
            'visual_escalation' => self::visualEscalation($arc),
            'recurring_motifs' => array_slice(is_array($dna['visualMetaphors'] ?? null) ? $dna['visualMetaphors'] : [], 0, 3),
            'ambiguities_to_preserve' => is_array($dna['ambiguities'] ?? null) ? $dna['ambiguities'] : [],
            'literal_or_unsafe_interpretations_to_avoid' => [
                'lyric quotation', 'album art recreation', 'performer likeness', 'logo or trademark marks',
            ],
            'portrait_opportunities' => self::portraitOpportunities($dna),
            'confidence' => (float) ($dna['confidence'] ?? 0.7),
        ]);

        $validation = VisualNarrativeContracts::validateBoard($board);
        return $validation['repaired'];
    }

    /**
     * @param array<string, mixed> $dna
     * @param array<string, mixed> $board
     * @return list<array<string,mixed>>
     */
    public static function buildDirections(array $dna, array $board, int $portraitCount): array
    {
        $moment = trim((string) ($dna['originalVisualMoment'] ?? ''));
        $essence = trim((string) ($dna['essence'] ?? ''));
        $opening = trim((string) (($dna['emotionalArc']['openingState'] ?? '') ?: ''));
        $closing = trim((string) (($dna['emotionalArc']['closingState'] ?? '') ?: ''));
        $environment = self::primaryEnvironment($dna);
        $primarySymbol = $board['symbolic_artifacts'][0] ?? 'a charged symbolic detail';
        $roles = $board['character_roles'];
        $roleLabel = $roles[0] ?? ($portraitCount > 1 ? 'two protagonists' : 'the protagonist');

        $dnaIds = VisualNarrativeContracts::stringList(array_merge(
            is_array($dna['themes'] ?? null) ? $dna['themes'] : [],
            is_array($dna['mood'] ?? null) ? $dna['mood'] : [],
            [$dna['narrativeArchetype'] ?? '']
        ), 8, 60);

        $primary = [
            'id' => 'dir-primary',
            'type' => 'primary',
            'title' => self::titleFromMoment($moment, $essence, 'At the pivot'),
            'user_summary' => self::summaryFromDna($dna, 'The decisive instant where ' . $board['emotional_pivot'] . ' becomes visible.'),
            'scene_premise' => $moment !== '' ? $moment : $essence,
            'emotional_focus' => $board['emotional_pivot'],
            'dna_element_ids' => $dnaIds,
            'portrait_suitability' => $portraitCount > 1 ? 'high' : 'high',
            'viewpoint' => 'participant',
            'relationship_emphasis' => implode(', ', array_slice(is_array($dna['relationshipDynamics'] ?? null) ? $dna['relationshipDynamics'] : ['shared pivot'], 0, 2)),
            'symbol_strategy' => 'anchor the turning point on ' . self::firstSymbolLabel($board),
        ];

        $alternateFocus = $closing !== '' ? $closing : ($opening !== '' ? $opening : $board['emotional_pivot']);
        $alternate = [
            'id' => 'dir-alternate',
            'type' => 'alternate',
            'title' => self::titleFromState($alternateFocus, $environment),
            'user_summary' => self::summaryFromDna($dna, 'A neighboring beat where ' . $alternateFocus . ' reshapes the room.'),
            'scene_premise' => self::alternatePremise($dna, $moment, $alternateFocus, $environment, $roleLabel),
            'emotional_focus' => $alternateFocus,
            'dna_element_ids' => $dnaIds,
            'portrait_suitability' => 'medium',
            'viewpoint' => 'witness',
            'relationship_emphasis' => 'residual tension in ' . $environment,
            'symbol_strategy' => 'let aftermath echo through ' . self::firstSymbolLabel($board),
        ];

        $metaphor = is_array($dna['visualMetaphors'] ?? null) && $dna['visualMetaphors'] !== []
            ? (string) $dna['visualMetaphors'][0] : (string) ($dna['essence'] ?? 'transformation');
        $unexpected = [
            'id' => 'dir-unexpected',
            'type' => 'unexpected',
            'title' => self::titleFromMetaphor($metaphor, $primarySymbol),
            'user_summary' => self::summaryFromDna($dna, $metaphor . ' becomes the visible scene instead of literal action.'),
            'scene_premise' => self::unexpectedPremise($dna, $environment, $primarySymbol, $roleLabel),
            'emotional_focus' => implode(', ', array_slice(is_array($dna['visualMetaphors'] ?? null) ? $dna['visualMetaphors'] : [$metaphor], 0, 2)),
            'dna_element_ids' => $dnaIds,
            'portrait_suitability' => $portraitCount > 1 ? 'medium' : 'low',
            'viewpoint' => 'observer',
            'relationship_emphasis' => 'symbol-led reading of ' . $metaphor,
            'symbol_strategy' => 'let ' . self::firstSymbolLabel($board) . ' carry the metaphor',
        ];

        return [
            VisualNarrativeContracts::sanitizeDirection($primary),
            VisualNarrativeContracts::sanitizeDirection($alternate),
            VisualNarrativeContracts::sanitizeDirection($unexpected),
        ];
    }

    /**
     * @param list<array<string,mixed>> $directions
     * @return list<array<string,mixed>>
     * @deprecated Use DirectionRanker::rank()
     */
    public static function rankDirections(array $directions, int $portraitCount): array
    {
        return DirectionRanker::rank($directions, [], $portraitCount);
    }

    /** @param array<string, mixed> $direction */
    public static function portraitIntegrationPlan(int $portraitCount, array $direction): string
    {
        $suitability = (string) ($direction['portrait_suitability'] ?? 'medium');
        if ($portraitCount >= 2) {
            return 'Both uploaded people are co-protagonists with separate readable faces; staging follows '
                . (string) ($direction['relationship_emphasis'] ?? 'shared emphasis')
                . ' (' . $suitability . ' suitability).';
        }
        return 'The uploaded person is the sole protagonist with motivated placement in the scene ('
            . $suitability . ' suitability).';
    }

    /**
     * @param array<string, mixed> $dna
     * @param array<string, mixed> $board
     * @param array<string, mixed> $direction
     */
    public static function compileSceneContract(array $dna, array $board, array $direction, int $portraitCount): array
    {
        $camera = is_array($dna['camera'] ?? null) ? $dna['camera'] : [];
        $composition = is_array($dna['composition'] ?? null) ? $dna['composition'] : [];
        $lighting = is_array($dna['lighting'] ?? null) ? $dna['lighting'] : [];
        $palette = is_array($dna['palette'] ?? null) ? $dna['palette'] : [];
        $motion = is_array($dna['motion'] ?? null) ? $dna['motion'] : [];
        $texture = is_array($dna['texture'] ?? null) ? $dna['texture'] : [];
        $weather = is_array(($dna['environment']['weather'] ?? null)) ? $dna['environment']['weather'] : [];

        $viewpoint = (string) ($direction['viewpoint'] ?? 'witness');
        $viewerRelationship = match ($viewpoint) {
            'participant' => 'participant',
            'observer' => 'observer',
            default => 'witness',
        };

        $primarySymbol = $board['symbolic_artifacts'][0] ?? 'a single charged object';
        $secondary = $board['symbolic_artifacts'][1] ?? ($board['recurring_motifs'][0] ?? '');

        $contract = VisualNarrativeContracts::sanitizeSceneContract([
            'version' => VisualNarrativeContracts::SCENE_VERSION,
            'pov_owner' => $portraitCount > 1 ? 'the two protagonists' : 'the protagonist',
            'viewer_relationship' => $viewerRelationship,
            'decisive_instant' => (string) ($direction['scene_premise'] ?? $dna['originalVisualMoment'] ?? ''),
            'environment' => self::primaryEnvironment($dna),
            'subject_roles' => $board['character_roles'] !== [] ? $board['character_roles'] : ['protagonist'],
            'visible_action' => $board['physical_actions'][0] ?? 'a held breath before motion continues',
            'relationship_geometry' => $board['relationship_dynamic'] !== ''
                ? (string) $board['relationship_dynamic']
                : ($portraitCount > 1 ? 'two figures in shared but asymmetric space' : 'single figure anchored in environment'),
            'primary_symbol' => $primarySymbol,
            'secondary_detail' => $secondary !== '' ? $secondary : implode(', ', array_slice($weather, 0, 1)),
            'offscreen_tension' => $board['ambiguities_to_preserve'][0] ?? 'something unresolved beyond the frame edge',
            'camera_position' => $camera[0] ?? 'motivated eye-level near the emotional pivot',
            'shot_scale' => $composition[0] ?? 'medium-wide with readable faces',
            'lens_behavior' => $camera[1] ?? 'natural perspective with gentle depth falloff',
            'composition_hierarchy' => self::compositionHierarchy($composition, $primarySymbol),
            'lighting_logic' => implode('; ', array_slice($lighting, 0, 2)) ?: 'one motivated key with dimensional separation',
            'color_logic' => implode('; ', array_slice($palette, 0, 3)) ?: 'palette driven by emotional temperature',
            'atmosphere' => implode(', ', array_merge(
                is_array($dna['mood'] ?? null) ? array_slice($dna['mood'], 0, 3) : [],
                $weather
            )),
            'motion_state' => implode(', ', array_slice($motion, 0, 2)) ?: 'held kinetic tension',
            'texture' => implode(', ', array_slice($texture, 0, 2)) ?: 'tactile surfaces with readable material contrast',
            'ambiguity_to_preserve' => implode('; ', $board['ambiguities_to_preserve']) ?: 'leave one relational question unanswered',
            'portrait_integration_plan' => self::portraitIntegrationPlan($portraitCount, $direction),
            'negative_constraints' => array_merge(
                ['no lyric text', 'no logos', 'no extra unidentified people'],
                $board['literal_or_unsafe_interpretations_to_avoid']
            ),
        ]);

        $validation = VisualNarrativeContracts::validateSceneContract($contract);
        return $validation['repaired'];
    }

    /** @param array<string, mixed> $dna */
    private static function inferPhysicalActions(array $dna): array
    {
        $moment = strtolower((string) ($dna['originalVisualMoment'] ?? ''));
        $actions = [];
        foreach (['step', 'reach', 'turn', 'pause', 'hold', 'run', 'embrace', 'look', 'wait'] as $verb) {
            if (str_contains($moment, $verb)) {
                $actions[] = $verb . 'ing as part of the decisive instant';
            }
        }
        if ($actions === []) {
            $actions[] = 'a physical pause at the emotional pivot';
        }
        return array_slice($actions, 0, 3);
    }

    /** @param array<string, mixed> $arc */
    private static function visualEscalation(array $arc): string
    {
        $pattern = is_array($arc['intensityPattern'] ?? null) ? $arc['intensityPattern'] : [];
        if ($pattern !== []) {
            return 'intensity moves ' . implode(' then ', $pattern);
        }
        $opening = (string) ($arc['openingState'] ?? '');
        $closing = (string) ($arc['closingState'] ?? '');
        if ($opening !== '' && $closing !== '') {
            return $opening . ' escalates toward ' . $closing;
        }
        return 'emotional pressure concentrates into one readable instant';
    }

    /** @param array<string, mixed> $dna */
    private static function portraitOpportunities(array $dna): array
    {
        $roles = is_array($dna['subjectRoles'] ?? null) ? $dna['subjectRoles'] : [];
        $dynamics = is_array($dna['relationshipDynamics'] ?? null) ? $dna['relationshipDynamics'] : [];
        return array_values(array_filter(array_merge($roles, $dynamics)));
    }

    /** @param array<string, mixed> $dna */
    private static function primaryEnvironment(array $dna): string
    {
        $environment = is_array($dna['environment'] ?? null) ? $dna['environment'] : [];
        $parts = array_merge(
            is_array($environment['settingTypes'] ?? null) ? $environment['settingTypes'] : [],
            isset($environment['eraAtmosphere']) ? [(string) $environment['eraAtmosphere']] : [],
            is_array($environment['spatialCharacter'] ?? null) ? $environment['spatialCharacter'] : []
        );
        $text = implode(', ', array_values(array_filter($parts)));
        return $text !== '' ? $text : 'a specific lived-in environment';
    }

    /** @param array<string, mixed> $dna */
    private static function alternatePremise(array $dna, string $moment, string $focus, string $environment, string $roleLabel): string
    {
        $weather = '';
        if (is_array($dna['environment']['weather'] ?? null) && $dna['environment']['weather'] !== []) {
            $weather = (string) $dna['environment']['weather'][0];
        }
        if ($moment === '') {
            return $roleLabel . ' remains in ' . $environment . ($weather !== '' ? ' under ' . $weather : '') . ' while ' . $focus . ' settles through the space.';
        }
        $setting = is_array($dna['environment']['settingTypes'] ?? null) && $dna['environment']['settingTypes'] !== []
            ? (string) $dna['environment']['settingTypes'][0]
            : $environment;
        return $roleLabel . ' in ' . $setting . ' holds the echo of ' . $focus . ' while the room still remembers ' . substr($moment, 0, min(90, strlen($moment))) . '.';
    }

    /** @param array<string, mixed> $dna */
    private static function unexpectedPremise(array $dna, string $environment, string $symbol, string $roleLabel): string
    {
        $metaphor = '';
        if (is_array($dna['visualMetaphors'] ?? null) && $dna['visualMetaphors'] !== []) {
            $metaphor = (string) $dna['visualMetaphors'][0];
        }
        $basis = $metaphor !== '' ? $metaphor : (string) ($dna['essence'] ?? 'transformation');
        return $roleLabel . ' encounters ' . $symbol . ' in ' . $environment . ' where ' . $basis . ' becomes the scene instead of literal action.';
    }

    /** @param list<string> $composition */
    private static function compositionHierarchy(array $composition, string $primarySymbol): array
    {
        $hierarchy = ['protagonist faces readable at gallery scale'];
        if ($composition !== []) {
            $hierarchy[] = (string) $composition[0];
        }
        $hierarchy[] = 'primary symbol: ' . $primarySymbol;
        $hierarchy[] = 'environment supports but does not overwhelm subjects';
        return array_slice($hierarchy, 0, 4);
    }

    private static function titleFromMoment(string $moment, string $essence, string $fallback): string
    {
        $source = $moment !== '' ? $moment : $essence;
        if ($source === '') {
            return $fallback;
        }
        $words = preg_split('/\s+/', trim($source)) ?: [];
        return VisualNarrativeContracts::clip(implode(' ', array_slice($words, 0, 6)), 72);
    }

    /** @param array<string, mixed> $dna */
    private static function summaryFromDna(array $dna, string $suffix): string
    {
        $essence = trim((string) ($dna['essence'] ?? ''));
        if ($essence === '') {
            return VisualNarrativeContracts::clip($suffix, 240);
        }
        return VisualNarrativeContracts::clip($essence . ' — ' . $suffix, 240);
    }

    private static function titleFromState(string $state, string $environment): string
    {
        $env = trim(explode(',', $environment)[0] ?? $environment);
        return VisualNarrativeContracts::clip(ucfirst($state) . ' in ' . $env, 72);
    }

    private static function titleFromMetaphor(string $metaphor, string $symbol): string
    {
        $symbolLabel = trim(explode(':', $symbol)[0] ?? $symbol);
        return VisualNarrativeContracts::clip(ucfirst($metaphor) . ' through ' . $symbolLabel, 72);
    }

    /** @param array<string, mixed> $board */
    private static function firstSymbolLabel(array $board): string
    {
        $symbol = (string) ($board['symbolic_artifacts'][0] ?? 'a charged detail');
        return trim(explode(':', $symbol)[0] ?? $symbol);
    }
}

