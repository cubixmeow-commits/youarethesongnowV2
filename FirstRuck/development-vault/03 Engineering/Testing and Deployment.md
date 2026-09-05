# Testing and deployment

## Required local checks

From the repository root:

```sh
node --test FirstRuck/experience-lab/tests/flow.test.cjs FirstRuck/onboarding-lab/tests/onboarding.test.js
php FirstRuck/tests/run.php
php FirstRuck/tests/route-coach.php
php FirstRuck/tests/route-selection.php
php FirstRuck/tests/mapping.php
node --check FirstRuck/experience-lab/app.js
php -l FirstRuck/public/index.php
php -l FirstRuck/public/asset.php
php -l FirstRuck/public/mapping.php
git diff --check
```

For interaction or visual changes, run the relevant browser review:

```sh
node FirstRuck/experience-lab/tests/review.cjs
node FirstRuck/experience-lab/tests/mobile-web.cjs
```

Browser evidence lives under `docs/experience/review/`. The mobile web regression covers the complete onboarding, required answers, disabled-map state, interrupted demo recovery, pause/resume, durable journal/photo reload, postcard download, safety-answer omission, runtime errors, and widths from 320 to 1440 pixels.

## Local URLs

- Main PHP app: serve repository `public/`, then open `/FirstRuck/`.
- Design shell: serve `FirstRuck/`, then open `/experience-lab/`.
- Browser location requires localhost or HTTPS and explicit permission.

## GitHub and Hostinger

Current remote repository: `cubixmeow-commits/youarethesongnowV2`, branch `main`. The baseline app build was published in commit `51f272f`; the pack-image crop fix followed in `d4173f6`.

Hostinger should deploy the complete repository and use the repository `public/` folder as web root. FirstRuck is available at `/FirstRuck/`. The bridge depends on canonical code outside the public folder, so do not deploy only `public/FirstRuck/` by itself. Use PHP 8.1+ with SQLite, cURL, and JSON, and give PHP write access to `FirstRuck/var/`.

For live maps, create `FirstRuck/var/config.php` on the server from `config.example.php`. Never commit it. Full steps: `HOSTINGER-DEPLOYMENT.md` and `docs/experience/WEB-SETUP.md`.

## Release truth

A Git push or Hostinger preview is not a production launch. Before public launch, resolve real membership, privacy/terms, route evidence, qualified safety review, accounts/data deletion if used, monitoring, backups, and provider budgets.
