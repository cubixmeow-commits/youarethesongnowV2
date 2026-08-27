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

## Evidence labels

Use these labels in future assessment notes:

- **Observed** — directly verified in repository source/config/runtime evidence.
- **Inferred** — strongly suggested by evidence but not yet traced end-to-end.
- **Proposed** — a V2 design recommendation, not a statement about V1.
- **Decision** — explicitly accepted for V2 by the project owners.

## Important warning

The V1 repository contains documentation that is not consistently about Arcana. For example, the root `SCHEMA.md` describes the VibeKB documentation format rather than Arcana's application database. The V1 `.vibekb` inventory itself is useful where its records are marked `verified-from-source`, but claims must still be distinguished from runtime/production configuration that is not represented in Git.

## Current deep-audit findings

The traced V1 system includes accounts/authentication, invite registration, email verification, plans and credits, a queued image-generation form, Song DNA analysis, static and Dynamic Band Style logic, cinematic prompt construction, portrait references, Gemini image generation, multi-step safety/retry fallbacks, gallery/history, local + Backblaze B2 media storage, Stripe checkout/subscriptions/credit packs, email/notifications, admin tools, maintenance mode, and public/system pages.

The strongest V1 design concept to retain is asynchronous generation: submission creates durable queued work, while background workers perform creative analysis and rendering. The weakest areas are duplicated legacy worker/payment paths, plan-model drift, incomplete schema provenance, mutable credit accounting, and production configuration that cannot be proven from source alone.

## Next assessment pass

Continue without implementation by extracting and documenting:

1. exact Song DNA schema and prompt;
2. exact Dynamic Band StyleMap schema/prompt/fallback;
3. ordered image retry/safety chain;
4. complete user-facing route/screen inventory with keep/change/retire classification;
5. referenced DB tables/columns into a provisional V1 schema map;
6. production schema comparison when a schema-only database export becomes available;
7. product decisions about inputs, portraits, plans/credits, gallery/project model, privacy/sharing, and legacy migration.
