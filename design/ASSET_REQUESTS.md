---
type: asset-requests
status: active
updated: 2026-08-30
area: visual-design
phase: round-006-platinum-blue
collaborator: ChatGPT image generation
---

# Asset Request Manifest — Round 006 (Platinum + Blue on Black)

Assets support a **premium private venue** on a **platinum + sapphire/cobalt on black** palette. Luxury automotive interior + gallery + Apple-grade dark interface. Not gaming RGB, neon fog, cyberpunk chrome, or casino gold.

User-generated artwork remains the dominant content. Integrate via replaceable paths under `public/assets/images/system/`.

---

## 1. app-atmosphere-haze

**Purpose**  
Global atmospheric field behind the app scaffold. Should feel like expensive space and light at night, not a concert stage.

**Where / why**  
`body.app` background layer across Create, Gallery, Account, Sign-in. Quiet depth without competing with generated images.

**Required dimensions**

| Variant | Pixels | Aspect |
| --- | --- | --- |
| phone | 1290 × 2796 | ~9:19.5 |
| desktop | 2400 × 1600 | 3:2 |

**Composition**  
Black/graphite atmospheric field. Deep sapphire shadow and restrained cobalt edge illumination. Cool platinum light grazing one edge. Extremely restrained grain. Calm center for UI. No literal room or focal subject.

**Style**  
Ultra-premium dark interior light, abstracted. Architectural, not neon.

**Colors**  
Near-black / graphite base. Accents: deep sapphire, restrained cobalt, cool platinum edge. **No amber, brass, or gold.**

**Background**  
Opaque dark, gradient-compatible. Not transparent.

**Constraints**  
No logos, lyrics, song titles, UI mockups, musical notes, waveforms, vinyl, instruments, faces, or readable symbols.

**Intended placement**  
`public/assets/images/system/app-atmosphere-haze-{phone|desktop}.webp` referenced from `app.css` as `--asset-atmosphere`.

**Export**  
WebP (quality ~80), sRGB. Optional PNG master.

**Flutter**  
Raster asset in `assets/images/system/`; `DecorationImage` on scaffold `backgroundColor` + image stack.

---

## 2. launch-mark-tile

**Purpose**  
App identity mark: entry, aperture, private portal, curated world. Not literal music symbolism.

**Where / why**  
Top bar brand mark; future Flutter launch icon exploration reference (not replacing store icons yet).

**Required dimensions**

| Variant | Pixels | Aspect |
| --- | --- | --- |
| mark | 1024 × 1024 | 1:1 |
| small UI | derived 128 / 256 | 1:1 |

**Composition**  
Black/graphite outer field. Platinum aperture rim. Sapphire core. Tiny cobalt luminosity at center if useful. Simple at 28–32px.

**Colors**  
Graphite/black field, platinum rim, sapphire core. **No gold/brass.**

**Background**  
Opaque (for app icon testing) plus optional transparent version for in-app chrome.

**Constraints**  
No letterforms, no music notes, no faces, no gradients that band badly at 29px.

**Intended placement**  
`public/assets/images/system/launch-mark-tile.png` (and `.webp`) beside brand wordmark in `.app-topbar`.

**Export**  
PNG master + WebP derivative.

**Flutter**  
Raster for in-app; separate store icon pipeline later.

---

## 3. creative-session-backdrop

**Purpose**  
Quiet textured backdrop for Create so the suite feels like a private viewing/listening room, abstracted behind UI.

**Where / why**  
`.create` section background (replacing or layering over current groove photos once delivered).

**Required dimensions**

| Variant | Pixels | Aspect |
| --- | --- | --- |
| phone | 1290 × 2200 | tall |
| desktop | 2200 × 1600 | landscape |

**Composition**  
Black lacquer / graphite stone. Soft sapphire edge shadow. Faint platinum upper-edge light. Almost monochrome center for form readability.

**Colors**  
Black graphite, sapphire shadow, platinum crown edge. **No brass.**

**Background**  
Opaque dark.

**Constraints**  
No text, logos, faces, instruments, waveform diagrams, mixer panels, or busy patterns that fight inputs.

**Intended placement**  
`public/assets/images/system/creative-session-backdrop-{phone|desktop}.webp` in `.create` background-image stack.

**Export**  
WebP.

**Flutter**  
Raster `BoxDecoration` image behind create flow; swap per breakpoint.

---

## 4. empty-collection-still

**Purpose**  
Premium empty state for Gallery when the user has no finished works yet. Private collection awaiting its first piece.

**Where / why**  
`.gallery-page` empty / zero-item presentation.

**Required dimensions**

| Variant | Pixels | Aspect |
| --- | --- | --- |
| main | 1200 × 1200 | 1:1 |
| @2x optional | 1800 × 1800 | 1:1 |

**Composition**  
Black gallery alcove. Platinum rim on dark square aperture. Subtle sapphire depth. One tiny cobalt point of light if necessary.

**Colors**  
Black frame, platinum rim, sapphire depth. **No warm sparks.**

**Background**  
Transparent preferred (PNG/WebP with alpha) so it sits on `--color-bg`.

**Constraints**  
No people, no text, no folder icons, no sad-state tropes, no music-note stickers.

**Intended placement**  
`public/assets/images/system/empty-collection-still.webp` inside Gallery empty copy block.

**Export**  
WebP with alpha if possible; else PNG.

**Flutter**  
Raster empty-state illustration widget above `Text`.

---

## 5. paywall-world-preview (optional, defer)

**Purpose**  
Emotional still beside membership panel so paywall feels like invitation into a world, not a billing funnel.

**Where / why**  
Create summary paywall panel (`[data-paywall]` presentation).

**Required dimensions**  
1600 × 1000 (16:10) and 1200 × 1200 (1:1 crop).

**Composition**  
Cinematic threshold into a luminous landscape. Silhouettes optional only (no recognizable faces). Soft left third darker for text overlay safe area.

**Style**  
Same family as launch hero photography: emotional, adventurous, hospitality not sales.

**Colors**  
Indigo dusk + sapphire horizon. **No brass horizon.**

**Background**  
Opaque.

**Constraints**  
No logos, no pricing text in image, no couples-as-dating-ad framing, no steamy/seductive styling.

**Intended placement**  
`public/assets/images/system/paywall-world-preview.webp`

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

Round 004 CSS ships with tokenized hooks so these can drop in without functional changes.
