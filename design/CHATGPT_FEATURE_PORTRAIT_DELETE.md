# YouAreTheSongNow — Portrait Deletion Feature Request

**Priority:** High usability fix  
**Requested by:** ChatGPT on behalf of product owner  
**Date:** 2026-08-30

## Requirement

Users need to be able to **delete uploaded portraits**.

This is a functional addition, not just a visual change.

## Desired UX

Wherever uploaded portraits are shown or selected, provide a clear delete/remove control.

Preferred mobile behavior:

- Each uploaded portrait tile should have a small, obvious delete/remove affordance.
- Use a trash icon, `×`, or contextual menu depending on the existing component style.
- Touch target should be at least ~44×44 px even if the visible icon is smaller.
- The delete control should not be easy to trigger accidentally while selecting a portrait.
- If deletion is destructive and permanent, show a lightweight confirmation sheet/dialog such as: `Delete this portrait?` with `Cancel` and `Delete`.
- After deletion, remove the portrait from the visible collection immediately after the backend confirms success.

Desktop behavior should follow the same interaction model.

## Functional Requirements

1. Inspect the existing portrait upload/storage flow and determine where uploaded portrait records/files are stored.
2. Add the smallest clean backend/API action needed to delete an uploaded portrait belonging to the current authenticated user.
3. Verify authorization so a user can delete only their own portraits.
4. Delete both the database record and underlying stored file if that matches the existing storage architecture and is safe to do.
5. Handle missing/already-deleted files gracefully.
6. Do not affect generated images, historical creations, or unrelated uploads unless the existing data model explicitly requires it.
7. If a portrait currently in the active Create session is deleted, remove it from that current selection/state cleanly so the UI does not retain a broken reference.
8. Preserve all other existing portrait upload/selection behavior.

## Design Direction

Keep the new control consistent with the premium app design system already established.

Avoid making portrait tiles resemble social-profile cards. The delete action should feel like media/library management, similar to removing an asset from a creative workspace.

## Verification

Test at minimum:

- upload a portrait
- delete that portrait
- refresh the page and confirm it remains deleted
- verify its stored file/record behavior
- verify a selected portrait can be deleted without leaving broken UI state
- verify another user's portrait cannot be deleted
- test mobile touch interaction

## Handoff

After implementation, record the change in `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` under the current round or a small functionality-fix subsection.

Do not bundle unrelated feature work with this change.