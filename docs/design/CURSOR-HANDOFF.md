# CURSOR-HANDOFF — Explore cleanup complete (awaiting GPT visual review)

**Date:** 2026-08-31  
**Working branch:** `main`  
**Phase:** Immediate Explore product-interaction cleanup only  
**Status:** Ready for GPT visual review before Phase 1 premium redesign continues

## Scope completed

Implemented **only** the immediate Explore cleanup from this handoff / `docs/design/process/PREMIUM-SITE-DESIGN-BUILD-PLAN.md` Phase 0.

Preserved:

- working Gemini Explore provider/decoder path
- Quick Generate / Explore API contract
- draft/job, credits/paywall, portraits, generation handoff

Did **not** begin the broader premium redesign.

## Interaction changes

1. Removed customer-facing StyleMap copy (`Uses … internally`).
2. StyleMap bridge retained only as private `data-style-name` / `data-style-id` on cards for debugging.
3. Replaced “Gemini’s strongest fit” sentence with a visual **Recommended** chip on the first card.
4. Direction cards are larger full-width tap targets with clearer focus/hover and `aria-selected`.
5. Selected card uses a strong selected state.
6. After selection, shows dominant **Create this direction** CTA (bridges into existing Review → Create my image flow).
7. While an AI direction is active, legacy **Choose your world** style grid and the old Review button are collapsed.
8. Secondary **Choose a style manually** restores the legacy style picker.
9. **Generate for me** remains the primary button before Explore opens.
10. Failure diagnostics still append sanitized codes; success UI no longer leads with build/diagnostic chrome (build SHA kept visually hidden for private deploy checks).

## Files changed

- `public/assets/js/explore.js`
- `tests/run.php`
- `docs/design/CURSOR-HANDOFF.md`

No Gemini/provider PHP changes in this slice.

## Tests / results

```text
php tests/run.php
=== Results: 981 passed, 0 failed ===
```

## Final commit

- **Hash:** `01ad649079aeb70d3cf090b28209f23c0f163de8`
- **Message:** Clean up Explore direction selection UX for review
- **Branch:** `main`
- **Requires:** Hostinger git sync; no `.env` changes

## Exact iPhone retest

1. Sync Hostinger `/yatsnV2` to latest `main` (git pull). No `.env` changes.
2. Soft-refresh `/create`.
3. Discover a song with Song DNA → select portrait → open Direction.
4. Confirm **Generate for me** is the primary action; **Explore options** is secondary.
5. Tap **Explore options** → three cards appear; first has **Recommended**; no “Uses … internally” text.
6. Tap a card → strong selected state; **Create this direction** appears; style grid is hidden.
7. Tap **Choose a style manually** → style grid returns.
8. Select a direction again → **Create this direction** → continues into existing review/generation.
9. Retest **Generate for me** still auto-continues.
10. Force/observe a failure only if convenient; diagnostic suffix should still appear on errors.

## Desktop implications discovered

- At ≥700px the three direction cards sit in one row; selected-state border/background remains readable beside neighbors.
- Primary/secondary CTA wrap in the lab header; on narrow widths actions become full-width 48px targets.
- Collapsing the style grid on desktop removes a large competing control block and makes the AI cards the clear decision surface; quality/format/no-text remain visible for fine control.
- Summary board still shows Style after AI selection because the StyleMap bridge still patches the existing draft style — expected for Build 1 compatibility, but visually the customer no longer chooses that style directly.

## Design questions for GPT

1. Is **Create this direction** the right CTA wording versus **Continue** / **Use this direction** / **Create my image**?
2. Should quality/format/no-text remain visible while an Explore direction is active, or should those also collapse behind Fine Tune?
3. When restoring manual styles, should the Explore card selection clear completely (current) or remain visible as a non-selected reference?
4. Should the Recommended chip stay text-only, or become a quieter mark / ordering cue only?
5. Is hiding the Review button while Explore is active correct, or should Review remain as a secondary path beside Create this direction?

## Recommended next slice

From `PREMIUM-SITE-DESIGN-BUILD-PLAN.md`, after GPT reviews this cleanup:

**Formalize Create journey/state architecture** (program step 2) — define the canonical Create Home → Song → Song DNA → Quick Generate / Explore → Fine Tune → Generation → Reveal states and contracts before any broad visual rebuild.
