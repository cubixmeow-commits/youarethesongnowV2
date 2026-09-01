# Visual Narrative Planning Layer — Product and Implementation Plan

**Project:** YouAreTheSongNow V2  
**Status:** Approved for backend-first implementation before further design work  
**Reference:** https://github.com/cubixmeow-commits/POV-Campaign-Engine  
**Design principle:** AI removes decisions by default and offers intelligent choices when the user asks for control.

## Purpose

Adapt selected reasoning mechanisms from the POV Campaign Engine to improve YouAreTheSongNow image specificity, narrative coherence, composition, symbolism, portrait roles, and controlled retries.

Do not copy Campaign Engine text formats, real-world verification rules, investigative structures, chronological reporting requirements, or observable-only restrictions.

## Relationship to the current roadmap

The published V2 baseline has completed Luminous Night Studio Phase 2: Create entry and song selection.

Owner sequencing decision: implement and validate the POV-derived visual planning engine before continuing the rest of the customer-facing design.

Backend-first order:

1. audit and reuse the richest existing internal Song DNA representation;
2. define versioned internal contracts;
3. implement Visual Campaign Board generation;
4. implement exactly three Song-DNA-specific scene directions and ranking;
5. implement Visual Scene Contract compilation;
6. integrate portrait roles after direction planning;
7. compile the final provider prompt through the structured pipeline;
8. wire the planning layer into the existing development generation path;
9. implement controlled fallbacks, traceability, and evaluation;
10. only after GPT/owner acceptance, design the customer-safe Song DNA selector, Explore Options, and Fine Tune UI.

Do not expose raw internal Song DNA or planning artifacts to customers. No new customer-facing design is required for the backend integration pass.

## Locked UX

Quick Generate is the default.

After the user selects a song and one or more Song DNA elements, Quick Generate automatically determines:

- visual narrative;
- decisive scene;
- character roles;
- environment;
- symbolism;
- camera and composition;
- lighting and palette;
- atmosphere, motion, and texture;
- portrait treatment.

Explore Options exposes exactly three intelligent, song-specific scene directions already produced by the same planning run:

1. primary;
2. alternate;
3. unexpected.

Fine Tune is optional and reveals only controls relevant to the selected direction.

Directions are scenes, not style presets.

## Reference mechanisms to adapt

Cursor should inspect these files in the POV Campaign Engine repository:

- `SKILL.md`
- `references/board-components.md`
- `references/campaign-design.md`
- `references/scene-rules.md`
- `examples/crowdstrike-outage/campaign.md`
- `examples/crowdstrike-outage/scenes.md`

Adapt:

- staged structural extraction;
- information budgeting;
- viewpoint selection;
- structural pivots;
- scene contracts;
- recurring motifs/callbacks;
- medium-specific final assembly;
- retained intermediate artifacts for controlled revision.

## Target pipeline

```text
Song selection
→ customer-safe Song DNA
→ selected Song DNA elements
→ Visual Campaign Board
→ three candidate scene directions
→ selected direction
→ Visual Scene Contract
→ portrait integration
→ structured prompt compiler
→ existing generation system
→ controlled evaluation/retry
```

## Visual Campaign Board

Create a validated, versioned internal contract equivalent to:

```json
{
  "version": "visual-board-v1",
  "song_dna_basis": [],
  "emotional_pivot": "",
  "character_roles": [],
  "relationship_dynamic": "",
  "candidate_environments": [],
  "symbolic_artifacts": [],
  "physical_actions": [],
  "visual_escalation": "",
  "recurring_motifs": [],
  "ambiguities_to_preserve": [],
  "literal_or_unsafe_interpretations_to_avoid": [],
  "portrait_opportunities": [],
  "confidence": 0.0
}
```

Requirements:

- Ground fields in selected Song DNA.
- Preserve genuine ambiguity.
- Prefer physical scenes over abstract adjective collections.
- Do not send portrait image data to board/direction planning.
- Do not expose raw planning JSON, internal scores, portraits, or raw lyrics to customers.
- Treat the board as an intermediate artifact, not the final prompt.

