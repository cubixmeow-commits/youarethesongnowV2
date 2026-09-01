<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine\VisualNarrative;

use Yatsn\Support\Config;

/**
 * Orchestrates Visual Narrative Planning and safe fallback to the legacy compiler.
 */
final class VisualNarrativePlanningService
{
    public static function isEnabled(): bool
    {
        if (Config::getBool('ai.visual_narrative_legacy_compiler', false)) {
            return false;
        }
        return Config::getBool('ai.visual_narrative_planning_enabled', true);
    }

    /**
     * @param array<string, mixed> $package
     * @param array<string, mixed> $snapshot
     * @return array<string, mixed>
     */
    public static function applyToPackage(array $package, array $snapshot): array
    {
        if (!self::isEnabled()) {
            $package['visualPlanning'] = self::disabledTrace('planning_disabled_by_config');
            return $package;
        }

        $dna = is_array($package['dna'] ?? null) ? $package['dna'] : [];
        if ($dna === []) {
            $package['visualPlanning'] = self::fallbackTrace('missing_dna', null, $package);
            return $package;
        }

        try {
            $result = VisualNarrativePlanner::plan($dna, $snapshot);
            $board = $result['board'];
            $directions = $result['directions'];
            $selected = $result['selectedDirection'];
            $contract = $result['sceneContract'];

            $boardValidation = VisualNarrativeContracts::validateBoard($board);
            $contractValidation = VisualNarrativeContracts::validateSceneContract($contract);
            if (!$boardValidation['ok'] && !$contractValidation['ok']) {
                throw new \RuntimeException('planning_contract_validation_failed');
            }
            if (!$boardValidation['ok']) {
                $board = $boardValidation['repaired'];
            }
            if (!$contractValidation['ok']) {
                $contract = $contractValidation['repaired'];
            }

            $legacyPrompt = (string) ($package['compiledPromptSafe'] ?? '');
            $newPrompt = StructuredPromptCompiler::compile($contract, $package, $snapshot);

            $package['compiledPromptSafe'] = $newPrompt;
            $package['narrative'] = [
                'moment' => (string) ($contract['decisive_instant'] ?? ($package['narrative']['moment'] ?? '')),
                'styleLead' => (string) ($package['narrative']['styleLead'] ?? ($snapshot['styleName'] ?? '')),
                'orientation' => (string) ($package['narrative']['orientation'] ?? ($snapshot['orientation'] ?? 'square')),
                'sceneContractVersion' => VisualNarrativeContracts::SCENE_VERSION,
                'selectedDirectionId' => (string) ($selected['id'] ?? ''),
                'selectedDirectionType' => (string) ($selected['type'] ?? 'primary'),
            ];
            $package['visualSceneContract'] = $contract;
            $package['visualPlanning'] = self::successTrace($board, $directions, $selected, $contract, $legacyPrompt, $newPrompt);
            return $package;
        } catch (\Throwable $e) {
            $package['visualPlanning'] = self::fallbackTrace($e->getMessage(), null, $package);
            return $package;
        }
    }

    /**
     * Sanitized trace safe for persistence — no portrait bytes, no raw lyrics.
     *
     * @return array<string, mixed>
     */
    public static function sanitizedTrace(array $package): array
    {
        $trace = is_array($package['visualPlanning'] ?? null) ? $package['visualPlanning'] : [];
        unset($trace['legacyPrompt'], $trace['newPrompt']);
        return $trace;
    }

