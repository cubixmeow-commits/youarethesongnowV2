# 07 — V1 Data & Integrations Audit

**Purpose:** record the V1 storage, integration, and state model before designing V2 persistence.

**Status:** assessment only. This is not a V2 schema.

## Known V1 data/state surfaces

The source-derived inventory references the following relational tables or logical stores:

- `users`
- `access_codes`
- `arcana_queue`
- `arcana_renders`
- `arcana_styles`
- `stripe_products`
- `stripe_checkout_sessions`
- `credit_transactions`
- `user_subscriptions`

Additional columns/tables may exist in production but are not fully defined in repository migrations.

## `users`

Observed responsibilities are broad and overloaded. The row appears to hold:

- identity/login fields;
- email verification state;
- password/reset-related state;
- `plan_type` or equivalent entitlement state;
- `image_credits`;
- `story_credits`;
- references elsewhere to upscale/video credits;
- administrator/exception-related state in supporting code.

### V1 concern

Authentication, billing entitlement, credits, and feature access are concentrated on the user row. This makes historical accounting and plan evolution difficult to reason about.

### V2 recommendation

Separate durable concepts:

- identity/profile;
- subscription/entitlement;
- append-only credit ledger;
- computed/current balance;
- role/admin authorization.

Do not migrate V1 columns one-for-one without first understanding their actual production values.

## `access_codes`

Used by invite-only registration. A valid invite is consumed/deleted when registration succeeds.

### Uncertainty

The repository references this table but the source-derived audit did not find a definitive in-repo `CREATE TABLE` for it. Production schema may have been maintained manually.

### V2 question

Decide whether invite-only registration is still part of the product. If not, this is historical data only.

## `arcana_queue`

This is the durable handoff between user submission and generation workers.

Observed state includes or implies:

- user ownership;
- band/artist;
- song;
- lyrics/context;
- custom instructions;
- aspect ratio;
- selected image style / dynamic style key;
- portrait-reference paths/URLs;
- pending/processing/completed/failed status;
- generated result linkage and/or error state;
- queue timing/priority fields in some worker paths.

### V1 strength

The queue establishes asynchronous generation as a first-class concept.

### V1 concern

Multiple submission paths and worker variants mean the exact row shape and validation rules evolved over time.

### V2 recommendation

Model a durable `Generation`/`GenerationJob` record with:

- immutable request snapshot;
- orchestration version;
- provider/model attempts;
- state transitions;
- ownership;
- debit reservation/finalization state;
- error category;
- resulting artifact references.

## `arcana_renders`

Represents completed generation results and powers the user gallery.

Observed/derived responsibilities include:

- user ownership;
- band/song metadata;
- Song DNA and/or generation metadata;
- selected image style;
- generated file path/URL;
- thumbnail/path metadata;
- portrait/context references in some flows;
- timestamps used by gallery sorting/pagination.

### V2 recommendation

Separate the conceptual result from its physical files:

- `Project` / creation context;
- `Generation` attempt;
- `Artifact` or `GenerationAsset`;
- storage object metadata.

That allows regeneration, multiple outputs, future video/story modes, and provider changes without overloading one render row.

## `arcana_styles`

Used for static visual style data consumed by the generator/worker.

### V2 recommendation

Treat styles as versioned product configuration/domain content. Do not embed the only copy of a style prompt in UI code or a mutable database row without version history.

## Stripe-related data

Observed logical tables:

- `stripe_products`
- `stripe_checkout_sessions`
- `credit_transactions`
- `user_subscriptions`

Observed behaviors:

- Checkout session creation;
- one-time credit packs;
- subscription-mode products;
- webhook credit grants;
- subscription status updates;
- cancel-at-period-end management.

### Important V1 ambiguity

Two separate Stripe webhook paths exist:

- SDK-based `arcana.stripe.endpoint.php` / `stripe_lib.php` flow;
- lightweight `webhook.php` checkout-session flow with manual HMAC verification.

The repository does not prove which endpoint the production Stripe dashboard used.

### V2 recommendation

Use one canonical webhook endpoint and store processed Stripe event IDs for idempotency. Payment fulfillment should write auditable ledger/entitlement records rather than directly mutating balances without history.

## Filesystem storage

Observed local paths:

