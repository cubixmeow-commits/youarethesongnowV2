---
type: production-asset-manifest
status: delivered
updated: 2026-08-30
area: visual-design
identity: YS
---

# YS Production Asset Manifest

## Visual thesis

**A platinum threshold in a silent black gallery, lit by deep sapphire.**

The brand surfaces should feel architectural, restrained and tactile. The UI supplies structure, user artwork supplies emotional color, and system imagery creates depth without becoming the subject.

## Delivery inventory

| Asset | Final dimensions | Format | Alpha | Intended use |
| --- | ---: | --- | --- | --- |
| `public/assets/images/brand/ys-monogram-premium.png` | 2048 × 2048 | PNG | yes | Large brand/reveal moments at 96 CSS px or larger |
| `public/assets/images/brand/ys-monogram-flat-platinum.svg` | 512 viewBox | SVG | yes | Default in-app mark, dark merchandise, print |
| `public/assets/images/brand/ys-monogram-flat-white.svg` | 512 viewBox | SVG | yes | One-color white reproduction |
| `public/assets/images/brand/ys-monogram-flat-black.svg` | 512 viewBox | SVG | yes | Light merchandise and print |
| `public/assets/images/brand/ys-app-icon.png` | 1024 × 1024 | PNG | no | Store/icon master and large icon preview |
| `public/assets/images/brand/ys-app-icon.webp` | 1024 × 1024 | WebP | no | In-app or web icon when a raster tile is desired |
| `public/assets/images/brand/ys-wordmark.svg` | 960 × 160 viewBox | SVG | yes | Header/rail lockup; pair with an accessible text name |
| `public/assets/images/system/app-atmosphere-haze-phone.webp` | 1290 × 2796 | WebP | no | Global phone scaffold atmosphere |
| `public/assets/images/system/app-atmosphere-haze-desktop.webp` | 2400 × 1600 | WebP | no | Global desktop scaffold atmosphere |
| `public/assets/images/system/creative-session-backdrop-phone.webp` | 1290 × 2200 | WebP | no | Phone Create workspace background |
| `public/assets/images/system/creative-session-backdrop-desktop.webp` | 2200 × 1600 | WebP | no | Desktop Create workspace background |
| `public/assets/images/system/empty-collection-still.webp` | 1200 × 1200 | WebP | yes | Gallery zero state |
| `public/assets/images/system/paywall-world-preview-phone.webp` | 1200 × 1200 | WebP | no | Mobile paywall/sheet preview |
| `public/assets/images/system/paywall-world-preview-desktop.webp` | 1600 × 1000 | WebP | no | Desktop paywall preview |

Raster masters were created with the built-in OpenAI image generation workflow, visually inspected, resized/cropped to the exact production dimensions above, stripped of metadata and exported in sRGB-compatible PNG/WebP. The flat monogram and wordmark are deterministic SVG assets.

## Responsive usage rules

### Brand

- Use `ys-monogram-flat-platinum.svg` at 28–40 CSS px in the mobile top bar and desktop rail.
- Use `ys-wordmark.svg` at approximately 132–190 CSS px wide depending on available space.
- Use `ys-monogram-premium.png` only where its material detail is visible. It must not replace the small flat navigation mark.
- Use `ys-app-icon.webp` only as a tile. Do not crop the emblem out of the tile.
- Keep live accessible brand text or an explicit accessible name. SVG/PNG artwork does not replace semantics.

### Global atmosphere

- Phone: `background-size: cover; background-position: center top`.
- Desktop: `background-size: cover; background-position: right top`.
- Preserve the existing readability gradients above the image during the first integration review. Reduce them only after screenshots prove text remains readable.
- Do not animate or parallax the image on mobile. Avoid `background-attachment: fixed` on iOS.

### Create backdrop

- Apply to the Create page only, below an opaque-to-transparent readability veil.
- Phone position: `center top`.
- Desktop position: `right top`; keep the main form column in the calm left field.
- Do not place the image independently behind each movement. It is one continuous workspace atmosphere.

### Gallery empty state

- Render as a real `<img>` with an empty `alt` value when the surrounding heading/copy already explains the state.
- Mobile maximum: 240 CSS px. Desktop maximum: 300 CSS px.
- Do not add a second framed container around the artwork.

### Paywall world preview

