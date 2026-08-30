---
type: implementation-note
status: active
updated: 2026-08-29
area: build-1
---

# Private Development Build 1 — Local Implementation Note

## What is truly working

A private local web vertical slice on PHP + SQLite with:

- invite-only activation and passwordless/password sign-in;
- The Listening Room web UI with launch artwork, welcome, create, gallery, account and owner;
- versioned `/api/v1` resources for web and later Flutter clients;
- append-only credit ledger with reserve/capture/release;
- bounded generation worker, adapter factory, deterministic development adapters and bounded image retry;
- gallery download, delete, revocable link share and logged email share;
- regeneration that prepopulates an editable draft from the prior job snapshot;
- account profile edit, password reset/removal, email-change verification, sessions, logout-all, deletion preview/confirmation;
- mobile access/refresh token issue, rotation and reuse rejection;
- owner TOTP setup/confirm endpoints and recovery codes;
- Stripe test Checkout/portal/webhook code paths that activate only when real test credentials are present and signatures verify;
- Hostinger-compatible SMTP transport when `MAIL_TRANSPORT=smtp` and `MAIL_PASSWORD` are set; otherwise honest log transport;
- security headers, APP_KEY signing/encryption helpers and redacted mail/provider errors.

## Commands

```bash
cp .env.example .env   # set APP_KEY, OWNER_EMAIL, OWNER_PASSWORD
php bin/console.php migrate
php bin/console.php seed-styles
php bin/console.php seed-owner
php -S 127.0.0.1:8080 -t public public/router.php
php worker/run.php
php tests/run.php
```

## Automated test result

`php tests/run.php` → **59 passed, 0 failed** on 2026-08-29 (correction pass).

Added/strengthened coverage includes valid/invalid Stripe signatures, honest adapter status, no-text deterministic output, regeneration prepopulation, account deletion, mobile token rotation, logout-all and mail token redaction.

## Development substitutes vs real integrations

| Concern | Active without credentials | Becomes real when |
|---|---|---|
| Creative analysis | `deterministic-development` | `AI_PROVIDERS_ENABLED=true` and Groq/Gemini key present; live calls still require `GROQ_LIVE_CALLS` / `GEMINI_LIVE_CALLS` |
| Image generation | `deterministic-development-image` | fal key + `AI_PROVIDERS_ENABLED=true` + `FAL_LIVE_CALLS=true` |
| Mail | `log` → `var/log/mail.log` with token redaction | `MAIL_TRANSPORT=smtp` and `MAIL_PASSWORD` |
| Stripe Checkout / portal / webhooks | `stripe-unavailable` | test secret (`sk_test_…`), price id, webhook secret (`whsec_…`) |
| Membership without Stripe | development-only `POST /api/v1/billing/dev-activate` | never a live payment |

Owner setup status reports the adapter actually selected, not an aspirational label.

## Credentials still needed

Install only in local `.env` / protected hosting config:

- `STRIPE_SECRET_KEY` (`sk_test_…`), `STRIPE_PUBLISHABLE_KEY`, `STRIPE_WEBHOOK_SECRET` (`whsec_…`), `STRIPE_PRICE_ID`
- `MAIL_PASSWORD` (and keep Hostinger SMTP host/port/SSL)
- Optional: `GROQ_API_KEY`, `GEMINI_API_KEY`, `FAL_KEY`, plus explicit `*_LIVE_CALLS=true` only when intentionally spending budget

## Remaining blockers / not claimed

- External beta, public registration, live payments and commercial protected-lyrics use remain disabled.
- Owner 2FA is implemented but optional until enrolled; sensitive owner ops should be exercised after Authy enrollment.
- Licensed self-hosted WOFF2 files are not yet in the repo; the app uses a strong local serif/sans stack.
- Live adapters are implemented but remain independently opt-in to protect the development budget. Gemini is the default creative-analysis provider, Groq is the structured-output fallback, and fal.ai performs portrait-guided image generation. The deterministic adapters remain the development path when live AI is disabled; they are not used as a paid-generation fallback unless explicitly enabled.
- Provider requests contain no stored lyrics. Gemini and Groq receive only the submitted song and performer identifiers plus copyright-safety instructions. fal.ai receives the sanitized provider-neutral visual package and one or two private portrait data URIs in memory; portrait bytes and provider response bodies are never logged.
- Final Terms/Privacy legal language and provider/quality benchmarks remain launch gates.

## Explicitly not claimed

This local Build 1 is **not** external-beta ready.
