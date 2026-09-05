# Web application

## Stack

The current app uses PHP, vanilla JavaScript, CSS, browser storage, and SQLite. It has no frontend build step. MapLibre GL JS 5.24.0 is vendored locally. The design lab and the main PHP app share the same experience JavaScript and CSS.

## Entry and asset flow

`FirstRuck/public/index.php` outputs the marketing landing page. `FirstRuck/public/app/index.php` outputs the main mobile app demo. It loads only files allowed by `FirstRuck/public/asset.php`, which maps friendly asset names to the shared experience, original model/catalog, photography, logo, mascot, and MapLibre bundle. Mutable review assets use revalidation headers so normal refreshes show current edits.

The repository-level `public/FirstRuck/index.php`, `asset.php`, `api.php`, and `mapping.php` are small Hostinger bridges to the canonical files. Do not duplicate application logic into the bridge.

## Client modules

- `flow.js`: ordered 26-screen flow, teaching content, GPS-point validation, distance, and elapsed-time helpers.
- `app.js`: rendering, navigation, plan reveal, route selection, recording, reflections, journal, postcards, and accessibility announcements.
- `storage.js`: IndexedDB `firstruck-local`, object store `items`.
- `mapping.js`: mapping bootstrap, place search, route requests, and MapLibre rendering.
- `onboarding-lab/js/model.js`: deterministic plan construction and example-route ranking.
- `onboarding-lab/js/screens.js`: question catalog and stable values.

## Browser storage keys

- `firstruck-experience-answers`: non-sensitive onboarding state
- `firstruck-onboarding-complete`: returning-user entry state
- `firstruck-field-journal`: journal summary metadata
- IndexedDB keys include `selected-route`, interrupted track/reflection state, and `photo-<entry id>`.

Do not add the safety answer, precise GPS trace, provider credentials, or original photo bytes to persistent browser storage without an explicit privacy decision.

## Accessibility baseline

The app uses semantic headings, native form controls, visible validation, a skip link, keyboard focus handling, status announcements, touch-size actions, viewport-safe layout, and reduced-motion behavior. Preserve usability at 320 px and at 200% text enlargement when changing screens.
