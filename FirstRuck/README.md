# First Ruck

First Ruck is a web-first prototype for a personalized beginner rucking coach. The web experience establishes the product, interaction, backend contract, and design system before the Flutter/Dart iOS client is built.

## First prototype

- 12-step beginner onboarding
- Deterministic readiness profile and starter-session recommendation
- Ranked demonstration routes with transparent match reasons
- Responsive mobile-app shell for browser review
- PHP 8.2+ backend and SQLite persistence
- Provider-neutral seams for future trail-data and AI integrations

The seeded routes are explicitly demonstration data. The next geographic slice will replace them with verified candidates from OpenStreetMap, NPS, USFS, elevation, and weather providers. AI will rank and explain verified candidates; it will not invent routes.

## Run locally

```bash
php bin/setup.php
php -S 127.0.0.1:8088 -t public
```

Open `http://127.0.0.1:8088`.

## Test

```bash
php tests/run.php
```

## Hostinger shape

The existing repository-style Hostinger deployment can serve the prototype at:

`https://youarethesongnow.com/FirstRuck/`

Hostinger's root rewrite sends `/FirstRuck/` into `repo V2/public/FirstRuck`, where a small deployment bridge loads the application from `FirstRuck/public`. The First Ruck root `.htaccess` blocks web access to application internals. Keep `FirstRuck/var` writable by PHP. Runtime SQLite files and provider secrets are ignored by Git.

For a later dedicated deployment, point `firstruck.youarethesongnow.com` directly at `FirstRuck/public`; that removes the repository path from the public URL.

## Product design baseline

- **Visual thesis:** a calm field guide brought to life with topographic lines, warm trail-paper surfaces, forest ink, and one safety-orange action color.
- **Content plan:** focused onboarding, personal profile reveal, Today route, supporting alternatives, route detail.
- **Interaction thesis:** the route draws itself during analysis, onboarding advances with directional restraint, and recommendation details expand in place without losing context.

## Flutter / iOS handoff

Flutter work starts from [`CURSOR-FLUTTER.md`](./CURSOR-FLUTTER.md). The supporting product, design, architecture, API, build, and iOS testing contracts live in [`docs/flutter/`](./docs/flutter/README.md).

The first mobile milestone is intentionally narrow: create the native Flutter shell in `FirstRuck/mobile`, reproduce the welcome screen and first onboarding interaction, and run it in the iOS Simulator. The existing web prototype remains the visual and behavioral reference while the Flutter interface is implemented with native widgets.

## Researched onboarding lab

The implementation-ready brief for the next web-first onboarding experiment is [`CURSOR-ONBOARDING-WEB.md`](./CURSOR-ONBOARDING-WEB.md). It combines beginner rucking evidence, an audit of the 54-screen STOPPR reference walkthrough, an original 25-screen First Ruck sequence, deterministic prototype rules, and the review/acceptance contract. Cursor must build it as an isolated self-contained lab before any full Flutter port.

The original First Ruck identity, logo exports, six premium onboarding photographs, usage rules, alt text, and generation prompts are in [`brand/`](./brand/). These assets are approved for the isolated onboarding lab and must not be confused with STOPPR reference assets.

## Expanded experience review (2026-09-04)

`experience-lab/index.html` contains the new onboarding-to-journal design candidate, including Kip the wombat, teaching breaks, membership preview, recording and postcard export. See `docs/experience/PRODUCT-DIRECTION.md` and `docs/experience/BUILD-ARCHITECTURE.md` for the design and implementation boundaries. This experience now also powers the main mobile web app. The original onboarding lab and Flutter remain unchanged. See `docs/experience/WEB-SETUP.md` for local launch, optional mapping configuration and remaining integration boundaries.
