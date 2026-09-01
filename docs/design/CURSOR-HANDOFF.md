# CURSOR-HANDOFF — Luminous Night Studio Phase 1 complete

**Date:** 2026-09-01
**Working branch:** `cursor/luminous-night-studio-phase1-6bc7`
**Base:** `main` @ `750809ea759b8e5203df476bdf63d5fb3746f93f`
**Phase:** 1 — runtime semantic foundation, private component lab, current Explore presentation
**Status:** Implemented, tested, screenshot pack captured. Stop for Codex / GPT visual review. Do not merge yet.

## Package / slice verification

- `php tests/run.php`: **1036 passed, 0 failed**
- Runtime/backend/API/migration changes: **none**
- Explore endpoint: still `POST /api/v1/explore-directions`
- Three-direction schema, derived-DNA-only body, StyleMap data attributes, diagnostics gating: **preserved**
- Style-board PNG: art-direction evidence only; not copied into runtime

## Changed files

Runtime

- `public/assets/css/app.css`
- `public/assets/js/explore.js`
- `public/assets/js/component-lab.js` (new)
- `public/index.php`
- `src/Support/BuildInfo.php`
- `templates/layouts/main.php`
- `templates/owner/component-lab.php` (new)
- `templates/owner/dashboard.php`
- `templates/pages/create.php`
- `templates/pages/gallery.php`
- `tests/run.php`

Evidence / docs

- `design/review/round-010/`
- `docs/design/CURSOR-HANDOFF.md`
- `docs/design/DESIGN-OPERATING-SYSTEM.md`
- `docs/design/foundations/tokens.md`
- `docs/design/foundations/color.md`
- `docs/design/foundations/motion.md`
- `docs/design/screens/inventory.md`
- `docs/design/components/README.md`
- `docs/design/components/inventory.md`
- `docs/design/flutter/component-map.md`
- `docs/design/README.md`
- `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`
- `docs/design/research/README.md`
- `docs/dashboard-data.js`
- `development-vault/START HERE.md`
- `development-vault/01 Current Project/Current Priorities.md`
- `development-vault/01 Current Project/Current Architecture.md`
- `development-vault/01 Current Project/Dashboard Snapshot.md`

## Token / component decisions

- Canonical aliases added beside legacy `--color-*` / `--ink` names. Usage was not rewritten globally.
- `--color-focus` is a color. `--elevation-focus-ring` is the 3px ring. `--focus-ring` remains a legacy alias of the elevation token.
- `--color-text-tertiary` raised from `oklch(0.56 …)` to `oklch(0.62 0.01 256)` before caption use on new components.
- Control tokens: `--control-touch-min: 44px`, `--control-height: 48px`, `--control-primary-height: 52px`. `--touch-min` now aliases control height so existing chrome stays 48px.
- Selected surface/edge and status/info tokens added. Sheet/dialog use `--elevation-sheet` / `--elevation-dialog`.
- Canonical classes: `.yatsn-btn`, `.yatsn-icon-btn`, `.yatsn-status`, `.yatsn-dna-card`, `.yatsn-direction-card`, `.yatsn-sheet`, `.yatsn-dialog`, `.yatsn-artwork`. Explore keeps `.ai-direction-card` as the same card.
- Lab and Explore primary actions use solid cobalt, not a decorative gradient. No `transition: all`, glass stacks, or purple-pink glow.
- Explore flex rows were overriding native `[hidden]`. Canonical continue/status rows now force `display: none` when hidden; disabled opacity on lab controls is preserved.

## Interaction notes

- `Generate for me` remains primary. `Explore options` remains secondary.
- First server-ranked direction still has a quiet Recommended label.
- Whole-card radio selection, selected surface + edge + marker, `aria-checked` / `aria-selected`.
- After selection, `Create this direction` still bridges into the existing Review → Create my image path. Fine Tune was not invented.
- Internal `styleName` / `styleId` stay on `data-*` only.
- While an AI direction is active, the legacy style grid and Review button collapse. `Choose a style manually` restores them.
- Quality, format, and no-text stay visible.
- Loading shows three stable placeholders. Error shows a status banner and Try again. In-flight clicks are ignored via `exploreInFlight`.
- Component lab is `GET /owner/component-lab`: owner + private build. 404 when `ALLOW_EXTERNAL_USERS` is true. Quiet link from Owner operations.

## Backend / API / migration status

**none**

No schema, endpoint, provider, credit, paywall, portrait, or privacy-boundary changes.

## Tests

```text
php tests/run.php
=== Results: 1036 passed, 0 failed ===
```

Scoped coverage added for lab access, Explore semantics/state hooks, StyleMap copy exclusion, repeated async lock, Create `h1`, Gallery empty-state semantics, and token/hidden-row guards.

## Screenshots

Stored under `design/review/round-010/`. Index: `design/review/round-010/README.md`.

Create/Explore loading/ready/selected/error/manual states used private fixtures on `/create` because this slice does not change lookup or portrait contracts.

## Accessibility / contrast / motion / zoom

- Create has a real `<h1 class="session-header__title">`.
- Gallery empty state is no longer `aria-hidden`; CSS `display: none` still hides it when the grid has items.
- Focus is a 3px outline on the interactive surface (`outline-offset: 2px`), not a clipped box-shadow.
- Explore selection is keyboard operable and programmatic.
- Reduced motion clears travel, spinner, and shimmer on touched canonical components.
- Increased-contrast CSS already forces black/white edges; the 390 evidence shot injects those rules because Puppeteer-core cannot emulate `prefers-contrast`.
- 200% zoom: no horizontal overflow at 320 Create, 390 Explore, 900 lab, 1440 Explore (`review-notes.json`).

## Deviations / questions

1. Handoff said commit on `main`. This work is on the Cursor task branch and is **not merged**, per the executing agent instructions.
2. Explore remains inside the current Create Direction stage. No standalone Explore route.
3. `Create this direction` is kept because the current bridge still submits through existing creation.
4. Desktop Explore can still show quality/format under the cards. That is required for this slice, not Fine Tune.
5. Error restores the legacy style grid as the existing manual escape. Confirm whether error should hide it.
6. Tertiary contrast was raised for new components; leftover product captions still using tertiary were not recertified globally.
7. Flutter remains documentation-only. Web class names and the Explore `dataset.yatsnExploreState` hook are recorded in `docs/design/flutter/component-map.md`. Compact layout is canonical; expanded layout adds comparison columns, not extra controls.

## Final commit

- **Hash:** (filled after the documentation commit)
- **Branch:** `cursor/luminous-night-studio-phase1-6bc7`
- **Requires:** Codex / GPT screenshot review. Hostinger sync only after approval. No `.env` changes.

## Recommended next slice

**Phase 2: Create entry and existing-contract song selection** from `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`.

Do not begin Song DNA API, persistence, or customer-safe projection work in that slice.