## Candidate scene directions

Produce exactly three materially distinct directions:

```json
{
  "id": "",
  "type": "primary | alternate | unexpected",
  "title": "",
  "user_summary": "",
  "scene_premise": "",
  "emotional_focus": "",
  "dna_element_ids": [],
  "portrait_suitability": "high | medium | low",
  "visual_distinctiveness": 0.0,
  "narrative_coherence": 0.0,
  "song_dna_fidelity": 0.0,
  "overall_rank": 0.0
}
```

- `primary`: strongest overall interpretation; Quick Generate default.
- `alternate`: another valid emotional or relational emphasis.
- `unexpected`: less literal but still strongly grounded.

The directions must differ in premise, viewpoint, relationship emphasis, or symbolic strategy—not merely palette or style.

Quick Generate selects the highest-ranked valid direction. If planning fails, use a deterministic Song-DNA-grounded fallback without stranding the user.

Explore Options reuses these directions rather than generating unrelated presets later.

## Visual Scene Contract

Convert the selected direction into a validated, versioned contract:

```json
{
  "version": "visual-scene-v1",
  "pov_owner": "",
  "viewer_relationship": "participant | witness | observer",
  "decisive_instant": "",
  "environment": "",
  "subject_roles": [],
  "visible_action": "",
  "relationship_geometry": "",
  "primary_symbol": "",
  "secondary_detail": "",
  "offscreen_tension": "",
  "camera_position": "",
  "shot_scale": "",
  "lens_behavior": "",
  "composition_hierarchy": [],
  "lighting_logic": "",
  "color_logic": "",
  "atmosphere": "",
  "motion_state": "",
  "texture": "",
  "ambiguity_to_preserve": "",
  "portrait_integration_plan": "",
  "negative_constraints": []
}
```

Every populated field must serve the scene. Do not add decorative instructions merely to fill the schema.

## Information budget

Default visual budget:

- one decisive instant;
- one dominant subject or relationship;
- one primary environment;
- one primary symbol;
- no more than two supporting symbolic details;
- one readable composition hierarchy;
- one motivated lighting strategy;
- one motivated palette;
- one readable motion state;
- one unresolved or off-screen tension.

Additional detail requires a compositional purpose.

## POV and structural pivot

Adapt Campaign Engine POV selection into camera motivation:

- whose experience owns the frame;
- whether the viewer participates, witnesses, or observes;
- what is visible;
- what remains outside or obscured;
- why the selected distance and angle communicate this song’s decisive instant.

Prefer the emotional or relational pivot: the instant where the Song DNA state changes, the moment immediately before it, or its immediate physical aftermath.

Avoid generic summaries of an entire song when a specific decisive instant is available.

## Recurring motifs

Callbacks are optional for a single image.

Use recurring objects, locations, gestures, or motifs for variations, image series, and return visits to the same song. Preserve the motif while changing its context or meaning.

## Structured prompt compiler

Compile in this order:

1. semantic scene premise;
2. decisive instant and visible action;
3. subject roles and portrait instructions;
4. environment and spatial relationships;
5. primary symbolism;
6. camera and composition;
7. lighting, palette, and atmosphere;
8. motion and texture;
9. output requirements;
10. negative constraints.

Retain structured sections for debugging and retries even when the provider receives one coherent prompt.

Do not concatenate all Song DNA fields equally.

## Style compatibility

Preserve the V2 style system and current contracts.

- Style is rendering language, not scene meaning.
- Style must not override the scene contract.
- Quick Generate uses the AI-selected/default treatment.
- Explicit style selection belongs in Fine Tune or existing advanced behavior.
- Preserve owner style activate/deactivate functionality.

## Controlled retries

Persist enough planning information to retry without reinterpreting the song.

Where practical classify:

- identity/portrait failure;
- composition failure;
- scene incoherence;
- symbol overload;
- incorrect environment;
- text/logo contamination;
- provider failure;
- safety rejection.

