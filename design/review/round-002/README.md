# Round 002 — Visual review pack

**Branch:** `cursor/visual-music-adventure-94fc`  
**Commit:** *(filled at commit time — see git log)*  
**Date:** 2026-08-30  
**Source instructions:** `design/CHATGPT_NEXT_PASS.md` (Pass 2)

## Viewports captured

| File | Intended | Actual capture size | Auth state |
| --- | --- | --- | --- |
| `home-mobile-390.png` | 390 CSS px | ~384×689 device px | Logged out |
| `create-mobile-390.png` | 390 CSS px | ~384×689 | Logged in (owner) |
| `gallery-mobile-390.png` | 390 CSS px | ~384×689 | Logged in (owner), empty collection |
| `home-desktop-1440.png` | ~1440 CSS px | ~1240×995 (browser chrome limited) | Logged out |
| `create-desktop-1440.png` | ~1440 CSS px | ~1240×995 | Logged in (owner) |
| `gallery-desktop-1440.png` | ~1440 CSS px | ~1240×995 | Logged in (owner), empty |

Desktop captures are slightly under 1440 CSS width due to the review browser window; layout still shows left rail + expanded app shell.

## What changed in Pass 2 (for reviewers)

- Guest Home → app launchpad: adventure/solo hero, “Start with a song”, examples reordered (solo/energy first; intimate last)
- Create → Creative Session header + progress movements + session board (fields/behavior unchanged)
- Owner tab visually de-emphasized (still present for private Build 1)
- Auth panels less card/membership-funnel framed
- Palette/chrome foundation preserved (amber + indigo + graphite; bottom tabs / rail)

## What Cursor wants ChatGPT to judge

1. Does guest Home now read as **opening a creative app / beginning a journey**, not dating/membership marketing?
2. Does Create feel like a **creative instrument / session**, or still too form-like?
3. Is desktop Create’s session board + negative space intentional, or still sparse/SaaS?
4. Is Owner de-emphasis enough without hiding the route?
5. Which assets should ChatGPT generate next (still recommend `app-atmosphere-haze` + `launch-mark-tile`)?

## Intentionally not captured

- Paywall panel (requires unsubscribed account path)
- Reveal / image detail with real artwork (empty gallery in this private DB)
- Activate invitation flow
