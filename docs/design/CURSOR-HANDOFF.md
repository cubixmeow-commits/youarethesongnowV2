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

## Current limitations

1. Existing People stage still gates Direction, so a portrait must currently be selected before the new panel appears.
2. Current draft/job schema requires `styleId`; Explore directions are translated through an internal StyleMap for now.
3. Explore directions are not persisted as first-class draft/snapshot objects yet.
4. `promptHint` bridges through special instructions (500-char limit).
5. Explore is not cached yet; repeated clicks make repeated Gemini calls.
6. Live tests are still required for output quality, latency, provider failures, and the final review/generate handoff.

## Manual first-build test

1. Sign in and open `/create`.
2. Discover a song that produces usable Song DNA.
3. Select an existing portrait (temporary Build 1 gating).
4. In Direction, verify **Let AI shape the direction** appears.
5. Test **Explore options**: expect exactly three song-specific directions; first is recommended.
6. Select one and verify the existing internal direction controls update; then review/generate.
7. Repeat with **Generate for me** and verify it applies the top recommendation and continues into the existing generation pipeline.
8. Repeat with very different songs to assess whether outputs are genuinely DNA-specific.

## Next review

After live testing, determine caching, persistence, cheaper Quick Generate logic, first-class visual-direction fields, and removal of People-stage gating. The canonical target Create architecture remains Song-DNA-first.
