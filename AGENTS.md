# AGENTS.md — YouAreTheSongNow V2

This file is the entry point for Cursor and other coding agents working in this repository.

## Project intent

YouAreTheSongNow V2 is a clean web + mobile rebuild of AISaga Arcana. The legacy repository is `cubixmeow-commits/youarethesongnow`.

V1 is a **reference implementation**, not a template to port wholesale.

## CURRENT BUILD FREEZE

**Do not scaffold, implement, install frameworks, create application source, create migrations, or otherwise begin building V2 until the project owners explicitly end this freeze.**

Allowed work during the freeze:

- inspect V1 source;
- document verified behavior;
- create feature/route/data/integration inventories;
- write assessment and migration Markdown;
- create architecture diagrams/ADRs marked proposed;
- record product questions and owner decisions;
- compare legacy schemas/configuration without modifying production.

If asked to work in this repository without an explicit instruction to begin implementation, default to planning/analysis only.

## Before changing code after the freeze is lifted

1. Read `docs/rebuild/README.md`.
2. Read the relevant architecture/migration notes.
3. When using V1 as evidence, label findings as observed, inferred, or proposed.
4. Do not trust legacy planning documents merely because they are in the repo. Verify claims against Arcana-specific source and runtime behavior where possible.

## Working rules

- Preserve product behavior intentionally, not accidentally.
- Do not automatically copy legacy PHP implementation into V2.
- Prefer small end-to-end vertical slices over broad scaffolding.
- Keep web and mobile domain contracts shared where practical.
- Keep AI providers behind explicit adapters/contracts; UI code should not call model vendors directly.
- Treat long-running image/media generation as asynchronous work with explicit job state.
- Keep secrets out of source control and provide safe environment examples.
- Add tests around business rules, credits/payments, generation state, permissions, and data migration.
- Record meaningful architecture changes in the rebuild docs or an ADR.
- Update documentation when implementation invalidates a documented assumption.

## Source-of-truth order during rebuild

1. Explicit product decisions made for V2
2. Verified V2 implementation and tests
3. Verified Arcana behavior in V1
4. Arcana-specific V1 source code/data
5. Legacy documentation that agrees with the above
6. Inference/assumption

## Immediate objective

Continue the V1 assessment: extract exact Song DNA and Dynamic Band Style behavior, map routes and schema, identify migration-worthy data, and capture owner product decisions. Do not begin the vertical slice until the owners explicitly approve implementation.
