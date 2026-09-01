# Round 012 — Visual Narrative Planning Layer

**Date:** 2026-09-01  
**Scope:** Backend-first POV Campaign Engine adaptation  
**Verification level:** Prompt-level only (no controlled image generation in CI)

## What was verified

- Visual Campaign Board generation from normalized internal Song DNA
- Exactly three ranked directions: primary, alternate, unexpected
- Automatic selection of highest-ranked valid direction (primary wins in deterministic planner)
- Visual Scene Contract compilation before prompt assembly
- Portrait roles integrated after direction selection (portrait count only — no image bytes in planning)
- Structured prompt compiler (`structured-prompt-v1`) wired into creative package build
- Safe fallback to legacy compiler via `VISUAL_NARRATIVE_LEGACY_COMPILER=true` or planning failure
- Planning trace persisted to `song_dna_artifacts.planning_trace_json` (sanitized)
- Gemini image adapter consumes scene contract block when present

## Five-fixture prompt comparison

See `prompt-comparison.json` for sanitized board summaries, direction rankings, scene contract summaries, and legacy vs new prompt excerpts.

| Fixture | Primary selected | Fallback |
| --- | --- | --- |
| intimate_loss | yes | no |
| triumphant_transformation | yes | no |
| ambiguous_relationship | yes | no |
| kinetic_adventure | yes | no |
| quiet_introspection | yes | no |

## Image-level evaluation

Not run in automated CI (deterministic adapters only). Controlled development image comparison can be run locally when `GEMINI_IMAGE_LIVE_CALLS=true` with the same fixtures.

## Config switches

- `VISUAL_NARRATIVE_PLANNING_ENABLED=true` (default)
- `VISUAL_NARRATIVE_LEGACY_COMPILER=false` (set `true` to force legacy compiler for A/B evaluation)
