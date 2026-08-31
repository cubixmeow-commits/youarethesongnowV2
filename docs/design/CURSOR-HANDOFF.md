# CURSOR-HANDOFF — Design Operating System

**Date:** 2026-08-31  
**Working branch:** `main`  
**Phase:** First implementation test — **Song DNA Quick Generate + Explore Options compatibility build**

## Locked product decisions

**AI should remove decisions by default and offer intelligent choices when the user asks for control.**

Default: `Song → Song DNA → Generate`.
Optional: `Song → Song DNA → Explore Options → AI-generated visual directions → optional Fine Tune → Generate`.

Portrait management belongs at the top of Gallery for now. Create should ultimately use an active/default portrait automatically. The current People stage remains only as a temporary Build 1 constraint.

## First implementation test

A compatibility-first implementation validates **Generate for me** and **Explore options** before rebuilding the draft schema / generation worker.

- Reuses the existing grounded Song DNA produced during song discovery.
- Sends derived Song DNA + the internal active StyleMap catalog to Gemini.
- Sends no portraits and no raw lyrics in the Explore call.
- Gemini returns exactly 3 song-specific visual directions: name, description, internal compatibility style id, and prompt hint.
- `Generate for me` applies Gemini's strongest direction and hands off to the existing generation path.
- `Explore options` shows all three directions for selection.
- StyleMap mapping and special-instructions bridging are temporary compatibility plumbing, not the target product architecture.

### Added / changed

- `src/AI/GeminiExploreService.php` — structured Gemini direction generation, default `gemini-2.5-flash-lite`, override via `ai.gemini_explore_model`.
- `src/Api/ExploreApi.php` — authenticated + CSRF-protected `POST /api/v1/explore-directions`.
- `public/assets/js/explore.js` — temporary Create UI bridge, loaded before `app.js` to observe the existing song-lookup response.
- `public/index.php` — registers `ExploreApi`.
- `templates/layouts/main.php` — loads `explore.js` only on `/create`.

## Live test result — current blocker

The first live mobile test reaches the new panel correctly, but both Explore/Generate-for-me fail server-side.

Observed customer-facing message after the request-format fix:

> AI directions are unavailable right now. You can still choose a direction manually.

That exact message is emitted by `ExploreApi` when the service returns one of:

- `gemini_unavailable`
- `provider_http_401`
- `provider_http_403`
- `provider_http_404`

The original request-format bug was already corrected: `generateContent` now uses `generationConfig.responseMimeType = application/json` with `responseJsonSchema`, plus a plain-JSON retry on provider HTTP 400.

Google's current docs confirm `gemini-2.5-flash-lite` remains a stable model code and currently has a Gemini Developer API free tier. Do not assume the model name is invalid without testing the actual provider response.

## Cursor task — debug and fix the live Explore call

Work directly on `main` for this development-only site.

1. Read this handoff first, then inspect the existing Gemini Song DNA path and the new Explore path side by side.
2. Determine why `/api/v1/explore-directions` is returning `explore_unavailable` in the deployed environment.
3. Trace all four possible causes separately:
   - provider gate disabled
   - Gemini live-calls flag disabled
   - API key missing / wrong config mapping
   - Gemini provider response 401 / 403 / 404
4. Reuse the exact same proven Gemini credential/config path already used successfully by `GeminiLyricsResearchService`; do not introduce a second API key or unrelated provider configuration.
5. Inspect `Config` and environment-variable mapping. If `ai.gemini_explore_model` is not a real configured key, either add it correctly or intentionally reuse the working general Gemini model configuration with a safe explicit fallback.
6. Verify the actual provider request against current Google Gemini REST `generateContent` requirements for `gemini-2.5-flash-lite`.
7. Add safe development diagnostics so owner/development testing can distinguish configuration-disabled, auth/permission, model-not-found, rate-limit, malformed-request, timeout, and incomplete-output failures. Do **not** expose API keys, provider response bodies containing sensitive input, raw lyrics, prompts, or portrait data.
8. Prefer server-side sanitized logging plus a concise development-only error/status in the UI or API response. Customer-facing production copy can stay generic later.
9. If 2.5 Flash-Lite is unavailable for this specific project despite current docs, implement a tested fallback to a currently available free-tier Gemini text model already accessible to the same API key. Do not silently switch to a paid-only model.
10. Keep the Explore call limited to derived Song DNA + compatibility style catalog. No portraits and no raw lyrics.
11. Add automated tests for:
    - successful three-direction structured response
    - provider 400 structured-output fallback
    - 401/403 auth mapping
    - 404/model-unavailable handling and fallback if implemented
    - 429 rate-limit mapping
    - incomplete/invalid JSON
12. Run the repo's full existing test suite and report exact pass/fail totals.
13. If possible from the agent environment, make a real provider smoke test using the existing development configuration without printing secrets. If provider credentials are unavailable to the cloud agent, build a temporary safe owner/development diagnostic endpoint or command that can be run on deployment and document exactly how to read the result.
14. Do not redesign the UI in this debugging task. Fix reliability first.
15. Commit the fix directly to `main` with a clear commit message.
16. Update this handoff before finishing with:
    - root cause
    - files changed
    - tests run/results
    - exact live-test steps for the user on iPhone
    - any remaining blocker requiring hosting/environment changes

## Current limitations

1. Existing People stage still gates Direction, so a portrait must currently be selected before the new panel appears.
2. Current draft/job schema requires `styleId`; Explore directions are translated through an internal StyleMap for now.
3. Explore directions are not persisted as first-class draft/snapshot objects yet.
4. `promptHint` bridges through special instructions (500-char limit).
5. Explore is not cached yet; repeated clicks make repeated Gemini calls.
6. Live provider reliability is the current blocker.

## Manual first-build test after Cursor fix

1. Sign in and open `/create` on iPhone.
2. Discover a song that produces usable Song DNA.
3. Select an existing portrait (temporary Build 1 gating).
4. In Direction, verify **Let AI shape the direction** appears.
5. Tap **Explore options**: expect exactly three song-specific directions; first is recommended.
6. Select one and verify the existing internal direction controls update; then review/generate.
7. Repeat with **Generate for me** and verify it applies the top recommendation and continues into the existing generation pipeline.
8. Repeat with very different songs to assess whether outputs are genuinely DNA-specific.

## Next review

After reliability is fixed and live output is visible, return to GPT/design review for the quality of the three directions, caching/persistence, first-class visual-direction fields, Song DNA selection UI, and removal of People-stage gating. The canonical target Create architecture remains Song-DNA-first.
