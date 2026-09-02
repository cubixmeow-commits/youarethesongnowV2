<?php
/** @var string $content */
/** @var string $title */
/** @var array|null $session */
/** @var bool $isHome */
use Yatsn\Support\AssetRelease;

$isHome = !empty($isHome);
$authed = !empty($session);
$isOwner = $authed && (($session['role'] ?? '') === 'owner');
$path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$componentLab = !empty($componentLab);
$showcaseScripts = $showcaseScripts ?? [];
$showcaseHero = $showcaseHero ?? null;

$assetBundle = 'core';
if ($path === '/create') {
    $assetBundle = 'create';
} elseif ($componentLab) {
    $assetBundle = 'component-lab';
} elseif (in_array('showcase', $showcaseScripts, true)) {
    $assetBundle = 'showcase';
}

$navItems = [];
if ($authed) {
    $navItems[] = ['href' => '/create', 'label' => 'Create', 'icon' => 'create'];
    $navItems[] = ['href' => '/gallery', 'label' => 'Gallery', 'icon' => 'gallery'];
    $navItems[] = ['href' => '/account', 'label' => 'Account', 'icon' => 'account'];
    if ($isOwner) {
        $navItems[] = ['href' => '/owner', 'label' => 'Owner', 'icon' => 'owner', 'secondary' => true];
    }
} else {
    $navItems[] = ['href' => '/sign-in', 'label' => 'Sign in', 'icon' => 'signin'];
}

$bodyClass = trim(($layoutClass ?? '') . ($isHome ? ' is-home' : '') . ($authed ? ' is-authed' : ' is-guest') . ($path === '/create' ? ' is-create-focus' : ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= e($title ?? 'You Are The Song Now') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <?php if ($isHome && !empty($showcaseHero['display'])): ?>
  <link rel="preload" as="image" href="<?= e($showcaseHero['display']) ?>" fetchpriority="high">
  <?php endif; ?>
  <link rel="stylesheet" href="<?= e(AssetRelease::url('css/app.css', $assetBundle)) ?>">
  <meta name="theme-color" content="#080A10">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>
<body class="app <?= e($bodyClass) ?>">
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="app-topbar">
    <div class="app-topbar__inner">
      <a class="brand" href="<?= $authed ? '/create' : '/' ?>">
        <img
          class="brand__mark"
          src="/assets/images/brand/ys-monogram-flat-platinum.svg"
          width="32"
          height="32"
          alt=""
          aria-hidden="true"
          decoding="async">
        <img
          class="brand__wordmark"
          src="/assets/images/brand/ys-wordmark.svg"
          width="150"
          height="25"
          alt=""
          aria-hidden="true"
          decoding="async">
        <span class="visually-hidden">You Are The Song Now</span>
      </a>
    </div>
  </header>

  <main id="main" class="app-main">
    <?= $content ?>
  </main>

  <nav class="app-nav" aria-label="Primary">
    <?php foreach ($navItems as $item): ?>
      <?php
        $href = $item['href'];
        $current = $path === $href || ($href !== '/' && str_starts_with($path, $href));
      ?>
      <a
        class="app-nav__item<?= $current ? ' is-active' : '' ?><?= !empty($item['secondary']) ? ' app-nav__item--secondary' : '' ?>"
        href="<?= e($href) ?>"
        <?php if ($current): ?>aria-current="page"<?php endif; ?>
        <?php if (!empty($item['secondary'])): ?>title="Private operations"<?php endif; ?>
      >
        <span class="app-nav__icon app-nav__icon--<?= e($item['icon']) ?>" aria-hidden="true"></span>
        <span class="app-nav__label"><?= e($item['label']) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <footer class="app-legal">
    <a href="/terms">Terms</a>
    <a href="/privacy">Privacy</a>
  </footer>
  <?php if ($path === '/create'): ?>
  <script src="<?= e(AssetRelease::url('js/song-search.js', $assetBundle)) ?>" defer></script>
  <script src="<?= e(AssetRelease::url('js/explore.js', $assetBundle)) ?>" defer></script>
  <?php endif; ?>
  <?php if ($componentLab): ?>
  <script src="<?= e(AssetRelease::url('js/component-lab.js', $assetBundle)) ?>" defer></script>
  <?php endif; ?>
  <script src="<?= e(AssetRelease::url('js/app.js', $assetBundle)) ?>" defer></script>
  <?php if (in_array('imagesloaded', $showcaseScripts, true)): ?>
  <script src="/assets/vendor/imagesloaded.pkgd.min.js" defer></script>
  <?php endif; ?>
  <?php if (in_array('masonry', $showcaseScripts, true)): ?>
  <script src="/assets/vendor/masonry.pkgd.min.js" defer></script>
  <?php endif; ?>
  <?php if (in_array('showcase', $showcaseScripts, true)): ?>
  <script src="<?= e(AssetRelease::url('js/showcase.js', $assetBundle)) ?>" defer></script>
  <?php endif; ?>
</body>
</html>
