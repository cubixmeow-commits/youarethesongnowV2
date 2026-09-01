# YouAreTheSongNow — Cursor Correction Instructions

**Round:** 012.1  
**Written by:** GPT design/architecture review  
**Date:** 2026-09-01  
**Status:** Ready for Cursor  
**Repository:** `cubixmeow-commits/youarethesongnowV2`  
**Working branch:** `main`  
**Scope:** Make the POV-derived planner genuinely intelligent and establish one canonical prompt compiler

## Review outcome

Round 012 is structurally successful but not yet accepted as the completed POV Campaign Engine integration.

Accepted foundations:

- hidden Visual Campaign Board;
- exactly three direction contracts;
- Visual Scene Contract;
- portrait boundary after planning;
- versioned sanitized trace;
- legacy fallback switch;
- persistence migration;
- five fixtures;
- 1164 passing tests;
- no customer-facing design work.

Acceptance blockers:

1. `VisualNarrativePlanner` is deterministic template assembly only.
2. Direction scores are substantially hard-coded by direction type, so primary is designed to win regardless of the song.
3. Direction titles, summaries, scores, and framing repeat across fixtures instead of demonstrating three genuinely song-specific alternatives.
4. `StructuredPromptCompiler` produces `compiledPromptSafe`, but `GeminiImageAdapter::buildImagePrompt()` constructs a second large prompt from Song DNA and Scene Contract. There must be one canonical semantic compiler, not competing prompt assembly paths.
5. The test assertion intended to limit arbitrary prompt growth is ineffective because `... || strlen($newPrompt) > 0` passes every non-empty prompt.
6. Verification is prompt-level only; no controlled image A/B evidence exists yet.

## Required correction

### 1. Add configured structured planning-model calls

Use the existing text-provider abstraction. Prefer the approved/configured low-cost Gemini text model (Gemini 2.5 Flash-Lite if that remains the project setting).

The model must receive sanitized internal Song DNA and return strict JSON for:

- Visual Campaign Board;
- exactly three materially distinct directions;
- content-derived scoring/ranking inputs;
- selected direction;
- Visual Scene Contract or the inputs required to compile it deterministically.

Do not send portrait image bytes or raw lyrics.

Use:

- explicit schema;
- versioned prompt/template;
- strict validation;
- bounded repair;
- time/token limits;
- sanitized logs;
- deterministic planner as fallback only.

Live text planning must be behind clear configuration and use existing credentials/provider clients.

### 2. Make ranking meaningful

Remove fixed “primary always has highest fidelity” behavior.

Ranking must reflect the actual direction and Song DNA:

- Song DNA fidelity;
- narrative coherence;
- visual distinctiveness;
- portrait suitability;
- information-budget compliance;
- contradiction/risk penalties.

`primary`, `alternate`, and `unexpected` describe creative roles, not guaranteed ranking order.

Quick Generate selects the actual highest-ranked valid direction, whichever type wins.

Tie-breaking must be deterministic and documented.

### 3. Make directions song-specific

Each direction needs:

- distinct song-specific title;
- distinct user-safe summary;
- concrete scene premise;
- different viewpoint, relationship emphasis, decisive instant, or symbolic strategy;
- no generic “After the turn” / “Symbolic reframing” boilerplate as final output;
- no template fragment such as “Immediately beside the main beat.”

Tests must confirm meaningful textual/semantic differences, not only three distinct type labels.

### 4. Establish one canonical prompt path

`StructuredPromptCompiler` and the validated Scene Contract must be the semantic source of truth.

Refactor `GeminiImageAdapter` so it wraps the canonical compiled prompt with only provider-specific identity attachment, image modality, aspect, and safety requirements.

Do not reconstruct a second competing Song DNA/staging prompt.

Other adapters should consume the same canonical compiled semantic prompt wherever their provider interface permits.

Avoid duplicated or conflicting camera, composition, symbolism, staging, and “creative freedom” instructions.

### 5. Strengthen tests

Replace the ineffective prompt-length assertion.

Add tests for:

- model-output schema validation;
- malformed response repair;
- timeout/provider failure fallback;
- portrait bytes absent from planning payload;
- raw lyrics absent from planning payload and persisted trace;
- non-primary direction capable of winning;
- all three directions materially different;
- content-derived scores;
- deterministic tie break;
- information-budget enforcement;
- canonical compiled prompt used by Gemini image adapter;
- legacy switch;
- migration/persistence compatibility;
- no extra credit charge from planning.

Keep the deterministic fixtures, but identify them as fallback/contract fixtures rather than evidence of intelligent planning quality.

### 6. Controlled evaluation

Generate sanitized comparison artifacts for the five fixtures using:

- deterministic fallback;
- live/recorded structured planning output;
- legacy compiler;
- corrected canonical compiler.

If configured live image calls and owner-authorized development credits are available, run a small controlled A/B image comparison. Otherwise provide an exact command/harness and state that image-quality acceptance remains pending.

Do not claim improved images from prompt structure alone.

## Preserve

No new customer-facing Song DNA, Explore Options, Fine Tune, or broader design work.

Preserve routes, auth, privacy, credits, queue ownership, portraits, owner controls, providers, storage, gallery, responsive behavior, and Flutter contracts.

Do not deploy.

## Finish

1. Run the full suite.
2. Update both handoffs and Round 012 review evidence.
3. Record exact planning model, schema/template versions, fallback rates, and provider boundaries.
4. Include examples where alternate or unexpected legitimately outranks primary.
5. Commit and push `main`.
6. Stop for GPT/owner review.
