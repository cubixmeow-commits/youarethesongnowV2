# Deploy FirstRuck from GitHub to Hostinger

The deployable web root is the repository's `public/` directory. FirstRuck is served at `/FirstRuck/`, while its application source stays outside the public web root under `FirstRuck/`.

## Hostinger setup

1. Connect the GitHub repository and deploy the `main` branch.
2. Keep the complete repository checkout together; the small files under `public/FirstRuck/` load the application from `FirstRuck/public/`.
3. Set the site's document root to the repository's `public/` directory.
4. Visit `https://your-domain.example/FirstRuck/`.
5. Use PHP 8.1 or newer with SQLite, cURL and JSON enabled.
6. Give PHP write access to `FirstRuck/var/` so the app can create its local SQLite state.

Do not upload `FirstRuck/var/config.php` to GitHub. For live maps, create that file directly on the server from `FirstRuck/config.example.php`, add the Geoapify key and enable maps. The key remains server-side.

The published review works without provider credentials using the clearly labelled example routes. Membership checkout, public community sharing and live AI coaching are not connected yet.
