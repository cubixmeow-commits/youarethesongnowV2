---
type: implementation-note
status: active
updated: 2026-08-30
area: build-1
---

# Private Development Build 1 — Local Implementation Note

## End-to-end pipeline (current)

```text
song selection
  → Gemini Song DNA (Google Search, gemini-3.6-flash)  [transient lyrics/analysis only]
  → saved derived Song DNA (never raw lyrics)
  → locked generation snapshot
  → selected portrait(s)
  → Gemini native multimodal image (gemini-3.1-flash-image)
  → gallery image
```

**Defining product rule:** the uploaded person or people must be recognizable central subjects. An image without the uploaded subject is not usable.

Worker reuses the exact saved derived Song DNA and does **not** repeat Gemini search or regenerate DNA in the image adapter.

## What is truly working

A private local web vertical slice on PHP + SQLite with invite-only auth, Listening Room UI, `/api/v1`, credit ledger, worker, gallery/sharing, regeneration, account/mobile/owner flows, Stripe test paths, SMTP/log mail, and security helpers.

## Song DNA (Gemini text)

- Model: `gemini-3.6-flash` via **Interactions API** (`/v1beta/interactions`) with Google Search + strict JSON Schema.
- **`store=false` mandatory** so lyrics/search material are never retained as Gemini interaction state.
- Legacy `generateContent` could not combine Search with JSON MIME; that caused incomplete Song DNA (e.g. live `Dancing in the Dark` / Bruce Springsteen). V1’s JSON-repair call is historical only, not the primary V2 path.
- Lyrics are searched/analyzed **transiently** for private development only. Never saved to Git, SQLite, queues, logs, prompts, analytics, or backups.
- V1-compatible acceptance labels: lyrics confirmed / song-context / V1-compatible analysis after semantic validation.
- Derived DNA shown at selection is locked into the generation snapshot.

## Image provider status

| Provider | Status |
|---|---|
| **Gemini `gemini-3.1-flash-image`** | **Selected portrait path.** One `generateContent` call with text instructions + each portrait as `inlineData`. Uses persisted Song DNA, curated style, aspect, special instructions, no-text. 1K development size. `IMAGE_PROVIDER=gemini` or `auto`. |
| Replicate `prunaai/p-image-edit` | Strong Song DNA scenes but **omitted/minimized** uploaded people. Not suitable for the defining portrait workflow. Retained for later benchmarking only. |
| fal.ai `fal-ai/flux-pro/kontext/multi` | Strong Song DNA scenes but **omitted people entirely**. Not suitable for the defining portrait workflow. Retained for later benchmarking only. |

Print, poster, T-shirt, and upscaling remain post-Build-1 work.

## Commands

```bash
cp .env.example .env   # set APP_KEY, OWNER_EMAIL, OWNER_PASSWORD
php bin/console.php migrate
php bin/console.php seed-styles
php bin/console.php seed-owner
php -S 127.0.0.1:8080 -t public public/router.php
php worker/run.php
php tests/run.php
php bin/console.php setup-status
```

## Automated test result

`php tests/run.php` → **146 passed, 0 failed** after Interactions API Song DNA path.

## Development substitutes vs real integrations

| Concern | Active without credentials | Becomes real when |
|---|---|---|
| Creative analysis | `deterministic-development` | `AI_PROVIDERS_ENABLED=true` + Gemini/Groq key + `GEMINI_LIVE_CALLS` / `GROQ_LIVE_CALLS` |
| Image generation | `deterministic-development-image` | Gemini key + `AI_PROVIDERS_ENABLED=true` + `GEMINI_IMAGE_LIVE_CALLS=true` (`IMAGE_PROVIDER=gemini` or `auto`) |
| Experimental image | — | `IMAGE_PROVIDER=fal` or `replicate` with matching `*_LIVE_CALLS` |
| Mail / Stripe | log / unavailable | SMTP password / Stripe test credentials |

## Remaining blockers / not claimed

- External beta, live payments, commercial protected-lyrics use remain disabled.
- Live Gemini image identity acceptance still requires a Hostinger/private live test.
- Gallery upscaling / print merchandise deferred.
- fal and Replicate kept as optional experimental providers only.
