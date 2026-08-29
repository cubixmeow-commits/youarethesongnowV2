# AGENTS.md — YouAreTheSongNow V2

This file is the entry point for Cursor and other coding agents working in this repository.

## Project intent

YouAreTheSongNow V2 is a clean web + mobile rebuild of AISaga Arcana. The legacy repository is `cubixmeow-commits/youarethesongnow`.

V1 is a **behavioral and product reference**, not a template to port wholesale.

## CURRENT BUILD AUTHORIZATION

**CuBiX Meow and Brut authorized Private Development Build 1 on 2026-08-28. Application scaffolding, implementation, migrations, local tests and protected sandbox integrations are now allowed.**

Build 1 may use PHP, SQLite, local private media storage, Stripe test mode, configurable development credits and protected server-side AI-provider credentials.

Build 1 must not enable:

- public registration or external tester access;
- live Stripe charges;
- commercial processing of protected lyrics;
- client-side provider credentials;
- unrestricted provider spending;
- final provider, quality-tier or credit claims before benchmarking.

Raw lyrics are memory-only. Never write them to Git, SQLite, queues, temporary files, prompt histories, logs, analytics, errors or backups.

## Shared development vault

The project has a shared Obsidian vault at `development-vault/` for working development memory. GitHub is the shared sync/source-of-truth mechanism for the vault.

Before planning or future implementation work, always read:

1. `development-vault/START HERE.md`
2. `development-vault/01 Current Project/Current Priorities.md` (when present)
3. `development-vault/01 Current Project/Current Architecture.md`
4. `development-vault/02 Decisions/Decision Index.md`

For Build 1, also read:

- `CURSOR-BUILD-1.md`
- `development-vault/05 Product Design/First Build Feature Contract.md`
- `development-vault/05 Product Design/V2 Song DNA and Prompt Pipeline Contract.md`
- `development-vault/05 Product Design/Onboarding and First-Creation Paywall Contract.md`
- `development-vault/05 Product Design/V2 Visual Design Direction.md`
- `development-vault/07 Development/Shared API, Security and Data Contract.md`
- `development-vault/07 Development/Deployment and Operating Cost Contract.md`
- `development-vault/07 Development/Acceptance Test Contract.md`

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

## Meow Control dashboard (`/docs`)

`docs/index.html` is the **Meow Control** GitHub Pages command center for CuBiX Meow and Brut.

- `development-vault/` = workshop / working memory
- `/docs` = polished daily project hub

When Current Project truth changes in a way the dashboard should reflect:

1. update the relevant vault notes;
2. update `development-vault/01 Current Project/Dashboard Snapshot.md` if present;
3. update `docs/dashboard-data.js` and/or `docs/index.html` so Meow Control stays aligned.

Do **not** invent a second vault. Sync/rebase onto current `main` before assuming vault files are missing.

Keep `/docs` static and GitHub Pages compatible: HTML/CSS/vanilla JS only. No frontend frameworks, no npm build for the dashboard.

## Current accepted direction

- Rebuild/refine **V1 functionality**, not V1 implementation.
- Use **PHP** for V2.
- Use **SQLite initially** while the product is small, keeping domain/data design portable.
- Preserve the staged creative-engine idea while refining Song DNA, narrative planning, artist visual identity, portrait handling and retries.
- Private Development Build 1 is authorized. External beta and commercial launch gates remain active.

See the accepted ADRs in `development-vault/02 Decisions/`.

## Before changing code

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

Implement the private web Build 1 as a small end-to-end vertical slice, following `CURSOR-BUILD-1.md`. Do not stop after planning or broad scaffolding. Produce a runnable, tested local application while keeping unfinished external-release gates disabled.
