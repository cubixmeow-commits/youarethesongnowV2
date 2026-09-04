# API contract and mobile boundary

## Current prototype endpoint

The web prototype currently uses:

```text
GET  https://youarethesongnow.com/FirstRuck/api.php?action=bootstrap
POST https://youarethesongnow.com/FirstRuck/api.php?action=recommend
```

`bootstrap` creates a PHP session and returns a CSRF token. `recommend` expects the same session cookie and the token in `X-CSRF-Token`.

Example successful recommendation shape:

```json
{
  "ok": true,
  "profile": {
    "level": "Fresh start",
    "session_minutes": 25,
    "starting_load": "Begin without added load",
    "weekly_frequency": "2 sessions each week",
    "terrain": "Mostly level, forgiving terrain",
    "goal": "general-fitness",
    "coaching_note": "Build consistency first. Change distance, hills, or load one at a time.",
    "health_note": "First Ruck offers general fitness guidance, not medical advice. Stop if pain changes your movement."
  },
  "recommendations": [],
  "data_mode": "demonstration"
}
```

Canonical server behavior lives in:

- `FirstRuck/public/api.php`
- `FirstRuck/src/RecommendationEngine.php`
- `FirstRuck/database/schema.sql`

## Milestone 1 rule

Do not call this endpoint in Milestone 1. The first native screen uses fixture state so iOS layout and navigation can be reviewed independently of session/cookie behavior.

## Required mobile-safe contract before live integration

Before Flutter connects to production data, add and test a versioned endpoint such as `/FirstRuck/api/v1/`. It should provide:

- explicit JSON request and response schemas;
- stable machine-readable error codes;
- mobile-appropriate opaque authentication or documented anonymous-session handling;
- idempotency where a repeated write could create duplicate work;
- `data_mode` on every route response;
- source name/reference and freshness for every live route fact;
- no PHP errors, private paths, SQL details, prompts, or provider secrets;
- backward-compatible additions within v1 and a new version for breaking changes.

The mobile client sends stable answer values from `screen-flow.md`, never display labels.

## Repository boundary

Define a small interface when network work begins, conceptually:

```dart
abstract interface class RecommendationRepository {
  Future<RecommendationResult> recommend(OnboardingDraft draft);
}
```

Use a fixture implementation for UI development and an HTTP implementation later. UI widgets depend on the interface or controller, not on URLs or JSON maps.

## Geographic and AI boundary

Future order of operations:

```text
User constraints
  -> verified geographic candidates
  -> deterministic hard filters
  -> deterministic ranking baseline
  -> optional AI explanation/reranking
  -> sourced recommendations with unknowns shown
```

AI output never creates geometry, closures, access rules, weather, or safety facts.
