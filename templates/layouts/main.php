<?php
/** @var string $content */
/** @var string $title */
/** @var array|null $session */
/** @var bool $isHome */
$isHome = !empty($isHome);
$authed = !empty($session);
$isOwner = $authed && (($session['role'] ?? '') === 'owner');
$path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$cssVersion = (string) (filemtime(YATSN_ROOT . '/public/assets/css/app.css') ?: '1');
$jsVersion = (string) (filemtime(YATSN_ROOT . '/public/assets/js/app.js') ?: '1');

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

$bodyClass = trim(($layoutClass ?? '') . ($isHome ? ' is-home' : '') . ($authed ? ' is-authed' : ' is-guest'));
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
  <?php if ($isHome): ?>
  <link rel="preload" as="image" href="/assets/images/launch/example-solo-560.webp" imagesrcset="/assets/images/launch/example-solo-560.webp 560w, /assets/images/launch/example-solo-1122.webp 1122w" imagesizes="100vw" fetchpriority="high">
  <?php endif; ?>
  <link rel="stylesheet" href="/assets/css/app.css?v=<?= e($cssVersion) ?>">
  <meta name="theme-color" content="#080A10">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>
<body class="app <?= e($bodyClass) ?>">
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="app-topbar">
    <div class="app-topbar__inner">
      <a class="brand" href="<?= $authed ? '/create' : '/' ?>">
        <span class="brand__mark" aria-hidden="true"></span>
        <span class="brand__text">You Are The Song Now</span>
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
  <script src="/assets/js/app.js?v=<?= e($jsVersion) ?>" defer></script>
</body>
</html>
