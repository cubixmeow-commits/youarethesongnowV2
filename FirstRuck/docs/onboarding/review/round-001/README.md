# Onboarding lab review — round 001

**Date:** 2026-09-04  
**Browser:** Cursor IDE browser (Chromium) against a local static copy of `FirstRuck/onboarding-lab/`  
**Also required:** the lab opens from `index.html` over `file://` with relative asset paths only  
**Viewport evidence:** 1440 × 1000 desktop review shell; 390 × 844 phone; 320 × 568 small phone; 640 CSS px for 200% reflow  

## Tests

```bash
node --test FirstRuck/onboarding-lab/tests/onboarding.test.js
node --check FirstRuck/onboarding-lab/js/model.js
node --check FirstRuck/onboarding-lab/js/screens.js
node --check FirstRuck/onboarding-lab/js/app.js
```

Result: 23 passed, 0 failed. `git diff --check` is clean for the new lab files.

## Screenshots

| File | What it shows |
| --- | --- |
| `desktop-review-shell-1440.png` | Desktop shell at ~1440 × 1000 with welcome in the phone |
| `phone-390-welcome.png` | Welcome filling a 390 × 844 viewport |
| `phone-320-welcome.png` | Welcome at 320 × 568; CTA remained on-screen, no horizontal scroll |
| `what-rucking-is.png` | Screen 2 walk-to-pack silhouette |
| `question-goal.png` | One question screen with a selected native radio |
| `safety-gate.png` | Exercise safety gate |
| `equipment-lesson.png` | Pack-fit photograph and original cross-section |
| `analysis-reduced-motion.png` | Analysis with Reduce Motion: all three checks visible, Continue required |
| `profile-reveal.png` | Personalized starting profile |
| `four-week-plan.png` | Four-week timeline, one loaded day, Week 3 changes one thing |
| `route-matches.png` | Demonstration routes with unknowns |
| `leave-ready.png` | Final checklist and Today preview action |
| `zoom-200.png` | Goal screen reflowed at 640 CSS px (200% of 1280) |

## Limitations

- Screenshots were captured from a local HTTP copy so the review browser could load the lab. Runtime paths remain relative; no remote fonts, CDNs, or APIs are referenced.
- The desktop review shell is designed around ~1440 × 1000. At 200% zoom that three-column chrome can scroll; the onboarding column itself was checked at 320 CSS px and 640 CSS px without horizontal overflow.
- Reduce Motion evidence is the analysis screenshot: checks appear together and the user continues with an explicit tap.
- Demonstration routes and generated photographs are visual models, not live trails or real First Ruck users.
- This prototype still needs owner/Codex visual review, and qualified expert review of the conservative first-month rules before any public health-and-fitness launch.

## Isolation

The live First Ruck PHP site, Flutter app, and STOPPR reference were not modified.
