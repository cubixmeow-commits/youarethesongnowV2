---
type: current-project
status: active
updated: 2026-08-27
area: priorities
---

# Current Priorities

This note is the daily “what matters now” list for CuBiX Meow and Brut. Keep it short. Update it when focus shifts.

## Active now

1. Use the new V1 prompt-functionality map to decide which prompting behaviors are worth preserving/refining in V2.
2. Finish creative-engine product decisions (lore vs visual identity, portrait promise, lyrics architecture).
3. Keep the shared development vault as working memory; keep `/docs` as the polished command center.
4. Treat V1 research as evidence, not an implementation template.

## Newly documented

- V1 prompts come from hardcoded source templates, runtime assembly, database-stored style prompts, model-generated artifacts, and retry mutations.
- Later V1 stores static visual style prompts in `arcana_styles.prompt_text` with admin CRUD.
- The older worker preserves a large inline style catalog that is strong evidence for the predecessor of the DB-managed prompt library.
- Deep references: `docs/rebuild/13-prompting-functionality-reference.md` and `14-prompt-quality-and-refinement-analysis.md`.

## Next

1. If the old production DB is recoverable, export `arcana_styles` and compare the final rows against the legacy inline style catalog.
2. Accept or revise the staged creative-engine pipeline into owner-approved contracts.
3. Decide which open Decision Inbox items must be settled before any build-freeze lift.
4. Only then plan the first vertical slice.

## Waiting / blocked

- Owner workshop on Decision Inbox creative-engine questions.
- Final V1 database style rows unless a production DB/export is available.
- Explicit lift of the build freeze before any application scaffolding.

## Not now

- Scaffolding PHP app structure
- Installing frameworks
- Creating databases, APIs, or UI product code
- Porting V1 PHP workers wholesale

## Cat says

Document the spellbook before rewriting the spells. Protect portrait identity before style flourishes. Lore and style are still separate questions.
