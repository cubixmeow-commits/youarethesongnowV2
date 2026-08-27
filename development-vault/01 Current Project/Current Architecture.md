---
type: current-project
status: active
updated: 2026-08-27
area: architecture
---

# Current Architecture

## Status

**Direction only. Not implemented. Build freeze active.**

## Current preferred stack

- PHP for the V2 backend and web application.
- SQLite as the initial database while V2 is small.
- Plain HTML/CSS/JavaScript where practical.
- Background PHP worker for long-running generation jobs.
- Provider adapters so creative logic is not hardwired to Gemini or any single vendor.
- Object/media storage abstracted from application records.

## Architectural principle

**Rebuild V1 functionality, not V1 code.**

The V1 repository is useful for understanding user-visible behavior, creative logic, data semantics, retries, payments and media handling. Its file layout and implementation patterns are not the V2 architecture.

## Likely V2 shape

```text
public/
src/
  Auth/
  Projects/
  CreativeEngine/
  Generation/
  AI/
  Credits/
  Payments/
  Storage/
worker/
database/
templates/
config/
docs/
development-vault/
```

This is a planning sketch, not authorization to scaffold it yet.

## SQLite guidance

SQLite is preferred initially because it reduces operational complexity for a small product and works well for local development and modest production workloads. Keep domain logic portable so a future move to MySQL/PostgreSQL is possible without rewriting the creative engine.

Potential future measures once implementation begins: WAL mode, short transactions, atomic job claiming, append-only credit ledger, migrations, backups, and explicit database locking/error handling.

## Related decisions

- [[../02 Decisions/ADR-20260827-rebuild-functionality-not-code]]
- [[../02 Decisions/ADR-20260827-use-php-for-v2]]
- [[../02 Decisions/ADR-20260827-use-sqlite-initially]]