- `uploads/dble`
- `uploads/dble/refs`

Observed uses:

- generated WebP output;
- thumbnails;
- portrait-reference uploads;
- possible local backup retention alongside B2.

### V2 concern

A filesystem path is not a durable cross-platform media identity. Web/mobile needs media ownership and URL issuance independent of any server filesystem.

## Backblaze B2

Observed environment/config concepts:

- `B2_KEY_ID`
- `B2_APP_KEY`
- `B2_BUCKET_NAME`
- `B2_BUCKET_ID`
- `B2_ENDPOINT`
- `B2_REGION`
- `B2_PUBLIC_URL`

Observed helpers:

- authorize;
- upload;
- delete;
- derive public URL.

### V2 recommendation

Keep storage behind an adapter. B2 may remain perfectly viable, but V2 should depend on object-storage semantics rather than B2-specific calls throughout application code.

## Gemini integration

Observed V1 text model:

- `gemini-2.0-flash-exp`

Observed V1 image model:

- `gemini-2.5-flash-image`

Observed uses:

1. Song DNA analysis;
2. optional Dynamic Band StyleMap analysis;
3. image generation;
4. safety/retry fallback behavior.

### V2 recommendation

Define provider-neutral capabilities, for example:

- `analyzeSongContext()`;
- `deriveVisualStyle()`;
- `generateImage()`.

Persist model/provider/version used per attempt so output behavior remains explainable after models change.

## Email / SMTP

Observed stack:

- PHPMailer;
- SMTP configuration;
- verification email;
- password-related transactional mail;
- gallery/render email functionality;
- some `mail()` fallback behavior.

Likely environment concepts include SMTP host, port, username, password, encryption, from email and from name.

### V2 recommendation

Use one mail abstraction and one template system. Persist important delivery intent/events where it matters, but do not tie application success to synchronous email delivery unless required.

## Cloudflare Turnstile

Observed in registration for bot protection.

### V1 concern

The audit notes a hard-coded site key in registration HTML while the secret is environment/config driven.

### V2 recommendation

Keep both public and secret integration settings environment/config controlled.

## Apache / PHP deployment assumptions

Observed V1 infrastructure assumptions include:

- PHP 8.2;
- Apache / `.htaccess`;
- XAMPP local development;
- Hostinger production;
- cron/CLI PHP worker execution;
- MySQL/MariaDB.

### V2 disposition

These are historical constraints, not rebuild requirements.

## Production-schema uncertainty

A recurring V1 pattern is runtime code checking/adding or assuming columns while formal migrations are incomplete or absent. Source-derived notes specifically flag uncertainty around:

- `access_codes` table creation;
- `users.plan_type` creation;
- Stripe-related schema;
- upscale/video credit fields;
- exact production cron target.

Therefore, **the Git repository alone is not sufficient to define a safe V1 data migration**.

Before migrating real users, we should obtain a schema-only dump of the live/last production database and compare it to source expectations. No data needs to be copied during the planning phase.

## Candidate V1 → V2 data mapping

This is deliberately conceptual:

| V1 | Candidate V2 concept | Notes |
|---|---|---|
| `users` | User + Profile + Entitlement + Ledger | Split responsibilities |
| `access_codes` | Invite | Only if invite system survives |
| `arcana_queue` | Generation / Job | Preserve request snapshot and status history |
| `arcana_renders` | Artifact / GenerationAsset | Separate logical result from storage |
| `arcana_styles` | StyleDefinition | Versioned content/config |
| credit columns | CreditLedgerEntry + balance | Ledger becomes source of truth |
| `user_subscriptions` | Subscription/Entitlement | Provider identifiers stored explicitly |
| `credit_transactions` | Ledger/payment fulfillment evidence | Reconcile with Stripe events |
| filesystem/B2 path | MediaAsset / StorageObject | Ownership + storage key abstraction |

## Migration categories to decide later

Every V1 dataset should eventually be classified as one of:

- **must migrate** — users would reasonably expect continuity;
- **optional archive** — useful history but not essential for launch;
- **reconstruct** — derive from stronger source data;
- **do not migrate** — obsolete implementation state;
- **unknown** — requires production-schema/data inspection.

No migration scripts should be written until those decisions are made.
