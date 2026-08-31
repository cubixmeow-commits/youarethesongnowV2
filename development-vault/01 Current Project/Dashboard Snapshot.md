---
type: dashboard-snapshot
status: active
updated: 2026-08-30
area: project
---

# Dashboard Snapshot

Curated summary for the GitHub Pages command center in `/docs`.

Agents and humans: when Current Project truth changes, update this snapshot **and** `docs/dashboard-data.js` (or the equivalent dashboard content) so the public hub stays aligned.

## Phase

Private Development Build 1 — live quality and reliability testing

## Build status

CORE PRIVATE PIPELINE WORKING: song → Gemini Interactions (`gemini-3.6-flash`, Google Search + JSON Schema, `store=false`) → saved derived DNA → portrait inlineData → Gemini image → Hostinger worker → gallery. One- and two-person identity have succeeded live. Flash Lite is the low-cost candidate; full Flash is the quality anchor. Automated tests **162 passed, 0 failed**. No-text, quality consistency, provider routing, live operational checks, rights and commercial gates remain open. See `Build 1 Assessment 2026-08-30.md`.

## Assessment headline

- **Continue private development:** yes; the core product premise works.
- **Begin Flutter:** not yet; web quality and live acceptance are incomplete.
- **Invite external users or charge live customers:** no; rights, reliability, operations and commercial gates remain active.
- **Live evidence:** health, SMTP sign-in/invite, Stripe test membership and credits, cron worker, gallery, Interactions Song DNA, one-person Gemini identity and two-person Flash Lite identity.
- **Known creative defects:** Flash Lite quality variance, occasional unwanted text, and no automatic identity/OCR acceptance layer.

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

The focused workshop is complete. CuBiX Meow and Brut approved its consolidated First Build Feature Contract on 2026-08-27 and authorized Private Development Build 1 on 2026-08-28.

Core scope: invite-only paid beta; $20 monthly subscription with fixed non-rolling credits; complimentary reviewers; accounts and immediate deletion; reusable one/two-person portraits; legally permitted song/lyrics context; hidden cost-optimized creative engine; fifteen active launch styles plus 37 inactive admin-ready styles; style- and quality-aware model routing; background generation; gallery/download/regeneration; one-time email and revocable link sharing; simple admin; local storage first; shared web/Flutter API.

The implementation checklist covers contract approval, private song-analysis feasibility, subscription economics, provider benchmarks, launch styles/routing, onboarding/screens, implementation-ready contracts, deployment/costs and acceptance tests. Unfinished legal, economics and provider items remain external-release gates.

The owners recovered an omitted product direction and resolved its phase boundary. Gallery upscaling, print-master preparation and poster/T-shirt functionality will be added through replaceable provider adapters after the core V2 web build works and receives owner approval. V1 provider integrations are evidence only. This deferred phase does not block the first Cursor build.

Checklist item 2 is split into two gates. First, owners and authorized developers privately prove a terms-compliant, ephemeral AI/search-provider workflow, preferably with authorized test material. Then the owners make a go/no-go decision. If proceeding, a licensed lyrics API, written permission for temporary AI processing and qualified legal review are required before any invited tester, reviewer or paid user receives access. Musixmatch is the leading verified commercial candidate.

The rights-aware research now defines two commercial paths behind the same creative engine. The general-song path runs only when a provider or rightsholder explicitly permits retrieval, temporary AI analysis, retained non-reconstructive Song DNA and commercial image generation. The band and verified indie-artist path uses directly authorized catalogs and can proceed independently if broad popular-song licensing is unavailable or uneconomic.

The owners will not purchase broad lyrics licensing during development. Owners and authorized developers may privately compare context-only and ephemeral full-lyric analysis using the approved popular-song benchmark, with no persistence, public access, external testing, distribution or commercial use. This is an owner-selected development risk boundary, not a copyright exemption or permission to violate source terms. Any user-accessible release remains limited to verified public-domain or permissively licensed works, owner-controlled songs and directly authorized artist catalogs unless the owners later approve broad commercial licensing.

