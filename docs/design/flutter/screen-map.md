# Flutter screen map

**Boundary:** design/API mapping, not Flutter implementation authorization.

| Product state | Web implementation target | Flutter route/composition | Shared backend contract |
| --- | --- | --- | --- |
| Create destination | `/create` destination state | adaptive scaffold tab/rail destination | drafts, portraits, recent images |
| Choose song | focused Create state | pushed/focused route | `POST /song-lookups`, lookup status |
| Song DNA | focused Create state | pushed/focused route | future customer-safe projection + draft selection |
| Quick Generate | ready state within Create | state/confirmation surface | summary/quote, membership, job submit |
| Explore | inline/focused direction state | route or sheet based on space | `POST /explore-directions` |
| Fine Tune | compact sheet/expanded panel | bottom sheet/side panel | current draft option fields + quote |
| Generation | immersive route/state | full-screen route | generation job submit/status |
| Reveal | `/images/{id}` | detail/reveal route | image, content, download, share, regeneration |
| Gallery | `/gallery` | tab/rail destination with slivers | portraits, images, private media |
| Account | `/account` | tab/rail destination, sectioned settings | me, membership, credits, security, billing, deletion |

## Navigation rules

- Top-level order is Create, Gallery, Account. Owner remains web-first/admin. Discover is not added until product approval.
- Focused Create routes may sit above the destination shell so bottom navigation disappears without losing the draft.
- Back from Song DNA returns to song confirmation; back from Explore returns to DNA with selection preserved; back from Fine Tune returns to the calling Quick/Explore state; leaving Generation does not cancel the job.
- Completion deep-links to Reveal and Gallery refreshes on return.

## Adaptive rules

- Compact uses one task per route/sheet and safe-area primary actions.
- Medium may widen art and show two-column choices where readable.
- Expanded keeps identical state/labels while showing rail, main workspace, and an optional 320–360px context panel.
- Layout switching must preserve draft, scroll position when reasonable, selected DNA/direction, form text, and job polling.

## Mobile API gate before Flutter UI

Before implementation, verify every required endpoint has opaque mobile-session support, consistent validation/error shapes, authorization on every resource, idempotency for paid/job actions, and no web-only cookie/DOM assumption. Record any gap as backend work; do not duplicate the rule in Dart.
