---
type: technical-contract
status: owner-approved
updated: 2026-08-28
area: shared-api-security-data
owners:
  - CuBiX Meow
  - Brut
approved: 2026-08-28
source: Implementation Readiness Contract
---

# Shared API, Security and Data Contract

## Status

CuBiX Meow and Brut approved this implementation-ready technical translation on 2026-08-28. It defines the contract that the PHP web client and later Flutter client share and is authorized for Private Development Build 1. External access and commercial launch remain gated.

## Contract principles

- One versioned PHP HTTP/JSON API serves the web client and later Flutter app.
- The server owns every business, authorization, billing, credit, job, privacy and deletion decision.
- Clients never contain private provider credentials or directly fulfill payments, credits or generation work.
- Every user-owned resource is authorized on every request.
- Paid or credit-changing requests are safely replayable and cannot duplicate work.
- Provider-specific fields and failures do not leak into the stable client contract.
- Raw lyrics are memory-only and are never retained. No later license changes this product rule unless CuBiX Meow and Brut explicitly amend the contract.
- Portraits and images remain private unless a generated image is deliberately shared.

## HTTP and JSON conventions

- Base path: `/api/v1`
- Production transport: HTTPS only.
- Request and response fields: `camelCase`.
- Resource identifiers: opaque random strings. Do not expose sequential database row numbers.
- Time values: UTC RFC 3339 strings.
- Money values: integer minor units plus ISO currency, such as `2000` and `USD`.
- Credit values: integers. No floating-point credit arithmetic.
- Successful single-resource response: `{ "data": { ... } }`.
- Successful list response: `{ "data": [ ... ], "meta": { "nextCursor": null } }`.
- Failure response: `{ "error": { "code": "stable_code", "message": "safe message", "fields": {}, "requestId": "...", "retryAfterSeconds": null } }`.
- Clients branch on the stable error `code`, not provider text or the English message.
- Cursor pagination is required for gallery, jobs, invitations, users and audit records.
- Responses containing private data use no-store cache headers.
- Downloads set an explicit safe content type and disposition.

## Authentication transports

### Web

- Use a server-managed opaque session in a Secure, HttpOnly cookie with an appropriate SameSite policy.
- Require a CSRF token on every state-changing cookie-authenticated request.
- Rotate the session identifier after activation, sign-in, password change and owner privilege confirmation.

### Flutter

- Issue a short-lived opaque access token and a rotating server-managed refresh token.
- Store mobile credentials only in iOS secure storage.
- Rotation invalidates the previous refresh token. Reuse of an invalidated token revokes the token family and requires sign-in again.
- Mobile session activity follows the approved 30-day active-session rule.

### Shared rules

- Store only hashes of invitation, sign-in, reset, email-change, share and refresh secrets.
- Passwordless sign-in links expire after 15 minutes and are single-use.
- Password-reset and email-change confirmation links expire after one hour and are single-use.
- Passwords use PHP's current recommended memory-hard password hashing and automatic rehashing when settings improve.
- Signing out invalidates the current session or token family.
- `Sign out of all devices` increments the account security version and invalidates all sessions and refresh tokens.
- Owner accounts require a password plus an authenticator-app code. Provide single-use recovery codes stored as hashes.
- Sensitive account actions require authentication completed within the previous 10 minutes. Permanent deletion also requires its own explicit irreversible-action confirmation.

## Roles and access states

Roles:

- `user`
- `owner`

Commercial access types:

- `paidBeta`
- `complimentaryReviewer`

Account states:

- `invited`
- `active`
- `grace`
- `inactive`
- `restricted`
- `deletionPending`
- `deleted`

Role, commercial access and account state are separate fields. A client cannot grant or infer them. Every protected operation checks the server-side combination.

## Stable public-client resources

### Authentication and activation

- `POST /auth/activations/complete`
- `POST /auth/magic-links`
- `POST /auth/magic-links/complete`
- `POST /auth/password-sessions`
- `POST /auth/password-resets`
- `POST /auth/password-resets/complete`
- `POST /auth/refresh`
- `POST /auth/logout`
- `POST /auth/logout-all`

Authentication request endpoints return neutral responses that do not reveal account existence. Rate-limit metadata is returned only when safe.

### Current account

- `GET /me`
- `PATCH /me/profile`
- `POST /me/email-changes`
- `POST /me/email-changes/complete`
- `PUT /me/password`
- `DELETE /me/password`
- `GET /me/sessions`
- `POST /me/deletion-preview`
- `POST /me/deletion-confirmation`

