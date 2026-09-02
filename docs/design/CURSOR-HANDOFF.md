# OVERRIDING NEXT DIRECTIVE — Round 015 Card-by-Card Create Flow

**Date:** 2026-09-02  
**Repository:** `cubixmeow-commits/youarethesongnowV2`  
**Target branch:** `main`  
**Status:** Owner-approved design; ready for Cursor implementation  
**Canonical instructions:** `design/CHATGPT_NEXT_PASS.md`

Replace the scrolling Create form with a mobile-canonical, single-card state flow: **Song → People → Direction → Review → Generating**.

Non-negotiable outcomes:

- only one decision group visible at a time;
- Generate for me remains primary and reaches Review through the real POV preparation chain;
- Explore remains secondary and shows three compact direction rows;
- saved portraits precede collapsed upload controls;
- quality, format, no-text, and special instructions move behind collapsed Fine Tune on Review;
- final Generate image is always visible/reachable on Review;
- global mobile tabs do not compete with the active Create flow;
- the existing POV planner, prompt compiler, APIs, credits, draft behavior, portraits, Gallery, and asset cache busting remain intact;
- implementation, browser tests, sanitized Round 015 evidence, handoff updates, commit, and push go to `main`;
- no Hostinger deployment.

Read and follow the complete Round 015 contract in `design/CHATGPT_NEXT_PASS.md`. This directive supersedes all older “stop/no broader design work” instructions below.

---

# OVERRIDING NEXT DIRECTIVE — Round 014 GitHub sync complete

**Date:** 2026-09-02  
**Repository:** `cubixmeow-commits/youarethesongnowV2`  
**Target branch:** `main`  
**Final `origin/main` SHA:** `7c706af`  
**Status:** GitHub `main` synchronized — owner deploys manually; do not deploy from Cursor

## Synchronization result

| Check | Result |
| --- | --- |
| `HEAD` equals `origin/main` | yes (`3ab9dca`) |
| Working tree | clean (test-run artifact churn restored) |
| `f6daa92` direction hierarchy | ancestor of `origin/main` |
| `2d1e1cc` asset cache busting | ancestor of `origin/main` |
| `8ce6784` Quick Generate async fix | ancestor of `origin/main` |
| `AssetRelease` + `/assets/r/{releaseId}/...` layout | present on `origin/main` |
| `directionLoadInFlight` / `preparationInFlight` / `await continueWithDirection` | present on `origin/main` |
| `php tests/run.php` | **1241 passed, 0 failed** |

No additional implementation commits were required. Hostinger was not accessed.

---

# OVERRIDING NEXT DIRECTIVE — Round 014 GitHub Main Synchronization Only

**Date:** 2026-09-02  
**Repository:** `cubixmeow-commits/youarethesongnowV2`  
**Target branch:** `main`  
**Scope:** GitHub synchronization only — owner will deploy manually

This directive supersedes the Hostinger deployment instructions immediately below. Do **not** access, inspect, configure, or deploy Hostinger.

## Required work

1. Fetch the GitHub remote and inspect local/remote branch state.
2. Confirm all intended implementation commits are ancestors of `origin/main`, including:
   - `f6daa92` — direction hierarchy / Generate bar gating;
   - `2d1e1cc` — path-fingerprinted asset cache busting;
   - `8ce6784` — Quick Generate async dead-end correction;
   - latest handoff commits.
3. Inspect the working tree for uncommitted or untracked project changes created by Cursor. Preserve unrelated user work.
4. Commit any intended missing Round 013 implementation, tests, evidence, or handoff changes.
5. Push the resulting branch directly to GitHub `main`.
6. Verify:
   - local `HEAD` equals `origin/main`;
   - `git status` is clean except deliberately excluded local-only files;
   - `origin/main` contains the current `AssetRelease` implementation;
   - `origin/main` contains `directionLoadInFlight`, `preparationInFlight`, and `await continueWithDirection(direction)`;
   - rendered-layout source uses `/assets/r/{releaseId}/...` through `AssetRelease::url`;
   - full suite remains passing.
7. Do not rewrite history, force-push, reset destructively, merge unrelated branches, deploy, or alter server data.

Update the handoff with the final full `origin/main` commit SHA, test count, and clean-status result. Push that update to `main`, then stop. The owner will deploy the resulting GitHub main branch manually.

