# Create-flow specification

**Status:** structural product architecture (approved principles; **not** final visual design)  
**Date:** 2026-08-31  
**Branch:** `cursor/design-system-audit-95fa`  
**Authority:** GPT / design-review product decisions recorded into the permanent design OS  
**Companion vault note:** `development-vault/05 Product Design/Create Flow Architecture Contract.md`  
**Do not implement** this Create UX in the production Create screen as part of this documentation pass.

This document specifies the **target Create product architecture**, maps it against the **current Build 1 Create implementation**, and lists backend/UI dependencies. Each major section will later receive its own focused design/UX pass.

---

## 1. Core creation principle (locked)

**AI should remove decisions by default and offer intelligent choices when the user asks for control.**

Rules:

1. The app must **not** primarily expose generic image-generator controls.
2. When a Song DNA concept can express the user’s intent more naturally, **use Song DNA** instead of technical image-generation parameters.
3. The user is creating a **visual interpretation of a song**, not operating an image model.
4. Never expose customer-facing technical model terminology (prompt engineering, inference, sampling, LoRA, model parameters, provider names).

---

## 2. Target canonical flow

```text
Create
  ↓
Choose Song
  ↓
Choose Song DNA
  ↓
Quick Generate          ← DEFAULT
  OR
Explore Options         ← secondary (curious users)
  ↓
AI-generated visual directions   ← Explore Options only (or after opt-in)
  ↓
Fine Tune (optional)             ← advanced only; under a recommended direction
  ↓
Generate
  ↓
Generation experience
  ↓
Reveal
  ↓
Save / Share / Variation / Reimagine
```

### Path summaries

| Path | Sequence | Audience |
| --- | --- | --- |
| **Simple (default)** | Song → Song DNA → Generate | Most users |
| **Curious** | Song → Song DNA → AI visual directions → Generate | Users who want more creative control |
| **Advanced** | Song → Song DNA → Recommended direction → Fine Tune → Generate | Power users only |

**Quick Generate** is the default. After song + one or more Song DNA elements, the system **automatically** determines fitting generation choices from AI analysis of that specific Song DNA (visual treatment, composition, atmosphere, camera language, lighting, color, environment, framing, symbolism, and related parameters). Those parameters are **not** required technical controls in the UI.

**Explore Options** replaces generic style presets (`cinematic`, `anime`, `watercolor`, `realistic`, etc.) with a **small set of context-aware visual directions** generated for the selected Song DNA. Directions are **not** permanent presets; they are recommendations for that song + DNA selection.

**Fine Tune** is optional and lives **beneath** an AI-recommended direction. It must not dominate the default experience.

---

## 3. Song DNA as the creative control surface

Song DNA is a core differentiator. **Do not design the flow around direct lyric selection.**

The user chooses meaningful **analyzed** parts of the Song DNA. A beginner can select **one** element and continue. A more engaged user may combine multiple elements. Complexity should be exposed progressively (start with one dimension; offer “add another layer”).

### Provisional UX dimension labels → actual Song DNA fields

Provisional labels are **not frozen**. Map them to the **current** `song-dna-v2.0` schema (contract + `CreativePackageBuilder`) before finalizing copy.

| Provisional UX label | Primary schema fields (verified) | Selection shape (proposed) |
| --- | --- | --- |
| Emotional Core | `essence`, `mood[]`, `emotionalArc.*`, `themes[]` | Pick one mood/theme cluster or accept essence summary |
| Story / Situation | `narrativeArchetype`, `originalVisualMoment`, `themes[]` | Pick archetype and/or endorse/select moment emphasis |
| Character / Point of View | `subjectRoles[]`, `relationshipDynamics[]` | Pick role + optional relationship dynamic |
| Setting / World | `environment.settingTypes[]`, `eraAtmosphere`, `weather[]`, `spatialCharacter[]` | Pick setting cluster |
| Symbols / Imagery | `symbols[{concept,visualTranslation}]`, `visualMetaphors[]` | Pick one or more symbols/metaphors |
| Conflict / Tension | `emotionalArc.turningPoint`, `intensityPattern[]`, related `themes[]` / `ambiguities[]` | Pick tension cue |

**Open:** final customer-facing labels, how multi-select combines into the locked generation snapshot, and whether `originalVisualMoment` is user-selectable or always system-owned (see §12).

### Actual Song DNA fields currently available

Verified from `development-vault/05 Product Design/V2 Song DNA and Prompt Pipeline Contract.md` and `src/AI/CreativePackageBuilder.php` (`schemaVersion: song-dna-v2.0`):

