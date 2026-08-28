---
type: current-project
status: active
updated: 2026-08-28
area: priorities
---

# Current Priorities

This note is the daily “what matters now” list for CuBiX Meow and Brut. Keep it short. Update it when focus shifts.

## Active now

1. **Next session:** begin the dedicated onboarding and first-creation paywall design workshop on the morning of 2026-08-29.
2. Work the approved contract's movable build-freeze exit checklist; move to another item when one is blocked.
3. **Lyrics feasibility first, licensing second:** privately build and test ephemeral lyrics-to-Song-DNA processing with a terms-compliant AI/search provider. After technical and quality validation, make a go/no-go decision; commercial licensing and legal approval remain required before external or paid beta access. See `development-vault/03 Research/Lyrics Retrieval and Legal Feasibility.md`.
4. **Provider benchmark planning active:** Groq is available for text/reasoning tests; fal.ai is the tentative primary image hub. Start the reference-image matrix with Seedream V4 Edit, FLUX.2 Pro, Nano Banana, Nano Banana 2 and GPT Image 2, then assign tiers only from blind quality/cost results. See `development-vault/03 Research/AI Provider Benchmark Plan.md`.
5. **Stripe selected; U.S. $20 paywall set:** the business is California-based; accept USD payments from U.S. billing addresses, charge $20.00 monthly immediately with no trial, and keep complimentary reviewers outside Stripe billing. Use product name `You Are The Song Now Membership` and card descriptor `YOU ARE THE SONG`. California guidance preliminarily treats electronically delivered/SaaS products without tangible media as nontaxable; obtain qualified confirmation and monitor other-state nexus. Included credits and tier pricing remain pending provider benchmarks. See `development-vault/03 Research/Stripe Subscription Plan.md`.
6. Keep the shared development vault as working memory; keep `/docs` as the polished command center.

## Newly documented

- **First Build Feature Contract owner-approved:** CuBiX Meow and Brut approved the consolidated 35-decision contract on 2026-08-27. Checklist item 1 is complete; the build freeze remains active.
- **Lyrics/legal research opened:** Musixmatch is the leading verified licensed-API candidate; written rights for temporary AI processing and original commercial image generation remain required.
- **Development boundary approved:** owners and authorized developers may test a terms-compliant AI/search workflow before purchasing lyrics licensing. No invited tester, reviewer or paid-user access is allowed until the later licensing/legal gate is cleared.
- **Artist-direct strategy recorded:** future commercial lyrics may come primarily from direct band agreements and verified indie artists supplying material they control. Public lyrics are mainly a development aid; artist verification, rights, revocation and catalog tooling remain outside the approved first-build scope unless explicitly added.
- **Stripe approved and account inspected:** the existing live-capable account has an empty product catalog, so V2 starts clean. No products, prices, keys or webhooks were created or changed during planning.
- **First-creation paywall:** onboarding lets the invited user prepare the song, portrait(s) and image choices; the spectacular value-focused paywall appears on the first generation attempt. $20.00 is charged immediately with no trial, then the preserved creation resumes.
- **Cancellation versus deletion:** cancelling the subscription preserves access and credits through the paid month. Choosing permanent account deletion immediately ends access, cancels renewal and removes application content; required payment records may remain with Stripe or the business.
- **Failed-payment grace:** seven days of sign-in, billing/account management and existing gallery access; new generations and renewal credits remain paused until verified payment recovery.
- **Inactive accounts:** after cancellation expiration or unresolved grace, accounts retain read-only gallery/account access while generation is blocked and unused subscription credits expire. Reactivation grants a fresh paid-period allowance.
- **Refund rule:** monthly payments are non-refundable and non-prorated, including immediate account deletion, except duplicate charges, billing errors, unauthorized payments and legally required refunds. Technical generation failures return credits, not subscription money.
- **Initial market:** paid beta enrollment is U.S.-only and USD-only; complimentary reviewers are owner-granted internally without a Stripe payment.
- **Tax posture under review:** California-based, electronic-only service/digital images, provisionally not subject to California sales tax under current CDTFA guidance. Tax-code selection, professional confirmation and other-state nexus monitoring remain required before live charging.
- **Stripe customer names:** `You Are The Song Now Membership` is the product; `YOU ARE THE SONG` is the card statement descriptor and will be disclosed before payment.
- **Groq available:** use as a replaceable text/reasoning candidate, not the final image generator. No API key has been created or stored; benchmark access waits for authorization after the freeze.
- **fal.ai tentative:** use one server-side adapter to benchmark multiple reference-capable image models. Final provider and tier routing remain contingent on portrait/couples, style, reliability, privacy and total-cost evidence.
- **Style catalog selected:** fifteen balanced launch candidates are chosen. All 52 recoverable V1 styles will be database-seeded and admin-accessible; 37 start inactive for future activation. Prompt rewrites, previews and routing remain required. See `development-vault/05 Product Design/Launch Style Catalog.md`.
- **First Build Feature Contract:** invite-only paid beta; $20 monthly subscription with fixed non-rolling credits; complimentary reviewers; hidden cost-optimized creative engine; fifteen active launch styles plus 37 inactive admin-ready styles; style-aware model routing; one/two-person portraits; local storage first; shared web/Flutter API; simple owner admin; one-time email and revocable link sharing.
- V1 prompts come from hardcoded source templates, runtime assembly, database-stored style prompts, model-generated artifacts, and retry mutations.
- Later V1 stores static visual style prompts in `arcana_styles.prompt_text` with admin CRUD.
- The older worker preserves a large inline style catalog that is strong evidence for the predecessor of the DB-managed prompt library.
- Deep references: `docs/rebuild/13-prompting-functionality-reference.md` and `14-prompt-quality-and-refinement-analysis.md`.
- **V2 systems inventory / build map** (planning only): `docs/rebuild/15-v2-systems-inventory.md` (detail) and `docs/rebuild/16-v2-build-map-summary.md` (glanceable skeleton, dependency map, phased order). Review against V1 audits before any freeze lift.

## Next

1. Run the onboarding/paywall design workshop.
2. Work the remaining movable freeze-exit checklist in the approved contract.
3. Turn accepted creative-engine choices into testable experiments and explicit stage contracts.
4. If the old production DB is recoverable, export `arcana_styles` and compare the final rows against the legacy inline style catalog.
5. After the complete checklist and explicit freeze lift, hand the approved vault specification to Cursor to build the first version.

## Waiting / blocked

- Lyrics/provider licensing and legal review.
- Provider/style/quality/cost benchmarks.
- Monthly included credits and low/medium/high tier economics pending provider benchmarks.
- Final V1 database style rows unless a production DB/export is available.
- Explicit lift of the build freeze before any application scaffolding.

## Not now

- Scaffolding PHP app structure
- Installing frameworks
- Creating databases, APIs, or UI product code
- Porting V1 PHP workers wholesale

## Cat says

Choose the first toys before building the whole cat tree. Document the spellbook before rewriting the spells.
