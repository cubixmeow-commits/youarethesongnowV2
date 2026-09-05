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

For AI-enabled searches, `WalkDiscovery` sends Gemini only a generalized area label and non-sensitive route preferences. Gemini must use Google Search grounding and return named public walking-area leads with HTTPS citations. Results are cached by generalized area and preferences for 24 hours by default. Exact coordinates never enter its prompt. Geoapify then resolves each name with a proximity bias, rejects results more than 25 km from the requested area, and builds at most one pedestrian candidate near each of the first three names. The UI says this may not reproduce an official named trail and exposes the grounding sources.

If grounded discovery is unavailable, ungrounded, exhausted, or produces no usable geographic matches, `Geoapify::routes()` generates at most three generic pedestrian candidates around the chosen point. Both paths filter to the requested 10–30 minute target and support out-and-back or short-loop geometry. Output is always `verified: false` and lists unknown access, closures, sidewalks/crossings, surface, hills, and weather.

## Current selection pipeline

```text
User constraints and chosen area
  -> grounded named-place discovery (optional)
  -> Geoapify place resolution and pedestrian candidate generation
  -> source, geometry, duration, distance, and freshness normalization
  -> deterministic hard eligibility filters
  -> deterministic scoring baseline
  -> optional bounded LLM ordering/explanation
  -> UI with sources, unknowns, and fallback
```

`src/Coaching/RouteSelectionEngine.php` now implements this pipeline for the web prototype. It accepts up to six provider candidates, rejects stale or malformed records and large duration mismatches, assigns approved source-bound reason codes, ranks with rules, and sends at most three candidates to `RouteCoach`. The response distinguishes structurally verified provider facts from unverified suitability. Access, closures, crossings, surface, hills, and weather remain explicit unknowns.

An LLM must never create geometry or assert safety facts. Route ranking receives only eligible internal route IDs, deterministic baseline scores, and approved reason codes. Gemini is attempted first and Groq second. Invalid output, missing configuration, exhausted daily call allowance, or provider failure preserves the rules-based order. Exact coordinates, health answers, journal content, and user prose are never sent to the LLM. The generalized area label is sent only to grounded walk discovery.

## RouteCoach

`src/Coaching/RouteCoach.php` supports Gemini first and Groq second, with at most two provider calls per instance and deterministic rules fallback. It accepts only candidates whose source facts passed structural validation and are comparison-eligible, checked within 24 hours, with approved reason codes. That validation does not mean a route is safe or suitable. Structured output must return every supplied ID once and only supplied reason codes. Hallucinated IDs or codes invalidate the response.

The existing mapping route endpoint now invokes the selector. When deployed inside YouAreTheSongNow, FirstRuck reuses the protected root `GEMINI_API_KEY` and `GEMINI_MODEL`; it does not copy those values into FirstRuck. The separate `route_ai_enabled` flag and daily call allowance still control FirstRuck. Do not describe AI route coaching as live until a configured deployment and provider test confirm it.

The route endpoint can now use Gemini grounding to discover named-place leads before Geoapify generates geometry. This remains an evaluation slice until live provider tests confirm response compatibility, latency, quota use, and candidate quality across several kinds of locations.

## Evidence still needed

Distance and pedestrian geometry alone are insufficient. Production route labels need a defined policy for elevation and grade, surface, crossings/sidewalks, legal access, closures, current conditions, weather, lighting, freshness, and user reports. Missing evidence must stay `unknown`, never inferred by the LLM.
