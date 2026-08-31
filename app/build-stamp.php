<?php

declare(strict_types=1);

/**
 * Fallback build identity when .git is not present on the host.
 * Updated whenever private-build verification requires a known revision marker.
 * Prefer the live Git checkout when the Hostinger tree is synchronized from main.
 */
return [
    'commit' => 'e5dfe824e2d2ef2701872a9f395acd31c2724388',
    'note' => 'Private-build Explore diagnostics + build marker (no .env change)',
];
