# Round 011 review pack — Luminous Night Studio Phase 2

**Date:** 2026-09-01
**Branch:** `cursor/luminous-night-studio-phase2`
**Scope:** Create entry and existing-contract song selection

## Fixture setup

- Owner session cookie created through `SessionService::create` against a local seeded owner.
- Song search states: `window.YatsnSongSearchFixtures` on `/create` when `data-private-build="1"`.
- No live lyrics, provider payloads, or private media in this pack.

## Screenshots

| File | Route | State | Viewport |
| --- | --- | --- | --- |
| `create-entry-320.png` | `/create` | entry, stable `h1` | 320 |
| `create-entry-390.png` | `/create` | entry | 390 |
| `create-entry-768.png` | `/create` | entry | 768 |
| `create-entry-900.png` | `/create` | entry, rail | 900 |
| `create-entry-1440.png` | `/create` | entry, rail + summary | 1440 |
| `song-loading-390.png` | `/create` | lookup loading | 390 |
| `song-results-390.png` | `/create` | artwork-led result | 390 |
| `song-results-768.png` | `/create` | result | 768 |
| `song-results-1440.png` | `/create` | result | 1440 |
| `song-no-results-390.png` | `/create` | no reliable match | 390 |
| `song-error-390.png` | `/create` | error + Try again | 390 |
| `song-selected-390.png` | `/create` | confirmed selection + Change song | 390 |
| `song-selected-1440.png` | `/create` | confirmed selection | 1440 |
| `song-focus-390.png` | `/create` | keyboard focus on Find this song | 390 |
| `song-focus-result-390.png` | `/create` | keyboard focus on result row | 390 |
| `song-reduced-motion-390.png` | `/create` | loading without shimmer travel | 390 |
| `song-increased-contrast-390.png` | `/create` | selected, high-contrast treatment | 390 |
| `zoom-create-entry-320.png` | `/create` | 200% zoom entry | 320 |
| `zoom-song-results-390.png` | `/create` | 200% zoom results | 390 |
| `zoom-song-selected-1440.png` | `/create` | 200% zoom selected | 1440 |

## Review notes

- **Hierarchy:** one real `<h1>Choose your song</h1>`; stage title remains `The song` as `h2`.
- **Contract:** lookup still uses `POST /api/v1/song-lookups` with `{ artist, title }`; draft still patches `songLookupId` on successful lookup.
- **Selection:** result row confirms the match; People stage stays hidden until confirmation; `Change song` is reversible.
- **Artwork:** API has no artwork field yet; restrained circular fallback renders when `artworkUrl`/`thumbnailUrl` are absent.
- **Reduced motion:** `prefers-reduced-motion: reduce` removes button/row travel on touched song-search surfaces.
- **Increased contrast:** product CSS already forces black/white edges; the 390 shot injects those rules because Puppeteer-core cannot emulate `prefers-contrast`.
- **200% zoom:** see `review-notes.json` for `scrollWidth === innerWidth` checks.

## Capture

```bash
php -S 127.0.0.1:8765 -t public public/router.php
cd design/review/round-011 && npm install && node capture-screenshots.mjs
```
