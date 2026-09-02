# YouAreTheSongNow — Cursor Implementation Directive

**Round:** 015  
**Written by:** GPT design director / UX review  
**Date:** 2026-09-02  
**Status:** Ready for Cursor implementation  
**Repository:** `cubixmeow-commits/youarethesongnowV2`  
**Working branch:** `main`  
**Priority:** Replace the scrolling Create form with the approved mobile card flow  
**Deployment:** Do not deploy; commit and push verified work to `main`, then stop for GPT/owner review

## Product decision

The owner approved a true card-by-card Create experience. Mobile is canonical. The current page behaves like a long desktop form: completed sections stay expanded, the next action is often below the fold, the portrait upload instructions dominate saved portraits, Recent Creations interrupts the task, and final generation competes with the global tab bar.

Implement one focused decision group at a time:

1. Song
2. People
3. Direction
4. Review
5. Generating state

This is a customer-facing interaction redesign, not a backend rewrite. Preserve the working POV visual-narrative planner, generation APIs, draft persistence, credit behavior, authentication, portraits, owner controls, Gallery, cache-busted asset system, and provider boundaries.

## Canonical UX principle

AI removes decisions by default and offers intelligent choices when the user asks for control.

**Generate for me is the primary/default path.** Explore is secondary. Manual construction is tertiary. The default route must not ask the user to choose quality, format, style, camera language, composition, atmosphere, or special instructions before the AI prepares a direction.

## Mobile structure

### Focus shell

- On mobile, active Create uses a focused task shell: compact brand header, Back/Exit controls, four-segment progress indicator, one active card, and one contextual action region.
- Hide the global mobile bottom tab bar during the active Create flow. It must not compete with the card action or consume the final action’s viewport space. Restore normal global navigation on other pages.
- Desktop retains the premium app shell/rail, but the same single-card state machine is canonical. Center the active card in the work area; do not restore the long-form layout on desktop.
- At 390 × 844, the active card heading, primary content, and primary action should normally fit without page scrolling.
- At 320px width or short viewports, the contextual action remains visible and safe-area aware; content may scroll, but the user must never hunt for the next action.
- Do not put independent scrolling regions inside ordinary cards. Fine Tune may expand the page when deliberately opened.

### Shared card anatomy

Each decision card has:

- compact step label;
- one display heading;
- at most one short guidance paragraph;
- only controls belonging to that decision;
- one clear primary continuation action;
- inline pending/error feedback that does not create a second tall panel.

Completed cards are not left expanded below or above the active card. Progress segments and Back provide orientation.

## State contract

| State | Visible customer content | Primary action | Transition |
| --- | --- | --- | --- |
| Song | Artist, title, match/result | Use this song / confirm match | Confirmed song → People |
| People | Saved portraits first; collapsed Add portrait affordance | Continue with 1/2 people | Valid portrait selection → Direction |
| Direction choice | Generate for me, Explore 3 directions, manual link | Generate for me | Quick preparation → Review |
| Direction explore | Three compact direction rows | Use selected direction | Preparation → Review |
| Review | Direction preview + compact song/people/output summary | Generate image | Generation request → Generating |
| Generating | Dedicated progress/status | Continue to Gallery | Background job completes normally |

Only one of Song, People, Direction, or Review is the active customer card at a time.

## Card 1 — Song

- Keep the existing artist/title lookup and confirmation behavior.
- On initial entry, show only the Song card.
- On a successful confirmed match, advance automatically to People after draft synchronization succeeds.
- If multiple-match confirmation is required, confirmation remains inside the Song card; do not advance before the explicit match confirmation.
- Remove Recent Creations from the active Create flow. Recent work belongs in Gallery. Do not delete gallery data or endpoints.
- The private development Song DNA inspection must not lengthen the customer card. Keep it behind private-build diagnostics, collapsed and outside the normal customer decision path.
- Pending search uses a compact inline state. Failure preserves inputs and exposes retry.

## Card 2 — People

