<?php

declare(strict_types=1);

/**
 * Fallback build identity when .git is not present on the host.
 * Updated whenever private-build verification requires a known revision marker.
 * Prefer the live Git checkout when the Hostinger tree is synchronized from main.
 */
return [
    'commit' => '2e851833d3043bccf4d4e1dd1e6a598228f40140',
    'note' => 'Private-build Explore diagnostics + build marker (no .env change)',
];
