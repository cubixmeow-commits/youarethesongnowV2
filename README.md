# YouAreTheSongNow V2

A clean rebuild of the original **AISaga Arcana / YouAreTheSongNow.com** project as a modern web and mobile application.

This repository intentionally starts clean. The legacy repository, `cubixmeow-commits/youarethesongnow`, is a reference for product behavior, data semantics, prompts, integrations, media, and business rules. Legacy implementation code should not be copied into V2 by default.

## Start here

- `development-vault/START HERE.md` — shared working memory / current direction
- `AGENTS.md` — working rules for Cursor and other coding agents
- `docs/index.html` — **Meow Control**, CuBiX Meow & Brut daily command center (GitHub Pages)
- `docs/rebuild/README.md` — assessment and planning index

## Current status

**Phase: Private Development Build 1 authorized. External beta and commercial launch remain gated.**

Accepted direction so far:

- rebuild/refine **V1 functionality**, not V1 code;
- backend: **PHP**;
- initial database: **SQLite**.
- web client: **plain HTML/CSS/JavaScript where practical**;
- background generation: **bounded PHP worker with explicit job state**.

Cursor and other coding agents should begin with `CURSOR-BUILD-1.md` and `AGENTS.md`.

See accepted ADRs in `development-vault/02 Decisions/`.