- Show saved portraits before upload controls.
- Make existing portrait tiles compact, clearly selected/unselected, and accessible with `aria-pressed`.
- Preserve the current product requirement of one or two selected portraits. Continue is disabled until at least one portrait is selected.
- Add an explicit contextual action: **Continue with 1 person** / **Continue with 2 people**.
- Replace the always-expanded upload form and explanatory copy with a secondary **Add another portrait** affordance. Expand upload controls only after it is requested.
- Preserve upload, privacy copy, two-portrait maximum, delete confirmation, and draft patching.
- Selecting a portrait must not unexpectedly advance. The user confirms the group with Continue.

## Card 3 — Direction

Initial state contains only:

1. **Generate for me** — primary;
2. **Explore 3 directions** — secondary;
3. **Build a direction manually** — quiet tertiary action.

Do not show quality, format, no-text, special instructions, the full style grid, Overview, or final Generate image in this initial state.

### Quick path

- Tapping Generate for me runs the real existing POV direction load, ranking, application, draft synchronization, and summary/readiness preparation.
- Replace the prior dead-end presentation with compact in-card progress.
- Await the real async chain. Preserve separate direction-loading, preparation, and generation submission locks.
- On success, advance to Review and focus/announce its heading.
- On recoverable failure, remain on Direction, show the real concise error, preserve state, and expose Retry. Never leave the user on “Preparing…” indefinitely.
- Do not auto-submit a paid generation from this button.

### Explore path

- Morph the Direction card in place; do not append a tall panel below the initial choices.
- Show exactly three compact selectable rows. Each row should use a title plus one concise differentiator, not a full paragraph.
- Selection uses radio semantics. The contextual action becomes **Use [direction name]**.
- Applying a direction runs the same preparation/readiness path and then advances to Review.
- Offer **Let AI choose instead** and a quiet manual path without resetting the song or portraits.

### Manual path

- Keep existing style functionality, but contain it within the Direction card.
- A valid manual choice advances to Review through the same draft/readiness contract.
- Do not make manual controls visually equal to Quick Generate.

## Card 4 — Review

This replaces the permanently visible Overview panel.

Show:

- selected/AI-chosen direction as the dominant review item;
- song;
- selected portrait count/thumbnails;
- output defaults in one compact row;
- credits/cost if currently required at confirmation;
- one collapsed **Fine-tune image settings** disclosure.

Move these existing controls into Fine Tune without changing their backend meaning:

- quality;
- format/orientation;
- no text;
- special instructions;
- manual style only when the chosen path permits changing it.

Defaults remain Medium, Square, text allowed, and no special instructions unless current persisted draft values say otherwise. Fine Tune changes must patch the draft, refresh readiness, preserve the Review card, and update the compact summary.

The contextual **Generate image** action must be visible on Review without scrolling at normal mobile sizes. While readiness is being verified it may be disabled with concise progress, but it must not disappear. On readiness failure, show the exact recoverable requirement beside it.

## Generating state

- After one successful Generate image submission, replace Review with a dedicated generation state.
- Prevent duplicate jobs on repeated taps.
- Explain that generation continues in the background and the user may continue to Gallery.
- Preserve existing polling, failure recovery, credit behavior, and result routing.
- A submission failure returns to a usable Review card with the Generate action available to retry.

## Navigation and restoration

- Provide an in-flow Back action on People, Direction, and Review.
- Back preserves valid prior selections.
- Changing the confirmed song invalidates only downstream data that is actually song-dependent.
- Changing portraits invalidates/rechecks direction readiness only as required by current contracts.
- Browser refresh/restored drafts return to the furthest valid card:
  - no confirmed song → Song;
  - confirmed song but no valid portrait → People;
  - song + portrait but no prepared direction → Direction;
  - prepared/reviewable direction → Review;
  - active job → Generating/current existing job behavior.
- Back/forward and focus behavior must not expose two decision cards simultaneously.
- Use a single explicit client-side flow state model. Do not infer the active step only from whether legacy sections happen to be hidden.

## Implementation guidance

Likely touch points include:

- `templates/pages/create.php`
- `templates/layouts/main.php` if a page-scoped focus-shell hook is needed
- `public/assets/js/app.js`
- `public/assets/js/song-search.js`
- `public/assets/js/explore.js`
- `public/assets/css/app.css`
- automated browser/contract tests
- `design/review/round-015/`
- both Cursor/GPT handoff documents

Reuse the current semantic controls and APIs where practical. Refactor presentation and orchestration cleanly; do not add a framework or duplicate the draft/generation state in a second incompatible store.

