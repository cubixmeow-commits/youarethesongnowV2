# YouAreTheSongNow

# ChatGPT ↔ Cursor Design Handoff

**Branch:** `cursor/visual-music-adventure-94fc`  
**Last updated by Cursor:** 2026-08-30 (Pass 2 / Round 002)  
**Active round:** 002 (awaiting ChatGPT visual review)

**Workflow roles**

- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` = **canonical history** (this file), maintained by Cursor after each round.
- `design/CHATGPT_NEXT_PASS.md` = **ChatGPT inbox** for the next pass instructions.

---

## Current Objective

Transform the existing application into a premium mobile-app-first experience inspired by music, creativity, cinematic imagery, and adventure, while preserving application functionality.

Target read: **song → imagination → visual journey → collection**  
UI should feel like a premium creative instrument wrapped around artwork, not a membership funnel.

---

## Current Design State

Pass 2 builds on Pass 1 tokens/chrome.

### Navigation model

- Mobile: bottom tabs (`Create`, `Gallery`, `Account`; `Owner` de-emphasized as secondary/private)
- Desktop: compact ~88px left rail (unchanged width per ChatGPT)
- Guest: `Sign in` tab only

### Color system

Unchanged foundation: graphite/midnight + amber stage-light accent + indigo haze. Artwork supplies vivid color. Amber used as light/selection/action, not large brand fill.

### Typography

Instrument Serif + DM Sans; tokenized roles unchanged.

### Surfaces

Open composition preferred. Auth uses hairline sheet framing (less floating card). Create summary = **session board** (album/sleeve feel). Session header sits above movements.

### Artwork treatment

- Guest hero now uses **adventure/solo** launch imagery (`example-solo`), not couple embrace as brand identity
- Examples ordered: solo → energy → intimate (shared worlds last, still valid)
- Gallery/reveal/portraits remain square album-style

### Mobile layout

Launchpad home; Create session header + progress chips; stacked session board; bottom tabs; safe areas.

### Desktop layout

Left rail preserved; Create = workspace + session board; intentional separators; atmosphere still CSS-only pending assets.

### Creation interface

Presentation-only **Creative Session**: header (art placeholder, title synced to song lookup, progress 01/02/03), movements as one composition, session board instead of “Summary/checkout” framing. **All fields and submit behavior preserved.** Tiny JS presentation sync updates session title + progress emphasis when sections unhide—no business logic changes.

### Responsive behavior

Same as Pass 1 (phone → tablet → desktop expansion of one app).

---

## Design System

Unchanged core tokens from Pass 1 (`design/DESIGN_SYSTEM.md` + `public/assets/css/app.css`).

Pass 2 additions (presentation classes):

- `.hero--launchpad`
- `.session-header`, `.session-progress`, `.session-board`
- `.movement--primary`, `.movement__lead`
- `.app-nav__item--secondary` (Owner)
- `.btn--generate`, `.paywall-panel`
- `.examples__intro`

---

## Asset Inventory

| Filename | Path | Purpose | Status |
| --- | --- | --- | --- |
| `example-solo-*.webp` | `public/assets/images/launch/` | Now primary guest hero + example | integrated |
| `example-energy-*.webp` | `public/assets/images/launch/` | Example adventure | integrated |
| `example-intimate-*.webp` | `public/assets/images/launch/` | Example shared world (de-emphasized in order) | integrated |
| `hero-listening-room-*.webp` | `public/assets/images/launch/` | Former couple hero | retired from primary hero (file retained) |
| `layout-groove-*` / `layout-mobile-*` | `public/assets/images/launch/` | Temp create backdrop | integrated stand-in |
| `layout-interlude-*` | `public/assets/images/launch/` | Interlude | integrated |
| system assets | `public/assets/images/system/` | ChatGPT deliveries | **empty / awaiting** |

CSS hooks remain: `--asset-atmosphere`, `--asset-session-phone`, `--asset-session-desktop`.

---

## Open Asset Requests

Priority unchanged per ChatGPT Pass 2:

1. **`app-atmosphere-haze`** (phone + desktop) — highest global impact  
2. **`launch-mark-tile`** — replace CSS brand mark placeholder  
3. `creative-session-backdrop`  
4. `empty-collection-still`  
5. `paywall-world-preview` (defer until Create/home identity is solid)

Full specs: `design/ASSET_REQUESTS.md`. No spec changes required after Pass 2 composition, except:

- Brand mark should drop into `.brand__mark` / optional `.session-header__art` once delivered
- Atmosphere should assign to `--asset-atmosphere`
- Session backdrop should replace groove stand-ins via `--asset-session-*`

---

## CURRENT HANDOFF

### Cursor → ChatGPT

#### Work Completed

Implemented **Pass 2** exactly from `design/CHATGPT_NEXT_PASS.md`:

- Preserved Pass 1 foundation (tabs/rail/tokens/palette/square crops/functionality)
- Guest Home → app launchpad (adventure hero, Start with a song, example reorder)
- Create → Creative Session instrument presentation (header, progress, session board)
- Reduced dating/membership visual cues (hero, auth framing, people remain square chips, paywall quieter panel framing)
- Owner nav visually secondary
- Asset hooks kept ready; no fake decorative substitutes invented
- Screenshot protocol + review pack added
- Handoff workflow roles documented

#### Files Changed

- `templates/pages/welcome.php`
- `templates/pages/create.php`
- `templates/layouts/main.php`
- `public/assets/css/app.css`
- `public/assets/js/app.js` (presentation-only: session title + progress emphasis sync)
- `design/CHATGPT_NEXT_PASS.md` (marked consumed)
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` (this update)
- `design/review/round-002/*` (screenshots + README)

