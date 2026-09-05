# FirstRuck — Build architecture and next vertical slices

2026-09-04. This document distinguishes implemented review behavior from proposed production services. The new experience does not change the YATSN creative pipeline or its provider configuration.

## What exists now

- PHP/SQLite starter app with deterministic recommendations and seeded demonstration routes.
- Original 25-screen browser onboarding lab and its rule tests.
- Flutter app shell, welcome, and first question; no installed map, location, payment, or AI dependencies in `mobile/pubspec.yaml`.
- Shared `experience-lab/` UI now serves the main PHP mobile web app: 26-screen onboarding-to-membership review; Today, Routes, recording, reflection, Journal, Journey, postcard preview/export.
- Optional same-origin Geoapify search, pedestrian route candidates and tile proxy with MapLibre 5.24.0. Disabled until protected configuration is installed; candidate suitability remains explicitly unknown.
- Optional browser foreground GPS: permission requested only on explicit action; distance from accepted fixes; inaccurate, out-of-order and implausible fixes rejected; pause clears watch and resets the previous fix so resumed tracks do not bridge a paused segment. No background guarantee or turn-by-turn navigation.
- Demo clock/distance remain labeled demo throughout reflection and export. The main web app persists interrupted recording state and resized photos in IndexedDB; journal summaries use local storage. Photo exports omit original metadata. The standalone design lab keeps images in memory.
- `src/Coaching/RouteCoach.php`: isolated Gemini/Groq server adapter slice, default disabled, tested through injected transport. No public endpoint, app wiring, deployment credential lookup, or live provider benchmark is claimed.

## One product, three responsibilities

```text
Flutter client
  Onboarding / Today / Routes / Recording / Journal / Journey
  Local session database, permission controller, recording state machine
        |
Versioned FirstRuck PHP API (separate auth and data scope)
  Profile + deterministic plan rules
  Route candidate service + evidence validation
  Coaching broker + quotas + fallback
  Journal and photo storage
  Subscription entitlement and webhook verification
        |
External services through explicit adapters
  Mapping/routing | route authority/closures | Gemini/Groq | App Store
```

Do not reuse song-domain prompts, user records, safety assumptions, or quotas merely because the text adapters live in the same repository. Reuse the protected transport pattern and owner-managed secret mechanism after a FirstRuck-specific configuration scope is installed.

## Route suggestions that can be trusted

Updated for the owner’s free-provider preference: evaluate **MapLibre + Geoapify Free**, with openrouteservice as a replaceable routing candidate. See `FREE-MAPPING-OPTIONS.md` for verified quotas, commercial-use conditions and the division between renderer, tiles and routing. This supersedes the initial Mapbox candidate. Routing geometry does not prove present conditions or legal accessibility.

Pipeline:

1. User supplies a general area or explicitly requests current location.
2. Search produces actual candidate places / trailheads from a geographic service. Resolve coordinates and source IDs; never let an LLM make them up.
3. Routing service creates pedestrian geometry for bounded candidates. For loops, generate candidate waypoints and inspect returned geometry; do not draw a fictional loop and call it a route.
4. Compute distance, estimated time range, ascent and grade when supported by adequate elevation data. Preserve unknown fields explicitly.
5. Merge named land-manager access/closure information and timestamped weather where available. National-park or forest APIs are supplements for their geography, not universal local route coverage.
6. Hard filters exclude unsuitable distance, hills, known closures, access conflicts and missing required evidence. A lack of closure data must not become “open” or “safe.”
7. Deterministic preference ranking produces up to three options with reason codes. Show why each fits, its source, update time, and unresolved conditions.
8. Optional LLM can reorder already eligible IDs or select already supported explanation reasons. The current adapter implements this bounded step. Unknown IDs and invented reason codes fall back to deterministic results.
9. Detail screen includes route distance/time estimate, terrain, ascent confidence, return options, source attribution, last check, unknowns, and “Check before leaving.” Accepting a route persists its version, not merely its name.

