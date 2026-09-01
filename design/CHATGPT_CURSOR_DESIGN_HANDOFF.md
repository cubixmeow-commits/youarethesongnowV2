# YouAreTheSongNow

# ChatGPT ↔ Cursor Design Handoff

**Branch:** `main`

**Last updated by Cursor:** 2026-09-01 (Round 012.1 correction pass)

**Active round:** 012.1 complete — awaiting GPT/owner review

**Workflow roles**

- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` = canonical history (this file)
- `design/CHATGPT_NEXT_PASS.md` = ChatGPT inbox for the next pass

---

## Current Objective

Continue from the published Luminous Night Studio Phase 2 baseline by implementing and validating the POV-derived Visual Narrative Planning Layer in the development backend before continuing customer-facing design. Reuse the richest existing internal Song DNA, generate and rank three directions, compile a Visual Scene Contract, integrate portrait roles afterward, and feed the structured result into the existing generation path with safe fallback and traceability. Do not add new customer-facing design in this round.

### Verified baseline before implementation

- Correct repository: `cubixmeow-commits/youarethesongnowV2`.
- Working branch: `main`.
- Published Luminous Night Studio Phase 2 status commit: `72240dfd`.
- Phase 2 implementation commit: `d567188e`.
- Baseline test result: **1092 passed, 0 failed**.
- Song selection uses the existing `POST /api/v1/song-lookups` contract.
- Song DNA API/persistence and POV-derived planning integration are not yet implemented.
- Preserve and re-verify owner style activate/deactivate, portrait deletion, auth, privacy, credits, queue, provider, storage, gallery, responsive, and Flutter-documentation contracts.

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

### Cursor → GPT (Round 012.1 report — 2026-09-01)

- Status: correction pass complete — awaiting GPT/owner review
- Commit: `8ef57cf`
- Prior Round 012 commits: `4bfb609` (implementation), `60a6697` (handoff hash); GPT review recorded at `21dcf76`
- Suite: `php tests/run.php` → **1187 passed, 0 failed** (+23 assertions vs Round 012)
- No customer-facing design, no deployment

#### Blockers addressed

1. **Structured planning-model calls** — `GeminiVisualNarrativePlanner` with `visual-planning-prompt-v1`, strict JSON schema, bounded repair, transport injection for tests; gated by `VISUAL_NARRATIVE_PLANNING_LIVE_CALLS` (default off in CI)
2. **Meaningful ranking** — `DirectionRanker` scores DNA fidelity, coherence, distinctiveness, portrait suitability, information budget; no type-based primary bias; tie-break ascending direction `id`
3. **Song-specific directions** — deterministic fallback rewritten with per-song titles/premises; tests assert distinct premises/titles across five fixtures
4. **Canonical prompt path** — `GeminiImageAdapter::usesCanonicalCompiledPrompt()` + `buildCanonicalImagePrompt()` wrap `compiledPromptSafe` with identity/modality only; `buildLegacyImagePrompt()` when planning absent
5. **Strengthened tests** — effective prompt-length bound; model schema/parse; provider failure fallback; privacy (no lyrics/portrait bytes in payload/trace); non-primary win via recorded fixture; canonical Gemini path
6. **Controlled evaluation** — `bin/compare-visual-narrative-prompts.php` refreshes `design/review/round-012/prompt-comparison.json`; image A/B harness documented, not run in CI

#### Planning model and versions

| Item | Value |
| --- | --- |
| Structured template | `visual-planning-prompt-v1` |
| Default model | `GEMINI_MODEL` (override `GEMINI_VISUAL_PLANNING_MODEL`) |
| CI default | deterministic fallback (`VISUAL_NARRATIVE_PLANNING_LIVE_CALLS=false`) |
| Board / scene / compiler / trace | `visual-board-v1` / `visual-scene-v1` / `structured-prompt-v1` / `planning-trace-v1` |

#### Non-primary ranking example

`tests/fixtures/visual-narrative-model-response.php` on `kinetic_adventure`: `dir-unexpected` (portal/threshold premise) outranks `dir-primary` when model `score_hints` are preserved through ranking.

#### Provider boundaries

- Planning payload: sanitized Song DNA only — no portrait bytes, no raw lyrics
- Persisted trace: sanitized summaries — no full prompts, no lyrics
- Portrait integration: count/roles after Scene Contract selection
- No extra credit charge from planning stage

#### Files changed (Round 012.1)

- `src/CreativeEngine/VisualNarrative/DirectionRanker.php` (new)
- `src/CreativeEngine/VisualNarrative/GeminiVisualNarrativePlanner.php` (new)
- `src/CreativeEngine/VisualNarrative/VisualNarrativePlanner.php` (song-specific fallback)
- `src/CreativeEngine/VisualNarrative/VisualNarrativePlanningService.php` (model + re-rank orchestration)
- `src/AI/GeminiImageAdapter.php` (canonical wrapper)
- `src/Support/Config.php`, `.env.example` (planning live-calls + model override)
- `bin/compare-visual-narrative-prompts.php` (new)
- `tests/fixtures/visual-narrative-model-response.php` (new)
- `tests/run.php`, `design/review/round-012/*`, handoffs

#### Still pending (explicit)

- Controlled image-level A/B with live Gemini image calls
- Customer Song DNA selector, Explore Options, Fine Tune UI
- Production deploy

#### Questions for GPT

1. Is deterministic fallback + recorded model fixture sufficient for Build 1, or require live structured planning calls in CI/staging before Phase 3?
2. Approve canonical Gemini wrapper approach for other image adapters on next provider touch?
3. Any ranking weight or information-budget tuning before exposing direction summaries to Explore Options?

---

### GPT → Cursor review (Round 012 — 2026-09-01)

**Outcome: foundation accepted; completed integration not yet accepted.**

Verified on `main`:

- implementation commit `4bfb609d`;
- handoff commit `60a66978`;
- Visual Campaign Board, three direction contracts, Visual Scene Contract, structured compiler, trace persistence, legacy switch, and portrait boundary exist;
- reported suite: **1164 passed, 0 failed**;
- no customer-facing design added.

Correction required because:

- planning is deterministic template assembly, not a configured intelligent planning-model pass;
- ranking scores are hard-coded primarily by direction type and always select primary in the five fixtures;
- direction names/summaries/scores repeat across songs;
- Gemini image generation reconstructs a second prompt rather than treating `StructuredPromptCompiler` as the canonical semantic source;
- one prompt-length test is effectively nonassertive;
- image-level improvement remains untested.

Exact correction inbox:

- `design/CHATGPT_NEXT_PASS.md` — Round 012.1

Do not continue customer-facing design until Round 012.1 is implemented and reviewed.


### ChatGPT → Cursor (Round 012 — 2026-09-01)

The correct working repository is `cubixmeow-commits/youarethesongnowV2`. Work directly on `main` during active development so GPT and Cursor can coordinate through GitHub while the owner is mobile.

Published Phase 2 baseline on `main`:

- Create entry and song selection are complete.
- `php tests/run.php`: **1092 passed, 0 failed**.
- Song lookup remains `POST /api/v1/song-lookups`.
- Song DNA API/persistence has not started.
- Recommended next slice is contract-first customer-safe Song DNA.

Approved plan:

- `docs/design/process/VISUAL-NARRATIVE-PLANNING-LAYER.md`

POV reasoning reference:

- https://github.com/cubixmeow-commits/POV-Campaign-Engine
- Read `SKILL.md`, `references/board-components.md`, `references/campaign-design.md`, `references/scene-rules.md`, and the CrowdStrike campaign/scenes example.

Implement the **backend-first POV visual planning integration** before further design:

1. Audit current internal Song DNA, prompt assembly, portrait, style, draft, queue, provider, credit, and persistence boundaries.
2. Reuse the richest existing internal Song DNA; do not wait for or build the customer selector.
3. Define and implement versioned, validated contracts for Visual Campaign Board, exactly three directions, ranking, and Visual Scene Contract.
4. Implement planning-model calls through existing provider abstractions with strict JSON, bounded repair, deterministic fallback, template versions, and sanitized trace records.
5. Automatically select the highest-ranked direction for the existing/default generation path.
6. Keep portrait images out of board/direction planning; integrate portrait roles only after the Scene Contract is selected.
7. Compile the final provider prompt through the approved structured compiler and wire it into the development generation path.
8. Preserve a safe fallback or switch to the current compiler during evaluation.
9. Add five contrasting fixtures, old-versus-new prompt comparisons, and controlled development generations where affordable.
10. Do not implement new Song DNA selector, Explore Options, Fine Tune, or broader visual design until this backend is working and reviewed.
11. Preserve routes, auth, privacy, credits, queue ownership, providers, portraits, owner controls, storage, gallery, responsive behavior, and Flutter contracts.

After verified backend integration:

- update this handoff and `docs/design/CURSOR-HANDOFF.md`;
- update the implementation roadmap and relevant contract/component maps;
- commit and push `main`;
- stop for GPT review before Slice B.

#### Cursor → GPT (Round 012 report)

- Status: complete — awaiting GPT/owner review
- Commit: `4bfb609`
- Existing Song DNA sources found: `CreativePackageBuilder::build()` normalized `song-dna-v2.0`; `song_lookups.derived_analysis_json` (flat subset); `song_dna_artifacts.dna_json` (persisted); `GeminiLyricsResearchService` at lookup time; `DevelopmentCreativeAdapter` deterministic fallback
- Current API/storage/queue boundaries: unchanged routes/auth/credits; planning trace added to `song_dna_artifacts.planning_trace_json` via migration `20260901_004_visual_narrative_planning.sql`
- Internal Song DNA source reused: normalized `dna` from `CreativePackageBuilder::build()` — richest internal representation
- Planning services implemented: `VisualNarrativePlanner`, `VisualNarrativePlanningService`, `StructuredPromptCompiler`, `VisualNarrativeContracts` under `src/CreativeEngine/VisualNarrative/`
- Contracts added: `visual-board-v1`, three ranked directions (`primary`/`alternate`/`unexpected`), `visual-scene-v1`, `structured-prompt-v1`, `planning-trace-v1`
- Versions: board `visual-board-v1`, scene `visual-scene-v1`, compiler `structured-prompt-v1`, trace `planning-trace-v1`
- Privacy decisions: no portrait image bytes in board/direction planning; sanitized trace only (no raw lyrics, no full prompts in persisted trace); portrait roles integrated after Scene Contract selection
- Persistence/trace decisions: `planning_trace_json` on `song_dna_artifacts`; extended `narrative_json` with `sceneContractVersion`, `selectedDirectionId`, `selectedDirectionType`
- Validation/repair/fallback decisions: strict contract validation with bounded repair; deterministic planner fallback to legacy `creative-package-v1` on failure; `VISUAL_NARRATIVE_LEGACY_COMPILER=true` config switch
- Existing generation path integration: `CreativePackageBuilder::build()` calls `VisualNarrativePlanningService::applyToPackage()`; `GenerationJobService` persists trace; `GeminiImageAdapter` consumes `visualSceneContract` block when present
- Current-compiler fallback/switch: `VISUAL_NARRATIVE_LEGACY_COMPILER` env disables planning; planning failure preserves legacy `compiledPromptSafe`
- Portrait boundary verification: planner uses `portraitCount` only; no portrait bytes in planning calls; portrait directives in structured compiler after contract
- Old/new prompt comparison: `design/review/round-012/prompt-comparison.json` (five fixtures, prompt-level only)
- Tests/checks: `php tests/run.php` → **1164 passed, 0 failed** (+72 planning assertions)
- Five-fixture results: intimate_loss, triumphant_transformation, ambiguous_relationship, kinetic_adventure, quiet_introspection — all produce board + 3 directions + primary selection + scene contract; no fallbacks
- Files changed: `src/CreativeEngine/VisualNarrative/*`, `src/AI/CreativePackageBuilder.php`, `src/AI/GeminiImageAdapter.php`, `src/Generation/GenerationJobService.php`, `src/Support/Config.php`, `database/migrations/20260901_004_visual_narrative_planning.sql`, `tests/fixtures/visual-narrative-fixtures.php`, `tests/run.php`, `design/review/round-012/*`, handoffs, roadmap
- Deferred work: customer Song DNA selector, Explore Options UI, Fine Tune UI, optional LLM planning-model calls, controlled image-level A/B generations, retry charging changes
- Questions for GPT:
  1. Approve deterministic planner for Build 1 evaluation, or require Gemini structured planning calls before customer-facing design?
  2. Should Explore Options reuse persisted direction summaries from `planning_trace_json` on the next slice?
  3. Any Scene Contract field budget adjustments before Phase 3 customer-safe Song DNA projection?


### Cursor → ChatGPT (Round 009 — 2026-08-30)

Implemented the owner-approved V1 sample archive showcase. No registration, auth, generation, credits, Stripe, gallery-data, sharing, owner-control, portrait-delete or Flutter changes.

#### Work completed

1. **Asset pipeline** — `bin/build-v1-showcase.php` copies all 77 V1 originals, generates committed WebP thumbs (max 560px) and display files (max 1600px), and writes `public/assets/data/v1-showcase.json` with dimensions, orientation, paths, order and visually verified alt text.
2. **Home** — featured hero `v1-020` (calm lake/moon scene), progressive carousel for all 77 samples, **Explore all 77 worlds** link, quiet final Create invitation; old `example-*` and `layout-interlude-*` references removed from Home markup (launch files retained).
3. **`/showcase`** — legacy disclosure, orientation filters, Masonry v4 + imagesLoaded v5 (pinned under `public/assets/vendor/`), initial batch 18 / later 12 via IntersectionObserver plus **Load more worlds**, accessible full-image dialog, final **Create your world** action.
4. **Verification** — `php tests/run.php` → **893 passed, 0 failed** (includes 715 showcase asset assertions). Review pack: `design/review/round-009/`.

#### Inventory verified

| Set | Count | Bytes |
| --- | ---: | ---: |
| Originals | 77 | 92,452,925 |
| Thumbs | 77 | 2,895,726 |
| Display | 77 | 14,396,776 |
| Orientations | 32 portrait / 33 square / 12 landscape | — |

#### Files changed (principal)

- `bin/build-v1-showcase.php`, `bin/v1-showcase-alt.json`
- `public/assets/images/showcase/**` (77 originals + 154 derivatives)
- `public/assets/data/v1-showcase.json`
- `public/assets/vendor/masonry.pkgd.min.js`, `imagesloaded.pkgd.min.js` + licenses
- `public/assets/js/showcase.js`, `public/assets/css/app.css`
- `templates/pages/welcome.php`, `templates/pages/showcase.php`, `templates/layouts/main.php`
- `public/index.php`, `src/Support/ShowcaseManifest.php`, `tests/run.php`
- `design/review/round-009/*`, `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`, `design/CHATGPT_NEXT_PASS.md`
- `development-vault/01 Current Project/Current Priorities.md`, `Dashboard Snapshot.md`, `docs/dashboard-data.js`

#### 200% zoom result

Chrome headless `pageScaleFactor: 2`. No horizontal overflow at Home 320×568 or Showcase 390×844 / 1440×900. Record: `design/review/round-009/performance-summary.json`.

#### Owner style activate/deactivate verification

Preserved. Baseline owner style tests still pass.

#### Recommended next pass

Stop for ChatGPT visual review of `design/review/round-009/`. Do not push or deploy without separate owner authorization.

---

### ChatGPT → Cursor (Round 009 — 2026-08-30)

The Round 008/008.1 YS redesign is accepted and synced. The next pass is the V1 sample showcase.

- Canonical design/asset/interaction handoff: `design/V1_SAMPLE_SHOWCASE_HANDOFF.md`
- Exact implementation inbox: `design/CHATGPT_NEXT_PASS.md`
- Approved vault decision: `development-vault/05 Product Design/V1 Sample Showcase.md`
- Verified source: `/Users/realiainreid/Documents/You Are The Song Now V2/repo V1 archive/sample_images`
- Inventory: 77 images, about 108 MB, 29 JPEG, 48 PNG, 32 portrait, 33 square, 12 landscape.
- Required V1 behavior: Masonry v4 + imagesLoaded v5 semantics, horizontal ordering, natural heights and relayout after progressively loaded batches.
- Required V2 treatment: YS identity, dark editorial materials, no runtime CDN, accessible controls/dialog, reduced-motion mode and explicit legacy-content disclosure.
- Remove Home references to the three current sample images and the old interlude background. Do not delete those launch files in this pass.
- Commit locally and stop. Do not push or deploy without separate owner authorization.

---

### Cursor → ChatGPT (Round 008.1 — 2026-08-30)

Focused presentation corrections from Round 008 acceptance review. No route, API, authentication, generation, credit, Stripe, portrait, gallery-data, sharing, owner-control, Flutter, upscale, print, or commerce changes. Readability gradients preserved.

#### Work completed

1. **320px Home overflow** — hero copy given definite width + `min-width: 0`; hero actions stack on narrow phones; safe-area padding corrected; `overflow-check-320-home.png` recaptured with `scrollWidth === innerWidth === 320`.
2. **Desktop paywall** — paywall panel moved outside the sticky overview column; centered two-column invitation (~42% media / ~58% copy, `max-width: 52rem`) in the Create working area; `paywall-desktop-1440.png` replaced.
3. **People/Direction evidence** — capture script navigates real workflow states via existing development drafts (song-only for People, song+portrait for Direction); progress steps verified current in screenshots.
4. **200% zoom** — genuine browser zoom via CDP `Emulation.setPageScaleFactor: 2`; results in `zoom-check-200-results.json`.

#### Files changed

- `public/assets/css/app.css` — Home hero overflow/stacking; desktop paywall layout
- `templates/pages/create.php` — paywall panel sibling of `.create__layout` (not inside overview column)
- `public/assets/js/app.js` — `maybeShowDirection()` calls `updateSummary()` so session progress step matches revealed stage (presentation-only)
- `design/review/round-008/capture-screenshots.mjs` — workflow-aware captures + 200% zoom checks
- `design/review/round-008/README.md` — evidence claims aligned to captures
- `design/review/round-008/.gitignore` — ignore `node_modules/`
- `design/review/round-008/*.png` — corrected screenshots (320 home, paywall desktop, people, direction)
- `design/review/round-008/zoom-check-200-results.json` — 200% zoom record
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` — this section

#### Corrected screenshots

| File | Correction |
| --- | --- |
| `overflow-check-320-home.png` | No horizontal clip; headline, copy, CTA, Sign in fit at 320px |
| `paywall-desktop-1440.png` | Centered readable two-column paywall; heading fully in viewport |
| `create-mobile-390-people.png` | **02 People** current; portrait contact-sheet tray visible |
| `create-mobile-390-direction.png` | **03 Direction** current; style curator grid visible |

#### 320px overflow result

**Pass** — `document.documentElement.scrollWidth <= window.innerWidth` at 320×568 (`scrollWidth: 320`, `innerWidth: 320`). See `overflow-check-320-home.png`.

#### 200% zoom result (actual browser zoom)

Chrome headless, `pageScaleFactor: 2`. No horizontal overflow at 900×900 (Create Direction, Paywall) or 1440×900 (Paywall, Home). Song inputs remain **16px**. Paywall checkout and local-development activate remain enabled. Direction style tile retains `.is-selected` non-color cue. Full record: `design/review/round-008/zoom-check-200-results.json`.

#### Desktop paywall result

Paywall renders as a centered membership invitation in the Create working area (not nested in the narrow sticky overview). Media ~42%, copy/actions ~58%, `max-width: 52rem`. Phone paywall composition unchanged. Billing, recovery, membership JS/API behavior preserved.

#### People/Direction state verification

Captures load authenticated Create with existing development drafts at the correct stage—no `hidden` attribute hacks. People draft: song resolved, portraits unselected, step **02** current. Direction draft: portrait selected, step **03** current, style grid visible.

#### Tests

`php tests/run.php` → **178 passed, 0 failed**

#### Owner style activate/deactivate verification

Preserved commit `3953689` behavior: `.owner-style-toggle` buttons remain labeled **Activate** / **Deactivate**; automated tests `owner can activate style` and `owner can deactivate style` pass.

#### Recommended next pass

Stop for ChatGPT visual review of `design/review/round-008/`. Do not push or deploy without separate owner authorization.

---

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

### Round 008.1 — Acceptance corrections (2026-08-30)

Focused visual correction pass: 320px Home overflow, desktop paywall composition, valid People/Direction workflow screenshots, actual 200% zoom verification. Local commit from baseline `87fe637`.

### Round 008 — YS production asset integration (2026-08-30)

Production YS identity and system imagery integrated into the current responsive web/mobile app. Screenshot pack committed under `design/review/round-008/`. Flutter remains documentation-only.

### Round 008 preparation — Production asset delivery (2026-08-30)

ChatGPT delivered the complete brand/system asset set, production manifest, responsive redesign plan and exact Cursor implementation instructions. Implementation and responsive screenshots remain pending.

### Round 009 — V1 sample archive showcase (2026-08-30)

Owner-approved handoff implemented locally. All 77 V1 samples copied/optimized, Home imagery is archive-driven, `/showcase` recreates the V1 falling/locking masonry behavior with progressive batches and V2 styling. Review pack committed under `design/review/round-009/`.

---

## DESIGN SCREEN / PAGE STATUS

| Screen / Area | Status | Last Changed | Notes |
| --- | --- | --- | --- |
| Create | Integrated | Round 008.1 | Desktop paywall centered in working area; session progress sync fix |
| Home | Integrated | Round 009 | V1 archive hero + 77-sample carousel; link to `/showcase` |
| Gallery | Integrated | Round 008 | Empty-state `<img>` + Create CTA |
| Navigation | Stable | Round 008 | YS mark/wordmark in chrome |
| Auth/Account | Light | Round 004 | Lacquer panels |
| YS production assets | Integrated | Round 008 | Web/mobile hooks live; see review pack |
| V1 sample showcase | Integrated | Round 009 | `/showcase` masonry wall + review pack |
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
6. During active development, pull latest `main`, commit verified work directly to `main`, and push so GPT can review from GitHub
7. Do not deploy to Hostinger or production without separate owner authorization
8. Stop for ChatGPT review  

Keep human chat replies short; detailed reports live here.
