---
type: adr
status: accepted
date: 2026-08-31
area: product-visual-design
owners:
  - CuBiX Meow
  - Brut
---

# ADR-20260831 — Luminous Night Studio is the premium design baseline

## Decision

Adopt **Luminous Night Studio** as the production visual and interaction baseline for the responsive web product and later Flutter expression.

## Why

It best combines the existing YS black/platinum/sapphire identity with the research synthesis: artwork hierarchy, editorial restraint, low-friction creative action, AI complexity hidden behind creative language, and a distinctly Song-DNA-led experience. It can scale from compact touch interfaces to an expanded creative workspace without becoming either a generic AI-glow product or a desktop dashboard.

## Consequences

- Canonical design rules and assets live under `docs/design/` and `assets/design/`.
- Mobile remains canonical; desktop adapts the same product; Flutter ports semantics/state rather than CSS.
- Cursor proceeds through the review-gated roadmap, beginning with foundations/components/current Explore presentation.
- Backend/data/product contract changes remain explicit and test-first.
- The existing V2 visual-direction contract remains valid where compatible and is refined by the Luminous Night Studio contract.

## Related

- `development-vault/05 Product Design/Luminous Night Studio Design Contract.md`
- `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`
