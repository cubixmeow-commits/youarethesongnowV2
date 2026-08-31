# CURSOR-HANDOFF — Design Operating System

**Date:** 2026-08-31  
**Branch:** `cursor/design-system-audit-95fa`  
**Primary delivery commit:** `be911ac1db56e05e5157301ca293a95922b6a5bb`  
**Branch tip:** `d7f9fe0f07e6f6f0f471cdd00c967c6d96c443ee`
**Phase:** 1 — Audit & structure (**complete; awaiting GPT / design-director review**)  
**Do not begin Phase 2** until this handoff is reviewed.

---

## What was inspected

- Vault: `development-vault/START HERE.md`, Current Priorities, Current Architecture, Decision Index, V2 Visual Design Direction
- Build contracts: `CURSOR-BUILD-1.md` (context only; no Build 1 feature work)
- Routes: `public/index.php`
- Layout + pages: `templates/layouts/main.php`, all `templates/pages/*`, `templates/owner/dashboard.php`
- Styling: `public/assets/css/app.css` (full token + breakpoint + component pass)
- JS: `public/assets/js/app.js`, `public/assets/js/showcase.js`
- Runtime assets: `public/assets/images/brand|system|showcase|launch`
- Prior design tree: `design/DESIGN_SYSTEM.md`, `BRAND_SYSTEM_YS.md`, `RESPONSIVE_REDESIGN_PLAN.md`, `FLUTTER_DESIGN_HANDOFF.md`, `CHATGPT_CURSOR_DESIGN_HANDOFF.md`, review rounds 008–009
- Adjacent (out of product scope but noted): `site/styles.css`, `docs/styles.css` (Meow Control)
- API surface relevance: `src/Api/ApiV1.php` (UI coupling only)

**Missing source:** `YouAreTheSongNow_Design_Operating_System.md` was referenced as supplied but was **not** in the repo, attachments, or agent transcript. Governing OS doc was reconstituted in `docs/design/DESIGN-OPERATING-SYSTEM.md` from the task brief + verified repo/vault/design contracts. **Please replace/amend if the original file differs.**

---

## Files created

### `docs/design/` (permanent OS)

- `README.md`
- `DESIGN-OPERATING-SYSTEM.md`
- `CURSOR-HANDOFF.md` (this file)
- `foundations/` — README, tokens, color, typography, spacing-layout, motion, iconography-imagery
- `components/` — README, inventory
- `screens/` — README, inventory
- `audits/` — README, `ui-audit-2026-08-31.md`
- `flutter/` — README, portability
- `process/` — README, phases

### `assets/design/` (non-runtime artifacts)

- `README.md`
- `tokens/semantic-tokens.proposed.json`
- `references/README.md`, `exports/README.md`
- `audits/2026-08-31/README.md`

### Pointer updates

- `development-vault/01 Current Project/Current Priorities.md` (design OS note)
- `development-vault/01 Current Project/Dashboard Snapshot.md`
- `docs/dashboard-data.js` (brand/design status line)

**No production behavior or visual CSS migration in this task.**

---

## Architecture discovered

- **MPA** PHP app: `public/index.php` → `View::page` → shared `layouts/main.php`
- **Chrome:** sticky YS topbar + fixed `.app-nav` (bottom tabs &lt;900px → left rail ≥900px) + legal footer
- **Product interactivity:** single deferred `app.js` bound via `data-*` roots (`data-create`, `data-gallery`, `data-image-page`, `data-account`, `data-owner`, auth forms)
- **Marketing island:** `showcase.js` + static `/assets/data/v1-showcase.json` (+ Masonry vendors on `/showcase`)
- **Design tokens:** coherent OKLCH system in `app.css` `:root` with legacy aliases
- **Identity:** YS flat/premium marks + wordmark already integrated (Round 008)
- **Backend shared with future Flutter:** `/api/v1/*` JSON; mobile session endpoints exist but unused by web templates

---

## Screens discovered

| Route | Role |
| --- | --- |
| `/` | Guest home / launchpad (hero + 77-world carousel) |
| `/showcase` | V1 archive masonry + lightbox |
| `/sign-in`, `/sign-in/complete`, `/activate` | Auth / invitation |
| `/create` | Core 3-stage creation + paywall + generate |
| `/gallery`, `/images/{id}` | Collection + reveal/actions |
| `/account` | Profile / security / deletion |
| `/owner` | Private ops console |
| `/terms`, `/privacy`, `/shared/{token}`, 404 | Legal / shared / fallback |

---

## Components discovered

