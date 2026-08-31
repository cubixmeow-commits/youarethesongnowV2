# Iconography and imagery

## Brand marks (runtime: `public/assets/images/brand/`)

| Asset | Use |
| --- | --- |
| `ys-monogram-flat-platinum.svg` | Top bar, Create session header (primary UI mark) |
| `ys-monogram-flat-white.svg` / `…-black.svg` | One-color / accessibility / print contexts |
| `ys-monogram-premium.png` | Large/premium moments (≥96 CSS px) |
| `ys-wordmark.svg` | Top bar wordmark (decorative; accessible name is text) |
| `ys-app-icon.webp` / `.png` | App icon contexts |

Do not redraw marks in CSS. See `design/BRAND_SYSTEM_YS.md`.

## System imagery (`public/assets/images/system/`)

| Asset | Hook |
| --- | --- |
| `app-atmosphere-haze-phone.webp` / `-desktop.webp` | App background atmosphere |
| `creative-session-backdrop-phone.webp` / `-desktop.webp` | Create session backdrop |
| `empty-collection-still.webp` | Gallery empty state |
| `paywall-world-preview-phone.webp` / `-desktop.webp` | Paywall media |

## Showcase archive

77 V1 sample worlds under `public/assets/images/showcase/` with thumbs + display derivatives and `public/assets/data/v1-showcase.json`. Marketing/reference only — not signed-in Gallery data.

## Nav icons

CSS mask SVGs on `.app-nav__icon--*` (create, gallery, account, owner, signin). Not a shared icon font. Flutter should use dedicated vector assets with the same metaphors.

## Imagery rules

- Artwork prefers edge-to-edge or large framed stages
- Gallery = collection / contact sheet, not profile grid
- Avoid music clip-art (notes, vinyl, waveforms) as chrome decoration
- Generated artwork carries emotion; UI stays quiet

## Open question

Should nav icons become a small named icon set in `/assets/design/` for Flutter parity, or stay CSS-mask-only until Flutter start?
