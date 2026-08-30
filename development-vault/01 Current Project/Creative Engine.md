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
song selection → Gemini Song DNA → saved derived DNA → locked snapshot
  → selected portrait(s) → Gemini 3.1 Flash Image (inline portraits) → gallery
```

V1 comparison: `development-vault/04 Prompt Lab/V1 to V2 Creative Engine Comparison.md`.

## Current provider path

- **Analysis:** `gemini-3.6-flash` + Google Search; transient lyric use in private development only; never persist lyrics; worker reuses saved DNA.
- **Image (selected):** native Gemini multimodal `gemini-3.1-flash-image` — text + portrait `inlineData` in one request; V1-style identity-first prompt; no second Song DNA call.
- **Rejected for portrait workflow:** Replicate P-Image-Edit (scenes OK, people omitted/minimized); fal Kontext Multi (scenes OK, people omitted). Both retained for later benchmarking only.
- **Deferred:** print / poster / T-shirt / upscaling.

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

Live Gemini image identity acceptance on Hostinger. See [[../02 Decisions/Decision Inbox]] for other product choices.
