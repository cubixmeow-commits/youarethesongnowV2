# Current status

Updated: 2026-09-04

## Product stage

FirstRuck is an actively developed beginner rucking product. The team has chosen to continue building it. The mobile web app is the current product and design reference while the experience is refined. Flutter will follow after the web flow is strong enough to reproduce natively.

## Working now

- A 26-screen onboarding alternates groups of at most three questions with useful, lightly entertaining field notes.
- Onboarding moves from motivation and ability through preparation and route preferences, then reveals a starter plan, route choice, readiness summary, and membership preview.
- Kip, a friendly wombat with an orange backpack, appears as the trail companion.
- Main app sections exist for Today, Routes, Journal, and Journey.
- Users can choose labelled example routes or use optional Geoapify-backed place search and pedestrian route candidates.
- Foreground browser GPS recording validates fixes, calculates distance, overlays current position and the recorded breadcrumb on a selected live route, supports pause/resume, and recovers an interrupted session.
- A labelled demo walk lets the experience be reviewed without location access.
- Post-walk reflection, resized local photos, journal entries, postcard PNG export, and native sharing where supported are implemented.
- Non-sensitive onboarding answers and journal summaries persist locally. Photos and interrupted recording state use IndexedDB. The safety response is memory-only.
- The PHP recommendation engine and seeded demonstration route data still exist.
- A server-side route-selection pipeline validates and scores map-derived candidates, then optionally uses the bounded Gemini/Groq adapter to reorder eligible IDs. Rules remain the fallback and suitability unknowns remain visible.
- The complete mobile web experience is published to GitHub `main` and prepared for Hostinger deployment.

## Not connected yet

- Real membership products, purchase processing, entitlement restore, or subscription webhooks
- User accounts, cross-device sync, server journal storage, or production analytics
- A public or friends-only community feed
- Live AI advice during a walk
- Configured live-provider validation and production evidence for the route-selection pipeline
- Verified route surface, hill suitability, access, closures, crossings, weather, or trail authority data
- Background GPS, offline maps, turn-by-turn navigation, HealthKit, Apple Watch, or notifications
- A production-complete Flutter app

## Important truth labels

- Example routes are demonstrations.
- Geoapify output is map-derived geometry, not a verified safe or suitable route.
- Membership is a preview and shows no invented price or trial.
- Demo recordings remain labelled through reflection and export.
- Fitness rules are conservative prototype rules awaiting qualified review.

## Public web entry (2026-09-05)

- `/FirstRuck/` serves the promotional landing page.
- `/FirstRuck/app/` serves the interactive mobile web demo previously at the FirstRuck root.
- Shared `asset.php`, `mapping.php`, and `api.php` endpoints remain at `/FirstRuck/`.

## Current headline

“A little weight goes a long way.”

Supporting line: “A first ruck built around you. A little companion along the way.”
