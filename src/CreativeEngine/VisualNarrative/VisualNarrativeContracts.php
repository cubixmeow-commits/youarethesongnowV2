<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine\VisualNarrative;

/**
 * Versioned internal contracts for the Visual Narrative Planning Layer.
 */
final class VisualNarrativeContracts
{
    public const BOARD_VERSION = 'visual-board-v1';
    public const SCENE_VERSION = 'visual-scene-v1';
    public const COMPILER_VERSION = 'structured-prompt-v1';
    public const TRACE_VERSION = 'planning-trace-v1';

    /** @return list<string> */
    public static function directionTypes(): array
    {
        return ['primary', 'alternate', 'unexpected'];
    }

    /** @return list<string> */
    public static function portraitSuitabilityLevels(): array
    {
        return ['high', 'medium', 'low'];
    }

    /** @return list<string> */
    public static function viewerRelationships(): array
    {
        return ['participant', 'witness', 'observer'];
    }

    /**
     * @param array<string, mixed> $board
     * @return array{ok:bool,errors:list<string>,repaired:array<string,mixed>}
     */
    public static function validateBoard(array $board): array
    {
        $errors = [];
        $repaired = $board;
        if (($board['version'] ?? '') !== self::BOARD_VERSION) {
            $errors[] = 'invalid_board_version';
            $repaired['version'] = self::BOARD_VERSION;
        }
        foreach ([
            'song_dna_basis' => 'list',
            'emotional_pivot' => 'string',
            'character_roles' => 'list',
            'relationship_dynamic' => 'string',
            'candidate_environments' => 'list',
            'symbolic_artifacts' => 'list',
            'physical_actions' => 'list',
            'visual_escalation' => 'string',
            'recurring_motifs' => 'list',
            'ambiguities_to_preserve' => 'list',
            'literal_or_unsafe_interpretations_to_avoid' => 'list',
            'portrait_opportunities' => 'list',
        ] as $field => $type) {
            if ($type === 'list' && !is_array($board[$field] ?? null)) {
                $errors[] = 'board_missing_' . $field;
                $repaired[$field] = [];
            }
            if ($type === 'string' && trim((string) ($board[$field] ?? '')) === '') {
                $errors[] = 'board_missing_' . $field;
            }
        }
        $confidence = (float) ($board['confidence'] ?? -1);
        if ($confidence < 0 || $confidence > 1) {
            $errors[] = 'board_invalid_confidence';
            $repaired['confidence'] = max(0.0, min(1.0, $confidence < 0 ? 0.5 : $confidence));
        }
        return ['ok' => $errors === [], 'errors' => $errors, 'repaired' => self::sanitizeBoard($repaired)];
    }

    /**
     * @param array<string, mixed> $direction
     * @return array{ok:bool,errors:list<string>,repaired:array<string,mixed>}
     */
    public static function validateDirection(array $direction): array
    {
        $errors = [];
        $repaired = $direction;
        $type = (string) ($direction['type'] ?? '');
        if (!in_array($type, self::directionTypes(), true)) {
            $errors[] = 'invalid_direction_type';
        }
        foreach (['id', 'title', 'user_summary', 'scene_premise', 'emotional_focus'] as $field) {
            if (trim((string) ($direction[$field] ?? '')) === '') {
                $errors[] = 'direction_missing_' . $field;
            }
        }
        if (!is_array($direction['dna_element_ids'] ?? null)) {
            $errors[] = 'direction_missing_dna_element_ids';
            $repaired['dna_element_ids'] = [];
        }
        $suitability = (string) ($direction['portrait_suitability'] ?? '');
        if (!in_array($suitability, self::portraitSuitabilityLevels(), true)) {
            $errors[] = 'direction_invalid_portrait_suitability';
            $repaired['portrait_suitability'] = 'medium';
        }
        foreach (['visual_distinctiveness', 'narrative_coherence', 'song_dna_fidelity', 'overall_rank'] as $scoreField) {
            $score = (float) ($direction[$scoreField] ?? -1);
            if ($score < 0 || $score > 1) {
                $errors[] = 'direction_invalid_' . $scoreField;
                $repaired[$scoreField] = max(0.0, min(1.0, $score < 0 ? 0.5 : $score));
            }
        }
        return ['ok' => $errors === [], 'errors' => $errors, 'repaired' => self::sanitizeDirection($repaired)];
    }

