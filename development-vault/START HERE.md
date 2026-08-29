---
type: current-state
status: active
updated: 2026-08-28
area: project
---

# YouAreTheSongNow V2 — Development Vault

This vault is the shared development memory for YouAreTheSongNow V2.

## Current phase

**Private Development Build 1 preparation and implementation.**

CuBiX Meow and Brut authorized Private Development Build 1 on 2026-08-28. External beta access, live Stripe charges and commercial use of protected lyrics remain gated.

## Delivery path

**Web first -> Flutter/Dart iOS second.**

1. Rebuild and refine the web application first on the V2 PHP backend.
2. Build the iOS mobile application second in **Flutter + Dart**.
3. The Flutter client should consume the same PHP backend through clean HTTP/JSON APIs rather than duplicate core business logic.
4. The web rebuild is not a throwaway prototype; it establishes the shared backend and reference behavior for mobile.

See [[02 Decisions/ADR-20260827-web-first-then-flutter-ios]].

## Current direction

- Rebuild and refine **V1 functionality**, not V1 code.
- Backend direction: **PHP**.
- Initial database direction: **SQLite** while the product is small.
- Mobile direction: **Flutter + Dart, iOS first**, after the web rebuild is validated.
- V1 is evidence for behavior and product ideas, not an implementation template.
- The creative engine should preserve staged song interpretation while simplifying and modernizing the pipeline.

## Current creative-engine direction

```text
Song / source context
  -> Song interpretation
  -> Song DNA
  -> Visual narrative plan
  -> Artist visual identity
  -> Portrait integration plan
  -> Scene composition
  -> Prompt compiler
  -> Image generation
  -> Quality / safety evaluation
  -> Controlled retry
```

This is the approved direction for the private Build 1 implementation. Provider assignments and final quality tiers remain provisional until benchmarked.

## Read next

- [[01 Current Project/Current Priorities]]
- [[01 Current Project/Dashboard Snapshot]]
- [[01 Current Project/Product Definition]]
- [[01 Current Project/Current Architecture]]
- [[01 Current Project/Creative Engine]]
- [[02 Decisions/Decision Index]]
- [[02 Decisions/Decision Inbox]]
- [[04 Prompt Lab/Prompt Index]]

## Existing rebuild research

The polished audit and planning documents remain under `docs/rebuild/` in the repository. The GitHub Pages dashboard in `/docs` is the **Meow Control** command center for CuBiX Meow and Brut. This vault is the working room / workshop memory. When current truth changes, update Current Project notes and the dashboard snapshot so the Pages hub stays aligned.

## Information hierarchy

1. Explicit owner instruction
2. Accepted ADRs
3. Current Project notes
4. Verified implementation/tests once development begins
5. Verified V1 evidence
6. Research
7. Experiments
8. Inbox / brainstorming

Never treat a brainstorm or AI proposal as a settled product decision.
