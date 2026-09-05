# Onboarding and membership

## Current flow

The current flow has 26 screens. No group contains more than three consecutive question screens.

| Screens | Content | Why it exists |
| --- | --- | --- |
| 1–2 | Welcome; meet Kip | Establish the promise and companion |
| 3–5 | Goal; comfortable walk; recent activity | Set motivation and duration boundary |
| 6 | Small-start field note | Reward the first question group |
| 7–9 | Loaded experience; available time; weekly rhythm | Bound load, session length, and repeatability |
| 10 | Weekly rhythm field note | Connect plan to ordinary life |
| 11–13 | Safety boundary; pack; available load | Decide whether added load is appropriate and tailor setup |
| 14 | Pack check | Give practical value before more questions |
| 15–17 | Shoes/socks; surface; hills | Tailor preparation and route filters |
| 18 | Turnaround field note | Normalize control and shorter outings |
| 19–21 | Route shape; priority; starting area | Define route character and search area |
| 22 | Photo-memory field note | Preview the journal value |
| 23–25 | Starter plan; route choice; ready summary | Deliver the personalized payoff |
| 26 | Membership preview | Explain ongoing value after the payoff |

Canonical order and lesson copy: `experience-lab/flow.js`. Question definitions and deterministic plan logic are still supplied by `onboarding-lab/js/screens.js` and `onboarding-lab/js/model.js`.

## Current copy direction

- Welcome headline: “A little weight goes a long way.”
- Supporting caption: “A first ruck built around you. A little companion along the way.”
- Tone: calm, capable, warm, occasionally observant.
- Humor belongs in low-stakes field notes. Symptoms, failures, privacy, and payment use plain language.

## Membership principles

The user sees a useful plan and route before membership. Current benefits are an adapting rhythm, practical route and recording help, and a personal field journal with Kip. The screen is explicitly a preview.

Before real checkout, add actual product data, billing period and total, renewal terms, eligible trial details, restore purchases, terms, privacy, purchase progress, cancellation, and failure recovery. Preserve the plan and selected route if checkout is cancelled.

## Constraints

- Do not invent prices, savings, reviews, user counts, or trial terms.
- Do not use guilt, countdowns, false scarcity, or streak-loss pressure.
- Do not ask precise location until its benefit is explained; retain manual area search.
- Do not persist the safety answer.
- Every collected answer must affect a visible result, branch, preparation note, or route rule.
