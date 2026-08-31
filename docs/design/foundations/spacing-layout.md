# Spacing and layout

## Spacing scale (4px base)

| Token | rem | px |
| --- | --- | --- |
| `--space-1` | 0.25 | 4 |
| `--space-2` | 0.5 | 8 |
| `--space-3` | 0.75 | 12 |
| `--space-4` | 1 | 16 |
| `--space-5` | 1.5 | 24 |
| `--space-6` | 2 | 32 |
| `--space-7` | 3 | 48 |
| `--space-8` | 4 | 64 |

Touch target: `--touch-min: 48px` (some legal/progress controls still 36–44px).

## Chrome

| Token | Value | Use |
| --- | --- | --- |
| `--topbar-h` | 52px | sticky brand top bar |
| `--tabbar-h` | 64px | phone bottom nav |
| `--rail-w` | 88px | desktop left rail |
| `--app-max` | 1120px | working content |
| `--measure` | 36rem | reading measure |

Safe areas: `env(safe-area-inset-*)` on topbar, tabbar, horizontal page padding. Prefer `100svh` for app shell; some dialogs still use `vh`.

## Breakpoints observed in `app.css`

| Width | Role |
| --- | --- |
| ≤359 | ultra-narrow hero type |
| ≥390 | padding / hero action wrap |
| ≤480 | full-width buttons; summary stack |
| ≥600 | gallery denser grid |
| ≥700 | song fields 2-col |
| ≥768 | examples row; showcase 2-col; hero desktop veil |
| 768–1099 | showcase 3-col |
| ≥900 | **desktop shell**: left rail, Create split, paywall 2-col, desktop atmosphere |
| ≥1100 | showcase 4-col |

Design-system docs previously advertised phone &lt;600 / tablet 600–899 / desktop ≥900 / wide ≥1200. Implementation has additional polish breakpoints — document both, consolidate later.

## Adaptive shell

- **Phone/tablet:** fixed bottom `.app-nav`
- **Desktop ≥900:** same markup becomes left rail; body gains `padding-left: var(--rail-w)`

Create layout: single column on phone; ~62/38 sticky summary split on desktop.
