<?php
/** @var string $content */
/** @var string $title */
/** @var array|null $session */
/** @var bool $isHome */
$isHome = !empty($isHome);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= e($title ?? 'You Are The Song Now') ?></title>
  <?php if ($isHome): ?>
  <link rel="preload" as="image" href="/assets/images/launch/hero-listening-room-960.webp" imagesrcset="/assets/images/launch/hero-listening-room-960.webp 960w, /assets/images/launch/hero-listening-room-1672.webp 1672w" imagesizes="100vw" fetchpriority="high">
  <?php endif; ?>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="<?= e(trim(($layoutClass ?? '') . ($isHome ? ' is-home' : ''))) ?>">
  <a class="skip-link" href="#main">Skip to content</a>
  <header class="site-header">
    <div class="site-header__inner">
      <a class="brand" href="<?= !empty($session) ? '/create' : '/' ?>">You Are The Song Now</a>
      <nav class="site-nav" aria-label="Primary">
        <?php if (!empty($session)): ?>
          <a href="/create">Create</a>
          <a href="/gallery">Gallery</a>
          <a href="/account">Account</a>
          <?php if (($session['role'] ?? '') === 'owner'): ?>
            <a href="/owner">Owner</a>
          <?php endif; ?>
        <?php else: ?>
          <a href="/sign-in">Sign in</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <main id="main">
    <?= $content ?>
  </main>
  <footer class="site-footer">
    <a href="/terms">Terms</a>
    <a href="/privacy">Privacy</a>
  </footer>
  <script src="/assets/js/app.js" defer></script>
</body>
</html>
