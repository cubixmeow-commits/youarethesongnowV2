---
type: product-design-asset-manifest
status: approved-for-private-development-build-1
updated: 2026-08-28
area: visual-design
---

# Build 1 Artwork and Layout Graphics

This set establishes the approved visual language for the private V2 build. The images use fictional people and original scenes. They contain no lyrics, song titles, artist names, logos, album artwork, wedding-specific cues or other recognizable protected material.

The direction is premium, music-inspired and editorial: matte dark surfaces, warm vermilion and aged-gold light, tactile grain, restrained motion and artwork-led composition. Do not turn the interface into a collage. Use the layout graphics quietly behind content and let the four narrative images carry the emotional weight.

## Narrative artwork

| Purpose | Large source | Responsive source | Recommended treatment | Alt text |
|---|---|---|---|---|
| Welcome hero | `/assets/images/launch/hero-listening-room-1672.webp` | `/assets/images/launch/hero-listening-room-960.webp` | Full-bleed hero. Keep the people on the right and place copy in the calm left field behind a protective gradient. | Two people embracing in a cinematic world of warm light |
| Intimate example | `/assets/images/launch/example-intimate-1122.webp` | `/assets/images/launch/example-intimate-560.webp` | Portrait example in the artwork story or gallery preview. | Two people sharing a quiet moment beside the water at night |
| Solo example | `/assets/images/launch/example-solo-1122.webp` | `/assets/images/launch/example-solo-560.webp` | Portrait example showing the product is equally strong for one person. | A person standing in a theater that opens into a starlit landscape |
| Energetic example | `/assets/images/launch/example-energy-1122.webp` | `/assets/images/launch/example-energy-560.webp` | Portrait example showing a more adventurous visual direction. | Two people moving through a surreal landscape of light and motion |

Suggested supporting labels:

- `Two people, one shared world`
- `A song seen from the inside`
- `Another mood, another universe`

Do not use `wedding` as a product category, example label or design theme. Weddings may eventually be one use case, but they do not define this product or this build.

## Layout graphics

| Purpose | Large source | Responsive source | Recommended treatment |
|---|---|---|---|
| Editorial interlude | `/assets/images/launch/layout-interlude-1774.webp` | `/assets/images/launch/layout-interlude-960.webp` | Full-bleed section behind a short product promise, paywall moment or transition. Keep text in the calm left or central field. Do not repeat it in every section. |
| Groove field | `/assets/images/launch/layout-groove-1254.webp` | `/assets/images/launch/layout-groove-640.webp` | Low-contrast support for onboarding, gallery empty states or the generation stage. It may be cropped aggressively. Keep an additional CSS overlay behind text. |
| Mobile stage | `/assets/images/launch/layout-mobile-941.webp` | `/assets/images/launch/layout-mobile-480.webp` | Optional vertical atmosphere for narrow onboarding or generation views. Keep the center readable and do not place it behind long forms. |

## Integration requirements

- Use responsive `<picture>` or `srcset` markup with explicit width, height and aspect ratio.
- Preload only the hero and give it high fetch priority. Lazy-load below-fold images.
- Use `object-fit` and deliberate `object-position`; do not distort or indiscriminately center-crop faces.
- Maintain WCAG 2.2 AA contrast over every image at every breakpoint.
- Honor reduced motion. Artwork reveals may use a brief opacity, blur and 12-pixel movement, but no constant motion.
- Keep one filled accent action per decision view.
- Avoid glass panels, neon glow, generic AI gradients, heavy card grids and literal music clip art.
- These are provisional launch visuals. Replace example imagery with genuine V2 benchmark results before external beta when those results are ready.

## Original generation files

The lossless generation files remain outside the repository under the local Codex generated-images directory. The repository contains optimized WebP derivatives so the first build remains fast and deployable.
