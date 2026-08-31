# Round 005 — Venue system lock + Flutter handoff review pack

**Branch:** `cursor/visual-music-adventure-94fc`  
**Commit:** `9ac597b`  
**Date:** 2026-08-30  
**Source:** `design/CHATGPT_NEXT_PASS.md` (Round 005)

## Screenshots

| File | State |
| --- | --- |
| `home-mobile-390.png` | Guest Home launchpad |
| `create-mobile-390-top.png` | Create header + song stage |
| `create-mobile-390-people.png` | People / portrait tray |
| `create-mobile-390-direction.png` | Direction curator controls |
| `gallery-mobile-390.png` | Gallery empty collection state |
| `home-desktop-1440.png` | Guest Home desktop |
| `create-desktop-1440.png` | Create split + overview panel |
| `gallery-desktop-1440.png` | Gallery desktop empty state |

People/Direction mobile captures unhide sections for presentation review.

## Round 005 changes

### A. Locked venue design system
- Material hierarchy documented in CSS header and `DESIGN_SYSTEM.md`
- Harmonized nav active state, confirm sheet, buttons (restrained brass)
- Smaller curator option tiles (less dashboard-like)
- Gallery empty state markup + asset hook (`.gallery-empty__art`)

### B. Targeted Create/Home copy
- Reduced literal venue wording: “Private suite” → “Create”; “Enter the suite” → “Begin creating”
- Overview panel: “Overview” / “Your creation”
- Home eyebrow restored to product line, not venue label

### C. Asset integration readiness
- New `design/ASSET_INTEGRATION_MAP.md` — hooks, paths, crops, fallbacks, Flutter mapping
- CSS variables: `--asset-launch-mark`, `--asset-empty-collection`, `--asset-session-overlay`

### D. Flutter / iOS handoff
- New `design/FLUTTER_DESIGN_HANDOFF.md` — tokens, widget map, responsive model, iOS notes

## Remaining inconsistencies to judge

| Risk | Notes |
| --- | --- |
| Generic SaaS | Account forms still utilitarian; acceptable for Build 1 |
| Dating/membership | Paywall copy unchanged; still emotional not steamy |
| Casino/gold cliché | Brass toned down Round 005; watch primary buttons in review |
| Literal venue metaphor | Copy reduced; feel should come from materials |
| Instrument-like | No playhead/console; direction grid still functional |

## Flutter readiness

**Yes for specification** — tokens, components, assets, and responsive model are documented. **No for implementation** — assets not delivered; widgets not built.

## Tests

`php tests/run.php` → **173 passed, 0 failed**

## What ChatGPT should judge

1. Is the venue system consistent enough to freeze before asset drop-in?
2. Does `FLUTTER_DESIGN_HANDOFF.md` give enough structure to start native UI?
3. Generate `app-atmosphere-haze` + `launch-mark-tile` now?
