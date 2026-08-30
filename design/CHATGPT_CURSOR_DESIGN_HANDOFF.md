# YouAreTheSongNow

# ChatGPT ↔ Cursor Design Handoff

**Branch:** `cursor/visual-music-adventure-94fc`  
**Last updated by Cursor:** 2026-08-30 (Round 003)  
**Active round:** 003 (awaiting ChatGPT visual review)

**Workflow roles**

- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` = canonical history (this file)
- `design/CHATGPT_NEXT_PASS.md` = ChatGPT inbox for the next pass

---

## Current Objective

Premium mobile-app-first creative experience: **song → imagination → visual journey → collection**, with Flutter-portable tokens. Preserve functionality except where a pass explicitly authorizes a focused feature (Round 003: portrait deletion).

---

## Current Design State

### Navigation

Unchanged in Round 003 (user accepted bottom tabs; **hotfix not applied**). Mobile bottom tabs + compact ~88px desktop rail. Owner remains visually secondary.

### Create (Round 003)

- Session header + progress retained
- Song: **track source** grouped Artist/Song surface + retrieve action
- People: **portrait tray** / contact-sheet source material; square tiles; selected = included in session; secondary × delete with confirmation sheet
- Direction: denser tactile treatments; generate remains one strong primary action on session board
- Fields/behavior preserved aside from delete feature

### Home

Launchpad direction retained; no structural Home redesign this round.

### Color / type / tokens

Unchanged: graphite/midnight + amber + indigo; Instrument Serif + DM Sans.

---

## Design System

Core tokens unchanged (`design/DESIGN_SYSTEM.md`).

Round 003 presentation additions:

- `.track-source`, `.track-source__fields`, `.btn--retrieve`
- `.portrait-tray`, `.portrait-chip__select`, `.portrait-chip__delete`
- `.direction-controls`, `.direction-fieldset`
- `.confirm-sheet` (+ actions)

---

## Asset Inventory

System asset folder still empty / awaiting ChatGPT generation. Priorities unchanged:

1. `app-atmosphere-haze`
2. `launch-mark-tile`
3. `creative-session-backdrop`
4. `empty-collection-still`

No crop/safe-area changes required this round.

---

## Open Asset Requests

See `design/ASSET_REQUESTS.md`. No blocking dependency for Round 003.

---

## CURRENT HANDOFF

### Cursor → ChatGPT

#### Work Completed

Implemented Round 003 exactly:

1. Create instrument composition refinements (track source, portrait tray, direction controls)
2. Authorized portrait deletion (service already existed; hardened + UI + tests)
3. Did **not** apply nav hotfix / did not redesign nav
4. Did not reinvent Home
5. Screenshot pack + handoff updates

#### Portrait deletion implementation

| Layer | Detail |
| --- | --- |
| Route | `DELETE /api/v1/portraits/{portraitId}` (session + CSRF) |
| Authz | `PortraitService::findOwned` — other users → `not_found` / 404 |
| Persistence | Soft-delete `deleted_at`, then delete storage + thumb via `LocalStorage` (missing files OK) |
| UI | × on tile (44×44 hit area) → `<dialog>` confirm → API delete → remove from `state.portraits` + `selectedPortraitIds` + `patchDraft` |
| Direction section | Hides again if selection becomes empty after delete |

#### Files Changed

- `src/Portraits/PortraitService.php`
- `src/Api/ApiV1.php`
- `public/assets/js/app.js`
- `public/assets/css/app.css`
- `templates/pages/create.php`
- `tests/run.php`
- `design/CHATGPT_NEXT_PASS.md` (consumed)
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
- `design/review/round-003/*`

#### Tests performed

`php tests/run.php` → **173 passed, 0 failed**

Covered:

1. owned delete succeeds + soft-delete timestamp  
2. storage/thumb files removed  
3. absent from list after delete  
4. foreign user delete rejected; files intact  
5. already-deleted delete rejected  
6. replacement upload after delete succeeds  

UI selection-clean verified in browser during screenshot pass (delete confirm open; selection/tray update path exercised in code).

#### Screenshots

`design/review/round-003/` — see README there.

#### Functionality Audit

- Portrait deletion: **authorized intentional feature**
- No unrelated backend/API/database/auth/generation logic changes
- Song lookup/upload/generation/payment flows untouched
- Navigation hotfix **not** applied

#### Questions for ChatGPT (max 3)

1. Does the portrait tray + delete confirm read as creative source-material management (not dating/profile)?
2. Is Create’s track-source grouping enough, or should Round 004 push further presentation densification?
3. Generate `app-atmosphere-haze` + `launch-mark-tile` next, or more Create polish first?

#### Recommended Next Pass

Stop for ChatGPT review of `design/review/round-003/`. Do not start Round 004 until new inbox instructions land in `design/CHATGPT_NEXT_PASS.md`.

---

### ChatGPT → Cursor

*(Awaiting Round 003 review / Round 004 inbox.)*

---

## ROUND HISTORY

### Round 001 — Mobile-app-first foundation

Tokens, bottom tabs/rail, design docs, asset requests. Merged.

### Round 002 — Launchpad + Creative Session

Adventure hero, session header/board, Owner de-emphasis, screenshot protocol. Merged.

**ChatGPT response:** Round 003 inbox — keep direction; push Create instrument feel; implement portrait deletion; leave nav alone.

### Round 003 — Create instrument + portrait deletion (2026-08-30)

**Cursor Report:** See CURRENT HANDOFF.  
**ChatGPT Response:** *(pending)*  
**Outcome:** *(pending)*

---

## DESIGN SCREEN / PAGE STATUS

| Screen / Area | Status | Last Changed | Notes |
| --- | --- | --- | --- |
| Create | Needs review | Round 003 | Track source + tray + delete |
| People / portraits | Needs review | Round 003 | Deletion live |
| Direction | Needs review | Round 003 | Denser controls |
| Home | Stable | Round 002 | No structural change |
| Navigation | Stable | Round 002 | Hotfix skipped |
| Gallery empty | Waiting asset | — | |
| Reveal | Light | Round 001 | |

---

## DESIGN ISSUES / BACKLOG

- Atmosphere/mark assets still pending
- Empty gallery illustration pending
- Create may still want more instrument densification after review
- Paywall world preview deferred
- Flutter widget inventory still thin

---

## DESIGN DECISIONS — DO NOT REGRESS

- Mobile-first; desktop expands same app
- Flutter/Dart future
- Artwork dominates; avoid dating/SaaS/glass/rainbow
- Preserve functionality unless a pass explicitly authorizes a feature
- Bottom tabs + ~88px rail; do not apply old nav hotfix unless newly authorized
- Square album crops
- Amber stage light + indigo haze + graphite foundations
- Guest Home = launchpad; Create = creative session
- Portrait delete is a supported library/source-material action
- ChatGPT inbox = `CHATGPT_NEXT_PASS.md`; Cursor consolidates here

---

## CURSOR OPERATING RULES

1. Read `design/CHATGPT_NEXT_PASS.md` + this file  
2. Implement the requested pass  
3. Capture `design/review/round-XXX/` when possible  
4. Update this canonical handoff  
5. Mark inbox consumed  
6. Commit/push  
7. Stop for ChatGPT review — no speculative next pass  

Keep human chat replies short; detailed reports live here.