    /**
     * @param array<string, mixed> $contract
     * @return array{ok:bool,errors:list<string>,repaired:array<string,mixed>}
     */
    public static function validateSceneContract(array $contract): array
    {
        $errors = [];
        $repaired = $contract;
        if (($contract['version'] ?? '') !== self::SCENE_VERSION) {
            $errors[] = 'invalid_scene_version';
            $repaired['version'] = self::SCENE_VERSION;
        }
        $required = [
            'pov_owner', 'viewer_relationship', 'decisive_instant', 'environment',
            'subject_roles', 'visible_action', 'relationship_geometry', 'primary_symbol',
            'secondary_detail', 'offscreen_tension', 'camera_position', 'shot_scale',
            'lens_behavior', 'composition_hierarchy', 'lighting_logic', 'color_logic',
            'atmosphere', 'motion_state', 'texture', 'ambiguity_to_preserve',
            'portrait_integration_plan', 'negative_constraints',
        ];
        foreach ($required as $field) {
            if (in_array($field, ['subject_roles', 'composition_hierarchy', 'negative_constraints'], true)) {
                if (!is_array($contract[$field] ?? null) || $contract[$field] === []) {
                    $errors[] = 'scene_missing_' . $field;
                }
                continue;
            }
            if (trim((string) ($contract[$field] ?? '')) === '') {
                $errors[] = 'scene_missing_' . $field;
            }
        }
        $relationship = (string) ($contract['viewer_relationship'] ?? '');
        if (!in_array($relationship, self::viewerRelationships(), true)) {
            $errors[] = 'scene_invalid_viewer_relationship';
            $repaired['viewer_relationship'] = 'witness';
        }
        return ['ok' => $errors === [], 'errors' => $errors, 'repaired' => self::sanitizeSceneContract($repaired)];
    }

    /** @param array<string, mixed> $board */
    public static function sanitizeBoard(array $board): array
    {
        return [
            'version' => self::BOARD_VERSION,
            'song_dna_basis' => self::stringList($board['song_dna_basis'] ?? [], 12, 120),
            'emotional_pivot' => self::clip((string) ($board['emotional_pivot'] ?? ''), 200),
            'character_roles' => self::stringList($board['character_roles'] ?? [], 6, 80),
            'relationship_dynamic' => self::clip((string) ($board['relationship_dynamic'] ?? ''), 200),
            'candidate_environments' => self::stringList($board['candidate_environments'] ?? [], 6, 120),
            'symbolic_artifacts' => self::stringList($board['symbolic_artifacts'] ?? [], 6, 120),
            'physical_actions' => self::stringList($board['physical_actions'] ?? [], 6, 120),
            'visual_escalation' => self::clip((string) ($board['visual_escalation'] ?? ''), 200),
            'recurring_motifs' => self::stringList($board['recurring_motifs'] ?? [], 5, 100),
            'ambiguities_to_preserve' => self::stringList($board['ambiguities_to_preserve'] ?? [], 4, 120),
            'literal_or_unsafe_interpretations_to_avoid' => self::stringList($board['literal_or_unsafe_interpretations_to_avoid'] ?? [], 6, 120),
            'portrait_opportunities' => self::stringList($board['portrait_opportunities'] ?? [], 4, 120),
            'confidence' => max(0.0, min(1.0, (float) ($board['confidence'] ?? 0.5))),
        ];
    }

