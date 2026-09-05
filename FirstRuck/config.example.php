<?php
// Copy to FirstRuck/var/config.php, which is excluded from Git and blocked by Apache.
// Keep the key on the server. Do not paste it into the UI or commit it.
return [
    'maps_enabled' => false,
    'geoapify_key' => '',
    // Route AI may use grounded search to find named walking areas from a
    // reverse-geocoded city/region label, then reorder validated candidates.
    // It never receives coordinates or private answers.
    // Set true to rank eligible candidates with Gemini. When FirstRuck is
    // inside YouAreTheSongNow, the protected root GEMINI_API_KEY and
    // GEMINI_MODEL are reused unless the FirstRuck overrides below are set.
    'route_ai_enabled' => false,
    'route_ai_daily_call_limit' => 50,
    'route_discovery_cache_seconds' => 86400,
    'gemini_key' => '',
    'gemini_model' => '',
    'groq_key' => '',
    'groq_model' => '',
];
