<?php

declare(strict_types=1);

$assets = [
    'app.css' => [
        'path' => __DIR__ . '/assets/app.css',
        'type' => 'text/css; charset=utf-8',
    ],
    'app.js' => [
        'path' => __DIR__ . '/assets/app.js',
        'type' => 'text/javascript; charset=utf-8',
    ],
];

$root = dirname(__DIR__);
foreach (['experience.js'=>'app.js','experience.css'=>'app.css','flow.js'=>'flow.js','storage.js'=>'storage.js','mapping.js'=>'mapping.js'] as $name=>$file) {
    $assets[$name] = ['path'=>$root.'/experience-lab/'.$file,'type'=>str_ends_with($file,'.css')?'text/css; charset=utf-8':'text/javascript; charset=utf-8'];
}
foreach (['model.js','screens.js'] as $file) $assets[$file]=['path'=>$root.'/onboarding-lab/js/'.$file,'type'=>'text/javascript; charset=utf-8'];
foreach (['hero-beginner-greenway.png','pack-fit-adjustment.png','route-choice-greenway.png','completion-portrait.png','equipment-flatlay.png','community-park-walk.png'] as $file) $assets[$file]=['path'=>$root.'/brand/assets/photography/'.$file,'type'=>'image/png'];
$assets['kip-wombat-cutout.png']=['path'=>$root.'/experience-lab/assets/kip-wombat-cutout.png','type'=>'image/png'];
$assets['firstruck-mark.svg']=['path'=>$root.'/brand/assets/logo/firstruck-mark.svg','type'=>'image/svg+xml'];
$assets['maplibre.js']=['path'=>__DIR__.'/assets/vendor/maplibre/maplibre-gl.js','type'=>'text/javascript; charset=utf-8'];
$assets['maplibre.css']=['path'=>__DIR__.'/assets/vendor/maplibre/maplibre-gl.css','type'=>'text/css; charset=utf-8'];

// Marketing landing page (optimized derivatives under public/assets/landing/)
foreach ([
    'landing.css' => ['path' => __DIR__ . '/assets/landing/landing.css', 'type' => 'text/css; charset=utf-8'],
    'landing-hero.jpg' => ['path' => __DIR__ . '/assets/landing/hero.jpg', 'type' => 'image/jpeg'],
    'landing-route.jpg' => ['path' => __DIR__ . '/assets/landing/route.jpg', 'type' => 'image/jpeg'],
    'landing-pack.jpg' => ['path' => __DIR__ . '/assets/landing/pack.jpg', 'type' => 'image/jpeg'],
    'landing-complete.jpg' => ['path' => __DIR__ . '/assets/landing/complete.jpg', 'type' => 'image/jpeg'],
    'landing-community.jpg' => ['path' => __DIR__ . '/assets/landing/community.jpg', 'type' => 'image/jpeg'],
    'landing-kip.png' => ['path' => __DIR__ . '/assets/landing/kip.png', 'type' => 'image/png'],
] as $name => $meta) {
    $assets[$name] = $meta;
}

$name = (string) ($_GET['file'] ?? '');
if (!isset($assets[$name]) || !is_file($assets[$name]['path'])) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Asset not found.';
    exit;
}

header('Content-Type: ' . $assets[$name]['type']);
// Landing assets are requested with a filemtime `v` query. When present, allow
// long-lived caching; URL changes whenever the file changes. Demo/experience
// assets keep no-cache so design review refreshes stay immediate.
$isVersionedLanding = ($name === 'landing.css' || str_starts_with($name, 'landing-'))
    && isset($_GET['v']) && $_GET['v'] !== '';
if ($isVersionedLanding) {
    header('Cache-Control: public, max-age=31536000, immutable');
} else {
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
}
header('X-Content-Type-Options: nosniff');
if ($name === 'experience.css') { echo str_replace('../brand/assets/photography/hero-beginner-greenway.png', 'asset.php?file=hero-beginner-greenway.png', file_get_contents($assets[$name]['path'])); } else { readfile($assets[$name]['path']); }
