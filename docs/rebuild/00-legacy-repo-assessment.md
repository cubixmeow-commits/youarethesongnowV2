# 00 — Initial Legacy Repository Assessment

**Legacy source:** `cubixmeow-commits/youarethesongnow`  
**V2 target:** `cubixmeow-commits/youarethesongnowV2`  
**Assessment date:** 2026-08-27

## Executive conclusion

The old repository is valuable as a product and behavioral reference, but it should **not** be used as the structural foundation for V2. Rebuild cleanly and selectively recover the parts of Arcana that represent product knowledge: flows, data semantics, prompt logic, media behavior, integrations, credit rules, and proven UX ideas.

## What is verified so far

### Product identity — Observed

The V1 `README.md` identifies the project as **AI Saga Arcana — You Are The Song Now** and describes the core promise as transforming songs into cinematic stories and visuals using a Dynamic Band Lore Engine.

### V1 capabilities — Observed from README

The README says V1 was designed to:

- parse band and song metadata;
- generate narrative and visual prompts using Gemini;
- create cinematic WebP imagery;
- manage user credits, logins, and uploads;
- display results in a user gallery.

These are useful leads for the deeper source audit, not yet a complete V2 specification.

### Legacy stack — Observed from README

- PHP 8.2
- MySQL/MariaDB
- XAMPP locally and Hostinger in production
- GD/Imagick for WebP conversion
- Google Gemini Flash 2.5 / 2.0 for text/image work
- PHPMailer for SMTP
- Stripe for credit purchases

### Repository condition — Observed

- The legacy repository is large and contains substantial non-application/project-management material.
- Root documentation has drifted. `SCHEMA.md` currently documents a VibeKB file-based content model rather than Arcana's application database/schema.
- Because of that drift, file names such as `PRODUCT.md` or `SCHEMA.md` cannot automatically be treated as product truth.

## Risks to avoid in V2

1. **Accidental porting.** Copying PHP structure into a new web/mobile codebase would preserve old deployment constraints and hidden coupling.
2. **Documentation contamination.** Unrelated/generated documentation can cause humans and agents to implement the wrong system.
3. **Provider coupling.** Model-provider details should not leak throughout UI/domain code.
4. **Synchronous generation assumptions.** Image/media generation can be slow or fail and needs durable job state.
5. **Credit/payment correctness.** Credits and Stripe touch money and require idempotent, auditable server-side rules.
6. **Media migration ambiguity.** We need an inventory of original uploads, generated assets, metadata, ownership, and storage paths before deciding what migrates.
7. **Copyright/product-policy review.** Song/lyrics-derived experiences should be designed around licensed/user-provided/permitted inputs and transformative outputs, with final product rules reviewed before launch.

## Salvage vs rewrite

| Area | V2 approach |
|---|---|
| Product concept and successful user flows | Preserve/rethink intentionally |
| Prompt/lore logic | Extract behavior, then redesign behind contracts |
| Existing user/data semantics | Inventory and map before migration |
| Generated media conventions | Preserve useful metadata; move storage behind an abstraction |
| Credits/payment rules | Re-specify and test server-side |
| PHP page/controller structure | Rewrite |
| Apache/XAMPP/Hostinger assumptions | Do not carry forward by default |
| Gemini-specific calls scattered through app | Replace with provider adapters |
| Stale/generated planning docs | Reference only after verification |

## Initial recommendation

Use V1 as a **museum plus test oracle**: inspect what users could do and what data was created, document that behavior, and then reproduce only the behavior V2 still wants. Do not make file-for-file parity a goal.

## Deeper audit still required

Before architecture is locked, trace:

- public/user/admin routes and screens;
- signup/login/password/email flows;
- database schema and data ownership;
- song/band input and metadata lookup;
- lore/narrative/prompt construction;
- text/image provider calls and fallback behavior;
- image upload/edit/conversion/storage pipeline;
- gallery/project lifecycle;
- credits, pricing, Stripe checkout/webhooks;
- SMTP/email;
- rate limits, moderation/safety and error paths;
- production configuration and deploy dependencies;
- export/share/delete/account-deletion behavior;
- migration-worthy user records and media.
