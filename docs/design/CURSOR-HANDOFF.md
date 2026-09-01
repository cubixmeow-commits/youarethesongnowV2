# CURSOR-HANDOFF — Luminous Night Studio Phase 2 complete

**Date:** 2026-09-01
**Working branch:** `cursor/luminous-night-studio-phase2`
**Base:** `main` @ `795fc2a`
**Phase:** 2 — Create entry and existing-contract song selection
**Status:** Final focused correction pass complete. Ready for Codex review. Do not merge to `main`.

## Package / slice verification

- `php tests/run.php`: **1092 passed, 0 failed**
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

- **Page hierarchy:** one stable `<h1>Choose your song</h1>`; stage title remains `The song` as `h2`.
- **Primary action:** `Find this song` uses canonical `.yatsn-btn--primary` at 52px height.
- **Lookup contract:** unchanged request/response. `app.js` still posts `{ artist, title }` and patches `songLookupId` after a successful lookup.
- **Selection flow:** lookup renders an artwork-led result button; user confirms by activating it; People stage stays hidden until confirmation. `Change song` reverses confirmation without clearing typed fields.
- **In-flight guard:** `song-search.js` blocks duplicate submits while lookup is pending.
- **Result semantics:** `data-song-results` is `role="region"` with `aria-label="Song match"`. Selectable row is a native `<button>` with `aria-label` containing title, artist, and match status. No custom Enter/Space keydown handler on the native button.
- **Resume row:** hidden in Phase 2. No list-drafts or alternate-draft contract exists to show a meaningful resume action without a self-link.
- **Recent creations:** semantic `<ul>` / `<li>` list with one anchor per item; no invalid `role="list"` without `listitem`.
- **Hidden state rows:** `[hidden]` song-search placeholders use `display: none !important` so `.yatsn-song-result { display: grid }` cannot leak the loading skeleton into the entry view.
- **Mobile spacing:** compact Create entry spacing at `max-width: 599px` only so 390px honest entry evidence shows identity, progress, song context, both fields, and primary action at `scrollY = 0`.
- **Artwork:** uses `artworkUrl` / `thumbnailUrl` when present; restrained circular fallback otherwise.
- **Recent creations row:** optional from existing `GET /api/v1/images` only.
- **Active portrait summary:** not added — no explicit server active/default portrait contract yet.

## Tests

```text
php tests/run.php
=== Results: 1092 passed, 0 failed ===
```

Also: `php -l` on touched PHP templates, `node --check` on `song-search.js` / `app.js` / `capture-screenshots.mjs`, and `git diff --check` clean.

Scoped coverage includes song-search state hook, in-flight lock, reversible selection, labelled result region, native-button activation guard (no redundant keydown), hidden resume row, hidden-state CSS guard, compact mobile spacing guard, recent-creations list semantics, private fixture gating, Create `h1` stability, and `song-lookups` body preservation.

## Screenshots

Stored under `design/review/round-011/`. Index: `design/review/round-011/README.md`.

- `create-entry-*` captured at `scrollY = 0` without pre-scroll (honest first-open evidence).
- Optional `create-form-*` shots provide form-focused scroll evidence without substituting for entry captures.
- Compound state screenshots use `positionAndAssertGroup()` so every required member is simultaneously visible below the sticky top bar; capture fails when any member has `targetVisible: false`. Bounds recorded per member in `review-notes.json`.

## Accessibility / contrast / motion / zoom

- Real `h1`, visible field labels, one `role="status"` region with `aria-live="polite"`.
- Result button accessible name includes title, artist, and match qualifier.
- Recent creations use valid list/listitem semantics.
- Focus uses the canonical 3px `outline` via `--elevation-focus-ring`.
- Reduced motion removes row/button travel on touched song-search surfaces.
- Increased-contrast CSS forces white borders on song rows; the 390 evidence shot injects those rules because Puppeteer-core cannot emulate `prefers-contrast`.
- 200% zoom: no horizontal overflow at 320 entry, 390 results, 1440 selected (`review-notes.json`).

## Deviations / questions

1. Lookup API returns no artwork URL today; fallback art is intentional until a customer-safe artwork field is approved.
2. Draft still patches on lookup success (existing behavior). Confirmation gates only the People stage reveal.
3. Resume row deferred until a contract can identify a genuinely different resumable creation without a self-link.
4. `docs/design/screens/create.md` was not in the repo; implementation followed `premium-product-screens.md` §1 and `core-components.md` §4.
5. Flutter mapping documented in `component-map.md`; no Dart code in this slice.

## Final commit

- **Branch:** `cursor/luminous-night-studio-phase2`
- **Branch head:** `d567188e3689ebecceeaaff20d237794dfcdea22`

## Recommended next slice

**Phase 3: customer-safe Song DNA contract and selector** from `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`. Contract-first only.
