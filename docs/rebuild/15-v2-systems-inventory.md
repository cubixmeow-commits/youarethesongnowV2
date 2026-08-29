# 15 — V2 Systems Inventory & Implementation Map

**Purpose:** comprehensive but practical inventory of every major system V2 will eventually need to build or integrate.  
**Status:** implementation guidance for authorized Private Development Build 1. External and commercial launch gates remain active.
**Assessment date:** 2026-08-27  
**Companion summary:** `16-v2-build-map-summary.md`

> **Superseding decision notice, 2026-08-28:** identity, invitations, sessions, API transport, privacy, deletion, sharing, abuse protection, owner audit and initial local-storage behavior are now owner-approved in `development-vault/05 Product Design/Implementation Readiness Contract.md` and `development-vault/07 Development/Shared API, Security and Data Contract.md`. Open or proposed statements on those topics below are historical inventory notes and must not override the approved contracts.

> **Deployment decision notice, 2026-08-28:** Hostinger Premium Web Hosting, SQLite, a one-minute Cron Jobs worker, controlled `main` releases, Hostinger SMTP, local-media staging and the approved beta cost limits are defined in `development-vault/07 Development/Deployment and Operating Cost Contract.md`. VPS is the larger-deployment path.

---

## How to read this document

| Label | Meaning |
|---|---|
| **Verified from V1** | Observed in Arcana source / audits |
| **Accepted V2 direction** | Explicit owner ADR / Current Project truth |
| **Proposed** | Recommendation from analysis — not settled |
| **Needs research** | Requires owner decision, production evidence, or external policy research |

| MVP class | Meaning |
|---|---|
| **MVP Required** | First useful web product cannot ship without it |
| **MVP Useful** | Strongly improves first release; can be thin |
| **Later** | After vertical slice / web validation |
| **Open Decision** | Scope depends on unsettled product choice |
| **Legacy Only / Retire** | V1 capability not assumed for V2 |

Evidence hierarchy: accepted ADRs > Current Project notes > verified V1 behavior > research/proposals.  
`docs/rebuild/02-target-architecture.md` still sketches an older TypeScript/Next/Expo shape; **stack truth is PHP + SQLite initially + Flutter/Dart iOS second** (accepted ADRs). Treat 02 as historical proposal for domain ideas only.

---

## Accepted V2 direction (current truth)

| Decision | Source | Confidence |
|---|---|---|
| Rebuild/refine V1 functionality, not V1 code | ADR-20260827-rebuild-functionality-not-code | Accepted V2 direction |
| PHP backend / web | ADR-20260827-use-php-for-v2 | Accepted V2 direction |
| SQLite initially; portable domain design | ADR-20260827-use-sqlite-initially | Accepted V2 direction |
| Web first → Flutter + Dart iOS second; shared PHP HTTP/JSON APIs | ADR-20260827-web-first-then-flutter-ios | Accepted V2 direction |
| Staged creative engine (design direction, not implemented) | Creative Engine / Product Definition | Accepted V2 direction (pipeline shape); stage contracts still open |
| Private Development Build 1 authorized | Owners, 2026-08-28 | Current V2 direction |

---

## Cross-system dependency overview

Hidden couplings that shape design:

1. **Image generation** needs authz, credits/entitlements, queue state, provider availability, storage, and ownership metadata — not just a model call.
2. **Flutter** depends on stable JSON APIs and media URLs, not PHP templates or session-cookie-only UX.
3. **Portrait uploads** couple validation, private storage, retention, provider transmission disclosure, and retry identity policy.
4. **Billing** must fulfill via idempotent webhooks into ledger/entitlements — never “trust the client paid.”
5. **SQLite** constrains concurrent job claiming and long transactions; queue design must stay short and atomic.
6. **Prompt versions** must be recorded on generation attempts or outputs become unexplainable after edits.
7. **Credits** without reservation create V1’s race: pre-check then late debit under concurrency.

