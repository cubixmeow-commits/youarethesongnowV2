# Routes, mapping, and AI

## Current provider choice

The preferred low-cost starting stack is MapLibre for rendering and Geoapify for hosted tiles, geocoding, and walking route geometry. Geoapify was selected over relying on the public OpenStreetMap tile servers. OpenRouteService remains a possible replaceable routing source after privacy and terms review.

The verified research and links are in `docs/experience/FREE-MAPPING-OPTIONS.md`. Recheck pricing, quota, terms, and API versions before a production commitment because provider conditions change.

## Current mapping behavior

Mapping is disabled by default. Copy `config.example.php` to protected `var/config.php`, provide `geoapify_key`, and set `maps_enabled` true on the server. Never ship the key to JavaScript.

`public/mapping.php` provides:

- `GET ?action=bootstrap`
- `POST ?action=search`
- `POST ?action=routes`
- `GET ?action=tile&z=...&x=...&y=...`

It uses session CSRF for writes, a six-request-per-minute search/route limit, a tile-session limit, and a SQLite global daily provider budget. The daily cap is 10,800 internal units, corresponding to 2,700 provider credits under the current accounting assumption.

`Geoapify::routes()` generates at most three pedestrian candidates around the chosen point and filters them to the requested 10–30 minute target. It supports out-and-back or short-loop geometry. Output is always `verified: false` and lists unknown access, closures, sidewalks/crossings, surface, hills, and weather.

## Required production pipeline

```text
User constraints and chosen area
  -> geographic candidate generation
  -> source and freshness normalization
  -> deterministic hard eligibility filters
  -> deterministic scoring baseline
  -> optional bounded LLM ordering/explanation
  -> UI with sources, unknowns, and fallback
```

An LLM must never create geometry or assert safety facts. It receives only eligible internal route IDs and approved reason codes.

## RouteCoach

`src/Coaching/RouteCoach.php` supports Gemini first and Groq second, with at most two provider calls per instance and deterministic rules fallback. It accepts only candidates marked verified and eligible, checked within 24 hours, with approved reason codes. Structured output must return every supplied ID once and only supplied reason codes. Hallucinated IDs or codes invalidate the response.

It currently has no public endpoint, production credential wiring, live-provider benchmark, or UI connection. Do not describe AI route coaching as live.

## Evidence still needed

Distance and pedestrian geometry alone are insufficient. Production route labels need a defined policy for elevation and grade, surface, crossings/sidewalks, legal access, closures, current conditions, weather, lighting, freshness, and user reports. Missing evidence must stay `unknown`, never inferred by the LLM.
