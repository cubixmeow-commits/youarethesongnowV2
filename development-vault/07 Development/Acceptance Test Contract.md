---
type: acceptance-test-contract
status: owner-approved
updated: 2026-08-28
area: acceptance-testing
owners:
  - CuBiX Meow
  - Brut
source: Acceptance Testing Workshop
---

# Acceptance Test Contract

## Status

CuBiX Meow and Brut approved this complete contract on 2026-08-28. It defines the web-journey, quality, security, billing, operations, accessibility and Flutter-facing API acceptance gates for Private Development Build 1 and later external-release review.

## Approved final image-quality set

Run 90 final acceptance generations:

- Test every combination of the 15 active launch styles and three quality tiers twice.
- Produce 30 square, 30 portrait and 30 landscape images.
- Include 30 individual-portrait compositions.
- Include 40 couple compositions.
- Include 20 wedding or celebration compositions.
- Use multiple songs and emotional tones.
- Reuse controlled portrait and song fixtures where direct provider, tier or style comparison matters.
- Include sufficiently varied faces, skin tones, hair, ages and lighting conditions to expose portrait-fidelity weaknesses without collecting unnecessary personal data.

Quality pass rule:

- At least 81 of 90 images, or 90 percent, must preserve recognizable identity and be judged usable by CuBiX Meow and Brut.
- No accepted image may contain visible lyrics, song titles, band names, unwanted third-party logos or other prohibited text.
- When `No text in image` is selected, no accepted image may contain readable words or lettering. The system must reject or repair a result that violates the selected constraint before delivery.
- When the setting is not selected, any visible text must still be intentional, readable and permitted by the approved originality and legal safeguards.
- Record style, quality, orientation, portrait fixture, song fixture, provider/model route, attempts, total cost, duration and owner quality decision for every test.

## Portrait-fixture preparation note

This note records preparation evidence and does not change the approved acceptance gates.

- On 2026-08-28, the owners confirmed that the people represented in the known V1 sample material consent to its reuse for private V2 development.
- The local V1 `sample_images` folder was inventoried read-only. It contains 77 finished generated artworks, not the original portrait uploads needed for controlled identity comparisons.
- V1 therefore does not supply a reusable portrait-input fixture set.
- On 2026-08-28, eight fully synthetic single-person portrait fixtures and a private pairing manifest were prepared outside Git. They provide four controlled solo assignments, four two-person assignments and two wedding/celebration assignments using separate portrait uploads.
- Keep fixture portraits outside the public repository, minimize personal data, strip unnecessary metadata and limit access to the owners and authorized developers.

## Approved technical reliability and speed gate

- At least 86 of the 90 acceptance jobs, or 95 percent, deliver an image without manual intervention.
- At least 90 percent complete within five minutes of submission.
- Every job reaches `completed` or `failed` within ten minutes.
- No job remains permanently queued or generating.
- No repeated request, page reload, browser close or scheduled-worker overlap creates a duplicate image, credit transaction or gallery entry.
- Deliberately simulate a provider failure and verify the configured comparable-quality fallback.
- A job that cannot deliver an image releases or returns reserved credits within one minute of final failure.
- Closing the browser does not interrupt generation.
- Returning later shows the correct job state, progress or completed image.
- Reliability measurement includes all automatic attempts and records total elapsed time and total provider cost.

## Approved invited paid-user journey gate

Complete three consecutive end-to-end runs with new paid-tester invitations:

1. Desktop Safari.
2. Desktop Chrome.
3. An actual iPhone using Safari.

Every run verifies:

- invitation activation and required terms acceptance;
- passwordless sign-in;
- optional password creation and password recovery;
- onboarding and song lookup;
- one-person and two-person portrait selection across the set;
- style, quality, orientation, the optional no-text setting and Special instructions;
- Stripe test-mode Checkout and verified webhook fulfillment;
- the correct monthly credit grant;
- background generation after the browser is closed or left;
- correct gallery, download and regeneration behavior;
- one-recipient email sharing and revocable persistent-link sharing;
- subscription management and cancellation behavior;
- individual image deletion and permanent account deletion.

