# Responsive and adaptive behavior

**Canonical order:** compact → medium → expanded. Layout changes are based on available space and content fit, not device detection.

## Layout bands

| Band | Reference range | Shell | Content behavior |
| --- | ---: | --- | --- |
| Compact | 320–599 CSS px | top bar + bottom navigation on destinations; focused Create may hide global nav | one reading column; safe-area action region; vertical option lists |
| Medium | 600–899 CSS px | bottom navigation by default | wider art; two-column grids only when labels remain readable; optional non-sticky context |
| Expanded | 900–1199 CSS px | 88px navigation rail | centered workspace; optional contextual panel; horizontal comparison where it improves decisions |
| Wide | ≥1200 CSS px | navigation rail | more art/context scale, not more controls; gallery density may increase |

The current CSS also contains fit breakpoints at 359, 390, 480, 700, 768, and 1100. Cursor may keep a local fit breakpoint when content proves it is required, but must not create device-specific product behavior.

## Global shell

- Compact destination screens use the bottom navigation. Focused Create screens use a compact back/close header and may hide the bottom navigation after draft persistence and exit behavior are implemented.
- Expanded screens use the same destinations in a rail. Owner remains an owner-only utility destination; Discover remains unapproved for the primary shell.
- The optional right context panel is permitted only for creation summary, Song DNA context, generation context, reveal metadata, or account help that materially supports the current task.
- Primary content remains readable at 200% zoom and 320 CSS px with no horizontal page scroll.

## Layout primitives

| Primitive | Compact | Medium | Expanded |
| --- | --- | --- | --- |
| `pageInset` | 16px, 20px from 390px | 24px | 32px |
| `decisionMeasure` | full available width | max 640px | max 720px |
| `readingMeasure` | full available width | max 576px | max 576px |
| `workspaceMax` | full width | max 960px | 1120px |
| `artStage` | edge-conscious inside safe inset | larger centered stage | dominant stage with optional 320–360px context |
| `stickyAction` | bottom safe area when the action would leave view | only when helpful | usually inline or in context panel |

## Input adaptation

- Minimum customer touch target is 44×44 CSS px; canonical control height is 48px and primary CTA height is 52px.
- Hover is an enhancement only. Every hover action is visible or discoverable on touch and keyboard.
- Enter/Space activates selectable cards. Escape closes dismissible sheets/dialogs and restores focus.
- When the mobile keyboard opens, the active field and primary continuation action remain reachable.
- Use logical properties (`margin-inline`, `padding-block`) for direction-aware layout.

## Screen adaptation rules

- **Create:** compact uses focused steps; expanded may keep a quiet sticky summary, but not a dashboard.
- **Song DNA:** compact starts with one recommended layer and progressive disclosure; expanded may show more dimensions for comparison without changing the selection contract.
- **Explore:** compact stacks three directions; expanded may use a three-column comparison if full names/descriptions remain legible.
- **Fine Tune:** compact uses a full-height sheet or route; expanded uses a constrained side panel. It stays optional in both.
- **Generation:** compact is immersive; expanded may show stable selected-DNA context beside the stage.
- **Reveal:** compact prioritizes the image, then actions; expanded places quiet actions/metadata beside a larger image.
- **Gallery:** portrait shelf remains first; creation grid increases density without mandatory masonry.
- **Account:** compact uses grouped vertical sections; expanded may add a section index, never a KPI dashboard.

## Required review widths

Every implementation slice captures and reviews 320, 390, 768, 900, and 1440 CSS px. Add 200% zoom and one landscape mobile check for screens containing sticky actions, sheets, or image stages.