The deletion preview returns consequences and required confirmation state. Confirmation performs immediate live removal and returns no reusable account credential.

### Portraits

- `GET /portraits`
- `POST /portraits`
- `GET /portraits/{portraitId}`
- `GET /portraits/{portraitId}/content`
- `DELETE /portraits/{portraitId}`

Portrait upload uses multipart form data. The server decodes and validates the image before creating the final portrait resource. An account cannot own more than ten active saved portraits.

### Styles and product choices

- `GET /styles`
- `GET /styles/{styleId}`
- `GET /product-options`

Normal clients receive only active styles and safe display fields. `product-options` provides current orientations, quality tiers, credit prices, the optional no-text control and relevant limits from server configuration.

### Song lookups

- `POST /song-lookups`
- `GET /song-lookups/{lookupId}`

Lookup states:

- `queued`
- `searching`
- `found`
- `fallbackFound`
- `notFound`
- `failed`

The public result returns entered artist and title, state and safe user-facing classification. It never returns lyrics, provider prompts or copyrighted source text. Provider attempts and derived Song DNA remain server-side and owner-visible only where permitted.

### Creation drafts

- `POST /creation-drafts`
- `GET /creation-drafts/{draftId}`
- `PATCH /creation-drafts/{draftId}`
- `DELETE /creation-drafts/{draftId}`
- `POST /creation-drafts/{draftId}/summary`

A draft holds the user's song lookup, one or two portrait identifiers, style, orientation, quality, the `noTextInImage` boolean and optional Special instructions. Draft validation is server-side. The no-text value defaults to `false`. Checkout cancellation or failure preserves the draft. Creating a generation job from a ready draft creates one immutable generation snapshot and prevents later draft changes from altering that submitted job.

### Billing and credits

- `GET /membership`
- `GET /credits`
- `GET /credit-transactions`
- `POST /billing/checkout-sessions`
- `POST /billing/portal-sessions`

Checkout-session creation accepts the prepared `draftId` so successful payment can return to the preserved creation. Stripe webhook endpoints are server-to-server and are not part of the public client surface. Verified idempotent webhooks are the authority for paid membership state and monthly credit grants. Credit state is an append-only ledger with reservation, capture, release and owner-adjustment transaction types.

### Generation jobs

- `POST /generation-jobs`
- `GET /generation-jobs/{jobId}`
- `GET /generation-jobs`

Generation-job creation accepts a ready `draftId` and requires an idempotency key. User-visible job states:

- `queued`
- `generating`
- `completed`
- `failed`

Internal attempts may use more detailed states and error classes. One submitted job produces at most one delivered gallery image. A completed job includes its generated-image identifier. A failed job includes a safe stable failure code and only reports returned credits after the ledger confirms release.

### Gallery and generated images

- `GET /images`
- `GET /images/{imageId}`
- `GET /images/{imageId}/content`
- `GET /images/{imageId}/download`
- `DELETE /images/{imageId}`
- `POST /images/{imageId}/regenerations`

Gallery responses may include small authorized thumbnail URLs, portrait display thumbnails, style, orientation, quality, creation time and share status. They do not expose raw prompts, lyrics, source-provider credentials or private storage paths.

### Sharing

- `POST /images/{imageId}/email-shares`
- `DELETE /images/{imageId}/email-shares/{shareId}`
- `POST /images/{imageId}/link-share`
- `DELETE /images/{imageId}/link-share`
- `POST /images/{imageId}/link-share/replace`
- `GET /shared/images/{shareToken}`
- `GET /shared/images/{shareToken}/download`

The shared routes authorize only the single generated image represented by the token. Revocation, replacement, image deletion and account deletion take effect immediately.

## Owner/admin resources

Owner endpoints require an owner role, recent password authentication and a valid second factor.

- invitations: create, list, revoke and resend;
- users: list, inspect access state, restrict and restore;
- complimentary access: grant and revoke;
- credits: inspect and append reasoned adjustments;
- styles: create, version, preview, reorder, activate and deactivate;
- jobs and attempts: inspect state, failure class, routing and cost;
- provider routing and spend limits: view and update approved configuration;
- audit: cursor-paginated read-only history;
- source-portrait support access: reasoned, time-limited access with an audit event.

There is no user-impersonation endpoint.

## Idempotency and concurrency

