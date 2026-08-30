# YouAreTheSongNow

# ChatGPT ↔ Cursor Design Handoff

**Branch:** `cursor/visual-music-adventure-94fc`  
**Last updated by Cursor:** 2026-08-30 (Round 004)  
**Active round:** 004 (awaiting ChatGPT visual review)

**Workflow roles**

- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` = canonical history (this file)
- `design/CHATGPT_NEXT_PASS.md` = ChatGPT inbox for the next pass

---

## Current Objective

Premium mobile-app-first creative experience: **song → imagination → visual journey → collection**, expressed as a **private creative venue** (not a music-device interface). Flutter-portable tokens. Portrait deletion remains supported.

---

## Current Design State

### Navigation

Unchanged (Round 004 design-only). Mobile bottom tabs + compact ~88px desktop rail. Owner visually secondary. Nav hotfix not applied.

### Create (Round 004)

- **Private suite** header + stages (not “creative session / movements”)
- Song: hairline-composed centerpiece fields; **Discover my song** premium service action
- People: gallery casting tray; portrait delete retained (subtle secondary ×)
- Direction: curator-style lacquer/stone selectors
- Session overview panel (was session board); `venue-progress` replaces playhead bar
- All fields/behavior preserved

### Home

Calmer launchpad hospitality copy; examples quieter; interlude “Enter the suite”. No structural rebuild.

### Gallery

Private collection framing; exhibition spacing; quieter metadata typography.

### Color / type / tokens

Graphite/midnight foundation refined. Amber evolved toward **brass/champagne architectural light**. Quieter indigo haze. Warm ivory text. New material tokens: lacquer, stone, hairline borders, subtle grain.

---

## Design System

See `design/DESIGN_SYSTEM.md`. Round 004 additions:

- `--color-surface-lacquer`, `--color-surface-stone`
- `--color-accent-brass`, `--color-border-hairline`
- `--shadow-inset-stone`, `--grain-opacity`
- `.venue-progress` (replaces `.playhead` presentation)

---

## Asset Inventory

System asset folder still empty / awaiting ChatGPT generation. Specs updated for premium venue philosophy in `design/ASSET_REQUESTS.md`.

Priorities:

1. `app-atmosphere-haze` — luxury interior atmospheric field
2. `launch-mark-tile` — portal/aperture mark (no music literalism)
3. `creative-session-backdrop` — private suite wall
4. `empty-collection-still` — gallery alcove empty state

---

## Open Asset Requests

See `design/ASSET_REQUESTS.md` (Round 004 reinterpretation). No blocking dependency for layout.

---

## CURRENT HANDOFF

### Cursor → ChatGPT

#### Work Completed

Implemented Round 004 exactly:

1. Visual philosophy shift: creative instrument → premium private creative venue
2. Create suite refinements (copy, materials, spacing, session overview)
3. Home/Gallery/Auth shared hospitality surfaces
4. Updated asset request specs for venue direction
5. Screenshot pack + handoff updates
6. No intentional functional/API/backend changes

#### Instrument → venue changes

| Removed/softened | Introduced |
| --- | --- |
| Boxed track-source console | Hairline-composed song centerpiece |
| Session board / creative session labels | Private suite / in review / stages |
| Playhead progress animation | Venue-progress glow line |
| Bright orange CTA accent | Brass/champagne gradient primary |
| Dense control-panel cards | Lacquer + stone surfaces, hairline structure |
| “Source material” portrait framing | Gallery casting selection tray |

#### Files Changed

- `public/assets/css/app.css`
- `templates/pages/create.php`
- `templates/pages/welcome.php`
- `templates/pages/gallery.php`
- `design/ASSET_REQUESTS.md`
- `design/DESIGN_SYSTEM.md`
- `design/CHATGPT_NEXT_PASS.md` (consumed)
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
- `design/review/round-004/*`

#### Tests performed

`php tests/run.php` → **173 passed, 0 failed**

#### Screenshots

`design/review/round-004/` — see README there.

#### Functionality Audit

- Portrait deletion: **intact** (Round 003 feature preserved)
- Navigation: **unchanged**
- No APIs, backend, database, auth, generation, payment, or routing changes
- Presentation copy/button label only: “Discover my song” (same submit behavior as “Find my song”)

#### Flutter portability notes

- New colors are flat OKLCH tokens (no CSS-only tricks required)
- Grain overlay could become a small tiled raster asset or `ShaderMask` in Flutter
- Gradients on primary button and surfaces are simple linear stacks reproducible in `BoxDecoration`
- Avoided heavy backdrop-filter dependence for core structure (chrome only)

#### Questions for ChatGPT (max 3)

1. Does Create now read as a hosted private suite rather than equipment/console?
2. Is brass/champagne accent restrained enough for premium venue (not casino cliché)?
3. Prioritize atmosphere + mark asset generation, or another venue surface pass?

#### Recommended Next Pass

Stop for ChatGPT review of `design/review/round-004/`. Do not start Round 005 until new inbox instructions land in `design/CHATGPT_NEXT_PASS.md`.

---

### ChatGPT → Cursor

*(Awaiting Round 004 review / Round 005 inbox.)*

---

## ROUND HISTORY

### Round 001 — Mobile-app-first foundation

Tokens, bottom tabs/rail, design docs, asset requests. Merged.

### Round 002 — Launchpad + Creative Session

Adventure hero, session header/board, Owner de-emphasis, screenshot protocol. Merged.

**ChatGPT response:** Round 003 inbox — keep direction; push Create instrument feel; implement portrait deletion; leave nav alone.

### Round 003 — Create instrument + portrait deletion (2026-08-30)

**Cursor Report:** Track source, portrait tray, delete feature.  
**ChatGPT Response:** Round 004 inbox — shift to premium private venue; keep deletion + nav.  
**Outcome:** Merged direction into Round 004 spec.

### Round 004 — Premium private creative venue (2026-08-30)

**Cursor Report:** See CURRENT HANDOFF.  
**ChatGPT Response:** *(pending)*  
**Outcome:** *(pending)*

---

## DESIGN SCREEN / PAGE STATUS

| Screen / Area | Status | Last Changed | Notes |
| --- | --- | --- | --- |
| Create | Needs review | Round 004 | Private suite venue |
| People / portraits | Needs review | Round 004 | Gallery tray + delete intact |
| Direction | Needs review | Round 004 | Curator controls |
| Home | Needs review | Round 004 | Calmer hospitality |
| Gallery | Needs review | Round 004 | Collection framing |
| Navigation | Stable | Round 002 | Hotfix skipped |
| Auth/Account | Light polish | Round 004 | Lacquer panels |
| Reveal | Light | Round 001 | |

---

## DESIGN ISSUES / BACKLOG

- Atmosphere/mark assets still pending (specs updated)
- Empty gallery illustration pending
- Paywall world preview deferred
- Flutter widget inventory still thin
- Grain overlay may become raster asset for native parity

---

## DESIGN DECISIONS — DO NOT REGRESS

- Mobile-first; desktop expands same app
- Flutter/Dart future
- Artwork dominates; avoid dating/SaaS/glass/rainbow
- Preserve functionality unless a pass explicitly authorizes a feature
- Bottom tabs + ~88px rail; do not apply old nav hotfix unless newly authorized
- Square album crops
- Premium private venue (not music-device / instrument UI)
- Brass/champagne architectural light + quiet indigo + graphite foundations
- Guest Home = launchpad; Create = private suite
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