Proposed route record: `id`, `sourceId`, `sourceUrl`, `checkedAt`, `geometry`, `distanceMeters`, `ascentMeters|null`, `gradeConfidence`, `surface`, `accessStatus`, `closureStatus`, `conditionUnknowns[]`, `returnOptions[]`, `eligible`, `reasonCodes[]`.

The 24-hour timestamp check in the new adapter is a defensive prototype limit, not a safety freshness guarantee. Route service must apply source-specific freshness before setting eligible. The client must not be allowed to submit a trusted `verified` or `eligible` flag.

## LLM provider structure

Observed existing text adapters: `src/AI/GeminiCreativeAdapter.php` and `GroqCreativeAdapter.php`. Existing image adapters also include Gemini, fal and Replicate. Their availability in source is not evidence of enabled FirstRuck credentials. No secret values were read or copied during this design pass.

FirstRuck broker tasks:

| Task | Inputs | Output | Fallback |
|---|---|---|---|
| Route explanation | Approved route IDs + verified fit codes | Valid IDs + subset of codes | Existing rule order and explanations |
| Post-walk reflection, later | Coarse goal, duration band, selected feeling | Short supportive reflection | Authored response for feeling |
| Next-session explanation, later | Rules-approved next session | Explanation of that exact session | Authored explanation |
| Walking check-in | Selected symptom/comfort category | Authored practical message | Always available offline |

Do not put live medical advice, emergency triage, live route discovery, arbitrary route changes, progression decisions, or GPS capture behind a model call. The prototype's walking check-ins are authored, not AI. A future open-ended coach needs a separate reviewed policy and evaluation set.

`RouteCoach` accepts a server-only config containing `enabled`, `geminiKey`, `geminiModel`, `groqKey`, `groqModel`; the default is disabled. Model identifiers are configurable and must be verified against the account's available structured-output models. No speculative latest model is hardcoded.

It tries Gemini then Groq, with at most two calls over the instance lifetime, eight-second HTTP timeout per attempt, 500 output tokens, and strict validation. Requests omit names, location coordinates, photos, health text and journal text. Provider output cannot add route geometry or arbitrary advice. There is no live network smoke test yet. Configuration and model-specific schema support need a controlled test before activation.

Before exposing a route endpoint: authenticated user scope, per-user daily token/request allowance, account-wide spend cap, durable budget reservation, rate limit, output cache keyed by candidate/profile versions, provider timeout metrics with no prompt/body logging, and rules fallback. The two-call instance limit is not a production account budget.

## Native recording

Build native recording before promising locked-screen reliability. Use Flutter as the interface with a reviewed Core Location integration on iOS, required capability/configuration, and tested permission behavior. A web timer continuing does not establish that location capture continued.

State machine: `idle → requesting permission → acquiring fix → recording ↔ paused → finishing → saved`. Add `permission denied`, `location unavailable`, `interrupted`, `storage failure`, and `recovered session` states. Keep Stop and Finish available offline and without an active subscription call.

Store session UUID and checkpoint locally before Start succeeds. Append timestamped points with accuracy and segment IDs in batches; keep elapsed active time separate from wall-clock time. Pause creates a segment boundary. Persist load selected for that session, not a later profile change. Preserve raw accepted points for inspection; smoothing is a separate derived series. Avoid claiming accuracy based on a single GPS reading.

Acceptance on real iPhone: screen lock, backgrounding, permission denied/revoked, reduced accuracy, airplane mode, a tunnel or weak signal, app termination/relaunch, incoming call, pause/resume after moving, low battery, and interrupted save. Compare distance against known walks and measure battery drain. Do not interpolate missing segments as recorded activity.

Proposed session data: `sessionId`, `profileVersion`, `routeVersion|null`, `startedAt`, `endedAt`, `activeSeconds`, `distanceMeters`, `addedLoad`, `segments[]`, `qualityFlags[]`, `reflection`, `syncState`.