| Field | Type | Notes |
| --- | --- | --- |
| `essence` | string | High-level meaning |
| `emotionalArc.openingState` | string | |
| `emotionalArc.turningPoint` | string | |
| `emotionalArc.closingState` | string | |
| `emotionalArc.intensityPattern` | string[] | |
| `themes` | string[] | ≤6 |
| `relationshipDynamics` | string[] | ≤4 |
| `narrativeArchetype` | string | |
| `originalVisualMoment` | string | Newly invented scene; required for package build |
| `symbols[]` | `{concept, visualTranslation}` | ≤5 |
| `visualMetaphors` | string[] | ≤5 |
| `mood` | string[] | ≤6 |
| `environment.settingTypes` | string[] | |
| `environment.eraAtmosphere` | string | |
| `environment.weather` | string[] | |
| `environment.spatialCharacter` | string[] | |
| `palette` | string[] | Production-ready visual direction |
| `lighting` | string[] | |
| `camera` | string[] | |
| `composition` | string[] | |
| `motion` | string[] | |
| `texture` | string[] | |
| `subjectRoles` | string[] | |
| `ambiguities` | string[] | |
| `confidence` | number 0–1 | |
| `riskFlags` | string[] | Categorical codes only |

Analyzer JSON uses a **flat** Interactions schema (`openingState`, `settingTypes`, …); `CreativePackageBuilder::build()` nests them into the contract shape above.

---

## 4. Current Create implementation (as-built)

**Route:** `GET /create` → `templates/pages/create.php` + `public/assets/js/app.js` (`data-create`)  
**Auth:** required  
**Layout:** session header + 3 movements + sticky summary (desktop) + paywall panel + portrait delete dialog

### Current stages

| # | UI label | Required inputs | API |
| --- | --- | --- | --- |
| 01 | The song | Artist + title | `POST /api/v1/song-lookups` → patch draft `songLookupId` |
| 02 | The people | 1–2 portraits | `POST /api/v1/portraits`, draft `portraitIds` |
| 03 | The direction | Style, quality, orientation; optional no-text + special instructions | `GET /styles`, `GET /product-options`, draft patch |
| Review | Overview | Summary ready check | `POST /creation-drafts/{id}/summary` |
| Paywall | Membership | If unsubscribed | checkout / dev-activate |
| Generate | Progress | Locked job | `POST /generation-jobs`, poll `GET /generation-jobs/{id}` |
| Reveal | Image page | `/images/{id}` | download / share / regenerate (“Create another”) / delete |

### Current generation progress stages (backend-truthful today)

From `GenerationJobService` + Onboarding contract:

1. `Finding the heart of your song` (queued)
2. `Building your cinematic world` (claimed / generating)
3. `Bringing you into the scene` (creative package)
4. `Adding the final details` (image generation)

No fake percentages. User may leave; gallery receives the result.

### Current Song DNA exposure

| Surface | Behavior |
| --- | --- |
| Customer Create UI | Song DNA **not** selectable. User never picks DNA dimensions. |
| `SongLookupService::public()` | Returns id, artist, title, state, classification, timestamps only. |
| Lookup create response | May attach `developmentAnalysis` for **private development inspection** only (dev panel in Create). |
| Persistence | Safe derived analysis may be stored on `song_lookups.derived_analysis_json` (no lyrics). Full artifacts on `song_dna_artifacts` after job creative stage. |
| Worker | Rebuilds/uses creative package at generation time; customer does not steer DNA fields. |

### Current “direction” controls (generic generator-leaning)

- Curated **StyleMap** grid (15 launch styles — includes Watercolor, Cinematic Realism, anime-adjacent catalog entries, etc.)
- Quality low/medium/high
- Orientation square/portrait/landscape
- No text in image
- Special instructions textarea

These conflict with the new principle that generic style presets should not be the primary creative control surface.

---

## 5. Current → target step mapping

