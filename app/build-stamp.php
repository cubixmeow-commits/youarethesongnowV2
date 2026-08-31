<?php

declare(strict_types=1);

/**
 * Fallback build identity when .git is not present on the host.
 * Prefer the live Git checkout when the Hostinger tree is synchronized from main.
 */
return [
    'commit' => '8201020e26404e7fc77e56a53c13804c5f1d5e55',
    'note' => 'Private-build Explore diagnostics + build marker (no .env change)',
];
