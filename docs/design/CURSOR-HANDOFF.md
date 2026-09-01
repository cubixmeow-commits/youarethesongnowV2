# NEXT DIRECTIVE — Round 012 backend-first POV integration

**Date:** 2026-09-01  
**Working branch:** `main`  
**Status:** Ready for Cursor

Owner sequencing decision: implement and validate the POV-derived Visual Narrative Planning Layer before continuing customer-facing Song DNA or broader design.

Read:

- `design/CHATGPT_NEXT_PASS.md`
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
- `docs/design/process/VISUAL-NARRATIVE-PLANNING-LAYER.md`
- https://github.com/cubixmeow-commits/POV-Campaign-Engine

Implement the hidden/backend planning pipeline against the richest existing internal Song DNA: Visual Campaign Board → exactly three ranked directions → selected Visual Scene Contract → portrait roles → structured prompt compiler → current development generation path.

Preserve a safe fallback to the current compiler. Add five fixtures and old/new comparisons. Do not add the Song DNA selector, Explore Options, Fine Tune, or unrelated design yet.

After verification, update both handoffs with commit, tests, sanitized artifacts, prompt comparison, and any controlled image evaluation. Push `main`, stop for GPT review, and do not deploy.

---

# CURSOR-HANDOFF — Luminous Night Studio Phase 2 published

**Date:** 2026-09-01
**Published to:** `main`
**Reviewed branch head:** `ab6f82a255cda0783863791197942a3b93fbca7c`
**Implementation commit:** `d567188e3689ebecceeaaff20d237794dfcdea22`
**Phase:** 2 — Create entry and existing-contract song selection
**Status:** Independently reviewed and published to `main`. Ready for private deployment review. No Hostinger deployment performed in this publication.

## Package / slice verification

- `php tests/run.php`: **1092 passed, 0 failed**
- Runtime/backend/API/migration changes: **none**
- Song lookup endpoint: still `POST /api/v1/song-lookups` with `{ artist, title }`
- Draft patch: still `songLookupId` on successful lookup
- Privacy/rate-limit behavior: preserved (server-side)
- Song DNA API/persistence: **not started**

## Review evidence

- Pack: `design/review/round-011/`
- Mobile, responsive, state, focus, contrast, motion, and zoom evidence approved
- Honest `create-entry-*` captures at `scrollY = 0`; optional `create-form-*` shots; compound group-visibility harness

## Changed files (Phase 2)

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

## Preserved contracts

- **Page hierarchy:** one stable `<h1>Choose your song</h1>`; stage title remains `The song` as `h2`.
- **Primary action:** `Find this song` uses canonical `.yatsn-btn--primary` at 52px height.
- **Lookup contract:** unchanged request/response. `app.js` still posts `{ artist, title }` and patches `songLookupId` after a successful lookup.
- **Selection flow:** lookup renders an artwork-led result button; user confirms by activating it; People stage stays hidden until confirmation. `Change song` reverses confirmation without clearing typed fields.
- **In-flight guard:** `song-search.js` blocks duplicate submits while lookup is pending.
- **Result semantics:** `data-song-results` is `role="region"` with `aria-label="Song match"`. Selectable row is a native `<button>` with `aria-label` containing title, artist, and match status.
- **Resume row:** hidden in Phase 2 until a genuine alternate-draft contract exists.
- **Recent creations:** semantic `<ul>` / `<li>` list with one anchor per item.
- **Mobile spacing:** compact Create entry spacing at `max-width: 599px` only.

## Recommended next slice

**Phase 3: customer-safe Song DNA contract and selector** from `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`. Contract-first only; do not implement UI against unapproved projection fields.
