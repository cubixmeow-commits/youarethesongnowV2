---
type: adr
status: accepted
date: 2026-08-31
area: product-create-flow
owners:
  - CuBiX Meow
  - Brut
---

# ADR-20260831 — Create flow is Song-DNA-first with Quick Generate by default

## Decision

Adopt Song-DNA-first Create architecture as permanent product direction:

1. AI removes decisions by default; intelligent choices appear when the user asks for control.
2. Default path: Song → Song DNA selection → Quick Generate (system derives generation parameters).
3. Secondary path: Explore Options with AI-generated, context-aware visual directions (not permanent generic style presets).
4. Fine Tune is optional, advanced, and subordinate to a recommended direction.
5. Generation UX is a creative transformation sequence with honest stages; reveal is artwork-first with Save / Share / Variation / Reimagine.

## Why

Generic style grids and image-generator controls undersell Song DNA, the product’s differentiator, and push users into technical choices the system can make from analysis.

## Consequences

- Design OS and `docs/design/screens/create-flow.md` govern structural Create work.
- Shipped Build 1 Create (Song → People → Direction/style grid) remains until First Build and Onboarding contracts are amended and a focused Create UX pass is authorized.
- New API/draft fields will be required for customer-safe DNA projection, selections, and visual directions.
- Launch Style Catalog remains an engine/admin StyleMap resource; it must not stay the primary customer Explore Options UI.
- Portrait placement, Discover IA, and Variation vs Reimagine remain open and must not be guessed in implementation.

## Related

- `development-vault/05 Product Design/Create Flow Architecture Contract.md`
- `docs/design/screens/create-flow.md`
- Does not supersede lyrics privacy, credits/paywall placement, or provider-adapter rules.
