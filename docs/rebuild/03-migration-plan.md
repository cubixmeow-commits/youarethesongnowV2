# 03 — Rebuild and Migration Plan

## Phase 0 — Preserve and understand

- Keep V1 intact as the historical reference.
- Inventory Arcana-specific source, routes, database tables, prompts, integrations and media.
- Identify stale/unrelated repo artifacts so agents do not treat them as source of truth.
- Capture screenshots or behavior notes for important V1 journeys when useful.
- Classify every major V1 capability: keep, change, replace, or retire.

**Exit:** a verified V1 feature map and data/integration inventory.

## Phase 1 — Lock the minimum V2 contracts

- Decide initial project/generation lifecycle.
- Define API/domain contracts for the first vertical slice.
- Choose web/mobile framework, auth, database, object storage and job mechanism.
- Write ADRs for decisions that are expensive to reverse.
- Establish environment/secrets conventions and CI.

**Exit:** architecture sufficient to build one end-to-end path, without prematurely designing every feature.

## Phase 2 — Vertical slice

Build one complete path:

1. identity/session;
2. create project;
3. submit permitted song/context input;
4. create generation job;
5. execute Arcana orchestration through an adapter;
6. persist output/media;
7. display status/result on web and mobile;
8. retry/failure behavior;
9. basic tests and telemetry.

**Exit:** the same durable project works across both clients.

## Phase 3 — Product parity where valuable

Add only V1 capabilities the owners still want: gallery/history, uploads, richer Arcana controls, regeneration, sharing/export, email, credits and payments, etc. Rewrite behavior against V2 contracts rather than copying files.

## Phase 4 — Data/media migration

- Map V1 IDs/tables/files to V2 entities.
- Build repeatable migration scripts with dry-run/report mode.
- Preserve ownership and provenance.
- Reconcile credit/account data explicitly.
- Verify media counts/hashes/links where practical.
- Run a test migration before production cutover.

## Phase 5 — Launch and retire V1

- Run production readiness/security/payment checks.
- Define rollback and support procedures.
- Cut traffic/accounts over deliberately.
- Keep the legacy system read-only for an agreed validation window if needed.
- Archive migration reports and final V1 commit/reference.

## Rule

“Parity” is not the objective. **Preserving the product value while eliminating accidental legacy constraints is the objective.**
