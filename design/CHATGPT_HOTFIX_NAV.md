# YouAreTheSongNow — Mobile Navigation Hotfix

**Date:** 2026-08-30  
**Priority:** Immediate, before further visual refinement

## Observation from live mobile screenshot

The current mobile build visually has a bottom tab bar, but the product navigation is incomplete as a user-facing app structure.

Visible tabs are:

- Create
- Gallery
- Account
- Owner

There is no clear **Home / Discover / app launch** destination in the mobile primary navigation. This makes the user feel trapped inside Create and makes the new app-home direction inaccessible once they leave it.

Also, `Owner` is being presented as an equal primary consumer tab, which weakens the premium consumer-app feel.

## Required correction

Preserve all existing routes and permissions. This is a navigation presentation fix only unless an existing route must simply be surfaced.

### Mobile primary navigation

Use a consumer-facing bottom navigation hierarchy centered on the real product experience.

Preferred order:

1. **Home** — existing app/home/welcome route; icon should read as home/discover/launch, not marketing
2. **Create** — primary creation workflow
3. **Gallery** — collection of generated work
4. **Account** — profile/settings/account

Do **not** keep `Owner` as an equal permanent consumer tab.

If Owner must remain accessible for the private build, expose it through an existing account/menu/admin affordance or a visually secondary private/admin entry that does not consume one of the four primary consumer tab positions. Do not change owner authorization or route logic.

If there is no safe presentation-only way to relocate Owner without touching functional logic, keep its route intact but document the constraint and propose the smallest safe change. The consumer nav should still gain Home.

### Active-state behavior

Make sure the active tab correctly reflects:

- Home when on home/welcome
- Create when in the creation flow
- Gallery when in gallery/collection
- Account when in account

No page should appear to have "lost navigation" because its destination is absent from the tab bar.

### Mobile chrome

The compact top brand bar is good and can stay. Do not reintroduce a generic website hamburger solely to solve this. The bottom navigation should be the primary app navigation.

### Safe-area / browser overlap

Verify the bottom tab bar remains fully visible above iOS browser chrome and uses safe-area padding correctly. The supplied screenshot shows the tabs are visible, but this should be explicitly tested at common iPhone widths.

## Visual note from screenshot

The overall visual direction is improved: the typography, dark foundation, amber accent, and app-style bottom navigation feel more premium. However, the current screen still reads as a long form because the first viewport is dominated by text fields. Continue the Pass 2 creative-session treatment after this navigation correction.

## Cursor action

1. Read this file together with `design/CHATGPT_NEXT_PASS.md`.
2. Fix the mobile primary navigation first.
3. Preserve all backend/API/database/auth/generation behavior.
4. Then continue Pass 2.
5. Update `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` with the navigation correction and rationale.
6. Include mobile screenshots showing Home, Create, and Gallery tabs after the fix.
7. Commit/push and stop for ChatGPT review.
