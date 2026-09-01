# Round 012.2 — Live POV Validation

**Date:** 2026-09-01  
**Mode:** live attempt blocked  
**Authority:** Owner-approved small live test (per `docs/design/CURSOR-HANDOFF.md`)

## Method

| Item | Value |
| --- | --- |
| Harness | `php bin/run-round-012-live-validation.php` (dry-run default) |
| Live execution | `php bin/run-round-012-live-validation.php --live` |
| Pass A budget | 5 Gemini structured planning calls (five fixtures) |
| Pass B budget | 4 Gemini image generations (2 fixtures × legacy vs live POV) |
| Image fixtures | `intimate_loss` (relationship/emotional), `kinetic_adventure` (kinetic/high-motion) |
| Portrait | Harness provisions one synthetic authorized-development portrait when live image pass runs; no portrait bytes committed |
| Committed default | `VISUAL_NARRATIVE_PLANNING_LIVE_CALLS=false` (unchanged) |

### Environment flags used in validation (no secrets)

```json
{
  "AI_PROVIDERS_ENABLED": "true",
  "GEMINI_LIVE_CALLS": "true",
  "GEMINI_IMAGE_LIVE_CALLS": "true",
  "VISUAL_NARRATIVE_PLANNING_ENABLED": "true",
  "VISUAL_NARRATIVE_PLANNING_LIVE_CALLS": "true",
  "VISUAL_NARRATIVE_LEGACY_COMPILER": "false"
}
```

### Models / templates (when live)

| Layer | Setting |
| --- | --- |
| Planning template | `visual-planning-prompt-v1` |
| Planning model | `gemini-3.6-flash` (`GEMINI_MODEL`) |
| Image model | `gemini-3.1-flash-image` |
| Image size | `1K` |
| Aspect | square (`1:1`) |

## Readiness check (this run)

```json
{
  "planningModel": "gemini-3.6-flash",
  "imageModel": "gemini-3.1-flash-image",
  "planningTemplate": "visual-planning-prompt-v1",
  "geminiKeyPresent": false
}
```

## Blockers

Live validation **did not execute provider calls** in this Cloud Agent environment:

1. **`GEMINI_API_KEY` missing** — no `.env` and no injected environment secret in the validation pod.
2. **`GeminiVisualNarrativePlanner` unavailable** — `config-gemini-api-key-missing`.
3. **`GeminiImageAdapter` unavailable** — cannot run Pass B image A/B without the same key.

No raw lyrics, portrait bytes, private storage paths, or provider response bodies were exposed or committed.

## Pass A / Pass B status

| Pass | Planned | Executed | Notes |
| --- | ---: | ---: | --- |
| A — live planning (5 fixtures) | 5 | 0 | Blocked before first call |
| B — image A/B (4 generations) | 4 | 0 | Blocked; synthetic portrait path ready in harness |

## Result summary

- Harness implemented and dry-run verified.
- Sanitized artifact schema committed under `design/review/round-012-live/`.
- **No live planning or image evidence collected** — results are not fabricated.
- Image files would be written only to `var/tmp/round-012-live-private/` (gitignored runtime path) when live succeeds.

## Acceptance gate

**BLOCKED** — Cannot recommend ACCEPT, ACCEPT WITH TUNING, or REJECT/ROLL BACK without live provider evidence. Architecture from Round 012.1 remains conditionally accepted only.

### To unblock (owner / environment)

1. Add `GEMINI_API_KEY` to the Cloud Agent environment secrets (or provide a local `.env` on an authorized development host).
2. Re-run: `php bin/run-round-012-live-validation.php --live`
3. Review private images locally; add human 1–5 scores to `image-ab-results.json` before GPT acceptance.

## Artifacts

- `planning-results.json` — Pass A sanitized output (blocked this run)
- `image-ab-results.json` — Pass B metadata and blind-label schema (blocked this run)
- `validation-run-summary.json` — machine-readable run metadata from the harness
