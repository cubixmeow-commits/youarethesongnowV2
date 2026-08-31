# CURSOR-HANDOFF — Design Operating System

**Date:** 2026-08-31  
**Working branch:** `main`  
**Phase:** First implementation test — **Song DNA Quick Generate + Explore Options compatibility build**

---

## Locked product decisions

### Core creation principle

**AI should remove decisions by default and offer intelligent choices when the user asks for control.**

The default product path is:

```text
Song → Song DNA → Generate
```

The optional control path is:

```text
Song → Song DNA → Explore Options → AI-generated visual directions → optional Fine Tune → Generate
```

Generic image-generator controls and permanent style presets are not the intended primary customer experience.

### Portrait placement — resolved

For now, **portrait management belongs at the top of Gallery**, not in the core Create flow.

Target behavior:

```text
Gallery
├── Your portraits
│   ├── active/default portrait
│   ├── additional portraits
│   └── Add portrait
└── Your creations
```

Create should ultimately use the active/default portrait automatically, with a lightweight way to change it only when necessary. The target repeat flow remains `Song → Song DNA → Generate`.

The shipped Build 1 Create screen still contains its existing People stage until that broader Create refactor is performed. Do not interpret that temporary implementation constraint as the target IA.

---

## First implementation test: Quick Generate + Explore Options

A narrow compatibility-first implementation has been added so the new interaction can be tested **before** rebuilding the draft schema / generation worker.

### Intent

1. Reuse the existing grounded Song DNA produced during song discovery.
2. Send only derived Song DNA + the internal active StyleMap catalog to Gemini for the Explore step.
3. Gemini returns exactly three context-aware visual directions specific to that Song DNA.
4. Each visual direction has:
   - customer-facing direction name
   - short description
   - internal style id (compatibility bridge only)
   - concise prompt hint
5. **Generate for me** uses Gemini's strongest recommendation (first ranked direction).
6. **Explore options** displays all three recommendations.
7. Selecting a recommendation privately maps it into the existing StyleMap + special-instructions fields so the current Build 1 generator can still run unchanged.

### Important architecture rule

The current StyleMap mapping is **temporary compatibility plumbing**. It is not approval to relabel or retain the static style grid as the future Explore Options experience.

Full product intent still requires the visual direction itself to become a first-class draft/generation input rather than being translated through a legacy style selection.

---

## Files added / changed for first build

### `src/AI/GeminiExploreService.php`

New lightweight structured-output service.

- Default model: `gemini-2.5-flash-lite`
- Config override: `ai.gemini_explore_model`
- Input: safe subset of already-derived Song DNA + active internal style catalog
- Does **not** send portraits or raw lyrics
- Produces exactly 3 visual directions using a JSON schema
- Validates style ids returned by Gemini against the active internal catalog

Safe Song DNA projection currently includes:

- essence
- emotional arc
- themes
- relationship dynamics
- narrative archetype
- original visual moment
- symbols
- visual metaphors
- mood
- environment
- palette
- lighting
- camera
- composition
- motion
- texture
- subject roles

It intentionally excludes confidence / risk flags / internal analysis metadata from the Explore prompt.

### `src/Api/ExploreApi.php`

Registers:

```text
POST /api/v1/explore-directions
```

Requires:
- authenticated session
- CSRF token
- derived Song DNA

Returns friendly failures for unavailable Gemini / free-tier rate limit / incomplete output.

### `public/assets/js/explore.js`

Temporary first-build UI bridge loaded before `app.js` on `/create`.

Responsibilities:

- observes the existing `/api/v1/song-lookups` response
- captures already-derived `developmentAnalysis.analysis`
- injects a small AI direction panel into the existing Direction stage
- exposes:
  - **Generate for me**
  - **Explore options**
- renders three returned visual-direction cards
- applies selected internal style + promptHint into the existing Build 1 controls
- Quick Generate then hands off to the existing review / generation pipeline

This is deliberately isolated from the large current `app.js` so the experiment can be removed or replaced cleanly during the full Create refactor.

### `public/index.php`

Registers `ExploreApi` after `ApiV1`.

### `templates/layouts/main.php`

Loads `explore.js` before `app.js` on `/create` only so it can observe the existing song-lookup fetch without editing the large app script in this first experiment.

---

## Privacy / provider note

Explore sends **derived Song DNA**, not portraits and not raw lyrics, to the Gemini text call.

This is especially important while testing on the Gemini Developer API free tier. Before external-user release, provider terms/data-use behavior must be reviewed again and the production provider/tier decision must be explicit.

---

## Current limitations / expected first-build behavior

1. The current shipped People stage still gates Direction, so an existing portrait must still be selected before the new AI panel becomes visible. This is a temporary implementation constraint; target portrait management is Gallery-level.
2. Quick Generate internally maps to the existing StyleMap because the current draft/job schema requires `styleId`.
3. The Explore direction itself is not yet persisted as a first-class object on the draft/snapshot.
4. `promptHint` is bridged through the existing special-instructions field (max 500 chars).
5. The Explore call uses the Song DNA returned by the existing development song lookup; customer-safe Song DNA projection still needs a proper API in the full implementation.
6. Explore is not cached yet; repeated clicks can make repeated Gemini calls and consume free-tier quota.
7. Direction output quality, latency, and failure handling need live testing with a representative set of songs.

---

## Manual first-build test

1. Open `/create` while signed in.
2. Discover a song that returns usable Song DNA.
3. Select an existing portrait so the current Direction stage appears (temporary Build 1 limitation).
4. Confirm the new **Let AI shape the direction** panel appears.
5. Test **Explore options**:
   - loading state appears
   - exactly three song-specific directions appear
   - first is labeled as recommended
   - selecting one maps into the existing direction controls
6. Review the creation and confirm the current generation pipeline still works.
7. Repeat with **Generate for me**:
   - Gemini generates directions
   - first recommendation is automatically applied
   - existing review/generation handoff is triggered
8. Test provider failure / rate limiting if practical.
9. Test several very different songs to judge whether the three outputs are genuinely Song-DNA-specific rather than generic style renames.

---

## Next review questions

After live testing, GPT / Cursor should review:

1. Are the three directions meaningfully different and song-specific?
2. Is a model call necessary for Quick Generate every time, or can Quick Generate derive a single direction more cheaply/directly?
3. Which parts of the Gemini result need to be persisted on the creation snapshot for reproducibility?
4. Should Explore results be cached per Song DNA selection?
5. How should customer-facing Song DNA selection modify the Explore request once the DNA picker exists?
6. Which visual-direction attributes should become first-class generation inputs instead of being collapsed into `styleId` + special instructions?
7. After this interaction proves useful, remove the current People-stage gating and move portrait management to the top of Gallery as already decided.

---

## Prior design/audit context

The permanent UI audit, semantic-token proposal, Flutter portability notes, and structural Create-flow specification remain under `docs/design/`.

The canonical target Create architecture remains Song-DNA-first. This first build is an experiment designed to validate **Generate for me vs Explore options**, not the final production architecture.
