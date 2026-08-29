---
type: implementation-readiness-contract
status: owner-approved
updated: 2026-08-28
area: platform-contracts
owners:
  - CuBiX Meow
  - Brut
approved: 2026-08-28
source: Implementation Readiness Workshop
---

# Implementation Readiness Contract

CuBiX Meow and Brut have each selected a separate private owner-login email. The addresses are deployment secrets and must not be committed to the repository, exposed in the client or published in project documentation.

Both owners have Authy available for separate two-factor enrollment. Authenticator secrets, phone numbers and recovery codes must remain outside the repository and application logs.

## Status

CuBiX Meow and Brut approved this contract and its technical translation on 2026-08-28. The API, authentication, privacy, deletion, sharing, abuse-protection, owner-audit and initial local-storage behavior is implementation-ready for Private Development Build 1. External access and commercial launch remain gated.

The approved technical translation is in [[../07 Development/Shared API, Security and Data Contract]].

## Approved invitation control

- Only CuBiX Meow and Brut can invite beta users through the owner/admin area.
- Every invitation is classified as either `Paid beta tester` or `Complimentary reviewer`.
- The invitation email contains a single-use activation link.
- An unused invitation expires seven days after issuance.
- Owners can revoke an unused invitation.
- Owners can resend an invitation. Resending invalidates the earlier unused link and issues a new seven-day link.
- A used, revoked or expired link cannot activate or sign in to an account.
- Invitation and activation attempts must be recorded for owner support and security review without storing the raw secret link token.

## Approved account activation and optional passwords

- Opening a valid invitation link begins activation.
- The invited user enters or confirms a display name and accepts the Terms of Service and Privacy Policy.
- Successful use of the private invitation link verifies the invited email address.
- The account activates and the user is signed in after the required activation information and consent are complete.
- Creating a password is optional and skippable.
- A user can create or change a password later in account settings.
- Passwordless email sign-in remains available after a password is created.
- Forgotten-password recovery sends a single-use reset link that expires after one hour.
- Using, replacing or expiring an activation or password-reset link invalidates that link without storing its raw secret token.

## Approved sessions and sensitive-action confirmation

- A browser session remains valid for up to 30 days after the user's most recent authenticated activity.
- Signing out invalidates the current browser session immediately.
- Account settings provide `Sign out of all devices`, which invalidates every active session for that account.
- Changing the account email, changing the password, permanently deleting the account or opening sensitive billing controls requires recent identity confirmation.
- A password user can confirm with the current password.
- A passwordless user confirms through a single-use email link.
- A recent confirmation may satisfy multiple sensitive actions for a short, implementation-defined window, but permanent deletion must always include its own explicit irreversible-action confirmation.

## Approved authentication abuse protection

- Require at least 60 seconds before sending another sign-in or password-reset email for the same address.
- Send no more than five authentication emails to one address in one hour.
- Apply an additional network-address limit so one source cannot target many accounts. The exact threshold may be tuned from beta traffic without weakening the per-address rule.
- After five incorrect password attempts within 15 minutes, pause further password attempts for that account and source for 15 minutes.
- Use neutral responses that do not reveal whether an email address belongs to an invited account.
- Introduce a human-verification challenge only for suspicious behavior rather than adding it to every normal sign-in.
- Require an authenticated, active invited account before song lookup, creative analysis or image generation can incur provider costs.
- Record bounded security events for owner review while avoiding raw passwords, raw link tokens and unnecessary personal data.

## Approved shared web and Flutter service contract

