# 08 — V1 Risks, Unknowns & Decision Register

**Purpose:** prevent V2 from silently inheriting V1 ambiguity.

**Status:** planning only.

## Highest-priority risks

### R1 — Multiple implementations of the same subsystem

**Observed:** V1 contains multiple queue processors and at least two Stripe webhook paths.

**Risk:** an agent could choose the wrong legacy implementation and encode obsolete behavior as V2 truth.

**Rule for V2:** no feature is considered specified merely because one V1 file implements it. Prefer the best-evidence current path plus owner intent.

### R2 — Production wiring is not fully represented in source

**Observed uncertainties:**

- which worker production cron actually invoked;
- which Stripe webhook URL was configured;
- some database tables/columns appear to have existed without definitive repository migrations.

**Risk:** repository-only migration assumptions could be wrong.

**Rule for V2:** before any real migration, compare against production/last-production configuration and a schema-only DB dump.

### R3 — Plan model drift

**Observed:** helper code understands `APPRENTICE`, `STARTER`, `CREATOR`, while older paths use `free` / `paid` and some gating is UI-heavy.

**Risk:** inconsistent entitlements and possible bypasses.

**Rule for V2:** define capabilities centrally and enforce them server-side. UI visibility is secondary to authorization.

### R4 — Credit race conditions

**Observed:** generator checks balance before enqueue, but credit is deducted after successful render. Concurrent submissions can pass the pre-check before later deductions.

**Risk:** oversubmission, inconsistent failure behavior, difficult refunds.

**Rule for V2:** use transactional reservation/debit semantics or another explicit accounting state machine backed by a ledger.

### R5 — Uploaded portrait privacy/retention

**Observed:** portrait refs are stored on local filesystem and included in generation requests; fallback logic may remove them on later attempts.

**Risk:** unclear retention/deletion expectations for personally identifying media.

**Rule for V2:** define ownership, retention, deletion, access URLs, provider transmission, and user disclosure before implementing portrait uploads.

### R6 — Lyrics/input rights and product policy

**Observed:** V1 accepts lyrics directly and sends song/lyric context to the model.

**Risk:** product behavior around copyrighted lyrics, storage, transmission, and generated outputs needs explicit V2 policy rather than accidental continuation.

**Rule for V2:** decide permitted input sources, whether lyrics are stored, truncation/retention rules, and user-facing guidance before launch implementation is finalized.

### R7 — Prompt logic trapped in worker code

**Observed:** Song DNA, Dynamic StyleMap behavior, cinematic prompt construction, branding, safety, and retry behavior live close to worker/provider plumbing.

**Risk:** difficult testing/versioning and accidental behavior changes when providers change.

**Rule for V2:** treat creative orchestration as versioned domain behavior with testable schemas and prompt versions.

### R8 — Storage coupling

**Observed:** gallery/delete paths understand local filesystem and B2 details.

**Risk:** changing storage provider or making assets private can ripple through the app.

**Rule for V2:** application records reference media assets/storage keys; storage adapters resolve upload/delete/access.

### R9 — Stale/unrelated documentation in V1 root

**Observed:** root documentation includes VibeKB material that does not describe Arcana's application schema even though it lives beside Arcana docs/code.

**Risk:** Cursor or another agent can confidently implement the wrong thing.

**Rule for V2:** legacy docs are evidence only when corroborated by Arcana-specific source or explicit owner decisions.

## Product decisions that remain open

These are **not blockers for assessment**, and no implementation should be started just to force an answer.

### Product identity

- Is V2's primary output one image, an image sequence, a story+image package, or multiple creation modes?
- Is **Song DNA** still the central interpretation layer?
- Is **Dynamic Band Lore / Dynamic Band Style** the defining Arcana feature in V2?
- How prominently should the user put themselves into the visual via portrait references?

### Input model

- Does the user type/paste lyrics, provide a song title only, or select from metadata/search integrations?
- Are lyrics retained after generation or used ephemerally?
- What custom instructions should users be allowed to add?

### Result model

- Should V2 organize results primarily as projects, individual generations, galleries, or all three?
- Can one project contain multiple attempts/variations?
- Which output metadata should users be able to inspect or edit?

### Business model

- Keep credits?
- Subscription only?
- Subscription plus monthly credits / top-up packs?
- Which capabilities are entitlement-gated rather than credit-costed?

### Plans

- Keep or replace `APPRENTICE / STARTER / CREATOR`?
- Is a free tier desired?
- Does priority queueing remain meaningful?
- Is permanent gallery/storage a paid entitlement or baseline product behavior?

### Legacy continuity

- Must V1 users be able to sign into V2 with existing credentials?
- Must historical renders migrate?
- Must credit balances migrate?
- Must subscriptions migrate, or should existing Stripe subscriptions be transitioned separately?

### Sharing/privacy

- Are creations private by default?
- Does V2 have public share links?
- Can shared pages reveal song/artist/custom prompt data?
- What is the deletion promise for generated images and portrait references?

## Technical decisions intentionally deferred

Do not lock these until product requirements and one architecture spike justify them:

- Next.js vs another web framework;
- Expo/React Native vs another mobile framework;
- PostgreSQL vendor/hosting;
- auth provider;
- queue provider;
- object-storage provider;
- deployment platform;
- AI provider mix;
- real-time transport vs polling;
- subscription/entitlement vendor abstractions.

## What can be decided from V1 evidence now

These are strong recommendations, even before implementation:

1. **Keep generation asynchronous.** V1 already proves this interaction model.
2. **Create one canonical generation pipeline.** Do not repeat V1's worker proliferation.
3. **Make Song DNA explicit and versioned** if it remains part of the product.
4. **Separate creative orchestration from AI provider adapters.**
5. **Persist generation attempts and errors**, not just final renders.
6. **Use server-side entitlement enforcement.**
7. **Use an auditable credit/payment ledger** if credits survive.
8. **Treat media as owned objects**, not filesystem paths.
9. **Use one Stripe webhook/fulfillment path.**
10. **Do not begin data migration from repository assumptions alone.**

## Suggested assessment-only next steps

No application code is required for any of these:

1. Extract the exact V1 Song DNA schema and prompt.
2. Extract the exact Dynamic Band StyleMap prompt/schema and fallback.
3. Document the seven-ish image retry attempts in order.
4. Inventory all V1 PHP user-facing routes/screens and classify keep/change/retire.
5. Inventory all referenced DB tables/columns from source into a provisional schema map.
6. Compare that map against a schema-only production database export when available.
7. Inventory Stripe product/price intent without exposing secrets.
8. Decide which V1 behavior is product identity versus historical implementation.
9. Capture the owners' new V2 product decisions in ADR/decision Markdown before Cursor builds features.

## Build freeze

At the owners' current direction, **V2 is in assessment/planning mode. Do not scaffold or implement the application yet.** Documentation, source analysis, diagrams, inventories, and decision records are allowed; product code is not.
