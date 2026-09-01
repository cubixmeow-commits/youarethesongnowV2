# YouAreTheSongNow Design Operating System

**Status:** production design package approved 2026-08-31; phased implementation ready
**Owners:** CuBiX Meow, Brut  
**Source note:** The task referenced `YouAreTheSongNow_Design_Operating_System.md` as a supplied governing file. That file was **not present** in the repository, agent attachments, or transcript at audit time. This document is reconstituted from: (1) the Cloud Agent task brief, (2) accepted vault visual direction, (3) Round 006–009 `/design/` contracts, and (4) verified code under `public/` + `templates/`. GPT/design-director should replace or amend this file if the original source differs. Create-flow principles below were approved in GPT/design review (2026-08-31) and recorded as structural product architecture — **not** a broad visual redesign authorization.

---

## 1. Purpose

Establish a permanent, repo-aware design-engineering system so that:

- the web client stays coherent while Build 1 continues;
- mobile (phone) remains the canonical composition;
- Flutter iOS/Android can later map tokens, components, and screens without reverse-engineering CSS;
- agents (Cursor) and GPT/design direction share one handoff surface.

The approved direction is **Luminous Night Studio**. Implementation proceeds only through the phased, review-gated slices in `process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`. The immediate authorized slice is foundations/component fixtures/current Explore presentation. Song DNA API/database work remains contract-first and is not authorized by visual documentation alone.

## 2. Product thesis

Turn a meaningful song into a cinematic visual world, with the people you love at the heart of its story.

The interface should feel like a **private listening room / contemporary gallery / record sleeve**, not an AI control panel.

### Approved visual baseline

**Luminous Night Studio** is a quiet midnight creative studio: near-black and smoked graphite surfaces, platinum editorial structure, one controlled sapphire/cobalt action light, Instrument Serif + DM Sans, sparse material depth, and artwork as the strongest color/emotion.

- Principles: [foundations/principles.md](./foundations/principles.md)
- Reference: `assets/design/references/luminous-night-studio-style-board.png`
- Canonical tokens: `assets/design/tokens/semantic-tokens.json`
- Responsive behavior: [foundations/responsive.md](./foundations/responsive.md)
- Component states: [components/core-components.md](./components/core-components.md)
- Screen specifications: [screens/premium-product-screens.md](./screens/premium-product-screens.md)

Selected identity: intertwined **YS** monogram + `YouAreTheSongNow` wordmark (`design/BRAND_SYSTEM_YS.md`, assets under `public/assets/images/brand/`).

## 2a. Core creation principle (locked)

**AI should remove decisions by default and offer intelligent choices when the user asks for control.**

- Do **not** primarily expose generic image-generator controls.
- When Song DNA can express intent more naturally, prefer Song DNA over technical image-generation parameters.
- Default path: **Song → Song DNA → Generate** (Quick Generate).
- Secondary path: Song DNA → **AI-recommended visual directions** (Explore Options) — not permanent generic presets (`cinematic` / `anime` / `watercolor` / `realistic`).
- Advanced path only: recommended direction → **Fine Tune** → Generate. Fine Tune must not dominate.
- Generation is a creative transformation of a song, not model-operator UX. No customer-facing prompt/inference/LoRA/parameter language.
- Full structural specification: [screens/create-flow.md](./screens/create-flow.md).

## 3. Platform philosophy

| Priority | Surface | Role |
| --- | --- | --- |
| 1 | Phone (~320–599 CSS px) | Canonical product |
| 2 | Tablet (600–899) | Wider phone chrome; still bottom nav |
| 3 | Desktop (≥900) | Same app on a larger canvas (left rail, main content, optional contextual panel) |

Desktop must not invent a second product language (marketing mega-nav, dashboard cards, separate desktop-only IA).

**Approved customer destinations:** Create, Gallery, Account. Owner is owner-only. Discover remains a future product question and is not added to chrome. Focused Create may hide global navigation only after draft persistence and exit behavior are implemented.

On mobile, focused creation steps **may** hide primary navigation for immersion. On desktop, expand the **same** architecture (rail + main + optional context) — do not design a separate desktop product.

Future Flutter apps consume the same PHP `/api/v1` contracts. Web DOM/CSS is a **visual and interaction reference**, not a porting target.

## 4. Premium rules

**Prefer**

- clear hierarchy and restraint
- expressive but purposeful typography (Instrument Serif + DM Sans already in app)
- generous, rhythmic spacing
- artwork as the emotional center
- Song DNA as the creative control surface (not lyric picking or style-taxonomies as the default)
- excellent loading / empty / error / disabled / retry / offline states
- consistency over novelty
- purposeful motion (cover reveal, track advance, focus, honest generation transformation)

**Avoid unless product-justified**

- gratuitous gradients and glow borders
- glassmorphism stacks
- endless dashboard cards
- pill clusters and stat strips
- AI-console / neon / Orbitron aesthetics (rejected for V2 customer UI)
- generic image-generator control panels as the primary Create UX
- permanent generic style-preset browsers as the main creative choice
- fake percentage completion unless the backend provides reliable progress
- emojis in primary controls
- provider/model names and technical generation jargon in customer-facing UI

