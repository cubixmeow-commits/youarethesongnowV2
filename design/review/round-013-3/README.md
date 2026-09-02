# Round 013.3 — Fix Quick Generate Async Dead-End

**Date:** 2026-09-02  
**Scope:** Repair `exploreInFlight` sequencing so Quick Generate completes preparation/review

## Root cause

`loadDirections(true)` set `exploreInFlight` before `continueWithDirection()`, whose first guard returned immediately. Preparation never ran; status stuck on “Preparing your creation…”.

## Repair

| Area | Change |
| --- | --- |
| `explore.js` | Split `directionLoadInFlight` and `preparationInFlight`; unlock direction loading before `await continueWithDirection()` on Quick Generate success |
| Locks | `isExploreLocked()` blocks duplicate direction loads; `preparationInFlight` blocks duplicate preparation only |

## Evidence

| File | State | Viewport |
| --- | --- | --- |
| `mobile-390-before-quick-generate.png` | Direction choice, Song DNA ready | 390×844 |
| `mobile-390-preparation-pending.png` | After real Quick Generate click, preparing | 390×844 |
| `mobile-390-ready-generate-image.png` | Preparation complete, Generate image enabled | 390×844 |
| `mobile-390-preparation-failure.png` | Recoverable summary failure, retry usable | 390×844 |

## Verification

`verify-quick-generate-chain.mjs` clicks the real `[data-ai-quick]` button (no `showPreparedReady` / `setDirectionPrepared` injection), mocks only API responses, and asserts request ordering, bar visibility, and single generation submit.

```bash
php -S 127.0.0.1:8768 -t public public/router.php
cd design/review/round-013-3 && npm install
node verify-quick-generate-chain.mjs
php tests/run.php
```
