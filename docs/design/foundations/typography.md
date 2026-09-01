# Typography

## Families (app)

| Role | Family | Loading |
| --- | --- | --- |
| Display / emotional | **Instrument Serif** | Google Fonts in `templates/layouts/main.php` |
| UI / body | **DM Sans** | Google Fonts |

Fallbacks: Iowan/Palatino/Georgia for display; Avenir Next / Segoe UI / system-ui for UI.

This pairing is finalized for Luminous Night Studio. Self-host licensed WOFF2 files before external beta and bundle equivalent assets for Flutter so product typography does not depend on a runtime font network.

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
- Instrument Serif is reserved for emotional headings, the Song DNA/reveal moment, and sparse editorial labels. Do not use it for long body copy, dense settings, buttons, or error messages.
- DM Sans owns body, navigation, labels, controls, status, account, and owner operations.
- Minimum mobile form/control text is 16px. Meta text stays at 12px only when contrast and line height pass.
- Use tabular numerals for credits, prices, durations, and aligned counts.
- Keep body measure near 36rem; major display headings target 2–3 lines on compact screens.

## Audit findings

- Create screen uses `<p class="session-header__title">` instead of an `<h1>` — heading hierarchy gap.
- Nav labels use one-off sizes (`0.68rem` / `0.7rem`) outside the type scale.
- Docs Meow Control uses Orbitron/Outfit — intentionally separate from product UI.
- Site microsite uses Instrument/DM but a different color language.