No accepted run may require direct database editing, manual credit repair or other hidden operator intervention. A failed run must be corrected and the three-run sequence restarted from a clean invited account.

## Approved complimentary reviewer journey gate

Complete two end-to-end complimentary-reviewer runs:

1. Desktop browser.
2. An actual iPhone using Safari.

Every run verifies:

- an owner can invite the account as a Complimentary Reviewer;
- the reviewer bypasses Stripe Checkout and the subscription paywall;
- the reviewer receives the same monthly credit allowance and quality-tier costs as a paid beta tester;
- credits reset exactly once each month without duplicate grants;
- access remains active until an owner revokes it, unless the owner assigns an optional expiration date;
- revocation prevents new image generation while preserving read-only access to the existing gallery;
- restoring complimentary access preserves the account and gallery without duplicating credits or account data.

The complimentary-reviewer tests must consume credits normally and count against the approved shared reviewer AI-cost pool. They may not use hidden unlimited generation or bypass the normal credit ledger.

## Approved authentication and account-security gate

- An uninvited person cannot create an account.
- Expired, reused, altered or revoked invitation links are rejected.
- A magic sign-in link expires, works only once and cannot be altered or used for another account.
- Sign-in and recovery responses do not disclose whether an email address belongs to an account.
- Repeated magic-link requests, sign-in attempts and password attempts trigger the approved rate limits without blocking ordinary use.
- Optional password creation, password sign-in, password recovery and password change all work correctly.
- Changing an account email requires successful verification of the new address before the change takes effect.
- A normal session can remain active for 30 days, while every sensitive action enforces the approved recent-authentication requirement.
- Signing out invalidates the session and prevents further use of its credentials.
- Revoking or deleting an account immediately removes authenticated access.
- A customer account cannot read or change another customer's profile, portraits, jobs, images, shares, subscription or credit ledger through either the interface or direct API requests.
- Customer accounts cannot access owner functions or owner-only data.
- Owner accounts require successful two-factor authentication.
- Owner and customer permissions are verified on the server for every protected request and are not dependent on hidden interface controls.

## Approved Stripe subscription and credit-ledger gate

- Test the $20.00 USD monthly membership entirely with Stripe test tools before enabling live billing.
- A successful Checkout return alone does not activate paid access. A verified Stripe webhook is the subscription-state authority.
- Cancelled or failed Checkout preserves the prepared creation, grants no credits and starts no generation.
- Duplicate, delayed, replayed and out-of-order Stripe events never duplicate a subscription transition or credit grant.
- A successful monthly renewal replaces the included subscription-credit allowance on the billing date. Unused subscription credits do not roll over.
- Every credit grant, reservation, capture, release, expiration and reasoned owner adjustment appears exactly once in the durable credit ledger.
- Generation submission atomically reserves the displayed credit price and creates no more than one job.
- Successful image delivery captures the reservation exactly once. Final failure releases the reserved credits automatically.
- Ordinary cancellation preserves access and remaining credits through the current paid period, then makes the account read-only.
- A failed renewal begins the approved seven-day grace period, grants no renewal credits and blocks new generations while preserving gallery and billing access.
- At the end of an unresolved grace period, the account becomes read-only and unused subscription credits expire.
- Immediate account deletion cancels renewal and forfeits unused credits without an automatic subscription refund, except for the approved billing exceptions or a refund required by law.
- Test-mode customers, subscriptions, events and payments can never be treated as live billing records.
- Acceptance includes successful, cancelled and failed Checkout; successful renewal; failed renewal and recovery; cancellation at period end; immediate deletion; webhook signature rejection; webhook replay; and event delivery in an unexpected order.

## Approved privacy, sharing and immediate-deletion gate