- Use the square phone asset above or behind the membership sheet, with the lower portion masked by the sheet.
- Use the 16:10 desktop asset as the media side of a two-column paywall composition.
- Never place price or legal disclosure directly over the bright horizon. All billing copy remains live HTML.

## Normalized generation prompts

The raster assets were generated individually. These are the final normalized production prompts.

### Premium YS monogram

```text
Use case: logo-brand
Asset type: premium rendered primary monogram
Primary request: one original intertwined capital Y and S as a single sculptural emblem; the Y flows into the S and remains legible at small size.
Style/medium: satin platinum brand object with restrained sapphire edge light.
Composition: centered, front-facing, square, generous clear margin, transparent background.
Constraints: Y and S only; no wordmark, music symbols, AI sparkles, gold, brass, neon, sci-fi chrome, extra text or watermark; silhouette must not depend on glow.
```

### YS app icon

```text
Use case: logo-brand
Asset type: 1024px app icon
Primary request: simplified intertwined Y and S with one clean Y stem and one continuous S ribbon, readable at 28–32px.
Style/medium: satin-platinum emblem on a matte near-black graphite rounded-square tile.
Composition: centered emblem at about 62 percent of the canvas with store-icon safe margin.
Constraints: no wordmark, extra symbols, music imagery, neon, purple, cyan, gold, brass, extra text or watermark.
```

### Global phone atmosphere

```text
Use case: stylized-concept
Asset type: 1290 × 2796 phone scaffold background
Primary request: abstract premium gallery light and material; near-black graphite field, deep sapphire architectural shadow, thin platinum grazing light and tiny cobalt reflection.
Composition: calm dark center for UI; energy only near upper-right and lower-left edges.
Constraints: no literal room, subject, text, logo, people, music imagery, particles, neon, warm metal or UI framing.
```

### Global desktop atmosphere

```text
Use case: stylized-concept
Asset type: 2400 × 1600 desktop scaffold background
Primary request: wide abstract premium gallery field with calm left/center workspace and controlled sapphire/platinum depth on the right.
Composition: left rail and central content remain near-black; architectural light held to upper-right and lower-right edges.
Constraints: no literal room, subject, text, logo, people, music imagery, particles, neon, warm metal or UI framing.
```

### Create phone backdrop

```text
Use case: stylized-concept
Asset type: 1290 × 2200 Create background
Primary request: almost-monochrome black lacquer and graphite stone with a platinum crown light and sapphire far-edge shadow.
Composition: upper detail, exceptionally calm central 70 percent, smooth fade to black at bottom.
Constraints: no objects, people, symbols, text, logo, music imagery, particles, neon, warm metal or UI framing.
```

### Create desktop backdrop

```text
Use case: stylized-concept
Asset type: 2200 × 1600 Create background
Primary request: wide black lacquer and graphite architectural material field with a platinum upper-right detail and sapphire far-right depth.
Composition: left and center stay calm for form and sticky summary; detail remains on the right.
Constraints: no objects, people, symbols, text, logo, music imagery, particles, neon, warm metal or UI framing.
```

### Gallery empty state

```text
Use case: stylized-concept
Asset type: 1200 × 1200 Gallery zero-state art
Primary request: one thin satin-platinum square aperture with deep sapphire interior depth and one tiny cobalt point, suggesting a collection awaiting its first work.
Composition: centered, strong silhouette, transparent exterior.
Constraints: not a folder or picture-frame icon; no text, logo, people, music imagery, fantasy portal, warm metal or watermark.
```

### Paywall previews

```text
Use case: stylized-concept
Asset type: 1200 × 1200 phone and 1600 × 1000 desktop paywall world previews
Primary request: a dark gallery threshold opening into an original nocturnal landscape of black stone terraces and distant sapphire/platinum light.
Composition: preserve a calm dark copy-safe region; hold the horizon away from billing text.
Constraints: no people, dating-ad framing, sci-fi portal, city, fantasy castle, music imagery, text, price, logo, warm metal or watermark.
```

## Do not regress

- Do not recolor the system imagery toward amber, gold, purple or cyan.
- Do not place generated text, prices, song titles or logos inside system imagery.
- Do not use these backgrounds as decorative cards or repeat them as tiles.
- Do not let system imagery compete with user-generated artwork.
- Do not add Flutter assets or native scaffolding during this web/mobile pass.
