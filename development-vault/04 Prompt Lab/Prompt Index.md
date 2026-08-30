---
type: index
status: active
updated: 2026-08-30
area: prompt-lab
---

# Prompt Lab

Treat prompts like versioned product behavior, not disposable text snippets.

## Current V1 reference

Start with [[V1 to V2 Creative Engine Comparison]] for the authoritative feature-by-feature bridge, then [[V1 Prompt Functionality Map]] for the recoverable V1 prompt architecture. Implementation note: [[V2 V1-Derived Image Prompt Foundation]].

Deep documentation:

- [`13 — V1 Prompting Functionality Reference`](../../docs/rebuild/13-prompting-functionality-reference.md) — source/static/runtime/DB/model-generated/fallback prompt behavior.
- [`14 — Prompt Quality & Refinement Analysis`](../../docs/rebuild/14-prompt-quality-and-refinement-analysis.md) — strengths, contradictions, prompt governance and V2 refinement recommendations.
- [`10 — V1 Prompt Pipeline`](../../docs/rebuild/10-v1-prompt-pipeline.md) — stage-by-stage forensic pipeline.
- [`11 — V2 Prompt Refinement Plan`](../../docs/rebuild/11-v2-prompt-refinement-plan.md) — proposed future multi-artifact design, not implemented.

## Experiment flow

1. State a hypothesis.
2. Record the exact prompt/spec version.
3. Record representative input conditions.
4. Compare outputs.
5. Note what improved and what regressed.
6. Keep rejected experiments so failed ideas are not rediscovered later.
7. Promote successful behavior into a current creative-engine spec only after review.

## Recommended experiment categories

- Song Interpretation
- Song DNA
- Visual Narrative Planner
- Artist Visual Identity
- Portrait Integration
- Prompt Compiler
- Retry / Safety
- Curated Style Presets

## V2 prompt-governance goal

When implementation begins, every important generation should eventually be traceable to the exact prompt/spec versions, style revision, compiler version, retry policy, provider/model, and experiment revision that produced it.

## Naming

Use descriptive names such as:

`Scene Planner v0.3 — metaphor balance test.md`

or date-prefixed experiment notes when useful.

## Template

See [[../Templates/Prompt Experiment Template]].