#### Visual Decisions

- Couple hero retired from brand identity; solo/adventure defines launch
- CTA label “Start with a song” (same `/sign-in` href)
- Create reads as session assembly; summary renamed/framed as session board
- Owner remains available but visually quieter
- Palette not re-litigated

#### Mobile Result

See `design/review/round-002/home-mobile-390.png`, `create-mobile-390.png`, `gallery-mobile-390.png`.

Launchpad hero + bottom Sign in; Create shows Creative Session header and 01/02/03 chips; Owner label muted/uppercase.

#### Desktop Result

See `home-desktop-1440.png`, `create-desktop-1440.png`, `gallery-desktop-1440.png` (actual ~1240px wide browser capture).

Left rail retained; Create shows session board on the right; guest home shows adventure hero with Sign in in rail.

#### Dating / SaaS cue audit

| Cue | Action |
| --- | --- |
| Couple-as-brand hero | Reduced — replaced with solo adventure hero |
| Intimate example first | Reduced — moved last |
| Create as form sections | Reduced — session header + movements + board |
| Summary as checkout card | Reduced — session board framing |
| Auth floating membership card | Reduced — hairline open panel |
| Circular portraits | Already square — kept |
| Owner as equal consumer tab | Reduced — secondary styling |
| Flat dark website emptiness | Partially addressed via composition; still needs atmosphere asset |
| Paywall membership tone | Lightly quieted visually; copy untouched; asset deferred |

#### Functionality Audit

**No intentional backend/API/database/auth/generation/route/business-logic changes.**  
Forms, fields, submits, and permissions unchanged. JS only syncs presentational session header/progress from existing create state.

#### Assets Needed From ChatGPT

Still: **1) app-atmosphere-haze**, **2) launch-mark-tile** first.

#### Problems / Uncertainties

- Session art placeholder is still CSS glow (awaits launch-mark / session art)
- Empty gallery still text-only (awaits empty-collection-still)
- Desktop capture width ~1240 not full 1440
- Create may still feel somewhat form-like until atmosphere/backdrop assets land
- Paywall not screenshot (needs unsubscribed path)

#### Questions for ChatGPT (max 3)

1. After screenshots, does guest Home read as a creative app launchpad, or still marketing?
2. Is Create’s session header + board enough of an “instrument,” or should Pass 3 push further **presentation-only** densification (without field removal)?
3. Confirm next asset generation order is still haze → mark, or switch after seeing screenshots?

#### Recommended Next Pass

Do **not** start Pass 3 until ChatGPT reviews `design/review/round-002/` and writes new instructions into `design/CHATGPT_NEXT_PASS.md`.

---

### ChatGPT → Cursor

*(Awaiting ChatGPT visual review of Round 002 screenshots and next inbox in `design/CHATGPT_NEXT_PASS.md`.)*

---

## ROUND HISTORY

### Round 001 — Mobile-app-first system foundation (2026-08-30)

**Cursor Report**  
Tokens, bottom tabs / left rail, creative-session CSS foundation, design docs, asset request manifest. PR #9 merged.