| Current step | Target counterpart | Disposition |
| --- | --- | --- |
| Create shell / session header | Create shell | **Reuse** chrome patterns; retitle stages |
| 01 Song (artist/title + lookup) | Choose Song | **Reuse** lookup UX and API; improve loading/error states |
| Dev Song DNA inspection panel | — | **Keep private-dev only**; never the customer DNA picker |
| 02 People (portraits) | Gallery portrait shelf + compact active portrait summary in Create | **Resolved:** manage portraits at the top of Gallery; Create uses an active/default portrait after explicit server support. Do not drop portrait capability or invent persistence. |
| 03 Style grid (StyleMap catalog) | Explore Options → AI visual directions | **Change role**: curated StyleMaps become backend/compiler material and/or Fine Tune options — **not** the default customer path. Eventually **remove or demote** the primary style grid from Quick Generate |
| Quality / orientation / no-text / special | Fine Tune (optional) and/or auto choices | **Change**: auto under Quick Generate; optional under Fine Tune. Exact which knobs survive Fine Tune is **open** |
| Review summary | Pre-generate confirmation (lighter) | **Reuse** summary idea; content shifts to Song + DNA (+ direction if Explore) + people |
| Paywall before first generate | Still after configuration, before generation | **Reuse** placement contract; trigger after DNA (+ optional direction) ready |
| Generation progress (4 stages) | Generation experience (creative transformation) | **Evolve** copy and DNA-aware UI; keep honest stage mapping |
| `/images/{id}` reveal | Reveal → Save / Share / Variation / Reimagine | **Reuse** artwork-first page; **rename/reshape** actions |
| `Create another image` (regenerate draft) | Variation / Reimagine | **Split concepts** once product defines difference |
| Download | Save (or Save + Download) | **Clarify** naming |
| Share link / email / stop share | Share | **Reuse** |
| Delete | Account/gallery hygiene | **Keep**; not part of primary post-reveal CTA set |

### Reuse / change / remove summary

**Reuse**

- Song lookup flow and rate limits
- Draft + snapshot + job + credit reservation model
- Portrait library and 1–2 person constraint (until owners expand)
- Paywall timing (after meaningful setup, before generation)
- Honest progress stages (no fake %)
- Gallery + image detail as destination after complete
- Shared `/api/v1` as Flutter contract surface

**Change**

- Primary creative control: StyleMap grid → Song DNA selection
- Default path: auto generation choices from DNA (Quick Generate)
- Secondary path: AI-recommended visual directions instead of generic presets
- Generation UI: DNA-aware transformation sequence
- Reveal CTAs: Save / Share / Variation / Reimagine
- Progressive disclosure model (DNA layers, not Song→People→Direction)

**Eventually remove or demote from default Create**

- Customer-facing style grid as the main “Choose your world” step
- Framing Create as an image-generator control panel
- Dev DNA JSON as any part of the customer path
- Any UI that requires users to understand medium/style taxonomy to succeed

---

## 6. Backend / API / data dependencies

### Already present (foundation)

| Capability | Location | Gap vs target |
| --- | --- | --- |
| Song lookup + optional derived DNA at lookup | `SongLookupService`, `GeminiLyricsResearchService` | DNA not returned as **customer-safe selectable dimensions** |
| DNA schema + package build | `CreativePackageBuilder` | No user selection weighting |
| Style catalog / StyleMap | `styles` table, `StylePromptCatalog` | Generic presets; not per-DNA directions |
| Draft fields | songLookup, portraits, styleId, quality, orientation, noText, special | **Missing** DNA selection + visual-direction choice fields |
| Job progress stages | `generation_jobs.progress_stage` | Only 4 stages; not DNA checklist UX |
| Artifacts | `song_dna_artifacts` | Post-job; not interactive Create input |

### Likely new or extended backend behavior

1. **Customer-safe Song DNA projection API**  
   After successful lookup/analysis, return selectable dimension cards (labels + short values) **without** lyrics, raw prompts, risk internals, or unsafe fields. Possibly omit or sanitize `ambiguities` / `riskFlags` for customers.

2. **Persisted DNA selection on draft/snapshot**  
   e.g. selected dimension keys + selected value refs; immutable on job submit.

3. **Quick Generate auto-resolution**  
   Server derives style/treatment/composition/etc. from selected DNA (may still pick an internal StyleMap + narrative plan without showing them).

4. **AI visual directions endpoint (Explore Options)**  
   Input: song lookup + selected DNA. Output: small N (e.g. 3) named directions with short blurbs. **Not** the static 15-style list. May reference internal style keys without exposing taxonomy.

5. **Fine Tune schema**  
   Explicit allowlist of advanced overrides (orientation, quality, no-text, special instructions, maybe style intensity). Must not reintroduce a full generator console.

6. **Richer progress stages (optional)**  
   Only if worker can truthfully emit them (see §8).

7. **Variation vs Reimagine**  
   Product-defined job types or draft-recreation modes; today only `POST /images/{id}/regenerations` / recreate draft exists.

### Can AI visual directions be built from existing analysis alone?

