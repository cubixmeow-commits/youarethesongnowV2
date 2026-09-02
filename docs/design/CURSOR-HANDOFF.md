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
