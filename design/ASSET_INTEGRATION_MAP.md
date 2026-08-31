---
type: asset-integration
status: production-assets-delivered
updated: 2026-08-30
area: visual-design
phase: round-006-platinum-blue
---

# Production Asset Integration Map

Drop-in guide for delivered YS brand and system assets. Specs and prompts live in `design/PRODUCTION_ASSET_MANIFEST.md`. This map documents **where** each asset connects in the current web/mobile application. Flutter implementation remains deferred.

System assets directory (create when delivering):

`public/assets/images/system/`

---

## 1. app-atmosphere-haze

| Field | Value |
| --- | --- |
| **Purpose** | Global luxury-interior atmospheric field behind app scaffold |
| **CSS variable** | `--asset-atmosphere` |
| **Opacity control** | `--asset-atmosphere-opacity` (default `1`) |
| **Phone file** | `public/assets/images/system/app-atmosphere-haze-phone.webp` |
| **Desktop file** | `public/assets/images/system/app-atmosphere-haze-desktop.webp` |
| **Selector / hook** | `body.app` `background-image` stack (first layer) |
| **Mobile crop** | `background-size: cover`; `background-position: center top`; scrolls normally (do not use fixed attachment on iOS) |
| **Desktop crop** | Same; desktop variant used at `min-width: 900px` via media query override in `app.css` |
| **Blend / overlay** | Sits beneath existing radial gradients and grain (`body.app::before`). No multiply blend. Gradients remain for readability until asset is validated alone. |
| **Fallback when absent** | `--asset-atmosphere: none` — black/graphite radial gradients + sapphire atmospheric glow + subtle SVG grain |
| **Flutter equivalent** | `DecorationImage` on `Scaffold` `backgroundColor` + `BoxDecoration` stack; separate phone/tablet/desktop assets via `LayoutBuilder` breakpoints |

### Integration snippet (web)

```css
:root {
  --asset-atmosphere: url("/assets/images/system/app-atmosphere-haze-phone.webp");
}
@media (min-width: 900px) {
  :root {
    --asset-atmosphere: url("/assets/images/system/app-atmosphere-haze-desktop.webp");
  }
}
```

---

## 2. YS brand mark and wordmark

| Field | Value |
| --- | --- |
| **Purpose** | Selected YS identity in top bar/rail; separate app-icon tile |
| **CSS variable** | `--asset-launch-mark` |
| **Files** | `public/assets/images/brand/ys-monogram-flat-platinum.svg`, `ys-wordmark.svg`, `ys-app-icon.webp` |
| **Selector / hook** | `.brand__mark` in `templates/layouts/main.php` |
| **Mobile crop** | Flat SVG at 30–32 CSS px; wordmark approximately 132–150 CSS px wide |
| **Desktop crop** | Flat SVG at 30–36 CSS px; wordmark approximately 150–190 CSS px wide |
| **Blend / overlay** | No blend. Preserve transparent SVG edges and keep the mark platinum. |
| **Fallback when absent** | CSS gradient mark: sapphire core + platinum/graphite rim |
| **Flutter equivalent** | Documentation only for now; later use the flat SVG in the native asset bundle after web approval |

### Integration snippet (web)

```css
:root {
  --asset-launch-mark: url("/assets/images/brand/ys-monogram-flat-platinum.svg");
}
```

Prefer real `<img>` elements for the mark and wordmark so intrinsic dimensions, alternate text behavior and responsive sizing are explicit. Keep `--asset-launch-mark` only as a compatibility hook during the transition.

---

## 3. creative-session-backdrop

| Field | Value |
| --- | --- |
| **Purpose** | Quiet backdrop behind Create flow |
| **CSS variables** | `--asset-session-phone`, `--asset-session-desktop`, `--asset-session-overlay` |
| **Phone file** | `public/assets/images/system/creative-session-backdrop-phone.webp` |
| **Desktop file** | `public/assets/images/system/creative-session-backdrop-desktop.webp` |
| **Selector / hook** | `.create` `background-image` (second layer after overlay gradient) |
| **Mobile crop** | `background-size: cover`; `background-position: center top` |
| **Desktop crop** | Same; switches at `min-width: 900px` |
| **Blend / overlay** | `--asset-session-overlay` gradient (default `color-mix(in oklab, var(--color-bg) 92%, transparent)`) sits above image for form readability. Tune overlay opacity after asset delivery. |
| **Fallback when absent** | Current launch groove photos (`layout-mobile-480.webp`, `layout-groove-1254.webp`) |
| **Flutter equivalent** | `BoxDecoration` image on Create scaffold; overlay `Container` with semi-transparent `Color` |

### Integration snippet (web)

```css
:root {
  --asset-session-phone: url("/assets/images/system/creative-session-backdrop-phone.webp");
  --asset-session-desktop: url("/assets/images/system/creative-session-backdrop-desktop.webp");
}
```

---

## 4. empty-collection-still

| Field | Value |
| --- | --- |
| **Purpose** | Gallery zero-state illustration |
| **CSS variable** | `--asset-empty-collection` |
| **File** | `public/assets/images/system/empty-collection-still.webp` |
| **Selector / hook** | `.gallery-empty__art` in `templates/pages/gallery.php` |
| **Mobile crop** | `width: min(72vw, 240px)`; `aspect-ratio: 1`; `background-size: contain` |
| **Desktop crop** | Same; centered in column |
| **Blend / overlay** | Transparent PNG/WebP preferred; CSS gradient fallback behind when variable is `none` |
| **Fallback when absent** | Sapphire depth spark on graphite gradient (current behavior) |
| **Visibility** | Shown when `.gallery-grid:empty` (no JS change required) |
| **Flutter equivalent** | `Image.asset` in `GalleryEmptyState` widget above title/copy |

### Integration snippet (web)

```css
:root {
  --asset-empty-collection: url("/assets/images/system/empty-collection-still.webp");
}
```

---

## 5. paywall-world-preview

| Field | Value |
| --- | --- |
| **CSS hooks** | `--asset-paywall-preview-phone`, `--asset-paywall-preview-desktop` |
| **Phone file** | `public/assets/images/system/paywall-world-preview-phone.webp` |
| **Desktop file** | `public/assets/images/system/paywall-world-preview-desktop.webp` |
| **Selector / hook** | `.paywall-panel` media region or a new presentational child inside the existing paywall container |
| **Mobile crop** | 1:1 header; `object-position: center`; membership sheet may overlap lower edge |
| **Desktop crop** | 16:10 media side in a two-column paywall composition; `object-position: center` |
| **Overlay rule** | Billing, renewal and legal copy remain live HTML on an opaque surface; never bake text into the image |
| **Flutter equivalent** | Documentation only for now; later header/media asset in the native paywall sheet |

### Integration snippet (web)

```css
:root {
  --asset-paywall-preview-phone: url("/assets/images/system/paywall-world-preview-phone.webp");
  --asset-paywall-preview-desktop: url("/assets/images/system/paywall-world-preview-desktop.webp");
}
```

---

## Delivery checklist

1. Place files at paths above (WebP preferred).
2. Update `:root` variables in `public/assets/css/app.css` (or a dedicated `assets.css` import if preferred later).
3. Capture new screenshots in `design/review/round-XXX/`.
4. Note any crop/overlay adjustments in `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`.
5. Do not copy assets into Flutter or scaffold native UI during this pass.

## Do not

- Commit placeholder fake artwork.
- Block layout on assets — all hooks have CSS fallbacks today.
- Change functional behavior when integrating assets.
