# Luminous Night Studio implementation roadmap

**Status:** approved production design program  
**Baseline:** `assets/design/references/luminous-night-studio-style-board.png`  
**Delivery:** mobile-canonical responsive web first; Flutter mapping maintained; Flutter build separately authorized

## Non-negotiable boundaries

- Preserve working lookup, portraits, drafts/snapshots/jobs, credits/paywall, private media, Explore, gallery, sharing, account, and owner behavior unless a focused task explicitly changes it.
- Do not silently change API shapes, database fields, provider adapters, pricing, credit semantics, portrait privacy, raw-lyrics policy, async job truth, or deletion behavior.
- When a target screen requires a backend change, write/approve the contract and tests before UI code depends on it.
- One slice at a time: implement, test, capture evidence, review, then lock.

## Phase 0: baseline and design OS lock

**Deliverables**

- Luminous Night Studio principles and canonical reference board
- semantic token JSON and foundation rules
- core component/state contracts
- responsive/adaptive rules
- screen specs for the core product flow
- Flutter component/screen mapping
- Cursor handoff and synchronized vault/dashboard truth

**Acceptance**

- all documents link correctly and agree on the same baseline, type pairing, palette, navigation status, Create architecture, and backend boundaries;
- no production code or backend contract is changed;
- board provenance is explicit.

## Phase 1: foundation slice and component lab

**Implement**

1. Add canonical semantic aliases to `public/assets/css/app.css` without changing accepted backend behavior.
2. Split focus color from focus-ring elevation and verify contrast.
3. Build lightweight private-development component fixtures for Button, IconButton, StatusBanner, SongDnaCard, CreativeDirectionCard, Sheet/Dialog, and artwork states.
4. Refactor current Explore presentation onto the canonical DirectionCard/Button/Status patterns without changing `POST /api/v1/explore-directions` or its StyleMap bridge.
5. Fix the known Create `<h1>`, Gallery empty-state semantics, and focus-ring defects when the touched canonical component owns them.

**Acceptance**

- visual fixtures show default/hover/pressed/focus/selected/recommended/disabled/loading/error states;
- 320, 390, 768, 900, and 1440 screenshots exist;
- keyboard, screen-reader semantics, reduced motion, increased contrast, and 200% zoom pass;
- current Quick Generate and Explore behavior remains functional;
- full PHP test suite passes with exact count reported;
- no raw literals are introduced where a semantic token exists.

## Phase 2: Create entry and song selection

**Implement**

- Create destination hierarchy and focused-flow shell
- song artist/title search using the existing lookup contract
- resume draft and small recent-work treatment
- active portrait summary only if the server has an explicit active/default portrait contract

**Acceptance**

- song action is obvious in two seconds at 320px;
- lookup loading, match, ambiguous/no-match, rate/provider failure, offline/retry preserve inputs;
- no StyleMap/quality/orientation controls appear before Song DNA;
- back/exit preserves draft;
- existing lookup privacy/rate rules and tests pass.

## Phase 3: customer-safe Song DNA contract and selector

**Contract first**

- define customer-safe projection response, stable dimension/value IDs, allowed copy, fallback provenance, draft selection fields, maximum layers, conflict behavior, snapshot immutability, and Flutter error shape;
- add migrations/API/service tests only after approval;
- explicitly amend First Build and Onboarding configuration contracts where required.

**Implement after contract**

- Song DNA loading/unavailable/retry
- recommended starting layer and progressive `Add another layer`
- 1–3 layer selection and compact summary

**Acceptance**

- response contains no lyrics, reconstructive text, prompts, risk flags, confidence internals, provider detail, or private artifacts;
- selection persists to draft and immutable job snapshot;
- keyboard/screen-reader multi-select works;
- context-fallback copy is truthful;
- API and Flutter contract tests pass.

## Phase 4: Quick Generate, Explore, and Fine Tune integration

**Implement**

