# AGENTS.md — YouAreTheSongNow V2

This file is the entry point for Cursor and other coding agents working in this repository.

## Project intent

YouAreTheSongNow V2 is a clean web + mobile rebuild of AISaga Arcana. The legacy repository is `cubixmeow-commits/youarethesongnow`.

V1 is a **behavioral and product reference**, not a template to port wholesale.

## CURRENT BUILD FREEZE

**Do not scaffold, implement, install frameworks, create application source, create migrations, or otherwise begin building V2 until the project owners explicitly end this freeze.**

Allowed work during the freeze:

- inspect V1 source;
- document verified behavior;
- create feature/route/data/integration inventories;
- write assessment and migration Markdown;
- create architecture diagrams/ADRs marked proposed;
- record product questions and owner decisions;
- maintain the development vault and GitHub Pages documentation;
- compare legacy schemas/configuration without modifying production.

If asked to work in this repository without an explicit instruction to begin implementation, default to planning/analysis only.

## Shared development vault

The project has a shared Obsidian vault at `development-vault/` for working development memory. GitHub is the shared sync/source-of-truth mechanism for the vault.

Before planning or future implementation work, always read:

1. `development-vault/START HERE.md`
2. `development-vault/01 Current Project/Current Architecture.md`
3. `development-vault/02 Decisions/Decision Index.md`

Then read only the vault notes relevant to the task. **Do not scan the entire vault by default.**

Vault information authority:

1. Explicit owner instruction
2. Accepted ADRs
3. `01 Current Project/` notes
4. Verified implementation/tests once development begins
5. Verified V1 source behavior
6. Research
7. Experiments
8. Inbox/brainstorming

Never turn an experiment, AI proposal, or brainstorming note into a requirement without an owner decision.

## Current accepted direction

- Rebuild/refine **V1 functionality**, not V1 implementation.
- Use **PHP** for V2.
- Use **SQLite initially** while the product is small, keeping domain/data design portable.
- Preserve the staged creative-engine idea while refining Song DNA, narrative planning, artist visual identity, portrait handling and retries.
- The build freeze remains active.

See the accepted ADRs in `development-vault/02 Decisions/`.

## Before changing code after the freeze is lifted

1. Read the shared vault current-state and accepted decisions above.
2. Read `docs/rebuild/README.md` and relevant architecture/migration notes.
3. When using V1 as evidence, label findings as observed, inferred, or proposed.
4. Do not trust legacy planning documents merely because they are in the repo. Verify claims against Arcana-specific source and runtime behavior where possible.

## Working rules

- Preserve product behavior intentionally, not accidentally.
- Do not automatically copy legacy PHP implementation into V2.
- Prefer small end-to-end vertical slices over broad scaffolding.
- Keep future web/mobile domain contracts clear where practical.
- Keep AI providers behind explicit adapters/contracts; UI code should not call model vendors directly.
- Treat long-running image/media generation as asynchronous work with explicit job state.
- Keep secrets out of source control and provide safe environment examples.
- Do not commit runtime SQLite database files.
- Add tests around business rules, credits/payments, generation state, permissions and data migration.
- Record meaningful architecture/product decisions in the vault as ADRs.
- Update Current Project notes when accepted decisions change current truth.
- Keep prompt experiments in `development-vault/04 Prompt Lab/`; preserve rejected experiments and lessons.
- Update documentation when implementation invalidates a documented assumption.

## Source-of-truth order during rebuild

1. Explicit product decisions made for V2
2. Accepted ADRs / Current Project notes
3. Verified V2 implementation and tests
4. Verified Arcana behavior in V1
5. Arcana-specific V1 source code/data
6. Legacy documentation that agrees with the above
7. Inference/assumption

## Immediate objective

Continue product and creative-engine refinement, use the decision inbox to resolve owner choices, and maintain the shared development memory. Do not begin the vertical slice until the owners explicitly approve implementation.
