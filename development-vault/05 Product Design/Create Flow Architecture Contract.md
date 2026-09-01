---
type: product-design-contract
status: design-os-approved-structural
updated: 2026-08-31
area: create-flow
owners:
  - CuBiX Meow
  - Brut
source: GPT / design review Create-flow decisions
---

# Create Flow Architecture Contract

## Status

GPT/design review approved the **structural Create product architecture** below on 2026-08-31. It is incorporated into the permanent design OS at `docs/design/DESIGN-OPERATING-SYSTEM.md` and fully specified at `docs/design/screens/create-flow.md`.

This is **not** authorization to broadly redesign or ship the new Create UX yet. First Build Feature Contract and Onboarding/Paywall Contract still describe the shipped Build 1 journey (song → portraits → style/quality/orientation). Those contracts need an explicit amendment before implementation and acceptance tests change.

## Locked principle

AI should remove decisions by default and offer intelligent choices when the user asks for control.

- Prefer Song DNA over generic image-generator controls.
- Quick Generate is the default: Song → Song DNA → Generate.
- Explore Options is secondary: AI-recommended visual directions for the selected DNA (not permanent generic presets).
- Fine Tune is optional and advanced only.
- Generation is a creative transformation sequence with honest stages and visible selected DNA when possible.
- Reveal prioritizes the image, then Save / Share / Variation / Reimagine.

## Song DNA authority

Customer Create must not be designed around direct lyric selection. Selectable dimensions must project from the current `song-dna-v2.0` schema. Provisional UX labels (Emotional Core, Story/Situation, Character/Point of View, Setting/World, Symbols/Imagery, Conflict/Tension) are not frozen until mapped and copy-approved.

## Shell

Mobile remains canonical. Approved customer destinations are Create, Gallery and Account; Owner remains owner-only. Discover is not added without a separate product decision. Focused Create may hide nav after safe draft/exit behavior exists; desktop expands the same product (rail + main + optional context panel).

## Resolved in the Luminous Night Studio package

- Portrait management lives at the top of Gallery; Create uses active/default identity after that server contract exists.
- Download remains the accurate current action; Save is not introduced without a distinct saved/favorite state.
- Current regeneration can become Create a variation; Reimagine remains future and distinct.
- Discover is not added to navigation.
- Fine Tune starts with orientation, no-text, Special instructions, and quality only while economics require it.

Remaining blockers: explicit amendment of First Build/Onboarding configuration requirements; customer-safe DNA projection and persistence; active/default portrait persistence if absent; DNA conflict rules; Quick Generate server auto-resolution.

## Related

- [[../02 Decisions/ADR-20260831-create-flow-dna-first]]
- [[Onboarding and First-Creation Paywall Contract]]
- [[First Build Feature Contract]]
- [[V2 Song DNA and Prompt Pipeline Contract]]
