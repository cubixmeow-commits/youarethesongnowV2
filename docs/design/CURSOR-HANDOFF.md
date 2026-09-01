# CURSOR-HANDOFF — Luminous Night Studio foundation slice

**Date:** 2026-08-31
**Branch convention:** work directly on `main` while the product remains private development
**Design package status:** approved and implementation-ready
**Design package commit:** `9fc4e53de268a064d888f0c703fd4c81bb7dee15`
**Execute now:** Phase 1 foundation/component-lab slice only

## Package verification

- `php tests/run.php`: **981 passed, 0 failed**
- canonical token JSON: valid
- Markdown relative links under `docs/design/` and `assets/design/`: valid
- `git diff --check`: clean
- runtime/backend/API/migration changes in the package: **none**
- reference asset SHA-256: `65c8ab28b1160121ef24f35c7393dbc9a91892f8954e0c89a3f84ebd3e1a83f8`

## Outcome to deliver

Establish the approved **Luminous Night Studio** design foundation in runtime code, prove the canonical component states, and migrate the already-working Explore presentation onto those components without changing backend behavior.

Stop after screenshots, tests, and the handoff update. Do not begin Create Home, Song DNA API/database work, or the full screen rebuild.

## Read first, in order

1. `AGENTS.md`
2. `development-vault/START HERE.md`
3. `development-vault/01 Current Project/Current Priorities.md`
4. `development-vault/01 Current Project/Current Architecture.md`
5. `development-vault/05 Product Design/Luminous Night Studio Design Contract.md`
6. `development-vault/05 Product Design/Create Flow Architecture Contract.md`
7. `docs/design/DESIGN-OPERATING-SYSTEM.md`
8. `docs/design/foundations/principles.md`
9. `docs/design/foundations/tokens.md`
10. `docs/design/foundations/color.md`
11. `docs/design/foundations/typography.md`
12. `docs/design/foundations/motion.md`
13. `docs/design/foundations/responsive.md`
14. `docs/design/components/core-components.md`
15. `docs/design/screens/premium-product-screens.md` (Explore only in this slice)
16. `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md` (Phase 1 only)

## Visual references

- Canonical board: `assets/design/references/luminous-night-studio-style-board.png`
- Token source: `assets/design/tokens/semantic-tokens.json`
- Current runtime baseline: `public/assets/css/app.css`
- Responsive review evidence: `design/review/round-008/`
- Brand/runtime assets: `public/assets/images/brand/` and `public/assets/images/system/`

The board is art-direction evidence, not a pixel template. Accessible token/component/screen rules override decorative board details. Do not copy its illustrative artwork into runtime assets.

## Exact implementation slice

### 1. Runtime semantic foundation

Primary file: `public/assets/css/app.css`.

- Add stable semantic aliases matching `semantic-tokens.json`; retain legacy aliases until usage reaches zero.
- Split focus color from focus-ring shadow/elevation.
- Raise and contrast-test tertiary content before using it for small text.
- Add canonical variables for 44px minimum target, 48px control, 52px primary action, selected surface/edge, statuses, and sheet/dialog elevation.
- Preserve the accepted black/graphite/platinum/sapphire appearance unless a documented token correction is required.
- Do not introduce purple-pink gradients, glass panels, continuous glow, or `transition: all`.

### 2. Private-development component lab

Create the smallest repo-native fixture route/page fitting this PHP app. It must be owner/private-development-only or unreachable in external production mode.

Show:

- Button: primary/secondary/quiet/destructive; default, hover, focus, pressed, disabled, loading
- IconButton: default/focus/disabled
- StatusBanner: info/success/warning/error with retry
- SongDnaCard: loading, recommended, selected, conflict-disabled (fixture only; no API work)
- CreativeDirectionCard: loading, recommended, selected, selected+recommended, error
- Sheet/Dialog and Confirmation
- Artwork tile/figure: loading, ready, unavailable

Use fixtures only. No lyrics, private portraits, provider payloads, copyrighted artwork, keys, or prompts.

### 3. Current Explore presentation migration

Likely files:

- `templates/pages/create.php`
- `public/assets/js/explore.js`
- `public/assets/css/app.css`
- `tests/run.php`

Requirements:

- preserve `POST /api/v1/explore-directions`, Gemini decoder/provider behavior, exact three-direction schema, derived-DNA-only privacy, diagnostics gating, and internal StyleMap bridge;
- use canonical CreativeDirectionCard/Button/Status patterns;
- keep `Generate for me` primary and `Explore options` secondary;
- keep first server-ranked direction quietly Recommended;
- maintain whole-card selection and selected state;
- keep `Create this direction` because the current bridge enters the existing creation action; do not invent Fine Tune/review in this slice;
- keep internal style data out of customer copy;
- preserve manual-style escape while the legacy flow remains;
- do not move quality/orientation/no-text yet.

### 4. Touched accessibility defects

- Create must have a real `<h1>`.
- Gallery empty-state `aria-hidden` may be fixed as an independent safe correction.
- Focus ring renders on the actual interactive surface without clipping.
- Selection works with keyboard and exposes state programmatically.
- Reduced motion removes movement/shimmer from touched components.

## Explicit non-goals

- no migrations or database fields;
- no customer-safe Song DNA projection endpoint;
- no changes to lookup, drafts/snapshots/jobs, credits, paywall, Stripe, portraits, private media, sharing, account, deletion, or provider contracts;
- no Discover route/navigation;
- no Flutter code;
- no broad Create, Gallery, Reveal, Account, onboarding, marketing, owner, or dashboard redesign;
- no runtime use of style-board artwork;
- no push or deploy unless separately requested.

## Automated verification

Run `php tests/run.php` and report the exact count. Add scoped tests for component-lab access, Explore semantics/state hooks, exclusion of customer-facing internal StyleMap copy, repeated async activation protection, and touched accessibility behavior. Fix scoped regressions rather than weakening unrelated contracts.

## Screenshot and manual review

Capture:

- component lab at 320, 390, 768, 900, and 1440 CSS px;
- Create/Explore at 320, 390, 900, and 1440;
- Explore loading, ready/recommended, selected, error/retry, manual-style escape;
- visible keyboard focus;
- reduced-motion and increased-contrast evidence or clear notes;
- 200% zoom at compact and expanded widths.

Store under `design/review/round-010/` with a README naming route, state, viewport, and fixture setup. Include no private data.

## Review requirements

Verify primary action, restraint, Luminous material/type/selection direction, compact-first quality, expanded context without extra controls, keyboard/touch/screen-reader behavior, reduced motion, unchanged Explore backend/privacy, full tests, and explicitly no backend/API/migration change.

## Required completion handoff

Update this file with changed files, implemented token/component decisions, interaction notes, explicit backend/API/migration status, exact tests, screenshot paths/states, accessibility/contrast/motion/zoom results, deviations/questions, final commit hash, and next recommended slice.

Commit directly to `main` and stop for review. After approval, the expected next slice is **Phase 2: Create entry and existing-contract song selection**, not Song DNA backend work.
