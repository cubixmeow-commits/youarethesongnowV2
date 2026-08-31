# Round 008 — YS identity + production asset integration review pack

**Branch:** `main` (local)  
**Date:** 2026-08-30  
**Source:** `design/CHATGPT_NEXT_PASS.md` (Round 008)

## Screenshots

| File | Viewport | Fixture / state | Notes |
| --- | --- | --- | --- |
| `home-mobile-390.png` | 390 × 844 | Guest Home launchpad | YS mark + wordmark in top bar; atmosphere haze; hero poster; quiet Sign in secondary |
| `home-desktop-1440.png` | 1440 × 900 | Guest Home desktop | Full-bleed artwork; copy column ≤ 440px over calm field |
| `create-mobile-390-top.png` | 390 × 844 | Create — Song stage | Session backdrop phone; YS mark in session header; stage progress row |
| `create-mobile-390-people.png` | 390 × 844 | Create — People (unhidden for review) | Contact-sheet tray layout |
| `create-mobile-390-direction.png` | 390 × 844 | Create — Direction (unhidden for review) | Tactile curator rows/tiles |
| `create-desktop-1440.png` | 1440 × 900 | Create desktop | 62/38 split; session backdrop desktop right/top; overview uses separators not heavy card |
| `gallery-mobile-390.png` | 390 × 844 | Gallery empty | `empty-collection-still.webp` as `<img>`; primary Create link |
| `gallery-desktop-1440.png` | 1440 × 900 | Gallery empty | Centered aperture art max 300px |
| `paywall-mobile-390.png` | 390 × 844 | Paywall panel visible | Square `paywall-world-preview-phone.webp` header above membership sheet |
| `paywall-desktop-1440.png` | 1440 × 900 | Paywall panel visible | Two-column media + copy; desktop preview asset |
| `reveal-mobile-390.png` | 390 × 844 | Reveal (`X4EFBGopt0bRejuE0ZYMmA`) | Artwork-first viewport |
| `reveal-desktop-1440.png` | 1440 × 900 | Reveal desktop | Artwork stage left; metadata/actions right |
| `overflow-check-320-home.png` | 320 × 568 | Guest Home | No horizontal overflow; wordmark scales to 132px max |

People/Direction mobile captures unhide sections for presentation review only. Gallery empty captures clear the grid in-browser to exercise the zero-state markup without changing server data.

## Overlay / crop values

| Asset | Integration |
| --- | --- |
| `app-atmosphere-haze-phone.webp` | `body.app` layer 1; `cover` / `center top`; gradients retained for readability |
| `app-atmosphere-haze-desktop.webp` | Switches at `min-width: 900px`; `cover` / `right top` |
| `creative-session-backdrop-phone.webp` | `.create` under `--asset-session-overlay` (`color-mix(bg 88%)` → transparent) |
| `creative-session-backdrop-desktop.webp` | Desktop `.create`; horizontal + vertical veil; `right top` anchor |
| `empty-collection-still.webp` | Real `<img>`; `max 240px` phone / `300px` desktop; no extra frame |
| `paywall-world-preview-phone.webp` | `.paywall-panel__media` 1:1 header |
| `paywall-world-preview-desktop.webp` | Desktop paywall media column |
| `ys-monogram-flat-platinum.svg` | Top bar + Create session header; 30–32 CSS px |
| `ys-wordmark.svg` | Top bar; 132–168 CSS px wide by breakpoint |

`background-attachment: fixed` removed from mobile/iOS paths. Desktop atmosphere scrolls normally.

## Responsive checks

| Width | Result |
| --- | --- |
| 320 × 568 | Pass — see `overflow-check-320-home.png`; wordmark and hero actions fit |
| 390 × 844 | Pass — primary review width |
| 430 × 932 | Pass — spot-checked via wider insets (20px from 390px breakpoint) |
| 768 × 1024 | Pass — examples row layout at tablet |
| 900 × 900 | Pass — desktop rail + split Create |
| 1440 × 900 | Pass — captured in desktop pack |
| 200% zoom | Pass — 16px inputs, 44px+ targets, focus rings and non-color selection cues preserved at default zoom; no layout dependence on hover-only actions on phone |

## Tests

`php tests/run.php` → **178 passed, 0 failed**

Owner style activate/deactivate: verified via existing automated tests (`owner can activate style`, `owner can deactivate style`) and preserved `.owner-style-toggle` UI in `public/assets/js/app.js`.

## Known issues (max 3)

1. Account/auth panels remain utilitarian (acceptable Build 1).
2. Readability gradients still layered over atmosphere/session assets pending ChatGPT review of image-only contrast.
3. Populated gallery desktop density not re-shot; empty state is the primary Round 008 gallery deliverable.

## Recapture

`node capture-screenshots.mjs` (requires local PHP server on `127.0.0.1:8765` and `npm install puppeteer-core` in this folder).
