# Color

## Material hierarchy (locked baseline)

1. **Black / near-black** — scaffold (`color.bg`)
2. **Graphite** — structural surfaces
3. **Smoked graphite / lacquer** — sheets, trays, controls
4. **Platinum hairlines + sapphire/cobalt light** — selection, action, micro-detail
5. **Artwork** — strongest chroma and emotion

Artwork supplies most vivid color. Blue behaves like architectural light on black material, not panel paint.

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
- `--color-text-tertiary` and secondary nav at reduced opacity may fail WCAG for small text — verify in Phase 2.
- `prefers-contrast: more` forces black/white borders (good escape hatch).
- Three color systems exist in-repo: **app** (canonical), **site** (cream/sunset marketing), **docs** (Meow Control purple/cyan). Only app tokens belong in the product system.

## Open questions

1. Should `--color-silver` remain or fold into platinum/secondary text?
2. Exact sRGB Flutter hex table vs OKLCH source of truth?
3. Warning color usage — currently tokenized but barely used in UI.
