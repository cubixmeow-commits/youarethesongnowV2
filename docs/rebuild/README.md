# Rebuild Planning Index

This directory is the durable planning area for rebuilding AISaga Arcana as YouAreTheSongNow V2.

## Current mode

**Assessment/planning only. Do not build or scaffold V2 yet.** Source analysis, inventories, diagrams, decision records, and migration planning are allowed; application implementation waits for an explicit owner decision to begin.

## Documents

- `00-legacy-repo-assessment.md` — what the old repository tells us now
- `01-product-rebuild-brief.md` — what we are rebuilding and what still needs product decisions
- `02-target-architecture.md` — proposed technical shape for web + mobile
- `03-migration-plan.md` — how to move from V1 knowledge to a V2 product safely
- `04-initial-backlog.md` — ordered work for the first rebuild phase
- `05-cursor-workflow.md` — how Cursor and other agents should use this material
- `06-v1-feature-map.md` — verified V1 capability map and canonical creative/generation flow
- `07-v1-data-integrations-audit.md` — V1 tables, storage, Gemini, Stripe, email, Turnstile and migration implications
- `08-v1-risks-and-decisions.md` — implementation risks, unknowns, deferred decisions, and explicit build freeze
- `09-v1-creative-engine-audit.md` — forensic audit of Song DNA, Dynamic Band Style/Lore, portraits, retries, and prompt behavior
- `10-v1-prompt-pipeline.md` — stage-by-stage prompt transformations, schemas, and retention matrix
- `11-v2-prompt-refinement-plan.md` — **PROPOSED — NOT IMPLEMENTED** multi-artifact creative pipeline and prompt specs
- `12-open-creative-decisions.md` — unresolved creative/product questions for owner decisions

## Evidence labels

Use these labels in future assessment notes:

- **Observed** — directly verified in repository source/config/runtime evidence.
- **Inferred** — strongly suggested by evidence but not yet traced end-to-end.
- **Proposed** — a V2 design recommendation, not a statement about V1.
- **Decision** — explicitly accepted for V2 by the project owners.
- **Open Question** — requires a product decision or unavailable production evidence.

## Important warning

The V1 repository contains documentation that is not consistently about Arcana. For example, the root `SCHEMA.md` describes the VibeKB documentation format rather than Arcana's application database. The V1 `.vibekb` inventory itself is useful where its records are marked `verified-from-source`, but claims must still be distinguished from runtime/production configuration that is not represented in Git.

## Current deep-audit findings

The traced V1 system includes accounts/authentication, invite registration, email verification, plans and credits, a queued image-generation form, Song DNA analysis, static and Dynamic Band Style logic, cinematic prompt construction, portrait references, Gemini image generation, multi-step safety/retry fallbacks, gallery/history, local + Backblaze B2 media storage, Stripe checkout/subscriptions/credit packs, email/notifications, admin tools, maintenance mode, and public/system pages.

### Creative-engine headline (2026-08-27)

- **Song DNA** is a real 12-field Gemini JSON blueprint and is the always-on interpretation stage.
- **Dynamic Band Style** (`ANALYZE_BAND_STYLE`) is a real optional StyleMap path derived from album-cover visual knowledge — a **visual-style generator**, not a lore bible.
- **“Dynamic Band Lore Engine™”** is primarily marketing language for the overall pipeline; V1 does **not** implement a separate song lore/story subsystem in the queue workers.
- The generator UI default is labeled as the Lore Engine but submits an empty `image_style`, so StyleMap analysis does **not** run for default users.
- Portraits are dropped at retry attempt 4; users are not clearly told when a successful image abandoned their identity.
- The strongest V1 creative ideas to keep: staged DNA, portrait-as-protagonist, soft custom instructions, async jobs, StyleMap-as-design-brief.
- The weakest: mega-prompt contradictions, lore/style naming confusion, silent identity loss, lyric persistence, corrupted v3 retry prompt text vs intact sequential workers.

## Next assessment / design pass

Continue without implementation by:

1. owner workshop on `12-open-creative-decisions.md` (especially lore meaning, portrait promise, lyrics architecture);
2. refining accepted contracts from `11-v2-prompt-refinement-plan.md` after decisions;
3. complete user-facing route/screen inventory with keep/change/retire classification;
4. provisional V1 schema map from referenced tables/columns;
5. production schema comparison when a schema-only database export becomes available;
6. product decisions about plans/credits, gallery/project model, privacy/sharing, and legacy migration.
