---
type: feature-contract
status: owner-approved
updated: 2026-08-28
area: first-build
owners:
  - CuBiX Meow
  - Brut
approved: 2026-08-27
source: First Build Feature Workshop
---

# First Build Feature Contract

## Status

**Approved by CuBiX Meow and Brut on 2026-08-27. The owners lifted the freeze for Private Development Build 1 on 2026-08-28. External beta and commercial launch gates remain active.**

This contract records the decisions made in the focused 35-decision workshop. It defines the intended first web build and the backend foundation for the later Flutter/Dart iOS client. Private Development Build 1 is now authorized. Unfinished checklist items continue to block external beta, live charging and commercial launch rather than the private implementation foundation.

The owners intend to use Cursor to implement Private Development Build 1 from the approved vault contracts, research, acceptance criteria and decision records. Conversation history is supporting context; the synchronized vault is the implementation source of truth.

## Product promise

YouAreTheSongNow V2 lets a person turn a meaningful song into original, personalized visual art, optionally placing one or two recognizable people inside the song-inspired world.

## Intended first users

- Invited beta testers only; no public registration.
- Most beta testers subscribe for approximately $20 per month.
- Selected reviewers may receive complimentary owner-granted access.
- The web build must function correctly end to end and meet the image-quality standard set by CuBiX Meow and Brut before Flutter development begins.

## Delivery path

1. Build and validate the PHP/SQLite web application and shared HTTP/JSON API.
2. Use the web application as the first real client of that API.
3. Begin the Flutter/Dart iOS client only after the web experience, API and output quality receive owner approval.
4. Flutter consumes the same server-side accounts, portraits, billing/credits, creative engine, jobs, gallery and sharing behavior.

## Exact first-build journey

1. The owner invites a beta tester by email.
2. The tester activates the account through a one-time passwordless link.
3. Future sign-in can use a fresh emailed link or an optional password created by the user.
4. The user accepts the Terms of Service and Privacy Policy and completes the designed onboarding flow.
5. The user arrives at one simple creation page.
6. The user enters the artist/band and song title as free text.
7. The system identifies the intended song, obtains legally usable lyrics or song context, and prepares the hidden creative analysis.
8. On the same creation page, the user uploads or selects one or two saved portraits.
9. The user chooses a curated style, low/medium/high quality, square/portrait/landscape orientation, an optional `No text in image` setting, and optional Special instructions.
10. The interface shows the credit price before submission.
11. On the user's first generation attempt, an unsubscribed user sees the $20.00 monthly paywall after configuring the creation but before generation begins or a result is delivered.
12. Successful Stripe payment grants the monthly credits and resumes the preserved creation; payment cancellation/failure preserves the inputs and performs no image generation.
13. Submission reserves the required credits and creates a locked background job.
14. The user sees a polished animation and honest progress messages and may leave the page.
15. A successful image appears in the gallery and the credits are finalized.
16. The user can view, download, delete, regenerate, email or link-share the image.

## First-build screens

1. Invitation activation / sign-in
2. Optional password setup and password reset
3. Terms/privacy acceptance and onboarding
4. Creation page
5. Generation progress state
6. Gallery
7. Full-image view
8. Account/profile settings
9. Subscription/credit status and payment
10. Owner/admin area
11. Legal/privacy/terms pages

## Accounts and profiles

- Access is invite-only.
- The initial invitation is a one-time account-activation/sign-in link.
- Future passwordless sign-in links are one-time and short-lived.
- Users may optionally create a password; password reset is required for those users.
- Editable profile information is limited to display name and email.
- Changing email requires verification of the new address before it becomes the active sign-in address.
- Portraits are managed in a separate portrait library.
- Users may delete their account themselves.
- Account deletion requires identity confirmation and a clear irreversible-action warning.
- Deletion is immediate, permanent and has no recovery period.
- It removes the account, portraits, generated images, gallery data, creative artifacts and other associated server data.
- It also ends access, forfeits unused credits and cancels the Stripe subscription immediately so it cannot renew.
- Payment, fraud, tax or accounting records that Stripe or the business must legally retain are the limited exception and must be disclosed in the Privacy Policy.

## Onboarding

