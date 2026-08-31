# YouAreTheSongNow — YS Brand System

**Status:** selected direction; production asset set delivered 2026-08-30
**Phase:** current web/mobile site first; Flutter later
**Visual target:** premium private creative venue; black + platinum + sapphire/cobalt

## 1. Core identity

Use an intertwined **YS monogram** as the primary brand mark.

Character:
- platinum/silver geometry
- subtle sapphire-blue edge light
- deep black/graphite field
- elegant, sculptural, cinematic
- no music-note, vinyl, headphone, waveform, equalizer, or generic AI-sparkle symbolism
- should read clearly at 28–32px and still feel special at hero scale

The mark should feel like a premium object, not a glowing gaming emblem.

The delivered identity has two deliberate reproduction tiers:

- **premium rendered mark:** use at 96 CSS px and larger, or in launch/reveal moments where the satin-platinum material can be seen;
- **flat monogram:** use for navigation, print, embroidery, one-color merchandise, accessibility fallbacks and any placement where glow or bevel detail would collapse.

Do not redraw either tier in CSS. Do not attempt to make the flat mark look three-dimensional. Both tiers use the same intertwined Y/S idea, while the flat version removes fragile intersections and surface detail for reliable reproduction.

## 2. Wordmark

Primary wordmark:

**YouAreTheSongNow**

Use a refined high-contrast serif/display face already compatible with the project direction. Keep UI/body copy in the existing clean sans system.

Wordmark should be platinum/cool-white on black. Do not add gradients to the letters. Use the supplied SVG as the implementation asset; live text remains the accessible name.

## 3. Palette

Baseline:

- Noir black: `#0A0A0A`
- Graphite: `#1B1D21`
- Platinum: `#E6E7EA`
- Deep sapphire: `#0D1B3D`
- Cobalt accent: `#1E4CFF`

Use platinum for hierarchy, hairlines, labels and premium edge detail.
Use sapphire for depth, selected states, focus and atmospheric surfaces.
Use cobalt sparingly for high-value active moments and primary actions.

Avoid large flat cobalt areas, neon glow, chrome sci-fi, or gaming blue.

## 4. Surface language

The site should feel like a high-end private gallery / luxury creative environment rather than a SaaS dashboard.

Surfaces:
- near-black base
- graphite / lacquer panels
- cool platinum hairlines
- sapphire shadow depth
- very restrained blue edge lighting
- subtle grain
- generated artwork supplies most vivid color

## 5. Mobile identity

On mobile:
- compact top brand lockup with small YS mark + wordmark
- bottom navigation remains current structure
- primary content should feel like one premium app viewport, not a responsive website
- use safe-area aware spacing and 44–48px touch targets
- avoid oversized page headers when artwork can carry the screen

## 6. Desktop identity

On desktop:
- preserve compact left rail
- use larger negative space and curated artwork presentation
- desktop should feel like the same app expanded, not a separate marketing website
- YS mark may appear larger in rail/header moments but should not become decorative wallpaper

## 7. Home

Home should introduce the product through:
- small YS brand lockup
- one strong emotional generated-art hero
- concise statement
- one clear primary action
- curated visual examples

Avoid feature-grid/SaaS layouts.

## 8. Create

Create should feel like a premium creative workspace in the same black/platinum/blue environment.

Preserve all current functionality and fields.

Visual priorities:
- strong but quiet YS identity at top
- song section as a single composed source block
- portrait selection as a gallery/contact sheet
- direction choices as premium tactile options
- final generation action as a single high-value cobalt/platinum moment

## 9. Gallery / Reveal

Gallery should be the strongest expression of the private-gallery metaphor:
- large artwork
- sparse metadata
- platinum hierarchy
- blue only for selected/interactive moments

Reveal should feel cinematic and celebratory, not like a results card.

## 10. Merchandise readiness

Because poster/T-shirt ordering is planned next, the brand mark must have:

1. Full premium version: platinum + subtle blue light on dark
2. Flat one-color platinum version
3. Flat one-color white version
4. Flat black version for light merchandise
5. Small embroidery-safe version without fine glow/detail

Do not rely on glow to define the silhouette.

## 11. Production asset family

Delivered paths:

- `public/assets/images/brand/ys-monogram-premium.png`
- `public/assets/images/brand/ys-monogram-flat-platinum.svg`
- `public/assets/images/brand/ys-monogram-flat-white.svg`
- `public/assets/images/brand/ys-monogram-flat-black.svg`
- `public/assets/images/brand/ys-app-icon.png`
- `public/assets/images/brand/ys-app-icon.webp`
- `public/assets/images/brand/ys-wordmark.svg`
- `public/assets/images/system/app-atmosphere-haze-phone.webp`
- `public/assets/images/system/app-atmosphere-haze-desktop.webp`
- `public/assets/images/system/creative-session-backdrop-phone.webp`
- `public/assets/images/system/creative-session-backdrop-desktop.webp`
- `public/assets/images/system/empty-collection-still.webp`
- `public/assets/images/system/paywall-world-preview-phone.webp`
- `public/assets/images/system/paywall-world-preview-desktop.webp`

Exact dimensions, usage, source prompts and responsive behavior are recorded in `design/PRODUCTION_ASSET_MANIFEST.md`.

## 12. Important implementation rule

The generated branding boards are visual references, not pixel-perfect UI screenshots to copy literally.

Cursor should translate their qualities into real responsive components using the existing app architecture and functionality.

Do not begin Flutter implementation yet. Keep Flutter documentation updated only as a future handoff.