- Require an `Idempotency-Key` header for Checkout creation, generation submission, email sharing, credit adjustments and other money or side-effect operations.
- Scope each key to the authenticated actor and operation.
- Store the request hash and completed response. Reusing a key with different input returns a conflict.
- Hold idempotency records long enough to cover client retries and delayed webhooks.
- Generation submission atomically validates membership, validates the draft, reserves credits and creates one job.
- Stripe event identifiers are unique in the webhook ledger. Replayed events return success without granting credits twice.
- SQLite transactions remain short. Provider calls and file processing never occur inside a database transaction.
- Job claiming is atomic and records worker ownership and a lease so abandoned work can be recovered without duplicate delivery.

## Authorization matrix

- A normal user can access only their own account, drafts, lookups, portraits, jobs, images, credits and shares.
- A share token grants access only to its one generated image and allowed download operation.
- A complimentary reviewer receives creation access through a server entitlement, never through a fake Stripe payment.
- Grace and inactive accounts follow the approved read-only rules. They cannot submit new lookups that incur cost or create generation jobs.
- Restricted accounts can sign in only to the extent needed for account, privacy, billing and support actions defined by the restriction reason.
- Owners use explicit owner endpoints. Owner status does not bypass audit, recent-authentication or portrait-access-reason requirements.

## Stable error categories

At minimum:

- `authentication_required`
- `recent_authentication_required`
- `permission_denied`
- `account_inactive`
- `account_restricted`
- `validation_failed`
- `rate_limited`
- `resource_not_found`
- `resource_conflict`
- `song_not_found`
- `portrait_invalid`
- `portrait_limit_reached`
- `membership_required`
- `insufficient_credits`
- `checkout_not_completed`
- `job_already_submitted`
- `provider_temporarily_unavailable`
- `generation_failed_refunded`
- `share_expired`
- `share_revoked`
- `service_spend_paused`

Provider names, raw exceptions, SQL details, private paths and secrets never appear in client errors.

## Data snapshots and provenance

- A submitted generation stores an immutable snapshot of entered artist and title, safe lookup status, derived Song DNA reference, portrait identifiers, style version, quality, orientation, the `noTextInImage` value, Special instructions, prompt/engine version and credit price.
- Each provider attempt stores provider, model, adapter version, timing, outcome, safe error class and estimated or actual cost.
- Raw lyrics are not stored in the job snapshot, queue, database, file system, prompt history, logs, analytics, error reports or backups.
- Prompt and style edits create new versions. Historical jobs continue to reference the versions used at submission.
- Deleting portraits or an account removes recoverable portrait files even when historical provenance remains.

## File and privacy enforcement

- Local private storage follows [[../05 Product Design/Implementation Readiness Contract]].
- File content routes authorize every request and do not reveal physical paths.
- Share content routes use active token records rather than converting files to public objects.
- Uploaded metadata is stripped before persistent storage.
- Temporary files expire within 24 hours or sooner.
- Provider configurations must prohibit customer-image training.
- Logs redact tokens, cookies, authorization headers, passwords, full payment data, lyrics and private file content.

## Deletion execution

- Individual image deletion atomically revokes shares, removes the live database resource and queues verified file/provider cleanup.
- Account deletion first records a minimal deletion operation, then invalidates sessions, cancels renewal, revokes shares, removes live account data and runs file/provider cleanup.
- Late provider results for a deleted account are discarded and deleted.
- Cleanup operations are safely retryable and expose internal completion status to owners without restoring deleted content.
- During the Hostinger shared-hosting beta, Hostinger-managed encrypted backups expire deleted data within 45 days and reapply deletion records on restoration.
- Application-managed daily media backups remain deferred until Backblaze B2 under the approved storage decision.

## Abuse and cost enforcement

- Apply the approved authentication, song-lookup, email-share, concurrent-job and provider-spend limits on the server.
- Cache only legally and contractually permitted derived lookup outcomes. Do not use caching as a reason to retain raw lyrics.
- A service-wide spend pause blocks new paid work but does not block sign-in, deletion, billing management or access to existing images.
- Limit changes are configuration updates with owner audit events.

## Contract verification required during Build 1

- Publish an OpenAPI description matching this contract before client implementation depends on it.
- Validate example requests and responses for every public resource.
- Add contract tests for web and future Flutter authentication behavior.
- Test cross-account authorization for every user-owned resource.
- Test replay of Checkout, webhook, credit and generation operations.
- Test session revocation, link expiry, share revocation and account deletion.
- Test that logs and client errors contain no lyrics, secrets, provider payloads or private paths.
- Test local-media access and the deferred-backup rule.
