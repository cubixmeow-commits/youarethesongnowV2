---
type: product-design
status: approved-for-private-build
updated: 2026-08-30
area: visual-showcase
---

# V1 Sample Showcase

## Owner decision

CuBiX Meow approved a private-development V2 showcase using all 77 finished images from the local V1 `sample_images` archive.

The current Home hero/sample imagery will be replaced with archive imagery and a progressively loaded carousel. A new `/showcase` page will present all 77 images through the V1 behavior the owner liked: mixed orientations fall into place and lock together as a tight masonry wall.

## Locked direction

- Source: `repo V1 archive/sample_images`
- Verified inventory: 77 images, about 108 MB, 29 JPEG, 48 PNG, 32 portrait, 33 square and 12 landscape.
- Copy all images into V2 and create committed optimized thumbnail/display derivatives.
- Preserve V1 Masonry behavior, not V1 neon styling or application code.
- Load an initial batch, then append later batches lazily and relayout after decoding.
- Keep a visible Load more fallback in addition to automatic sentinel loading.
- Home carousel contains the full archive through progressive batches and does not autoplay.
- Full page uses `/showcase`; do not mix static archive entries into the signed-in user Gallery.
- Full-image viewing uses an accessible dialog with Close, Previous and Next.
- Keep V2 YS identity and current black/graphite/platinum/sapphire materials.
- External release, public registration, live billing, Flutter and commerce gates remain unchanged.

## Legacy-content disclosure

Many samples contain Arcana-era branding, generated lettering, song references or experimental text. The UI must identify the collection as V1 development reference and must not imply that all examples meet current V2 no-text, identity or quality standards.

## Implementation authority

Exact asset pipeline, responsive composition, lazy loading, accessibility, performance targets and review evidence are specified in `design/V1_SAMPLE_SHOWCASE_HANDOFF.md` and `design/CHATGPT_NEXT_PASS.md` Round 009.
