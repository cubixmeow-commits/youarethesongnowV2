<section class="gallery-page" data-gallery data-csrf="<?= e($csrf ?? '') ?>">
  <header>
    <h1>Gallery</h1>
    <p class="lede">Your private collection.</p>
  </header>
  <div class="gallery-grid" data-gallery-grid></div>
  <div class="gallery-empty" aria-hidden="true">
    <div class="gallery-empty__art" role="presentation"></div>
    <p class="gallery-empty__title">Your collection awaits</p>
    <p class="gallery-empty__copy">No finished works yet. Create your first cinematic world.</p>
  </div>
  <p class="status" data-gallery-status role="status" aria-live="polite"></p>
</section>