| Approach | Feasible now? | Notes |
| --- | --- | --- |
| **Heuristic packaging** of existing DNA fields (`mood` + `environment` + `lighting`/`camera`/`palette` into 3 labeled cards) | **Partially** | No new model call; weaker “named direction” quality; still better than generic global presets |
| **New model step** that reads approved DNA + selection and returns N directions | **Required for full intent** | Matches “AI-generated… specifically suited to the selected Song DNA”; needs schema, cost, latency, caching rules |
| **Reuse StyleMap catalog as directions** | **No (product conflict)** | Catalog is permanent generic presets — explicitly rejected as the Explore Options primary UI |

**Verdict:** Existing analysis can **bootstrap** a limited Explore Options MVP via projection/heuristics, but the approved product intent (**context-aware generated recommendations**) needs **new backend behavior** (dedicated generation or structured expansion step). Do not ship Explore Options as a relabeled style grid.

---

## 7. Required UI states

| State | Trigger | UI requirements | Notes |
| --- | --- | --- | --- |
| **Song loading** | Lookup in flight | Disable submit; status “Finding your song…” (or successor copy); no DNA chrome yet | Exists today |
| **Song DNA loading** | Analysis running after/with lookup | Distinct loading for DNA dimensions; do not imply DNA ready until projection available | **New**; today DNA is hidden/dev-only and may complete inside lookup |
| **Song DNA unavailable** | Lookup `notFound`, analysis failed/incomplete, or empty selectable projection | Honest recovery: fix song, try another, or limited fallback path if product allows | Partial today (`notFound` / analysis error messages in status + dev panel) |
| **Quick Generate ready** | ≥1 DNA element selected | Primary CTA Generate; no required style grid | **New** |
| **Explore Options** | User opts for control | Entry to direction generation; secondary to Quick Generate | **New** |
| **AI direction generation** | Fetching N directions | Loading for recommendations; empty/error/retry | **New** |
| **Fine Tune** | Optional under a direction | Collapsed by default; advanced controls only | **New** (repurposes some Direction fields) |
| **Generation** | Job queued/generating | Creative transformation sequence; DNA checklist when possible; no fake % | Evolve current venue-progress |
| **Failure / retry** | Job `failed` | Clear message; credits returned per contract; retry path | Exists inline; may need dedicated treatment |
| **Completed reveal** | Job completed | Artwork-first brief reveal, then Save / Share / Variation / Reimagine | Evolve `/images/{id}` |

Paywall remains a **gate state** between ready-to-generate and generation for first-time unsubscribed users (existing contract).

---

## 8. Generation experience (target)

Not a generic AI progress screen. Represent a **creative transformation sequence**.

Provisional stage labels (only claim backend stages that can be represented truthfully):

```text
Reading the Song DNA
  ↓
Building your world
  ↓
Placing you inside it
  ↓
Shaping the visual direction
  ↓
Creating the image
  ↓
Finishing details
```

**Today’s truthful mapping (4 stages):**

| Current backend stage | Closest provisional label |
| --- | --- |
| Finding the heart of your song | Reading the Song DNA *(approximate)* |
| Building your cinematic world | Building your world |
| Bringing you into the scene | Placing you inside it |
| Adding the final details | Creating the image / Finishing details *(collapsed)* |

**Do not** show six distinct checkmarks unless the worker emits six real stages. Prefer showing **selected Song DNA** during wait:

```text
Building your world
Emotional Core
Longing + defiance      ✓
Setting
Empty coastal city     ✓
Point of View
Returning home         ●
```

Copy must stay free of model terminology.

---

## 9. Reveal (target)

1. Brief clean reveal prioritizing the image.
2. Then expose: **Save**, **Share**, **Variation**, **Reimagine**.
3. Image remains visually dominant.

Current `/images/{id}` already centers the figure but immediately shows a dense action row (Download, Share link, Stop sharing, Create another, Delete) plus email share. Structural redesign of this screen is **out of scope for this doc pass**; record as a later focused reveal pass.

---

## 10. Mobile-first shell implications (document only — do not implement nav change yet)

Canonical viewport order remains **phone → tablet → desktop**.

### Proposed primary destinations (conceptual)

- Create  
- Gallery  
- Discover  
- Account  

**Current authed chrome:** Create, Gallery, Account (+ Owner for owners). **Discover does not exist** as a route.

**Implications (do not ship in this task):**

