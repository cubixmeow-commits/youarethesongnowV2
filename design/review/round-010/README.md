# Round 010 review pack — Luminous Night Studio Phase 1

**Date:** 2026-09-01
**Branch:** `cursor/luminous-night-studio-phase1-6bc7`
**Scope:** runtime semantic foundation, private component lab, current Explore presentation, scoped accessibility

## Fixture setup

- Owner session cookie created through `SessionService::create` against a local seeded owner. No live portraits, lyrics, or provider payloads.
- Component lab: `/owner/component-lab` static fixtures.
- Create/Explore states: `window.YatsnExploreFixtures` on `/create`. The Direction stage is unhidden without song lookup or portraits because this slice does not change those contracts.
- Style-board PNG is not used in runtime or screenshots as product art.

## Screenshots

| File | Route | State | Viewport |
| --- | --- | --- | --- |
| `lab-320.png` | `/owner/component-lab` | full fixture page | 320 |
| `lab-390.png` | `/owner/component-lab` | full fixture page | 390 |
| `lab-768.png` | `/owner/component-lab` | full fixture page | 768 |
| `lab-900.png` | `/owner/component-lab` | full fixture page | 900 |
| `lab-1440.png` | `/owner/component-lab` | full fixture page | 1440 |
| `lab-390-sheet.png` | `/owner/component-lab` | sheet open | 390 |
| `lab-390-confirm.png` | `/owner/component-lab` | confirmation dialog | 390 |
| `lab-reduced-motion-390.png` | `/owner/component-lab` | `prefers-reduced-motion: reduce` | 390 |
| `create-320.png` | `/create` | song stage with real `h1` | 320 |
| `create-390.png` | `/create` | song stage | 390 |
| `create-900.png` | `/create` | song stage, rail | 900 |
| `create-1440.png` | `/create` | song stage, rail + summary | 1440 |
| `explore-ready-320.png` | `/create` | three directions, Recommended | 320 |
| `explore-ready-390.png` | `/create` | three directions, Recommended | 390 |
| `explore-ready-900.png` | `/create` | three-column comparison | 900 |
| `explore-ready-1440.png` | `/create` | expanded comparison + summary | 1440 |
| `explore-loading-390.png` | `/create` | three loading placeholders | 390 |
| `explore-selected-390.png` | `/create` | selected + Create this direction | 390 |
| `explore-selected-1440.png` | `/create` | selected expanded | 1440 |
| `explore-error-390.png` | `/create` | error + Try again + manual styles | 390 |
| `explore-manual-390.png` | `/create` | Choose a style manually | 390 |
| `explore-focus-390.png` | `/create` | keyboard focus on first card | 390 |
| `explore-reduced-motion-390.png` | `/create` | loading without shimmer | 390 |
| `explore-increased-contrast-390.png` | `/create` | selected, high-contrast treatment | 390 |
| `zoom-create-320.png` | `/create` | 200% zoom | 320 |
| `zoom-explore-390.png` | `/create` | 200% zoom, ready | 390 |
| `zoom-lab-900.png` | `/owner/component-lab` | 200% zoom | 900 |
| `zoom-explore-1440.png` | `/create` | 200% zoom, selected | 1440 |

## Review notes

- **Keyboard:** Direction cards are a radiogroup. Enter/Space select. Arrow keys move between cards. Focus uses a 3px outline on the interactive surface.
- **Reduced motion:** `prefers-reduced-motion: reduce` removes button/card travel, spinner, and skeleton shimmer on touched components.
- **Increased contrast:** product CSS already forces black/white edges. The 390 screenshot injects the same rules because Puppeteer-core cannot emulate `prefers-contrast`.
- **200% zoom:** `scrollWidth === innerWidth` on 320 Create, 390 Explore, 900 lab, and 1440 Explore. See `review-notes.json`.
- **Privacy:** no lyrics, portraits, keys, or provider payloads in this pack.

## Capture

```bash
php -S 127.0.0.1:8765 -t public public/router.php
cd design/review/round-010 && npm install && node capture-screenshots.mjs
```