```text
Identity ──► Projects / Song sources
                 │
                 ▼
            Creative Engine ──► AI Providers
                 │                    │
                 ▼                    ▼
            Generation Jobs ◄── Credits / Entitlements
                 │                    ▲
                 ▼                    │
            Media Storage ◄── Billing (Stripe / later store)
                 │
                 ▼
              Gallery
                 ▲
API ─────────────┴──── Web UI + future Flutter
```

---

# 1. Users and authentication

**MVP:** Required · **Confidence:** Verified from V1 + Proposed V2 shape

### What V1 proved we need

- Registration, login/logout, sessions, email verification, password reset, profile, protected routes, admin flag, maintenance-mode access behavior.
- Invite-only registration via `access_codes` (consume on success).
- Bot protection (Cloudflare Turnstile) on registration.
- Account creation can succeed even if verification email fails (Observed risk).

### What V2 has decided

- Server owns authn/authz so web and Flutter share one model (Accepted — shared-backend principle).
- Exact auth vendor / invite-only / session-vs-token strategy: **not decided**.

### What V2 probably needs

| Capability | MVP class | Notes |
|---|---|---|
| Register / login / logout | MVP Required | One canonical path |
| Server sessions (web) | MVP Required | CSRF for cookie sessions |
| Password reset | MVP Required | Tokenized, rate-limited |
| Email verification | MVP Useful | Product may allow limited use before verify — open |
| Account / profile settings | MVP Required | Email/username/password change |
| Roles / admin flag | MVP Useful | Minimal admin gate |
| Account deletion | MVP Useful / Open | Privacy promise; cascade media |
| Token/API auth for Flutter | Later (design now) | Same identity store; bearer or equivalent |
| Invite-only registration | Open Decision | Legacy Only if retired |
| Turnstile / bot protection | MVP Useful | Config-driven keys |

### Future mobile auth

- Prefer opaque session or refreshable tokens issued by PHP API.
- Do not assume cookie-only auth works for Flutter.
- Store auth credentials via platform secure storage on iOS (client concern).

### Security requirements (baseline)

- Password hashing (modern algorithm), CSRF on web mutations, rate limits on auth endpoints, no secrets in client, authorization on every resource by owner or role.

### Open decisions

- Invite-only vs open registration vs waitlist.
- Must V1 credentials migrate?
- Session length / refresh policy.
- Soft vs hard delete for accounts.

---

# 2. User projects / generation records

**MVP:** Required · **Confidence:** Proposed (V1 had queue + renders, not a clean project model)

### What V1 proved

- Durable **queue row** (submission) and **render row** (result/gallery).
- Generation is async; client polls by queue id.
- No first-class multi-attempt “project” UX; history is render-centric.

### Recommended V2 concepts (separate)

| Concept | Purpose | Persist? | MVP |
|---|---|---|---|
| **Project** | User-owned container for a song creation (artist/title context, settings, gallery grouping) | Yes | MVP Useful (can thin-start as auto-created per generation) |
| **Song source** | Input snapshot: artist, title, lyrics policy, instructions, provenance | Yes (snapshot) | MVP Required |
| **Generation job** | Orchestration unit: status, request snapshot, credit reservation, engine version | Yes | MVP Required |
| **Generation attempt** | One provider try (prompt/hash, model, failure reason, timing) | Yes | MVP Required |
| **Creative artifacts** | Song DNA, narrative plan, visual identity, compiled prompt refs | Selective | MVP Required for DNA; others per decisions |
| **Generated asset** | Logical image result linked to storage object | Yes | MVP Required |
| **Favorite** | User bookmark | Yes | Later / MVP Useful thin |
| **Variation / rerun** | New job linked to parent project or prior job | Yes | Later |
| **Deletion** | Soft or hard delete of job/assets with storage cleanup | Yes | MVP Required |

### Recommendation