---

# NEXT DIRECTIVE — Round 014 Synchronize Hostinger Deployment to Main

**Date:** 2026-09-02  
**Working branch:** `main`  
**Priority:** Blocking deployment drift  
**Scope:** Deploy and verify the already-reviewed frontend; do not redesign or rewrite it

## Confirmed live evidence

GPT inspected `https://youarethesongnow.com/sign-in` directly after Round 013.3. The deployed layout still emits legacy per-file query URLs:

- `/assets/js/app.js?v=1788312527`
- `/assets/css/app.css?v=1788312527`

Current `main` must emit path-fingerprinted release bundle URLs:

- `/assets/r/{releaseId}/css/app.css`
- `/assets/r/{releaseId}/js/song-search.js`
- `/assets/r/{releaseId}/js/explore.js`
- `/assets/r/{releaseId}/js/app.js`

Therefore the production-development host is not serving current `main`. The missing Generate image action cannot be evaluated against Round 013.3 until deployment drift is fixed.

## Required work

1. Audit the Hostinger Git/deployment configuration:
   - repository: `cubixmeow-commits/youarethesongnowV2`;
   - branch: `main`;
   - deployed document root and release directory;
   - pull/build/deploy command;
   - whether deployment is automatic or requires an explicit trigger;
   - whether an older checkout, branch, symlink, or document root is active.
2. Resolve the exact deployed commit before changing anything.
3. Deploy current `main` at or after handoff commit `10fe22c` (implementation `8ce6784` plus cache busting `2d1e1cc`).
4. Preserve server-only secrets, uploads, SQLite/storage, permissions, and environment configuration. Do not replace or expose them.
5. Ensure `public/.htaccess` is present in the actual web document root and Apache honors the fingerprint rewrite.
6. Do not run destructive Git cleanup or overwrite user data.
7. If deployment credentials or Hostinger access are unavailable, stop and report the exact blocker and the currently deployed commit/path if determinable.

## Live acceptance checks

After deployment, verify against the public host—not localhost:

- `GET /sign-in` or authenticated `GET /create` emits `/assets/r/{12-hex-release-id}/...` URLs, not `?v=filemtime`;
- each fingerprinted CSS/JS URL returns HTTP 200;
- HTML returns `Cache-Control: no-store`;
- fingerprinted assets return long-lived immutable cache headers where Hostinger modules permit;
- live `explore.js` contains `directionLoadInFlight`, `preparationInFlight`, and the awaited Quick Generate continuation;
- live Create shows Generate for me + Explore options before preparation;
- tapping Generate for me leaves “Preparing…” and reveals an enabled Generate image action;
- one Generate image tap creates one job.

Record the deployed commit, document root/release identifier, asset release id, URLs, headers, and smoke-test result in `design/review/round-014/README.md`. Do not include secrets, cookies, portrait data, lyrics, or customer information.

Only make a code change if a verified Hostinger-specific deployment defect requires it. Commit any necessary safe deployment correction and handoff update to `main`. Stop for GPT/owner review after live verification. Do not resume broader design work.

---

# NEXT DIRECTIVE — Round 013.3 complete, awaiting GPT review

**Date:** 2026-09-02  
**Working branch:** `main`  
**Published to:** `main` at `8ce6784`  
**Status:** Quick Generate async dead-end repaired — stop for GPT/owner review

## Root cause

`loadDirections(true)` set `exploreInFlight` before `continueWithDirection()`, whose guard returned immediately. Preparation/review never ran; status stuck on “Preparing your creation…”.

## Repair

- Split `directionLoadInFlight` and `preparationInFlight` locks
- Quick Generate unlocks direction loading before `await continueWithDirection()`
- `await` the real `prepareAndReview()` path — no timers or synthetic clicks

## Verification

- `php tests/run.php`: **1241 passed, 0 failed** (includes real `[data-ai-quick]` click chain test)
- Evidence: `design/review/round-013-3/`

Do not deploy. Do not resume broader design work.

---

# NEXT DIRECTIVE — Round 013.3 Fix Quick Generate Async Dead-End

**Date:** 2026-09-02  
**Working branch:** `main`  
**Priority:** Blocking confirmed runtime defect  
**Scope:** Repair the real Generate-for-me async transition and test the actual click path

