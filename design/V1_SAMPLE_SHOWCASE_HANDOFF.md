# YouAreTheSongNow V1 Sample Showcase Handoff

**Round:** 009

**Owner decision:** 2026-08-30

**Scope:** Current responsive PHP web/mobile app only

**Status:** Ready for Cursor implementation

## Objective

Turn the complete V1 sample-image archive into a cinematic, fast, internal-test showcase for V2:

1. replace the current hardcoded Home hero/sample imagery with the V1 sample set;
2. place all V1 samples in a lazy-loading Home carousel;
3. add a dedicated `/showcase` page where all samples settle into a tight mixed-orientation masonry wall;
4. preserve V2's YS identity, black/graphite/platinum/sapphire materials and artwork-first restraint;
5. keep every existing product, owner, billing, creation and gallery behavior unchanged.

This is a **legacy visual archive**, not the signed-in user's Gallery and not evidence that every V1 output meets the current V2 no-text or identity standards.

## Verified source inventory

Source checkout:

`/Users/realiainreid/Documents/You Are The Song Now V2/repo V1 archive`

Source folder:

`/Users/realiainreid/Documents/You Are The Song Now V2/repo V1 archive/sample_images`

Verified inventory on 2026-08-30:

| Property | Value |
| --- | ---: |
| Total files | 77 |
| Git-tracked files | 77 |
| Approximate source size | 108 MB |
| JPEG | 29 |
| PNG | 48 |
| Portrait orientation | 32 |
| Square orientation | 33 |
| Landscape orientation | 12 |

All 77 are finished generated artworks. They are not source portraits. Many contain legacy Arcana marks, generated lettering, song references or other V1-era experimental text. Preserve the art, label it honestly and do not present it as current V2 output quality.

## Verified V1 behavior to preserve

V1 behavior was inspected in:

- `repo V1 archive/index.gemini.php`: all sample files fed the Home carousel; later images used native lazy loading;
- `repo V1 archive/arcana.image.gallery.php`: Masonry v4 plus imagesLoaded v5; `horizontalOrder: true`; natural image heights; responsive 4/3/2/1-column sizing; 20px desktop gutter;
- `repo V1 archive/arcana.image.gallery.php`: thumbnails used `loading="lazy"`.

The owner specifically wants the V1 feeling where different orientations **fall into place and lock together** as one continuous wall. Reproduce that behavior. Do not port the V1 neon styling, inline PHP/CSS/JS structure, old branding, account code or CDN dependence.

## Visual thesis

A living cinematic contact sheet: archival worlds arrive in quiet batches, settle into one precise dark wall and open into a full-screen viewing room.

## Content plan

### Home

1. YS identity and existing promise/primary action.
2. One stable featured V1 sample as the hero artwork.
3. A horizontal **Worlds from the first chapter** carousel containing all 77 samples through progressive batches.
4. A clear **Explore all 77 worlds** link to `/showcase`.
5. A quiet final Create invitation without the old launch/interlude image.

### `/showcase`

1. Eyebrow: **V1 archive**
2. Heading: **Seventy-seven worlds**
3. Supporting copy: **A visual archive from the first version of You Are The Song Now, preserved as creative reference.**
4. Honest disclosure: **These development samples may contain legacy Arcana branding or generated text. They are reference work, not a promise of current V2 output.**
5. Orientation filters: **All 77**, **Portrait 32**, **Square 33**, **Landscape 12**.
6. Progressive masonry wall.
7. Accessible full-image viewing dialog.
8. Final action: **Create your world** linking to the existing sign-in/Create journey as appropriate for the current session.

Use sentence case and no em dashes.

## Asset ingestion and derivative contract

Copy every source artwork into the V2 repository. Do not depend on the V1 checkout at runtime.

Destination structure:

```text
public/assets/images/showcase/
  originals/     # all 77 archived source images
  display/       # optimized full-view WebP derivatives
  thumbs/        # optimized carousel/masonry WebP derivatives
public/assets/data/v1-showcase.json
```

Add a deterministic development script, preferably `bin/build-v1-showcase.php`, that accepts the source directory as an argument and:

