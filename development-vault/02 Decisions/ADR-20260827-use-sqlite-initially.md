---
type: decision
status: accepted
date: 2026-08-27
area: architecture
---

# ADR — Use SQLite initially

## Context

V2 is expected to begin small. The project benefits from low operational overhead, easy local development, and simple portability while the product and creative engine are still being validated.

## Decision

Use SQLite as the preferred initial database for V2.

## Consequences

- Keep transactions short and design job claiming carefully once implementation starts.
- Prefer portable SQL/domain logic so migration to MySQL/PostgreSQL remains possible later.
- Use migrations and backups from the beginning.
- Do not commit runtime database files to Git.
- Revisit this decision if concurrency, availability, operational, or reporting needs justify a server database.
