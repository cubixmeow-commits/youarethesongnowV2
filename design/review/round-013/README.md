# Round 013 — Blocking Mobile Generate Action Repair

**Date:** 2026-09-01  
**Scope:** Create completion action only — no broader redesign or deploy

## Root cause

The final generate CTA lived in `[data-summary-actions]`, which stayed `hidden` until a successful **Review my creation** POST returned `ready: true`. On mobile:

1. Overview sits below the full direction column, so the CTA was off-screen even when revealed.
2. Quick Generate used fixed `setTimeout` bridges instead of awaiting review, so the action often never appeared.
3. When AI direction mode hid the manual Review button, users had no recovery path.

## Repair

| Area | Change |
| --- | --- |
| Markup | Replaced hidden `data-summary-actions` with always-present `data-generate-bar` + `data-generate-hint` + `Generate image` button |
| Mobile CSS | Fixed generate bar above `.app-nav` with safe-area inset; page padding prevents overlap |
| `app.js` | Central `updateGenerateAction()` — visible when Direction stage is open; disabled + hint when requirements missing; auto server review via `scheduleGenerationReview()` |
| `explore.js` | Quick Generate awaits `YatsnCreate.prepareAndReview()` / `submitGeneration()` — no timer race |
| Failure | `restoreGenerateActionAfterFailure()` returns actionable state after API/job errors |

## Evidence

Screenshots (private build fixtures via `window.YatsnCreateFixtures`):

| File | State | Viewport |
| --- | --- | --- |
| `mobile-320-ready.png` | Valid Quick Generate / enabled action | 320×640 |
| `mobile-390-ready.png` | Valid Quick Generate / enabled action | 390×844 |
| `mobile-390-missing-style.png` | Disabled action + missing requirement | 390×844 |
| `mobile-390-pending.png` | Pending submission | 390×844 |
| `mobile-390-recoverable-error.png` | Recoverable error with actionable button | 390×844 |

Capture: `node design/review/round-013/capture-screenshots.mjs` (requires local PHP server + Chrome).

## Verification

- `php tests/run.php` — regression assertions for markup, CSS, and JS contracts
- Manual: Generate for me on iPhone-width viewport shows reachable **Generate image** above bottom navigation