Treat **Project** as the user-facing owned object and **GenerationJob** as the executable unit. Do not overload one “render” row with request, attempts, storage paths, and DNA. Favorites and variations can wait if projects + jobs + assets ship first.

---

# 3. Song input and source data

**MVP:** Required · **Confidence:** Verified from V1 inputs; policy Open Decision

### V1 inputs (Observed)

- Artist/band, song title, lyrics (truncated ~16k in worker), custom instructions, aspect ratio, style key, optional portraits, t-shirt mode, admin watermark flag.
- Lyrics persisted on queue and render rows.

### V2 product direction (Accepted intent, not full contracts)

- V2 decision: **never persist raw lyrics**. Process them only in volatile memory for one analysis, then discard them immediately.
- Persist derived artifacts (interpretation / DNA) instead when possible.

### Inventory

| Element | Persist | MVP | Notes |
|---|---|---|---|
| Artist | Yes | Required | |
| Song title | Yes | Required | |
| Lyrics / input text | Resolved | Required as input path | Memory-only processing; never stored, logged, cached or backed up |
| User instructions | Snapshot on job | Required | Soft steering |
| Source provenance | Yes if multi-source | Later / Open | typed paste vs licensed vs search |
| Metadata (aspect, style, flags) | Job snapshot | Required | Immutable after enqueue |

### Unresolved legal/product (do not pretend settled)

- User-supplied lyrics vs licensed source vs hybrid.
- Retention/deletion of lyric text and provider transmission disclosure.
- Whether title-only generation is allowed without lyrics.
- Copyrighted lyric risk — **Needs research** / owner policy, not engineering fiat.

---

# 4. Creative engine

**MVP:** Required (staged core) · **Confidence:** Accepted direction for stages; contracts Proposed / Open

Current expected stages:

```text
Source context → Song interpretation → Song DNA → Visual narrative plan
  → Artist visual identity → Portrait integration → Scene composition
  → Prompt compiler → Provider adapter → Evaluation → Controlled retry
```

| Stage | Purpose | Input | Output | Persist? | Dependencies | Open decisions |
|---|---|---|---|---|---|---|
| **Source context** | Normalize song inputs | Artist, title, lyrics?, instructions | Source snapshot | Yes (snapshot) | Auth, project | Lyric retention |
| **Song interpretation** | Separate meaning from shot-making | Source | Meaning fields | Optional / Proposed | Text provider | Literal vs metaphorical; user edit? |
| **Song DNA** | Structured visual/narrative blueprint | Interpretation or source | Versioned JSON artifact | Yes | Schema + text model | Exact V2 schema vs V1 12-field |
| **Visual narrative plan** | Cinematography / scene beat before prompt | DNA | Plan artifact | Proposed yes | DNA | New vs fold into DNA |
| **Artist visual identity** | Artist-specific look (not “lore bible”) | Artist/title ± refs | Identity brief / StyleMap-like | Open | Text model or curated | Default on? Lore vs style naming |
| **Portrait integration** | Role of uploaded people as protagonists | Portraits + DNA/plan | Integration plan | Job-linked | Portrait assets, entitlements | Drop policy on retry |
| **Scene composition** | Concrete scene description | Plan + identity + portraits | Scene spec | Proposed | Prior artifacts | |
| **Prompt compiler** | Ranked, conflict-resolved provider text | All artifacts + style version | Compiled prompt + hash | Yes (hash/version) | Prompt versions | Priority contract |
| **Provider adapter** | Call text/image models | Compiled request | Bytes / JSON / errors | Attempt log | AI adapters | Vendor choice |
| **Evaluation** | Quality/safety gates | Image + policy | Pass/fail reasons | Attempt | | What is auto-evaluated |
| **Controlled retry** | Progressive demotion without silent identity loss | Prior attempt | New attempt | Yes | Retry policy | Never drop vs ask vs label |

**Priorities when portrait mode on** (Current Project direction unless owners change): identity fidelity → song meaning → scene readability → artist identity → cinematography → branding → provider flourishes.

