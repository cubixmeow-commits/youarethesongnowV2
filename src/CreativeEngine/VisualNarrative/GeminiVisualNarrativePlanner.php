<?php

declare(strict_types=1);

namespace Yatsn\CreativeEngine\VisualNarrative;

use Yatsn\AI\ProviderHttp;
use Yatsn\Support\Config;

/**
 * Structured Gemini planning pass for Visual Narrative artifacts.
 * Portrait bytes and raw lyrics never enter the request payload.
 */
final class GeminiVisualNarrativePlanner
{
    public const PROMPT_TEMPLATE_VERSION = 'visual-planning-prompt-v1';

    /** @var null|callable(string,array,array,int):array */
    private static $transport = null;

    private static string $lastDiagnostic = '';

    /** @param null|callable(string,array,array,int):array $transport */
    public static function setTransport(?callable $transport): void
    {
        self::$transport = $transport;
    }

    public static function lastDiagnostic(): string
    {
        return self::$lastDiagnostic;
    }

    public static function isAvailable(): bool
    {
        return self::availabilityFailure() === null;
    }

    public static function availabilityFailure(): ?string
    {
        if (!Config::getBool('gates.ai_providers_enabled')) {
            return 'config-ai-providers-disabled';
        }
        if (!Config::getBool('ai.visual_narrative_planning_live_calls')) {
            return 'config-visual-planning-live-calls-disabled';
        }
        if (!Config::getBool('ai.gemini_live_calls')) {
            return 'config-gemini-live-calls-disabled';
        }
        if ((string) Config::get('ai.gemini_api_key') === '') {
            return 'config-gemini-api-key-missing';
        }
        return null;
    }

    public static function resolveModel(): string
    {
        $override = trim((string) Config::get('ai.gemini_visual_planning_model', ''));
        if ($override !== '') {
            if (!preg_match('/^[A-Za-z0-9._-]+$/', $override)) {
                throw new \RuntimeException('gemini_model_invalid');
            }
            return $override;
        }
        $model = trim((string) Config::get('ai.gemini_model', 'gemini-3.6-flash'));
        if ($model === '' || !preg_match('/^[A-Za-z0-9._-]+$/', $model)) {
            throw new \RuntimeException('gemini_model_invalid');
        }
        return $model;
    }

    /**
     * @param array<string, mixed> $dna
     * @param array<string, mixed> $snapshot
     * @return array{
     *   board: array<string,mixed>,
     *   directions: list<array<string,mixed>>,
     *   selectedDirection: array<string,mixed>,
     *   sceneContract: array<string,mixed>,
     *   plannerSource: string
     * }
     */
    public static function plan(array $dna, array $snapshot): array
    {
        $failure = self::availabilityFailure();
        if ($failure !== null) {
            self::$lastDiagnostic = $failure;
            throw new \RuntimeException('gemini_visual_planning_unavailable');
        }

        $model = self::resolveModel();
        $safeDna = self::safeDna($dna);
        $portraitCount = max(1, min(2, (int) ($snapshot['portraitCount'] ?? 1)));
        $payload = self::requestPayload($safeDna, $portraitCount, $model);
        $headers = ['x-goog-api-key: ' . (string) Config::get('ai.gemini_api_key')];
        $timeout = Config::getInt('ai.text_timeout_seconds', 45);
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent';

        $response = self::$transport !== null
            ? (self::$transport)($url, $payload, $headers, $timeout)
            : ProviderHttp::postJson($url, $payload, $headers, $timeout);

        $decoded = self::decodeResponse($response);
        $parsed = self::parsePlanningPayload($decoded, $dna, $snapshot, $portraitCount);
        $parsed['plannerSource'] = 'gemini:' . $model . ':' . self::PROMPT_TEMPLATE_VERSION;
        return $parsed;
    }

    /** @param array<string, mixed> $dna */
    public static function safeDna(array $dna): array
    {
        $copy = $dna;
        foreach (['lyrics', 'rawLyrics', 'lyricText', 'portraitBytes', 'portraitData', 'matchedLyrics'] as $field) {
            unset($copy[$field]);
        }
        return $copy;
    }

