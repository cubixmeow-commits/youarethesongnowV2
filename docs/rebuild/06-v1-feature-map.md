# 06 — V1 Feature Map

**Purpose:** document what AISaga Arcana V1 actually does before any V2 implementation begins.

**Legacy source:** `cubixmeow-commits/youarethesongnow`  
**Assessment date:** 2026-08-27  
**Status:** planning/evidence only — no V2 implementation implied

## Executive summary

V1 is not merely a single image generator. It is a complete account-gated creative system with authentication, plans, credits, queued AI generation, portrait-reference uploads, dynamic/static visual styles, result persistence, gallery management, Stripe purchases/subscriptions, email, notifications, admin tools, and mixed local/B2 media storage.

The strongest architectural idea worth preserving is the separation between **submission** and **generation**: the user-facing generator writes a pending queue row, and background workers later perform analysis, prompt creation, image generation, persistence, notification, and credit consumption.

The implementation itself should not be ported wholesale because multiple legacy paths overlap and some enforcement/data assumptions are inconsistent.

## Capability inventory

The source-derived V1 inventory groups Arcana into eight major areas and 36 named capabilities.

### 1. Accounts & authentication

Observed capabilities:

- account registration;
- login/logout;
- email verification;
- password reset;
- account profile;
- protected-page access checks;
- maintenance-mode access behavior.

Registration is invite-only in the traced flow. The form uses username, email, password, invite code, CSRF protection, Cloudflare Turnstile, and email verification. Account creation can succeed even if verification email delivery fails.

**V2 disposition:** preserve the account concepts, but re-decide whether invite-only registration is still wanted. Do not copy the PHP/session implementation.

### 2. Plans & credits

Observed plan model:

- `APPRENTICE`;
- `STARTER`;
- `CREATOR`;
- older code also refers directly to `free` / `paid`.

Observed plan-gated capabilities include some combination of:

- portrait references;
- multiple aspect ratios;
- visual styles;
- priority queueing;
- permanent gallery behavior;
- upscaling;
- poster-store access;
- monthly-credit hints.

Observed credit types include at least:

- image credits;
- story credits;
- references to upscale/video credits also exist, but their schema is not fully evidenced.

Generation checks for available image credit before enqueue. Credit is deducted only after a successful render has been inserted. If deduction then fails, the worker deletes the render and marks the job failed.

**V2 disposition:** preserve the user-facing idea of entitlements/credits if desired, but redesign accounting around an auditable ledger and server-side entitlement checks.

### 3. Image-generation input experience

The main generator accepts:

- band/artist;
- song;
- lyrics;
- custom instructions;
- aspect ratio;
- image style;
- optional portrait reference image 1;
- optional portrait reference image 2;
- t-shirt mode;
- an admin-only no-watermark option.

The primary generator does **not** call Gemini synchronously. It validates the request, writes an `arcana_queue` row with pending status, returns a `queue_id`, and the browser polls for status.

Alternate generator/submission paths also exist, including batch/CSV/older queue paths. These do not all appear to enforce rules identically.

**V2 disposition:** preserve one canonical generation-submission contract. Retire duplicate submission paths unless a specific batch product need survives discovery.

### 4. Song / lyric analysis

The worker sends band, song, and lyrics to Gemini text generation to create structured **Song DNA**.

Observed behavior:

- lyrics are truncated, with 16,000 characters seen in worker behavior;
- the output follows a fixed JSON structure;
- fields cover themes, mood, palette, camera/composition and related visual interpretation data;
- the best-evidence parallel worker sends multiple analysis calls with `curl_multi`;
- older workers contain JSON-repair retry behavior.

Observed text model in this version of V1:

- `gemini-2.0-flash-exp` via Google Generative Language `generateContent`.

**V2 disposition:** Song DNA is one of the most important V1 concepts to recover precisely. It should become an explicit domain artifact/schema rather than an implementation detail buried inside a worker prompt.

### 5. Cinematic prompt construction

After Song DNA succeeds, the worker constructs a long image prompt using:

- Song DNA fields;
- band/song identity;
- selected aspect framing;
- custom instructions;
- static or dynamic style instructions;
- portrait-reference handling;
- branding/watermark directives;
- safety-oriented prompt constraints.

The prompt is therefore a second-stage transformation: **song context → Song DNA → final cinematic image prompt**.

**V2 disposition:** preserve this staged concept, but separate prompt templates/rules from worker plumbing so they can be versioned and tested.

### 6. Static and Dynamic Band Style

V1 has both static style support and a special Dynamic Band Style path.

The dynamic path is triggered when `image_style = ANALYZE_BAND_STYLE`. The worker performs additional Gemini text analysis to create a band-specific **StyleMap**, then incorporates that into the final image prompt. If style analysis fails, a canned StyleMap can be used as fallback.

Important uncertainty: the worker supports the dynamic style path, but the traced main generator UI does not clearly expose `ANALYZE_BAND_STYLE` as its default/end-to-end selection. That makes the feature implemented in backend logic but only partially verified as a normal user journey.

**V2 disposition:** treat the Dynamic Band Lore/Style idea as a core product concept to re-specify deliberately. Do not assume V1 UI represented its intended final form.

### 7. Portrait-reference generation

Paid plan users can upload one or two portrait references.

Observed pipeline:

1. validate upload;
2. resize/compress it;
3. store reference under `uploads/dble/refs`;
4. place reference path/URL into queued work;
5. attach image bytes to Gemini as inline data;
6. safety fallbacks may later remove portrait references on retry.

**V2 disposition:** preserve as an optional creation mode if still desired. V2 needs explicit user-media ownership, retention, deletion and consent semantics.

### 8. Gemini image generation

Observed image generation model:

- `gemini-2.5-flash-image`;
- image response modality;
- model aspect-ratio configuration.

Worker behavior:

- calls image model with constructed cinematic prompt;
- optionally includes portrait inline data;
- receives image bytes inline;
- writes WebP assets;
- creates thumbnails;
- uploads/persists media;
- inserts an `arcana_renders` record.

**V2 disposition:** preserve provider-independent generation semantics, not Gemini-specific plumbing. Provider/model names must be integration configuration rather than domain assumptions.

### 9. Retry and safety fallback chain

V1 has a significant resilience mechanism around image generation. On failures/safety blocks it can retry multiple times, progressively:

- soften/simplify the prompt;
- remove portrait references;
- move toward safer/abstract/landscape framing;
- fall through several attempts before failing.

The source-derived inventory describes roughly seven attempts in parts of this chain.

**V2 disposition:** this is valuable product behavior and should be documented as a retry policy/state machine, not re-created ad hoc inside provider code.

### 10. Queue and worker system

Observed queue lifecycle:

`pending -> processing -> completed | failed`

Best-evidence worker:

`arcana.queue.processor.cron.parallel.v3.dynamicstyle.php`

It combines:

- row claiming/locking;
- parallel Gemini analysis calls;
- static/dynamic styles;
- prompt construction;
- multiple image attempts;
- watermark behavior;
- storage;
- render insertion;
- credit deduction;
- notifications;
- persistence of image style/status.

Multiple older worker variants remain in the repo. The repository does not prove which processor production cron currently invokes.

**V2 disposition:** preserve durable queued generation, but have exactly one canonical worker pipeline per job type with versioned orchestration.

### 11. Gallery & render lifecycle

Authenticated users can browse their own completed renders.

Observed gallery features:

- list renders;
- search;
- sorting;
- pagination;
- lightbox/viewer;
- deletion;
- optional image email/share behavior;
- B2-backed URL resolution when configured.

The gallery reads `arcana_renders` and deletion can affect both database rows and stored files/B2 objects.

**V2 disposition:** preserve projects/history/gallery as a first-class cross-device experience. Decouple display records from raw storage paths.

### 12. Media storage

Observed storage is hybrid:

