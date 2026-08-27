---
type: current-project
status: active
updated: 2026-08-27
area: product
---

# Product Definition

YouAreTheSongNow V2 rebuilds the **functionality and product value** of AISaga Arcana without porting its legacy implementation.

## Product promise

Turn a song into an interpreted cinematic visual experience, optionally placing the user inside that visual world.

## Preserve from V1

- song interpretation before generation;
- Song DNA as a structured intermediate artifact;
- persistent personal generation history/gallery;
- optional portrait reference mode;
- asynchronous generation jobs;
- progressive fallback/retry behavior;
- artist/band-specific visual identity as a distinct creative layer.

## Refine for V2

- separate song meaning from cinematography;
- add a Visual Narrative Plan before final prompting;
- preserve portrait identity longer during retries;
- distinguish artist visual identity from actual lore;
- remove contradictory prompt instructions;
- reduce dependence on one giant provider-facing prompt;
- avoid persisting raw lyrics by default unless explicitly chosen;
- keep provider/model implementation replaceable.

## Do not inherit automatically

- V1 PHP file structure;
- duplicated worker variants;
- duplicated Stripe webhook paths;
- mutable credit columns as the sole accounting model;
- runtime schema hacks;
- hardwired Gemini model IDs;
- marketing terminology that does not match actual behavior.

## Open product questions

See [[../02 Decisions/Decision Inbox]].
