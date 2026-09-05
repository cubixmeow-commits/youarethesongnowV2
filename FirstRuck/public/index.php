<?php
declare(strict_types=1);

header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; object-src 'none'");
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=300');

$demoPath = 'app/';
$title = 'FirstRuck · A first ruck built around you';
$description = 'FirstRuck helps beginners prepare for a gentle first ruck, choose a manageable route, record the walk, and keep the memory in a personal field journal — with Kip along the way.';

/**
 * Build an asset.php URL with a filemtime cache-buster.
 * When the source file changes, the query string changes and browsers fetch the new copy.
 */
$asset = static function (string $file): string {
    static $paths = null;
    if ($paths === null) {
        $root = dirname(__DIR__);
        $paths = [
            'landing.css' => __DIR__ . '/assets/landing/landing.css',
            'landing-hero.jpg' => __DIR__ . '/assets/landing/hero.jpg',
            'landing-route.jpg' => __DIR__ . '/assets/landing/route.jpg',
            'landing-pack.jpg' => __DIR__ . '/assets/landing/pack.jpg',
            'landing-complete.jpg' => __DIR__ . '/assets/landing/complete.jpg',
            'landing-community.jpg' => __DIR__ . '/assets/landing/community.jpg',
            'landing-kip.png' => __DIR__ . '/assets/landing/kip.png',
            'firstruck-mark.svg' => $root . '/brand/assets/logo/firstruck-mark.svg',
        ];
    }
    $path = $paths[$file] ?? '';
    $version = ($path !== '' && is_file($path)) ? (string) filemtime($path) : '1';
    return 'asset.php?file=' . rawurlencode($file) . '&v=' . rawurlencode($version);
};
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#14331d">
  <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="robots" content="index,follow">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:image" content="<?= htmlspecialchars($asset('landing-hero.jpg'), ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($asset('landing-hero.jpg'), ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="icon" href="<?= htmlspecialchars($asset('firstruck-mark.svg'), ENT_QUOTES, 'UTF-8') ?>" type="image/svg+xml">
  <link rel="stylesheet" href="<?= htmlspecialchars($asset('landing.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
  <a class="skip" href="#main">Skip to content</a>

  <header class="site-header">
    <div class="shell nav">
      <a class="brand" href="./" aria-label="FirstRuck home">
        <img src="<?= htmlspecialchars($asset('firstruck-mark.svg'), ENT_QUOTES, 'UTF-8') ?>" width="28" height="34" alt="">
        <div>FirstRuck<span>Field guide for first rucks</span></div>
      </a>
      <nav class="nav-links" aria-label="Page">
        <a href="#how">How it works</a>
        <a href="#difference">Why FirstRuck</a>
        <a href="#demo">Demo</a>
      </nav>
      <a class="btn btn-primary" href="<?= htmlspecialchars($demoPath, ENT_QUOTES, 'UTF-8') ?>">Try the Demo</a>
    </div>
  </header>

  <main id="main">
    <section class="shell hero" aria-labelledby="hero-title">
      <div class="hero-copy">
        <p class="eyebrow">Beginner rucking · Mobile web preview</p>
        <h1 id="hero-title">A little weight goes a long way.</h1>
        <p class="lede">FirstRuck builds a calm first outing around you — preparation, a starter plan, a manageable route, and a field journal worth keeping. Kip walks with you.</p>
        <div class="cta-row">
          <a class="btn btn-primary" href="<?= htmlspecialchars($demoPath, ENT_QUOTES, 'UTF-8') ?>">Try the Demo</a>
          <a class="btn btn-secondary" href="#how">See how it works</a>
        </div>
        <p class="trust">Interactive preview · Example routes · No purchase required</p>
      </div>

      <div class="hero-visual" aria-hidden="true">
        <img class="hero-photo" src="<?= htmlspecialchars($asset('landing-hero.jpg'), ENT_QUOTES, 'UTF-8') ?>" width="1200" height="800" alt="" fetchpriority="high">
        <img class="kip-float" src="<?= htmlspecialchars($asset('landing-kip.png'), ENT_QUOTES, 'UTF-8') ?>" width="280" height="280" alt="">
        <div class="phone-card">
          <div class="notch"><span></span></div>
          <img src="<?= htmlspecialchars($asset('landing-route.jpg'), ENT_QUOTES, 'UTF-8') ?>" width="440" height="550" alt="">
          <div class="caption">
            <strong>Neighbourhood greenway</strong>
            Example starter route in the live demo
          </div>
        </div>
      </div>
    </section>

    <section class="shell section" id="how" aria-labelledby="how-title">
      <div class="section-head">
        <p class="eyebrow">How it works</p>
        <h2 id="how-title">From a few calm questions to a walk you can finish.</h2>
        <p>Onboarding asks only what changes the plan, then rewards you with field notes, a starter plan, and a route before any membership preview.</p>
      </div>
      <ol class="steps">
        <li>
          <div>
            <h3>Answer a few calm questions</h3>
            <p>Goals, comfortable distance, time, pack, and terrain — never an interrogation.</p>
          </div>
        </li>
        <li>
          <div>
            <h3>Get a starter plan &amp; route</h3>
            <p>See a personal first outing and choose a manageable path before anything else.</p>
          </div>
        </li>
        <li>
          <div>
            <h3>Go outside and record</h3>
            <p>Use foreground GPS or a labelled demo walk. Pause, resume, and check in as you go.</p>
          </div>
        </li>
        <li>
          <div>
            <h3>Keep it in your journal</h3>
            <p>Save the feeling, a photo, and a postcard — memories over streaks and leaderboards.</p>
          </div>
        </li>
      </ol>
    </section>

    <section class="shell section" id="difference" aria-labelledby="diff-title">
      <div class="section-head">
        <p class="eyebrow">What makes it distinctive</p>
        <h2 id="diff-title">A living outdoor field guide — not another workout dashboard.</h2>
      </div>
      <div class="distinct">
        <article>
          <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3c2.8 2.2 4.5 5.2 4.5 8.2A4.5 4.5 0 0 1 12 15.7 4.5 4.5 0 0 1 7.5 11.2C7.5 8.2 9.2 5.2 12 3Z" stroke="currentColor" stroke-width="1.7"/><path d="M8 18.5h8M10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
          <h3>Preparation before load</h3>
          <p>Consistency first. Turning back, resting, or choosing no added weight stays a valid decision.</p>
        </article>
        <article>
          <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 16.5c2-.8 3.7-2.8 4.4-4.8.4 1.5 1.4 2.8 2.6 3.6 1.4-2.2 3.7-3.7 6.5-4.3-1.5 3.4-4.6 5.9-8.3 6.7-1.9.4-3.6.3-5.2-.2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="7" r="2.2" stroke="currentColor" stroke-width="1.7"/></svg>
          <h3>Kip, your trail companion</h3>
          <p>A friendly wombat with an orange pack — warmth and continuity without guilt or streak pressure.</p>
        </article>
        <article>
          <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 10h8M8 13h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
          <h3>Memories over metrics</h3>
          <p>Journal entries and postcards capture place and feeling. Private by default. Share only when you choose.</p>
        </article>
      </div>
    </section>

    <section class="shell section" id="demo" aria-labelledby="demo-title">
      <div class="feature">
        <div class="feature-media">
          <img src="<?= htmlspecialchars($asset('landing-complete.jpg'), ENT_QUOTES, 'UTF-8') ?>" width="1000" height="750" alt="Walker finishing a gentle outdoor path lined with trees" loading="lazy">
        </div>
        <div class="feature-copy">
          <p class="pill">Featured demo</p>
          <h2 id="demo-title">Walk the current FirstRuck experience</h2>
          <p class="lede" style="margin-top:0.7rem">Open the working mobile web preview: meet Kip, move through the 26-screen onboarding, pick an example greenway route, try a labelled demo walk, and save a journal postcard.</p>
          <ul>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span>Today, Routes, Journal, and Journey are all in the preview.</span>
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span>Example routes stay clearly labelled as demonstrations.</span>
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span>Membership is a preview only — no invented price or checkout.</span>
            </li>
          </ul>
          <div class="cta-row">
            <a class="btn btn-primary" href="<?= htmlspecialchars($demoPath, ENT_QUOTES, 'UTF-8') ?>">Open the Demo</a>
            <a class="btn btn-secondary" href="#how">See how it works</a>
          </div>
        </div>
      </div>
    </section>

    <section class="shell final-cta" aria-labelledby="final-title">
      <h2 id="final-title">Ready when you are.</h2>
      <p>Start the interactive FirstRuck preview on your phone or desktop. No account needed for this build.</p>
      <div class="cta-row">
        <a class="btn btn-on-dark" href="<?= htmlspecialchars($demoPath, ENT_QUOTES, 'UTF-8') ?>">Try the Demo</a>
        <a class="btn btn-ghost" href="#how">Review the path</a>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="shell footer-row">
      <p>© <?= date('Y') ?> FirstRuck · A little weight goes a long way.</p>
      <p>
        <a href="<?= htmlspecialchars($demoPath, ENT_QUOTES, 'UTF-8') ?>">Demo</a>
        ·
        <a href="#main">Back to top</a>
      </p>
    </div>
  </footer>
</body>
</html>