## Exact root cause confirmed by GPT review

In `public/assets/js/explore.js`:

1. `loadDirections(true)` calls `setBusy(true)`, which sets `exploreInFlight = true`.
2. The successful Quick Generate branch calls `applyDirection(..., { autoContinue: true })`.
3. `applyDirection()` invokes `continueWithDirection(direction)`.
4. The first line of `continueWithDirection()` is `if (exploreInFlight) return;`.
5. Therefore preparation/review never runs.
6. `finally` later calls `setBusy(false)`, but nothing retries `continueWithDirection()`.

The visible result exactly matches the owner’s live screenshot: status remains **“Using ‘Static Revolt’. Preparing your creation…”** and **Generate image** never appears.

## Required repair

- Restructure the Quick Generate success path so direction loading finishes/unlocks before the preparation transition executes.
- Do not “fix” this by broadly removing duplicate-action protection. Preserve an explicit state/lock model for direction loading, preparation, and generation submission.
- Await the real preparation operation. Do not fire it through `void`, fixed timers, synthetic clicks, or an unobserved promise.
- On success:
  - selected AI direction and style are applied;
  - draft synchronization finishes;
  - summary review returns ready;
  - prepared-direction state is set to `ai-quick`;
  - status changes to ready;
  - final **Generate image** action becomes visible and enabled.
- On preparation/review failure:
  - status shows the actual recoverable error;
  - Generate for me / retry remains usable;
  - no final enabled action is shown;
  - user selections remain intact.
- Explore options and manual paths must continue working.
- Keep Round 013.2 cache-busted asset URLs.

## Real behavior verification — fixtures alone are insufficient

Add a browser test that exercises the actual production event chain:

1. Load authenticated `/create`.
2. Establish a valid confirmed song, portrait, and Song DNA state.
3. Intercept/mock only the external/API responses as necessary.
4. Click the actual `[data-ai-quick]` button.
5. Allow the real `loadDirections(true) → apply direction → prepare/review` code to execute.
6. Assert the explore-directions request occurs exactly once.
7. Assert the summary/review request occurs exactly once and only after direction loading unlocks.
8. Assert the final bar changes from computed `display:none` to visible.
9. Assert **Generate image** is enabled.
10. Assert status no longer contains “Preparing your creation”.
11. Click Generate image and assert exactly one generation request.
12. Repeat failure response: confirm recoverable error and retry path.
13. Verify Explore options still renders three directions without premature final bar.

Do not use `YatsnCreateFixtures.showPreparedReady()`, direct `setDirectionPrepared()`, or equivalent state injection as proof of this bug. Those may remain for visual captures, but acceptance requires the real click/async chain.

Capture sanitized 390px evidence under `design/review/round-013-3/` for:

- before Generate for me;
- preparation pending;
- preparation completed with Generate image visible;
- recoverable preparation failure.

Run the full suite and syntax checks. Commit implementation, behavior test, evidence, and handoff update to `main`, then stop for GPT/owner review. Do not deploy or resume broader design work.

---

# NEXT DIRECTIVE — Round 013.2 complete, awaiting GPT review

**Date:** 2026-09-02  
**Working branch:** `main`  
**Published to:** `main` at `9042ceb`  
**Status:** Automatic asset cache busting complete — stop for GPT/owner review

## Root cause

Per-file `?v=filemtime` query strings allowed mixed stale/new first-party assets after deploy. Create could load new `explore.js` copy with old cached `app.css`/`app.js`, so a normal mobile refresh did not guarantee one coherent frontend release.

## Repair

- `Yatsn\Support\AssetRelease` computes a 12-character release id from SHA-256 digests of every file in a bundle
- Create bundle (`app.css`, `app.js`, `explore.js`, `song-search.js`) shares one release id in path-fingerprinted URLs: `/assets/r/{releaseId}/...`
- `public/.htaccess` rewrites versioned paths to on-disk files and applies `Cache-Control: public, max-age=31536000, immutable`
- `View::page()` sends `Cache-Control: no-store` for HTML responses
- PHP dev router mirrors rewrite + immutable headers for local verification

## Verification

- `php tests/run.php`: **1236 passed, 0 failed** (includes asset cache + Round 013.1 behavior harness)
- Evidence: `design/review/round-013-2/`

