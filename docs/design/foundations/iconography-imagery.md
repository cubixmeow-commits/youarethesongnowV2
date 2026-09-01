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

## Utility icon contract

- Use one named outline family with consistent 1.75–2px apparent stroke and 24px default box.
- Icons inherit `currentColor`; active/disabled/error color comes from component state.
- Do not use icons as decoration when a text label is clearer.
- Do not use music notes, headphones, waveforms, sparkles, robot/AI marks, or magic wands as generic product chrome.
- The YS monogram is identity, never a substitute for a utility icon.
- Before Flutter begins, copy the approved utility vectors into a named cross-platform asset set; do not redraw them from CSS masks in Dart.

## Imagery rules

- Artwork prefers edge-to-edge or large framed stages
- Gallery = collection / contact sheet, not profile grid
- Avoid music clip-art (notes, vinyl, waveforms) as chrome decoration
- Generated artwork carries emotion; UI stays quiet

## Open question

Create a small named vector set during the web foundation/component phase, then freeze/export it for Flutter after web review. Until that slice lands, existing CSS masks remain the runtime source.
