# FirstRuck experience review 02

Open `index.html` directly for the design flow, or serve the parent `FirstRuck/` folder locally and visit `/experience-lab/`. Foreground GPS requires a secure context such as localhost and explicit permission. Relative assets and the original onboarding model are intentionally shared with the parent source tree.

Includes a 26-screen onboarding with a maximum of three question screens between lessons, Kip mascot, prepared plan, demonstration routes, membership preview, Today, demo/foreground recording, fixed check-ins, private local journal, Journey, photo upload and PNG postcard export.

The same experience now powers the landing page at `/FirstRuck/` and mobile web demo at `/FirstRuck/app/` through `public/index.php` and `public/app/index.php`. The standalone lab remains available for design review. Flutter and the original onboarding lab are unchanged. The web app saves walk recovery state and resized photos in IndexedDB, with journal summaries and non-sensitive answers in local storage. The safety answer is not persisted. Browser GPS is foreground-only. Live map search and pedestrian route candidates are implemented but require the optional protected Geoapify configuration; without it, routes remain clearly labeled examples. Purchases, live AI coaching and community publishing remain previews or future integrations. Nothing has been deployed. See `../docs/experience/WEB-SETUP.md`.

Design and architecture: `../docs/experience/PRODUCT-DIRECTION.md` and `BUILD-ARCHITECTURE.md`.

Validation:

```sh
node --test FirstRuck/experience-lab/tests/flow.test.cjs FirstRuck/onboarding-lab/tests/onboarding.test.js
php FirstRuck/tests/route-coach.php
node --check FirstRuck/experience-lab/app.js
```

Browser review: start a local server for `FirstRuck/` on port 8097, then run `tests/review.cjs`. The test uses installed Playwright via `PLAYWRIGHT_PATH` or the workspace runtime default, and a local Chrome installation. Evidence lives in `../docs/experience/review/`. It exercises required-answer validation, the full onboarding, recording pause/resume, journal save, postcard download, viewport overflow and runtime errors.
