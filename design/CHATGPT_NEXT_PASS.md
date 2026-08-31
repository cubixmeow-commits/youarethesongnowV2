# YouAreTheSongNow — Cursor Implementation Instructions

**Round:** 008

**Written by:** ChatGPT

**Date:** 2026-08-30

**Status:** Ready for Cursor

**Scope:** Current web/mobile application only

# Round 008 — Integrate the production YS identity and responsive art system

Production assets now exist in the repository. This pass replaces the CSS placeholders and provisional warm/groove imagery with the delivered YS identity and black/platinum/sapphire system, then refines the real phone and desktop compositions around those assets.

## Required reading

Read these files completely before editing:

1. `AGENTS.md`
2. required Current Project and Build 1 contracts named there
3. `design/BRAND_SYSTEM_YS.md`
4. `design/DESIGN_SYSTEM.md`
5. `design/PRODUCTION_ASSET_MANIFEST.md`
6. `design/RESPONSIVE_REDESIGN_PLAN.md`
7. `design/ASSET_INTEGRATION_MAP.md`
8. `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`

Inspect every delivered image at full resolution before integrating it.

## Hard boundaries

- Do not begin Flutter implementation or create Flutter/Dart files.
- Do not change APIs, routes, authentication, song lookup, portrait upload/delete, generation, credits, Stripe behavior, gallery data, sharing or business logic.
- Do not implement Upscale, Order print or Order T-shirt.
- Do not add fake commerce buttons or inactive controls that look functional.
- Do not modify the production image/SVG files unless a verified encoding problem blocks rendering.
- Preserve all approved product copy and no-em-dash rules.

## 1. Wire the delivered assets

Set the current web asset hooks to the exact files below:

```css
:root {
  --asset-atmosphere: url("/assets/images/system/app-atmosphere-haze-phone.webp");
  --asset-launch-mark: url("/assets/images/brand/ys-monogram-flat-platinum.svg");
  --asset-empty-collection: url("/assets/images/system/empty-collection-still.webp");
  --asset-session-phone: url("/assets/images/system/creative-session-backdrop-phone.webp");
  --asset-session-desktop: url("/assets/images/system/creative-session-backdrop-desktop.webp");
  --asset-paywall-preview-phone: url("/assets/images/system/paywall-world-preview-phone.webp");
  --asset-paywall-preview-desktop: url("/assets/images/system/paywall-world-preview-desktop.webp");
}

@media (min-width: 900px) {
  :root {
    --asset-atmosphere: url("/assets/images/system/app-atmosphere-haze-desktop.webp");
  }
}
```

Use real `<img>` markup for the top-bar flat monogram and wordmark when practical:

- mark: `public/assets/images/brand/ys-monogram-flat-platinum.svg`
- wordmark: `public/assets/images/brand/ys-wordmark.svg`

Keep an accessible brand name. Decorative duplicates must use empty alt text/hidden semantics.

Do not use `background-attachment: fixed` on phone/iOS. Keep the existing fallback gradients behind/beside the files until screenshot review proves the images alone are readable.

## 2. Shared phone shell

Target 390–430px first and verify 320px.

- top bar total height approximately 52–56px plus safe area;
- 30–32px flat YS mark;
- compact wordmark approximately 132–150px wide;
- 16px page inset at 320px, 20px at 390px and above;
- preserve current bottom navigation/routes and safe-area padding;
- 44px minimum touch target, prefer 48px;
- platinum text/hairlines, sapphire depth, cobalt only for current selection/focus/primary action;
- remove any remaining brass/amber brand chrome;
- do not put every section in a rounded card.

## 3. Shared desktop shell

- preserve the 88px navigation rail and its existing destinations;
- use the flat YS mark in rail/top brand moments;
- use the delivered desktop atmosphere anchored right/top;
- content working max width 1120px, artwork-led pages may reach 1280px;
- keep the same information architecture as phone;
- use negative space, alignment and platinum hairlines before new panels/shadows.

## 4. Home

### Phone

- First viewport is one poster: current artwork, compact YS identity, headline, one sentence, one primary action.
- Top bar plus the primary action should fit in the initial `100svh` where practical.
- Move Sign in out of the current interrupting middle strip; keep it available as a quiet shell/secondary action without changing its route.
- Keep examples as an artwork-first horizontal rail with a visible partial next item and keyboard/touch access.

### Desktop

- Use one full-bleed artwork plane after the rail, with copy no wider than 440px in the calm tonal region.
- Present examples as an open editorial artwork row, not feature cards.
- Do not add a SaaS feature grid, logo cloud, stats or dashboard preview.

