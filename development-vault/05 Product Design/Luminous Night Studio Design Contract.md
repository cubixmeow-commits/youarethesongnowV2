---
type: product-design-contract
status: owner-approved
updated: 2026-08-31
area: premium-design
owners:
  - CuBiX Meow
  - Brut
source: desktop continuation of Design GPT Cursor Workflow
---

# Luminous Night Studio Design Contract

## Decision

**Luminous Night Studio** is the approved production baseline for the YouAreTheSongNow premium product design program.

It refines, rather than rejects, the existing YS black/graphite/platinum/sapphire identity. The customer product becomes a quiet midnight creative studio: matte near-black and smoked graphite provide the room, platinum typography provides editorial structure, one controlled sapphire/cobalt light identifies action and selection, and generated artwork supplies the strongest color and emotion.

## Visual and interaction contract

- Instrument Serif for sparse emotional/display moments; DM Sans for UI/body/status/account.
- Artwork-first hierarchy; neutral chrome; one dominant action per decision view.
- Mobile/touch is canonical; desktop adapts the same state into rail + workspace + optional context.
- Quick Generate is default, Explore is secondary, Fine Tune is optional.
- Selection uses tonal lift + edge + explicit marker/semantics; no generic AI glow.
- Motion concentrates in track advance, selection response, and short cover reveal, with reduced-motion alternatives.
- Cards exist only when the boundary is the interaction.
- No AI-console language, provider/model names, prompts, raw lyrics, internal StyleMap names, glass stacks, purple-pink gradients, emoji controls, or music clip-art.

## Resolved product-design questions

- Portrait management lives at the top of Gallery. Create uses an active/default portrait after that contract exists and may show a compact change affordance.
- Primary customer navigation remains Create, Gallery, Account. Discover is not added without a separate product decision.
- Use `Download`, not `Save`, while completed images are already persisted to Gallery and no favorite/save state exists.
- Current regeneration may be labeled `Create a variation` when behavior matches. `Reimagine` stays future until distinct behavior and API are approved.
- Fine Tune initially permits orientation, no-text, Special instructions, and quality only while pricing/economics require quality choice. Internal StyleMap/provider/model controls remain hidden.

## Backend boundary

This design contract does not silently change backend contracts. Song DNA customer projection/persistence, active/default portrait persistence, Quick Generate auto-resolution, and distinct Reimagine behavior require explicit API/data/business-rule contracts and tests before implementation.

Existing privacy, lyrics, provider-adapter, draft/snapshot/job, credit/paywall, media, sharing, deletion, and async-state contracts remain authoritative.

## Canonical repo artifacts

- `docs/design/DESIGN-OPERATING-SYSTEM.md`
- `docs/design/foundations/principles.md`
- `assets/design/references/luminous-night-studio-style-board.png`
- `assets/design/tokens/semantic-tokens.json`
- `docs/design/components/core-components.md`
- `docs/design/screens/premium-product-screens.md`
- `docs/design/foundations/responsive.md`
- `docs/design/flutter/component-map.md`
- `docs/design/flutter/screen-map.md`
- `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`
- `docs/design/CURSOR-HANDOFF.md`

## Implementation authorization

Cursor may execute the roadmap's Phase 1 foundation/component-lab/current Explore presentation slice directly on `main`, then stop for owner/GPT screenshot review. Later phases remain separately gated by their acceptance criteria; Song DNA backend work is contract-first.

## Reference provenance

The prior conversation recorded an approved generated board at a session-scoped `/mnt/data` path that was unavailable in the desktop repo session. The checked-in board is an honestly documented reconstruction from the approved brief and is the canonical repo reference from this decision forward, not a claimed byte-for-byte copy of the missing artifact.

## Related

- [[../02 Decisions/ADR-20260831-luminous-night-studio-baseline]]
- [[V2 Visual Design Direction]]
- [[Create Flow Architecture Contract]]
