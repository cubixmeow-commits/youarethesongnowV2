---
type: design-system
status: active
updated: 2026-08-30
area: visual-design
phase: round-005-baseline
---

# You Are The Song Now — Design System (Pass 1)

This document is the web prototype of the long-term visual system for Flutter iOS/Android and desktop/web. Prefer concepts that map cleanly to `ThemeData`, `ColorScheme`, `TextTheme`, and reusable widgets.

## Product thesis

A premium creative app: choose music → create a visual interpretation → enter the world of the song → collect and explore the imagery.

Benchmark: flagship App Store product, not a responsive marketing website.

## Platform philosophy

1. Design for ~390–430 CSS px iPhone-class first.
2. Expand the same primitives to tablet/desktop.
3. Desktop is the same app on a larger canvas (split views, side rail, larger artwork), not a different product.

## Color tokens

CSS variables live in `public/assets/css/app.css` under `:root`. Flutter should mirror these names.

| Token | Role | CSS | Flutter mapping |
| --- | --- | --- | --- |
| `bg` | Base application background | `--color-bg` | `ColorScheme.surface` / scaffold |
| `surface` | Elevated content surface | `--color-surface` | `ColorScheme.surfaceContainerLow` |
| `surface-elevated` | Cards, sheets, sticky panels | `--color-surface-elevated` | `surfaceContainer` |
| `surface-chrome` | Top bar / tab bar / rail | `--color-surface-chrome` | translucent app bar |
| `text` | Primary text | `--color-text` | `onSurface` |
| `text-secondary` | Supporting copy | `--color-text-secondary` | `onSurfaceVariant` |
| `text-tertiary` | Metadata / captions | `--color-text-tertiary` | muted variant |
| `accent` | Primary creative action / selected | `--color-accent` | `primary` |
| `accent-soft` | Soft accent wash | `--color-accent-soft` | `primaryContainer` |
| `haze` | Atmospheric indigo light | `--color-haze` | decorative only |
| `surface-lacquer` | Charcoal lacquer panels | `--color-surface-lacquer` | `surfaceContainerHigh` |
| `surface-stone` | Honed stone inputs/trays | `--color-surface-stone` | `surfaceContainerHighest` |
| `accent-brass` | Warm architectural light | `--color-accent-brass` | `primary` (refined) |
| `border-hairline` | Ultra-quiet structure | `--color-border-hairline` | `outlineVariant` (lighter) |
| `border` | Quiet hairline | `--color-border` | `outlineVariant` |
| `border-strong` | Interactive outline | `--color-border-strong` | `outline` |
| `success` | Success | `--color-success` | `tertiary` / custom |
| `warning` | Warning | `--color-warning` | custom |
| `destructive` | Destructive | `--color-destructive` | `error` |
| `focus` | Focus ring | `--focus-ring` | focus highlight |

Artwork supplies most chroma. Accents behave like light, not paint.

## Spacing scale

| Token | Value | Use |
| --- | --- | --- |
| `--space-1` | 4px | micro gaps |
| `--space-2` | 8px | tight control gaps |
| `--space-3` | 12px | related items |
| `--space-4` | 16px | default padding |
| `--space-5` | 24px | section padding |
| `--space-6` | 32px | major blocks |
| `--space-7` | 48px | screen breathing |
| `--space-8` | 64px | rare hero gaps |

Touch targets: **44–52 CSS px** minimum (`--touch-min: 48px`).

Safe areas: honor `env(safe-area-inset-*)` on chrome and bottom tab bar.

## Radius scale

| Token | Value | Use |
| --- | --- | --- |
| `--radius-sm` | 8px | inputs, chips |
| `--radius-md` | 14px | buttons, small cards |
| `--radius-lg` | 20px | panels, sheets |
| `--radius-xl` | 28px | large artwork frames |
| `--radius-pill` | 999px | only segmented/selected pills when needed |

## Elevation

