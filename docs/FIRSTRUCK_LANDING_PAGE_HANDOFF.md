# FirstRuck landing page handoff

Date: 2026-09-05  
Branch: `cursor/firstruck-landing-page-4bd0`

## What changed

The public FirstRuck entry point is now a marketing landing page. The previous interactive mobile web demo is preserved and served at `/app/`.

| URL (Hostinger / repo `public/`) | Serves |
| --- | --- |
| `/FirstRuck/` | New promotional landing page |
| `/FirstRuck/app/` | Existing interactive demo (unchanged product behaviour) |
| `/FirstRuck/asset.php`, `/mapping.php`, `/api.php` | Unchanged shared endpoints |

When `FirstRuck/public` is the document root instead, the same split is `/` and `/app/`.

## Files added or modified

### Added
- `FirstRuck/public/app/index.php` — relocated demo shell with parent-relative asset/API paths
- `public/FirstRuck/app/index.php` — Hostinger bridge into the demo shell
- `FirstRuck/public/assets/landing/` — optimized JPEG/PNG derivatives (`hero.jpg`, `route.jpg`, `kip.png`, …) + `landing.css`
- `docs/FIRSTRUCK_LANDING_PAGE_HANDOFF.md` — this note

### Modified
- `FirstRuck/public/index.php` — marketing landing page (was the demo shell)
- `FirstRuck/public/asset.php` — allowlists landing CSS/images
- `FirstRuck/experience-lab/tests/mobile-web.cjs` — opens `/FirstRuck/app/`
- Docs/vault notes that pointed at `/FirstRuck/` as the demo entry:
  - `FirstRuck/README.md`
  - `FirstRuck/HOSTINGER-DEPLOYMENT.md`
  - `FirstRuck/docs/experience/WEB-SETUP.md`
  - `FirstRuck/experience-lab/README.md`
  - `FirstRuck/development-vault/01 Current Project/Current Architecture.md`
  - `FirstRuck/development-vault/03 Engineering/Web Application.md`

## Routing decisions

1. **No SPA router.** The demo is still an in-memory screen machine. Moving it to `/app/` only required HTML path rewrites.
2. **Keep `asset.php` / `mapping.php` / `api.php` at the FirstRuck public root.** Nested copies would break MapLibre tile URLs and CSRF session assumptions.
3. **Demo shell uses `../asset.php?file=` and `../mapping.php`.** Wordmark returns to `../` (landing).
4. **Landing assets also go through `asset.php`.** Under Hostinger, `public/FirstRuck/` is a bridge folder, so raw `/FirstRuck/assets/...` paths are not automatically exposed.

## Design-system decisions (UI/UX Pro Max)

Skill workflow used: design-system + landing + typography + UX domain searches.

Synthesized direction (aligned to existing FirstRuck tokens, not generic SaaS purple):

| Token | Choice | Rationale |
| --- | --- | --- |
| Style | Nature Distilled / Organic Biophilic | Outdoor field-guide feel; matches pine/paper photography |
| Pattern | Product Demo + Features | Hero → how it works → distinctives → featured demo → final CTA |
| Colour | Existing demo palette | `--pine #14331d`, `--paper #f9f3e6`, `--orange #f45707`, ink/muted/line from experience CSS |
| Type | Georgia + system sans | Matches the live demo; avoids Google Fonts (CSP is `'self'`) |
| Motion | Subtle only | Soft sticky header; respects `prefers-reduced-motion` |
| Density | Spacious marketing | Comfortable mobile-first rhythm, 48px CTAs |

Landing copy follows the real product order:

Invitation → questions + field notes → preparation → plan → route → readiness → membership preview → Today → record → reflect → journal → journey.

Kip (wombat companion) and honest demo labels are preserved. No invented prices, reviews, or health claims. No Purrnia / adventure-game framing.

## Existing functionality preserved

- Onboarding, Today, Routes, recording (GPS + labelled demo), reflection, Journal, Journey
- `asset.php` allowlist for experience/onboarding/MapLibre/brand assets
- `mapping.php` CSRF, budget, and tile proxy behaviour
- `api.php` recommendation prototype
- localStorage / IndexedDB behaviour (origin-scoped; path change does not reset it)
- Hostinger bridges for api/asset/mapping remain at `/FirstRuck/`

Internal demo links that assumed the demo lived at `./` were updated only where required (wordmark → landing, asset/API → parent).

## Verification performed

- `curl` `/FirstRuck/` → landing HTML 200
- `curl` `/FirstRuck/app/` → demo shell 200
- Landing CTA targets `app/`
- Landing CSS/images via `asset.php?file=landing-*` → 200
- Demo styles/scripts via `../asset.php?file=...` resolve to `/FirstRuck/asset.php` → 200
- `php -l` on landing + app shells + `asset.php`
- `php FirstRuck/tests/run.php` — pass
- `php FirstRuck/tests/route-coach.php` — pass
- `node --check FirstRuck/experience-lab/app.js` — pass
- `node --test` experience flow + onboarding suites — pass (28 tests)

Browser Playwright `mobile-web.cjs` was path-updated but not re-run here (requires local Chrome/Playwright paths from the original machine).

## Remaining issues / next steps

1. Re-run `FirstRuck/experience-lab/tests/mobile-web.cjs` on a machine with Playwright + Chrome against `/FirstRuck/app/`.
2. Optionally add a short “Demo” link in the demo studio chrome back to the landing (currently wordmark only).
3. Further compress `landing-kip.png` (~360KB) if Hostinger bandwidth matters.
4. After merge, smoke-check Hostinger: `/FirstRuck/`, `/FirstRuck/app/`, map bootstrap, and a demo walk.
5. Update Meow Control / dashboard snapshot only if owners want the public entry change called out there.
