# YouAreTheSongNow — ChatGPT Next Design Pass

**Round:** 007  
**Written by:** ChatGPT  
**Date:** 2026-08-30  
**Status:** Ready for Cursor

# Round 007 — Implement the selected YS identity into the current web/mobile app

The selected brand direction is the **YS monogram** documented in:

`design/BRAND_SYSTEM_YS.md`

This round is about translating that identity into the real responsive site/app shell so mobile and desktop begin to look like the chosen black / platinum / sapphire reference system.

## Critical product constraint

**Do not begin Flutter implementation.**

The current PHP/web app remains the product we are actively building. Flutter documentation may be kept synchronized, but native implementation is deferred until the existing product experience, generation workflow, commerce, credits/subscription behavior, ordering flows, and visual system are much further along.

## 1. Treat the selected visual board as a direction, not a literal screenshot

Use the YS brand board qualities:

- near-black foundation
- graphite/lacquer depth
- platinum typography/hairlines
- sapphire shadows
- cobalt only for focused active/primary moments
- large cinematic generated imagery
- sparse premium composition
- elegant YS identity

Do not copy fake UI labels or navigation from the generated concept image.
Do not replace working product navigation/functionality with mockup UI.

## 2. Build a real responsive brand shell

### Mobile

Target first: 390–430px.

- top bar should use a compact YS mark area + `YouAreTheSongNow` wordmark treatment
- preserve current bottom navigation and routes
- make the page feel edge-to-edge and native-app-like
- use premium dark surfaces and restrained separators
- maintain safe-area handling
- minimum interactive hit area 44px; prefer 48px where practical
- reduce webpage-like container framing

### Desktop

- preserve compact ~88px rail
- apply YS identity to rail/top branding
- let artwork and content breathe
- use platinum hairlines and sapphire depth instead of generic cards
- desktop is an expanded version of mobile, not a separate corporate site

## 3. Home

Refine the real Home screen around the chosen brand language.

Priorities:

- compact YS identity
- cinematic art as the dominant element
- concise message and one strong action
- premium gallery-like spacing
- examples should resemble collected works rather than marketing feature cards

Do not reintroduce dating/couple-first brand identity.
Do not create a SaaS feature grid.

## 4. Create

Keep all existing fields, upload, song lookup, portrait deletion, selection, generation, and state behavior.

Translate the screen visually:

- YS identity should be present but subtle
- song section = one composed source area
- portraits = curated gallery/contact-sheet selection
- delete remains secondary and safe
- direction controls = tactile premium selectors
- cobalt reserved for selected/focus/final action
- final Generate action should feel like the visual culmination

Avoid large glowing neon controls.

## 5. Gallery + Reveal

Gallery should become a strong expression of the brand:

- larger artwork priority
- cool platinum hierarchy
- very sparse metadata
- sapphire/cobalt interaction only
- empty state uses current asset hook until custom art is delivered

Reveal should be audited for the same visual system. Do not make it a generic bordered result card.

## 6. Brand mark implementation strategy

We do not yet have final production vector geometry for the YS monogram.

For this round:

- create a clean replaceable brand component / hook in markup/CSS
- do NOT attempt to trace a complicated faux-3D logo into fragile CSS
- if a temporary simplified `YS` typographic/geometry mark is needed, clearly label it temporary
- use paths/naming from `design/BRAND_SYSTEM_YS.md`
- make replacing the placeholder with final SVG/PNG trivial

The final monogram will eventually need flat merchandise-safe versions as well as a premium rendered version.

## 7. Commerce readiness — prepare, do not implement ordering yet

The next product phase will include image upscaling and ordering posters / T-shirts.

During this design pass, ensure generated-art/gallery/reveal layouts have sensible places for future secondary actions such as:

- Upscale
- Order print
- Order T-shirt

But **do not invent or implement commerce/backend behavior in Round 007** unless it already exists.
Do not add fake checkout buttons that appear functional.

Instead document recommended insertion points in:

`design/COMMERCE_UI_INSERTION_MAP.md`

Create that file with:

- Reveal insertion point
- Gallery item action entry point
- mobile action-sheet recommendation
- desktop action placement
- poster/T-shirt preview entry point
- where upscale status/progress could appear
- accessibility/touch requirements
- what backend contracts will eventually be needed (high-level only)

This will prepare us for the next functional phase without mixing it into brand implementation.

## 8. Update design docs

Update where needed:

- `design/DESIGN_SYSTEM.md`
- `design/ASSET_REQUESTS.md`
- `design/ASSET_INTEGRATION_MAP.md`
- `design/FLUTTER_DESIGN_HANDOFF.md` only to record the current web system for future use; do not scaffold Flutter
- canonical handoff

Explicitly state that `design/BRAND_SYSTEM_YS.md` is the selected identity direction.

## 9. Review pack

Create:

`design/review/round-007/`

Preferred screenshots:

- `home-mobile-390.png`
- `create-mobile-390-top.png`
- `create-mobile-390-people.png`
- `create-mobile-390-direction.png`
- `gallery-mobile-390.png`
- `reveal-mobile-390.png` if a representative state can be captured
- `home-desktop-1440.png`
- `create-desktop-1440.png`
- `gallery-desktop-1440.png`

Add README with:

- branch/commit
- exact viewports
- visual changes
- placeholder vs production brand assets
- remaining inconsistencies
- what ChatGPT should judge

## 10. Functionality / tests

Preserve:

- auth
- routes
- APIs
- song lookup
- portraits/upload/delete
- generation logic
- payment/subscription behavior
- current navigation

Run existing test suite and report results.

## Report back

Update `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` with:

1. Round 007 implementation summary
2. exact files changed
3. mobile/desktop brand-shell decisions
4. how final YS assets can drop in
5. commerce insertion map summary
6. tests/results
7. screenshot references
8. no more than 3 high-value questions
9. confirmation that Flutter implementation was NOT started

Commit/push and stop for ChatGPT review. Do not begin the upscaling/poster/T-shirt functional build yet.