**ChatGPT Response**  
Recorded in `design/CHATGPT_NEXT_PASS.md` (Pass 2 instructions): keep foundation; fix visual story; launchpad home; Create as instrument; keep 88px rail; prioritize atmosphere + mark assets; add screenshot protocol; split inbox vs canonical handoff.

**Outcome**  
Accepted → implemented as Round 002 / Pass 2.

### Round 002 — Creative session + launchpad (2026-08-30)

**Cursor Report**  
See CURRENT HANDOFF above. Screenshots in `design/review/round-002/`.

**ChatGPT Response**  
*(pending)*

**Outcome**  
*(pending visual review)*

---

## DESIGN SCREEN / PAGE STATUS

| Screen / Area | Status | Last Changed | Notes |
| --- | --- | --- | --- |
| Global shell | In progress | Round 002 | Atmosphere asset still pending |
| Mobile navigation | Improved | Round 002 | Owner secondary |
| Desktop shell | Stable | Round 002 | 88px rail kept |
| Home (guest) | Needs review | Round 002 | Launchpad + adventure hero |
| Create | Needs review | Round 002 | Session instrument presentation |
| Find My Song | Unchanged behavior | Round 002 | Same fields |
| Reveal | Lightly styled | Round 001 | Not re-shot (no images) |
| Gallery | Needs empty asset | Round 002 | Empty state text only |
| Account | Light | Round 001 | Clarity first |
| Sign-in | Improved | Round 002 | Less card funnel |
| Paywall | Deferred polish | Round 002 | Presentation quieted only |
| Owner | De-emphasized | Round 002 | Route intact |
| Review screenshots | Added | Round 002 | `design/review/round-002/` |

---

## DESIGN ISSUES / BACKLOG

- Atmosphere still CSS gradients (needs `app-atmosphere-haze`)
- Brand mark still CSS placeholder (needs `launch-mark-tile`)
- Empty gallery needs premium still
- Create may still read partly as a form until backdrop/ denser instrument pass
- Desktop emptiness partially improved; needs haze + possible session backdrop
- Paywall world preview deferred
- Flutter component inventory still thin beyond tokens
- `site/` marketing landing still separate from app shell identity

---

## DESIGN DECISIONS — DO NOT REGRESS

- Mobile app is the primary design target.
- Desktop is an expanded expression of the mobile app.
- Future implementation will be Flutter/Dart.
- Generated imagery should visually dominate.
- Avoid dating-site aesthetics.
- Avoid generic SaaS aesthetics.
- Avoid excessive glassmorphism / rainbow gradients / unnecessary cards.
- Preserve existing functionality while refining design.
- Prefer Flutter-portable primitives.
- Bottom tabs (mobile) / compact ~88px left rail (desktop).
- Square album-style crops (not profile circles).
- Accent = amber stage light; haze = indigo; foundations = midnight/graphite.
- **Do not solve dating associations by changing the palette again** — fix imagery/composition/story.
- Guest Home should feel like an **app launchpad**, not a marketing/dating landing.
- Create should feel like a **creative session/instrument**; fields remain; no accordion/wizard logic in design-only passes unless later authorized.
- Owner may remain for private Build 1 but must not read as a primary consumer destination.
- ChatGPT writes next instructions to `CHATGPT_NEXT_PASS.md`; Cursor consolidates into this canonical handoff.
- Significant rounds include screenshots under `design/review/round-XXX/` when environment allows.

---

## CURSOR OPERATING RULES

When instructed to continue from the design handoff:

1. Read `design/CHATGPT_NEXT_PASS.md` (ChatGPT inbox) **and** this canonical handoff.
2. Read **ChatGPT → Cursor** / inbox instructions fully.
3. Inspect referenced assets/screenshots.
4. Implement the requested design pass (design-only unless explicitly told otherwise).
5. Do not make unrelated functional changes.
6. Update Current Design State, Asset Inventory, Open Asset Requests.
7. Capture screenshots into `design/review/round-XXX/` when possible; add README.
8. Write Cursor → ChatGPT report here; archive prior exchange into Round History.
9. Mark `CHATGPT_NEXT_PASS.md` consumed (or leave for ChatGPT to overwrite).
10. Commit/push so ChatGPT can review via GitHub.
11. Stop for visual review — do not speculate Pass N+1.

Detailed design communication belongs in these Markdown files. Cursor’s chat reply to the human should stay very short.
