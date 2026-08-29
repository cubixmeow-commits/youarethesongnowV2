# Cursor prompt: complete and polish Private Development Build 1

Continue the existing uncommitted work on branch `build/private-development-build-1` in this repository. Preserve all current work. Do not edit V1, do not commit, do not push and do not enable external beta or live payments. Run the existing test suite before editing, then work through this entire prompt in ordered phases. Read the contracts in `development-vault/05 Product Design` and `development-vault/07 Development` first, including `Build 1 Artwork and Layout Graphics.md`.

The goal is an honest, functional private development build that can be tested locally and later configured with test credentials. Never claim that an integration works merely because settings exist. A setup screen must report the adapter actually running.

## Phase 1: fix false or unsafe integration behavior

1. Stripe test mode
   - Replace the placeholder Checkout implementation with Stripe-hosted Checkout in test mode.
   - Add Customer Portal support if the current feature contract calls for it.
   - Verify webhook signatures cryptographically with the Stripe webhook secret. Reject missing, malformed, expired and invalid signatures.
   - Process Stripe events idempotently and map subscriptions to the correct account and credit grant.
   - Keep the local development membership route gated to development only.
   - Never use live mode, persist unnecessary raw webhook payloads or expose Stripe secrets.
   - Replace the current fake signature test with real valid-signature and invalid-signature tests.

2. AI provider adapters
   - Add clean provider interfaces and a factory for the creative-analysis and image-generation jobs.
   - Add protected test adapters for the configured Groq, Gemini and fal.ai providers where they fit the job. Use deterministic adapters only when provider access is disabled or unavailable.
   - The owner setup screen must state the exact adapter in use. Do not report `configured-real-adapters` or `fal-ready` unless that adapter exists, is selected and passes its configuration check.
   - Keep raw lyrics memory-only. Never write them to SQLite, files, job payloads, exception text, logs or Git.
   - Add a bounded retry to a comparable image provider for technical, provider or safety failures. Preserve uploaded portraits. Capture credits only for a usable result and release them after final failure.
   - Do not expose provider or model names in the customer interface.

3. SMTP
   - Implement authenticated SMTP suitable for Hostinger using environment values. The intended settings are `smtp.hostinger.com`, port `465`, SSL, and sender `support@youarethesongnow.com`.
   - Keep the password in `.env` only. Keep log mail as the local default and make transport status honest in owner setup.
   - Cover activation, magic link, verified email change, password reset and share email without logging tokens.

4. Secret and log handling
   - Use `APP_KEY` for an appropriate server-side signing or encryption purpose, or remove the misleading requirement and document the actual security design.
   - Normalize and redact provider errors. Never log raw lyrics, portraits, prompts containing personal information, access tokens, magic links, webhook secrets, API keys or complete provider payloads.

## Phase 2: complete the approved account and API contract

- Add editable profile fields.
- Require verification before an email change becomes active.
- Add password reset, password removal, session list, logout all and recent-auth protection for sensitive changes.
- Add immediate account deletion with a clear preview and confirmation. Delete live database records, local files, portraits, generated images, shares, sessions, credits and creative artifacts. Cancel Stripe renewal when configured and safely ignore or clean up late provider results.
- Implement owner two-factor authentication using standard TOTP plus one-time recovery codes. Protect sensitive owner operations with 2FA and recent authentication.
- Add opaque mobile access and refresh tokens, rotation and revocation according to the shared API contract. Do not use browser cookies as the future Flutter token design.
- Finish the documented `/api/v1` endpoints with consistent JSON errors that do not leak internals.
- Enforce cross-account authorization on every account resource and real owner authorization on every owner route.

## Phase 3: finish the creation workflow

- Make `Create another` and regeneration create an editable draft populated from the previous creation: song lookup or safe song reference, saved portraits, style version, quality, orientation, no-text preference and special instructions.
- Keep a submitted generation locked. Do not add cancel or mid-run editing.
- Correct portrait EXIF orientation, strip metadata and resize/compress uploads adaptively toward the approved storage target while preserving useful quality.
- Make the deterministic image adapter honor `No text in image` completely. When no-text is selected, it must render no development label, adapter label, style text or other readable text.
- Add daily and per-IP lookup limits in addition to the rolling limit. Add enforceable monthly provider-spend and disk-space gates with clear owner visibility.
- Preserve the approved credit lifecycle: reserve at submit, capture only on usable delivery, release after final technical failure.

## Phase 4: integrate the premium visual system

Use the assets and exact guidance in `development-vault/05 Product Design/Build 1 Artwork and Layout Graphics.md`.

- Replace the welcome-page placeholder silhouettes with the wide hero artwork.
- Make the first viewport feel like a full-bleed poster. Keep this approved copy:

  `You Are The Song Now`

  `A meaningful song becomes a cinematic world, with you and the people you love at the heart of its story.`

- Keep one primary action and place the copy in the hero's calm left field with a reliable contrast gradient.
- Use the three narrative examples below the fold with these broad labels, not use-case categories:
  - `Two people, one shared world`
  - `A song seen from the inside`
  - `Another mood, another universe`
- Remove `Wedding or celebration` and any wedding-specific framing. A wedding was only one possible example, not part of the core product description.
- Use the wide interlude, groove field and vertical mobile graphics sparingly to give onboarding, paywall, generation and empty states a cohesive premium atmosphere.
- Keep the design cardless, matte, tactile and editorial. Avoid neon, glass effects, generic AI gradients, decorative dashboard grids and repeated boxed panels.
- Fix the known 390-pixel mobile horizontal overflow caused by the hero and hero stage. Ensure the header does not awkwardly wrap and controls respect safe margins.
- Use responsive images with explicit dimensions, deliberate crops, hero preload/high fetch priority and lazy loading below the fold.
- Remove the external Google Fonts request. Self-host properly licensed WOFF2 files if they are already available, or use a strong local serif and sans stack until licensed files are added.
- Verify WCAG 2.2 AA contrast, keyboard behavior, visible focus, semantic structure, touch targets and reduced-motion behavior.
- Do not use em dashes in product copy.

## Phase 5: strengthen verification

Keep all existing tests passing and add meaningful integration coverage for:

- valid and invalid Stripe webhook signatures and event idempotency;
- CSRF, unauthenticated access, cross-account access and owner authorization through actual HTTP routes;
- password reset, verified email change, logout all, 2FA recovery and account deletion;
- regeneration prepopulation;
- controlled provider retry and correct credit release;
- no readable text in deterministic no-text output;
- lyric, token, secret and provider-payload non-persistence;
- daily/IP lookup limits, monthly spend gate and disk gate;
- portrait orientation and adaptive processing;
- safe, non-leaking JSON errors.

Also add central production security headers: CSP appropriate to the actual assets, `X-Content-Type-Options`, clickjacking protection, `Referrer-Policy`, a restrained `Permissions-Policy`, and HSTS only when HTTPS production is active. Hide the PHP version header where the deployment permits it.

## Definition of done for this pass

- The full test suite and PHP syntax checks pass.
- The main welcome, sign-in, onboarding, creation, generation, reveal, gallery, account and owner views are manually checked at desktop and 390-pixel mobile widths.
- There is no horizontal overflow.
- The setup screen reports real runtime state rather than intended configuration.
- Test credentials are read only from local environment variables. If a credential is missing, finish everything that does not require it and provide a short exact list of values the owner must install. Do not ask the owner to paste secrets into chat or commit them.
- Update the local implementation note with what is truly working, what remains a deterministic substitute and the exact test count.
- End with a concise report of files changed, tests run, screenshots checked, credentials still needed and any remaining blockers. Do not commit or push.