CuBiX Meow and Brut approved a fixed 12-song private-development benchmark on 2026-08-28. It covers wedding, romance, celebration, narrative, symbolism, tonal shifts and ambiguous lookup behavior while storing no lyrics in the repository. The benchmark does not remove the later licensing and legal gates, and rights-controlled owner or indie-artist fixtures must be added before external beta use.

Longer-term commercial direction favors direct band partnerships and verified indie artists supplying lyrics they own or control. Public availability is not treated as permission. Artist onboarding, rights verification, revocation and catalog management are strategic follow-on work unless explicitly added to the first-build scope.

Stripe is approved for the beta. The live product catalog remains unchanged and clean. The existing `Logan White sandbox` now contains the approved `You Are The Song Now Membership` at $20.00 USD monthly with no trial, lookup key `yatsn_v2_membership_monthly`, and the approved Customer Portal controls. The value-focused paywall appears after the invited user configures the first creation but before generation/result delivery; successful Checkout resumes the preserved creation. Hosted Checkout, signed idempotent webhooks and sandbox/test-clock validation remain the planned architecture.

Ordinary cancellation now keeps access and remaining credits until the paid period ends. If a user chooses permanent account deletion instead, access ends immediately, Stripe renewal is cancelled and application content is permanently removed. Legally required payment records are the disclosed exception. Credit economics remain open.

A failed renewal receives a seven-day grace period. The user can sign in, manage billing/account data and view/download/delete existing gallery content, but new generations and renewal credits remain paused. After an unresolved grace period—or after a cancelled paid period expires—the account becomes read-only inactive: existing content remains accessible, generation is blocked and unused credits expire. Verified reactivation grants a fresh paid-period allowance once.

Subscription payments are non-refundable and non-prorated, including immediate account deletion, except for duplicate charges, confirmed billing errors, unauthorized payments and refunds required by law. Technical generation failures return credits instead of subscription money. Credit economics remain open pending provider benchmarks.

Paid beta enrollment is limited to U.S. billing addresses and USD. Complimentary reviewers receive internal access without a Stripe transaction. Stripe uses product name `You Are The Song Now Membership` and card statement descriptor `YOU ARE THE SONG`, disclosed before payment.

The business is California-based. Current CDTFA guidance preliminarily supports treating this electronic-only subscription and its digitally delivered images as nontaxable in California when no tangible property is transferred. Qualified confirmation, the final Stripe tax code and other-state nexus monitoring remain launch requirements.

Groq is available as the first text/reasoning benchmark candidate for song matching, authorized lyrics/context analysis, Song DNA and structured prompt work. Its current APIs provide text output, including from vision inputs, rather than final image generation. Separate image providers must be benchmarked. A 90-day `yatsn-v2-development` key exists in the Default Project, expires 2026-11-26 and is stored only in the owner's password manager. It is not installed in the repository or hosting configuration.

A separate Google AI Studio project and API key named `yatsn-v2-development` are verified. V2 uses `gemini-3.6-flash` for Song DNA (Google Search; V1-compatible acceptance; lyrics transient in private development only; never persisted) and `gemini-3.1-flash-image` for native multimodal portrait artwork (text + inline portrait parts in one request). Free-tier material rules still require synthetic, public-domain, owner-authorized and nonconfidential test material for unpaid traffic.

Replicate P-Image-Edit and fal Kontext Multi produced strong Song DNA scenes but failed the defining portrait requirement (people minimized or omitted). They remain available only as experimental `IMAGE_PROVIDER` options for later benchmarking. Gemini native image is the selected portrait path.

Fifteen balanced launch style candidates are selected across cinematic realism, fine art, romance, music, surrealism, dark romance and fantasy. All 52 recoverable V1 styles will live in the database and owner admin; 37 start inactive for later activation. Prompt rewrites, previews and routing remain open.

The onboarding and first-creation paywall design is owner-approved as of 2026-08-28. The first creation itself serves as onboarding: promise, three real examples, song, portraits, configuration, personalized summary, $20 paywall, generation and reveal. The four-part screen grouping, honest progress language, recovery behavior, disclosure placement and complete screen-by-screen copy foundation are recorded in `development-vault/05 Product Design/Onboarding and First-Creation Paywall Contract.md`. Product copy will not use em dashes.

