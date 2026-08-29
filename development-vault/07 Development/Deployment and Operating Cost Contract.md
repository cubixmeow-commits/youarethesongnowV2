---
type: deployment-cost-contract
status: owner-approved
updated: 2026-08-28
area: deployment-operations
owners:
  - CuBiX Meow
  - Brut
approved: 2026-08-28
source: Deployment and Operating Cost Workshop
---

# Deployment and Operating Cost Contract

## Status

CuBiX Meow and Brut approved this contract on 2026-08-28. The deployment target, worker, rollback, secrets, monitoring, capacity, email and operating-cost behavior are implementation-ready for Private Development Build 1. Production deployment and external launch remain gated.

## Approved initial deployment target

- Hostinger is the approved initial deployment provider for the V2 web beta.
- The repository is currently synchronized by the owner under `/yatsnV2/` on Hostinger.
- The public entry point is `https://youarethesongnow.com/`.
- A 2026-08-28 external check confirmed that the root domain returns HTTP 302 to `/yatsnV2/site/`, which returns HTTP 200 and serves the You Are The Song Now page.
- Direct directory access to `/yatsnV2/` returns HTTP 403 and is not the public entry point.
- Use the existing Hostinger service when it passes the required capability check.
- If the current plan cannot reliably support secure PHP, SQLite, private writable storage, scheduled tasks and the required background job worker, use a small Hostinger VPS rather than weakening the approved architecture.
- Do not publish a partially configured beta or expose secrets, SQLite files, portraits, generated images or temporary files through the public directory.

## Verified current Hostinger plan

A read-only hPanel review on 2026-08-28 confirmed:

- plan: Premium Web Hosting;
- disk allowance: 25 GB;
- observed disk use: 6.18 GB;
- RAM allowance: 2 GB;
- CPU cores: 1;
- PHP workers: 40;
- maximum processes: 80;
- inodes: 400,000;
- bandwidth: unlimited;
- Hostinger-managed backups: weekly;
- Cron Jobs control: available;
- SQLite: confirmed working in this shared-hosting account by the owner.

The current shared plan is the approved first target for development and beta validation. A VPS is not required initially. The plan must still pass bounded background-worker, concurrent request, private-storage and SQLite locking tests before external beta launch. If the owners proceed to a larger deployment after the beta, move the application to an appropriately sized VPS rather than treating Premium shared hosting as the long-term scale target.

Hostinger's published backup guidance states that automatic weekly web-hosting backups may be retained for six weeks and are stored outside the plan's disk quota. For the beta only, Hostinger-managed disaster backups may retain deleted data for no more than 45 days. Live deletion remains immediate, backup data remains unavailable to normal operations, and restoration must reapply deletion records before serving traffic.

## Approved background-worker operation

- Use Hostinger Cron Jobs as the canonical generation-worker scheduler. V1 successfully used this mechanism on the same hosting provider.
- Schedule the queue worker to check for work every minute.
- The web request validates and submits the job, then returns. It does not need to keep a long provider call open or start a separate immediate worker in the first build.
- Start with one global generation worker for the beta.
- Use an atomic worker lock and job lease. If the prior worker is still active, the next scheduled invocation exits safely rather than duplicating work.
- A claimed job records progress, bounded attempts, provider costs and its final credit outcome.
- The worker exits after its bounded unit of work and never runs an unbounded loop on shared hosting.
- Consider a second worker only after SQLite locking, duplicate-delivery and Hostinger resource tests pass.
- If the cron mechanism cannot meet the approved reliability or acceptable wait time during beta testing, a VPS becomes a launch requirement.

## Approved deployment and rollback process

- `main` is the Hostinger deployment branch.
- New work occurs on separate branches and does not change the live site before review.
- Merge to `main` only after the relevant automated checks, manual acceptance checks and owner approval pass.
- Keep the current public site online while the V2 application is being built and tested.
- Switch the root-domain redirect to the finished application only after the approved acceptance gate.
- Mark every beta deployment with a named Git release or tag so the exact previous working version is identifiable.
- Before a database migration, create one encrypted SQLite safety copy outside the public directory.
- Keep only the most recent pre-deployment SQLite safety copy and remove it after seven days.
- A failed release rolls application code back to the previous marked release. Restore the pre-deployment database copy only when required and only through a procedure that preserves newer user data whenever possible.
- Database migrations are versioned, ordered and recorded. A deployment never edits the production schema manually without a matching migration record.

## Approved environment and secret management

- Store Stripe, Groq, fal.ai, SMTP and other private credentials only in protected server configuration outside the publicly served website directory.
- Never place real secrets in Git, client JavaScript, Flutter code, logs or the SQLite database.
- Use separate development/test and live credentials.
- Commit only a placeholder configuration template that lists required names without real values.
- Limit production-secret access to CuBiX Meow and Brut.
- Apply the narrowest practical file permissions to production configuration.
- Revoke and replace an accidentally exposed credential immediately.
- Record secret creation and rotation metadata without storing the secret in the audit record.
- Do not create or install live payment or production AI credentials before their applicable launch gates are cleared.
- Provider adapters read credentials from server configuration and fail closed when a required value is missing.

## Approved monitoring, logs and alerts

