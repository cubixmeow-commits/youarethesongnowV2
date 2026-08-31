# CURSOR-HANDOFF — Gemini Explore reliability fix

**Date:** 2026-08-31  
**Working branch:** `main`  
**Phase:** First implementation test — **Song DNA Quick Generate + Explore Options** (reliability fix)

## Locked product decisions

**AI should remove decisions by default and offer intelligent choices when the user asks for control.**

Default: `Song → Song DNA → Generate`.  
Optional: `Song → Song DNA → Explore Options → AI-generated visual directions → optional Fine Tune → Generate`.

## Root cause (verified)

Live iPhone failure message:

> AI directions are unavailable right now. You can still choose a direction manually.

That message is returned only for `gemini_unavailable` / `provider_http_401` / `provider_http_403` / `provider_http_404`.

Side-by-side comparison with the **working** Song DNA path (`GeminiLyricsResearchService`):

| Check | Song DNA (working) | Explore (was failing) |
| --- | --- | --- |
| API key | `GEMINI_API_KEY` / `ai.gemini_api_key` | same |
| Gates | `AI_PROVIDERS_ENABLED` + `GEMINI_LIVE_CALLS` | same |
| Model config | `GEMINI_MODEL` → `ai.gemini_model` (live: `gemini-3.6-flash`) | **hard-defaulted to `gemini-2.5-flash-lite` via missing Config key** |
| Endpoint | Interactions API | `generateContent` |

Exact failure mode:

1. `Config` never mapped `GEMINI_EXPLORE_MODEL` → `ai.gemini_explore_model`.
2. Explore therefore always used the code default `gemini-2.5-flash-lite`.
3. Google currently returns **HTTP 404** for `gemini-2.5-flash-lite` on many API keys/projects (“no longer available to new users”), even while docs still list it.
4. ExploreApi mapped `provider_http_404` → the exact customer-facing unavailable message.
5. Song DNA kept working because it already uses the accessible `GEMINI_MODEL` (`gemini-3.6-flash`).

This was **not** an API-key / live-calls / providers-gate failure (those would also break Song DNA).

## Exact fix

1. Wire `GEMINI_EXPLORE_MODEL` → `ai.gemini_explore_model` in `Config` (empty = reuse `GEMINI_MODEL`).
2. Change Explore model resolution to **reuse the proven Song DNA `GEMINI_MODEL`** when no override is set.
3. If an explicit Explore override returns HTTP 404, **retry once** with `GEMINI_MODEL`.
4. Keep the existing HTTP 400 structured-output → plain JSON-mode fallback.
5. Add sanitized diagnostics:
   - server log: `var/log/ai-providers.log` (status/model/http only; no keys, DNA, prompts, bodies)
   - development API `error.fields.diagnostic` when `APP_DEBUG` or `APP_ENV=development`
   - owner readiness: `GET /api/v1/explore-directions/readiness`
   - CLI: `php bin/diagnose-gemini-explore.php` (+ optional `--smoke`)
6. Explore UI shows the diagnostic code when the API includes it (no redesign).

### Files changed

- `src/AI/GeminiExploreService.php`
- `src/Api/ExploreApi.php`
- `src/Support/Config.php`
- `src/AI/AdapterFactory.php`
- `public/assets/js/explore.js`
- `.env.example`
- `bin/diagnose-gemini-explore.php` (new)
- `tests/run.php`
- `docs/design/CURSOR-HANDOFF.md` (this file)

## Tests / results

```text
php tests/run.php
=== Results: 940 passed, 0 failed ===
```

Covered Explore failure modes:

- successful three-direction structured response
- provider 400 structured-output → JSON-mode fallback
- 401/403 auth mapping
- 404 model-unavailable + fallback to `GEMINI_MODEL`
- terminal 404 when no alternate model remains
- 429 rate-limit mapping
- incomplete / invalid JSON
- config gates: providers disabled, live calls off, API key missing
- sanitizer: no lyrics/portraits in Explore payload; diagnostics never log secrets/DNA

Cloud agent had no live `GEMINI_API_KEY`, so provider smoke was not run here. On the Hostinger host:

```bash
php bin/diagnose-gemini-explore.php
php bin/diagnose-gemini-explore.php --smoke
```

Or as owner (development): `GET /api/v1/explore-directions/readiness`

## Hosting note

No new env var is required if `GEMINI_MODEL` already works for Song DNA.  
Optional: leave `GEMINI_EXPLORE_MODEL` empty (recommended). Do **not** set it to `gemini-2.5-flash-lite` unless that key already has prior 2.5 access.

## Exact iPhone retest steps

1. Deploy/pull this `main` commit to the private Hostinger site.
2. Confirm `GEMINI_API_KEY`, `AI_PROVIDERS_ENABLED=true`, `GEMINI_LIVE_CALLS=true`, and working `GEMINI_MODEL` remain set (same values Song DNA already uses).
3. Optionally run `php bin/diagnose-gemini-explore.php --smoke` on the host; expect `status: ok`.
4. On iPhone: sign in → `/create`.
5. Discover a song that produces usable Song DNA.
6. Select an existing portrait (temporary Build 1 gating).
7. In Direction, confirm **Let AI shape the direction** appears.
8. Tap **Explore options** → expect **exactly three** song-specific directions; first is recommended.
9. Select one → existing style + special-instructions bridge update → review/generate.
10. Repeat with **Generate for me** → top recommendation applied and existing generation pipeline continues.
11. If anything fails, the status line should now include a diagnostic like `(provider-model-unavailable)` or `(provider-rate-limited)` in development; also check `var/log/ai-providers.log`.

## Current limitations (unchanged)

1. People stage still gates Direction (portrait required).
2. Explore still bridges through internal StyleMap + special instructions.
3. Directions are not first-class draft objects yet.
4. No Explore cache yet.
5. UI is still the temporary first-build panel (no redesign in this fix).

## What GPT should decide next

1. Approve live direction quality after the iPhone retest (are the three options meaningfully DNA-specific?).
2. Whether Explore should stay on `generateContent` with `GEMINI_MODEL`, or move to a dedicated free-tier text model once benchmarking exists.
3. Priority for: caching/persistence, first-class visual-direction fields, Song DNA selection UI, removal of People-stage gating.
4. Whether development diagnostics should remain in the Create status line long-term, or owner/log-only after private testing.

## Commit

- **Hash:** `9f7a27576e61c5f9335a6894668d31828e3eb3ca`
- **Message:** Fix Gemini Explore 404 by reusing proven GEMINI_MODEL
- **Branch:** `main`
