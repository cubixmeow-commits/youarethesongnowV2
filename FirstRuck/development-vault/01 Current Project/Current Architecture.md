# Current architecture

## Today

```text
Mobile browser
  shared experience UI
  onboarding + plan rules
  Today / Routes / Record / Journal / Journey
  localStorage + IndexedDB
          |
          | same-origin optional requests
          v
PHP application
  session + CSRF
  recommendation endpoint
  mapping proxy and daily budget
  SQLite
          |
          +--> Geoapify search, routing, and tiles when enabled
          +--> WalkDiscovery -> Gemini grounded Google Search
          +--> RouteCoach -> Gemini, then Groq, with deterministic fallback

Flutter
  early native shell and first onboarding question only
```

## Web ownership

- `experience-lab/` owns the shared current interface, flow, local storage helpers, and browser behavior.
- `public/index.php` is the marketing landing page; `public/app/index.php` is the canonical PHP-served mobile web demo and loads allowlisted shared assets through `public/asset.php`.
- Repository-level `public/FirstRuck/` contains deployment bridges for the shared Hostinger web root.
- `onboarding-lab/` is the older research prototype and deterministic model source. It is not the current UI.

## Server ownership

- `src/RecommendationEngine.php` builds the original profile and ranks seeded demonstration routes.
- `public/api.php` persists a session profile and recommendation event to SQLite.
- `src/Mapping/Geoapify.php` searches places and produces bounded pedestrian route candidates.
- `src/Coaching/WalkDiscovery.php` asks Gemini Google Search grounding for cited named walking-area leads using only a generalized area and non-sensitive preferences.
- `public/mapping.php` keeps the map key server-side, applies CSRF and rate/budget limits, and proxies tiles.
- `src/Coaching/RouteSelectionEngine.php` validates and deterministically scores current map-derived candidates, preserves suitability unknowns, and invokes `RouteCoach` through the mapping route endpoint.
- `src/Coaching/RouteCoach.php` can reorder eligible route IDs using baseline scores and validated reason codes. Gemini, then Groq, is optional and protected by configuration and a daily call allowance; rules remain the fallback.

## Target direction

```text
Web and Flutter clients
  presentation + local recording state
          |
Versioned FirstRuck API
  identity and entitlements
  plan rules
  verified route-candidate pipeline
  bounded coaching broker
  outings, journal, and media contracts
          |
Adapters
  maps/routing | elevation/access/weather | LLMs | purchases | object storage
```

Clients should depend on stable domain contracts, not provider response formats. Mapping providers and LLMs must remain replaceable adapters.

## State boundaries

- Safety answer: memory only in the current web build.
- Non-sensitive onboarding answers: localStorage.
- Journal summary metadata: localStorage.
- Resized photos, selected live route, and interrupted recording/reflection: IndexedDB.
- GPS points: held only for the active browser recording and excluded from postcards.
- Original photos and EXIF: not stored; canvas creates a JPEG derivative.
- Server prototype: PHP session plus SQLite profiles, trails, recommendation events, mapping/AI budgets, and a generalized-area walk-discovery cache.
