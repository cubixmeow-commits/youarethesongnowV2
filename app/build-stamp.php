<?php

declare(strict_types=1);

/**
 * Fallback build identity when .git is not present on the host.
 * Updated whenever private-build verification requires a known revision marker.
 * Prefer the live Git checkout when the Hostinger tree is synchronized from main.
 */
return [
    'commit' => '6fa401fdecdbdb454d2a81b015607a27d0b5a0a5',
    'note' => 'Private-build Explore diagnostics + build marker (no .env change)',
];
