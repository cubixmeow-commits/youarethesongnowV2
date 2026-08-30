---
type: assessment
status: active
updated: 2026-08-30
area: current-project
---

# Build 1 Assessment — 2026-08-30

## Outcome

Private Development Build 1 has moved from scaffold to a working Hostinger-hosted vertical slice. The core promise is now technically demonstrated: an invited account can select a song, derive grounded Song DNA without retaining lyrics, attach one or two portraits, generate recognizable people inside an original song-inspired scene, and receive the result through the worker and gallery.

The build is ready for continued private testing. It is **not ready for external beta, live charging or commercial processing of protected lyrics**. Image quality, identity reliability, text suppression, provider routing, live operational checks and rights/licensing gates remain unfinished.

## Evidence standard

- **Live verified:** observed on the deployed Hostinger application by CuBiX Meow and/or Brut.
- **Automated verified:** covered by the repository test suite, but not necessarily exercised through the deployed browser flow.
- **Partially verified:** one or more successful tests exist, but the acceptance sample is too small or a known failure remains.
- **Deferred/gated:** intentionally outside the current private Build 1 test phase or blocked by an external decision.

## Current end-to-end path

```text
invited account
  -> song and artist lookup
  -> Gemini Interactions API (`gemini-3.6-flash`)
       Google Search + strict Song DNA JSON Schema + `store=false`
  -> approved abstracted Song DNA persisted; lyrics never persisted
  -> one or two private portrait references
  -> Gemini native multimodal image generation
       Flash Lite or Flash Image + inline portrait parts
  -> one-minute Hostinger worker
  -> private local gallery
  -> download / controlled sharing / deletion
```

## Live-verified systems

| Area | Status | Evidence |
|---|---|---|
| Hostinger deployment | Working | Root routing, PHP 8.2, SQLite, required PHP extensions and `/api/v1/health` verified live. |
| Background worker | Working | One-minute cron and manual worker processing completed queued generations and placed images in the gallery. |
| Owner access and email | Working in private test | Hostinger SMTP sign-in link worked. Brut received an invitation, activated access and generated an image. |
| Stripe test checkout | Working in sandbox | Hosted Checkout opened successfully after deployment corrections. A test membership became active with 100 credits and generation reduced the balance to 98. Live payments remain disabled. |
| Gallery | Working | Completed images appeared in the private gallery. |
| Grounded Song DNA | Working after Interactions migration | `Dancing in the Dark` by Bruce Springsteen succeeded after replacing legacy Search-plus-freeform JSON with Interactions Search plus strict schema and `store=false`. |
| One-person Gemini identity | Working in initial sample | `gemini-3.1-flash-image` produced a high-quality scene with the uploaded person recognizable and central to the story. |
| Two-person Gemini identity | Working in initial samples | Flash Lite generated both people. `Battle Against Time` by Wintersun produced a strong narrative composition after spatial prompt over-constraint was removed. |
| Cost control | Working in initial sample | Four Flash Lite images were reported at about $0.14 total, consistent with roughly $0.0336 per 1K output plus small input costs. Monthly application AI budget remains $25 for development. |

## Automated verification

The complete local Build 1 suite passes: **162 passed, 0 failed** on 2026-08-30.

Coverage includes migrations, style seeding, invite activation, portrait isolation, queued generation, idempotent credit reservation, worker completion and failure refunds, gallery ownership, share revocation, image/account deletion, Stripe webhook verification and replay protection, secret/log redaction, Song DNA privacy, Interactions structured Search, Gemini one/two-portrait request construction, aspect settings, image decoding, provider error handling, mobile token rotation and public-registration gates.

Automated coverage does not replace live acceptance testing for browser UX, email delivery edge cases, Stripe lifecycle cases, visual quality, provider behavior, Hostinger load, accessibility or legal launch readiness.

## Creative-engine findings

### Confirmed

- Staged Song DNA remains the correct foundation. The image worker reuses the Song DNA selected by the user instead of repeating lyric research.
- The Interactions API is the correct current Song DNA interface because Gemini 3 can combine Google Search and strict structured output in one stateless request.
- `store=false` is mandatory. No interaction state, lyrics, retrieved page bodies or raw responses may be retained.
- Native Gemini multimodal generation restored the defining portrait behavior that Replicate P-Image-Edit and fal Kontext Multi failed to provide.
- Portrait identity requirements must remain hard, but spatial staging must remain creative. Duplicated foreground/crop/centering rules produced repeated two-person layouts. Commit `42f9f41` consolidated identity rules and returned placement, camera and depth to the Song DNA.
- Flash Lite can produce excellent two-person images at roughly half the output cost of Flash Image.

