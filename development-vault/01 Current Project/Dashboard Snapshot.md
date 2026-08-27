---
type: dashboard-snapshot
status: active
updated: 2026-08-27
area: project
---

# Dashboard Snapshot

Curated summary for the GitHub Pages command center in `/docs`.

Agents and humans: when Current Project truth changes, update this snapshot **and** `docs/dashboard-data.js` (or the equivalent dashboard content) so the public hub stays aligned.

## Phase

Planning / creative-engine design

## Build status

BUILD FREEZE — planning only

## Delivery path

**Web first -> Flutter/Dart iOS second**

- Phase 1: rebuild/refine the web application on the PHP backend with SQLite initially.
- Phase 2: build the iOS application in Flutter + Dart.
- Flutter should consume the same PHP backend through clean HTTP/JSON APIs.
- The web rebuild is the shared backend/reference client foundation, not a throwaway prototype.

## Stack direction

- Web/backend: PHP
- Database (initial): SQLite
- Mobile client: Flutter + Dart (iOS first, after web validation)
- Strategy: rebuild/refine V1 functionality, not V1 code

## Mission

Turn a song into an interpreted cinematic visual experience, optionally placing the user inside that world.

## Prompt-system status

**V1 prompting functionality is now mapped as a system, not a single prompt.**

Five prompt origins are documented:

1. hardcoded source templates/directives;
2. runtime-assembled cinematic prompts;
3. database-stored static style prompts (`arcana_styles.prompt_text`);
4. model-generated artifacts (Song DNA, Band Visual DNA, StyleMap);
5. fallback/retry prompt mutations.

The older worker preserves a large inline style catalog that appears to be strong predecessor material for the later DB-managed style library. Exact final production DB rows still require a database export if one is recoverable.

Deep references:

- `docs/rebuild/13-prompting-functionality-reference.md`
- `docs/rebuild/14-prompt-quality-and-refinement-analysis.md`
- `development-vault/04 Prompt Lab/V1 Prompt Functionality Map.md`

## Creative-engine direction

```text
Source Context
  -> Song Interpretation
  -> Song DNA
  -> Visual Narrative Plan
  -> Artist Visual Identity
  -> Portrait Integration Plan
  -> Scene Composition
  -> Prompt Compiler
  -> Provider Adapter
  -> Quality / Safety Evaluation
  -> Controlled Retry
```

## Accepted decisions

- Rebuild functionality, not code
- PHP backend/web app
- SQLite initially
- Web first, then Flutter/Dart iOS using the shared PHP backend

## Top open decisions

- Meaning of “Dynamic Band Lore” in V2
- Portrait fallback honesty
- Lyrics persistence architecture
- Branding in-image vs post-process
- Whether artist visual identity runs by default

## Read next

1. `START HERE.md`
2. `01 Current Project/Current Priorities.md`
3. `04 Prompt Lab/V1 Prompt Functionality Map.md`
4. `docs/rebuild/13-prompting-functionality-reference.md`
5. `docs/rebuild/14-prompt-quality-and-refinement-analysis.md`
6. `02 Decisions/Decision Inbox.md`
7. `docs/rebuild/12-open-creative-decisions.md`
