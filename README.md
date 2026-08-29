# YouAreTheSongNow V2

A clean rebuild of the original **AISaga Arcana / YouAreTheSongNow.com** project as a modern web and mobile application.

This repository intentionally starts clean. The legacy repository, `cubixmeow-commits/youarethesongnow`, is a reference for product behavior, data semantics, prompts, integrations, media, and business rules. Legacy implementation code should not be copied into V2 by default.

## Start here

- `CURSOR-BUILD-1.md` — Private Development Build 1 handoff
- `AGENTS.md` — working rules for Cursor and other coding agents
- `development-vault/START HERE.md` — shared working memory / current direction
- `docs/index.html` — **Meow Control**, CuBiX Meow & Brut daily command center (GitHub Pages)

## Current status

**Phase: Private Development Build 1 implemented locally. External beta and commercial launch remain gated.**

Accepted direction:

- rebuild/refine **V1 functionality**, not V1 code;
- backend: **PHP**;
- initial database: **SQLite**;
- web client: **plain HTML/CSS/JavaScript**;
- background generation: **bounded PHP worker with explicit job state**;
- API: versioned `/api/v1` shared with a later Flutter client.

## Local Build 1 quick start

```bash
cp .env.example .env
# Set APP_KEY to a random secret
# Set OWNER_EMAIL and OWNER_PASSWORD for the first owner account

php bin/console.php migrate
php bin/console.php seed-styles
php bin/console.php seed-owner

php -S 127.0.0.1:8080 -t public public/router.php
```

In another terminal:

```bash
php worker/run.php          # one bounded worker tick
php tests/run.php           # automated Build 1 checks
php bin/console.php setup-status
```

Open `http://127.0.0.1:8080`.

### Primary local journey

1. Sign in as the seeded owner (password), or invite a complimentary reviewer from `/owner`.
2. Create on one page: song, portraits, style/quality/orientation/direction.
3. Complimentary and owner accounts skip Stripe; unsubscribed accounts see the $20 paywall.
4. Generation uses deterministic development adapters until real AI credentials are enabled.
5. Gallery supports view, download, regenerate entry, delete, revocable link share and logged email share.

### Gates that remain disabled

- public registration
- external tester access
- live Stripe charges
- commercial processing of protected lyrics
- client-side provider credentials

### Credentials still needed for real integrations

Install only through `.env` or protected hosting config (never Git):

| Integration | Variables | Build 1 behavior without them |
|---|---|---|
| Stripe test Checkout | `STRIPE_SECRET_KEY`, `STRIPE_PUBLISHABLE_KEY`, `STRIPE_WEBHOOK_SECRET`, `STRIPE_PRICE_ID` | Checkout endpoint fails closed; creation draft is preserved; local membership substitute available in development |
| SMTP | `MAIL_PASSWORD` (+ host settings) | Logged email transport writes to `var/log/mail.log` |
| Groq / Gemini / fal.ai | `GROQ_API_KEY`, `GEMINI_API_KEY`, `FAL_KEY` plus `AI_PROVIDERS_ENABLED=true` | Deterministic development adapters identify themselves honestly |

### Hostinger notes

- Document root should point at `public/`.
- Keep `var/` (SQLite, private media, logs) outside the public directory in production.
- Schedule `php /path/to/worker/run.php` every minute via Cron Jobs.

See accepted ADRs in `development-vault/02 Decisions/` and the Build 1 contracts under `development-vault/05 Product Design/` and `development-vault/07 Development/`.