### Mixed or unresolved

- Flash Lite quality is variable. The Alestorm `Mexico` test included both people but read as a weaker symbolic collage; the Wintersun `Battle Against Time` test was strong, coherent and cinematic.
- The successful full Flash and Flash Lite samples are not yet a controlled A/B set. Provider/tier decisions remain provisional.
- A no-text request produced readable `TICKET` signage in an otherwise successful image. Prompt-only suppression is not yet reliable enough for acceptance.
- Human review currently determines whether identity is recognizable and an image is usable. Automated identity/presence and OCR checks are not yet implemented.
- The 90-image acceptance set has not been completed. Current successes prove feasibility, not the required 90-percent reliability threshold.

## Current provisional model routing

| Tier | Provisional model | Evidence | Decision status |
|---|---|---|---|
| Low | `gemini-3.1-flash-lite-image` at 1K | Approximately $0.0336 output cost; one mixed and one excellent two-person result | Benchmark candidate, not final |
| Medium/default | `gemini-3.1-flash-image` at 1K | Approximately $0.067 output cost; excellent initial one-person identity result | Benchmark anchor, not final |
| High | Undecided | No controlled high-tier benchmark yet | Open |

Replicate P-Image-Edit and fal Kontext Multi remain disabled experimental adapters. Both produced visually interesting Song DNA scenes but failed the non-negotiable portrait requirement in live tests.

## Known issues and risks

1. **Image-quality variance:** a small live sample cannot establish tier reliability.
2. **Unwanted text:** no-text prompting can still produce signage or lettering; OCR/retry or another enforcement layer is needed.
3. **Manual identity acceptance:** there is no automatic check that every required person appears recognizably before credits are captured.
4. **Lyrics rights:** private transient development is not commercial clearance. External access remains blocked until the catalog is public-domain, permissively licensed, owner-controlled, directly artist-authorized or covered by appropriate licensing and legal review.
5. **Provider privacy/terms:** paid settings must continue to prohibit using customer portraits or prompts for model improvement. Verify current terms before external beta.
6. **Operational evidence:** rollback, load, worker contention, monitoring alerts, storage thresholds and disaster recovery still need live drills.
7. **SQLite/Hostinger scale:** appropriate for the private beta target, not yet proven for larger deployment. Move to a VPS/database service if load warrants.
8. **Local media only:** Backblaze B2, application-managed media backups and daily backup policy remain deferred.
9. **Test-mode commerce only:** live Stripe charges, tax configuration, full lifecycle testing and final credits are gated.
10. **Product polish:** onboarding quality, admin usability, all orientations/styles and accessibility need structured live review.

## Next private test sequence

1. Complete a controlled same-input Flash Lite versus Flash Image comparison for one-person and two-person cases.
2. Test several additional Song DNA structures before changing prompts again; vary narrative, mood, orientation and style one variable at a time.
3. Record identity, narrative fidelity, visual coherence, prohibited text, latency, provider cost and usability for every sample.
4. Decide whether Flash Lite is low only or can serve medium/default; keep full Flash as the quality anchor until evidence says otherwise.
5. Design no-text enforcement and unusable-image detection before counting the 90-image acceptance set.
6. Exercise live share/revoke, deletion, failed generation/refund, password change, email change/re-verification, Stripe cancellation/grace and complimentary reviewer paths.
7. Test admin controls, mobile-facing APIs, accessibility, rollback, worker contention and storage/monitoring behavior.
8. Run the approved 90-image benchmark only after the measurement sheet, prompt versions and provider routing are stable.

## Deferred until the core web build is approved

- Flutter/Dart iOS client
- Backblaze B2 migration and daily application backups
- Gallery upscale, print masters, posters, T-shirts and fulfillment
- Additional credit purchases
- Broad public song catalog
- External beta and live Stripe charging

## Go/no-go summary

**Continue private development:** yes. The core technical and creative premise works.

**Begin Flutter development:** not yet. The web build has not completed its live acceptance and quality benchmark.

**Invite external beta testers or charge live customers:** no. Rights, reliability, operational and commercial gates remain active.

