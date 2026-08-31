---
type: current-project
status: active
updated: 2026-08-30
area: priorities
---

# Current Priorities

This note is the daily “what matters now” list for CuBiX Meow and Brut. Keep it short. Update it when focus shifts.

## Active now

1. **Run controlled private image tests:** compare the same one- and two-person inputs on Flash Lite and full Flash; change one variable at a time and record identity, narrative fidelity, coherence, prohibited text, latency, cost and usability.
2. **Resolve no-text and unusable-image detection:** one otherwise successful no-text image rendered `TICKET`. Prompt-only enforcement and human-only identity acceptance are not sufficient for the final 90-image gate.
3. **Provisional routing only:** Flash Lite is the low-cost candidate at about $0.0336 per 1K output; full Flash is the quality anchor at about $0.067. Medium/default and high routing are not decided.
4. **Complete live functional checks:** share/revoke, deletion, failure/refund, account recovery/change, Stripe lifecycle, complimentary access, admin, mobile API, accessibility and Hostinger operational drills.
5. **Keep release gates closed:** external beta, live Stripe and commercial protected-lyrics use remain blocked. Lyrics remain transient and never saved.
6. **Automated foundation healthy:** **162 passed, 0 failed** on 2026-08-30. See [[Build 1 Assessment 2026-08-30]].
7. **Print/poster/T-shirt/upscaling remain post-Build-1.** Vault = workshop; `/docs` = Meow Control.

## Newly documented

- **V1 sample showcase integrated (2026-08-30):** Round 009 copied all 77 V1 sample artworks into V2, replaced Home hardcoded examples with a featured archive hero and progressively loaded carousel, and added `/showcase` with Masonry v4 + imagesLoaded v5, orientation filters, legacy disclosure and an accessible viewing dialog. Review pack: `design/review/round-009/`. Archive remains separate from signed-in Gallery and SQLite.

- **Round 008 local baseline verified (2026-08-30):** the local Cursor checkout is clean, contains the complete YS production asset/handoff delivery, includes the owner-page style activate/deactivate control from `3953689`, and passes **178 tests with 0 failures**. The responsive redesign must preserve and re-verify that owner control. Cursor may commit locally but must not push or deploy without separate owner authorization.

- **YS production identity integrated (2026-08-30):** flat/premium YS marks, wordmark, app icon, responsive atmosphere/Create backgrounds, Gallery empty-state art and paywall previews are wired into the current web/mobile app. Screenshot review pack: `design/review/round-008/`. Flutter remains documentation-only.

- **Gemini Interactions Song DNA (2026-08-30):** replaced legacy `generateContent` grounding. Live Springsteen/`Dancing in the Dark` incomplete structure traced to Search without enforced JSON schema. V1 JSON-repair recorded as historical emergency fallback only. Mandatory `store=false`.

