---
type: current-project
status: active
updated: 2026-08-28
area: creative-engine
---

# Creative Engine

## Core V1 insight

Arcana's strongest idea is staged interpretation: understand the song before generating pixels.

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

The detailed draft contract is `development-vault/05 Product Design/V2 Song DNA and Prompt Pipeline Contract.md`. It uses the final V1 Song DNA, portrait integration, cinematic compiler and StyleMap as the foundation while adding memory-only lyric handling, structured leakage sanitization, strict validation, portrait-preserving retries and output evaluation.

V1's final compiler was Gemini-specific. V2 keeps a provider-neutral canonical creative package and compiles it through separately benchmarked model adapters. Equivalent creative intent may use different prompt length, ordering, reference controls, native parameters and retry behavior for each provider.

## Priorities

When portrait mode is enabled, retries should preserve priorities in this order unless owners decide otherwise:

1. user identity fidelity;
2. song meaning / emotional truth;
3. scene readability;
4. artist visual identity;
5. cinematography;
6. branding;
7. provider-specific flourishes.

## Important V1 discoveries

- Song DNA is a real 12-field interpretation artifact.
- Dynamic Band Style is a visual StyleMap, not a true lore system.
- V1's default UI did not actually trigger the dynamic StyleMap path.
- V1 retries silently removed portraits at attempt 4.
- The final V1 prompt mixed conflicting responsibilities such as exact portrait identity vs no recognizable real people and branding text vs no visible text.
- The newest prompt-bearing parallel worker preserved the strong creative structure but removed some JSON repair/normalization and contains corrupted abbreviated fallback strings. V2 will preserve the creative design, not those implementation defects.

## Open

See [[../02 Decisions/Decision Inbox]] for unresolved product choices.