**Legacy Only / Retire:** mega-prompt contradictions; silent portrait drop at attempt 4; marketing “lore” that is only StyleMap; multiple worker variants.

---

# 5. AI provider integration

**MVP:** Required · **Confidence:** Verified V1 used Gemini; V2 adapters Accepted direction — **not locked to Gemini**

### Abstraction needs

| Concern | Requirement |
|---|---|
| Text models | Song DNA / interpretation / identity analysis |
| Image models | Final render (+ optional eval) |
| Provider changes | Capability interfaces, not hardwired model IDs in domain |
| Provider-specific params | Adapter config (aspect, modality, JSON mime) |
| Structured JSON | Schema validate; repair policy explicit |
| Failures / refusals | Typed errors → retry state machine |
| Rate limits | Backoff, queue delay, user-safe messages |
| Cost tracking | Per-attempt tokens/images estimated cost |
| Safety refusals | Distinct from transport errors |

### Proposed capability surface

- `analyzeSongContext()` / `generateSongDna()`
- `deriveArtistVisualIdentity()` (optional path)
- `generateImage()`
- Optional later: `evaluateImage()`

Persist **provider, model id, adapter version, request hash, latency, error class** per attempt.

**Open Decision:** which providers for MVP text/image. Gemini is V1 precedent only.

---

# 6. Prompt management

**MVP:** Required (core compile + styles) · **Confidence:** Verified from V1 five-source system + Proposed governance

### V1 five sources (Verified)

1. Hardcoded source templates  
2. Runtime assembly  
3. DB-stored style prompts (`arcana_styles`)  
4. Model-generated artifacts (DNA, StyleMap)  
5. Retry mutations  

### V2 inventory

| Piece | MVP | Notes |
|---|---|---|
| Prompt specifications (contracts) | Required | Testable schemas |
| Prompt / style versions (immutable) | Required | Hash + revision history |
| Curated visual styles | Useful | Migrate from catalog/DB when recoverable |
| DB-stored styles with admin CRUD | Useful | Version, don’t only mutate live row |
| Prompt Lab experiments | Later (vault now) | `development-vault/04 Prompt Lab/` |
| Provenance on jobs | Required | Which prompt/style versions produced output |
| Rollback | Useful | Point generations at prior version |
| Provider-specific compilation | Useful | Thin adapter transforms |

**Do not** treat mutable `prompt_text` as sole history. **Do not** copy V1 strings wholesale; recover behavior as contracts.

---

# 7. Portrait / image-reference uploads

**MVP:** Open Decision for product promise; if enabled → Required plumbing · **Confidence:** Verified from V1; policy Open

### V1 (Observed)

- 1–2 portraits for paid plans; validate/resize; store under `uploads/dble/refs`; inline to Gemini; dropped at retry attempt 4 without clear user labeling.

### V2 needs

| Concern | Guidance |
|---|---|
| Upload flow | Web multipart now; iOS later same API |
| Validation | Type, size, dimensions, strip risky metadata |
| Ownership | User-owned media asset records |
| Privacy | Private by default; signed access |
| Temporary vs permanent | Prefer explicit retention TTL or delete-after-job option |
| Metadata | Dimensions, content-type, hash, created_at |
| Deletion | User delete + account delete cascades |
| Identity fidelity | Compiler + retry must prefer keeping portraits |
| Fallback | Never silent drop — ask, refuse, or label degraded result (**Open**) |
| Entitlement | Gate portrait count/feature server-side |

Web and Flutter should both call the same upload + attach-to-job APIs.

---

# 8. Generated image storage

**MVP:** Required · **Confidence:** Verified hybrid local+B2; adapter Proposed

### V1 precedent

- Local WebP + thumbnails; Backblaze B2 upload/delete/public URL helpers.
- Gallery/delete coupled to filesystem and B2 details.