    /** @param array<string, mixed> $direction */
    public static function sanitizeDirection(array $direction): array
    {
        return [
            'id' => self::clip((string) ($direction['id'] ?? 'direction'), 40),
            'type' => in_array((string) ($direction['type'] ?? ''), self::directionTypes(), true)
                ? (string) $direction['type'] : 'primary',
            'title' => self::clip((string) ($direction['title'] ?? ''), 120),
            'user_summary' => self::clip((string) ($direction['user_summary'] ?? ''), 240),
            'scene_premise' => self::clip((string) ($direction['scene_premise'] ?? ''), 400),
            'emotional_focus' => self::clip((string) ($direction['emotional_focus'] ?? ''), 160),
            'dna_element_ids' => self::stringList($direction['dna_element_ids'] ?? [], 8, 60),
            'portrait_suitability' => in_array((string) ($direction['portrait_suitability'] ?? ''), self::portraitSuitabilityLevels(), true)
                ? (string) $direction['portrait_suitability'] : 'medium',
            'visual_distinctiveness' => max(0.0, min(1.0, (float) ($direction['visual_distinctiveness'] ?? 0.5))),
            'narrative_coherence' => max(0.0, min(1.0, (float) ($direction['narrative_coherence'] ?? 0.5))),
            'song_dna_fidelity' => max(0.0, min(1.0, (float) ($direction['song_dna_fidelity'] ?? 0.5))),
            'overall_rank' => max(0.0, min(1.0, (float) ($direction['overall_rank'] ?? 0.5))),
        ];
    }

    /** @param array<string, mixed> $contract */
    public static function sanitizeSceneContract(array $contract): array
    {
        return [
            'version' => self::SCENE_VERSION,
            'pov_owner' => self::clip((string) ($contract['pov_owner'] ?? ''), 120),
            'viewer_relationship' => in_array((string) ($contract['viewer_relationship'] ?? ''), self::viewerRelationships(), true)
                ? (string) $contract['viewer_relationship'] : 'witness',
            'decisive_instant' => self::clip((string) ($contract['decisive_instant'] ?? ''), 400),
            'environment' => self::clip((string) ($contract['environment'] ?? ''), 300),
            'subject_roles' => self::stringList($contract['subject_roles'] ?? [], 4, 100),
            'visible_action' => self::clip((string) ($contract['visible_action'] ?? ''), 300),
            'relationship_geometry' => self::clip((string) ($contract['relationship_geometry'] ?? ''), 200),
            'primary_symbol' => self::clip((string) ($contract['primary_symbol'] ?? ''), 200),
            'secondary_detail' => self::clip((string) ($contract['secondary_detail'] ?? ''), 200),
            'offscreen_tension' => self::clip((string) ($contract['offscreen_tension'] ?? ''), 200),
            'camera_position' => self::clip((string) ($contract['camera_position'] ?? ''), 160),
            'shot_scale' => self::clip((string) ($contract['shot_scale'] ?? ''), 120),
            'lens_behavior' => self::clip((string) ($contract['lens_behavior'] ?? ''), 160),
            'composition_hierarchy' => self::stringList($contract['composition_hierarchy'] ?? [], 5, 120),
            'lighting_logic' => self::clip((string) ($contract['lighting_logic'] ?? ''), 200),
            'color_logic' => self::clip((string) ($contract['color_logic'] ?? ''), 200),
            'atmosphere' => self::clip((string) ($contract['atmosphere'] ?? ''), 200),
            'motion_state' => self::clip((string) ($contract['motion_state'] ?? ''), 160),
            'texture' => self::clip((string) ($contract['texture'] ?? ''), 160),
            'ambiguity_to_preserve' => self::clip((string) ($contract['ambiguity_to_preserve'] ?? ''), 200),
            'portrait_integration_plan' => self::clip((string) ($contract['portrait_integration_plan'] ?? ''), 300),
            'negative_constraints' => self::stringList($contract['negative_constraints'] ?? [], 8, 120),
        ];
    }

    /** @return list<string> */
    public static function stringList(mixed $value, int $maxItems, int $maxLen): array
    {
        if (!is_array($value)) {
            return [];
        }
        $out = [];
        foreach (array_slice($value, 0, $maxItems) as $item) {
            $text = self::clip(is_scalar($item) ? (string) $item : '', $maxLen);
            if ($text !== '') {
                $out[] = $text;
            }
        }
        return array_values(array_unique($out));
    }

    public static function clip(string $value, int $max): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';
        return substr(trim($value), 0, $max);
    }
}