Shell: Brand, Topbar, AppNav (tab/rail), Legal  
Controls: Button variants, IconBtn, Form stack, ChoiceRow, Status, ConfirmSheet/dialog  
Create: SessionHeader, SessionProgress, Movement, PortraitChip, StyleTile, SummaryBoard, PaywallPanel, VenueProgress  
Media: Hero, WorldCarousel, GalleryTile/Empty, RevealFigure, Showcase masonry + lightbox  
Owner: tables + style toggle  

**No PHP component partial library** — duplication is markup/JS-level.

---

## Major design inconsistencies

1. Three style systems in-repo (app / site / docs) — product OS applies to **app only**
2. Legacy aliases + hardcoded OKLCH drift beside tokens
3. Multiple max-width systems vs `--app-max`
4. Confirm UX split (`dialog` vs `window.confirm`)
5. Empty-state design only formalized on Gallery
6. Guest chrome omits Showcase
7. Broken showcase tile `:focus-visible` outline (token misuse)
8. Button one-offs (`--retrieve`, `--generate`) beside primary system
9. Create heading hierarchy (`<p>` title, `h2` stages)

---

## Mobile problems

1. Hover-driven hero/gallery/chip polish without `(hover: hover)` gate
2. Some `vh` usage in dialogs vs `svh` shell
3. Session progress steps ~36px (below 44–48 guidance)
4. Create desktop sticky split needs careful phone stacking verification on every change
5. Showcase masonry is a web layout, not a mobile-app pattern
6. Full-width button rule ≤480px can fight multi-action rows if not excepted

---

## Accessibility issues

1. Create missing `<h1>`
2. Gallery empty always `aria-hidden="true"`
3. Broken showcase focus ring
4. Style listbox not keyboard-complete
5. Weak focus management on stage/paywall reveal and portrait dialog
6. Possible tertiary-text / secondary-nav contrast failures
7. Image delete `confirm()` vs accessible dialog pattern
8. Owner JSON `<pre>` dumps
9. Incomplete reduced-motion coverage on secondary controls

---

## Flutter-portability concerns

- Port **tokens + API screen contracts**, not DOM/CSS layout
- Masonry / IntersectionObserver / CSS-mask icons / backdrop-filter need Flutter-native redesigns
- Dual tab/rail chrome should be modeled as adaptive navigation, not CSS reposition
- Marketing Home/Showcase may stay web-only initially — **needs owner decision**
- OKLCH → Flutter sRGB conversion table not frozen yet
- Do not start Flutter UI until owners authorize (vault: web first → Flutter second)

---

## Proposed first design-system changes (Phase 2 candidates — not implemented)

1. Freeze semantic token names from `assets/design/tokens/semantic-tokens.proposed.json`
2. Add breakpoint custom properties (behavior-neutral)
3. Split focus color vs focus ring elevation
4. Fix showcase focus ring + gallery empty a11y (small pixel/AT wins)
5. Unify confirms on `ConfirmSheet`
6. Button loading/disabled during in-flight auth/create submits
7. Minimal offline / retry patterns for Create + Gallery
8. Replace matching hardcoded OKLCH with tokens

---

## Questions requiring GPT / design-director review

1. Confirm or replace reconstituted `DESIGN-OPERATING-SYSTEM.md` vs the missing original `YouAreTheSongNow_Design_Operating_System.md`.
2. Should `/docs/design/` fully supersede `design/DESIGN_SYSTEM.md`, or keep `/design/` as round-ops and `/docs/design/` as permanent OS (current proposal)?
3. Collapse CSS polish breakpoints (390/480/700/768/1100) into the four named bands, or keep extras?
4. Is Showcase part of guest primary nav, or marketing-only from Home?
5. Ship Home/Showcase in Flutter v1, or web-only?
6. Owner admin: web-only forever?
7. Approve proposed semantic token names / JSON before any CSS migration?
8. Priority order for Phase 2: a11y fixes first vs token hygiene first?

---

## Recommended next implementation task

**After GPT review:** Phase 2 slice A — **critical a11y + state hygiene without redesign**

1. Fix Create `h1` hierarchy  
2. Fix gallery empty `aria-hidden`  
3. Fix showcase focus ring  
4. Disable primary buttons while auth/create requests are in flight  
5. Replace image `confirm()` with existing confirm-sheet pattern  

Then stop for visual/AT review. Token migration can be slice B in the same phase if approved.

---

## Git

- **Branch:** `cursor/design-system-audit-95fa`
- **Primary delivery commit:** `be911ac1db56e05e5157301ca293a95922b6a5bb`
- **Branch tip:** `d7f9fe0f07e6f6f0f471cdd00c967c6d96c443ee`
