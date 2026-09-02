# Round 015 — Card-by-card Create flow

**Date:** 2026-09-02  
**Scope:** Mobile-canonical single-card Create state machine (Song → People → Direction → Review → Generating)

## State model

| Step | Client `flowStep` | Visible card |
| --- | --- | --- |
| Song | `song` | `data-create-card="song"` |
| People | `people` | `data-create-card="people"` |
| Direction | `direction` | `data-create-card="direction"` |
| Review | `review` | `data-create-card="review"` |
| Generating | `generating` | `data-create-card="generating"` |

Navigation: `setFlowStep()` toggles one active card via native `hidden`. Mobile bottom tabs hidden via `body.is-create-focus` (restored on Gallery and other pages).

## Verification

- `node verify-create-card-flow.mjs` — 20 contract scenarios (real Quick/Explore/Back/Fine Tune/Generate handlers)
- Screenshots: `capture-screenshots.mjs` (sanitized fixtures)

## Focus / safe area

- Back visible on People, Direction, Review
- Generate image lives in Review card action region (not competing with mobile tab bar)
- Fine Tune expands page when opened; no nested scroll regions in ordinary cards

## Asset cache busting

Create layout continues to emit `/assets/r/{releaseId}/...` URLs via `AssetRelease`.