1. sorts source filenames stably;
2. verifies exactly 77 readable image files;
3. preserves every artwork's pixels and orientation;
4. copies the original files into `originals/` with metadata stripped where possible, without cropping;
5. creates WebP thumbs with a maximum 560px dimension, no upscaling, metadata stripped and visually reviewed quality around 78–82;
6. creates WebP display files with a maximum 1600px dimension, no upscaling, metadata stripped and visually reviewed quality around 84–88;
7. writes a stable manifest;
8. fails loudly on a missing, corrupt, duplicated or unexpected file;
9. reports final count, orientation counts and total derivative sizes.

Do not require ImageMagick or another image tool in the production request path. Derivatives are build-time files committed to the repository. Do not introduce Git LFS unless the owner separately authorizes it.

Manifest entry contract:

```json
{
  "id": "v1-001",
  "sourceFilename": "opaque-original-name.png",
  "original": "/assets/images/showcase/originals/opaque-original-name.png",
  "display": "/assets/images/showcase/display/v1-001.webp",
  "thumb": "/assets/images/showcase/thumbs/v1-001.webp",
  "width": 1024,
  "height": 1024,
  "orientation": "square",
  "alt": "Short, visually verified description of the artwork",
  "featured": true,
  "featuredOrder": 1
}
```

Visually inspect all 77 files. Write short descriptive alt text without naming unidentified people, guessing protected song titles or claiming that legacy text is intentional. Do not expose filesystem paths in rendered copy.

## Home replacement requirements

Remove all current Home references to:

- `example-solo-*`
- `example-energy-*`
- `example-intimate-*`
- `layout-interlude-*`

Do not delete those files in this pass unless a repository-wide search proves they are unused and the owner separately requests cleanup. Remove their Home markup and CSS dependencies.

### Hero

- Choose one visually inspected, clean V1 sample with a useful calm tonal area and appropriate crop.
- Use its optimized display derivative, not the source archive directly.
- Keep the YS mark, wordmark, headline, supporting sentence, Start action and quiet Sign in action.
- Keep the Home poster composition approved in Round 008.1.
- Keep a stable hero choice for deterministic screenshots; do not randomize on every request.
- Maintain a strong readability veil and meaningful alt text.

### Home carousel

Section copy:

- Heading: **Worlds from the first chapter**
- Supporting line: **Seventy-seven experiments in song, memory and cinematic imagination.**
- Link: **Explore all 77 worlds** → `/showcase`

Behavior:

- native horizontal scrolling and CSS scroll snap;
- mixed portrait, square and landscape frames with natural proportions;
- partial next artwork visible on phone;
- previous/next buttons on desktop and optional on phone, each a real `<button>` with an accessible name;
- touch drag, trackpad, mouse wheel and keyboard access;
- no autoplay;
- first 8–10 thumbnails available immediately, then append the next batch before the user reaches the end;
- only the first visible artwork may be eager; later images use `loading="lazy"`, `decoding="async"`, explicit dimensions and thumbnail derivatives;
- a polite status may announce newly appended counts, but do not announce every scroll movement;
- keep DOM order stable and show a simple `current / 77` counter without turning it into pagination clutter.

Do not bring back three hardcoded sample cards. Do not turn this into a SaaS feature strip.

## Masonry showcase requirements

The full page should reproduce the V1 falling/locking behavior with V2 materials.

Preferred implementation:

- vendor pinned local copies of Masonry v4 and imagesLoaded v5 with their licenses under `public/assets/vendor/`; no runtime CDN;
- `itemSelector` for the sample tile;
- percentage positioning;
- `horizontalOrder: true` so DOM and visual order remain as aligned as the library allows;
- natural image heights, never fixed portrait crops;
- relayout only after each new batch's images are decoded/loaded;
- initial batch: 18 items;
- later batches: 12 items through an `IntersectionObserver` sentinel;
- all 77 reachable without a mouse and without requiring endless rapid scrolling;
- include a visible **Load more worlds** button as the reliable keyboard/screen-reader fallback for the sentinel;
- stop observing and remove/disable the control after item 77.

Responsive target:

| Width | Columns | Gutter |
| --- | ---: | ---: |
| 320–389 | 1 | 10px |
| 390–767 | 2 | 10–12px |
| 768–1099 | 3 | 14–16px |
| 1100+ | 4 | 18–20px |

The wall may use slight alternating optical offsets, but must not create holes, reorder focus, overlap images or crop the source art. Avoid thick card borders, captions over every image and hover-only information.

### Settle motion

