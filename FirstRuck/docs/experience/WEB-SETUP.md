# FirstRuck mobile web review

The main `/FirstRuck/` entry now uses the shared experience UI. Run `php -S 127.0.0.1:8098 -t public` from the repository root and visit http://127.0.0.1:8098/FirstRuck/. The separate design lab is still at port 8097 when serving the FirstRuck directory.

## Optional live mapping

MapLibre 5.24.0 is vendored with its license. Geoapify provides hosted map tiles, place search and walking route geometry. Copy `FirstRuck/config.example.php` to protected `FirstRuck/var/config.php`, add an owner-provided Geoapify key and enable maps. Never add the key to browser assets or commit the configuration. No account or key was created during this build.

The same-origin endpoint includes CSRF protection for search/routing, session limits and a SQLite daily budget of 2,700 provider credits, leaving headroom below the advertised 3,000-credit free plan. This does not account for other applications using the same provider account. Each route search requests at most three candidates. Current candidates honor a short time budget but cannot guarantee surface, hills, access or closures. Those unknowns are displayed.

## Implemented and tested

26 onboarding screens with teaching breaks, Kip mascot, preparation and plan before membership preview; Today, route selection, foreground GPS and labeled demo recording; pause/resume, interrupted recording recovery, reflection, locally saved journal/photos, and downloadable share postcards. Photos are resized through canvas; original metadata and route coordinates are excluded from the postcard. Native sharing requires a supported browser.

Main-browser regression covers the full onboarding, disabled-map messaging, interrupted demo recovery, durable photo/journal reload, PNG download, sensitive-answer omission and widths 320–1440 pixels. Rule tests cover GPS filtering, beginner plans and flow order. PHP tests cover routing response validation and the isolated Gemini/Groq route-coach adapter. Provider tests use injected responses; live mapping has not been exercised without a key.

## Remaining before launch

Membership is a clearly labeled preview, with no payment processing. The Gemini/Groq adapter is implemented and tested separately but not connected to walking advice or live route choices. Live suitability enrichment, provider credentials, authentication, server journal sync, subscriptions and an actual community are not connected. Browser tracking is foreground-only; native background tracking and Flutter implementation follow web refinement. Local browser data can be removed by clearing site storage. This build has not been deployed.
