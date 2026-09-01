# NEXT DIRECTIVE — Round 012.2 blocked; awaiting credentials + GPT review

**Date:** 2026-09-01  
**Working branch:** `main`  
**Status:** Live validation harness complete — provider calls blocked in Cloud Agent environment

Round 012.2 attempted owner-approved live POV validation per the directive below. The harness is ready and sanitized artifacts are committed, but **no live Gemini planning or image calls executed** because `GEMINI_API_KEY` is not available in this environment.

Evidence: `design/review/round-012-live/`  
Harness: `php bin/run-round-012-live-validation.php` (dry-run) / `--live` (budget-capped)  
Tests: pending commit (1187+ baseline)

**Acceptance gate: BLOCKED** — cannot recommend ACCEPT / ACCEPT WITH TUNING / REJECT without live evidence.

### Blockers

1. `GEMINI_API_KEY` missing from Cloud Agent validation environment (no `.env`, no injected secret).
2. Pass A (5 planning calls) and Pass B (4 image generations) not executed.
3. Owner-authorized development portrait from private vault not mounted; harness will use synthetic portrait only when live image pass runs.

### To unblock

Add `GEMINI_API_KEY` to the Cloud Agent environment secrets, then re-run:

```bash
php bin/run-round-012-live-validation.php --live
```

Do not deploy. Do not begin Song DNA selector, Explore Options, Fine Tune, or broader design work.

---

# NEXT DIRECTIVE — Round 012.2 Live POV Validation

**Date:** 2026-09-01  
**Working branch:** `main`  
**Authority:** Owner approved a small live test  
**Scope:** Validation only — no customer-facing design, deployment, or pipeline redesign

GPT accepts the Round 012.1 architecture conditionally. Prove that the live planner is operational and that its canonical prompt can improve image results before any further design work.

## Budget and boundaries

- Maximum **5 live Gemini planning calls**.
- Maximum **4 live Gemini image generations**: two songs × legacy/new.
- Use existing development credentials and an existing authorized development portrait only.
- Do not expose or commit API keys, raw lyrics, portrait bytes, private storage paths, or unsanitized provider responses.
- Do not charge a customer account or alter customer credits.
- Do not deploy.
- If credentials, provider access, or a safe development portrait are unavailable, stop and report the exact blocker. Do not fabricate results.
- Keep `VISUAL_NARRATIVE_PLANNING_LIVE_CALLS=false` as the committed default. Enable it only in the validation environment.

## Pass A — live planning

Run the five existing varied Song DNA fixtures with:

- `AI_PROVIDERS_ENABLED=true`
- `GEMINI_LIVE_CALLS=true`
- `VISUAL_NARRATIVE_PLANNING_ENABLED=true`
- `VISUAL_NARRATIVE_PLANNING_LIVE_CALLS=true`
- `VISUAL_NARRATIVE_LEGACY_COMPILER=false`

Record only sanitized evidence:

- fixture name;
- planner source and model;
- three direction titles, types, summaries, and component scores;
- winning direction and why its score won;
- whether a non-primary direction won;
- contract validation/repair outcome;
- fallback or error classification;
- latency and planning-call count;
- confirmation that raw lyrics and portrait data were absent.

Evaluate specificity, direction diversity, Song DNA fidelity, coherent decisive instant, useful portrait role, and avoidance of generic cinematic boilerplate.

## Pass B — controlled image A/B

Choose two contrasting fixtures:

1. one relationship/emotional fixture;
2. one kinetic, surreal, or high-motion fixture.

Use the same portrait, model, aspect ratio, quality, no-text policy, and generation settings for each pair. The only intentional variable is:

- **A:** legacy compiler;
- **B:** live structured POV planner + canonical compiled prompt.

Generate exactly four images. Label comparisons blindly as A/B in the review artifact before stating which pipeline produced each.

Score each image from 1–5 for:

- Song DNA specificity;
- emotional legibility;
- scene coherence;
- visual distinctiveness;
- composition/decisive instant;
- portrait presence and identity fidelity;
- unwanted text/artifacts;
- overall preference.

Also record provider/model, latency, dimensions, estimated cost, generation success/failure, and fallback state.

## Artifacts

Write sanitized results under `design/review/round-012-live/`:

- `README.md` — method, environment flags without secrets, blockers, result summary, and recommendation;
- `planning-results.json` — sanitized five-fixture planning output;
- `image-ab-results.json` — scores/metadata and blind-label reveal;
- visual comparison files only if they contain no private portrait or sensitive content; otherwise record local/private references and do not commit the images.

The existing comparison harness currently reports prompt data but does not itself generate the promised image A/B. Extend it only as much as needed for a repeatable, budget-capped validation. It must default to dry-run and require an explicit live flag.

## Acceptance gate

Recommend one of:

- **ACCEPT:** live planning works and new images clearly outperform or tie legacy without new reliability/privacy problems;
- **ACCEPT WITH TUNING:** architecture works, but specific prompt/ranking adjustments are supported by the evidence;
- **REJECT/ROLL BACK:** legacy wins materially or live planning introduces unacceptable failures.

Run the full test suite after any harness-only or evidence-related code change. Commit the sanitized artifacts and handoff update to `main`, then stop for GPT/owner review. Do not begin the Song DNA selector, Explore Options, Fine Tune, or broader design work.

---

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
**Published to:** `main` at `8ef57cf`  
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