    /**
     * @param list<array<string,mixed>> $directions
     * @return array<string, mixed>
     */
    private static function successTrace(
        array $board,
        array $directions,
        array $selected,
        array $contract,
        string $legacyPrompt,
        string $newPrompt
    ): array {
        return [
            'version' => VisualNarrativeContracts::TRACE_VERSION,
            'status' => 'success',
            'compilerVersion' => VisualNarrativeContracts::COMPILER_VERSION,
            'boardVersion' => VisualNarrativeContracts::BOARD_VERSION,
            'sceneContractVersion' => VisualNarrativeContracts::SCENE_VERSION,
            'board' => self::sanitizeBoardForTrace($board),
            'directions' => array_map(self::sanitizeDirectionForTrace(...), $directions),
            'selectedDirectionId' => (string) ($selected['id'] ?? ''),
            'selectedDirectionType' => (string) ($selected['type'] ?? 'primary'),
            'selectedRank' => (float) ($selected['overall_rank'] ?? 0),
            'sceneContractSummary' => [
                'decisive_instant' => (string) ($contract['decisive_instant'] ?? ''),
                'environment' => (string) ($contract['environment'] ?? ''),
                'primary_symbol' => (string) ($contract['primary_symbol'] ?? ''),
                'viewer_relationship' => (string) ($contract['viewer_relationship'] ?? ''),
            ],
            'promptComparison' => [
                'legacyLength' => strlen($legacyPrompt),
                'newLength' => strlen($newPrompt),
                'legacyCompiler' => 'creative-package-v1',
                'newCompiler' => VisualNarrativeContracts::COMPILER_VERSION,
            ],
            'legacyPrompt' => $legacyPrompt,
            'newPrompt' => $newPrompt,
            'fallback' => false,
            'fallbackReason' => null,
            'failureClass' => null,
        ];
    }

    /** @return array<string, mixed> */
    private static function fallbackTrace(string $reason, ?\Throwable $e, array $package): array
    {
        return [
            'version' => VisualNarrativeContracts::TRACE_VERSION,
            'status' => 'fallback',
            'compilerVersion' => 'creative-package-v1',
            'boardVersion' => null,
            'sceneContractVersion' => null,
            'board' => null,
            'directions' => null,
            'selectedDirectionId' => null,
            'selectedDirectionType' => null,
            'selectedRank' => null,
            'sceneContractSummary' => null,
            'promptComparison' => [
                'legacyLength' => strlen((string) ($package['compiledPromptSafe'] ?? '')),
                'newLength' => null,
                'legacyCompiler' => 'creative-package-v1',
                'newCompiler' => null,
            ],
            'legacyPrompt' => (string) ($package['compiledPromptSafe'] ?? ''),
            'newPrompt' => null,
            'fallback' => true,
            'fallbackReason' => VisualNarrativeContracts::clip($reason, 120),
            'failureClass' => self::classifyFailure($reason),
        ];
    }

    /** @return array<string, mixed> */
    private static function disabledTrace(string $reason): array
    {
        return [
            'version' => VisualNarrativeContracts::TRACE_VERSION,
            'status' => 'disabled',
            'fallback' => true,
            'fallbackReason' => $reason,
            'failureClass' => 'config_disabled',
        ];
    }

    /** @param array<string, mixed> $board */
    private static function sanitizeBoardForTrace(array $board): array
    {
        return [
            'version' => $board['version'] ?? VisualNarrativeContracts::BOARD_VERSION,
            'emotional_pivot' => (string) ($board['emotional_pivot'] ?? ''),
            'relationship_dynamic' => (string) ($board['relationship_dynamic'] ?? ''),
            'candidate_environments' => VisualNarrativeContracts::stringList($board['candidate_environments'] ?? [], 4, 80),
            'symbolic_artifacts' => VisualNarrativeContracts::stringList($board['symbolic_artifacts'] ?? [], 3, 80),
            'confidence' => (float) ($board['confidence'] ?? 0),
        ];
    }

    /** @param array<string, mixed> $direction */
    private static function sanitizeDirectionForTrace(array $direction): array
    {
        return [
            'id' => (string) ($direction['id'] ?? ''),
            'type' => (string) ($direction['type'] ?? ''),
            'title' => (string) ($direction['title'] ?? ''),
            'user_summary' => (string) ($direction['user_summary'] ?? ''),
            'overall_rank' => (float) ($direction['overall_rank'] ?? 0),
            'song_dna_fidelity' => (float) ($direction['song_dna_fidelity'] ?? 0),
            'narrative_coherence' => (float) ($direction['narrative_coherence'] ?? 0),
            'visual_distinctiveness' => (float) ($direction['visual_distinctiveness'] ?? 0),
            'portrait_suitability' => (string) ($direction['portrait_suitability'] ?? ''),
        ];
    }

    private static function classifyFailure(string $reason): string
    {
        $lower = strtolower($reason);
        if (str_contains($lower, 'validation')) {
            return 'scene_incoherence';
        }
        if (str_contains($lower, 'missing_dna')) {
            return 'missing_dna';
        }
        if (str_contains($lower, 'disabled')) {
            return 'config_disabled';
        }
        return 'planning_failure';
    }
}
