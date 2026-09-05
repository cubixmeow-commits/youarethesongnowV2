# Repository map

Use this map before searching source.

| Path | Role | Current or historical |
| --- | --- | --- |
| `development-vault/` | Compact product and engineering source of truth | Current |
| `AGENTS.md`, `LLMS.md`, `.cursor/rules/` | LLM entry and working instructions | Current |
| `experience-lab/` | Shared current web experience, browser state, maps UI, and tests | Current reference |
| `public/` | Canonical PHP web entry, asset allowlist, APIs, and vendored MapLibre | Current |
| `src/` | PHP recommendation, mapping, database, and route-coach services | Current but partly unwired |
| `database/schema.sql` | Prototype SQLite schema and demo trail seeds | Current prototype |
| `tests/` | PHP rules, mapping, and route-coach tests | Current |
| `mobile/` | Flutter iOS shell, theme, first question, and tests | Early milestone |
| `brand/` | Product identity, logo, photography, prompts, and brand system | Current assets |
| `docs/experience/` | Detailed design, architecture, mapping research, setup, screenshots | Supporting current detail |
| `docs/flutter/` | Original native handoff and milestone contracts | Partly stale; web now leads |
| `onboarding-lab/` | Original 25-screen research prototype and deterministic rules | Historical UI, reused model |
| `CURSOR-ONBOARDING-WEB.md` | Brief that produced the original onboarding lab | Historical brief |
| `CURSOR-FLUTTER.md` | First native milestone brief | Historical/current milestone evidence |
| `bin/setup.php` | Initializes prototype SQLite state | Current utility |
| `config.example.php` | Safe example for protected Geoapify configuration | Current |
| `var/` | Runtime SQLite and protected configuration | Never commit runtime data/secrets |
| repository `public/FirstRuck/` | Hostinger bridge into canonical FirstRuck public files | Current deployment bridge |

## Fast code entry points

- Change current screens or interactions: `experience-lab/app.js`
- Change current flow or field-note copy: `experience-lab/flow.js`
- Change current visual system: `experience-lab/app.css`
- Change deterministic onboarding rules: `onboarding-lab/js/model.js`
- Change question catalog: `onboarding-lab/js/screens.js`
- Change web asset exposure: `public/asset.php`
- Change map API: `public/mapping.php`, then `src/Mapping/Geoapify.php`
- Change bounded AI ranking: `src/Coaching/RouteCoach.php`
- Change Flutter presentation: `mobile/lib/`