- Onboarding and the first-creation paywall are deliberate first-build features, not temporary screens.
- The web onboarding should follow the app-growth philosophy communicated by David Attias and supported by Adapty: sell the outcome, reach the first meaningful result quickly, build trust, personalize only where useful, and measure/iterate.
- The user completes the meaningful setup—song, portrait(s), style, quality and orientation—before encountering the paywall on the first generation attempt.
- The paywall must clearly communicate the outcome and included value before sending the user to Stripe Checkout.
- Do not begin paid image generation or reveal a finished result before payment confirmation.
- Preserve the prepared creation if Checkout is cancelled or fails.
- CuBiX Meow and Brut approved the dedicated onboarding and first-creation paywall design on 2026-08-28. Its philosophy, screen flow, recovery behavior and copy foundation are recorded in [[Onboarding and First-Creation Paywall Contract]]. Final V2 examples, credit values and qualified legal language remain dependencies of their separate checklist gates.
- Every account, including complimentary reviewers, accepts the Terms of Service and Privacy Policy during activation or payment.
- The later mobile onboarding should build on what is learned from the web beta rather than mechanically copy it.

## Song entry, lyrics and legal fallback

- Users enter artist/band and song title as free text; no catalog selection is required.
- An inexpensive model or lookup service resolves the intended song or closest reliable match.
- The system attempts to obtain the actual lyrics through a legally permitted source.
- Lyrics are never displayed to the user.
- Raw lyrics are memory-only and deleted immediately after derived analysis is produced.
- Raw lyrics are never stored or cached, even if a future provider license would permit retention. This applies to Git, databases, queues, temporary files, stored prompt histories, logs, analytics, error reports and backups. A protected in-memory request may send them to the approved analysis provider with provider-side storage disabled.
- Persist only the entered artist/title, retrieval source/status, match confidence and original derived Song DNA unless a license permits more.
- If lyrics are unavailable, use reliable information about the song, themes and meaning.
- Label fallback results as inspired by available song information rather than implying that lyrics were analyzed.
- If neither lyrics nor reliable song information can be found, stop and ask the user to choose another song.
- Never invent lyrics.
- No generation credits are charged when the process stops before image generation.
- Use a bounded two- or three-provider lookup chain when testing shows that it improves coverage economically.
- Cache safe lookup outcomes and apply per-account/IP limits, concurrency limits and fixed provider-call ceilings to prevent cost abuse.
- Final provider licensing, copyright workflow and user-facing language require legal review before the paid beta launches.
- Before that commercial decision, the owners may build and test the workflow privately with an AI/search provider under its applicable terms. This development-only path is limited to owners and authorized developers, uses ephemeral processing and non-quoting Song DNA, and does not authorize external beta or paid access.
- Prefer public-domain, owner-controlled or otherwise authorized songs for repeatable development tests whenever practical.
- After technical and quality feasibility are demonstrated, CuBiX Meow and Brut make a go/no-go decision. If they proceed, commercial lyrics licensing and legal approval remain mandatory before external beta access.

## Creative-engine contract

The following responsibilities run automatically behind the scenes:

```text
Song information
  -> Song DNA
  -> Visual scene plan
  -> Artist/band visual identity
  -> Portrait-integration plan
  -> Final prompt compiler
  -> Image generation
  -> Quality/safety check
  -> Controlled technical fallback
```

- These are required creative responsibilities, not necessarily separate model calls.
- Engineering experiments may combine, simplify or reorder stages when quality is maintained or improved.
- Normal users see none of the intermediate artifacts or compiled prompts.
- Owners can inspect them for debugging, evaluation and cost analysis.
- V1 prompt text is reference material, not a contract.
- Preserve effective V1 behavior while freely rewriting, combining or replacing prompts.
- Evaluate candidate pipelines on song specificity, portrait fidelity, image quality, reliability, speed, per-stage cost and total cost per successful result including retries.

## Originality and copyright-oriented prompt safeguards

- Lyrics may inform analysis but must not be quoted or directly paraphrased into retained creative artifacts.
- Final images contain no visible lyrics, song titles, band names, album titles, label marks, streaming icons or third-party logos.
- Do not replicate album-cover layouts, trademarked symbols or recognizable public figures.
- Produce original visual interpretations based on themes, mood, symbols, composition, lighting and cinematic storytelling.
- User Special instructions cannot override originality, portrait, safety or legal constraints.
- Provider acceptance is not treated as legal clearance.

## Artist identity and curated styles