Preserve the Song DNA, selected direction, and unaffected scene fields. Change only the failing dimension when possible. Do not silently alter credit behavior.

## Persistence and security

Prefer existing JSON/job metadata. If insufficient, use the smallest backward-compatible additive migration.

Trace at minimum:

- selected Song DNA;
- visual-board version;
- directions;
- selected direction;
- scene-contract version;
- prompt-compiler version;
- generation attempt;
- retry relationship;
- provider/model metadata;
- validation/fallback events.

Do not alter completed historical renders.

Use configured provider abstractions and credentials. Do not duplicate clients or hardcode secrets. Use strict structured output, validation, bounded repair, explicit template versions, token limits, and deterministic fallback.

## Implementation sequencing

### Phase A — Existing-system audit and contracts

- Trace current Song DNA, prompt, portrait, style, draft, queue, provider, credit, and persistence boundaries.
- Reuse the richest existing internal Song DNA representation.
- Define versioned contracts for Visual Campaign Board, three directions, ranking, and Visual Scene Contract.
- Add strict validation, bounded repair, deterministic fallback, and sanitized trace records.

### Phase B — Planning engine

- Implement board creation from internal Song DNA.
- Produce exactly three materially distinct directions: primary, alternate, unexpected.
- Rank directions for Song DNA fidelity, narrative coherence, visual distinctiveness, and portrait suitability.
- Select the strongest direction automatically for the current/default generation path.
- Compile the selected direction into the Visual Scene Contract.
- Keep portrait images out of board and direction planning.

### Phase C — Prompt and generation integration

- Integrate portrait roles only after Scene Contract selection.
- Compile the provider prompt in the approved structured order.
- Preserve style as rendering language subordinate to scene meaning.
- Wire the new compiler into the existing development generation path without changing auth, credits, queue ownership, provider credentials, storage, or gallery contracts.
- Preserve a safe switch/fallback to the current compiler during evaluation.

### Phase D — Evaluation and controlled retry foundation

- Persist/version the selected internal artifacts or sanitized trace data.
- Add five contrasting fixtures and old-versus-new compiler comparisons.
- Where affordable, run controlled development generations.
- Classify prompt/planning failures and preserve successful upstream decisions.
- Do not change retry charging or customer credit behavior in this phase.

### Phase E — Design after acceptance

Only after the backend planning layer is working and reviewed:

- define the customer-safe Song DNA projection and selector;
- expose the three direction summaries through Explore Options;
- add optional Fine Tune controls;
- complete remaining mobile/desktop design work;
- update Flutter interaction/component specifications.

## Verification fixtures

Use at least five contrasting Song DNA fixtures:

1. intimate loss;
2. triumphant transformation;
3. ambiguous relationship;
4. kinetic adventure;
5. quiet introspection.

Evaluate:

- direction distinctiveness;
- Song DNA fidelity;
- scene specificity;
- portrait-role clarity;
- composition hierarchy;
- symbol count;
- contradictions;
- fallback behavior.

Do not claim improvement merely because a prompt is longer.

## Acceptance criteria

- Quick Generate requires no visual-setting decisions.
- Exactly three valid directions are produced or a safe fallback is used.
- Quick Generate selects the strongest direction.
- Explore Options reuses those directions.
- A validated Scene Contract precedes prompt compilation.
- Prompts are scene-specific rather than adjective-heavy.
- Style cannot override scene meaning.
- Portrait data never enters direction planning.
- Existing auth, credits, privacy, queue, providers, storage, gallery, routes, and owner controls remain compatible.
- Planning artifacts are versioned and traceable.
- Retry behavior preserves successful decisions.
- Planning failure does not strand the user.
- No Campaign Engine text-format functionality is imported.

## Reporting

After each slice, update:

- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
- `docs/design/CURSOR-HANDOFF.md`
- implementation roadmap/status documents;
- relevant component, screen, interaction, and Flutter maps;
- test counts and review evidence.

Cursor may work directly on `main` during this active development workflow. Pull before editing, commit/push verified slices, update the handoff, and stop for GPT review.
