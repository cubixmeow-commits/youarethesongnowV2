---
type: decision
status: accepted
date: 2026-08-27
area: product-architecture
---

# ADR — Rebuild functionality, not V1 code

## Context

V1 contains valuable product behavior and creative-engine ideas, but also duplicated workers, legacy PHP structure, inconsistent enforcement, runtime schema evolution, and stale/contaminated documentation.

## Decision

V2 will recreate and refine the valuable **functionality and behavior** of V1 without porting the V1 implementation structure or copying it wholesale.

## Consequences

- V1 remains a behavioral reference and test oracle.
- New V2 code should be organized around current product responsibilities.
- Legacy file boundaries, workers, webhook duplication and schema hacks are not requirements.
- Any copied prompt/business behavior must be justified intentionally.