- The song's meaning determines narrative content.
- Launch with fifteen carefully tested active styles selected for romantic/fine-art, music-driven, surreal and cinematic range. See [[Launch Style Catalog]].
- Seed all 52 recoverable V1 style families into the database. The 37 non-launch styles start inactive but remain searchable, editable, previewable and activatable through admin.
- Each launch style has a name, description, preview image, prompt version and active/inactive status.
- If the user selects a curated style, it is the dominant visual treatment; artist/band visual identity remains a secondary influence.
- If no curated style is selected, artist/band visual identity becomes the primary visual direction.
- Owners manage styles in the admin area: create, edit, preview, reorder, activate and deactivate.
- Preserve style versions so earlier generation provenance is not erased.

## User-facing image controls

- Curated style
- Low, medium or high quality; medium is default and recommended
- Square, portrait or landscape orientation
- One or two portraits
- Optional `No text in image` checkbox. It starts unchecked; when selected, the completed image must contain no readable text or lettering.
- Optional Special instructions checkbox revealing a short field with examples
- One image per submitted generation

When `No text in image` is not selected, deliberate fitting text may appear only when it is original, user-owned, licensed, public-domain or otherwise lawful. Copyrighted lyrics, unauthorized artist/band branding, third-party logos and misleading endorsement signals remain prohibited in every mode.

Advanced model, camera, palette, era, seed and technical settings remain hidden.

## Model and provider routing

- Benchmark multiple lyrics/search, text/reasoning and image providers before launch.
- Groq is available for benchmarking as a fast, inexpensive text/reasoning provider. It may handle song matching, authorized lyric/context analysis, Song DNA, structured prompt work and text-based image evaluation where quality tests support it.
- Groq is not the final image generator under its current documented API capabilities; separate image-generation providers remain required.
- fal.ai is the tentative primary image-provider hub for benchmarking multiple commercial, reference-capable image models behind one server-side adapter. Final selection remains evidence-based.
- Select by measured effectiveness, reliability, speed and total cost.
- Inexpensive models handle work that does not benefit materially from stronger reasoning.
- Image routing is style-aware and quality-aware.
- Maintain an owner-editable mapping of selected style + quality tier to the best-performing image model/provider.
- Users see style, quality and credit cost, not provider details.
- Keep all providers behind replaceable adapters.
- The exact model map remains pending controlled experiments.

## Portrait contract

- Couples are a primary market, including romantic, celebratory and wedding-related use.
- Each account can save up to ten reusable portraits initially.
- A generation supports one or two people.
- Preserve recognizable facial identity: defining facial features, skin tone, hair and approximate age.
- Source clothing, pose, framing and background do not need to be preserved.
- Integrate people naturally into the scene rather than copying a studio portrait.
- Lower quality may use simpler compositions to protect resemblance; higher quality should better combine resemblance with adventurous composition.
- Portrait-quality testing targets at least 90% acceptable recognizable results across a representative evaluation set.
- Imperfect resemblance alone does not trigger an automatic retry or refund.
- Portraits are private, deletable and stripped of location/camera metadata.
- Terms require permission to upload every depicted person and disclose third-party AI processing.
- The upload area includes a short unobtrusive reminder and links to the relevant policies.

## Generation jobs and failures

- Generation is asynchronous and continues if the user leaves or closes the page.
- Gallery job states include queued, generating, completed and failed.
- During processing, use a lightweight animation and honest, aesthetically pleasing status messages.
- Once submitted, a job is locked and cannot be cancelled.
- Credits are reserved at submission.
- If a provider returns no usable image because of a technical/provider/safety failure, retry with a configured provider of comparable quality.
- If no usable image is delivered, release/refund the reserved credits automatically.
- Charge only for a delivered usable image.
- Do not retry or refund solely because facial resemblance is somewhat imperfect.

## Subscription and credits