## 5. Create

All existing fields and behavior remain intact.

### Phone

- Integrate `creative-session-backdrop-phone.webp` as one continuous page atmosphere under a strong readability veil.
- Keep Song, People and Direction as the persistent three-stage sequence.
- Song fields remain together with the lookup action immediately after them.
- People reads as a contact-sheet tray. Selected and delete states remain distinct and fully accessible.
- Direction options are tactile rows/tiles with restrained selected treatment.
- Move the overview after the active work rather than letting a tall card compete with an unfinished first stage.
- A sticky generation action is allowed only after the draft is ready and must respect keyboard/safe-area behavior.

### Desktop

- Use a 62/38 main/overview split.
- Main form column maximum approximately 640px.
- Sticky overview uses alignment and separators rather than heavy card chrome.
- Move the stage navigation into the main content header; do not leave it detached at the far-right page edge.
- Anchor `creative-session-backdrop-desktop.webp` right/top and keep the form in its calm dark field.

## 6. Gallery and reveal

### Gallery empty state

- Replace the CSS placeholder square with a real `<img>` using `empty-collection-still.webp`.
- Use empty alt text because the adjacent heading/copy explains the state.
- Maximum 240px on phone and 300px on desktop.
- Add one real primary link/action to the existing Create route if one is not already present.
- Do not add another frame/card around the supplied aperture artwork.

### Populated Gallery

- Keep artwork-first proportions and real orientation.
- Metadata stays sparse and outside the artwork.
- Touch actions remain available without hover; desktop focus/hover may reveal secondary actions.

### Reveal

- Phone: artwork owns the first viewport; actions follow in the existing functional order.
- Desktop: large artwork stage left, quiet metadata/actions right.
- Do not add commerce actions yet.
- Do not place the YS mark over user artwork.

## 7. Paywall presentation

Preserve every membership, Checkout, recovery and prepared-creation behavior.

### Phone

- Add `paywall-world-preview-phone.webp` as a square media header partially covered by the existing membership sheet/panel.
- Billing, renewal, credits, cancellation and legal copy remain live HTML on an opaque readable surface.

### Desktop

- Use `paywall-world-preview-desktop.webp` as the media side of a two-column composition.
- Keep all copy away from the bright horizon.
- This is one membership invitation, not a SaaS pricing-card grid.

## 8. Motion and accessibility

- Entry/cover reveal: 420ms opacity/blur/12px translate.
- Stage advance: 220ms directional translate/opacity.
- Press feedback: 140ms and no smaller than scale 0.97.
- No continuous background animation or mobile parallax.
- Honor reduced motion and increased contrast.
- Verify keyboard operation, focus visibility/return, announced errors/status, 16px mobile inputs and non-color selection cues.

## 9. Required responsive review

Verify at:

- 320 × 568
- 390 × 844
- 430 × 932
- 768 × 1024
- 900 × 900
- 1440 × 900
- 200% browser zoom

Create `design/review/round-008/` with:

- `home-mobile-390.png`
- `create-mobile-390-top.png`
- `create-mobile-390-people.png`
- `create-mobile-390-direction.png`
- `gallery-mobile-390.png`
- `paywall-mobile-390.png`
- `reveal-mobile-390.png` when a representative fixture exists
- `home-desktop-1440.png`
- `create-desktop-1440.png`
- `gallery-desktop-1440.png`
- `paywall-desktop-1440.png`
- `reveal-desktop-1440.png` when a representative fixture exists

Add a README with exact viewport, fixture/state, crop/overlay adjustments and any known issue. Include at least one 320px overflow check and one 200% zoom note.

## 10. Documentation and report

Update only where implementation evidence changes current truth:

- `design/DESIGN_SYSTEM.md`
- `design/ASSET_INTEGRATION_MAP.md`
- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
- `development-vault/01 Current Project/Dashboard Snapshot.md` and `docs/dashboard-data.js` if the dashboard needs the implemented status

Do not add Flutter implementation. `design/FLUTTER_DESIGN_HANDOFF.md` may receive a brief parity note only.

Run the full existing test suite. Inspect the real browser at phone and desktop sizes. Review the diff for functional scope creep.

Report in `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`:

1. exact files changed;
2. which production assets are integrated;
3. mobile and desktop composition decisions;
4. crop/overlay values;
5. accessibility/responsive checks;
6. exact test result;
7. screenshot paths;
8. remaining visual issues, maximum three;
9. confirmation that Flutter, commerce and application behavior were not changed.

Commit and push the completed Round 008 pass, then stop for ChatGPT review.
