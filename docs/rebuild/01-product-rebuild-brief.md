# 01 — Product Rebuild Brief

## Product direction

Rebuild **YouAreTheSongNow / AISaga Arcana** as a first-class web and mobile experience rather than a desktop-style PHP site squeezed onto a phone.

The legacy concept, as currently verified, turns song/band context into narrative and cinematic visual experiences. V2 should preserve the emotional core while simplifying the workflow and making generation state, saved projects, media, and purchases reliable across devices.

## Product principles — Proposed

- **Song first, complexity later.** The first useful creation should happen quickly.
- **A project is durable.** A creation should survive closing the app and be accessible on web/mobile.
- **Generation is visible state.** Users should know whether work is queued, generating, complete, or failed.
- **The user owns their workspace.** Uploads, generations, history and deletion need clear ownership semantics.
- **AI is a capability, not the architecture.** Providers can change without redesigning the app.
- **Credits are understandable.** Show cost before generation and record every debit/refund/reversal.

## Proposed core journey

1. Sign in or start onboarding.
2. Create a project around a song/artist or permitted source material.
3. Supply/select context and optional user media.
4. Build the Arcana narrative/creative interpretation.
5. Generate one or more visual results.
6. Save results into the project/gallery automatically.
7. Regenerate/refine where supported.
8. Share/export a finished result.
9. Purchase/manage credits when required.

This journey is provisional until compared against V1 behavior and the owners' new product vision.

## Questions to answer during discovery

- What does “Arcana” mean in V2: single image, sequence, story, card/deck, or multiple modes?
- How much song context is typed by the user versus looked up from metadata providers?
- Are full lyrics ever stored or transmitted? If so, under what rights/terms?
- Is a user's own photograph central to the experience, optional, or a separate mode?
- What parts of Dynamic Band Lore Engine are essential to the product identity?
- Are credits the long-term business model, a subscription component, or both?
- Which V1 accounts/projects/media must be migrated?
- What sharing/privacy defaults should projects use?
- What moderation and provider-safety behavior should be user-visible?

## First product milestone — Proposed

A thin, polished vertical slice: create a project, submit allowed song/context input, run one Arcana generation through the backend job system, persist the result, and view the same project from both web and mobile clients.
