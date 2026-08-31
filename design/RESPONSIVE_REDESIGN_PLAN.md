---
type: responsive-redesign-plan
status: ready-for-implementation
updated: 2026-08-30
area: current-web-mobile
identity: YS
---

# YS Responsive Redesign Plan

## Scope

This plan applies to the current PHP/HTML/CSS/JavaScript web application at phone and desktop widths. It does not authorize Flutter implementation, new routes, commerce behavior, API changes or business-logic changes.

## Three design theses

**Visual thesis:** A platinum threshold in a silent black gallery, lit by deep sapphire; generated artwork remains the emotional center.

**Content plan:** Brand and promise → creation workspace → private collection → cinematic reveal → membership invitation only when required.

**Interaction thesis:** A quiet cover reveal on entry, a clear track-advance transition between Song/People/Direction, and restrained state/focus motion that never becomes ornamental.

## Shared shell

### Phone: 320–599 CSS px

- 52–56px safe-area-aware top bar with a 30–32px flat YS mark and compact wordmark.
- Single natural reading column; 16px edge inset at 320px and 20px from 390px upward.
- Bottom navigation remains fixed and uses the existing routes. Content receives bottom padding for bar plus safe area.
- Background uses `app-atmosphere-haze-phone.webp`; Create replaces it with the dedicated session backdrop inside the page region.
- Keep filled cobalt to one primary action per decision view. Selected states use sapphire border/fill plus a non-color state cue.
- Minimum 44×44px touch target; target 48px for primary controls.

### Tablet: 600–899 CSS px

- Preserve mobile navigation and reading order.
- Increase content max width to 720px.
- Allow two-column option grids only where each option remains at least 160px wide.
- Do not introduce the desktop rail before 900px.

### Desktop: 900 CSS px and above

- Preserve the compact 88px navigation rail.
- Top brand strip begins after the rail; flat YS mark plus wordmark remain modest.
- Use `app-atmosphere-haze-desktop.webp`, anchored right/top.
- Working content max width: 1120px; artwork-led pages may expand to 1280px.
- Desktop is the expanded app. Do not add marketing navigation, dashboard cards or a second visual language.

## Home

### Phone

- First viewport is one poster: artwork, compact YS lockup, headline, one short sentence and one primary action.
- Fit the top bar plus hero action in the initial 100svh where possible.
- Examples become a horizontal artwork rail with intentional partial-next-item disclosure and accessible controls.
- Remove the current standalone sign-in strip from the middle of the poster. Sign in belongs in the shell or a quiet secondary action after the hero.

### Desktop

- Full-bleed artwork begins below/behind the quiet chrome and spans the available canvas after the rail.
- Copy column is no wider than 440px and sits over the calm tonal field.
- Examples become an open editorial row, not three cards.
- One final quiet invitation closes the page; no feature grid or pricing table on Home.

## Create

### Phone

- Header: small YS mark, `Create`, current stage title and one-line orientation copy.
- Persistent compact progress cue: `01 Song`, `02 People`, `03 Direction`. It scrolls with content until it reaches the top bar, then may become sticky if it does not obscure fields.
- Song fields remain visible together. The search action follows immediately.
- People becomes an artwork/contact-sheet tray with clear selected state, separate delete affordance and no card around the entire section.
- Direction options use tactile rows/tiles with strong labels and quiet supporting copy.
- The overview stays inline after the active stage; do not make it a long competing card above unfinished work.
- The final generation action may become a safe-area-aware sticky action only after the draft is ready.

### Desktop

- 62/38 split: main stage on the left, sticky overview/action on the right.
- The main form column remains 640px or less for readable scanning.
- Session backdrop is one continuous background, right-anchored, under a strong readability veil.
- Stage navigation sits at the top of the main column rather than in the far-right page edge.
- Overview uses separators and alignment before box chrome. It should feel like a concise liner-note sheet.

## Gallery

### Phone

- Heading and supporting copy use the same top rhythm as Create.
- Empty state uses `empty-collection-still.webp` at no more than 240px, followed by one primary action to Create.
- Populated gallery is artwork-first. Metadata follows each work without a heavy card background.
- Touch actions open a bottom sheet; no hover-only actions.

### Desktop

- Two to four artwork columns based on minimum tile width and real aspect ratios.
- Maintain generous exhibition gaps and avoid forced uniform crops.
- Empty state centers in the content canvas at no more than 300px art width.
- Item actions appear on focus/hover and remain available through the detail page.

## Reveal

### Phone

- Artwork owns the first viewport below the top bar.
- Metadata and actions follow in a strict order: Download, Share, Create another, Delete.
- Future Upscale/Print/T-shirt entry points remain absent until their backend phase is authorized.

### Desktop

- Large artwork stage on the left; quiet metadata/action column on the right.
- No generic result card, celebration confetti or repeated frames.
- Premium monogram may appear once at the completed-state transition, never over the user artwork.

## Paywall

### Phone

- Use `paywall-world-preview-phone.webp` as a 1:1 header that is partially covered by the membership sheet.
- Price, renewal, credits, cancellation and legal copy stay live and readable in the sheet.
- One cobalt primary action. Dismissal/cancel behavior preserves the prepared creation.

### Desktop

- Two-column composition: `paywall-world-preview-desktop.webp` media side and membership content side.
- Keep the bright horizon away from overlaid copy.
- Do not make the plan look like one option in a SaaS pricing grid.

## Motion

- Entry reveal: opacity 0→1, blur 6px→0 and translateY 12px→0 over 420ms.
- Stage advance: 220ms translate/opacity with direction reflecting forward/back movement.
- Press feedback: 140ms, scale no smaller than 0.97.
- Do not animate the background images continuously.
- Under `prefers-reduced-motion: reduce`, remove blur/translate and use immediate state changes or a short opacity change.

## Accessibility and verification

- Keyboard-complete journey with visible focus and logical focus return.
- Focus, selection, validation and disabled states never depend on cobalt alone.
- Mobile inputs remain at least 16px.
- Informative user artwork has useful alt text; decorative system imagery uses empty alt text or CSS backgrounds.
- Verify at 320×568, 390×844, 430×932, 768×1024, 900×900, 1440×900 and 200% zoom.
- Verify real light and dark user artwork against all overlays.
- Honor increased contrast and reduced motion.

## Implementation boundary

Allowed: markup presentation, CSS/tokens, responsive layout, decorative asset wiring, non-functional view transitions, accessible labels/states and screenshot fixtures.

Not allowed in this pass: Flutter files, APIs, routes, auth, credits, Stripe behavior, song lookup logic, generation logic, portrait storage/deletion behavior, gallery data behavior, sharing behavior, upscaling, print or T-shirt ordering.