- Check the public site and API health at least every five minutes.
- Record a heartbeat for each scheduled worker invocation and its outcome.
- Alert CuBiX Meow and Brut after two consecutive failed health checks, a missing worker heartbeat, repeated generation failures, failed Stripe event processing or provider spend approaching a configured limit.
- Alert at 70 percent and 85 percent of Hostinger disk usage.
- Keep ordinary application and worker logs for 30 days.
- Keep security and payment-event logs for 90 days.
- Keep the approved owner audit history for one year.
- Logs must not contain lyrics, portrait or image bytes, passwords, raw tokens, full payment details, private provider payloads or secrets.
- Show a simple current health, queue, failure, storage and provider-cost summary in the owner admin.
- Use Hostinger's existing metrics plus a free or inexpensive external uptime checker for the beta.
- Monitoring failure must not expose private health details through the public health endpoint.

## Approved storage, database and image-processing limits

- Store portrait and generated-image files as private files, never as SQLite binary data.
- Use SQLite for records, settings, credits, jobs, provenance and private file references.
- Accept portrait uploads up to 20 MB only as temporary incoming files.
- Immediately decode and validate the upload, correct orientation, remove unnecessary metadata and resize it to a maximum 2048-pixel longest edge.
- Save the normalized working portrait at high visual quality, targeting approximately 2.5 MB or less without visible identity damage.
- Create a separate approximately 320-pixel portrait thumbnail.
- Delete the original incoming portrait file immediately after successful normalization. If processing fails, delete the temporary upload and do not create a portrait record.
- Prefer the installed ImageMagick/Imagick PHP capability and use PHP GD as the fallback. Test both paths with orientation, color and malformed-file fixtures.
- Preserve the original full-quality delivered generated artwork for download. Create separate optimized display and thumbnail versions for web and gallery use.
- Keep the approved maximum of ten active saved portraits per account.
- Automatically remove temporary files within 24 hours or sooner under deletion rules.
- Alert owners at 70 percent disk usage and treat 85 percent as urgent.
- At 90 percent, pause new portrait uploads and generations while keeping sign-in, billing, existing downloads, sharing controls and deletion available.
- Begin the Backblaze B2 migration before usage remains above 70 percent, or immediately after the core beta is proven, whichever occurs first.
- Review SQLite performance and growth when the database reaches 500 MB. Require an approved storage or database plan before it reaches 1 GB.

## Approved fixed-cost limits

- Use the existing prepaid Hostinger plan, domain, weekly backups, Cron Jobs and included email wherever they meet the approved requirements.
- New non-AI recurring services are limited to $25 USD per month during private development.
- New non-AI recurring services are limited to $50 USD per month during the paid beta.
- Stripe transaction fees, AI usage and future Backblaze B2 storage are variable costs tracked separately.
- A new recurring service that would exceed the applicable limit requires recorded approval from CuBiX Meow and Brut.
- Prefer free monitoring and existing Hostinger features until beta evidence demonstrates that a paid service is needed.

## Approved variable AI-spend limits

- Private development and provider benchmarking have a $100 USD monthly AI budget.
- During paid beta, target average AI cost at or below $7 per paid subscriber per month, equal to 35 percent of the $20 subscription price.
- The hard beta ceiling is $10 per paid subscriber per month unless CuBiX Meow and Brut approve a temporary test override.
- Complimentary reviewers share a $50 monthly AI pool unless the owners approve more.
- Alert owners at 50, 75 and 90 percent of each configured budget.
- At 100 percent, automatically pause new paid provider work while keeping sign-in, billing, galleries, downloads, sharing controls and deletion available.
- A temporary override records amount, reason, approving owner and expiration.
- Monthly included credits, tier credit prices and retry allowances must fit within these limits after provider benchmarking.
- Record cost per lookup, text stage, image attempt, retry, successful delivered image, account and billing period.

## Approved initial email direction

- Use owner-created `youarethesongnow.com` email addresses through Hostinger SMTP for first-beta transactional email.
- Route email through the server-side notification adapter. Web and Flutter clients never receive SMTP credentials.
- Required email categories include invitation, passwordless sign-in, password reset, email-change confirmation, account/security notices, billing notices and one-recipient image sharing.
- Transactional sender: `support@youarethesongnow.com`.
- Sender display name: `You Are The Song Now`.
- Reply-to and customer help: `support@youarethesongnow.com`, using the same monitored mailbox for the beta.
- Hostinger reports the domain email-authentication configuration with a green status as of 2026-08-28. Confirm application SMTP delivery, replies and spam placement during implementation acceptance.
- Verified Hostinger SMTP configuration: `smtp.hostinger.com`, port `465`, TLS/SSL, authenticated as `support@youarethesongnow.com`. Store the mailbox password only in protected server configuration.
- Verified mailbox status: active Standard Business Email with a 20 GB mailbox allocation; email service currently expires 2027-08-28.
- Hostinger currently warns that `youarethesongnow.com` expires 2026-10-08. Confirm renewal or automatic renewal before beta launch so the site, email and sign-in links are not interrupted.
- Before external delivery, verify SMTP authentication, TLS, SPF, DKIM, DMARC alignment, bounce behavior and sending limits.
- Do not treat a queued email as delivered. Record safe delivery status and retry temporary failures without sending duplicates.

## Approval

Approved by CuBiX Meow and Brut on 2026-08-28. Later material changes require a recorded owner decision and synchronized contract updates.