Do not deploy. Do not resume broader design work.

---

# NEXT DIRECTIVE — Round 013.2 Automatic Asset Cache Busting

**Date:** 2026-09-02  
**Working branch:** `main`  
**Priority:** Blocking deployment reliability defect  
**Scope:** Ensure each deployed frontend release loads one coherent CSS/JS version

## Owner-observed failure

The mobile site is loading a mixed/stale frontend after the Round 013/013.1 changes. The UI shows new preparation behavior (“Using Static Revolt. Preparing your creation…”) while other controls/layout do not consistently reflect the corresponding current CSS/JS. A normal mobile refresh is not sufficient.

Do not ask users to clear their browser cache. Cache invalidation must be automatic.

## Required implementation

1. Find the canonical server-rendered layout/template that emits application asset URLs.
2. Add deterministic versioning to every first-party frontend asset involved in Create and global navigation, including at minimum:
   - `public/assets/css/app.css`;
   - `public/assets/js/app.js`;
   - `public/assets/js/explore.js`;
   - `public/assets/js/song-search.js`;
   - any other first-party CSS/JS loaded by the same layout.
3. The version must change automatically when the deployed asset changes. Prefer a content hash, deployment commit/build identifier, or per-file content/file modification hash generated server-side. Do not use a manually edited date string as the permanent mechanism.
4. Preserve script execution order and current defer/module behavior.
5. Version all mutually dependent Create assets together so the browser cannot combine old `app.js` with new `explore.js` or old `app.css`.
6. Confirm the chosen URL strategy is honored by the Hostinger/web-server/CDN path. Query strings are acceptable only if verified; otherwise use fingerprinted filenames or a rewrite-safe equivalent.
7. HTML/PHP responses for authenticated Create must not be cached as immutable. Versioned static assets may use long-lived caching because their URL changes with content.
8. Check for and update any service worker, manifest, preload, CSP, CDN, or rewrite rules that could continue serving stale asset URLs.
9. Do not expose filesystem paths, secrets, or internal deployment data in page source. A short public build/hash identifier is acceptable.
10. Keep all Round 013.1 interaction behavior unchanged.

## Verification

Add tests that prove:

- rendered Create HTML includes versioned URLs for all relevant first-party CSS/JS;
- changing an asset’s content or build identifier changes its rendered URL;
- all coupled Create assets share the intended coherent release version;
- no unversioned duplicate app/explore/song-search script is emitted;
- script ordering remains correct;
- `hidden` Generate bar behavior still passes;
- Generate for me and Explore options remain present before final Generate;
- production-like cache headers distinguish HTML from versioned static assets.

Create a sanitized report under `design/review/round-013-2/` containing:

- the versioning strategy;
- example old/new asset URLs;
- relevant response headers;
- a mobile fresh-load verification at 390px;
- confirmation that a normal reload receives the current release without clearing site data.

Run the full suite and syntax/lint checks. Commit implementation, tests, report, and handoff updates to `main`, then stop for GPT/owner review. Do not deploy or begin broader design work.

---

# NEXT DIRECTIVE — Round 013.1 complete, awaiting GPT review

**Date:** 2026-09-01  
**Working branch:** `main`  
**Published to:** `main` at `f6daa92`  
**Status:** Direction choice hierarchy restored — stop for GPT/owner review

## Root cause

`.create__generate-bar { display: grid; }` overrode the native `hidden` attribute. Round 013 also showed the final CTA whenever the Direction stage opened, before **Generate for me** / **Explore options**, and Quick Generate auto-submitted generation.

## Repair

- `.create__generate-bar[hidden] { display: none !important; }`
- `directionPrepared` + `directionPath` gate the final **Generate image** bar
- Quick Generate prepares only — no auto-submit
- `clearDirectionPrepared()` restores initial choice hierarchy on explore restart / manual back-out
- Readiness copy distinguishes missing song vs unconfirmed song; restored drafts never report song missing

## Verification

- `php tests/run.php`: **1218 passed, 0 failed** (includes browser behavior harness)
- Evidence: `design/review/round-013-1/`

Do not deploy. Do not resume broader design work.

---

# NEXT DIRECTIVE — Round 013.1 Restore Direction Choice Hierarchy

**Date:** 2026-09-01  
**Working branch:** `main`  
**Priority:** Immediate regression correction  
**Scope:** Restore Generate for me / Explore options and gate the final Generate action correctly