- local filesystem under `uploads/dble`;
- portrait refs under `uploads/dble/refs`;
- Backblaze B2 upload/delete helpers;
- public URLs based on `B2_PUBLIC_URL`;
- local backups may be retained.

**V2 disposition:** use a storage abstraction with private ownership-aware media by default; decide separately whether public CDN URLs are appropriate for shared results.

### 13. Stripe checkout, subscriptions and credit packs

Observed payment capabilities include:

- Stripe Checkout;
- one-time credit packs;
- subscription products;
- subscription status tracking;
- cancel-at-period-end behavior;
- credit grants from payment events.

There are at least two webhook implementations:

1. an SDK-based broader Stripe endpoint;
2. a lightweight manually verified checkout-session webhook.

The repository does not prove which webhook URL the live Stripe account used.

**V2 disposition:** keep one webhook entry point, idempotent fulfillment, explicit product/price mapping, auditable ledger entries, and no duplicate fulfillment paths.

### 14. Email & notifications

Observed communication mechanisms include:

- verification email;
- password/reset-related messaging;
- PHPMailer/SMTP configuration;
- `mail()` fallback in at least some paths;
- render/gallery email behavior;
- in-app notifications from worker completion paths.

**V2 disposition:** treat email and notifications as integration services behind explicit application events.

### 15. Admin/system/public functions

Observed additional system capabilities include:

- admin access gate;
- admin user tools/exceptions;
- maintenance mode;
- public landing page;
- legal pages;
- public feedback;
- environment loading;
- mobile redirect behavior in the old public landing flow.

**V2 disposition:** re-evaluate each separately. Mobile redirect behavior is obsolete for a responsive web app plus native mobile app.

## Canonical V1 creative flow

Best-evidence normal path:

```text
User signs in
  -> opens generator
  -> enters artist/song/lyrics/settings
  -> optional portrait references
  -> server validates auth / credits / plan permissions
  -> arcana_queue row inserted as pending
  -> client polls queue
  -> background worker claims row
  -> Gemini text: Song DNA
  -> optional Gemini text: Dynamic Band StyleMap
  -> worker constructs cinematic prompt
  -> Gemini image generation
  -> retry/safety fallback chain if necessary
  -> WebP + thumbnail generated
  -> local/B2 storage
  -> arcana_renders row inserted
  -> image credit consumed
  -> notification/status completed
  -> gallery displays render
```

## What appears core to Arcana's identity

These are the V1 concepts most worth protecting during product discovery:

1. **A song is interpreted, not merely pasted into an image prompt.**
2. **Song DNA** translates musical/lyrical material into visual/narrative attributes.
3. **Dynamic band lore/style** can make results feel specific to an artist rather than generic.
4. **Cinematic prompt construction** is its own transformation layer.
5. **The user can enter the imagery through portrait references.**
6. **Results persist in a personal gallery/history.**
7. **Generation can adapt when safety/provider failures occur rather than immediately giving up.**

## V1 capabilities that should not automatically become V2 requirements

- invite-only signup;
- exact V1 plan names;
- PHP session/auth implementation;
- multiple legacy worker scripts;
- multiple Stripe webhook endpoints;
- mobile redirect page;
- mutable credit columns as the sole accounting source;
- hardwired Gemini model versions;
- public B2 URL assumptions;
- t-shirt/poster-store modes unless explicitly retained;
- legacy admin exceptions;
- exact watermark behavior;
- CSV/batch generators unless there is a current product need.

## Remaining behavior questions

Still worth verifying before implementation:

- exact Song DNA JSON schema and prompt text;
- exact Dynamic StyleMap schema/prompt and whether it was intended as the primary Arcana experience;
- which worker was live in production;
- which Stripe webhook was live;
- exact existing database schema in production versus runtime-created columns/tables;
- whether historical user/gallery data needs migration;
- how long portrait refs and local backups were retained;
- what t-shirt mode changes in generation behavior;
- whether story credits were connected to a separate story generator not represented in the main image flow;
- which plan/credit model the owners want to retain in V2.
