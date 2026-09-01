# Round 012 / 012.1 — Visual Narrative Planning Layer

**Date:** 2026-09-01  
**Scope:** Backend-first POV Campaign Engine adaptation + GPT correction pass  
**Verification level:** Prompt-level only (no controlled image generation in CI)

## What was verified

### Round 012 foundation

- Visual Campaign Board generation from normalized internal Song DNA
- Exactly three ranked directions: primary, alternate, unexpected (creative roles)
- Visual Scene Contract compilation before prompt assembly
- Portrait roles integrated after direction selection (portrait count only — no image bytes in planning)
- Structured prompt compiler (`structured-prompt-v1`) wired into creative package build
- Safe fallback to legacy compiler via `VISUAL_NARRATIVE_LEGACY_COMPILER=true` or planning failure
- Planning trace persisted to `song_dna_artifacts.planning_trace_json` (sanitized)

### Round 012.1 corrections

- `GeminiVisualNarrativePlanner` — structured JSON planning behind `VISUAL_NARRATIVE_PLANNING_LIVE_CALLS`
- `DirectionRanker` — content-derived ranking; non-primary can win; deterministic tie-break by direction `id`
- Improved deterministic fallback with song-specific titles/premises (contract fixtures, not intelligence evidence)
- `GeminiImageAdapter` — canonical path wraps `compiledPromptSafe` only (identity + modality); legacy rebuild preserved when planning absent
- Recorded model response fixture proves `unexpected` can outrank `primary`
- Comparison harness: `php bin/compare-visual-narrative-prompts.php`

## Five-fixture prompt comparison

See `prompt-comparison.json` for sanitized board summaries, direction rankings, scene contract summaries, and legacy vs canonical prompt excerpts.

| Fixture | Default CI planner | Selected type (deterministic fallback) |
| --- | --- | --- |
| intimate_loss | deterministic fallback | primary |
| triumphant_transformation | deterministic fallback | primary |
| ambiguous_relationship | deterministic fallback | primary |
| kinetic_adventure | deterministic fallback | primary |
| quiet_introspection | deterministic fallback | primary |

With live/recorded structured planning enabled, ranking uses model `score_hints`; `kinetic_adventure` recorded fixture selects `dir-unexpected`.

## Image-level evaluation

Not run in automated CI. Controlled development image comparison:

```bash
# Requires owner-authorized credentials and development credits
AI_PROVIDERS_ENABLED=true GEMINI_IMAGE_LIVE_CALLS=true php bin/compare-visual-narrative-prompts.php
```

Image-quality acceptance remains pending — do not claim improved images from prompt structure alone.

## Config switches

- `VISUAL_NARRATIVE_PLANNING_ENABLED=true` (default)
- `VISUAL_NARRATIVE_LEGACY_COMPILER=false` (set `true` to force legacy compiler for A/B evaluation)
- `VISUAL_NARRATIVE_PLANNING_LIVE_CALLS=false` (default; set `true` + Gemini credentials for live structured planning)
- `GEMINI_VISUAL_PLANNING_MODEL=` (empty reuses `GEMINI_MODEL`)

## Tests

`php tests/run.php` → **1187 passed, 0 failed**