- Source portraits, full gallery images and thumbnails are private by default and cannot be retrieved by guessing or altering a file URL.
- An accepted portrait upload has embedded metadata removed and the original upload is replaced by the approved normalized working copy.
- Every production AI provider is configured under terms and settings that prohibit use of customer portraits, image inputs and generated images for model training.
- A one-recipient email share sends once to the specified address and its access link expires after seven days.
- A persistent share link remains available until the account owner stops sharing or deletes the image.
- A shared page exposes only the intended image and approved safe display information. It never exposes source portraits, prompts, account details, credits, private gallery information or other images.
- Stopping a share invalidates its link immediately.
- Deleting an image immediately removes its live full image, derived display copies, thumbnail, gallery record and all share access.
- Permanent account deletion requires recent authentication and explicit confirmation, then immediately invalidates sessions, cancels renewal, revokes shares and removes the live profile, portraits, images, gallery data, creative data and unused credits.
- A provider result that arrives after image or account deletion is discarded and removed instead of being attached to an account.
- Provider-side deletion is requested wherever supported and the cleanup outcome is recorded for verification.
- Only payment, tax, fraud or accounting records required by Stripe, the business or applicable law may remain after account deletion.
- Hostinger-managed backups may retain protected historical copies temporarily, but deleted data never reappears in the live application and ages out within the approved backup-retention window.
- Acceptance includes direct live-file and database verification after image deletion and account deletion, as well as attempts to reuse revoked, expired and deleted share links.

## Approved abuse, spending and storage gate

- Song lookup limits are enforced at 10 requests per 10 minutes and 50 requests per day for each account.
- Invalid and nonexistent song searches count toward those limits so they cannot be used for a cost attack.
- Repeated requests, multiple browser tabs and altered direct API calls cannot bypass a limit.
- Each account can have no more than one active generation job.
- No paid provider call begins until the server validates membership or complimentary entitlement, available credits, request limits and the current system-spending status.
- Spending alerts trigger at 50, 75 and 90 percent of every approved AI budget. Cost-incurring operations pause automatically at 100 percent.
- The shared complimentary-reviewer AI pool pauses at its approved $50.00 monthly limit.
- A spending pause blocks cost-incurring song lookups and new generations but preserves sign-in, gallery viewing, downloads, sharing management, billing, image deletion and account deletion.
- Resuming cost-incurring operations after an automatic pause requires an owner action with a recorded reason.
- Uploads larger than 20 MB, invalid images and unsafe files are rejected without being retained.
- An accepted portrait is normalized to a maximum 2048-pixel longest edge and a target size of approximately 2.5 MB or less, with a separate 320-pixel thumbnail.
- Storage alerts trigger at 70 and 85 percent of available space. New uploads and generations pause at 90 percent.
- SQLite receives a capacity review at 500 MB, and a migration plan is required before it reaches 1 GB.
- Acceptance confirms that rate limits, provider failures, spending pauses and storage pauses never consume, duplicate or strand credits incorrectly.

## Approved owner-admin and audit gate

- Only authenticated owner accounts with successful two-factor authentication can open or call admin functions.
- Owners can invite users, search accounts, inspect status, manage paid or complimentary access, revoke access and assign optional complimentary-reviewer expiration dates.
- Owners can inspect the durable credit ledger and append a reasoned adjustment but cannot silently rewrite or delete ledger history.
- All 52 curated styles remain available in admin, while only the approved 15 active styles appear to customers.
- Owners can edit style activation, safe display copy, prompt configuration, preview, quality routing and display order.
- Style, prompt and provider-routing changes affect only future submissions and never alter a locked or completed job.
- Owners can inspect jobs, failures, provider routes, attempts, duration and cost without exposing private provider credentials.
- Owners can manage provider routing and spending limits without API keys or other secrets being returned to the browser.
- Account restrictions, credit adjustments, source-portrait access and manual deletion assistance require a written reason.
- The admin provides no customer-account impersonation feature.
- Every sensitive admin action records the owner, time, action, affected record, reason and relevant before-and-after values.
- Audit records remain searchable, protected from ordinary editing or deletion and retained for one year.
- Deleted customer content disappears from admin views as well as customer-facing views.
- Replayed or repeated admin requests cannot duplicate invitations, credit changes or other side effects.

## Approved Hostinger deployment, worker and rollback gate

