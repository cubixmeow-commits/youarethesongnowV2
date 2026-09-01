<?php

declare(strict_types=1);

/**
 * Recorded structured planning response for offline Gemini planner tests.
 *
 * @return array<string, mixed>
 */
function visual_narrative_model_response_fixture(): array
{
    return [
        'board' => [
            'version' => 'visual-board-v1',
            'song_dna_basis' => ['kinetic adventure', 'freedom', 'lantern rise'],
            'emotional_pivot' => 'weightlessness',
            'character_roles' => ['daredevil', 'co-pilot'],
            'relationship_dynamic' => 'companions in motion',
            'candidate_environments' => ['rooftop cityscape', 'dusk skyline'],
            'symbolic_artifacts' => ['lantern: rising paper lights against dusk'],
            'physical_actions' => ['launching over the rooftop gap'],
            'visual_escalation' => 'rush then lift then glide',
            'recurring_motifs' => ['height as possibility'],
            'ambiguities_to_preserve' => [],
            'literal_or_unsafe_interpretations_to_avoid' => ['lyric quotation'],
            'portrait_opportunities' => ['shared airborne courage'],
            'confidence' => 0.9,
        ],
        'directions' => [
            [
                'id' => 'dir-primary',
                'type' => 'primary',
                'title' => 'Launch over the rooftop gap',
                'user_summary' => 'Both friends hit the air above the avenue as lanterns rise behind them.',
                'scene_premise' => 'Two friends launch over a rooftop gap on battered boards as paper lanterns rise behind them and the avenue blurs below.',
                'emotional_focus' => 'weightlessness',
                'dna_element_ids' => ['kinetic adventure', 'freedom'],
                'portrait_suitability' => 'high',
                'viewpoint' => 'participant',
                'relationship_emphasis' => 'shared airborne courage',
                'symbol_strategy' => 'lanterns crown the decisive instant',
                'score_hints' => [
                    'song_dna_fidelity' => 0.86,
                    'narrative_coherence' => 0.84,
                    'visual_distinctiveness' => 0.72,
                    'portrait_suitability' => 0.9,
                    'information_budget' => 0.8,
                ],
            ],
            [
                'id' => 'dir-alternate',
                'type' => 'alternate',
                'title' => 'Lanterns lift the skyline',
                'user_summary' => 'The city glow recedes while lanterns become the emotional center.',
                'scene_premise' => 'From the rooftop edge, both riders watch lanterns peel upward through warm dusk while the street below becomes a river of light.',
                'emotional_focus' => 'joyful drift',
                'dna_element_ids' => ['discovery', 'youth'],
                'portrait_suitability' => 'medium',
                'viewpoint' => 'witness',
                'relationship_emphasis' => 'shared wonder at the rising lights',
                'symbol_strategy' => 'lanterns dominate the frame',
                'score_hints' => [
                    'song_dna_fidelity' => 0.8,
                    'narrative_coherence' => 0.86,
                    'visual_distinctiveness' => 0.88,
                    'portrait_suitability' => 0.74,
                    'information_budget' => 0.92,
                ],
            ],
            [
                'id' => 'dir-unexpected',
                'type' => 'unexpected',
                'title' => 'Height as possibility made visible',
                'user_summary' => 'The open gap becomes a luminous threshold rather than a stunt.',
                'scene_premise' => 'Suspended above the avenue, the rooftop gap glows like a portal while lantern light turns vertical drop into invitation.',
                'emotional_focus' => 'height as possibility',
                'dna_element_ids' => ['freedom', 'height as possibility'],
                'portrait_suitability' => 'medium',
                'viewpoint' => 'observer',
                'relationship_emphasis' => 'symbolic threshold between fear and lift',
                'symbol_strategy' => 'gap and lantern merge into one luminous symbol',
                'score_hints' => [
                    'song_dna_fidelity' => 0.93,
                    'narrative_coherence' => 0.9,
                    'visual_distinctiveness' => 0.95,
                    'portrait_suitability' => 0.7,
                    'information_budget' => 0.94,
                ],
            ],
        ],
        'scene_contract' => [
            'version' => 'visual-scene-v1',
            'pov_owner' => 'the two protagonists',
            'viewer_relationship' => 'observer',
            'decisive_instant' => 'Suspended above the avenue, the rooftop gap glows like a portal while lantern light turns vertical drop into invitation.',
            'environment' => 'rooftop cityscape, dusk skyline, vertical drop',
            'subject_roles' => ['daredevil', 'co-pilot'],
            'visible_action' => 'hovering at the luminous threshold',
            'relationship_geometry' => 'companions in motion at the edge',
            'primary_symbol' => 'lantern: rising paper lights against dusk',
            'secondary_detail' => 'gap: open air between rooftops',
            'offscreen_tension' => 'the street far below still rushing',
            'camera_position' => 'wide action, slight dutch tilt',
            'shot_scale' => 'subjects mid-air over negative space',
            'lens_behavior' => 'natural perspective with gentle depth falloff',
            'composition_hierarchy' => ['protagonist faces readable', 'luminous gap', 'lantern field'],
            'lighting_logic' => 'backlit lanterns; city glow below',
            'color_logic' => 'amber dusk; cyan shadow',
            'atmosphere' => 'reckless, joyful, kinetic, warm updraft',
            'motion_state' => 'airborne; lantern rise',
            'texture' => 'scuffed deck; paper light',
            'ambiguity_to_preserve' => 'leave one relational question unanswered',
            'portrait_integration_plan' => 'Both uploaded people remain readable co-protagonists at the threshold.',
            'negative_constraints' => ['no lyric text', 'no logos'],
        ],
    ];
}