### V2 inventory

| Concern | MVP | Notes |
|---|---|---|
| Local/dev filesystem adapter | Required | Fast local loop |
| Production object storage | Required for real prod | B2 is precedent, not mandatory |
| Storage abstraction | Required | App stores keys + metadata, not vendor APIs everywhere |
| Originals + thumbnails | Required | WebP or configurable formats |
| Ownership / ACL | Required | Private default |
| Deletion / cleanup jobs | Required / Useful | Orphans from failed jobs |
| CDN / signed URLs | Useful → Required at share | Public URLs only if sharing product exists |
| Provider migration | Later | Key remap / copy tool |

**Legacy risk:** public B2 URL assumptions. Prefer signed or controlled URLs until sharing is designed.

---

# 9. Background jobs / generation queue

**MVP:** Required · **Confidence:** Verified async model; SQLite claiming Proposed

### V1 lifecycle

`pending → processing → completed | failed` with worker claim, multi-attempt retries, credit debit after success.

### V2 job model

| Concern | First release | Later scale |
|---|---|---|
| Submission | Insert job + immutable snapshot | Same |
| Statuses | pending, reserved/processing, completed, failed, cancelled | + progress substages |
| Claiming | Atomic `UPDATE … WHERE status=pending` under SQLite | External queue if needed |
| Retries | Policy-driven attempts on same job | Dead-letter / admin replay |
| Timeouts | Heartbeat / stale reclaim | Same |
| Failure reason | Typed enum + safe message | Same |
| Progress | Coarse stage for UI poll | Finer events / push |
| Cancellation | Best-effort before provider call | Stronger abort |
| Priority | Optional entitlement | Multi-queue |
| Concurrency | Single worker or low N workers | Horizontal workers + Postgres/Redis |
| SQLite | WAL, short tx, careful locks | Migrate DB if contended |

**MVP:** one canonical worker pipeline per job type. **Retire:** multiple legacy processors.

---

# 10. Gallery and media library

**MVP:** Required (personal history) · **Confidence:** Verified from V1

| Feature | MVP | Notes |
|---|---|---|
| Render / asset history | Required | Own assets only |
| List / detail views | Required | |
| Search / filter / sort | Useful | Artist, song, date |
| Favorites | Later / Useful | |
| Delete | Required | DB + storage |
| Download | Required / Useful | |
| Share | Open / Later | Privacy product |
| Project grouping | Useful | |
| Public state | Open | Default private |
| Mobile gallery | Later | Same APIs |

---

# 11. Credits / usage accounting

**MVP:** Open Decision (if credits remain → Required ledger) · **Confidence:** Verified V1 race; ledger Proposed

### V1 issues (Verified)

- Balance check at enqueue; debit after successful render.
- Concurrent jobs can oversubscribe credits.
- Mutable credit columns on `users` as primary model; `credit_transactions` exist but accounting was not race-safe.

### If V2 keeps credits

| Need | Why |
|---|---|
| Append-only **credit ledger** | Source of truth |
| **Reservation** on enqueue | Prevent races |
| Finalize / capture on success | |
| **Release / refund** on failure or cancel | User trust |
| Usage history | Account UI + admin |
| Admin adjustments | Support |
| Optional cost tracking | Ops, not user currency |

**Do not** use a simple mutable counter as sole truth. Derived balance = sum(ledger) or maintained cache reconciled to ledger.

**Open:** credits vs subscription-only vs hybrid.

---

# 12. Payments and subscriptions

**MVP:** Useful for monetized launch; can follow first vertical slice · **Confidence:** Verified Stripe patterns; iOS store **Needs research**

### Inventory

| Piece | Notes |
|---|---|
| Stripe Checkout | Credit packs + subscriptions (V1 precedent) |
| Webhooks | **One** endpoint; verify signatures; store event ids for idempotency |
| Fulfillment | Ledger grants + entitlement updates only |
| Plans / products | Explicit price→entitlement mapping |
| Receipts / history | User-visible |
| Refunds / cancel-at-period-end | Support paths |

