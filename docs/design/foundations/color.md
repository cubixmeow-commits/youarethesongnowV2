# Color

## Luminous Night Studio material hierarchy

1. **Black / near-black** — scaffold (`color.bg`)
2. **Graphite** — structural surfaces
3. **Smoked graphite / lacquer** — sheets, trays, controls
4. **Platinum hairlines + sapphire/cobalt light** — selection, action, micro-detail
5. **Artwork** — strongest chroma and emotion

Artwork supplies most vivid color. Blue behaves like architectural light on black material, not panel paint.

Rules:

- one cobalt filled action per decision view;
- selection uses tonal lift + edge + marker, never glow/color alone;
- gradients are permitted only as subtle atmospheric depth or artwork scrims, not button/brand decoration;
- sapphire/cobalt is the only decorative/action accent family;
- success, warning, error, and info colors are semantic only;
- large background haze stays below the contrast-critical content layer;
- `content.tertiary` must be raised/tested before use for small text; do not preserve a failing value for visual subtlety.

## Brand hex reference (from `design/BRAND_SYSTEM_YS.md`)

Approximate sRGB anchors for Flutter / print (CSS uses OKLCH):

| Role | Hex guide |
| --- | --- |
| Noir black | `#0A0A0A` |
| Graphite | `#1B1D21` |
| Platinum | `#E6E7EA` |
| Deep sapphire | `#0D1B3D` |
| Cobalt | `#1E4CFF` |

Live CSS values are OKLCH — convert carefully when exporting to Flutter `Color`.

## Contrast notes (audit)

- Primary text on bg is strong.
- `--color-text-tertiary` / `content.tertiary` was raised to `oklch(0.62 0.01 256)` in Phase 1 before new caption use. Leftover product captions were not recertified globally.
- `prefers-contrast: more` forces black/white borders (good escape hatch).
- Three color systems exist in-repo: **app** (canonical), **site** (cream/sunset marketing), **docs** (Meow Control purple/cyan). Only app tokens belong in the product system.

## Implementation checks

1. Fold `--color-silver` into semantic platinum/secondary roles unless a real component requires a distinct value.
2. Keep OKLCH as web design source and store reviewed sRGB guides for Flutter; contrast-test the actual rendered pair on each platform.
3. Warning remains semantic and rare; underuse is preferable to decorative misuse.
4. Test text, borders, focus, selected, disabled, and overlays against the lightest and darkest real artwork.
