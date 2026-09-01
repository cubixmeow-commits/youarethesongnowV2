# NEXT DIRECTIVE — Round 012.1 complete, awaiting GPT review

**Date:** 2026-09-01  
**Working branch:** `main`  
**Status:** Correction pass complete — stop for GPT/owner review

Round 012.1 addressed GPT review blockers from Round 012:

- configured structured Gemini planning (`visual-planning-prompt-v1`) behind `VISUAL_NARRATIVE_PLANNING_LIVE_CALLS`;
- content-derived `DirectionRanker` (non-primary can win; deterministic tie-break by direction `id`);
- song-specific deterministic fallback titles/premises;
- canonical Gemini image prompt path via `compiledPromptSafe` wrapper only;
- strengthened tests and comparison artifacts.

Evidence: `design/review/round-012/`  
Tests: **1187 passed, 0 failed**

Do not deploy. Do not start customer Song DNA selector, Explore Options, or Fine Tune UI until GPT accepts the backend layer.

---

# CURSOR-HANDOFF — Round 012.1 Visual Narrative Planner Correction

**Date:** 2026-09-01  
**Scope:** POV planner correction pass (backend only)  
**Status:** Implementation complete. Prompt-level verification in CI. Image A/B pending.

## Verification

- `php tests/run.php`: **1187 passed, 0 failed**
- Prompt comparison harness: `php bin/compare-visual-narrative-prompts.php` → `design/review/round-012/prompt-comparison.json`
- Image-level evaluation: not run in CI (`GEMINI_IMAGE_LIVE_CALLS=true` required for controlled A/B)

## Pipeline (hidden)

```
normalized Song DNA → Visual Campaign Board → 3 ranked directions →
selected Visual Scene Contract → portrait roles (count only) →
StructuredPromptCompiler → compiledPromptSafe →
GeminiImageAdapter canonical wrapper (when planning succeeded) → existing generation path
```

## Planning model and versions

| Item | Value |
| --- | --- |
| Structured planning template | `visual-planning-prompt-v1` |
| Default planning model | `GEMINI_MODEL` (override: `GEMINI_VISUAL_PLANNING_MODEL`) |
| Deterministic fallback | `VisualNarrativePlanner` + `deterministic-fallback-v1` |
| Board / scene / compiler | `visual-board-v1` / `visual-scene-v1` / `structured-prompt-v1` |
| Ranking | `DirectionRanker` — content-derived; tie-break ascending `id` |

## Config

- `VISUAL_NARRATIVE_PLANNING_ENABLED=true` (default)
- `VISUAL_NARRATIVE_LEGACY_COMPILER=false` (set `true` for legacy A/B)
- `VISUAL_NARRATIVE_PLANNING_LIVE_CALLS=false` (default — deterministic fallback in CI)
- `GEMINI_VISUAL_PLANNING_MODEL=` (empty reuses `GEMINI_MODEL`)

## Non-primary ranking example

Recorded model fixture (`tests/fixtures/visual-narrative-model-response.php`): `dir-unexpected` outranks `dir-primary` on `kinetic_adventure` when model `score_hints` are present (portal/threshold premise).

## Preserved

Routes, auth, privacy, credits, queue, providers, storage, gallery, owner controls, Flutter docs — unchanged. No extra credit charge from planning.

## Recommended next slice (after GPT acceptance)

Phase 3: customer-safe Song DNA contract and selector — expose sanitized direction summaries through Explore Options on a later slice.
