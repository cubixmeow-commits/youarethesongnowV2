# Design tokens

## Implementation today

All live product tokens live in `public/assets/css/app.css` under `:root` (Round 008). Flutter mapping notes exist in `design/FLUTTER_DESIGN_HANDOFF.md`.

There is a **legacy alias layer** (`--ink`, `--paper`, `--stage`, etc.) retained for older hooks. New work should use `--color-*`, `--space-*`, `--type-*`, `--motion-*`.

Breakpoints are **not** tokenized in CSS — they are hardcoded media queries.

## Approved canonical semantic set

These names are stable across CSS custom properties, the canonical JSON export, and Flutter `ThemeExtension`s. Runtime migration is incremental and begins with the Phase 1 foundation slice; visual/behavioral changes still require screenshot review.

Canonical export: `assets/design/tokens/semantic-tokens.json`. The older `semantic-tokens.proposed.json` is superseded.

### Color — surfaces

| Semantic token | Current CSS | Role |
| --- | --- | --- |
| `color.bg` | `--color-bg` | Near-black scaffold |
| `color.surface` | `--color-surface` | Graphite structural |
| `color.surfaceElevated` | `--color-surface-elevated` | Raised graphite |
| `color.surfaceLacquer` | `--color-surface-lacquer` | Smoked sheet / controls |
| `color.surfaceStone` | `--color-surface-stone` | Quiet working area |
| `color.surfaceChrome` | `--color-surface-chrome` | Top bar / tab / rail |

### Color — content & brand

| Semantic token | Current CSS | Role |
| --- | --- | --- |
| `color.text` | `--color-text` | Primary platinum text |
| `color.textSecondary` | `--color-text-secondary` | Supporting |
| `color.textTertiary` | `--color-text-tertiary` | Meta / captions |
| `color.platinum` | `--color-platinum` | Hairlines / fine edge |
| `color.accent` | `--color-accent-sapphire` | Selection / calm brand |
| `color.accentStrong` | `--color-accent-cobalt` | Primary action emphasis |
| `color.accentSoft` | `--color-accent-soft` | Soft wash |
| `color.haze` | `--color-haze` | Atmospheric only |
| `color.border` | `--color-border` | Default outline |
| `color.borderHairline` | `--color-border-hairline` | Finest rules |
| `color.borderStrong` | `--color-border-strong` | Interactive outline |
| `color.success` | `--color-success` | Success |
| `color.warning` | `--color-warning` | Warning (underused) |
| `color.destructive` | `--color-destructive` | Danger / delete |
| `color.focus` | `--focus-ring` | Focus ring (*value is a shadow, not a color — see open question*) |

### Space

| Semantic | CSS | px |
| --- | --- | --- |
| `space.1` … `space.8` | `--space-1` … `--space-8` | 4 … 64 |

### Radius

| Semantic | CSS | Use |
| --- | --- | --- |
| `radius.sm` | `--radius-sm` | inputs, chips |
| `radius.md` | `--radius-md` | buttons |
| `radius.lg` | `--radius-lg` | panels / sheets |
| `radius.xl` | `--radius-xl` | artwork frames |
| `radius.pill` | `--radius-pill` | filters only when needed |

### Motion

| Semantic | CSS | Use |
| --- | --- | --- |
| `motion.fast` | `--motion-fast` | 140ms press/toggle |
| `motion.base` | `--motion-base` | 220ms hover/focus |
| `motion.enter` | `--motion-enter` | 420ms cover reveal |
| `ease.standard` | `--ease-standard` | default |
| `ease.emphasized` | `--ease-emphasized` | entrances |

### Layout chrome

| Semantic | CSS | Notes |
| --- | --- | --- |
| `layout.touchMin` | `--touch-min` | 48px |
| `layout.topbar` | `--topbar-h` | 52px + safe area |
| `layout.tabbar` | `--tabbar-h` | 64px + safe area |
| `layout.rail` | `--rail-w` | 88px desktop |
| `layout.appMax` | `--app-max` | 1120px |
| `layout.measure` | `--measure` | 36rem reading |

### Layout bands

| Semantic | Value | Behavior today |
| --- | --- | --- |
| `bp.phoneMax` | 599 | bottom tabs |
| `bp.tabletMin` | 600 | wider grids |
| `bp.desktopMin` | 900 | left rail + Create split |
| `bp.wideMin` | 1200 | denser gallery / showcase |

Also present in CSS but not in the Round 008 design-system table: `390`, `480`, `700`, `768`, `1100`. Phase 2 should decide whether to collapse to the four named breakpoints above.

### Deprecate incrementally

- Legacy aliases: `--ink`, `--charcoal`, `--paper`, `--muted`, `--stage`, `--line`, …
- Unused / weakly used: `--color-silver`, `--asset-launch-mark` (token declared; markup uses path directly), duplicate max-width hardcodes

## First adoption slice

1. Add semantic aliases while retaining legacy aliases until usage reaches zero.
2. Split `--focus-ring` into `color.focus` + `elevation.focusRing`.
3. Raise/test tertiary content contrast before applying it broadly.
4. Render canonical component states in a private component lab.
5. Refactor current Explore presentation onto canonical components without changing its endpoint/bridge.
6. Export reviewed sRGB values to Flutter ThemeExtensions only when Flutter work is authorized.

See `assets/design/tokens/semantic-tokens.json` and `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`.