| Topic | Implication |
| --- | --- |
| Discover | Needs product definition (showcase? community? featured worlds?) before IA change |
| Conflict with architecture | Adding a fourth tab affects `main.php` nav, Flutter adaptive scaffold, and guest vs authed chrome; coordinate with Phase 3+ screen work |
| Mobile immersive Create | Focused creation steps **may hide** primary nav — new shell mode; not in current CSS/JS |
| Desktop expansion | Navigation rail + main content + **optional contextual panel** (summary/DNA/direction). Same product architecture, not a separate desktop app. Current sticky summary is a weak precursor |

---

## 11. Flutter portability notes (major interactions)

| Interaction | Flutter mapping |
| --- | --- |
| Choose Song | Form + async repository call; same `/song-lookups` contract |
| Song DNA loading / unavailable | Explicit `AsyncValue` / sealed UI states; do not reuse web dev panel |
| DNA multi-select progressive layers | Custom selection chips/cards; shared draft state notifier |
| Quick Generate CTA | Single primary action → membership gate → job create |
| Explore Options | Secondary route or bottom sheet → directions list from new API |
| AI direction cards | Selectable cards (title + blurb); not StyleMap grid widgets |
| Fine Tune | Expansion panel / secondary route; keep off happy path |
| Generation experience | Full-screen route; poll job; render DNA checklist from selection snapshot; honest stages only |
| Failure/retry | Banner + retry action; credit messaging from API |
| Reveal | Hero image route; delayed secondary actions animation |
| Save / Share / Variation / Reimagine | Map to download/share APIs + future variation endpoints; avoid web `window.confirm` |
| Immersive Create (hide nav) | Flutter `ShellRoute` / nested navigator without bottom bar |
| Desktop rail + contextual panel | `NavigationRail` + optional `EndDrawer` / side `SizedBox` — same IA |

Port **tokens + API screen contracts**, not Create DOM structure.

---

## 12. Resolved and remaining decisions

### Resolved by the Luminous Night Studio package

1. Portrait management lives at the top of Gallery; Create shows/uses active/default identity only after an explicit server contract exists.
2. Fine Tune initially allows orientation, no-text, Special instructions, and quality only while pricing/economics require it. StyleMap/provider/model/camera/palette controls stay hidden.
3. Current regeneration may become `Create a variation`; `Reimagine` remains future until behavior/API are distinct.
4. Use `Download`, not `Save`, while images already persist in Gallery and no favorite/pin state exists.
5. Explore is a secondary action after DNA, not a top-level mode switch.
6. Discover is not added to navigation without a separate product decision.
7. DNA cards use checkbox semantics; Explore directions use radio semantics; DNA is context on generation, not fake checklist progress.

### Remaining contract-first decisions

1. Final customer-facing DNA labels, stable value IDs, and approved projection copy.
2. Whether `originalVisualMoment` is selectable, read-only, or system-only.
3. Minimum/maximum selection and compiler conflict resolution. The UI baseline is 1–3 layers, pending server approval.
4. Truthful context-fallback language when analysis is not lyric-grounded.
5. Customer-safe projection/persistence and active/default portrait persistence.
6. Quick Generate server auto-resolution from selected DNA.
7. Explicit amendments to First Build and Onboarding configuration requirements.
8. Whether quality remains customer-visible after final economics are decided.

---

## 13. Relationship to existing contracts

| Contract | Relationship |
| --- | --- |
| First Build Feature Contract | Journey still song → people → style/quality/orientation. **This Create-flow architecture intends to supersede the customer creative-control portion** once owners formally amend onboarding/first-build contracts. Until then, treat as **approved design-OS direction**, not an automatic override of Build 1 acceptance tests. |
| Onboarding and First-Creation Paywall Contract | Progress language and paywall placement remain useful; configuration step list needs amendment. |
| V2 Song DNA and Prompt Pipeline Contract | Schema authority for fields; Create UI must not invent lyric selection or unsafe DNA content. |
| Shared API, Security and Data Contract | Draft/job/idempotency/privacy rules remain; new endpoints must follow same patterns. |
| Launch Style Catalog | Remains engine/admin catalog; **not** the primary Explore Options UX. |
| V2 Visual Design Direction | Atmosphere/reveal/motion still apply; no broad visual redesign in this pass. |

---

## 14. Implementation sequencing (documentation recommendation only)

1. Owner/GPT amendment of First Build + Onboarding contracts for DNA-first Create.  
2. API: customer DNA projection + draft selection fields.  
3. Structural Create UX: Song → DNA → Quick Generate (portraits placement resolved).  
4. Explore Options + directions endpoint.  
5. Fine Tune demotion of legacy controls.  
6. Generation DNA checklist + reveal CTA pass.  
7. Flutter screen contracts freeze after web validation.

**This task stops at specification.** No production Create refactor.