- Stripe is the approved first-beta payment and subscription provider.
- Use Stripe-hosted Checkout for signup, the hosted Customer Portal for billing management, and verified idempotent webhooks as the subscription-state authority.
- Development uses Stripe's sandbox/testing tools. Do not create live charges until the payment rules and launch gates are cleared.
- Stripe product name: **You Are The Song Now Membership**.
- Card statement descriptor: **YOU ARE THE SONG**; disclose this recognizable descriptor before payment and in billing help.
- The invite-only beta subscription is $20.00 USD per month, charged immediately with no free trial.
- Paid beta enrollment initially accepts U.S. billing addresses and USD only. Complimentary reviewer access is granted internally without a Stripe charge.
- The business is California-based. Treat the subscription provisionally as an electronically delivered service/digital product with no tangible media; obtain qualified tax confirmation, set the correct Stripe tax code and monitor other-state nexus before live charging.
- The first-creation paywall appears after configuration but before image generation or result delivery.
- Ordinary subscription cancellation takes effect at the end of the current paid period; access and remaining credits continue until then.
- Permanent account deletion is a separate immediate action and overrides end-of-period access.
- A failed renewal starts a seven-day grace period. The user may sign in, manage billing/account data, view/download/delete existing gallery content and manage sharing, but may not submit new generations or receive renewal credits.
- Verified payment recovery restores generation access and grants the monthly allowance exactly once.
- When a cancelled paid period or failed-payment grace period ends, the account becomes read-only inactive rather than being deleted.
- Read-only users may manage billing/account data and view/download/share/delete existing work, but cannot generate or use credits. Unused subscription credits expire when paid access ends.
- Verified reactivation starts a new paid period and grants its allowance exactly once; existing content remains available.
- Subscription payments are non-refundable and non-prorated, including immediate account deletion, except for duplicate charges, confirmed billing errors, unauthorized payments or refunds required by law.
- Documented owner-approved exceptions may be issued through Stripe. Technical generation failures refund credits rather than subscription money.
- Selected reviewers may receive complimentary access from an owner.
- The subscription grants a fixed monthly credit allowance.
- Subscription credits reset on the billing date and do not roll over.
- Complimentary accounts receive an owner-assigned allowance and reset schedule.
- Low, medium and high quality consume different credit amounts based on measured provider cost and value.
- Show the exact credit price before submission.
- Maintain a durable credit ledger rather than relying only on a mutable balance.
- Track lookup, text-model, image-model, retry, storage and other variable cost per account and generation.
- Final monthly allowance and tier prices are set only after provider benchmarks and margin modeling.
- Additional credit packs are SOON AFTER the core functioning build, not required for the first complete slice.

## Gallery

- Keep the gallery image-first and simple.
- Show up to two small circular portrait thumbnails over the image in the private gallery interface; do not bake them into the artwork.
- Use the real song and artist/band as the gallery label when legal/licensing review permits.
- Allow an original descriptive title as a fallback if required.
- Hide technical prompts and provider details.
- Actions: full-size view, download, permanent delete and generate another version.
- Regeneration reopens the prior song, portraits, style, quality, orientation and Special instructions for optional adjustment and charges normally.

## Sharing

- Images are private by default.
- Each image may be emailed once to one recipient, preserving the successful V1 interaction.
- The email contains a preview and a share link rather than exposing storage credentials.
- Users may copy an unguessable share link.
- The link remains active until the image/account is deleted or the owner selects Stop sharing.
- Stop sharing immediately invalidates the current token.
- Sharing again later creates a new token.
- Sharing does not consume generation credits.

## Storage

- The first functioning web build stores portraits and generated images locally on the application server.
- Application records and physical files remain separate behind a replaceable storage interface.
- Deleting an image/account removes database records and physical files.
- Local operation requires backups, disk-usage monitoring and explicit retention/deletion jobs.
- Backblaze B2 integration is SOON AFTER the core build works and must not change gallery/API behavior.

## Shared PHP HTTP/JSON API

The web client uses the same API intended for Flutter wherever practical. Required domains include:

- invitation activation, passwordless authentication, optional passwords and sessions/tokens;
- profile and account deletion;
- portrait upload/list/delete;
- song submission and resolution state;
- generation price quote, submission and job status;
- subscription status and credit ledger/balance;
- gallery list/detail/delete/regenerate;
- share-link creation/status/invalidation and one-time email sharing;
- consistent validation, authorization and error responses.

Core business and creative logic remains on the server. Flutter is a client, not a duplicate backend.

## Owner/admin area

Keep the first admin deliberately small:

1. Users: search, status, subscriptions, complimentary access and credit adjustments
2. Styles: previews, prompt versions, ordering, activation and style-to-model routing
3. Jobs: status, failures, retries and per-generation provider cost
4. Totals: users, images, credits and AI spending to date

## First-build non-goals

