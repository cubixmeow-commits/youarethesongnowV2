---
type: current-project
status: active
updated: 2026-08-28
area: product
---

# Product Definition

YouAreTheSongNow V2 rebuilds the **functionality and product value** of AISaga Arcana without porting its legacy implementation.

## Product promise

Turn a song into an interpreted cinematic visual experience, optionally placing the user inside that visual world.

## Artist-direct strategic direction

A long-term goal is to work directly with bands and integrate their authorized music and lyrics into the system. Indie artists should also be able to supply lyrics they own or are authorized to use when generating images from their music.

This artist-direct model can become the preferred commercial catalog path: it gives participating artists a new visual experience for fans while establishing clear permission at the source. Publicly available lyrics are expected to be used mainly for private development and feasibility testing, not assumed to be the commercial catalog foundation.

The exact artist onboarding, identity/rights verification, lyric submission, catalog management, revocation, reporting and commercial terms remain to be designed. This direction does not silently add those systems to the approved first-build contract.

## First-build focus established by the owners

The first usable web build should let an invited beta user create an account, save profile information and reusable portraits, select a song and artist/band, obtain permitted lyrics or song context, choose a curated visual style and quality tier, spend subscription credits, generate an image asynchronously, and save/download/share it from a personal gallery.

The PHP backend should expose clean HTTP/JSON APIs for the later Flutter/Dart iOS client. Inexpensive AI work should use inexpensive models while stronger image models are reserved for the quality tiers that require them. Initial images may be stored on the application host.

The invite-only beta includes a $20.00 USD monthly subscription for U.S. billing addresses, charged immediately with no free trial, fixed non-rolling credits and owner-granted complimentary reviewer access. Additional credit packs and Backblaze B2 storage follow the core functioning build. Onboarding and the paywall are deliberate first-build features: the user prepares the first song-and-portrait creation before encountering the paywall, and generation begins only after payment. Their final design remains pending.

Ordinary subscription cancellation preserves access and remaining credits through the paid billing period. A user may instead permanently delete the account immediately, ending access, cancelling renewal and deleting application content; legally required payment records are the disclosed exception.

After a cancelled paid period or unresolved seven-day payment grace expires, the account becomes read-only inactive. Existing images remain viewable, downloadable, share-manageable and deletable, while generation is blocked and unused credits expire. Verified reactivation restores paid access with a fresh allowance.

## Preserve from V1

- song interpretation before generation;
- Song DNA as a structured intermediate artifact;
- persistent personal generation history/gallery;
- optional portrait reference mode;
- asynchronous generation jobs;
- progressive fallback/retry behavior;
- artist/band-specific visual identity as a distinct creative layer.

## Refine for V2

- separate song meaning from cinematography;
- add a Visual Narrative Plan before final prompting;
- preserve portrait identity longer during retries;
- distinguish artist visual identity from actual lore;
- remove contradictory prompt instructions;
- reduce dependence on one giant provider-facing prompt;
- avoid persisting raw lyrics by default unless explicitly chosen;
- keep provider/model implementation replaceable.

## Do not inherit automatically

- V1 PHP file structure;
- duplicated worker variants;
- duplicated Stripe webhook paths;
- mutable credit columns as the sole accounting model;
- runtime schema hacks;
- hardwired Gemini model IDs;
- marketing terminology that does not match actual behavior.

## Open product questions

See [[../02 Decisions/Decision Inbox]].
