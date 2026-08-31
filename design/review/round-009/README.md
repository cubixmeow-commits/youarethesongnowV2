# Round 009 review pack — V1 sample showcase

**Date:** 2026-08-30  
**Scope:** Home hero/carousel replacement and new `/showcase` masonry archive

## What shipped

- All 77 V1 sample originals copied into `public/assets/images/showcase/originals/`
- 77 committed WebP thumbs (max 560px) and display derivatives (max 1600px)
- Stable manifest at `public/assets/data/v1-showcase.json`
- Home hero uses featured sample `v1-020` (calm lake/moon scene)
- Home carousel: progressive batches (9 initial, 10 later), scroll snap, prev/next, `current / 77`
- `/showcase`: Masonry v4 + imagesLoaded v5 (pinned local vendor), initial 18 / later 12, filters, dialog
- Legacy disclosure copy on showcase page

## Asset totals

| Set | Files | Bytes |
| --- | ---: | ---: |
| Originals | 77 | 92,452,925 |
| Thumbs | 77 | 2,895,726 |
| Display | 77 | 14,396,776 |

Orientation totals: portrait 32, square 33, landscape 12.

## Batch behavior

| Surface | Initial | Later | Stop |
| --- | ---: | ---: | --- |
| Home carousel | 9 | 10 | All 77 in DOM |
| Showcase wall | 18 | 12 | Sentinel + Load more disabled at 77 |

## Verification

| Check | Result |
| --- | --- |
| `php tests/run.php` | 893 passed, 0 failed (includes 715 new showcase asset assertions) |
| 200% browser zoom (Home 320, Showcase 390/1440) | No horizontal overflow (`performance-summary.json`) |
| Keyboard | Carousel arrows on track focus; dialog Escape/Left/Right; Load more named button |
| Reduced motion | Batch insert without transform/stagger in `showcase.js` |
| Slow 4G | Thumbs only in carousel/wall; display loads in dialog only |
| Owner Activate/Deactivate | Preserved (baseline tests still pass) |

## Screenshots

| File | Viewport / state |
| --- | --- |
| `home-mobile-320.png` | Home poster + carousel header |
| `home-mobile-390-carousel.png` | Carousel scrolled to show partial next item |
| `home-desktop-1440.png` | Desktop hero + carousel controls |
| `showcase-mobile-390-initial.png` | First 18 masonry items |
| `showcase-mobile-390-loaded.png` | After Load more |
| `showcase-mobile-390-dialog.png` | Full-image dialog open |
| `showcase-desktop-1440-initial.png` | Desktop initial batch |
| `showcase-desktop-1440-loaded.png` | All 77 loaded |
| `showcase-desktop-1440-dialog.png` | Dialog open |
| `showcase-desktop-1440-landscape-filter.png` | Landscape 12 filter |

## Remaining issues

None blocking ChatGPT visual review. Launch example files remain in repo but are no longer referenced on Home.

## Capture

```bash
php -S 127.0.0.1:8765 -t public public/router.php
cd design/review/round-008 && node capture-round-009.mjs
```