Maintain the intertwined YS identity and black/graphite, platinum, sapphire/cobalt system. Avoid generic SaaS cards, glassmorphism, excessive copy, gradients used as decoration, or giant empty areas.

## Accessibility and interaction requirements

- Native `hidden` must win in CSS for inactive cards.
- Move focus to the new card heading after forward/back transitions, except when that would disrupt an active pointer interaction; provide a focusable heading strategy.
- Announce pending, success, and failure changes with restrained live regions.
- Controls meet 44px minimum targets; inputs remain 16px or larger on iOS.
- Respect reduced motion and increased contrast.
- Preserve keyboard operation, visible focus, dialog semantics, and portrait delete behavior.
- No horizontal overflow at 320px.

## Required behavior tests

Add browser tests that use actual customer controls and production event chains:

1. Initial Create shows Song only; People, Direction, Review, Generate image, and Recent Creations are not visible.
2. Confirming a song advances to People and focuses/announces the People heading.
3. People displays saved portraits first; upload details remain collapsed.
4. Continue is disabled with zero portraits and correctly labels one/two selected portraits.
5. Continue advances to Direction without leaving Song or People expanded.
6. Direction initially shows Generate for me + Explore 3 directions + manual tertiary only.
7. Quick path clicks the real `[data-ai-quick]` control, performs exactly one direction request and one readiness/summary request, then shows Review with one enabled Generate image action.
8. Quick preparation failure stays recoverable on Direction and never remains indefinitely pending.
9. Explore renders exactly three compact choices, applies the selected direction once, and reaches Review.
10. Manual path reaches Review without exposing unrelated controls early.
11. Fine Tune starts collapsed; changing each setting patches the draft and keeps Review active.
12. Back preserves valid song/portrait/direction state and never shows two cards.
13. Generate image creates exactly one job, then shows the dedicated Generating state.
14. Generation failure restores a usable Review state and retry.
15. Restored drafts land on the furthest valid card.
16. Mobile global tabs are absent only during focused Create; navigation remains intact elsewhere.
17. At 320, 390, and 430px, active card/action have no horizontal overflow and the primary action is reachable without searching below unrelated content.
18. Desktop at 900 and 1440px uses the same state sequence in a centered premium work area.
19. Existing Round 013.2 release-path cache busting remains active for all changed CSS/JS.
20. Explore and Quick preserve POV planner output and do not add generation credit charges before final Generate image.

Do not satisfy tests through fixture-only prepared-state injection. At least Quick, Explore, Back, Fine Tune, and final generation must exercise their real handlers.

## Review evidence

Create sanitized evidence under `design/review/round-015/`:

- 390px screenshots: Song, People with saved portraits, Direction initial, Explore choices, Quick pending, Review with Generate visible, Fine Tune expanded, Generating;
- 320px short-viewport screenshot proving the primary action remains reachable;
- 430px screenshot;
- 900px and 1440px desktop screenshots;
- a short state-transition table and notes on focus, safe area, and global-nav behavior;
- exact tests and result count.

Do not include customer lyrics, portrait photographs, secrets, cookies, or private data. Use sanitized fixtures.

## Preserve / do not do

- Preserve POV Campaign Engine-derived planning and the canonical structured prompt compiler.
- Preserve `AssetRelease` and `/assets/r/{releaseId}/...` URLs.
- Preserve all routes, auth, privacy, billing, credits, queue ownership, providers, storage, Gallery, portrait deletion, owner style controls, and Flutter design contracts.
- Do not begin Flutter implementation.
- Do not change pricing or the portrait requirement.
- Do not deploy to Hostinger.
- Do not resume unrelated broader design work.
- Do not force-push or destructively rewrite history.

## Finish

1. Pull current `main` and confirm the Round 014/013.3 baseline.
2. Implement Round 015.
3. Run the full test suite plus syntax/lint checks.
4. Capture sanitized review evidence.
5. Update `docs/design/CURSOR-HANDOFF.md` and `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` with files changed, state model, test count, screenshots, deviations, and remaining issues.
6. Mark this inbox consumed.
7. Commit and push verified work directly to `main`.
8. Stop for GPT/owner review. Do not deploy.
