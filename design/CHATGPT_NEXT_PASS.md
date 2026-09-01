# YouAreTheSongNow — Cursor Implementation Instructions

**Round:** 012  
**Written by:** ChatGPT  
**Date:** 2026-09-01  
**Status:** Consumed by Cursor on 2026-09-01  
**Repository:** `cubixmeow-commits/youarethesongnowV2`  
**Working branch:** `main`  
**Scope:** Backend-first POV Campaign Engine adaptation

## Owner sequencing decision

Implement and validate the POV-derived visual planning engine first. Continue the rest of the customer-facing Song DNA and application design only after this backend integration is working and reviewed.

Do not spend this round designing the Song DNA selector, Explore Options, Fine Tune, or unrelated screens.

## Start

1. Pull the latest `main`.
2. Read:
   - `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
   - `docs/design/CURSOR-HANDOFF.md`
   - `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`
   - `docs/design/process/VISUAL-NARRATIVE-PLANNING-LAYER.md`
3. Inspect the reference repository:
   - https://github.com/cubixmeow-commits/POV-Campaign-Engine
   - `SKILL.md`
   - `references/board-components.md`
   - `references/campaign-design.md`
   - `references/scene-rules.md`
   - `examples/crowdstrike-outage/campaign.md`
   - `examples/crowdstrike-outage/scenes.md`

Adapt staged extraction, information budgeting, viewpoint selection, structural pivots, scene contracts, motifs, and retained intermediate artifacts. Do not import investigative reporting rules, real-world verification requirements, X-thread/newsletter formats, or chronological observable-only restrictions.

## Implement the working backend integration

### 1. Audit actual V2 boundaries

Trace and document:

- richest existing internal Song DNA source;
- song interpretation services;
- prompt assembly/compiler;
- portrait integration;
- style mapping;
- drafts and persistence;
- queue/job creation and execution;
- provider abstractions;
- credit boundaries;
- current test and fixture architecture.

Map the implementation to actual files before editing.

### 2. Contracts and validation

Implement versioned, validated internal contracts for:

- Visual Campaign Board;
- exactly three directions: primary, alternate, unexpected;
- direction ranking;
- Visual Scene Contract;
- structured prompt sections;
- sanitized planning trace/fallback records.

Use strict structured output, validation, bounded repair, deterministic fallback, explicit template versions, and reasonable token limits.

### 3. Planning engine

Using the richest current internal Song DNA:

- identify the emotional/relational pivot;
- build the Visual Campaign Board;
- generate three materially distinct scene directions;
- score/rank them for Song DNA fidelity, narrative coherence, visual distinctiveness, and portrait suitability;
- automatically choose the strongest valid direction;
- compile the Visual Scene Contract;
- enforce the approved information budget.

Directions must differ in scene, viewpoint, relationship emphasis, or symbolism—not merely style.

### 4. Portrait and prompt integration

- Do not send portrait image data into board or direction planning.
- Integrate portrait roles only after the Scene Contract is selected.
- Compile the final provider prompt in the approved ordered sections.
- Keep style subordinate to scene meaning.
- Reuse existing provider clients and credentials.
- Do not hardcode secrets.

### 5. Existing generation path

Wire the new planner/compiler into the current development generation flow.

Preserve:

- routes;
- authentication/authorization;
- privacy;
- song lookup and drafts;
- portrait security;
- credits and plan rules;
- queue ownership;
- provider execution;
- storage and gallery;
- owner style activate/deactivate;
- current responsive UI.

Maintain a safe fallback or development switch to the current prompt compiler while evaluation is underway.

Planning failure must not strand the user.

### 6. Evaluation

Add fixtures for:

1. intimate loss;
2. triumphant transformation;
3. ambiguous relationship;
4. kinetic adventure;
5. quiet introspection.

For each, record sanitized:

- Visual Campaign Board;
- three directions and ranking;
- selected Scene Contract;
- compiled prompt;
- fallback behavior.

Compare old and new compiler prompts using identical inputs. Evaluate specificity, coherence, composition hierarchy, portrait role, symbol count, contradictions, and Song DNA fidelity. Do not claim improvement merely because a prompt is longer.

Where affordable and supported by the development environment, run controlled old/new image generations. Clearly separate prompt-level verification from image-level verification.

### 7. Controlled retry foundation

Persist or trace enough versioned planning data to preserve successful upstream decisions. Classify failures where practical, but do not change customer retry charging or credit behavior in this round.

## Do not implement this round

- no new customer Song DNA selector;
- no Explore Options UI;
- no Fine Tune UI;
- no unrelated screen redesign;
- no production deployment;
- no unsupported Flutter implementation;
- no credit or billing behavior changes.

## Finish

1. Run the full existing suite and all new tests.
2. Update `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`.
3. Update `docs/design/CURSOR-HANDOFF.md`.
4. Update roadmap, architecture, contract, and prompt documentation.
5. Clearly distinguish prompt-level verification from actual image-generation evaluation.
6. Commit and push verified work directly to `main`.
7. Do not deploy to Hostinger/production.
8. Stop for GPT/owner review before continuing design.