  /**
     * @param array<string, mixed> $safeDna
     * @return array<string, mixed>
     */
    public static function requestPayload(array $safeDna, int $portraitCount, string $model): array
    {
        return [
            'systemInstruction' => ['parts' => [['text' => self::systemPrompt()]]],
            'contents' => [['role' => 'user', 'parts' => [['text' => self::userPrompt($safeDna, $portraitCount)]]]],
            'generationConfig' => [
                'temperature' => 0.55,
                'maxOutputTokens' => 3200,
                'responseFormat' => [
                    'text' => [
                        'mimeType' => 'application/json',
                        'schema' => self::responseSchema(),
                    ],
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function responseSchema(): array
    {
        $direction = [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => [
                'id', 'type', 'title', 'user_summary', 'scene_premise', 'emotional_focus',
                'dna_element_ids', 'portrait_suitability', 'viewpoint', 'relationship_emphasis', 'symbol_strategy',
                'score_hints',
            ],
            'properties' => [
                'id' => ['type' => 'string'],
                'type' => ['type' => 'string', 'enum' => ['primary', 'alternate', 'unexpected']],
                'title' => ['type' => 'string'],
                'user_summary' => ['type' => 'string'],
                'scene_premise' => ['type' => 'string'],
                'emotional_focus' => ['type' => 'string'],
                'dna_element_ids' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 8],
                'portrait_suitability' => ['type' => 'string', 'enum' => ['high', 'medium', 'low']],
                'viewpoint' => ['type' => 'string'],
                'relationship_emphasis' => ['type' => 'string'],
                'symbol_strategy' => ['type' => 'string'],
                'score_hints' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['song_dna_fidelity', 'narrative_coherence', 'visual_distinctiveness', 'portrait_suitability', 'information_budget'],
                    'properties' => [
                        'song_dna_fidelity' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'narrative_coherence' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'visual_distinctiveness' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'portrait_suitability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'information_budget' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                    ],
                ],
            ],
        ];

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['board', 'directions', 'scene_contract'],
            'properties' => [
                'board' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => [
                        'version', 'song_dna_basis', 'emotional_pivot', 'character_roles', 'relationship_dynamic',
                        'candidate_environments', 'symbolic_artifacts', 'physical_actions', 'visual_escalation',
                        'recurring_motifs', 'ambiguities_to_preserve', 'literal_or_unsafe_interpretations_to_avoid',
                        'portrait_opportunities', 'confidence',
                    ],
                    'properties' => [
                        'version' => ['type' => 'string'],
                        'song_dna_basis' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 12],
                        'emotional_pivot' => ['type' => 'string'],
                        'character_roles' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 6],
                        'relationship_dynamic' => ['type' => 'string'],
                        'candidate_environments' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 6],
                        'symbolic_artifacts' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 6],
                        'physical_actions' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 6],
                        'visual_escalation' => ['type' => 'string'],
                        'recurring_motifs' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 5],
                        'ambiguities_to_preserve' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 4],
                        'literal_or_unsafe_interpretations_to_avoid' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 6],
                        'portrait_opportunities' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 4],
                        'confidence' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                    ],
                ],
                'directions' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 3,
                    'items' => $direction,
                ],
                'scene_contract' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => [
                        'version', 'pov_owner', 'viewer_relationship', 'decisive_instant', 'environment',
                        'subject_roles', 'visible_action', 'relationship_geometry', 'primary_symbol',
                        'secondary_detail', 'offscreen_tension', 'camera_position', 'shot_scale',
                        'lens_behavior', 'composition_hierarchy', 'lighting_logic', 'color_logic',
                        'atmosphere', 'motion_state', 'texture', 'ambiguity_to_preserve',
                        'portrait_integration_plan', 'negative_constraints',
                    ],
                    'properties' => [
                        'version' => ['type' => 'string'],
                        'pov_owner' => ['type' => 'string'],
                        'viewer_relationship' => ['type' => 'string', 'enum' => ['participant', 'witness', 'observer']],
                        'decisive_instant' => ['type' => 'string'],
                        'environment' => ['type' => 'string'],
                        'subject_roles' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 4],
                        'visible_action' => ['type' => 'string'],
                        'relationship_geometry' => ['type' => 'string'],
                        'primary_symbol' => ['type' => 'string'],
                        'secondary_detail' => ['type' => 'string'],
                        'offscreen_tension' => ['type' => 'string'],
                        'camera_position' => ['type' => 'string'],
                        'shot_scale' => ['type' => 'string'],
                        'lens_behavior' => ['type' => 'string'],
                        'composition_hierarchy' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 5],
                        'lighting_logic' => ['type' => 'string'],
                        'color_logic' => ['type' => 'string'],
                        'atmosphere' => ['type' => 'string'],
                        'motion_state' => ['type' => 'string'],
                        'texture' => ['type' => 'string'],
                        'ambiguity_to_preserve' => ['type' => 'string'],
                        'portrait_integration_plan' => ['type' => 'string'],
                        'negative_constraints' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 8],
                    ],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $response @return array<string, mixed> */
    public static function decodeResponse(array $response): array
    {
        $finish = (string) ($response['candidates'][0]['finishReason'] ?? '');
        if ($finish !== '' && !in_array($finish, ['STOP', 'MAX_TOKENS'], true)) {
            throw new \RuntimeException('gemini_visual_planning_blocked');
        }
        $parts = $response['candidates'][0]['content']['parts'] ?? [];
        $text = '';
        if (is_array($parts)) {
            foreach ($parts as $part) {
                if (is_array($part) && isset($part['text'])) {
                    $text .= (string) $part['text'];
                }
            }
        }
        $decoded = json_decode(trim($text), true);
        if (!is_array($decoded)) {
            $decoded = self::recoverJson($text);
        }
        if (!is_array($decoded)) {
            throw new \RuntimeException('gemini_visual_planning_invalid_json');
        }
        return $decoded;
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $dna
     * @param array<string, mixed> $snapshot
     * @return array{board:array<string,mixed>,directions:list<array<string,mixed>>,selectedDirection:array<string,mixed>,sceneContract:array<string,mixed>}
     */
    public static function parsePlanningPayload(array $payload, array $dna, array $snapshot, int $portraitCount): array
    {
        $board = VisualNarrativeContracts::sanitizeBoard(is_array($payload['board'] ?? null) ? $payload['board'] : []);
        $boardValidation = VisualNarrativeContracts::validateBoard($board);
        if (!$boardValidation['ok']) {
            $board = $boardValidation['repaired'];
        }

        $directions = [];
        foreach (is_array($payload['directions'] ?? null) ? $payload['directions'] : [] as $direction) {
            if (!is_array($direction)) {
                continue;
            }
            $directions[] = $direction;
        }
        if (count($directions) !== 3) {
            throw new \RuntimeException('gemini_visual_planning_incomplete_directions');
        }

        $ranked = DirectionRanker::rank($directions, $dna, $portraitCount);
        $selected = $ranked[0];
        $sceneContract = is_array($payload['scene_contract'] ?? null) ? $payload['scene_contract'] : [];
        $contract = VisualNarrativeContracts::sanitizeSceneContract($sceneContract);
        $contractValidation = VisualNarrativeContracts::validateSceneContract($contract);
        if (!$contractValidation['ok'] || (string) ($contract['decisive_instant'] ?? '') === '') {
            $contract = VisualNarrativePlanner::compileSceneContract($dna, $board, $selected, $portraitCount);
        } else {
            $contract = $contractValidation['repaired'];
            $contract['decisive_instant'] = (string) ($selected['scene_premise'] ?? $contract['decisive_instant']);
            $contract['portrait_integration_plan'] = VisualNarrativePlanner::portraitIntegrationPlan($portraitCount, $selected);
        }

        return [
            'board' => $board,
            'directions' => $ranked,
            'selectedDirection' => $selected,
            'sceneContract' => $contract,
        ];
    }

    /** @param array<string, mixed> $safeDna */
    private static function userPrompt(array $safeDna, int $portraitCount): string
    {
        return implode("\n", [
            'Create a visual narrative plan from the approved internal Song DNA below.',
            'Portrait protagonists: ' . $portraitCount,
            'Return strict JSON only.',
            'Directions must be song-specific with distinct titles, summaries, premises, viewpoints, and symbolic strategies.',
            'Do not use generic template titles such as "After the turn" or "Symbolic reframing".',
            'Score hints must reflect actual content quality, not direction type labels.',
            'Enforce the information budget: one decisive instant, one dominant relationship, one environment, one primary symbol, at most two supporting details.',
            'SONG DNA JSON:',
            json_encode($safeDna, JSON_THROW_ON_ERROR),
        ]);
    }

    private static function systemPrompt(): string
    {
        return implode("\n", [
            'You are the Visual Narrative Planning stage for You Are The Song Now.',
            'Invent one physical cinematic world per direction grounded in the supplied Song DNA.',
            'Never quote lyrics, never request portrait images, never imitate performers or album art.',
            'Produce exactly three materially distinct directions labeled primary, alternate, and unexpected as creative roles only.',
            'The highest score should reflect the strongest scene for this song, not the primary label.',
            'Template version: ' . self::PROMPT_TEMPLATE_VERSION,
        ]);
    }

    /** @return null|array<string, mixed> */
    private static function recoverJson(string $text): ?array
    {
        if (preg_match('/```json\s*(\{.*\})\s*```/s', $text, $matches) === 1) {
            $decoded = json_decode($matches[1], true);
            return is_array($decoded) ? $decoded : null;
        }
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }
        $decoded = json_decode(substr($text, $start, $end - $start + 1), true);
        return is_array($decoded) ? $decoded : null;
    }
}