- A production-like deployment succeeds on the actual Hostinger shared-hosting account using PHP and SQLite.
- `https://youarethesongnow.com/` reaches the V2 application correctly, while private application, database, upload, log and configuration files cannot be opened through public URLs.
- Stripe, AI-provider and email credentials remain outside the public site and never appear in source control, browser responses, logs or error pages.
- Every database migration completes successfully against a production-like copy before deployment.
- Deployment creates the approved encrypted pre-migration SQLite safety copy and removes it after seven days.
- The existing working site remains available until the new release passes its health check.
- Each release is tagged, with its exact code and database version recorded.
- Hostinger Cron launches the background worker once per minute.
- The worker lock prevents overlapping launches from processing the same job twice.
- A queued job continues after the browser closes and is recovered safely after an interrupted worker run.
- A stale job eventually completes or fails honestly instead of remaining permanently stuck.
- Health checks run every five minutes. Two consecutive failures or a missing worker heartbeat alerts CuBiX Meow and Brut.
- Failed Stripe-event processing, repeated generation failures and provider spending approaching an approved limit also generate alerts.
- Transactional SMTP from `You Are The Song Now <support@youarethesongnow.com>` passes invitation, sign-in, sharing and required account-message tests, with replies returning to the same monitored address.
- A real rollback exercise restores the preceding release without losing completed customer records or duplicating credits, jobs or payments.
- After rollback, health checks, sign-in, gallery access, deletion and background-worker operation are tested again.

## Approved Flutter-facing API and compatibility gate

- Flutter development does not begin until the web application passes its acceptance tests and CuBiX Meow and Brut approve its image quality.
- The web application and future Flutter application use the same versioned `/api/v1` PHP API and the same server-side accounts, credits, jobs, gallery and data.
- No essential business, billing, credit, authorization, privacy or generation rule exists only in browser code.
- A documented API test suite covers authentication, profiles, product options, portraits, song lookup, drafts, Checkout handoff, credits, jobs, gallery, sharing and deletion.
- Every endpoint has a defined successful response, safe error response, authorization rule and validation behavior.
- API errors use stable codes and never expose PHP errors, database details, private file paths, internal prompts or provider information.
- A reference mobile-style client completes the full creation journey against the actual Hostinger API before Flutter work begins.
- Portrait upload and authorized image download work through an actual iPhone-sized client connection.
- Slow, interrupted and repeated requests do not duplicate Checkout sessions, jobs, credits, images or shares.
- A background job can be checked correctly after the client closes and later reopens.
- Authentication uses opaque revocable credentials, with no server, Stripe, email or AI-provider secrets stored in the client.
- Customer data remains isolated when resource identifiers are altered or protected requests are made directly.
- After Flutter development begins, a breaking API change requires a new API version. Backward-compatible additions may remain within `/api/v1`.
- The API contract and automated compatibility suite pass before every web or mobile release.

## Approved accessibility and responsive-interface gate

- The beta web application targets WCAG 2.2 Level AA across the complete customer and owner journey.
- The complete journey works with a keyboard alone, with a logical focus order and a clearly visible focus indicator.
- A dialog contains focus while open, closes with Escape where dismissal is allowed and returns focus to the appropriate trigger.
- Every form control has a visible label and useful instructions. Validation announces each error and moves focus to the first invalid field after submission.
- Passwordless authentication and optional password flows permit pasting and do not introduce an inaccessible authentication puzzle.
- Song lookup, portrait upload, payment return, generation progress, completion, failure and credit changes communicate meaningful updates to screen readers without excessive repeated announcements.
- Status, validation and error meaning never depend on color alone.
- Text, controls, focus indicators and meaningful visual states meet applicable WCAG 2.2 Level AA contrast requirements.
- Touch controls aim for at least 44 by 44 CSS pixels and never fall below the applicable WCAG minimum or allowed exception.
- The complete interface remains usable at 200 percent zoom and at 320 CSS pixels wide without lost content, obscured controls or two-dimensional scrolling for ordinary page content.
- Customer pages work in portrait and landscape orientation.
- Motion honors the user's reduced-motion preference, and no essential information is available only through animation.
- Informative and functional images have useful alternative text. Decorative images are ignored by assistive technology.
- Acceptance includes manual keyboard traversal, VoiceOver testing on Mac and an actual iPhone, and automated accessibility checks across the complete journey.
- An accessibility failure that prevents a task or hides required information from assistive technology blocks acceptance.

## Owner approval

CuBiX Meow and Brut approve the Acceptance Test Contract.
