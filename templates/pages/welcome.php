<?php
/** @var array $showcaseHero */
$hero = $showcaseHero;
?>
<section class="hero hero--launchpad" aria-label="Welcome">
  <div class="hero__stage">
    <img
      class="hero__image"
      src="<?= e($hero['display']) ?>"
      width="<?= (int) $hero['width'] ?>"
      height="<?= (int) $hero['height'] ?>"
      alt="<?= e($hero['alt']) ?>"
      fetchpriority="high"
      decoding="async">
  </div>
  <div class="hero__veil" aria-hidden="true"></div>
  <div class="hero__copy">
    <p class="eyebrow">Song · imagination · visual journey</p>
    <h1>You Are The Song Now</h1>
    <p class="lede">A meaningful song becomes a cinematic world. Step inside and begin your journey.</p>
    <div class="hero__actions">
      <a class="btn btn--primary" href="/sign-in">Start with a song</a>
      <a class="btn btn--ghost hero__sign-in" href="/sign-in">Sign in</a>
    </div>
  </div>
</section>

<section class="world-carousel" aria-label="Worlds from the first chapter" data-home-carousel>
  <header class="world-carousel__intro">
    <h2 class="world-carousel__title">Worlds from the first chapter</h2>
    <p class="quiet">Seventy-seven experiments in song, memory and cinematic imagination.</p>
    <p class="world-carousel__link-wrap"><a class="world-carousel__link" href="/showcase">Explore all 77 worlds</a></p>
  </header>

  <div class="world-carousel__controls">
    <button type="button" class="icon-btn world-carousel__btn" data-carousel-prev aria-label="Previous world">←</button>
    <p class="world-carousel__counter" data-carousel-counter aria-live="polite">1 / 77</p>
    <button type="button" class="icon-btn world-carousel__btn" data-carousel-next aria-label="Next world">→</button>
  </div>

  <p class="visually-hidden" aria-live="polite" data-carousel-status></p>

  <div class="world-carousel__track" data-carousel-track tabindex="0" role="list"></div>
</section>

<section class="home-invite" aria-label="Create invitation">
  <div class="home-invite__copy">
    <h2>Begin creating</h2>
    <p>Choose a song, shape the journey, and collect original cinematic art made for you.</p>
    <a class="btn btn--primary" href="/sign-in">Start with a song</a>
  </div>
</section>
