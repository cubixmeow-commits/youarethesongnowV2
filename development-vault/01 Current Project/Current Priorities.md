---
type: current-project
status: active
updated: 2026-08-27
area: priorities
---

# Current Priorities

This note is the daily “what matters now” list for CuBiX Meow and Brut. Keep it short. Update it when focus shifts.

## Active now

1. **Complete the first-build feature-definition workshop together:** `development-vault/05 Product Design/First Build Feature Workshop.md`.
2. Use the workshop to classify every candidate feature as **FIRST BUILD / SOON AFTER / LATER / MAYBE / NO** and define FIRST BUILD behavior precisely.
3. Use the V1 prompt-functionality map to decide which prompting behaviors are worth preserving/refining in V2.
4. Finish creative-engine product decisions (lore vs visual identity, portrait promise, lyrics architecture).
5. Keep the shared development vault as working memory; keep `/docs` as the polished command center.
6. Treat V1 research as evidence, not an implementation template.

## Newly documented

- **First Build Feature Workshop:** an extensive product-definition questionnaire covering product promise, accounts, projects, song input, Song DNA, narrative planning, artist identity, visual styles, portraits, image controls, prompts, providers, queues, output, gallery, storage, credits, billing, entitlements, notifications, admin, screens, UX, privacy, failures, Prompt Lab, API/Flutter readiness, operations, testing, and explicit defer/retire decisions.
- V1 prompts come from hardcoded source templates, runtime assembly, database-stored style prompts, model-generated artifacts, and retry mutations.
- Later V1 stores static visual style prompts in `arcana_styles.prompt_text` with admin CRUD.
- The older worker preserves a large inline style catalog that is strong evidence for the predecessor of the DB-managed prompt library.
- Deep references: `docs/rebuild/13-prompting-functionality-reference.md` and `14-prompt-quality-and-refinement-analysis.md`.
- **V2 systems inventory / build map** (planning only): `docs/rebuild/15-v2-systems-inventory.md` (detail) and `docs/rebuild/16-v2-build-map-summary.md` (glanceable skeleton, dependency map, phased order). Review against V1 audits before any freeze lift.

## Next

1. Convert the completed workshop into a concise owner-approved **First Build Feature Contract**.
2. Turn accepted creative-engine choices into explicit stage contracts.
3. Decide which remaining Decision Inbox items must be settled before any build-freeze lift.
4. If the old production DB is recoverable, export `arcana_styles` and compare the final rows against the legacy inline style catalog.
5. Only after the first-build contract is accepted, plan the first vertical slice.

## Waiting / blocked

- CuBiX Meow + Brut first-build feature workshop.
- Owner workshop on unresolved creative-engine questions.
- Final V1 database style rows unless a production DB/export is available.
- Explicit lift of the build freeze before any application scaffolding.

## Not now

- Scaffolding PHP app structure
- Installing frameworks
- Creating databases, APIs, or UI product code
- Porting V1 PHP workers wholesale

## Cat says

Choose the first toys before building the whole cat tree. Document the spellbook before rewriting the spells.