## 5. Documentation map

```text
docs/design/                     permanent OS (this tree)
  DESIGN-OPERATING-SYSTEM.md     this file
  CURSOR-HANDOFF.md              latest agent ↔ GPT handoff
  foundations/                   approved principles, tokens, type, color, motion, responsive
  components/                    inventory + production state contracts
  screens/                       routes, architecture, and production screen specs
  audits/                        dated UI audits
  flutter/                       portability + component/screen maps
  process/                       phases, roadmap, acceptance gates

assets/design/                   non-runtime design artifacts
  tokens/                        proposed token exports
  references/                    mood/reference stills (optional)
  audits/                        audit evidence packs
  exports/                       future Flutter/CSS exports

design/                          existing ChatGPT ↔ Cursor round workflow
                                 (review packs, next-pass inbox)
                                 remains active for visual rounds

public/assets/                   runtime CSS/JS/images used by the app
```

## 6. Roles

| Role | Responsibility |
| --- | --- |
| Owners (CuBiX Meow / Brut) | Product approval, release gates |
| GPT / design director | Visual direction, audit review, Phase 2 authorization |
| Cursor / Cloud Agent | Repo-aware implementation, documentation accuracy, tests |
| Vault (`development-vault/`) | Product/architecture truth |
| Meow Control (`/docs`) | Polished owner hub — update when current truth changes |

## 7. Phases

| Phase | Goal | Gate |
| --- | --- | --- |
| **0 — Package lock** | Luminous baseline, tokens, components, screens, Flutter maps, vault truth | Complete in this documentation commit |
| **1 — Foundations** | Semantic aliases, focus/contrast, component lab, current Explore presentation | Screenshot + test review |
| **2 — Create entry** | Create Home and existing-contract song selection | Screenshot + journey review |
| **3 — Song DNA** | Contract-first projection/persistence, then selector | Contract + security/privacy + UI review |
| **4 — Creation choice** | Quick Generate, Explore, Fine Tune, quote/paywall integration | End-to-end review |
| **5–8 — Product completion** | Generation/reveal, Gallery/portraits, Account/onboarding/shell, quality retirement | Per-phase review packs |
| **9 — Flutter** | Freeze/export, then separate Flutter implementation | Explicit owner authorization |

The detailed order and acceptance criteria live in `process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`. Do not skip a gate or combine backend-contract work into a visual slice.

## 8. Working rules for agents

1. Read `docs/design/CURSOR-HANDOFF.md` before design work.
2. Treat phone as canonical; verify 320 / 390 / 768 / 900 / 1440 when changing UI.
3. Prefer small vertical slices over system-wide rewrites.
4. Do not invent routes or commerce behavior.
5. Implement only the roadmap's current slice. DNA projection/persistence waits for explicit First Build/Onboarding/API contract amendments and tests.
6. Keep secrets and raw lyrics out of docs, tokens, and assets.
7. Record decisions that change current truth in the vault + dashboard snapshot.
8. When risky or ambiguous, document the question — do not guess.

## 9. Relationship to existing `/design/` docs

| Existing file | Relationship |
| --- | --- |
| `design/DESIGN_SYSTEM.md` | Pre-OS token/component contract; still accurate for Round 008 baseline; foundations docs should supersede gradually |
| `design/BRAND_SYSTEM_YS.md` | Brand authority for YS marks |
| `design/FLUTTER_DESIGN_HANDOFF.md` | Pre-OS Flutter map; keep until `docs/design/flutter/` fully absorbs it |
| `design/RESPONSIVE_REDESIGN_PLAN.md` | Phone/tablet/desktop layout intent |
| `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` | Round history (009 active) |
| `design/review/round-00X/` | Visual evidence packs |

The permanent OS does **not** delete these. It absorbs and indexes them.

## 10. Design-package completion

- [x] `/docs/design/` structure created and repo-aware
- [x] `/assets/design/` structure created
- [x] Routes, screens, components, tokens audited against code
- [x] Mobile / a11y / Flutter concerns documented
- [x] First semantic token set proposed (not migrated)
- [x] `CURSOR-HANDOFF.md` complete with branch + commit
- [x] GPT/design-review Create-flow principles incorporated into OS + `screens/create-flow.md`
- [x] Luminous Night Studio selected and referenced with honest provenance
- [x] Canonical semantic token baseline finalized
- [x] Core component states and screen-by-screen specifications finalized
- [x] Responsive and Flutter mappings finalized
- [x] Phased build order and acceptance gates finalized
- [ ] Phase 1 runtime foundation/component slice (next executable work)
- [ ] Song DNA API/persistence and full Create UX (contract-first future phases)

## 11. Create-flow architecture pointer

Structural Create product architecture (current → target mapping, Song DNA fields, API gaps, UI states, Flutter notes, open questions):

→ [screens/create-flow.md](./screens/create-flow.md)  
→ Vault: `development-vault/05 Product Design/Create Flow Architecture Contract.md`  
→ ADR: `development-vault/02 Decisions/ADR-20260831-create-flow-dna-first.md`