- New batch: 320–420ms opacity plus 12–18px vertical settle.
- Stagger no more than 35ms per item and cap total batch delay.
- Existing items must not repeatedly animate when a later batch arrives.
- Under `prefers-reduced-motion: reduce`, insert and relayout immediately with no transform, stagger or smooth scrolling.
- Avoid continuous motion, parallax and autoplay.

## Full-image viewing room

Use a native `<dialog>` or the project's accessible modal pattern.

- Open from a real button wrapping each thumbnail.
- Show the optimized display derivative with original aspect ratio.
- Provide Close, Previous and Next controls with 44px touch targets.
- Support Escape, Left Arrow and Right Arrow.
- Keep background inert while open.
- Move focus into the dialog and return it to the originating tile on close.
- Announce **Sample N of 77** and the descriptive alt/caption.
- Do not expose download, share, delete, commerce or generation actions.

## Filters

Use native buttons with `aria-pressed` for All, Portrait, Square and Landscape.

- Filtering must retain stable manifest order.
- Update the visible result count in one polite status region.
- Relayout after filtering.
- Do not rely on color alone for the selected filter.
- Do not add search, song-title guessing or identity metadata in this pass.

## Performance budget

- Home showcase-specific initial transfer target: no more than about 1.5 MB after compression.
- Showcase initial batch transfer target: no more than about 2 MB.
- Never use original PNG/JPEG files in the Home carousel or masonry thumbnails.
- Load a display derivative only when its viewing dialog opens.
- Declare width/height or `aspect-ratio` from the manifest to prevent layout shift.
- The masonry must relayout after decoded dimensions without flashing a single-column pile.
- Test Slow 4G behavior and verify the page remains usable while later images arrive.

## Routing and product boundaries

- Add `GET /showcase` and `templates/pages/showcase.php`.
- The Home carousel and `/showcase` are available in the current private development build.
- Link to `/showcase` from Home; do not add another permanent mobile bottom-navigation destination.
- Do not change registration, invitation or external-release gates.
- Do not mix sample archive entries into `/gallery` or `/api/v1/images`.
- Do not store the static archive in SQLite.
- Do not add Flutter/Dart files.
- Do not change generation, portraits, payments, credits, sharing, deletion, owner controls or style activation.

## Tests and verification

Add automated checks for:

1. manifest count is exactly 77;
2. 32 portrait, 33 square and 12 landscape entries;
3. every original, thumb and display path exists;
4. IDs and paths are unique and confined to the showcase asset root;
5. `/showcase` returns 200;
6. Home no longer references the three old examples or `layout-interlude-*`;
7. Home and showcase render the approved copy and link;
8. lazy images have dimensions, `loading="lazy"` and `decoding="async"` where required;
9. the Load more fallback and dialog controls have accessible names;
10. current 178-test baseline remains green, including owner Activate/Deactivate and portrait deletion.

Manual verification:

- 320×568, 390×844, 430×932, 768×1024, 900×900, 1440×900;
- actual browser 200% zoom with reflow, not only page-scale magnification;
- keyboard-only carousel, filters, batch loading, dialog navigation and focus return;
- reduced motion;
- Slow 4G/no-cache initial load;
- no horizontal page overflow;
- no layout gaps/overlaps after each batch and filter;
- all 77 open successfully.

## Required review pack

Create `design/review/round-009/` with:

- `home-mobile-320.png`
- `home-mobile-390-carousel.png`
- `home-desktop-1440.png`
- `showcase-mobile-390-initial.png`
- `showcase-mobile-390-loaded.png`
- `showcase-mobile-390-dialog.png`
- `showcase-desktop-1440-initial.png`
- `showcase-desktop-1440-loaded.png`
- `showcase-desktop-1440-dialog.png`
- `showcase-desktop-1440-landscape-filter.png`
- `performance-summary.json`
- `README.md`

The README must record batch sizes, total items reached, source/derivative byte totals, viewport, fixture state, keyboard results, actual 200% browser-zoom result, reduced-motion behavior, Slow 4G observation and any remaining issue.

## Completion contract

Before stopping:

1. verify all 77 source images copied and all derivatives built;
2. run the complete test suite;
3. inspect the real browser and review pack;
4. confirm old Home image references are gone;
5. confirm no V1 code or style was ported wholesale;
6. update `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` and mark `design/CHATGPT_NEXT_PASS.md` consumed;
7. update the vault/dashboard only where implemented truth changes;
8. commit locally with a clear Round 009 message;
9. do not push or deploy without separate owner authorization;
10. stop for ChatGPT visual review.
