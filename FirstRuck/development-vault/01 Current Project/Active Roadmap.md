# Active roadmap

Updated: 2026-09-04

The order below is the current recommended build sequence. It is a planning baseline, not permission to invent unresolved product decisions.

## Now: refine the mobile web product

1. Review every onboarding screen for copy, pacing, image crops, small-screen layout, and whether each answer changes an outcome.
2. Replace remaining lab or preview language that should not appear in a consumer build while preserving honest demo labels.
3. Define the route-detail experience and the minimum facts required before a candidate can be called suitable.
4. Define the membership offer, free experience, real products, cancellation path, and restore behavior.
5. Decide what the user sees immediately after onboarding on first and returning visits.

Exit condition: the owner approves the complete onboarding-to-first-walk experience and the web reference passes its regression suite at supported widths.

## Next: production route slice

1. Configure Geoapify on a protected development server and test real searches in several urban, suburban, and trail-adjacent locations.
2. Measure route quality, quota cost, latency, duplicate rate, and failure modes.
3. Add independent evidence for elevation, surface, crossings, access, closures, and freshness where available.
4. Create deterministic eligibility and safety rules with explicit unknowns.
5. Validate the connected `RouteSelectionEngine` and `RouteCoach` pipeline against configured live providers; measure rules fallback and rejected-output behavior.
6. Add a versioned mobile-safe API contract.

Exit condition: no route is described as suitable unless required evidence passes; unknowns remain visible; provider failure falls back safely.

## Then: accounts, membership, and durable data

1. Choose identity and privacy model.
2. Define server records for profiles, plans, outings, track summaries, journal entries, photo objects, and entitlements.
3. Decide retention, deletion, export, location precision, and media access policy.
4. Integrate actual subscription products and server-verified entitlements.
5. Add analytics only for approved events with no precise location or private media leakage.

## Then: Flutter parity

1. Update Flutter contracts to the approved 26-screen web flow.
2. Port domain models and deterministic rules rather than copying browser UI code.
3. Build onboarding, Today, Routes, recording, reflection, Journal, and Journey in reviewed slices.
4. Add native location permissions and foreground recording first.
5. Evaluate background tracking, offline behavior, notifications, HealthKit, and Apple Watch separately.

## Later: social layer

Start with deliberate external sharing and privacy-safe postcards. Before an in-app community, decide audience controls, moderation, reporting, blocking, location redaction, photo review, deletion, and age requirements. Never publish a start point or exact trace by default.
