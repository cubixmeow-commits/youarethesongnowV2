---
type: asset-integration
status: active
updated: 2026-08-30
area: visual-design
phase: round-005
---

# Asset Integration Map

Drop-in guide for ChatGPT-generated system assets. Specs live in `design/ASSET_REQUESTS.md`. This map documents **where** each asset connects in the web prototype and how it should map to Flutter.

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
| **Mobile crop** | `background-size: cover`; `background-position: center top`; `background-attachment: fixed` |
| **Desktop crop** | Same; desktop variant used at `min-width: 900px` via media query override in `app.css` |
| **Blend / overlay** | Sits beneath existing radial gradients and grain (`body.app::before`). No multiply blend. Gradients remain for readability until asset is validated alone. |
| **Fallback when absent** | `--asset-atmosphere: none` — radial graphite/indigo/brass gradients + subtle SVG grain only |
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

## 2. launch-mark-tile

| Field | Value |
| --- | --- |
| **Purpose** | App mark in top bar; future store icon reference |
| **CSS variable** | `--asset-launch-mark` |
| **File** | `public/assets/images/system/launch-mark-tile.webp` (+ optional `.png` master) |
| **Selector / hook** | `.brand__mark` in `templates/layouts/main.php` |
| **Mobile crop** | 28×28 CSS px display; source 1024×1024 with 10% safe margin |
| **Desktop crop** | Same mark; no separate desktop asset required |
| **Blend / overlay** | `background-size: cover`; layered above CSS gradient fallback when variable is `none` |
| **Fallback when absent** | Brass/indigo CSS radial gradient emblem (current behavior) |
| **Flutter equivalent** | `Image.asset('assets/images/system/launch_mark_tile.webp', width: 28, height: 28)` beside wordmark in `VenueTopBar` |

### Integration snippet (web)

```css
:root {
  --asset-launch-mark: url("/assets/images/system/launch-mark-tile.webp");
}
```

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
| **Fallback when absent** | Radial brass spark on stone/lacquer gradient (current behavior) |
| **Visibility** | Shown when `.gallery-grid:empty` (no JS change required) |
| **Flutter equivalent** | `Image.asset` in `GalleryEmptyState` widget above title/copy |

### Integration snippet (web)

```css
:root {
  --asset-empty-collection: url("/assets/images/system/empty-collection-still.webp");
}
```

---

## 5. paywall-world-preview (deferred)

| Field | Value |
| --- | --- |
| **CSS hook** | Not wired in Round 005 — add to `.paywall-panel` when asset arrives |
| **Suggested variable** | `--asset-paywall-preview` |
| **File** | `public/assets/images/system/paywall-world-preview.webp` |
| **Flutter equivalent** | Header image in paywall sheet |

---

## Delivery checklist

1. Place files at paths above (WebP preferred).
2. Update `:root` variables in `public/assets/css/app.css` (or a dedicated `assets.css` import if preferred later).
3. Capture new screenshots in `design/review/round-XXX/`.
4. Note any crop/overlay adjustments in `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`.
5. Mirror filenames under Flutter `assets/images/system/` per `design/FLUTTER_DESIGN_HANDOFF.md`.

## Do not

- Commit placeholder fake artwork.
- Block layout on assets — all hooks have CSS fallbacks today.
- Change functional behavior when integrating assets.
