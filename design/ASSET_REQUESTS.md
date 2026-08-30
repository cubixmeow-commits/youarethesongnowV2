---
type: asset-requests
status: active
updated: 2026-08-30
area: visual-design
phase: mobile-app-first-pass-1
collaborator: ChatGPT image generation
---

# Asset Request Manifest — Pass 1

Only assets that meaningfully raise the premium app feel. User-generated artwork remains the dominant content. Integrate via replaceable paths under `public/assets/images/system/`.

---

## 1. app-atmosphere-haze

**Purpose**  
Subtle full-bleed atmospheric texture behind the app scaffold so the base background feels like stage haze / listening-room air rather than a flat CSS fill.

**Where / why**  
`body` / `.app` background layer across Create, Gallery, Account, Sign-in. Provides quiet depth without competing with generated images.

**Required dimensions**

| Variant | Pixels | Aspect |
| --- | --- | --- |
| phone | 1290 × 2796 | ~9:19.5 |
| desktop | 2400 × 1600 | 3:2 |

**Composition**  
Abstract soft light only. Soft indigo–violet pool upper-right; warm amber spill lower-left; deep graphite center remaining calm and dark. Large quiet mid/lower region for UI readability (no busy focal subject). No people, no text, no icons, no album covers.

**Style**  
Cinematic concert haze photographed through soft glass. Premium, restrained, photographic grain optional and light.

**Colors**  
Midnight navy / graphite base. Accents: deep indigo, electric blue haze, warm amber. No neon rainbow, no pure red/black dating palette.

**Background**  
Opaque dark, gradient-compatible. Not transparent.

**Constraints**  
No logos, lyrics, song titles, UI mockups, musical-note clip art, vinyl records as objects, headphones, equalizer bars, faces, or readable symbols.

**Intended placement**  
`public/assets/images/system/app-atmosphere-haze-{phone|desktop}.webp` referenced from `app.css` as a fixed/cover background layer.

**Export**  
WebP (quality ~80), sRGB. Optional PNG master.

**Flutter**  
Raster asset in `assets/images/system/`; `DecorationImage` on scaffold. Consider later procedural gradient if performance prefers.

---

## 2. empty-collection-still

**Purpose**  
Premium empty state for Gallery when the user has no finished works yet.

**Where / why**  
`.gallery-page` empty / zero-item presentation. Communicates “your collection begins here” without looking like a SaaS blank slate.

**Required dimensions**

| Variant | Pixels | Aspect |
| --- | --- | --- |
| main | 1200 × 1200 | 1:1 |
| @2x optional | 1800 × 1800 | 1:1 |

**Composition**  
Centered abstract “unopened journey” still: a dark square frame suggesting an empty album slot or unlit stage portal. Soft rim light; tiny warm accent spark deep in the frame. Generous outer margin (safe ~12%) so it can sit above caption text.

**Style**  
Editorial / photo-book empty plate. Quiet, collectible, not cartoon.

**Colors**  
Graphite frame, indigo rim light, single amber spark. Mostly desaturated.

**Background**  
Transparent preferred (PNG/WebP with alpha) so it sits on `--color-bg`.

**Constraints**  
No people, no text, no folder icons, no sad-state tropes, no music-note stickers.

**Intended placement**  
`public/assets/images/system/empty-collection-still.webp` inside Gallery empty copy block (CSS or a single `<img>` when empty markup exists / is added later for presentation only).

**Export**  
WebP with alpha if possible; else PNG.

**Flutter**  
Raster empty-state illustration widget above `Text`.

---

## 3. creative-session-backdrop

**Purpose**  
Quiet textured backdrop for the Create session so the workspace feels like a studio / listening room rather than a form page.

**Where / why**  
`.create` section background (replacing or layering over current groove photos once delivered).

**Required dimensions**

| Variant | Pixels | Aspect |
| --- | --- | --- |
| phone | 1290 × 2200 | tall |
| desktop | 2200 × 1600 | landscape |

**Composition**  
Very dark matte surface with soft concentric listening-room light falloff from top center. Extremely subtle paper/micro-grain. Keep the center 60% low-contrast for form readability. No hard objects in the lower two-thirds.

**Style**  
Record-sleeve liner / dark studio wall. Tactile, matte, premium.

**Colors**  
Charcoal, soft indigo edge glow, faint amber crown light.

**Background**  
Opaque dark.

**Constraints**  
No text, logos, faces, instruments as literal props, waveform diagrams, or busy patterns that fight inputs.

**Intended placement**  
`public/assets/images/system/creative-session-backdrop-{phone|desktop}.webp` in `.create` background-image stack.

**Export**  
WebP.

**Flutter**  
Raster `BoxDecoration` image behind create flow; swap per breakpoint.

---

## 4. launch-mark-tile

**Purpose**  
App-like brand mark tile for chrome / future splash concepts (not a full splash screen yet).

**Where / why**  
Top bar brand mark on mobile/desktop; optional future Flutter launch icon exploration reference (not replacing store icons yet).

**Required dimensions**

| Variant | Pixels | Aspect |
| --- | --- | --- |
| mark | 1024 × 1024 | 1:1 |
| small UI | derived 128 / 256 | 1:1 |

**Composition**  
Abstract square emblem: soft luminous aperture / portal suggesting “entering a song,” not a literal eye. Center glow warm amber; outer field deep indigo. Flat-enough edges for small sizes. Safe margin 10% inside canvas.

**Style**  
Premium app icon family: simple, memorable, no skeuomorphic vinyl.

**Colors**  
Indigo field, amber core light, graphite rim.

**Background**  
Opaque (for app icon testing) plus optional transparent version for in-app chrome.

**Constraints**  
No letterforms, no “YATSN” monogram required, no music notes, no faces, no gradients that band badly at 29px.

**Intended placement**  
`public/assets/images/system/launch-mark-tile.png` (and `.webp`) beside brand wordmark in `.app-topbar`.

**Export**  
PNG master + WebP derivative.

**Flutter**  
Raster for in-app; separate store icon pipeline later.

---

## 5. paywall-world-preview (optional, defer if capacity limited)

**Purpose**  
Emotional still behind / beside the membership panel so the paywall feels like stepping into a world, not a billing form.

**Where / why**  
Create summary paywall panel (`[data-paywall]` presentation).

**Required dimensions**  
1600 × 1000 (16:10) and 1200 × 1200 (1:1 crop).

**Composition**  
Cinematic doorway / threshold into a luminous landscape, figures optional only as silhouettes (no recognizable faces). Soft left third darker for text overlay safe area.

**Style**  
Same family as existing launch hero photography: emotional, adventurous, non-dating.

**Colors**  
Indigo dusk + amber horizon.

**Background**  
Opaque.

**Constraints**  
No logos, no pricing text in image, no couples-as-dating-ad framing, no steamy/seductive styling.

**Intended placement**  
`public/assets/images/system/paywall-world-preview.webp` as CSS background or `<img>` beside paywall copy when integrated.

**Export**  
WebP.

**Flutter**  
Raster header in paywall sheet.

---

## Delivery checklist for ChatGPT

For each approved asset, provide:

1. Final file(s) at requested pixel sizes  
2. Filename matching this manifest  
3. Brief note if composition differs from spec  
4. Drop into vault or `public/assets/images/system/` for Cursor integration  

Pass 1 CSS ships with tokenized hooks so these can drop in without functional changes.
