# Cursor Build 1 Handoff

## Authorization

CuBiX Meow and Brut authorized Private Development Build 1 on 2026-08-28.

Implement a runnable, tested private web application in this repository. Do not stop after producing a plan, wireframe or empty scaffold. Work in small vertical slices, run the application and tests, inspect the interface in a browser and leave the repository in a coherent state.

External beta access, public registration, live Stripe charging and commercial processing of protected lyrics remain disabled.

## Required reading order

1. `AGENTS.md`
2. `development-vault/START HERE.md`
3. `development-vault/01 Current Project/Current Priorities.md`
4. `development-vault/01 Current Project/Current Architecture.md`
5. `development-vault/05 Product Design/First Build Feature Contract.md`
6. `development-vault/05 Product Design/V2 Visual Design Direction.md`
7. `development-vault/05 Product Design/Onboarding and First-Creation Paywall Contract.md`
8. `development-vault/05 Product Design/V2 Song DNA and Prompt Pipeline Contract.md`
9. `development-vault/07 Development/Shared API, Security and Data Contract.md`
10. `development-vault/07 Development/Deployment and Operating Cost Contract.md`
11. `development-vault/07 Development/Acceptance Test Contract.md`

The vault is the product source of truth. V1 is behavioral and visual evidence only. Do not port its PHP architecture or copy its implementation wholesale.

## Build 1 outcome

Deliver a private local web build that demonstrates the complete product shape:

1. Owner-created invitation and activation.
2. Passwordless sign-in with optional password support.
3. Terms/privacy acceptance and first-creation onboarding.
4. One creation page containing song entry, one or two portraits and image direction.
5. Style, quality, orientation, no-text and Special instructions controls.
6. Server-side creation draft, quote and immutable job submission.
7. Asynchronous worker processing with honest queued, generating, completed and failed states.
8. Configurable provider adapters with safe local development substitutes when credentials are absent.
9. Credit reservation, capture and release through an append-only ledger.
10. Gallery, full image view, download, regeneration, deletion and revocable link sharing.
11. A deliberately small owner area for invitations, users, styles, jobs, costs and credits.
12. A versioned `/api/v1` surface that the web client uses and a later Flutter client can consume.

Build the real domain boundaries even when an external service uses a development substitute. Never fake a successful live payment, provider call or email delivery.

## Technical constraints

- PHP backend compatible with the current Hostinger shared-hosting target.
- SQLite with versioned migrations, foreign keys, WAL where supported and short transactions.
- Plain HTML, CSS and JavaScript where practical.
- No required Node production runtime.
- Private files and SQLite data must live outside the public document root in production.
- Web sessions use Secure, HttpOnly cookies in HTTPS environments and CSRF protection for state changes.
- All identifiers exposed through the API are opaque.
- Core business, credit, billing, privacy and authorization rules live on the server.
- Provider, mail, billing and storage integrations sit behind replaceable adapters.
- The browser never receives Stripe secrets, SMTP credentials, AI keys, private prompts or storage paths.
- Every user-owned resource is authorized on every request.
- Side-effecting and credit-changing operations are idempotent.
- Generation provider calls never run inside a database transaction.
- Runtime SQLite databases, media, logs and `.env` files remain ignored by Git.

Prefer a small dependency set and straightforward code over a large framework unless repository evidence shows that a framework materially improves security and Hostinger compatibility. Record any material architectural choice in the vault.

## Creative-engine boundary

Implement the approved stages and schemas as versioned server-side contracts:

```text
Song information
  -> Song DNA
  -> originality and leakage sanitizer
  -> visual narrative plan
  -> portrait integration plan
  -> StyleMap
  -> provider-specific prompt compiler
  -> image generation
  -> output evaluation
  -> controlled retry or final failure
```

Raw lyrics are memory-only. They must never enter Git, SQLite, queues, temporary files, stored prompt histories, logs, analytics, exception reports or backups. Persist only safe provenance and non-reconstructive derived creative artifacts allowed by the approved contract.

Use deterministic development adapters and fixtures when real credentials are absent. A development adapter must identify itself honestly in owner diagnostics and must never masquerade as a real provider result. Real provider adapters remain disabled until explicitly enabled through protected environment configuration.

## Visual direction

Build **The Listening Room**:

- visual thesis: a modern record sleeve meets a cinematic photo book;
- full-bleed artwork and a poster-like first viewport;
- matte near-black background, warm paper text and one warm stage-light accent;
- expressive editorial serif for major moments and one clean sans serif for interface text;
- creation flow organized as `The song`, `The people` and `The direction`;
- gallery composed like a record collection or photographer's contact sheet;
- subtle cover reveal, track advance and honest playhead progress;
- default to cardless composition using alignment, spacing, scale and imagery;
- no futuristic AI typography, cyan-purple gradients, glass panels, glowing borders, emojis or AI-console language;
- no provider or model names in customer-facing screens;
- no decorative music-note, vinyl, headphone, waveform or equalizer clutter;
- do not use em dashes in product copy.

Use provisional local imagery only when rights and privacy are clear. Do not copy private V1 portraits or generated images into Git without explicit approval.

## Responsive and accessibility requirements

- Complete keyboard operation with visible focus.
- Visible labels and announced form errors.
- Useful live status for lookup, upload, generation, payment and credit changes.
- State never depends on color or motion alone.
- WCAG 2.2 Level AA contrast target.
- Usable at 320 CSS pixels and 200 percent zoom.
- Mobile inputs at least 16 CSS pixels.
- Touch targets aim for at least 44 by 44 CSS pixels.
- Safe-area-aware mobile actions.
- Honor `prefers-reduced-motion` and increased-contrast preferences.
- Test with automated checks plus manual keyboard behavior.

## Safe development configuration

Start from `.env.example`. Default to:

- external users disabled;
- public registration disabled;
- live payments disabled;
- Stripe test mode;
- AI providers disabled until keys are installed;
- logged email transport until SMTP credentials are installed;
- configurable development credits;
- a hard monthly development AI budget;
- local private storage.

Fail closed when a required credential is missing. Show a clear owner-only setup status without displaying secret values.

## Implementation order

### Slice 1: runnable foundation

- Application entry point, configuration validation and safe error handling.
- SQLite connection, migration runner and initial schema.
- Private storage directories and access protections.
- Health endpoint that reveals no private details.
- Automated test runner and local start instructions.

### Slice 2: owner access and invitations

- Seed the first owner from protected configuration or a one-time local setup command.
- Owner sign-in foundation, two-factor-ready domain design and audit events.
- Invitation creation, activation and local logged-email delivery.
- No public registration route.

### Slice 3: onboarding and creation

- Terms acceptance and approved onboarding copy.
- The song, The people and The direction on one coherent page.
- Safe portrait decoding, normalization, metadata removal and thumbnails.
- Active style catalog and product options from the database.
- Server-side draft validation and summary.

### Slice 4: credits and background generation

- Append-only credit ledger.
- Atomic quote, reservation and immutable job creation.
- One bounded worker with locking, leases and safe retry.
- Deterministic local creative and image adapters first.
- Honest progress and automatic credit release on final technical failure.

### Slice 5: gallery and sharing

- Private gallery and authorized media delivery.
- Full image, download, regeneration and immediate deletion.
- Persistent unguessable link sharing with immediate revocation.
- Logged email-share flow with one recipient and seven-day access.

### Slice 6: Stripe sandbox and real provider readiness

- Stripe-hosted test Checkout and verified idempotent webhooks when test credentials are provided.
- Preserve and resume the prepared first creation.
- Provider adapter configuration, cost records and spend pause.
- Do not enable live mode.

### Slice 7: owner operations and verification

- Users, invitations, styles, jobs, attempts, costs and reasoned credit adjustments.
- No impersonation.
- Automated API, authorization, idempotency, credit, deletion and leakage tests.
- Browser inspection at desktop and mobile widths.
- Setup, worker, test and Hostinger deployment documentation.

## Completion evidence

Before reporting completion:

1. Run all migrations from an empty database.
2. Run automated tests and report exact results.
3. Start the application locally and exercise the primary journey in a browser.
4. Verify an uninvited account cannot register.
5. Verify a normal account cannot access another account's data or owner endpoints.
6. Verify repeated generation submission cannot duplicate a job or credit reservation.
7. Verify final generation failure releases reserved credits.
8. Verify image deletion revokes shares and removes private files.
9. Search tracked source and runtime logs for secrets and raw lyric fixtures.
10. Inspect responsive layouts at 320, 390, 768 and desktop widths.
11. Record incomplete external-service setup honestly.
12. Update the README and relevant vault notes with implemented behavior, commands, tests and remaining blockers.

Do not claim external-beta readiness from a local Build 1. Clearly separate completed code, development substitutes, unconfigured integrations and later launch gates.
