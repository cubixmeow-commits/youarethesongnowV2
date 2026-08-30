# Round 003 — Visual + portrait deletion review pack

**Branch:** `cursor/visual-music-adventure-94fc`  
**Commit:** *53a68d0*  
**Date:** 2026-08-30  
**Source:** `design/CHATGPT_NEXT_PASS.md` (Round 003) + `design/CHATGPT_FEATURE_PORTRAIT_DELETE.md`

## Screenshots

| File | State |
| --- | --- |
| `create-mobile-390-top.png` | Mobile Create: session header + track source |
| `create-mobile-390-people.png` | Mobile People: portrait tray with delete control |
| `create-mobile-390-direction.png` | Mobile Direction: style/quality/format controls |
| `create-desktop-1440.png` | Desktop Create split workspace + session board |
| `portraits-mobile-delete.png` | Mobile delete confirmation dialog |
| `portraits-desktop-delete.png` | Desktop delete confirmation dialog |

Desktop browser capture may be slightly under 1440 CSS px.

## Visual Create changes

- Track source grouping for Artist + Song
- Portrait tray as creative source material with square tiles + secondary × delete
- Direction treatments denser/tactile; generate remains primary culmination
- Nav intentionally unchanged (no hotfix)

## Portrait deletion implementation

- API: `DELETE /api/v1/portraits/{portraitId}` (owned-only, CSRF)
- Service soft-deletes DB row then removes storage/thumb files (missing files tolerated)
- UI: confirm sheet → delete → remove from tray + selected session state + draft patch
- Tests added in `tests/run.php` (owned delete, foreign reject, file removal, list absence, already-deleted reject)
- Latest automated run: **173 passed, 0 failed**

## What ChatGPT should judge

1. Does Create now feel more like a creative instrument / contact-sheet tray?
2. Is portrait delete clear without looking like dating/profile management?
3. Ready for atmosphere/mark assets, or more Create composition first?