- The PHP HTTP/JSON API is the shared service for the web application and later Flutter client from the first build.
- The server is authoritative for accounts, roles, consent, subscriptions, credits, portraits, song processing, creative artifacts, generation jobs, gallery items, deletion and sharing.
- Web and Flutter clients receive the same resource state and follow the same business rules.
- Clients never call Groq, fal.ai, Stripe, email or file-storage providers with private credentials. Provider access occurs only on the server.
- Every state-changing request that could charge money, reserve credits or create a job supports safe replay so repeated taps and interrupted connections cannot duplicate the operation.
- Generation jobs continue independently of the requesting client. Either authorized client can retrieve the current state and completed result.
- API failures use stable categories that clients can map to the approved user-facing recovery messages without exposing provider internals.
- The API is explicitly versioned from the first build. Compatible improvements may be added without silently changing existing field meaning or breaking the later mobile client.
- Server-side checks enforce authorization and ownership on every request. The client interface is never trusted as the security boundary.

## Approved portrait, image and provider privacy boundaries

- Saved portraits and generated images are private to the account unless the user deliberately shares a generated image.
- Source portraits never appear in public sharing links.
- Files use authenticated access or short-lived authorized delivery rather than permanent public addresses.
- Remove location, camera and other unnecessary embedded metadata during portrait upload.
- A provider receives only the files and derived information needed for the specific processing job.
- Use established commercial API providers under their standard business/API privacy terms for the first build.
- Provider settings and agreements must prohibit using customer portraits, image inputs and generated images for model training. A provider that cannot meet this requirement is not eligible for development involving customer material or for the external or paid beta.
- Verify and record each selected provider's current training, retention, deletion and subprocessors policy during the provider gate.
- The product does not require a custom enterprise privacy agreement for initial development, but no provider may be used for paid or external beta processing until its documented terms fit the approved disclosures and risk level.
- Disclose relevant third-party processing and maintain an accurate provider/subprocessor list.
- Beta terms state that CuBiX Meow and Brut may review generated images and related technical records for quality testing, safety and support.
- Source portraits are not routinely browsable in admin. Access requires a specific support or safety reason and creates an audit record.
- Deleting a generated image immediately removes user and share-link access to it. Physical and provider cleanup follows the approved deletion workflow.

## Approved deletion workflow

### Individual portrait or image

- Deleting a portrait or generated image removes it immediately from the live service.
- Every share link and pending email-share access tied to a deleted image stops immediately.
- A portrait that is referenced by historical generation provenance is removed as a file and from the user's library. Historical records retain no recoverable portrait copy.

### Permanent account deletion

- Require recent identity confirmation and a final explicit warning that deletion is immediate, permanent and cannot be recovered.
- On confirmation, invalidate every session and sign the user out.
- Cancel Stripe renewal immediately and end application access under the approved subscription contract.
- Remove the account, profile, portraits, generated images, gallery records, share access, creative artifacts and unused credits from live application systems.
- Stop active processing where the provider supports cancellation. Any result that arrives after deletion is not attached to an account and must be discarded and deleted.
- Send deletion requests to outside providers when they retain job files and offer deletion controls.
- Retain only payment, tax, fraud or accounting records that Stripe, the business or applicable law requires, and disclose this exception.

### Backups and completion

- During the Hostinger shared-hosting beta, Hostinger-managed encrypted disaster-recovery backups may retain deleted data for no more than 45 days before automatic expiration. A larger deployment must define and disclose its own owner-approved retention period before launch.
- Backup data is unavailable to users, owners/admins and normal application operations.
- A disaster restore must reapply completed deletion records before restored data becomes active, so a deleted account is not resurrected.
- The user-facing deletion is immediate when live access is removed. Internal deletion status tracks local file removal, provider requests and backup-expiration completion without retaining unnecessary deleted content.

## Approved sharing security

### Email one person

- One email-sharing action accepts exactly one recipient address and sends one message.
- The recipient receives a private generated-image link that expires seven days after issuance.
- The recipient can view and download that generated image without creating an account while access remains valid.
- The image owner can stop access before expiration.
- Sending again requires a new deliberate user action and a new access token.
- Apply per-account and network-address limits to prevent email spam and provider reputation damage.

### Create a share link

