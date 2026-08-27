---
type: decision
status: accepted
date: 2026-08-27
area: architecture
---

# ADR — Use PHP for V2

## Context

The project owners want V2 to remain in PHP while modernizing the architecture and creative engine.

## Decision

Use PHP as the V2 backend/web application language.

## Consequences

- Earlier TypeScript/Next-oriented architecture notes are superseded as implementation direction.
- Modernization should happen through cleaner boundaries, tests, queueing, provider adapters and data design rather than a language rewrite.
- Mobile can later consume PHP JSON endpoints if/when a native client is built.
