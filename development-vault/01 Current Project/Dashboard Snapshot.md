---
type: dashboard-snapshot
status: active
updated: 2026-08-28
area: project
---

# Dashboard Snapshot

Curated summary for the GitHub Pages command center in `/docs`.

Agents and humans: when Current Project truth changes, update this snapshot **and** `docs/dashboard-data.js` (or the equivalent dashboard content) so the public hub stays aligned.

## Phase

Planning / creative-engine design

## Build status

BUILD FREEZE — planning only

## Delivery path

**Web first -> Flutter/Dart iOS second**

- Phase 1: rebuild/refine the web application on the PHP backend with SQLite initially.
- Phase 2: build the iOS application in Flutter + Dart.
- Flutter should consume the same PHP backend through clean HTTP/JSON APIs.
- The web rebuild is the shared backend/reference client foundation, not a throwaway prototype.

## Stack direction

- Web/backend: PHP
- Database (initial): SQLite
- Mobile client: Flutter + Dart (iOS first, after web validation)
- Strategy: rebuild/refine V1 functionality, not V1 code

## Mission

Turn a song into an interpreted cinematic visual experience, optionally placing the user inside that world.

## First-build workshop status

The focused workshop is complete. CuBiX Meow and Brut approved its consolidated First Build Feature Contract on 2026-08-27. Checklist item 1 is complete; the build freeze remains active.

Core scope: invite-only paid beta; $20 monthly subscription with fixed non-rolling credits; complimentary reviewers; accounts and immediate deletion; reusable one/two-person portraits; legally permitted song/lyrics context; hidden cost-optimized creative engine; fifteen active launch styles plus 37 inactive admin-ready styles; style- and quality-aware model routing; background generation; gallery/download/regeneration; one-time email and revocable link sharing; simple admin; local storage first; shared web/Flutter API.

The movable freeze-exit checklist now covers contract approval, lyrics/legal work, subscription economics, provider benchmarks, launch styles/routing, onboarding/screens, implementation-ready contracts, deployment/costs, acceptance tests and an explicit owner freeze lift.

Checklist item 2 is split into two gates. First, owners and authorized developers privately prove a terms-compliant, ephemeral AI/search-provider workflow, preferably with authorized test material. Then the owners make a go/no-go decision. If proceeding, a licensed lyrics API, written permission for temporary AI processing and qualified legal review are required before any invited tester, reviewer or paid user receives access. Musixmatch is the leading verified commercial candidate.

Longer-term commercial direction favors direct band partnerships and verified indie artists supplying lyrics they own or control. Public lyrics are primarily a development aid. Artist onboarding, rights verification, revocation and catalog management are strategic follow-on work unless explicitly added to the first-build scope.

Stripe is approved for the beta. A read-only account audit found a live-capable account with an empty product catalog. The price is $20.00 USD monthly, charged immediately with no free trial. The value-focused paywall appears after the invited user configures the first creation but before generation/result delivery; successful Checkout resumes the preserved creation. Hosted Checkout, Customer Portal, signed idempotent webhooks and sandbox/test-clock validation remain the planned architecture.

Ordinary cancellation now keeps access and remaining credits until the paid period ends. If a user chooses permanent account deletion instead, access ends immediately, Stripe renewal is cancelled and application content is permanently removed. Legally required payment records are the disclosed exception. Credit economics remain open.

A failed renewal receives a seven-day grace period. The user can sign in, manage billing/account data and view/download/delete existing gallery content, but new generations and renewal credits remain paused. After an unresolved grace period—or after a cancelled paid period expires—the account becomes read-only inactive: existing content remains accessible, generation is blocked and unused credits expire. Verified reactivation grants a fresh paid-period allowance once.

Subscription payments are non-refundable and non-prorated, including immediate account deletion, except for duplicate charges, confirmed billing errors, unauthorized payments and refunds required by law. Technical generation failures return credits instead of subscription money. Credit economics remain open pending provider benchmarks.

Paid beta enrollment is limited to U.S. billing addresses and USD. Complimentary reviewers receive internal access without a Stripe transaction. Stripe uses product name `You Are The Song Now Membership` and card statement descriptor `YOU ARE THE SONG`, disclosed before payment.

The business is California-based. Current CDTFA guidance preliminarily supports treating this electronic-only subscription and its digitally delivered images as nontaxable in California when no tangible property is transferred. Qualified confirmation, the final Stripe tax code and other-state nexus monitoring remain launch requirements.

Groq is available as the first text/reasoning benchmark candidate for song matching, authorized lyrics/context analysis, Song DNA and structured prompt work. Its current APIs provide text output, including from vision inputs, rather than final image generation. Separate image providers must be benchmarked. No project API key has been created or stored.

fal.ai is the tentative primary image-provider hub. The initial reference-image comparison will include Seedream V4 Edit, FLUX.2 Pro, Nano Banana, Nano Banana 2 and GPT Image 2. Low/medium/high assignments wait for blind portrait, couples, style, reliability, privacy and total-cost results.

Fifteen balanced launch style candidates are selected across cinematic realism, fine art, romance, music, surrealism, dark romance and fantasy. All 52 recoverable V1 styles will live in the database and owner admin; 37 start inactive for later activation. Prompt rewrites, previews and routing remain open.

Session handoff: resume with the dedicated onboarding and first-creation paywall design workshop on the morning of 2026-08-29. After the complete freeze-exit checklist and explicit owner freeze lift, Cursor will build the first version from the approved vault as the implementation source of truth.

## Prompt-system status

**V1 prompting functionality is now mapped as a system, not a single prompt.**

Five prompt origins are documented:

1. hardcoded source templates/directives;
2. runtime-assembled cinematic prompts;
3. database-stored static style prompts (`arcana_styles.prompt_text`);
4. model-generated artifacts (Song DNA, Band Visual DNA, StyleMap);
5. fallback/retry prompt mutations.

The older worker preserves a large inline style catalog that appears to be strong predecessor material for the later DB-managed style library. Exact final production DB rows still require a database export if one is recoverable.

Deep references:

- `docs/rebuild/13-prompting-functionality-reference.md`
- `docs/rebuild/14-prompt-quality-and-refinement-analysis.md`
- `development-vault/04 Prompt Lab/V1 Prompt Functionality Map.md`

## Creative-engine direction

```text
Source Context
  -> Song Interpretation
  -> Song DNA
  -> Visual Narrative Plan
  -> Artist Visual Identity
  -> Portrait Integration Plan
  -> Scene Composition
  -> Prompt Compiler
  -> Provider Adapter
  -> Quality / Safety Evaluation
  -> Controlled Retry
```

## Accepted decisions

- Rebuild functionality, not code
- PHP backend/web app
- SQLite initially
- Web first, then Flutter/Dart iOS using the shared PHP backend

## Top open decisions

- Meaning of “Dynamic Band Lore” in V2
- Portrait fallback honesty
- Lyrics persistence architecture
- Branding in-image vs post-process
- Whether artist visual identity runs by default

## Read next

1. `START HERE.md`
2. `01 Current Project/Current Priorities.md`
3. `04 Prompt Lab/V1 Prompt Functionality Map.md`
4. `docs/rebuild/13-prompting-functionality-reference.md`
5. `docs/rebuild/14-prompt-quality-and-refinement-analysis.md`
6. `02 Decisions/Decision Inbox.md`
7. `docs/rebuild/12-open-creative-decisions.md`