- Quick Generate default from selected DNA
- existing Explore endpoint scoped to the selected DNA contract
- exactly three DirectionCards with recommended/selected/stale/retry states
- Fine Tune sheet/panel with approved allowlist
- authoritative quote refresh and existing paywall before job submission

**Acceptance**

- default path has one generation decision;
- Explore remains optional and StyleMap internals stay hidden;
- changing DNA invalidates stale directions;
- Fine Tune never blocks default generation;
- quote, paywall, idempotency, and credits retain current rules;
- failures preserve the prepared creation.

## Phase 5: generation and reveal

**Implement**

- immersive truthful GenerationStage using real job states
- stable selected-DNA context
- leave-safely behavior and polling recovery
- cover reveal with reduced-motion alternative
- Download, Share, Create a variation, and Delete using current contracts
- do not ship Reimagine until distinct behavior is approved

**Acceptance**

- no fake percentages/stages/previews;
- completion and final failure/refund messages are accurate;
- leaving does not cancel the job;
- artwork is visually dominant at compact and expanded widths;
- sharing/revocation/download/regeneration/deletion tests pass.

## Phase 6: Gallery and portrait shelf

**Implement**

- portraits first, active/default behavior only after explicit server support
- artwork-led grid with queued/generating/failed states
- canonical empty, media-error, confirmation, and touch actions

**Acceptance**

- portrait management no longer interrupts repeat Create;
- private media authorization/deletion remain intact;
- empty state is exposed to assistive technology;
- mixed aspect ratios do not cause destructive crop or layout shift;
- touch and keyboard can reach every action.

## Phase 7: Account, membership, onboarding, and final shell

**Implement**

- sectioned Account states without dashboard treatment
- membership lifecycle, credits, profile/email/password/sessions, billing portal, deletion
- onboarding/paywall copy amendments for DNA-first configuration
- navigation destinations finalized; Discover only with separate product approval

**Acceptance**

- all membership lifecycle and deletion behavior matches accepted contracts;
- no live Stripe/public access gate changes;
- focused Create shell and destination shell are consistent;
- account operations pass keyboard/screen-reader testing.

## Phase 8: system quality and retirement

**Implement**

- remove superseded legacy Create UI/CSS/JS only after replacements are proven
- accessibility, performance, image-loading, contrast, reduced-motion, offline/retry, 320px, 200% zoom, and token-drift audits
- review all customer copy and private-development diagnostics

**Acceptance**

- WCAG 2.2 AA target verified;
- no obsolete customer StyleMap flow competes with DNA-first creation;
- no raw lyrics/secrets/private prompts in source, assets, logs, or evidence;
- image grids use correct derivatives and avoid avoidable layout shift;
- full automated suite and complete manual journey pass.

## Phase 9: Flutter contract freeze and implementation gate

**Deliverables before authorization**

- reviewed sRGB token export, bundled-font licenses/assets, icon asset set
- frozen component/state contracts and screen/API map
- mobile session/security/idempotency verification
- web screenshots/behavior accepted by owners

**Acceptance**

- Flutter can implement one state model with compact/expanded compositions;
- no server business rule must be duplicated in Dart;
- owner explicitly authorizes Flutter work.

## Evidence required for every implementation slice

- changed files and explicit non-goals
- exact automated test results
- screenshots at 320, 390, 768, 900, 1440 as relevant
- selected/loading/error/empty/disabled evidence
- keyboard, focus, screen reader, reduced-motion, increased-contrast, and 200% zoom notes
- backend/API/migration changes listed separately, or `none`
- Flutter portability notes
- final commit hash in `docs/design/CURSOR-HANDOFF.md`

## Next executable slice

**Phase 1 is independently verified and published to `main`** at implementation commit `6951f0c` (`design/review/round-010/`). After private deployment review, execute **Phase 2: Create entry and existing-contract song selection**. Do not begin Song DNA API/database work in that slice.