Final V2 example files, the exact monthly credit allowance and qualified legal wording remain dependencies of their separate provider, economics and legal gates. They do not reopen the approved onboarding structure.

The implementation-readiness contracts are owner-approved as of 2026-08-28. They define the shared versioned PHP service for web and Flutter, secure passwordless and optional-password access, owner two-step verification, private media, immediate live deletion, secure email/link sharing, idempotent billing and generation work, abuse and spending limits, owner audit history and local storage with application-managed media backups deferred until B2.

The deployment and operating-cost contract is owner-approved as of 2026-08-28. Hostinger Premium Web Hosting is the private Build 1 target. `https://youarethesongnow.com/` now routes into the PHP application under `/yatsnV2/public/`; the health endpoint and one-minute Cron Jobs worker are working. The approved plan covers local SQLite and media staging, deployment/rollback, secret handling, monitoring, capacity limits, the single `support@youarethesongnow.com` beta mailbox and operating budgets. A larger deployment moves to a VPS.

The beta mailbox is active. Hostinger reports green MX, SPF, DKIM and DMARC status, with secure authenticated SMTP at `smtp.hostinger.com:465`. Application SMTP delivery and reply testing remain part of implementation acceptance. Hostinger currently displays a 2026-10-08 domain expiration warning, so renewal or automatic renewal must be confirmed before beta launch.

A read-only hPanel review confirmed 25 GB disk, 2 GB RAM, one CPU core, 40 PHP workers, 80 maximum processes, unlimited bandwidth, weekly backups and Cron Jobs. SQLite is already working. Hostinger-managed disaster backups receive a beta-only 45-day deletion exception while live deletion remains immediate.

The Acceptance Test Contract is owner-approved as of 2026-08-28. It defines the complete web journey, final image quality, reliability, paid and complimentary access, authentication, Stripe and credits, privacy and deletion, abuse controls, admin auditing, Hostinger deployment and rollback, Flutter API compatibility and WCAG 2.2 Level AA gates.

The final quality set contains 90 generations, tests every launch-style and quality-tier combination twice, balances all three orientations and emphasizes couples and wedding/celebration use. At least 81 images must preserve recognizable identity and be usable, with no visible lyrics, song titles, band names, unwanted logos or other prohibited text.

CuBiX Meow and Brut lifted the freeze for Private Development Build 1 on 2026-08-28. Cursor may now build the internal V2 foundation from the approved vault using Stripe sandbox, configurable development credits, private provider credentials, SQLite and local image storage. External access, live charges and commercial protected-lyrics use remain blocked until their unfinished gates are completed.

The approved Build 1 visual direction preserves V1's dark cinematic atmosphere and artwork-led reveal while replacing its futuristic typography, blue-purple neon gradients, glass panels, glowing borders, emoji controls and AI-console presentation. The selected identity is an intertwined YS monogram on a black/graphite, platinum and sapphire/cobalt system. Round 008 integrated production flat/premium marks, wordmark, app icon, phone/desktop atmosphere, Create backdrops, Gallery empty-state art and paywall previews into the current web/mobile app (see `design/review/round-008/`). Flutter remains documentation-only.

## Prompt-system status

**V1 prompting functionality is mapped as a system; V1-to-V2 comparison lives in Prompt Lab.**

See `development-vault/04 Prompt Lab/V1 to V2 Creative Engine Comparison.md`. The newest V1 prompt-bearing worker remains the approved creative foundation. V2 preserves the strong 12-field interpretation, one/two-person portrait direction, cinematic scene construction, soft user steering and StyleMap. It replaces raw-lyric persistence, prompt-only leakage protection, album-art-dependent general styling, conflicting text/portrait instructions, weak schema handling and retries that silently removed portraits.

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
- Lyrics retrieval method and user-accessible catalog rights. Persistence is resolved: raw lyrics are never saved.
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