- Public registration
- More than two people in one generation
- More than ten saved portraits per account
- Multiple images from one submitted request
- User-visible prompts or creative-engine artifacts
- Advanced image controls
- Exact V1 prompt or code migration
- Public community/social feed
- Automatic refunds for subjective resemblance dissatisfaction
- User cancellation after submission
- Advanced admin analytics
- Backblaze B2 before the core build functions
- Additional credit packs before the core build functions
- Gallery upscaling and print-master preparation
- Poster, art-print and T-shirt ordering or fulfillment
- Android client

## SOON AFTER

- Backblaze B2 migration through the storage adapter
- Additional credit-pack purchases
- Provider-neutral gallery upscaling, print-master preparation and poster/T-shirt functionality after the core V2 web build works and receives owner approval
- Further styles and style/provider benchmark updates
- Flutter/Dart iOS application after web/API/quality approval
- Continued onboarding experiments and optimization

## MAYBE / RESEARCH

- Artist-direct band partnerships and a verified indie-artist lyrics/catalog workflow; strategic direction approved, exact timing and scope pending
- Final lyrics provider, license and bounded fallback chain
- Final text/reasoning models per creative stage
- Final image model per style and quality tier
- Exact $20 subscription price
- Monthly included credits and tier credit costs
- Exact Stripe price and subscription lifecycle behavior
- Final rewritten launch prompts, previews and style-to-model routing
- Final onboarding flow
- Whether song/artist labels require any special trademark/provider-language treatment
- Legal review of lyrics processing, portraits, sharing, terms and paid commercial use

## Acceptance standard

The web build is ready for Flutter review only when:

- every selected feature works through the shared API;
- invited paid and complimentary users can complete the full journey;
- account/email/authentication and immediate deletion work correctly;
- lyrics/context lookup follows the approved legal and fallback policy;
- generations reliably finish or fail honestly without incorrect credit charges;
- portrait evaluation meets the owner-approved quality target;
- the fifteen active styles and three quality tiers meet owner quality expectations;
- gallery, regeneration, download, one-time email and revocable links work;
- admin users can operate the beta and inspect costs/failures;
- measured unit economics support the subscription and credit allowances;
- required privacy, security, deletion and abuse protections pass testing;
- CuBiX Meow and Brut approve the technical function and creative quality.

## Movable build-freeze exit checklist

Work these in the most useful order. If one item is blocked, move to another without treating the freeze as lifted.

- [x] CuBiX Meow and Brut approve this First Build Feature Contract. — Approved 2026-08-27.
- [ ] Development lyrics retrieval is proven privately; then lyrics licensing, temporary processing and legal language are confirmed before any external or paid beta.
- [ ] Subscription/payment provider, price, lifecycle, monthly credits, complimentary access and refund rules are defined.
- [ ] Candidate AI providers are benchmarked for quality, portrait fidelity, reliability and total cost.
- [x] Fifteen launch style candidates are selected and all 52 recovered styles are classified for database/admin access.
- [ ] Launch prompts, previews and style-to-model/quality routing are tested and owner-approved.
- [x] Web onboarding and the core screen flow are designed and owner-approved. Approved 2026-08-28; see [[Onboarding and First-Creation Paywall Contract]].
- [x] API, authentication, privacy, deletion, sharing, rate limiting and local-storage contracts are implementation-ready. Approved 2026-08-28; see [[Implementation Readiness Contract]] and [[../07 Development/Shared API, Security and Data Contract]].
- [x] Deployment target and operating-cost model fit the intended subscription. Approved 2026-08-28; see [[../07 Development/Deployment and Operating Cost Contract]].
- [x] Acceptance tests cover the complete web journey and Flutter-facing API. Approved 2026-08-28; see [[../07 Development/Acceptance Test Contract]].
- [x] CuBiX Meow and Brut lift the build freeze for Private Development Build 1. Approved 2026-08-28. This does not authorize external beta access, paid users, live Stripe charges or commercial protected-lyrics use; the unfinished gates above remain launch blockers.

## Private Development Build 1 authorization

Build 1 may implement the approved architecture using Stripe sandbox, configurable development credits, protected server-side development credentials, private internal song-analysis tests, local SQLite and local image storage.

Build 1 must not enable:

- public or invited external access;
- live Stripe charges;
- commercial processing of protected lyrics;
- unrestricted account registration;
- final provider, style or credit claims not supported by completed benchmarks.

Provider routing, credit values, style routes and eligible song sources remain configuration-driven and provisional. The purpose of Build 1 is to create the working foundation needed to test and refine those decisions.