### Mobile monetization boundary

- Keep **BillingProvider** interface separate from Credits/Entitlements domain.
- Future iOS App Store purchase rules may require different purchase UX / revenue share — **Needs research**; do not hardwire “Stripe everywhere” into Flutter.
- Web Stripe and iOS IAP should both only **fulfill** into the same ledger/entitlement model.

**Retire:** dual V1 webhook implementations.

---

# 13. Plans and entitlements

**MVP:** Required thin model · **Confidence:** Verified V1 inconsistency → redesign Proposed

### Separate concepts

1. **Billing plan** — commercial product (what was purchased).  
2. **Feature entitlement** — what the account may do.  
3. **Usage balance** — credits / quotas.

### Potential gates (examples, not V1 copy)

- Portrait uploads (count / allowed)
- Styles (catalog tier)
- Aspect ratios
- Queue priority
- Images per job
- Storage retention
- Premium generation options (dynamic identity, etc.)

**Enforce server-side.** Do not copy `APPRENTICE/STARTER/CREATOR` vs `free/paid` drift. UI hiding ≠ authorization.

**Open:** free tier, which gates, V1 plan names.

---

# 14. Notifications and email

**MVP:** Useful (auth email Required if verification/reset ship) · **Confidence:** Verified PHPMailer/SMTP

| Channel | MVP | Later |
|---|---|---|
| Verification / password reset email | Required with those features | |
| Generation complete | Useful (email and/or in-app) | Push |
| Payment notices | With billing | |
| Account notices | Useful | |
| Mobile push | Later | Behind same notification interface |

Interface: domain events → `Notifier` → EmailAdapter / PushAdapter. Do not block job success on email delivery (V1 lesson).

---

# 15. Admin / operations

**MVP:** Useful thin · **Later:** full console · **Confidence:** Verified V1 admin exists; scope Proposed

| Capability | MVP | Later |
|---|---|---|
| User lookup | Useful | |
| Credit adjustments | Useful if credits | |
| Style / prompt version management | Useful | Full revision UI |
| Job inspection / failed jobs | Useful | Replay tools |
| Media cleanup | Useful (scripts OK) | UI |
| Payment lookup | With billing | |
| Maintenance mode | Useful | |
| Feature flags | Later / Useful thin | |
| Provider settings | Config/env first | Admin UI |

Avoid a huge admin product for launch. CLI/SQL + small protected pages can suffice for MVP.

---

# 16. Data model (conceptual — no SQL)

| Entity | Purpose | Relationships | Need |
|---|---|---|---|
| **users** | Identity, credentials, profile | → roles, entitlements | MVP |
| **roles / admin flags** | Authorization | users | MVP thin |
| **projects** | User creation container | user → sources, jobs, assets | MVP Useful |
| **song_sources** | Input snapshot / provenance | project | MVP |
| **song_interpretations** | Meaning layer | source | Open / Proposed |
| **song_dna** | Structured blueprint | source/job | MVP |
| **visual_narrative_plans** | Scene/cinematography plan | dna | Open / Proposed |
| **artist_visual_identities** | Artist look brief | job/project | Open |
| **portrait_assets** | Uploaded references | user, jobs | If portraits |
| **generation_jobs** | Async work unit | project, user, reservation | MVP |
| **generation_attempts** | Provider tries | job | MVP |
| **generated_assets** | Logical outputs | job, media | MVP |
| **media_assets** | Storage metadata / keys | owner user | MVP |
| **visual_styles** | Curated styles | versions | MVP Useful |
| **prompt_versions** | Immutable prompt/spec revisions | styles, compiler | MVP |
| **credit_ledger** | Append-only usage money | user, job, purchase | If credits |
| **credit_reservations** | Hold on enqueue | job, ledger | If credits |
| **purchases** | One-time checkout records | user, stripe ids | With billing |
| **subscriptions** | Recurring entitlement source | user | With billing |
| **stripe_events** | Idempotency | purchases | With billing |
| **notifications** | Delivery intent/log | user | Useful |
| **feature_entitlements** | Capability grants | user/plan | MVP thin |
| **invites** | Access codes | — | Open / Legacy |

