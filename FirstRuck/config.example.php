<?php
// Copy to FirstRuck/var/config.php, which is excluded from Git and blocked by Apache.
// Keep the key on the server. Do not paste it into the UI or commit it.
return [
    'maps_enabled' => false,
    'geoapify_key' => '',
    // Route AI may reorder structurally validated map candidates using only
    // approved reason codes. It never receives coordinates or private answers.
    'route_ai_enabled' => false,
    'route_ai_daily_call_limit' => 50,
    'gemini_key' => '',
    'gemini_model' => '',
    'groq_key' => '',
    'groq_model' => '',
];