| Level | Treatment | Use |
| --- | --- | --- |
| 0 | flat on `bg` | page |
| 1 | hairline border + slight fill | inputs, list rows |
| 2 | soft shadow + elevated surface | sticky summary, sheets |
| 3 | deeper shadow | modal / artwork focus |

Avoid stacking floating cards everywhere.

## Typography roles

Families: **Instrument Serif** (display / emotional) + **DM Sans** (UI).

| Role | CSS class / token | Approx size | Flutter `TextTheme` |
| --- | --- | --- | --- |
| Display | `--type-display` / `.type-display` | clamp 40–56 | `displayLarge` |
| Large title | `--type-large-title` / `h1` | clamp 32–40 | `displaySmall` |
| Title | `--type-title` / `h2` | 24–28 | `headlineMedium` |
| Headline | `--type-headline` / `h3` | 18–20 | `titleLarge` |
| Body | `--type-body` | 16 / 1.5 | `bodyLarge` |
| Callout | `--type-callout` | 15 / 1.45 | `bodyMedium` |
| Caption | `--type-caption` | 13 | `labelMedium` |
| Metadata | `--type-meta` | 12 tracked | `labelSmall` |

No em dashes in product copy (product rule).

## Controls

| Control | Class | Behavior |
| --- | --- | --- |
| Primary | `.btn.btn--primary` | filled accent, one per decision view |
| Secondary | `.btn.btn--secondary` | outlined / quiet fill |
| Tertiary | `.btn.btn--ghost` | text/quiet |
| Destructive | `.btn.btn--danger` | quiet danger outline |
| Icon button | `.icon-btn` | 48×48 hit area |
| Nav item | `.app-nav__item` | icon + label |
| Segmented | `.choice-row label` | compact selectors |
| Input | native inputs styled | 16px min text (no iOS zoom) |
| Style tile | `.style-option` | selectable creative tile |
| Portrait chip | `.portrait-chip` | square album crop |

## Navigation

**Mobile:** fixed bottom tab bar (`Create`, `Gallery`, `Account`, optional `Owner`, or `Sign in` when logged out).

**Desktop (≥900px):** left app rail with the same destinations; compact top brand chrome.

Routes and labels match existing IA. Do not invent destinations.

## Surfaces & artwork

- Artwork prefers edge-to-edge or large framed stages (`.artwork`, `.reveal__figure`, `.gallery-item`, `.hero`).
- Gallery = collection / contact sheet, not profile grid.
- Create = private creative suite: curated stages, quiet secondary controls, session overview panel.

## Motion

| Token | Value | Use |
| --- | --- | --- |
| `--motion-fast` | 140ms | press / toggle |
| `--motion-base` | 220ms | hover / focus |
| `--motion-enter` | 420ms | cover reveal |
| `--ease-standard` | cubic-bezier(0.22, 1, 0.36, 1) | default |
| `--ease-emphasized` | cubic-bezier(0.2, 0, 0, 1) | entrances |

Honor `prefers-reduced-motion`.

## Breakpoints

| Name | Width | Behavior |
| --- | --- | --- |
| phone | < 600 | bottom tabs, single column |
| tablet | 600–899 | wider column, still app chrome |
| desktop | ≥ 900 | left rail + split create workspace |
| wide | ≥ 1200 | larger artwork / gallery denser |

Prefer `max-width` content shells (~430–720 for reading; wider for artwork grids).

## File map

| Concern | Location |
| --- | --- |
| Tokens + components | `public/assets/css/app.css` |
| App chrome | `templates/layouts/main.php` |
| Asset requests | `design/ASSET_REQUESTS.md` |
| Asset integration | `design/ASSET_INTEGRATION_MAP.md` |
| Flutter handoff | `design/FLUTTER_DESIGN_HANDOFF.md` |
| This system | `design/DESIGN_SYSTEM.md` |

## Pass status

Round 005 locks the premium venue system as baseline, documents asset integration (`design/ASSET_INTEGRATION_MAP.md`) and Flutter handoff (`design/FLUTTER_DESIGN_HANDOFF.md`). Targeted polish only; await ChatGPT asset delivery and visual review.
