---
type: current-project
status: active
updated: 2026-08-30
area: creative-engine
---

# Creative Engine

## Core V1 insight

Arcana's strongest idea is staged interpretation: understand the song before generating pixels. The defining You Are The Song Now requirement is that uploaded people become the recognizable central subjects of that interpreted world.

## Current V2 direction

```text
Source Context
  -> Song Interpretation
  -> Song DNA
  -> Visual Narrative Plan
  -> Artist Visual Identity
  -> Portrait Integration Plan
  -> Scene Composition
  -> Prompt Compiler
  -> Provider Adapter
  -> Quality / Safety Evaluation
  -> Controlled Retry
```

Build 1 active vertical slice:

```text
song selection → Gemini Interactions Song DNA → saved derived DNA → locked snapshot
  → selected portrait(s) → Gemini Flash Lite / Flash Image (inline portraits) → gallery
```

V1 comparison: `development-vault/04 Prompt Lab/V1 to V2 Creative Engine Comparison.md`.

## Current provider path

- **Analysis:** `gemini-3.6-flash` via Interactions API (Google Search + strict JSON Schema, `store=false`); transient lyric use in private development only; never persist lyrics; worker reuses saved DNA. Legacy generateContent Search/JSON conflict and V1 JSON-repair are historical notes only.
- **Image (selected family):** native Gemini multimodal image — text + portrait `inlineData` in one request; one authoritative identity section; no second Song DNA call. `gemini-3.1-flash-lite-image` is the low-cost benchmark candidate and `gemini-3.1-flash-image` is the current quality anchor.
- **Rejected for portrait workflow:** Replicate P-Image-Edit (scenes OK, people omitted/minimized); fal Kontext Multi (scenes OK, people omitted). Both retained for later benchmarking only.
- **Deferred:** print / poster / T-shirt / upscaling.

## Observed composition bias (2026-08-30)

Multiple two-person Gemini generations defaulted to a similar layout: one person toward the left/middle ground and the other larger in the right foreground. Cause: identity, crop, placement, interaction, and FG/MG rules were repeated in both `GeminiImageAdapter` and the appended `compiledPromptSafe`, overweighting a safe generic couple arrangement and suppressing Song DNA-driven staging.

**Fix:** one authoritative identity section; narrative/emotional “central” (not geometric); Song DNA camera/composition/roles/relationships/motion/environment choose staging; explicit freedom over scale, distance, separation, and depth; stop appending duplicate portrait-placement directives from `compiledPromptSafe`.

## Live benchmark observations (2026-08-30)

- Full Flash produced an excellent initial one-person result with the uploaded person recognizable and naturally integrated.
- Four Flash Lite generations cost about $0.14 total, consistent with roughly $0.0336 per 1K output plus small input costs.
- Flash Lite included both people in the Alestorm `Mexico` test but produced a weaker, collage-like composition.
- After the spatial-prompt fix, Flash Lite produced a strong, dynamic two-person `Battle Against Time` / Wintersun result.
- A successful no-text generation still rendered `TICKET`; prompt-only suppression is not an acceptance-grade solution.
- These samples prove feasibility but do not establish the 90-percent identity/usability requirement. Controlled same-input A/B tests and the formal 90-image set remain outstanding.

## Priorities

1. Portrait identity fidelity (uploaded people as unmistakable central subjects)
2. Song DNA consistency between inspection and final image
3. Narrative clarity
4. Protagonist scale and placement
5. Selected-style dominance
6. Composition, lighting and atmosphere
7. One-person versus two-person behavior
8. Low / medium / high quality routing
9. Output evaluation and automatic retry
10. Individual style refinement

## Important V1 discoveries

- Song DNA is a real 12-field interpretation artifact.
- Portraits were attached as Gemini `inlineData` beside the cinematic prompt — that multimodal pattern is the working foundation for V2's Gemini image adapter.
- V1 retries silently removed portraits at attempt 4 — V2 must not.
- Dynamic Band StyleMap and in-image Arcana branding are not carried into V2's general path.

## Open

Provider/tier routing, no-text enforcement, automatic required-person/identity checks, controlled retries, the formal 90-image benchmark and the remaining live acceptance checklist. See [[Build 1 Assessment 2026-08-30]] and [[../02 Decisions/Decision Inbox]].
