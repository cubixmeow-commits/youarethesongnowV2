# YouAreTheSongNow Design Operating System

**Status:** established 2026-08-31 (Phase 1 audit); Create-flow architecture incorporated 2026-08-31  
**Owners:** CuBiX Meow, Brut  
**Source note:** The task referenced `YouAreTheSongNow_Design_Operating_System.md` as a supplied governing file. That file was **not present** in the repository, agent attachments, or transcript at audit time. This document is reconstituted from: (1) the Cloud Agent task brief, (2) accepted vault visual direction, (3) Round 006–009 `/design/` contracts, and (4) verified code under `public/` + `templates/`. GPT/design-director should replace or amend this file if the original source differs. Create-flow principles below were approved in GPT/design review (2026-08-31) and recorded as structural product architecture — **not** a broad visual redesign authorization.

---

## 1. Purpose

Establish a permanent, repo-aware design-engineering system so that:

- the web client stays coherent while Build 1 continues;
- mobile (phone) remains the canonical composition;
- Flutter iOS/Android can later map tokens, components, and screens without reverse-engineering CSS;
- agents (Cursor) and GPT/design direction share one handoff surface.

This is **not** a broad redesign authorization. Token/visual system migration waits for Phase 2 review. Create UX implementation waits for a focused Create-flow design pass after contract amendments (see [screens/create-flow.md](./screens/create-flow.md)).

## 2. Product thesis

Turn a meaningful song into a cinematic visual world, with the people you love at the heart of its story.

The interface should feel like a **private listening room / contemporary gallery / record sleeve**, not an AI control panel.

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

**Shell destinations (conceptual target):** Create, Gallery, Discover, Account. **Current shipped chrome** is Create, Gallery, Account. Do not implement Discover or immersive Create-nav-hiding until IA implications in the Create-flow spec are resolved.

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
  foundations/                   tokens & foundations
  components/                    component inventory
  screens/                       routes & screens (+ create-flow.md)
  audits/                        dated UI audits
  flutter/                       portability notes
  process/                       phases & gates

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
| **1 — Audit & structure** | Document reality; propose first semantic tokens; Create-flow structural spec; no broad UI migration | GPT review of handoffs |
| **2 — Foundations** | Adopt canonical tokens in CSS carefully; fix critical a11y/state gaps | Owner + GPT approval |
| **3 — Components** | Canonical shared components / partials; reduce duplication | Review pack |
| **4 — Screens** | Screen-by-screen alignment; focused Create-flow UX pass after contract amendment | Review pack |
| **5 — Flutter parity docs** | Frozen token/widget map for Flutter implementation | Separate Flutter start authorization |

**Do not begin Phase 2 token migration or Create UX implementation until the relevant handoff is reviewed.** Create structural docs may land during Phase 1 without authorizing Create pixel work.

## 8. Working rules for agents

1. Read `docs/design/CURSOR-HANDOFF.md` before design work.
2. Treat phone as canonical; verify 320 / 390 / 768 / 900 / 1440 when changing UI.
3. Prefer small vertical slices over system-wide rewrites.
4. Do not invent routes or commerce behavior.
5. Do not implement the DNA-first Create UX until First Build / Onboarding contracts are amended and a focused Create pass is authorized; keep documentation ahead of pixels.
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

## 10. Success criteria for Phase 1

- [x] `/docs/design/` structure created and repo-aware
- [x] `/assets/design/` structure created
- [x] Routes, screens, components, tokens audited against code
- [x] Mobile / a11y / Flutter concerns documented
- [x] First semantic token set proposed (not migrated)
- [x] `CURSOR-HANDOFF.md` complete with branch + commit
- [x] GPT/design-review Create-flow principles incorporated into OS + `screens/create-flow.md`
- [ ] Broader Phase 2 token/visual migration (still gated)
- [ ] Create UX implementation (gated on contract amendment + focused design pass)

## 11. Create-flow architecture pointer

Structural Create product architecture (current → target mapping, Song DNA fields, API gaps, UI states, Flutter notes, open questions):

→ [screens/create-flow.md](./screens/create-flow.md)  
→ Vault: `development-vault/05 Product Design/Create Flow Architecture Contract.md`  
→ ADR: `development-vault/02 Decisions/ADR-20260831-create-flow-dna-first.md`
