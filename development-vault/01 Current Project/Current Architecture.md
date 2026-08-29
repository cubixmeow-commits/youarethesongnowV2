---
type: current-project
status: active
updated: 2026-08-28
area: architecture
---

# Current Architecture

## Status

**Approved direction for Private Development Build 1. Implementation is beginning.**

## Delivery path

**Phase 1 — Web application**

- PHP backend and web application.
- SQLite initially while V2 is small.
- Plain HTML/CSS/JavaScript where practical.
- Background PHP worker for long-running generation jobs.
- Clean HTTP/JSON endpoints so the backend can later serve mobile.

**Phase 2 — iOS mobile application**

- Flutter + Dart.
- iOS first.
- Consume the same PHP backend rather than duplicate creative/business logic in the client.

The web rebuild is not a disposable prototype. It establishes the shared backend, domain behavior and API contracts for the later Flutter client.

The owner-approved shared-service specification is maintained in `development-vault/07 Development/Shared API, Security and Data Contract.md`. It defines the versioned resource surface, web and Flutter authentication transports, authorization, idempotency, job states, stable errors, media privacy and deletion behavior. It governs Private Development Build 1.

## Current preferred stack

- PHP for the V2 backend and web application.
- SQLite as the initial database while V2 is small.
- Plain HTML/CSS/JavaScript where practical.
- Flutter + Dart for the later iOS client.
- Background PHP worker for long-running generation jobs.
- Provider adapters so creative logic is not hardwired to Gemini or any single vendor.
- Object/media storage abstracted from application records.

## Architectural principle

**Rebuild V1 functionality, not V1 code.**

The V1 repository is useful for understanding user-visible behavior, creative logic, data semantics, retries, payments and media handling. Its file layout and implementation patterns are not the V2 architecture.

## Shared-backend principle

Business rules should live on the PHP server so web and Flutter clients share one implementation of:

- authentication/authorization;
- project and generation state;
- creative-engine orchestration;
- prompt compilation and provider calls;
- retry/quality/safety policy;
- credits/entitlements and payment fulfillment;
- media ownership/storage metadata;
- gallery/history.

The Flutter client should focus on native mobile UX: onboarding, creation controls, portrait uploads, progress/status, gallery, sharing/export and platform-appropriate purchase flows.

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
  Api/
worker/
database/
templates/
config/
docs/
development-vault/

# later client
mobile/
  Flutter / Dart iOS app
```

This is a planning sketch, not authorization to scaffold it yet.

## SQLite guidance

SQLite is preferred initially because it reduces operational complexity for a small product and works well for local development and modest production workloads. Keep domain logic portable so a future move to MySQL/PostgreSQL is possible without rewriting the creative engine.

Potential future measures once implementation begins: WAL mode, short transactions, atomic job claiming, append-only credit ledger, migrations, backups, and explicit database locking/error handling.

## Related decisions

- [[../02 Decisions/ADR-20260827-rebuild-functionality-not-code]]
- [[../02 Decisions/ADR-20260827-use-php-for-v2]]
- [[../02 Decisions/ADR-20260827-use-sqlite-initially]]
- [[../02 Decisions/ADR-20260827-web-first-then-flutter-ios]]