## Owner-observed regression

After Round 013, **Generate for me** and **Explore options** appear to be gone while a disabled **Generate image** bar is forced onto the bottom of the mobile viewport. Screenshot state shows:

- grounded Song DNA exists;
- Overview shows the chosen song;
- People: None selected;
- Style: Not chosen yet;
- bottom bar incorrectly says “Choose and confirm a song”;
- final Generate action is visible before the creation is ready.

## Confirmed implementation defect

`.create__generate-bar { display: grid; }` overrides the native `hidden` attribute. Therefore `bar.hidden = true` does not reliably hide the bar. This repeats the hidden-state CSS class of bug previously fixed for song-search rows.

Round 013 also ties bar visibility only to `#the-direction.hidden`. That is too early: entering the Direction stage is not the same as selecting/preparing a direction. The final CTA must not compete with or visually replace the two AI choice actions.

## Canonical interaction sequence

1. Song is confirmed.
2. Required portrait/person selection is complete.
3. Direction stage opens with the existing choice hierarchy:
   - **Generate for me** — primary/default;
   - **Explore options** — secondary;
   - manual style path remains available according to the existing design.
4. At this initial choice state, the sticky/final **Generate image** action is hidden.
5. After **Generate for me** has successfully selected/applied the strongest AI direction, expose the final **Generate image** action.
6. After Explore options → a direction is selected and applied, expose the final action.
7. After a valid manual direction/style is prepared and reviewed, expose the final action.
8. If the selected/prepared path later becomes invalid, keep the final action in that prepared context but disabled with the correct missing requirement. Do not show it globally before a direction path has been chosen.
9. Backing out to the initial direction-choice state hides the final action and restores Generate for me / Explore options.
10. Quick Generate remains the default, lowest-decision path. Do not remove, rename, demote, or bypass it.

Do not automatically submit an image merely because **Generate for me** selected a direction unless that was the pre-Round-013 established behavior. Preserve the intended confirmation boundary: the user must have one clear final Generate action after the AI direction is prepared.

## Required correction

- Add an explicit hidden CSS guard, e.g. `.create__generate-bar[hidden] { display: none !important; }`.
- Model bar visibility from an explicit prepared-direction/path state, not merely Direction-section visibility.
- Restore/verify the AI direction panel and both choice buttons in the ordinary real runtime state—not only fixtures.
- Fix readiness copy so an already confirmed/restored song never reports “Choose and confirm a song.”
- Verify draft restoration correctly restores `songConfirmed`, portraits, chosen path/direction, and style before calculating the final action.
- Ensure fixed positioning never covers the AI choice panel, portrait controls, Overview content, browser chrome, or bottom navigation.
- Keep duplicate submission, pending state, recoverable errors, credits, queue, and endpoint behavior from Round 013.

## Regression verification

Add behavior-level tests and real-state mobile evidence for this full sequence:

1. confirmed song, no portrait → People stage; no Generate bar;
2. portrait selected, Direction opens → Generate for me + Explore options visible together; no final bar;
3. Generate for me prepared → both intended context controls remain coherent and final Generate image becomes reachable;
4. Explore options initial → three directions; no premature final bar;
5. explored direction applied → final action reachable;
6. manual path prepared → final action reachable;
7. restored draft with confirmed song never reports song missing;
8. hidden bar computed style is actually `display: none`;
9. 320×640 and 390×844 evidence includes the bottom navigation and proves no overlap;
10. 200% zoom, keyboard/textarea, safe area, reduced motion, and increased contrast.

Do not rely only on string-presence assertions. Exercise state transitions in the browser harness or focused JS tests. Run the full suite. Commit implementation, evidence under `design/review/round-013-1/`, and updated handoffs to `main`; then stop for GPT/owner review. Do not deploy or start broader design work.

---

# NEXT DIRECTIVE — Round 013 complete, awaiting GPT review

**Date:** 2026-09-01  
**Working branch:** `main`  
**Published to:** `main` at `89a1950`  
**Status:** Mobile Generate action repair complete — stop for GPT/owner review

## Root cause

The final CTA was inside `[data-summary-actions hidden]` and only appeared after a successful manual Review POST. On mobile the Overview card sits below the direction column, so the button was off-screen; Quick Generate used timer bridges that raced the async review path.

