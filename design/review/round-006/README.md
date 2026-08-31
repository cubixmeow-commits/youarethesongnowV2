# Round 006 — Platinum + blue on black review pack

**Branch:** `cursor/visual-music-adventure-94fc`  
**Commit:** *(recorded after push)*  
**Date:** 2026-08-30  
**Source:** `design/CHATGPT_NEXT_PASS.md` (Round 006)

## Token mapping (brass → platinum/blue)

| Round 005 (removed) | Round 006 (current) | Use |
| --- | --- | --- |
| `--color-accent-brass` | `--color-platinum` | Hairlines, eyebrows, labels, nav active tint |
| `--color-accent` (warm) | `--color-accent-sapphire` | Calm selection, borders, focus |
| — | `--color-accent-cobalt` | Primary CTA gradient top, progress highlight |
| `--color-bg` (midnight) | `--color-bg` (near-black) | Scaffold |
| `--color-text` (warm ivory) | `--color-text` (cool platinum white) | Body |
| `--color-haze` (indigo) | `--color-haze` (deep sapphire) | Atmospheric shadow |
| `--sunset` (legacy warm) | removed | — |

## Where platinum is used

- Border hairlines (`--color-border-*`)
- Eyebrows and session-board labels
- Movement stage numbers (with secondary text)
- Nav active label tint (mixed with sapphire)
- Brand mark fallback rim
- Gallery/metadata hierarchy accents

## Where sapphire / cobalt are used

| Token | Use |
| --- | --- |
| **Sapphire** | Selected portraits, style/quality/format chips, session stage current, input focus, secondary hover borders, atmospheric gradients |
| **Cobalt** | Primary button gradient emphasis, venue-progress glow line |

## Remaining warm colors

| Color | Why |
| --- | --- |
| `--color-warning` | Semantic warning only — not brand |
| User artwork | Emotional color from generated images |

No brass/gold in core UI.

## Screenshots

Same protocol as Round 005 — see files in this folder.

## Tests

`php tests/run.php` → **173 passed, 0 failed**

## What ChatGPT should judge

1. Does black feel rich rather than empty?
2. Is platinum refined (not chrome/sci-fi) and blue architectural (not gaming/neon)?
3. Ready to generate atmosphere + mark assets on new palette?

## Remaining risks called out

| Risk | Notes |
| --- | --- |
| Generic SaaS | Account forms still utilitarian |
| Dating/membership | Paywall copy unchanged |
| Casino/gold | Brass removed; watch cobalt CTA saturation |
| Cyberpunk | Avoid increasing blue glow further |
| Music-player | Structure unchanged; palette only |
