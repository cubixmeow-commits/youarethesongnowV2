---
type: current-project
status: active
updated: 2026-09-01
area: architecture
---

# Current Architecture

## Status

**Private Development Build 1 is implemented and deployed on Hostinger for owner-only testing.** External beta and commercial launch remain gated. Luminous Night Studio Phase 1 added runtime semantic tokens, a private owner-only `/owner/component-lab`, and an Explore presentation that uses canonical direction/button/status classes without changing `/api/v1` contracts.

Implemented shape:

```text
public/                 # web document root
src/                    # Auth, Api, Credits, Generation, CreativeEngine, Storage, ...
database/migrations/    # versioned SQLite schema
worker/run.php          # one-minute bounded cron worker
bin/console.php         # migrate, seed-owner, seed-styles, worker, setup-status
tests/run.php           # Build 1 automated checks
var/                    # private SQLite, media and logs (gitignored)
```

Development substitutes identify themselves honestly when Stripe/SMTP/AI credentials are absent. Live payments, public registration and external users remain configuration-gated off.

Current deployed creative path uses Gemini Interactions (`gemini-3.6-flash`, Google Search + strict schema, `store=false`) for transient private-development Song DNA and native Gemini image generation with inline portrait references. Hostinger SMTP, Stripe test checkout, the one-minute worker and local private media storage are active in the private test environment.

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

## Product-interface architecture

The approved client design architecture is **mobile-canonical and adaptive**:

```text
Compact: top/focused header + one task + optional bottom navigation
Medium: same task/state with wider comparison or artwork
Expanded: navigation rail + main workspace + optional context panel
```

Luminous Night Studio is the visual baseline. Web components and later Flutter widgets share semantic tokens, state contracts, screen intent, accessibility and `/api/v1` behavior rather than markup/CSS. The design OS lives under `docs/design/`; canonical non-runtime assets live under `assets/design/`.

Flutter remains deferred. Backend business rules and privacy stay on PHP; no client duplicates credits, membership, authorization, creative-engine, sharing or deletion policy.

## Related decisions

- [[../02 Decisions/ADR-20260827-rebuild-functionality-not-code]]
- [[../02 Decisions/ADR-20260827-use-php-for-v2]]
- [[../02 Decisions/ADR-20260827-use-sqlite-initially]]
- [[../02 Decisions/ADR-20260827-web-first-then-flutter-ios]]
- [[../02 Decisions/ADR-20260831-luminous-night-studio-baseline]]
