# Round 013.1 — Restore Direction Choice Hierarchy

**Date:** 2026-09-01  
**Scope:** Regression correction for Round 013 mobile Generate bar

## Root cause

`.create__generate-bar { display: grid; }` overrode the native `hidden` attribute, so `bar.hidden = true` did not hide the bar. Round 013 also tied bar visibility to Direction-section visibility instead of an explicit prepared-direction state, forcing the final CTA before **Generate for me** / **Explore options** and showing incorrect readiness copy.

## Repair

| Area | Change |
| --- | --- |
| CSS | `.create__generate-bar[hidden] { display: none !important; }` |
| `app.js` | `directionPrepared` + `directionPath`; `shouldShowGenerateBar()` gates final CTA |
| `explore.js` | Quick Generate prepares only — no auto-submit; `clearDirectionPrepared()` on explore start / manual back-out |
| Readiness | Separate confirmed-song vs missing-song messages; restored drafts never report song missing |

## Evidence

| File | State | Viewport |
| --- | --- | --- |
| `mobile-390-people-stage.png` | Song confirmed, no portrait — no generate bar | 390×844 |
| `mobile-390-direction-choice.png` | Generate for me + Explore options, no final bar | 390×844 |
| `mobile-320-direction-choice.png` | Direction choice hierarchy | 320×640 |
| `mobile-390-explore-options.png` | Three directions, no premature bar | 390×844 |
| `mobile-390-prepared-ready.png` | Prepared direction + reachable Generate image | 390×844 |
| `mobile-390-prepared-missing-style.png` | Disabled final action + requirement | 390×844 |
| `mobile-390-prepared-pending.png` | Pending submission | 390×844 |
| `mobile-390-prepared-error.png` | Recoverable error | 390×844 |
| `mobile-390-restored-draft.png` | Restored draft — no false song-missing copy | 390×844 |

## Verification

```bash
php -S 127.0.0.1:8765 -t public public/router.php
cd design/review/round-013-1 && npm install
node verify-create-direction-flow.mjs
node capture-screenshots.mjs
php tests/run.php
```