- **Final quality set approved:** 90 images, including 30 individual, 40 couple and 20 wedding/celebration compositions; 30 per orientation; every 15-style by three-tier combination tested twice.
- **Portrait fixtures ready:** the owners confirmed private-development consent for the known V1 sample material, but the 77 local V1 sample files are finished generated artworks rather than source portraits. Eight fully synthetic portrait inputs and a controlled solo/couple/wedding pairing manifest are now stored privately outside Git for provider benchmarking.
- **Controlled song fixtures approved:** CuBiX Meow and Brut approved twelve metadata-only private-development targets covering wedding, romance, celebration, narrative, symbolism and ambiguous lookup behavior. Lyrics remain outside the repository, and rights-controlled owner/indie fixtures must be added before external beta use.
- **Two-track song strategy documented:** the general-song path requires explicit commercial permission for retrieval, temporary AI analysis, retained non-reconstructive Song DNA and image generation. The band/verified-artist path uses directly authorized catalogs and remains commercially viable even if broad popular-song licensing does not scale. Both paths share the same rights-aware Song DNA and image pipeline.
- **No development lyrics purchase:** the owners selected private, ephemeral, noncommercial testing with popular-song lyrics before considering paid catalog licensing. Raw lyrics stay out of Git, databases, queues, logs, analytics, backups and external tests. This is a development risk boundary, not commercial clearance or permission to violate source terms.
- **Broad private development, gated commercialization:** use the fixed 12-song core for controlled comparisons plus a broad rotating range across genres, eras, moods, languages and narrative structures to refine the engine. Before user access, disable unrestricted sourcing and require a verified public-domain, permissive, owner-controlled, artist-authorized or licensed rights record.
- **V1 prompt foundation selected:** the approved V2 contract preserves V1's Song DNA, exact portrait direction, cinematic environment/camera language, soft user instructions and structured StyleMap. It adds an independent leakage sanitizer, newly invented visual moments, strict schemas, immutable versions, portrait-preserving retries and OCR/output review.
- **Provider-specific compilation selected:** V1's Gemini-shaped prompt is evidence, not a universal template. V2 keeps one canonical creative package and uses separately versioned, benchmarked compilers for each provider/model's reference images, prompt behavior, native controls, safety responses and retry requirements.
- **Build 1 visual direction approved:** preserve V1's dark cinematic atmosphere and artwork-first reveal, but replace futuristic type, blue-purple gradients, glass panels, glowing borders, emojis and AI-console language. The approved direction is a modern record sleeve and cinematic photo book with matte dark surfaces, warm paper text, one stage-light accent, editorial typography and restrained track/playhead motion.
- **Print/merchandise phase boundary approved:** V1 proves the intended functionality through a working gallery upscale action, separate upscale-credit concepts, T-shirt transparency tools and fulfillment demonstrations. V2 will rebuild that functionality behind replaceable adapters after the core V2 web build works and receives owner approval. No V1 provider is selected by default, and the deferred phase does not block the first Cursor build.
- **Acceptance-test checklist item complete:** CuBiX Meow and Brut approved the complete web, quality, reliability, user-journey, authentication, Stripe, privacy, deletion, abuse, admin, Hostinger, Flutter API and WCAG 2.2 Level AA acceptance gates on 2026-08-28.
- **Hostinger deployment live:** the existing shared-hosting account serves Build 1 through `/yatsnV2/public/`; the root domain redirects into the application. PHP 8.2, SQLite, required extensions, health endpoint and one-minute cron worker are working. Move to a VPS if a larger deployment proceeds.
- **Hostinger beta email ready:** use `support@youarethesongnow.com` as the single transactional sender, reply-to address and customer-help mailbox. The mailbox is active, MX/SPF/DKIM/DMARC are green, and secure SMTP is verified at `smtp.hostinger.com:465`. Confirm renewal or automatic renewal before the domain's displayed 2026-10-08 expiration.
- **Deployment checklist item complete:** the verified Hostinger plan, release/rollback process, worker, storage thresholds, monitoring, fixed-cost limits and variable AI-spend safeguards are owner-approved.
- **Hostinger beta capacity verified:** Premium Web Hosting provides 25 GB disk, 2 GB RAM, one CPU core, 40 PHP workers, 80 maximum processes, unlimited bandwidth, weekly backups and Cron Jobs. SQLite is already working. Hostinger-managed beta backups may retain deleted data for up to 45 days; live deletion remains immediate. A larger deployment moves to a VPS if the owners proceed.
- **Hostinger worker approved:** run the generation queue through a one-minute Cron Job, matching the proven V1 hosting pattern. Start with one locked worker and add concurrency only after SQLite and resource testing.
- **Invitation controls approved:** only CuBiX Meow and Brut can invite paid beta testers or complimentary reviewers. Invitation links are single-use, expire after seven days and can be revoked or resent.
- **Owner login addresses selected privately:** CuBiX Meow and Brut each have a separate private email selected for owner access. The addresses are intentionally excluded from the repository and public dashboard and will be installed through protected deployment configuration.
- **Owner two-factor method ready:** both owners have Authy available for the required separate owner-account two-factor enrollment. Phone numbers, recovery codes and authenticator secrets remain outside the repository.
- **Implementation-readiness checklist item complete:** the shared PHP API and security/data contract is owner-approved, including opaque web/mobile sessions, idempotent paid operations, private media, immediate live deletion, secure sharing, rate limits, owner two-step verification and deferred media backups until B2.
- **Onboarding foundation approved:** onboarding is the first creation and subscription journey, not a tutorial. The flow sells the emotional outcome, uses only meaningful inputs, builds anticipation, reflects the prepared creation and presents the $20 paywall before generation. Approved opening: `You are the song now. A meaningful song becomes a cinematic world, with you and the people you love at the heart of its story.` Product copy will not use em dashes.
- **Onboarding checklist item complete:** the exact first-build copy foundation, honest progress language, recovery messages, disclosure placement and welcome, creation, summary, paywall and reveal actions are owner-approved.
- **First Build Feature Contract owner-approved:** CuBiX Meow and Brut approved the consolidated 35-decision contract on 2026-08-27 and authorized Private Development Build 1 on 2026-08-28. External beta and commercialization gates remain active.
- **Lyrics/legal research opened:** Musixmatch is the leading verified licensed-API candidate; written rights for temporary AI processing and original commercial image generation remain required.
- **Development boundary approved:** owners and authorized developers may test a terms-compliant AI/search workflow before purchasing lyrics licensing. No invited tester, reviewer or paid-user access is allowed until the later licensing/legal gate is cleared.
- **Artist-direct strategy recorded:** future commercial lyrics may come primarily from direct band agreements and verified indie artists supplying material they control. Public lyrics are mainly a development aid; artist verification, rights, revocation and catalog tooling remain outside the approved first-build scope unless explicitly added.
- **Stripe sandbox configured:** live mode remains unchanged and its product catalog remains clean. The `Logan White sandbox` now contains the approved `You Are The Song Now Membership` at $20.00 USD per month with no trial, lookup key `yatsn_v2_membership_monthly`, and the approved Customer Portal cancellation and billing-management controls. Checkout and webhook configuration wait for application endpoints.
- **First-creation paywall:** onboarding lets the invited user prepare the song, portrait(s) and image choices; the spectacular value-focused paywall appears on the first generation attempt. $20.00 is charged immediately with no trial, then the preserved creation resumes.
- **Cancellation versus deletion:** cancelling the subscription preserves access and credits through the paid month. Choosing permanent account deletion immediately ends access, cancels renewal and removes application content; required payment records may remain with Stripe or the business.
- **Failed-payment grace:** seven days of sign-in, billing/account management and existing gallery access; new generations and renewal credits remain paused until verified payment recovery.
- **Inactive accounts:** after cancellation expiration or unresolved grace, accounts retain read-only gallery/account access while generation is blocked and unused subscription credits expire. Reactivation grants a fresh paid-period allowance.
- **Refund rule:** monthly payments are non-refundable and non-prorated, including immediate account deletion, except duplicate charges, billing errors, unauthorized payments and legally required refunds. Technical generation failures return credits, not subscription money.
- **Initial market:** paid beta enrollment is U.S.-only and USD-only; complimentary reviewers are owner-granted internally without a Stripe payment.
- **Tax posture under review:** California-based, electronic-only service/digital images, provisionally not subject to California sales tax under current CDTFA guidance. Tax-code selection, professional confirmation and other-state nexus monitoring remain required before live charging.
- **Stripe customer names:** `You Are The Song Now Membership` is the product; `YOU ARE THE SONG` is the card statement descriptor and will be disclosed before payment.
- **Groq development credential ready:** a 90-day `yatsn-v2-development` key was created in the Default Project and stored only in the owner's password manager. It expires 2026-11-26. Installation and use wait for private-development authorization and protected secret configuration.
- **Paid Gemini development API active:** the protected `yatsn-v2-development` key is installed server-side. `gemini-3.6-flash` performs stateless Interactions Song DNA and native Gemini image models receive private inline portraits. Paid-tier data terms, current provider settings and customer-data restrictions must be reverified before external beta.
- **Gemini portrait path selected provisionally:** Replicate P-Image-Edit and fal Kontext Multi remain disabled experimental adapters after live tests omitted or minimized people. Final quality-tier routing remains contingent on controlled portrait, couples, style, reliability, privacy and total-cost evidence.
- **fal.ai development credential ready:** a restricted API-only `DEVELOPMENT: yatsn-v2-development` key is stored only in the owner's password manager. It cannot manage billing, keys, usage or compute and is not installed in the repository or hosting configuration.
- **fal.ai test budget ready:** the account has a verified $25.00 one-time credit balance, no recorded usage and automatic top-up disabled. Credits currently show an expiration of 2027-08-28.
- **Style catalog selected:** fifteen balanced launch candidates are chosen. All 52 recoverable V1 styles will be database-seeded and admin-accessible; 37 start inactive for future activation. Prompt rewrites, previews and routing remain required. See `development-vault/05 Product Design/Launch Style Catalog.md`.
- **First Build Feature Contract:** invite-only paid beta; $20 monthly subscription with fixed non-rolling credits; complimentary reviewers; hidden cost-optimized creative engine; fifteen active launch styles plus 37 inactive admin-ready styles; style-aware model routing; one/two-person portraits; local storage first; shared web/Flutter API; simple owner admin; one-time email and revocable link sharing.
- V1 prompts come from hardcoded source templates, runtime assembly, database-stored style prompts, model-generated artifacts, and retry mutations.
- Later V1 stores static visual style prompts in `arcana_styles.prompt_text` with admin CRUD.
- The older worker preserves a large inline style catalog that is strong evidence for the predecessor of the DB-managed prompt library.
- Deep references: `docs/rebuild/13-prompting-functionality-reference.md` and `14-prompt-quality-and-refinement-analysis.md`.
- **V2 systems inventory / build map:** `docs/rebuild/15-v2-systems-inventory.md` (detail) and `docs/rebuild/16-v2-build-map-summary.md` (glanceable skeleton, dependency map and phased order). Use these as implementation guidance together with the newer approved contracts.

## Next

1. Controlled same-input Flash Lite versus full Flash comparison for one and two people.
2. Design no-text enforcement and unusable-image detection before the formal benchmark.
3. Complete the live functional and Hostinger operational checks listed above.
4. Keep fal/Replicate off the default path; use only for later benchmarks.

## Waiting / blocked

- Lyrics/provider licensing and legal review.
- Provider/style/quality/cost benchmarks.
- Monthly included credits and low/medium/high tier economics pending provider benchmarks.
- Final V1 database style rows unless a production DB/export is available.

## Not now

- Porting V1 PHP workers wholesale
- External tester or reviewer access
- Live Stripe charges
- Commercial use of protected lyrics without licensing and legal clearance
- Gallery upscaling, print-master preparation and poster/T-shirt fulfillment before the core V2 web build is approved

## Cat says

Choose the first toys before building the whole cat tree. Document the spellbook before rewriting the spells.
