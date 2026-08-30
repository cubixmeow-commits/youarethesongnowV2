# Round 004 — Premium private creative venue review pack

**Branch:** `cursor/visual-music-adventure-94fc`  
**Commit:** `035b855`  
**Date:** 2026-08-30  
**Source:** `design/CHATGPT_NEXT_PASS.md` (Round 004)

## Screenshots

| File | State |
| --- | --- |
| `home-mobile-390.png` | Guest Home launchpad (390×844) |
| `create-mobile-390-top.png` | Create suite header + song stage |
| `create-mobile-390-people.png` | People / portrait selection tray |
| `create-mobile-390-direction.png` | Direction curator controls |
| `gallery-mobile-390.png` | Gallery private collection (empty) |
| `home-desktop-1440.png` | Guest Home desktop |
| `create-desktop-1440.png` | Create split suite + session overview |
| `gallery-desktop-1440.png` | Gallery desktop |

People/Direction mobile captures unhide sections for presentation review (no behavior change).

## Key visual changes (Round 004)

### Philosophy shift
- **From:** creative instrument / track console / session board / playhead progress
- **To:** premium private creative venue / curated stages / gallery selection / architectural brass light

### Instrument-like cues softened or removed
- Boxed “track source” panel → hairline-composed song fields
- “Session board” / “Creative session” copy → “In review” / “Private suite”
- Playhead progress bar → subtle `venue-progress` glow line
- Orange CTA accent → champagne/brass gradient primary
- Pill-heavy control chrome → restrained lacquer/stone surfaces
- Portrait tray “source material” framing → gallery casting selection

### Premium venue cues introduced
- Material tokens: `--color-surface-lacquer`, `--color-surface-stone`, `--color-accent-brass`, `--color-border-hairline`
- Subtle full-page grain overlay (Flutter-portable noise asset candidate)
- Warmer ivory text, quieter indigo atmosphere
- Gallery exhibition spacing + quiet uppercase metadata
- Home/interlude hospitality copy (“private creative venue”, “Enter the suite”)
- Account/auth panels use lacquer stone gradient (not flat SaaS card)

### New/changed design tokens
See `design/DESIGN_SYSTEM.md` and `:root` in `public/assets/css/app.css`.

## Files changed

- `public/assets/css/app.css`
- `templates/pages/create.php`
- `templates/pages/welcome.php`
- `templates/pages/gallery.php`
- `design/ASSET_REQUESTS.md`
- `design/DESIGN_SYSTEM.md`
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
- `design/CHATGPT_NEXT_PASS.md` (consumed)

## Functionality

Design-only. Portrait deletion, navigation, routes, APIs, and generation behavior unchanged. `php tests/run.php` → **173 passed, 0 failed**.

## What ChatGPT should judge

1. Does Create now feel like a hosted private suite rather than operating equipment?
2. Is the brass/champagne accent restrained enough to stay premium (not casino/luxury cliché)?
3. Generate `app-atmosphere-haze` + `launch-mark-tile` next, or refine venue surfaces further?
