# First Ruck

First Ruck is a web-first prototype for a personalized beginner rucking coach. The web experience establishes the product, interaction, backend contract, and design system before the Flutter/Dart iOS client is built.

## First prototype

- 10-step beginner onboarding
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
