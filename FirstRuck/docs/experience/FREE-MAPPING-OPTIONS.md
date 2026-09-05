# FirstRuck — Free-to-start mapping options

Checked 2026-09-04/05 against current official provider pages. User preference: start with a free mapping provider. Search must work near the user or in any selected area, including custom street walks and trails.

## Recommended starting stack

**MapLibre for the map interface + Geoapify Free for hosted basemaps, search and initial walking routing.** This is the simplest coherent free-to-start candidate for evaluation, with one quota/dashboard for hosted services. No account was created, paid plan selected or key installed during research. The existing prototype remains on visibly labeled example routes.

MapLibre is the open-source map renderer, not a hosted map-data allowance. Its Flutter ecosystem supports native iOS/Android map rendering and custom styles; choose and pin a specific Flutter binding only after an iPhone spike. This lets FirstRuck keep a field-paper/forest style without making its whole product dependent on one tile provider.

| Candidate | Current free provision | Fit for FirstRuck | Limitation |
|---|---|---|---|
| Geoapify | 3,000 credits/day; up to 5 requests/second; no card required | Maps, geocoding, places and walking routing together; pricing FAQ permits commercial use with attribution | Credits shared across features, not 3,000 users or full route searches; no free-plan SLA |
| openrouteservice / HeiGIT Standard | 2,000 directions/day, 40/minute; geocoding 3,000/day, 100/minute | Dedicated routing alternative for streets/trails; investigate round-trip generation and elevation | Separate hosted basemap needed; current terms include attribution, result licensing and a prohibition on transmitting personal data |
| MapLibre | Open-source rendering software | Custom premium map appearance and provider independence | Needs a licensed tile source and separate search/routing |
| MapTiler Free | $0, testing/personal/non-commercial use | Strong vector map styling for development | Not a free commercial production choice for a subscription app |
| Stadia Maps Free | 200,000 credits/month; no commercial production use | Development/evaluation option | Paid plan needed for commercial deployment; API availability differs by plan |
| OSM public raster tiles | Community-funded best-effort service | Limited interactive use under published policy | Not unlimited production hosting; no offline bulk/prefetch; access may be blocked |

## Geoapify credit budget

Official credit schedule: tiles cost 0.25 credit each. Routing baseline is one credit per consecutive waypoint pair; route details and elevation add credits per pair. Other request types consume the same daily pool.

Illustrative, not a measured usage forecast: a search displaying 40 new tiles, doing one geocode and calculating three four-waypoint routes costs about **20 baseline credits**: 10 tiles + 1 geocode + 9 routing legs. This excludes places, elevation, detail requests, retries and other map movements. At that illustrative cost, 3,000 credits covers about **150 searches/day**, not 3,000. Measure actual tile/request consumption before assigning a user-capacity claim.

Keep search explicit or debounced, bound candidate generation, cancel stale requests, reuse maps without unnecessary reloads, and obey cache/storage terms. Do not treat the free plan's soft limit as an entitlement to excess usage. Set our own daily budget and a useful quota-exhausted state.

## Walking and trails

Geoapify documents both `walk` and `hike`. Start beginners on `walk`; its hiking mode may include higher-difficulty trails, so selecting `hike` merely because an app is outdoors would be a mistake. Map source metadata and FirstRuck's own suitability filters still need to constrain surface, hills and access.

For custom loops: generate bounded waypoints around a selected start, route on actual walkable edges, then measure and filter returned geometry. For out-and-back: include both legs in the time budget. LLMs interpret preferences and explain verified candidates; they do not supply invented routes.

Openrouteservice remains a candidate for a route-quality comparison. Its live plan page was verified in the browser because the page is JavaScript-rendered. The Standard plan says free for everyone; the special Collaborative plan is for eligible humanitarian, academic, governmental or not-for-profit organizations. Do not confuse those plans or rely on the outdated staging website.

Before using hosted HeiGIT with personal start points, resolve the current terms' prohibition on transmitting personal data. A public trailhead/park start with no user identity is a different data flow from uploading a user's private live trace. Do not send personal GPS histories to this service.

The provider announced migration from `api.openrouteservice.org` to `api.heigit.org`, with old-host shutdown scheduled for August 24, 2026. Do not build from old tutorial endpoints; use the current API documentation.

## Offline distinction

GPS recording itself does not require buying a map API and should work offline in the native app. Offline basemap downloads are a separate licensing/hosting concern. Do not use the OSM public tile server for offline areas. MapLibre's technical offline support does not grant offline rights to a provider's data.

## Next concrete integration

1. Obtain a free Geoapify project key through the owner's account; no paid upgrade required for the evaluation.
2. Use server-side credentials for search/routing; restrict any public tile token to the appropriate application context and map APIs where supported.
3. Evaluate MapLibre styles on an actual iPhone with visible Geoapify/OSM attribution and the recording overlay.
4. Test pedestrian streets, parks, rural paths and weak-coverage areas. Record route suitability and real credit usage.
5. Feed validated candidate IDs/fit codes into the already implemented Gemini/Groq broker only after route validation, authentication and durable quotas are wired.

## Official sources

- [Geoapify pricing and commercial-use FAQ](https://www.geoapify.com/pricing/)
- [Geoapify credit costs](https://www.geoapify.com/pricing-details/)
- [Geoapify routing modes and leg charges](https://apidocs.geoapify.com/docs/routing/)
- [MapLibre project](https://maplibre.org/)
- [Flutter MapLibre GL project](https://github.com/maplibre/flutter-maplibre-gl)
- [HeiGIT live plans](https://account.heigit.org/info/plans)
- [HeiGIT live terms](https://account.heigit.org/info/tos)
- [HeiGIT API migration announcement](https://ask.openrouteservice.org/t/deprecating-api-openrouteservice-org-in-favour-of-api-heigit-org/7912)
- [MapTiler pricing](https://www.maptiler.com/cloud/pricing/)
- [Stadia pricing](https://stadiamaps.com/pricing)
- [OSM tile usage policy](https://operations.osmfoundation.org/policies/tiles/)
