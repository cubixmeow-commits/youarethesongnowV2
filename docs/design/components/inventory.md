# Component inventory (repo-aware)

## Shell / chrome

| Pattern | Classes / location | Notes |
| --- | --- | --- |
| App shell | `layouts/main.php`, `body.app` | Skip link, topbar, main, nav, legal |
| Brand lockup | `.brand`, `.brand__mark`, `.brand__wordmark` | Decorative images + visually-hidden name |
| Top bar | `.app-topbar` | Sticky, safe-area aware |
| Primary nav | `.app-nav`, `.app-nav__item` | Bottom tabs ↔ left rail at 900px |
| Legal footer | `.app-legal` | Terms / Privacy |

## Controls

| Pattern | Classes | Used on |
| --- | --- | --- |
| Button | `.btn`, `--primary/--secondary/--ghost/--danger/--retrieve/--generate` | Most pages |
| Icon button | `.icon-btn` | Home carousel, showcase dialog |
| Action row | `.action-row` | Image detail |
| Form stack | `.stack`, nested `<label><span>` | Auth, create, account |
| File input | `.file` | Portraits |
| Checkbox | `.check` | Activate, create options |
| Choice row | `.choice-row` | Quality / orientation |
| Status line | `.status`, `.is-error` | Cross-cutting via `setStatus()` |
| Confirm sheet | `.confirm-sheet` + `<dialog>` | Portrait delete |
| Filter chip | `.showcase-filter` | Showcase orientation |

## Create-specific

| Pattern | Classes | Built by |
| --- | --- | --- |
| Session header | `.session-header*` | Template |
| Session progress | `.session-progress__step` | Template + JS classes |
| Movement section | `.movement` | Template |
| Portrait chip | `.portrait-chip` | `app.js` |
| Style tile | `.style-option` | `app.js` |
| Summary board | `.session-board`, `.summary-list` | Template + JS |
| Paywall panel | `.paywall-panel` | Template |
| Venue progress | `.venue-progress` | Template / JS |

## Media / collection

| Pattern | Classes | Notes |
| --- | --- | --- |
| Hero launchpad | `.hero--launchpad` | Home |
| World carousel | `.world-carousel*` | Home + `showcase.js` |
| Gallery tile | `.gallery-item` | JS |
| Gallery empty | `.gallery-empty` | Template (a11y gap) |
| Reveal figure | `.reveal` | Image detail |
| Showcase masonry | `.showcase-tile`, Masonry.js | Web-specific |
| Showcase lightbox | `.showcase-dialog` | `<dialog>` |

## Owner

| Pattern | Notes |
| --- | --- |
| Owner tables | JS `innerHTML` tables |
| Style toggle | `.owner-style-toggle` |

## Duplication / inconsistency flags

1. **No shared PHP partials** for button, field, status, empty state — copy-paste markup.
2. **Two confirm patterns:** native `<dialog>` (portrait delete, showcase) vs `window.confirm` (image delete).
3. **Empty state** formalized only on Gallery; portraits/owner/showcase filters lack dedicated empty UI.
4. **Button loading/disabled** rarely applied during in-flight requests (owner style toggle is an exception).
5. **CSS mask nav icons** vs future Flutter vector set — not unified.
6. **Legacy button modifiers** `--retrieve` / `--generate` sit beside the primary/secondary system.

## Canonical candidates (Phase 3 — after review)

Prioritized for shared extraction (web partials first; Flutter widgets later):

1. `AppChrome` (topbar + nav + legal)
2. `Button` (with loading/disabled)
3. `StatusBanner`
4. `FormField`
5. `EmptyState`
6. `ConfirmSheet` (replace `confirm()`)
7. `SessionProgress` + `MovementSection`
8. `PortraitChip` / DNA dimension cards / direction cards / `ChoiceRow` (StyleTile demoted over time per create-flow.md)
9. `SummaryBoard` / `PaywallPanel`
10. `GalleryTile` / `RevealFigure`
11. `FilterChipGroup` / `MediaLightbox`
12. `WorldCarousel` / `MasonryArchive` (marketing; may stay web-only)
