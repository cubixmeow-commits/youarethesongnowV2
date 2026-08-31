# YouAreTheSongNow

# ChatGPT ↔ Cursor Design Handoff

**Branch:** `cursor/visual-music-adventure-94fc`  
**Last updated by Cursor:** 2026-08-30 (Round 005)  
**Active round:** 005 (awaiting ChatGPT visual review)

**Workflow roles**

- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` = canonical history (this file)
- `design/CHATGPT_NEXT_PASS.md` = ChatGPT inbox for the next pass

---

## Current Objective

Premium mobile-app-first creative experience expressed as a **private creative venue** (materials, light, pacing — not literal hotel UI). Flutter-portable tokens locked Round 005. Portrait deletion remains supported.

---

## Current Design State

### Navigation

Unchanged. Mobile bottom tabs + ~88px desktop rail. Brass-tinted active state (Round 005). Nav hotfix not applied.

### Create

- Header: **Create** (reduced literal venue copy)
- Song / People / Direction stages unchanged functionally
- Overview panel (formerly session board)
- Portrait delete intact
- Curator options slightly denser (Round 005)

### Home / Gallery

- Home: product eyebrow restored; interlude “Begin creating”
- Gallery: empty collection presentation with asset hook; exhibition spacing retained

### Design system (locked baseline)

Material hierarchy: **midnight → stone → lacquer → hairline/brass → artwork**

Docs: `DESIGN_SYSTEM.md`, `ASSET_INTEGRATION_MAP.md`, `FLUTTER_DESIGN_HANDOFF.md`

---

## Asset Inventory

Awaiting ChatGPT generation. Integration hooks verified and documented.

| Asset | Hook | Fallback |
| --- | --- | --- |
| app-atmosphere-haze | `--asset-atmosphere` | CSS gradients + grain |
| launch-mark-tile | `--asset-launch-mark` | CSS gradient mark |
| creative-session-backdrop | `--asset-session-phone/desktop` | Launch groove photos |
| empty-collection-still | `--asset-empty-collection` | CSS gradient frame |

---

## CURRENT HANDOFF

### Cursor → ChatGPT

#### Work Completed

1. Locked Round 004 venue language as Round 005 baseline
2. Targeted component consistency (nav, buttons, confirm sheet, curator tiles, gallery empty)
3. Reduced over-literal venue copy
4. Created `design/ASSET_INTEGRATION_MAP.md`
5. Created `design/FLUTTER_DESIGN_HANDOFF.md`
6. Screenshot pack + handoff updates
7. No intentional functional changes

#### Round 005 visual refinements

- Restrained brass on primary buttons, nav active, eyebrows
- Confirm sheet → lacquer/stone materials
- Direction style tiles smaller (76px min-height)
- Gallery empty state HTML + CSS asset hook
- Asset CSS variables expanded for drop-in

#### Copy audit

| Before (Round 004) | After (Round 005) |
| --- | --- |
| Private suite | Create |
| Enter the suite | Begin creating |
| A private creative venue (eyebrow) | Song · imagination · visual journey |
| In review / Your session | Overview / Your creation |

#### Files Changed

- `public/assets/css/app.css`
- `templates/pages/create.php`
- `templates/pages/welcome.php`
- `templates/pages/gallery.php`
- `design/DESIGN_SYSTEM.md`
- `design/ASSET_INTEGRATION_MAP.md` (new)
- `design/FLUTTER_DESIGN_HANDOFF.md` (new)
- `design/CHATGPT_NEXT_PASS.md` (consumed)
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
- `design/review/round-005/*`

#### Tests performed

`php tests/run.php` → **173 passed, 0 failed**

#### Screenshots

`design/review/round-005/` — see README for consistency audit notes.

#### Functionality Audit

- Portrait deletion: **intact**
- Navigation / routes / APIs / generation / payments: **unchanged**
- Gallery empty: presentation markup only; JS empty message preserved for SR via clipped status

#### Flutter readiness

Specification ready (`FLUTTER_DESIGN_HANDOFF.md`). Implementation not started. Assets not delivered.

#### Remaining visual inconsistencies

- Paywall panel still membership-shaped (deferred)
- Account forms utilitarian (acceptable Build 1)
- Create backdrop still uses interim groove photos until session backdrop asset lands

#### Questions for ChatGPT (max 3)

1. Freeze this venue system and generate atmosphere + mark assets next?
2. Is `FLUTTER_DESIGN_HANDOFF.md` sufficient to begin native UI scaffolding?
3. Any remaining brass/gold reads as casino rather than architectural light?

#### Recommended Next Pass

Stop for ChatGPT review of `design/review/round-005/`. Do not start Round 006 until new inbox instructions land.

---

### ChatGPT → Cursor

*(Awaiting Round 005 review / Round 006 inbox.)*

---

## ROUND HISTORY

### Round 001–003

Foundation, launchpad, portrait deletion. Merged.

### Round 004 — Premium private creative venue

Venue pivot from instrument UI. Merged via PR #13.

**ChatGPT response:** Round 005 inbox — lock system, asset map, Flutter handoff, targeted polish.

### Round 005 — System lock + Flutter handoff (2026-08-30)

**Cursor Report:** See CURRENT HANDOFF.  
**ChatGPT Response:** *(pending)*  
**Outcome:** *(pending)*

---

## DESIGN SCREEN / PAGE STATUS

| Screen / Area | Status | Last Changed | Notes |
| --- | --- | --- | --- |
| Create | Stable baseline | Round 005 | Copy toned; curator tiles denser |
| People / portraits | Stable baseline | Round 005 | Delete intact |
| Direction | Stable baseline | Round 005 | Smaller option tiles |
| Home | Stable baseline | Round 005 | Less literal venue copy |
| Gallery | Stable baseline | Round 005 | Empty state hook |
| Navigation | Stable | Round 005 | Brass active tint |
| Auth/Account | Light | Round 004 | Lacquer panels |
| Flutter spec | Ready | Round 005 | Handoff doc |

---

## DESIGN DECISIONS — DO NOT REGRESS

- Mobile-first; desktop expands same app
- Flutter/Dart future — use handoff docs
- Artwork dominates; avoid dating/SaaS/glass/rainbow
- Premium private venue via materials (not literal hotel UI)
- Material hierarchy: midnight → stone → lacquer → brass → artwork
- Portrait delete supported
- Bottom tabs + ~88px rail; no nav hotfix unless authorized
- Square artwork/portrait crops
- Brass sparingly — architectural light, not gold wash

---

## CURSOR OPERATING RULES

1. Read `design/CHATGPT_NEXT_PASS.md` + this file  
2. Implement the requested pass  
3. Capture `design/review/round-XXX/` when possible  
4. Update this canonical handoff  
5. Mark inbox consumed  
6. Commit/push  
7. Stop for ChatGPT review  

Keep human chat replies short; detailed reports live here.