## Journal, photos and community

Native phase one uses on-device durable photo storage and a private journal. Upload only after explicit cloud-sync opt-in and authenticated ownership checks. Strip EXIF on derivative export, hide start/end points and route geometry by default, and let users review the final artifact before OS sharing. Removing metadata does not conceal a recognizable location in the photo itself.

Social feed is a later bounded feature, not a dependency of the first ruck. Required before enabling: opt-in audience, no public live location, report/block, moderation queue and abuse handling, signed photo URLs, deletion cascade and export, upload limits, and tests for cross-user access. Apple guideline 1.2 applies to user-generated content; verify the current requirements before submission.

## Purchase integration

Use actual App Store products, verified entitlement state and restore behavior. Adapty is a candidate if it is the chosen purchase layer; it is not installed or configured in FirstRuck today. Keep purchase cancellation resumable. Display localized actual billing totals and terms only from the verified product/offer. Handle pending purchase, error, restore, expired entitlement and offline grace explicitly. Never let a network purchase check terminate an active recording.

## Delivery order

1. Review this experience: character, pacing, premium finish, utility of the field journal. Freeze a screen contract after owner feedback.
2. Port the full onboarding and Today into the existing Flutter shell, reusing tested rule behavior. Add local plan persistence and accessible navigation. Include unselected/error/back/resume states.
3. Deliver native offline recording + private journal + postcard export as the first end-to-end functional slice. Real-device acceptance above is the exit gate.
4. Add location-based and user-selected-area route search; retain manual/familiar route option and unknown-data states.
5. Wire the provider broker only after auth, quotas and actual model benchmark. Measure whether its reordering is better than rules; remove the call if it adds no user value.
6. Configure membership and entitlement tests, then controlled testing. Add community only when moderation and privacy behaviors are implemented.

## Verified documentation

[Mapbox walking directions](https://docs.mapbox.com/api/navigation/directions/), [Apple background location](https://developer.apple.com/documentation/corelocation/handling-location-updates-in-the-background), [Gemini JSON schema](https://ai.google.dev/gemini-api/docs/structured-output), [Groq structured outputs](https://console.groq.com/docs/structured-outputs), [Apple review guidelines](https://developer.apple.com/app-store/review/guidelines/).

## Owner clarification: any nearby or selected area

Route discovery must work around the user's chosen search location, rather than a fixed launch city. Search modes: **Near me**, **Search a place**, and later **Choose a point on the map**. Coverage is the intersection of mapping and local access evidence; where coverage is weak, say so and offer an ordinary familiar walk instead of claiming a complete trail catalog.

Include both established trails and custom pedestrian street walks. Translate onboarding into a structured route intent: start/search center, active-time budget, user-confirmed pace or conservative range, maximum acceptable ascent/grade, surface preference, loop/out-and-back preference, return options, and optional scenery/quiet preference. “Quiet” and “shade” remain preferences with uncertain evidence, not guarantees.

For a custom street loop, propose several bearings and waypoint distances around the selected start, route those waypoints on the pedestrian network, then reject paths with excessive length, detours, backtracking, disconnected geometry, known access conflicts or insufficient data. For out-and-back, find a reachable turnaround within half the time budget and include the return. Route distance is measured from returned geometry, never straight-line radius. Snapping to a pedestrian network is not proof of a sidewalk on every street; expose sidewalk/crossing unknowns where relevant.

The LLM may convert natural-language preferences to this validated intent and explain eligible results. Its training knowledge is not current map, closure or safety evidence. Live geographic tools and source-linked retrieval supply those facts. Never accept model-invented coordinates, named trails or access assertions as source data.

Example interpretation: “20 minutes, clear my head, mostly flat, easy way home” → bounded walking-time range, flatness filter, reversible route, preference for quieter mapped paths. Return up to three actual candidates with different useful tradeoffs. Do not promise worldwide uniform coverage or an exact walking duration.
