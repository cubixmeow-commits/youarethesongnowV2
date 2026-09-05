# First Ruck

First Ruck is a web-first prototype for a personalized beginner rucking coach. The web experience establishes the product, interaction, backend contract, and design system before the Flutter/Dart iOS client is built.

## Development context

Start with [`development-vault/START HERE.md`](development-vault/START%20HERE.md) before planning or changing FirstRuck. The vault contains the maintained current status, product direction, architecture, roadmap, accepted decisions, safety boundaries, testing, deployment, and LLM handoff rules. GPT, Cursor, and other development tools should follow [`AGENTS.md`](AGENTS.md).

## Current build

- 26-screen progressive onboarding with field-note breaks and Kip the wombat
- Deterministic readiness profile, conservative starter plan, and transparent example routes
- Today, Routes, foreground recording, reflection, Journal, Journey, local photos, and shareable postcards
- Optional MapLibre and Geoapify place search, tiles, and pedestrian route candidates
- PHP and SQLite backend with protected provider configuration and explicit spending limits
- Isolated, bounded Gemini/Groq route-ranking adapter with deterministic fallback
- Early Flutter iOS shell awaiting parity with the refined web experience

Seeded routes are explicitly demonstration data. Geoapify routes are map-derived candidates with suitability unknowns, not verified recommendations. The production route pipeline must validate facts such as access, closures, surface, hills, crossings, elevation, weather, and freshness before optional AI ranking. AI will not invent routes.

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

`https://youarethesongnow.com/FirstRuck/` (landing) and `https://youarethesongnow.com/FirstRuck/app/` (demo)

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