---

# 17. PHP API layer

**MVP:** Required (JSON for web progressive enhancement and Flutter-ready) · **Confidence:** Accepted shared-backend

Capability boundaries (not final URLs):

| Boundary | Capabilities |
|---|---|
| **Auth** | register, login, logout, reset, verify |
| **Account** | profile, delete, sessions |
| **Projects** | CRUD, list |
| **Uploads** | portrait/media upload, attach |
| **Generation** | create job from project/input |
| **Job status** | get status/progress/result |
| **Gallery / assets** | list, detail, delete, download URL |
| **Credits** | balance, history |
| **Payments** | checkout session create; webhook separate |
| **Styles** | list active styles / metadata |
| **Entitlements** | what features are allowed |
| **Admin** | gated subset |

Web may also use server-rendered pages, but **domain mutations and mobile-critical reads should be available as JSON** from day one so Flutter is not a rewrite.

---

# 18. Web frontend

| Screen | MVP | Wait |
|---|---|---|
| Landing | Useful | Heavy marketing |
| Register / login | Required | |
| Dashboard / home | Useful | |
| Generator | Required | |
| Generation progress | Required | |
| Result view | Required | |
| Gallery | Required | Advanced search |
| Account | Required | |
| Billing | With monetization | |
| Help / legal | Useful | |
| Admin | Thin | Full console |

Obsolete V1: dedicated mobile-redirect landing behavior.

---

# 19. Flutter / iOS client

**Build:** Later · **Backend readiness:** design from day one · **Confidence:** Accepted delivery path

Backend should support from web MVP:

- JSON request/response contracts  
- Non-cookie auth usable by mobile  
- Multipart media upload  
- Job create + poll (push hooks later)  
- Gallery list/detail/delete/download  
- Account + entitlement + credit state  
- Stable error codes  

Client owns: onboarding UX, camera/roll picks, share sheet, secure token storage, future store purchases UI.

**Do not build Flutter during freeze / first web phases.**

---

# 20. Security / privacy

| Area | Guidance | MVP |
|---|---|---|
| CSRF | Web cookie sessions | Required |
| Sessions / tokens | Clear web vs API strategy | Required |
| Upload validation | Type/size/content | Required if uploads |
| Authorization | Owner checks everywhere | Required |
| Private media | Signed URLs / auth gate | Required |
| Secrets | Env only | Required |
| Provider keys | Server-only | Required |
| Stripe verification | Signature + idempotency | With billing |
| Abuse / rate limits | Auth + generate + upload | Useful → Required |
| Logging redaction | No raw lyrics/images in logs by default | Required |
| Data deletion | Account/media/job policy | Useful / Open |

---

# 21. Logging / observability

Keep appropriate for a small app:

| Signal | Persist / ship |
|---|---|
| Application errors | Yes |
| AI requests | Metadata only (model, latency, status); redact payloads |
| Generation attempts | Yes (first-class) |
| Provider failures | Typed | 
| Payment webhook events | Yes |
| Job timings | Yes |
| Admin diagnostics | Job + user id lookup |

Avoid full APM sprawl before product exists; structured logs + DB attempt rows are enough initially.

---

# 22. Deployment and operations

