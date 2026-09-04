# First Ruck onboarding lab

A self-contained beginner-onboarding walkthrough for product and visual review. Open `index.html` directly in Safari, Chrome, or Vivaldi. No server, account, location permission, map provider, AI, or purchase SDK is involved.

First Ruck is a first-month coach for civilians who are new to walking with load. This lab turns walking comfort, existing gear, and route character into a conservative first session and a four-week shape.

## Open

```text
FirstRuck/onboarding-lab/index.html
```

Above 1000 px, a desktop review shell surrounds a 390 × 844 phone viewport. Below that width, the walkthrough fills the screen.

## What this prototype does

- 25 original screens, with chapter progress rather than a fake global percentage
- Deterministic local recommendations in `js/model.js`
- A 5 lb first-session added-load ceiling, with an empty pack as a valid start
- One loaded day each week for the first month, plus optional ordinary walks
- Clearly labeled demonstration routes, never live trails
- Safety answers kept in session memory only

## What it does not do

- Modify the live First Ruck PHP site or Flutter app
- Call geolocation, trail providers, or AI
- Store medical details beyond the current browser session
- Present user counts, ratings, testimonials, or outcome promises
- Charge, authenticate, or request notifications

## Reset

Use **Reset walkthrough** in the desktop review shell. That clears `localStorage` and `sessionStorage` keys for this lab.

## Tests

```bash
node --test FirstRuck/onboarding-lab/tests/onboarding.test.js
node --check FirstRuck/onboarding-lab/js/model.js
node --check FirstRuck/onboarding-lab/js/screens.js
node --check FirstRuck/onboarding-lab/js/app.js
```

Review screenshots live in `../docs/onboarding/review/round-001/`.