- A link remains active until the image owner selects `Stop sharing`, replaces the link, deletes the image or deletes the account.
- Creating a replacement link immediately invalidates the previous link.
- The shared page shows only the generated image, minimal product context and simple download controls.
- It does not expose source portraits, account details, prompts, credits, private gallery information or other images.
- Shared pages are excluded from search-engine indexing.
- Every email and persistent share token is long, random, unguessable, stored safely and checked server-side.
- Revocation is immediate and does not depend on an old page or browser cache expiring.

## Approved lookup and generation abuse protection

- Only authenticated active accounts can trigger provider work.
- Allow one active song lookup per account.
- Initial account limits are no more than 10 song lookups in 10 minutes and 50 in one day.
- Reuse a safe cached lookup outcome for the same normalized song and artist when permitted, rather than paying a provider again.
- One lookup attempt can call no more than the approved two or three providers and must stop as soon as sufficient reliable information is available.
- Repeated invalid or meaningless searches trigger progressively longer temporary pauses.
- Allow one active image generation per account initially.
- Verify and reserve sufficient credits before calling an image provider.
- Every generation has a strict configured automatic-retry ceiling. A retry cannot recurse or create unbounded provider work.
- Enforce adjustable account-level and service-wide provider-spending limits that can pause new paid work before costs exceed the approved budget.
- Alert owners as spend approaches configured thresholds and show current lookup, generation, retry and provider costs in admin.
- Paid and complimentary accounts use the same safety controls unless an owner records a deliberate temporary override.
- A legitimate user who reaches a limit sees a friendly retry-time message and a support path.
- Owners can tune numeric limits after reviewing real beta usage and provider economics without removing the architectural ceilings.

## Approved initial local-storage contract

- Store portraits, generated images, thumbnails and temporary processing files in separate private areas outside the publicly accessible website directory.
- Use random internal identifiers and file names. Do not expose original file names, email addresses or account details in file paths or delivery addresses.
- Deliver a private file only after server-side account-ownership authorization or validation of an active share token.
- Decode and validate uploads as supported images, enforce configured file-size and dimension limits, remove unnecessary metadata and reject malformed or unsafe files.
- Require encrypted hosting storage and encrypted transport for every upload and download.
- Remove temporary processing files automatically within 24 hours unless a shorter provider or deletion rule applies.
- Put file operations behind one replaceable storage adapter so moving from local hosting to Backblaze B2 does not change the web or Flutter client contract.

### Backup staging decision

- Do not create application-managed daily backups of local portrait and generated-image files before Backblaze B2 integration. Hostinger storage is limited and must not be consumed by duplicate media.
- Keep only live local media during this initial stage, subject to deletion and temporary-file cleanup rules.
- Small encrypted database backups may be maintained separately when they fit the hosting allowance and do not duplicate media files.
- Implement durable media backup and restoration with or after Backblaze B2 integration.
- Any later application-managed backup process must use the owner-approved deleted-data retention period and must reapply deletion records during restoration.

## Approved owner/admin permissions and audit history

- CuBiX Meow and Brut each use a separate named owner account with equal full owner access.
- Owner accounts require a password plus a second verification step. Shared owner credentials are prohibited.
- Owners can manage invitations, users, paid or complimentary access, credits, styles, jobs, provider routing, failures, costs and spending limits.
- Owners may review generated images for disclosed beta quality testing, safety and support.
- Source-portrait access requires a specific recorded support or safety reason.
- User impersonation is not included in the first build.
- Record security-sensitive owner actions with the acting owner, time, action, affected account or resource and reason when required.
- Credit changes, account restrictions, source-portrait access and manual deletion assistance require a written reason.
- Retain the owner audit history for one year.
- The normal admin interface cannot edit or delete audit events.
- Audit records must not contain passwords, raw authentication tokens, full payment details or unnecessary portrait data.

## Approval

Approved by CuBiX Meow and Brut on 2026-08-28. Later changes to these contracts require a recorded owner decision and synchronized updates to both documents.