| Concern | Local | Initial production | Later scale |
|---|---|---|---|
| PHP app | Built-in / Apache/nginx | Modest PHP host | Same or containers |
| Worker / cron | CLI loop locally | Cron or long-running worker | Multiple workers |
| SQLite file | Dev path | Backed-up volume | Possible PG/MySQL move |
| Backups | Optional | DB + media schedule | Tested restores |
| Object storage | Local adapter | B2 or other | CDN |
| Env config | `.env` example | Host secrets | |
| Email | Mailtrap/dev sink | SMTP provider | |
| Stripe | Test mode | Live + webhook | |
| AI APIs | Dev keys / mocks | Production keys | Multi-provider |
| Logs | Files | Host logs | Aggregation |
| Migrations | From day one of coding | Apply on deploy | |

V1 Hostinger/XAMPP/MySQL assumptions are **Legacy Only** as requirements.

---

# 23. Testing

| Area | Priority | Why |
|---|---|---|
| Auth / authorization | High | Ownership bugs = privacy incidents |
| Credit reservation / ledger | High | Money + V1 race history |
| Webhook idempotency | High | Double grants |
| Job state transitions / claim | High | SQLite concurrency |
| Prompt contract / JSON schema | High | Creative engine reliability |
| Provider adapters (fake) | Medium–High | Swap vendors safely |
| Upload validation | Medium | Abuse |
| Media ownership | High | |
| Retry policy | High | Portrait identity promise |

Test creative contracts with fixtures before live providers. Prefer deterministic fake adapters in CI.

---

## Classification matrix (systems rollup)

| System | MVP class | Confidence |
|---|---|---|
| Users / Auth | MVP Required | Verified + Proposed |
| Projects / Jobs / Assets | MVP Required | Proposed from V1 gap |
| Song input | MVP Required | Verified; policy Open |
| Creative engine core | MVP Required | Accepted direction |
| Full narrative/identity stages | Open / Useful | Proposed |
| AI providers | MVP Required | Accepted adapters |
| Prompt management | MVP Required | Verified + Proposed |
| Portraits | Open Decision | Verified + Open |
| Media storage | MVP Required | Verified + Proposed |
| Job queue | MVP Required | Verified + Proposed |
| Gallery | MVP Required | Verified |
| Credits | Open Decision | Verified race |
| Billing | MVP Useful (launch $) | Verified Stripe |
| Entitlements | MVP Required thin | Proposed |
| Notifications | MVP Useful | Verified |
| Admin | MVP Useful thin | Proposed |
| PHP JSON API | MVP Required | Accepted |
| Web UI | MVP Required | Accepted web-first |
| Flutter client | Later | Accepted path |
| Security baseline | MVP Required | Proposed |
| Ops / deploy | MVP Required at launch | Proposed |
| Testing harness | MVP Required with code | Proposed |
| Invite-only / dual webhooks / multi-workers / mobile redirect | Legacy Only / Retire | Verified |

---

## Biggest unresolved decisions (pre-implementation)

1. Credits vs subscription vs hybrid; entitlement gates.  
2. Lyric source, persistence, and legal policy.  
3. Portrait promise and retry identity behavior.  
4. Artist visual identity default on/off; “lore” terminology.  
5. User-visible/editable DNA / narrative plan?  
6. Sharing / public media.  
7. V1 user/media/credit migration necessity.  
8. AI provider selection for MVP.  
9. Object storage provider for production.  
10. iOS monetization approach (research App Store rules when mobile nears).

## Biggest architectural risks

1. Re-encoding V1’s multiple workers/webhooks as “truth.”  
2. Credit races if ledger/reservation skipped.  
3. SQLite job claiming under premature multi-worker load.  
4. Cookie-only auth blocking Flutter.  
5. Storage/path coupling blocking private media and provider moves.  
6. Prompt logic trapped again in worker strings.  
7. Silent portrait drops violating product promise.  
8. Migrating V1 data from Git assumptions without production schema dump.

---

## Related documents

- Summary build map: `16-v2-build-map-summary.md`  
- V1 evidence: `06`–`10`, `13`–`14`  
- Open creative choices: `12-open-creative-decisions.md`  
- Vault: Product Definition, Creative Engine, Decision Inbox, accepted ADRs
