# CURSOR-HANDOFF — Luminous Night Studio Phase 2 complete

**Date:** 2026-09-01
**Working branch:** `cursor/luminous-night-studio-phase2`
**Base:** `main` @ `795fc2a`
**Phase:** 2 — Create entry and existing-contract song selection
**Status:** Ready for Codex review. Do not merge to `main`.

## Package / slice verification

- `php tests/run.php`: **1083 passed, 0 failed**
- Runtime/backend/API/migration changes: **none**
- Song lookup endpoint: still `POST /api/v1/song-lookups` with `{ artist, title }`
- Draft patch: still `songLookupId` on successful lookup
- Privacy/rate-limit behavior: preserved (server-side)
- Song DNA API/persistence: **not started**

## Changed files

Runtime

- `public/assets/css/app.css`
- `public/assets/js/app.js`
- `public/assets/js/song-search.js` (new)
- `templates/layouts/main.php`
- `templates/pages/create.php`
- `tests/run.php`

Evidence / docs

- `design/review/round-011/`
- `docs/design/CURSOR-HANDOFF.md`
- `docs/design/components/README.md`
- `docs/design/flutter/component-map.md`
- `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`
- `docs/dashboard-data.js`
- `development-vault/01 Current Project/Current Priorities.md`
- `development-vault/01 Current Project/Dashboard Snapshot.md`

## Decisions and preserved contracts

- **Page hierarchy:** one stable `<h1>Choose your song</h1>`; stage title remains `The song` as `h2`. Song title/artist no longer replace the `h1`.
- **Primary action:** `Find this song` uses canonical `.yatsn-btn--primary` at 52px height; full-width on compact, pane-aware width from `@container yatsn-create-entry`.
- **Lookup contract:** unchanged request/response. `app.js` still posts `{ artist, title }` and patches `songLookupId` after a successful lookup.
- **Selection flow:** lookup renders an artwork-led result row; user confirms by activating the row; People stage stays hidden until confirmation. `Change song` reverses confirmation without clearing typed fields.
- **In-flight guard:** `song-search.js` blocks duplicate submits while lookup is pending.
- **Artwork:** uses `artworkUrl` / `thumbnailUrl` when present on the lookup payload; otherwise a restrained circular fallback (no new provider integration).
- **Resume / recent:** resume row uses existing draft URL (`?draft=`); recent creations use existing `GET /api/v1/images` thumbnails. No new list endpoints.
- **Active portrait summary:** not added — no explicit server active/default portrait contract yet (per roadmap).
- **Progress model:** `01 Song / 02 People / 03 Direction` preserved; presentation-only `is-current` / `is-complete` logic unchanged.
- **Explore / Direction / DNA:** untouched in this slice.

## Tests

```text
php tests/run.php
=== Results: 1083 passed, 0 failed ===
```

Also: `php -l` on touched PHP templates, `node --check` on `song-search.js` / `app.js` / `capture-screenshots.mjs`, and `git diff --check` clean.

Scoped coverage added for song-search state hook, in-flight lock, reversible selection, canonical markup/classes, private fixture gating, Create `h1` stability, `song-lookups` body preservation, layout container hooks, and script registration.

## Screenshots

Stored under `design/review/round-011/`. Index: `design/review/round-011/README.md`.

Song states use `window.YatsnSongSearchFixtures` on `/create` when `data-private-build="1"`. Entry shots are viewport captures; result/selected rows may sit below the first viewport on 390 when the resume row is visible.

## Accessibility / contrast / motion / zoom

- Real `h1`, visible field labels, one `role="status"` region with `aria-live="polite"`.
- Result row is a single `button` activation target with title-first hierarchy; `Change song` is a quiet text button ≥44px tall.
- Focus uses the canonical 3px `outline` via `--elevation-focus-ring`.
- Reduced motion removes row/button travel on touched song-search surfaces; loading skeleton shimmer inherits existing global reduced-motion rules.
- Increased-contrast CSS forces white borders on song rows; the 390 evidence shot injects those rules because Puppeteer-core cannot emulate `prefers-contrast`.
- 200% zoom: no horizontal overflow at 320 entry, 390 results, 1440 selected (`review-notes.json`).

## Deviations / questions

1. Lookup API returns no artwork URL today; fallback art is intentional until a customer-safe artwork field is approved.
2. Draft still patches on lookup success (existing behavior). Confirmation gates only the People stage reveal, not persistence timing.
3. Resume row shows for fresh drafts (“saved as you go”) and for `?draft=` URLs; owners may prefer hiding it once a song is confirmed.
4. `docs/design/screens/create.md` was not in the repo; implementation followed `premium-product-screens.md` §1 and `core-components.md` §4.
5. Flutter mapping documented in `component-map.md`; no Dart code in this slice.

## Final commit

- **Branch:** `cursor/luminous-night-studio-phase2`
- **Commit:** _(filled after push)_

## Recommended next slice

**Phase 3: customer-safe Song DNA contract and selector** from `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`. Contract-first only; no UI depending on unapproved projection fields.
