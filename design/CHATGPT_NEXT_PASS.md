# YouAreTheSongNow — Cursor Implementation Instructions

**Round:** 009

**Written by:** ChatGPT

**Date:** 2026-08-30

**Status:** Ready for Cursor

**Scope:** V1 sample archive, Home showcase and new `/showcase` page in the current web/mobile app

## Objective

Implement the complete owner-approved V1 sample showcase specified in `design/V1_SAMPLE_SHOWCASE_HANDOFF.md`.

This is an implementation pass. Copy and optimize all 77 V1 sample artworks, replace the current Home example/background imagery with the archive, add the lazy Home carousel, add the `/showcase` route and build the mixed-orientation masonry wall with the V1 falling/locking behavior in the V2 YS design system.

## Required reading

Read completely before editing:

1. `AGENTS.md` and the required Current Project / Build 1 contracts it names
2. `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
3. `design/V1_SAMPLE_SHOWCASE_HANDOFF.md`
4. `design/DESIGN_SYSTEM.md`
5. `design/RESPONSIVE_REDESIGN_PLAN.md`
6. `development-vault/05 Product Design/V1 Sample Showcase.md`

Inspect the source folder and every sample image before generating the manifest or selecting the Home hero.

## Verified source

`/Users/realiainreid/Documents/You Are The Song Now V2/repo V1 archive/sample_images`

Expected: 77 tracked image files, about 108 MB, 29 JPEG + 48 PNG, with 32 portrait + 33 square + 12 landscape.

## Exact direction

- Preserve the V1 Masonry feeling: mixed natural heights fall into place, lock tightly, keep horizontal order and relayout after each lazy batch.
- Vendor pinned Masonry v4 and imagesLoaded v5 locally with licenses; do not use a runtime CDN.
- Initial showcase batch 18, later batches 12 through IntersectionObserver plus a visible Load more fallback.
- Home carousel represents all 77 through progressive batches, uses scroll snap, has no autoplay and links to `/showcase`.
- Replace the current Home hero `example-solo-*`, three hardcoded examples and `layout-interlude-*` usage with V1 sample derivatives and the new carousel.
- Do not delete the old launch files in this pass.
- Use optimized thumbs for Home/masonry and load a display derivative only in the viewing dialog.
- Label the collection as a V1 development archive because legacy marks/text are visible in many images.
- Keep the approved YS identity, black/graphite/platinum/sapphire system, existing Home actions and Round 008.1 responsive corrections.

## Hard boundaries

- Do not copy V1 application code or styling wholesale.
- Do not mix these samples into the signed-in Gallery or its APIs/database.
- Do not add autoplay, parallax, hover-only actions or a permanent new bottom-nav destination.
- Do not change routes other than adding `GET /showcase`.
- Do not change registration gates, auth, generation, portraits, owner controls, style activation, credits, Stripe, sharing or deletion.
- Do not add Flutter, upscale, print, T-shirt or commerce work.
- Do not push or deploy.

## Finish

Follow every asset, accessibility, performance, screenshot and verification requirement in `design/V1_SAMPLE_SHOWCASE_HANDOFF.md`. Run the full suite, update the canonical handoff, mark this inbox consumed, commit locally and stop for ChatGPT review.