## Repair

- Always-visible `data-generate-bar` with **Generate image** label
- Mobile fixed bar above bottom navigation + safe-area inset
- Disabled button + `data-generate-hint` when requirements missing (never silently hidden)
- Auto server review via `scheduleGenerationReview()`; Quick Generate awaits `YatsnCreate.prepareAndReview()`
- Duplicate-submit lock + recoverable failure restores actionable state

## Verification

- `php tests/run.php`: **1207 passed, 0 failed**
- Evidence: `design/review/round-013/` (320 + 390 widths, disabled/pending/error states)

Do not deploy. Do not resume broader design work.

---

# NEXT DIRECTIVE — Round 013 Blocking Mobile Generate Action Repair

**Date:** 2026-09-01  
**Working branch:** `main`  
**Priority:** Blocking functional defect  
**Scope:** Repair the existing Create completion action; no broader redesign or deployment

## Observed failure

On the live mobile Create flow, after the owner selects **Generate for me** and completes the visible choices, the review/Overview state contains no accessible final Generate action. The user cannot submit the image generation.

Observed on an iPhone-width viewport:

- song: Seven Pillars of Wisdom — Sabaton;
- one person selected;
- style: Cinematic Realism;
- square selected;
- optional special instructions visible;
- Overview card visible;
- fixed bottom navigation visible;
- no reachable **Generate image** button.

Treat this as a real product-blocking defect, not merely a screenshot adjustment. Determine whether the action is conditionally omitted, below an incomplete scroll region, obscured by the fixed bottom navigation/safe area, or disabled without an explanation.

## Required behavior

1. Quick Generate / **Generate for me** remains the default low-decision path.
2. Once all genuinely required inputs are present, expose one unmistakable primary action labeled **Generate image**.
3. On mobile, the action must remain reachable above the fixed bottom navigation and iOS safe area. Prefer the smallest robust solution consistent with the existing Luminous Night Studio system; a sticky mobile action region is appropriate if it does not duplicate actions or cover content.
4. If required data is missing, keep the action visible but disabled and provide concise nearby text identifying exactly what is missing. Never silently remove the action.
5. If an enabled submit is tapped:
   - prevent duplicate submission;
   - show immediate pending feedback;
   - use the existing generation endpoint, credit, queue, error, and refund contracts;
   - preserve entered selections and instructions on recoverable failure;
   - do not add new generation or credit behavior.
6. The manual-control path must continue to work, but do not add new Explore Options/Fine Tune design in this repair.
7. Desktop must retain the same premium app-like language and a reachable action without unnecessary sticky duplication.

## Audit before editing

Trace the actual state machine and markup from direction-mode selection through Overview and submission. Identify the exact root cause in the Cursor report. Check:

- conditional rendering for Generate for me versus manual control;
- form/button placement and form ownership;
- `hidden`, disabled, and validation conditions;
- scroll-container height, bottom padding, fixed navigation, and `env(safe-area-inset-bottom)`;
- keyboard and textarea interaction;
- return/resume state;
- pending, error, insufficient-credit, and success states.

## Verification

Add regression coverage that fails against the current defect and verifies:

- Generate for me + valid required data renders one enabled **Generate image** control;
- missing requirements render the same visible disabled control plus reason;
- the action remains within the scrollable/reachable area at 320×640, 390×844, and an iPhone safe-area viewport;
- the bottom navigation does not cover the action or final Overview content;
- textarea keyboard open/close does not permanently hide or displace it;
- one activation produces one request;
- pending prevents duplicate requests;
- recoverable API failure restores an actionable state without losing inputs;
- manual-control path still has a valid submit;
- keyboard focus, 200% zoom, reduced motion, and increased contrast remain usable.

Capture sanitized review evidence at `design/review/round-013/` for the valid Quick Generate state at 320 and 390 widths, plus disabled/missing-requirement, pending, and recoverable-error states. The evidence must show the bottom navigation and Generate action together.

Run the full suite and relevant syntax/lint checks. Commit implementation, tests, evidence, and updated handoffs to `main`, then stop for GPT/owner review. Do not deploy and do not resume broader design work.

---

# NEXT DIRECTIVE — Round 012.2 blocked; awaiting credentials + GPT review
