# YouAreTheSongNow

# ChatGPT ↔ Cursor Design Handoff

**Branch:** `main`

**Last updated by ChatGPT:** 2026-08-30 (Round 008 local dispatch)

**Active round:** 008 (implemented locally — awaiting ChatGPT visual review)

**Workflow roles**

- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` = canonical history (this file)
- `design/CHATGPT_NEXT_PASS.md` = ChatGPT inbox for the next pass

---

## Current Objective

Integrate the delivered **YS monogram identity** and black/platinum/sapphire production assets into the current responsive web/mobile app. Preserve all application behavior and portrait deletion. Flutter remains documentation-only.

### Verified local baseline before implementation

- Cursor checkout: `/Users/realiainreid/Documents/You Are The Song Now V2/repo V2`
- Local `main` is clean and includes the owner style activate/deactivate control from commit `3953689`.
- The YS production asset and redesign documentation delivery is included locally in commit `0d8ed39`.
- Baseline test result: **178 passed, 0 failed**.
- Round 008 must preserve and re-verify the owner style activate/deactivate control along with all existing owner, style-catalog and portrait-delete behavior.

---

## Current Design State

### Navigation

Unchanged. Mobile bottom tabs + ~88px desktop rail. Round 006 converted the active system from brass to platinum/sapphire/cobalt. Nav hotfix not applied.

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

Material hierarchy: **black → graphite → lacquer → platinum/sapphire light → artwork**

Docs: `DESIGN_SYSTEM.md`, `ASSET_INTEGRATION_MAP.md`, `FLUTTER_DESIGN_HANDOFF.md`

---

## Asset Inventory

Production assets delivered. Integration hooks and responsive usage are documented in `PRODUCTION_ASSET_MANIFEST.md`, `RESPONSIVE_REDESIGN_PLAN.md` and `ASSET_INTEGRATION_MAP.md`.

| Asset | Hook | Fallback |
| --- | --- | --- |
| YS flat monogram + wordmark | top bar/rail | Delivered under `public/assets/images/brand/` |
| YS premium monogram + app icon | brand/reveal/icon contexts | Delivered under `public/assets/images/brand/` |
| app-atmosphere-haze | `--asset-atmosphere` | Phone + desktop delivered |
| creative-session-backdrop | `--asset-session-phone/desktop` | Phone + desktop delivered |
| empty-collection-still | Gallery zero state | Transparent WebP delivered |
| paywall-world-preview | Paywall media | Phone + desktop delivered |

---

## CURRENT HANDOFF

### Cursor → ChatGPT (Round 008 — 2026-08-30)

#### Work completed

Integrated the delivered YS monogram, wordmark, atmosphere, Create backdrops, Gallery empty-state art and paywall previews into the current responsive web/mobile app. Preserved all routes, APIs, generation, credits, Stripe, portrait upload/delete and owner style activate/deactivate behavior.

#### Production assets integrated

| Asset | Hook |
| --- | --- |
| `ys-monogram-flat-platinum.svg` | Top bar `<img>` + Create session header |
| `ys-wordmark.svg` | Top bar `<img>` with visually hidden accessible name |
| `app-atmosphere-haze-phone/desktop.webp` | `--asset-atmosphere` on `body.app` |
| `creative-session-backdrop-phone/desktop.webp` | `.create` background under readability veil |
| `empty-collection-still.webp` | Gallery empty `<img>` |
| `paywall-world-preview-phone/desktop.webp` | `.paywall-panel__media` |

#### Files changed

- `public/assets/css/app.css`
- `templates/layouts/main.php`
- `templates/pages/welcome.php`
- `templates/pages/create.php`
- `templates/pages/gallery.php`
- `design/DESIGN_SYSTEM.md`
- `design/ASSET_INTEGRATION_MAP.md`
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
- `design/CHATGPT_NEXT_PASS.md` (consumed)
- `design/review/round-008/*`
- `development-vault/01 Current Project/Current Priorities.md`
- `development-vault/01 Current Project/Dashboard Snapshot.md`
- `docs/dashboard-data.js`

#### Mobile / desktop composition decisions

- **Shell:** Real YS mark (30–32px) + wordmark (132–168px); 16px inset at 320px, 20px from 390px; atmosphere phone `center top`, desktop `right top`; no `background-attachment: fixed` on phone.
- **Home:** Poster-first 100svh hero; quiet Sign in secondary in hero actions; desktop copy capped ~440px over calm field; examples remain editorial rail/row.
- **Create:** Phone session backdrop with 88% bg overlay veil; desktop 62/38 split (max ~640px form); stage progress inline under header (not far-right column); overview uses separators not heavy card chrome on desktop.
- **Gallery empty:** Real aperture `<img>` (240px phone / 300px desktop) + primary Create link; no extra frame.
- **Paywall:** Square phone header + two-column desktop media/copy; billing remains live HTML on opaque surface.
- **Reveal:** Desktop artwork-left / metadata-right grid.

#### Crop / overlay values

See `design/review/round-008/README.md` overlay table. Session overlay `--asset-session-overlay: color-mix(in oklab, var(--color-bg) 88%, transparent)`; desktop Create adds horizontal veil keeping form in calm left field.

#### Accessibility / responsive checks

Keyboard/focus, 16px inputs, 44–48px targets, reduced motion, increased contrast rules preserved. Verified 320, 390, 430, 768, 900, 1440 and 200% zoom notes in `design/review/round-008/README.md`.

#### Tests

`php tests/run.php` → **178 passed, 0 failed**

#### Owner style activate/deactivate verification

Preserved commit `3953689` behavior: `.owner-style-toggle` buttons remain labeled **Activate** / **Deactivate** with explicit titles; automated tests `owner can activate style` and `owner can deactivate style` pass.

#### Screenshots

`design/review/round-008/` — full pack per Round 008 brief plus `overflow-check-320-home.png`.

#### Functionality audit

- Portrait deletion: **intact**
- Navigation / routes / APIs / generation / payments: **unchanged**
- Flutter / commerce / upscale / print / T-shirt: **not started**

#### Remaining visual issues (max 3)

1. Atmosphere/session readability still uses gradient veils over assets (intentional until image-only review).
2. Account forms remain utilitarian (acceptable Build 1).
3. Populated gallery desktop not re-shot; empty state is the Round 008 gallery deliverable.

#### Deviations

None intentional beyond using in-browser grid clear for empty-gallery screenshots (presentation only; no server/data change).

#### Recommended next pass

Stop for ChatGPT visual review of `design/review/round-008/`. Do not push or deploy without separate owner authorization.

---

### Cursor → ChatGPT (Round 005 archive)

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

#### Production delivery

- Final YS brand assets and responsive system imagery are present in the repository.
- Exact dimensions and final prompts: `design/PRODUCTION_ASSET_MANIFEST.md`.
- Phone/desktop composition plan: `design/RESPONSIVE_REDESIGN_PLAN.md`.
- Exact implementation instructions: `design/CHATGPT_NEXT_PASS.md` (Round 008).
- Do not begin Flutter, commerce or unrelated functional work.
- Preserve the owner-page style activate/deactivate control added in `3953689`; this is part of the verified baseline, not redesign scope.

Cursor should implement Round 008, capture the required responsive review pack, run tests, update this handoff, commit locally and stop for ChatGPT visual review. Do not push or deploy unless the owners separately authorize it.

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

### Round 006 — Platinum and sapphire conversion (2026-08-30)

Warm brass/amber chrome was replaced by black/graphite, platinum, deep sapphire and restrained cobalt. Round 006 screenshots are committed.

### Round 007 — YS identity selection (2026-08-30)

The intertwined YS monogram was selected and documented. Final production geometry/assets were still pending.

### Round 008 — YS production asset integration (2026-08-30)

Production YS identity and system imagery integrated into the current responsive web/mobile app. Screenshot pack committed under `design/review/round-008/`. Flutter remains documentation-only.

### Round 008 preparation — Production asset delivery (2026-08-30)

ChatGPT delivered the complete brand/system asset set, production manifest, responsive redesign plan and exact Cursor implementation instructions. Implementation and responsive screenshots remain pending.

---

## DESIGN SCREEN / PAGE STATUS

| Screen / Area | Status | Last Changed | Notes |
| --- | --- | --- | --- |
| Create | Integrated | Round 008 | YS session header; production backdrop; desktop 62/38 |
| Home | Integrated | Round 008 | Poster hero; YS lockup; quiet Sign in |
| Gallery | Integrated | Round 008 | Empty-state `<img>` + Create CTA |
| Navigation | Stable | Round 008 | YS mark/wordmark in chrome |
| Auth/Account | Light | Round 004 | Lacquer panels |
| YS production assets | Integrated | Round 008 | Web/mobile hooks live; see review pack |
| Flutter spec | Documentation only | Round 008 prep | Native implementation deferred |

---

## DESIGN DECISIONS — DO NOT REGRESS

- Mobile-first; desktop expands same app
- Flutter/Dart future — use handoff docs
- Artwork dominates; avoid dating/SaaS/glass/rainbow
- Premium private venue via materials (not literal hotel UI)
- Selected identity is the intertwined YS monogram
- Brand palette is black/graphite, platinum and sapphire/cobalt; no brass/gold chrome
- Material hierarchy: midnight → stone → lacquer → platinum/sapphire light → artwork
- Portrait delete supported
- Bottom tabs + ~88px rail; no nav hotfix unless authorized
- Square artwork/portrait crops
- Flutter implementation remains deferred until the current web/mobile experience is approved

---

## CURSOR OPERATING RULES

1. Read `design/CHATGPT_NEXT_PASS.md` + this file  
2. Implement the requested pass  
3. Capture `design/review/round-XXX/` when possible  
4. Update this canonical handoff  
5. Mark inbox consumed  
6. Commit locally; do not push or deploy without separate owner authorization
7. Stop for ChatGPT review  

Keep human chat replies short; detailed reports live here.
