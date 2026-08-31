# Typography

## Families (app)

| Role | Family | Loading |
| --- | --- | --- |
| Display / emotional | **Instrument Serif** | Google Fonts in `templates/layouts/main.php` |
| UI / body | **DM Sans** | Google Fonts |

Fallbacks: Iowan/Palatino/Georgia for display; Avenir Next / Segoe UI / system-ui for UI.

## Type roles

| Role | CSS | Approx | Flutter map |
| --- | --- | --- | --- |
| Display | `--type-display` / `.type-display` | clamp 40–56 | `displayLarge` |
| Large title | `--type-large-title` / `h1` | clamp 32–40 | `displaySmall` |
| Title | `--type-title` / `h2` | 24–28 | `headlineMedium` |
| Headline | `--type-headline` / `h3` | 18–20 | `titleLarge` |
| Body | `--type-body` | 16 / 1.5 | `bodyLarge` |
| Callout | `--type-callout` | 15 | `bodyMedium` |
| Caption | `--type-caption` | 13 | `labelMedium` |
| Meta | `--type-meta` | 12 tracked | `labelSmall` |

## Product copy rules

- No em dashes in product copy (owner rule).
- Prefer emotional, music-aware language over AI/technical jargon.
- Do not expose model/provider names in customer UI.

## Audit findings

- Create screen uses `<p class="session-header__title">` instead of an `<h1>` — heading hierarchy gap.
- Nav labels use one-off sizes (`0.68rem` / `0.7rem`) outside the type scale.
- Docs Meow Control uses Orbitron/Outfit — intentionally separate from product UI.
- Site microsite uses Instrument/DM but a different color language.
