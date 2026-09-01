# NEXT DIRECTIVE — Round 012 complete, awaiting GPT review

**Date:** 2026-09-01  
**Working branch:** `main`  
**Status:** Backend integration complete — stop for GPT/owner review

Round 012 implemented the hidden Visual Narrative Planning pipeline:

`internal Song DNA → Visual Campaign Board → three ranked directions → Visual Scene Contract → portrait roles → structured prompt compiler → existing generation path`

Evidence: `design/review/round-012/`  
Tests: **1164 passed, 0 failed**

Do not deploy. Do not start customer Song DNA selector, Explore Options, or Fine Tune UI until GPT accepts the backend layer.

---

# CURSOR-HANDOFF — Round 012 Visual Narrative Planning Layer

**Date:** 2026-09-01  
**Published to:** `main` at `4bfb609`  
**Scope:** Backend-first POV Campaign Engine adaptation  
**Status:** Implementation complete. Prompt-level verification only in CI. No Hostinger deployment.

## Verification

- `php tests/run.php`: **1164 passed, 0 failed**
- Five contrasting fixtures with old/new prompt comparison: `design/review/round-012/prompt-comparison.json`
- Image-level evaluation: not run in CI (deterministic adapters only)

## Pipeline (hidden)

1. Reuse normalized `song-dna-v2.0` from `CreativePackageBuilder::build()`
2. Build Visual Campaign Board (`visual-board-v1`)
3. Generate exactly three directions and rank (primary wins in deterministic planner)
4. Compile Visual Scene Contract (`visual-scene-v1`)
5. Integrate portrait roles after contract (count only in planning)
6. Compile structured prompt (`structured-prompt-v1`)
7. Wire into existing creative package + Gemini image adapter
8. Persist sanitized trace to `song_dna_artifacts.planning_trace_json`

## Config

- `VISUAL_NARRATIVE_PLANNING_ENABLED=true` (default)
- `VISUAL_NARRATIVE_LEGACY_COMPILER=false` (set `true` for legacy A/B)

## Preserved

Routes, auth, privacy, credits, queue, providers, storage, gallery, owner controls, Flutter docs — unchanged.

## Recommended next slice (after GPT acceptance)

Phase 3: customer-safe Song DNA contract and selector — expose sanitized direction summaries through Explore Options on a later slice.
