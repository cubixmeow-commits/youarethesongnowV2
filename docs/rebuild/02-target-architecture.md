# 02 — Provisional Target Architecture

This is a **proposal**, not a locked decision. Validate it with the V1 audit and the first vertical slice.

## Recommended shape

Use a TypeScript-oriented monorepo so web/mobile can share domain contracts without forcing the two UIs to be identical.

```text
apps/
  web/              # web client
  mobile/           # iOS/Android client
  api/              # authenticated server/API
  worker/           # long-running generation/media jobs
packages/
  domain/           # core entities and business rules
  contracts/        # API schemas/types
  api-client/       # shared client
  ai/               # model/provider interfaces and adapters
  config/           # validated shared configuration
  ui-tokens/        # design tokens; optional shared primitives
docs/
  rebuild/
  adr/
```

Candidate implementations are React/Next.js for web and React Native/Expo for mobile, but those should become decisions only after a short spike verifies the desired UX, camera/upload/share requirements, hosting, and deployment workflow.

## Backend responsibilities

The server should own:

- identity and authorization;
- projects and generation records;
- credit ledger and entitlements;
- payment/webhook validation;
- provider credentials;
- prompt/orchestration policy;
- signed media access and ownership;
- generation job creation/state;
- moderation/policy enforcement;
- audit/event records needed for money and generation debugging.

## Generation architecture

```text
Web / Mobile
    -> API: create generation
    -> durable generation record (queued)
    -> job queue
    -> worker
    -> Arcana orchestration/domain layer
    -> text/image provider adapter(s)
    -> object storage
    -> generation record (complete/failed)
    -> clients poll/subscribe and render result
```

Never make a model-provider HTTP call the durable source of generation state.

## Data model — Proposed starting entities

- User
- Project
- Source/SongContext
- Upload/MediaAsset
- Arcana/Narrative artifact
- Generation
- GenerationAsset
- CreditLedgerEntry
- Purchase/Entitlement
- Share/Export record

Names can change after the V1 schema audit. Prefer append-only ledger semantics for credits rather than a mutable balance as the only source of truth.

## Storage

- Relational database (PostgreSQL is a strong default) for accounts, projects, states, ledgers, metadata and references.
- Object storage for original uploads and generated media.
- Queue/durable job mechanism for generation work.
- CDN/signed URLs as appropriate for media delivery.

## Provider boundaries

Define capabilities such as narrative generation, prompt construction, image generation, metadata lookup, email and payments behind adapters. Application/domain code consumes capabilities; vendor SDKs remain at integration edges.

## Security baseline

- Secrets server-side only.
- Authorization check on every project/media operation.
- Validate upload type/size and strip unsafe metadata where appropriate.
- Verify payment webhooks and make handlers idempotent.
- Record generation debit/refund behavior transactionally.
- Do not expose raw provider errors or keys to clients.
- Support deletion/privacy requirements deliberately rather than as filesystem side effects.

## What not to decide yet

Do not lock the database schema, hosting provider, queue vendor, auth vendor, AI vendor mix, subscription model, or exact shared-UI strategy until the V1 audit and first technical spike provide evidence.
