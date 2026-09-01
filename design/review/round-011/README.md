# Round 011 review pack — Luminous Night Studio Phase 2

**Date:** 2026-09-01
**Branch:** `cursor/luminous-night-studio-phase2`
**Scope:** Create entry and existing-contract song selection (final focused correction pass)

## Fixture setup

- Owner session cookie created through `SessionService::create` against a local seeded owner.
- Song search states: `window.YatsnSongSearchFixtures` on `/create` when `data-private-build="1"`.
- Resume row stays hidden in Phase 2 (no alternate-draft list contract).
- **Entry evidence:** `create-entry-*` captured at `scrollY = 0` with no pre-scroll. At 390px the harness requires `h1`, progress, `h2`, both fields, and primary submit visible together. At 320px the first field must visibly begin in the initial viewport.
- **Form evidence:** optional `create-form-*` shots scroll the form group below the sticky top bar for form-focused review without substituting for entry captures.
- **Compound states:** `positionAndAssertGroup()` scrolls once so every required member is simultaneously visible below the sticky top bar. Capture fails when any member has `targetVisible: false`. Bounds recorded per member in `review-notes.json`.
- No lyrics, provider payloads, or private media in this pack.

## Screenshots

| File | Route | State | Viewport |
| --- | --- | --- | --- |
| `create-entry-320.png` | `/create` | honest entry at scrollY=0 | 320 |
| `create-entry-390.png` | `/create` | honest entry at scrollY=0 | 390 |
| `create-form-320.png` | `/create` | form-focused scroll | 320 |
| `create-form-390.png` | `/create` | form-focused scroll | 390 |
| `create-entry-768.png` | `/create` | entry | 768 |
| `create-entry-900.png` | `/create` | entry, rail | 900 |
| `create-entry-1440.png` | `/create` | entry, rail + summary | 1440 |
| `song-loading-390.png` | `/create` | loading status + placeholder | 390 |
| `song-results-390.png` | `/create` | status + artwork-led result card | 390 |
| `song-results-768.png` | `/create` | status + result card | 768 |
| `song-results-1440.png` | `/create` | status + result card | 1440 |
| `song-no-results-390.png` | `/create` | no-match status + unavailable card + Try again | 390 |
| `song-error-390.png` | `/create` | error status + Try again | 390 |
| `song-selected-390.png` | `/create` | selected card + Change song | 390 |
| `song-selected-1440.png` | `/create` | selected card + Change song | 1440 |
| `song-focus-390.png` | `/create` | keyboard focus on Find this song | 390 |
| `song-focus-result-390.png` | `/create` | focused result card with visible ring | 390 |
| `song-reduced-motion-390.png` | `/create` | loading without shimmer travel | 390 |
| `song-increased-contrast-390.png` | `/create` | selected card + high-contrast edge | 390 |
| `zoom-create-entry-320.png` | `/create` | 200% zoom entry | 320 |
| `zoom-song-results-390.png` | `/create` | 200% zoom results | 390 |
| `zoom-song-selected-1440.png` | `/create` | 200% zoom selected | 1440 |

## Review notes

- **Resume row:** hidden in Phase 2. No fresh-draft self-link to the current Create URL.
- **Result semantics:** `data-song-results` is a labelled `role="region"`; the selectable match is a native `<button>` with `aria-label` containing title, artist, and match status. No redundant Enter/Space keydown handler.
- **Recent creations:** semantic `<ul>` / `<li>` list with one anchor per item; no invalid `role="list"` without `listitem`.
- **Hidden states:** `[hidden]` song-search rows use `display: none !important` so grid styles cannot leak the loading placeholder into the entry view.
- **Mobile spacing:** compact Create entry spacing at `max-width: 599px` only; tablet/desktop composition unchanged.
- **Keyboard:** result selection uses native button activation (click, Enter, Space once).
- **Reduced motion:** `prefers-reduced-motion: reduce` removes row travel on touched song-search surfaces.
- **Increased contrast:** product CSS forces white borders; the 390 shot injects the same rules because Puppeteer-core cannot emulate `prefers-contrast`.
- **200% zoom:** `scrollWidth === innerWidth` on 320 entry, 390 results, 1440 selected (`review-notes.json`).

## Capture

```bash
php -S 127.0.0.1:8765 -t public public/router.php
cd design/review/round-011 && npm install && node capture-screenshots.mjs
```
