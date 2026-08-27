# 05 — Cursor and AI Coding Workflow

The goal is to let Cursor move quickly without allowing stale V1 assumptions to silently become V2 architecture.

## Every new Cursor session

1. Read root `AGENTS.md`.
2. Read `docs/rebuild/README.md`.
3. Read the document relevant to the current task.
4. If implementing behavior inherited from V1, inspect the actual V1 evidence first.
5. State whether a conclusion is observed, inferred, proposed, or already decided.

## Good task shape

Prefer tasks like:

> Trace the complete V1 credit purchase flow and document files, tables, state changes, failure cases and Stripe webhook behavior. Do not modify V2 code yet.

or:

> Implement the V2 generation-state contract defined in the architecture notes, including tests. Do not add provider-specific SDK calls outside the AI adapter package.

Avoid broad prompts such as “port the old app” or “recreate the backend.”

## Handoff discipline

At the end of meaningful work, record:

- what changed;
- files touched;
- tests/run commands used;
- unresolved assumptions;
- data/API compatibility impact;
- documentation/ADR updates needed.

## Legacy-reference rule

When Cursor reads `cubixmeow-commits/youarethesongnow`, it should not infer that every root document belongs to Arcana. The current repo already contains at least one major unrelated/stale documentation system (`SCHEMA.md` describing VibeKB). Prefer Arcana runtime/source evidence and cross-check docs.

## Architecture guardrails

- Keep vendor SDKs at integration boundaries.
- Keep money/credits authoritative on the server.
- Persist generation state before/around external work.
- Do not duplicate domain rules separately in web and mobile.
- Avoid giant “shared UI” abstractions solely to maximize reuse.
- Make migration tools repeatable and observable.
- Add tests before refactoring sensitive payment/migration behavior.

## Documentation expectation

If code proves a documented assumption wrong, fix the document in the same change. If a new expensive-to-reverse decision is made, create an ADR rather than leaving the decision only in chat or Cursor history.
