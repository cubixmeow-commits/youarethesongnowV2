<?php
/** @var array|null $session */
$authed = !empty($session);
?>
<section class="showcase-page" data-showcase-page>
  <header class="showcase-page__header">
    <p class="eyebrow">V1 archive</p>
    <h1>Seventy-seven worlds</h1>
    <p class="lede">A visual archive from the first version of You Are The Song Now, preserved as creative reference.</p>
    <p class="showcase-page__disclosure quiet">These development samples may contain legacy Arcana branding or generated text. They are reference work, not a promise of current V2 output.</p>
  </header>

  <div class="showcase-filters" role="group" aria-label="Filter by orientation">
    <button type="button" class="showcase-filter is-active" data-filter="all" aria-pressed="true">All 77</button>
    <button type="button" class="showcase-filter" data-filter="portrait" aria-pressed="false">Portrait 32</button>
    <button type="button" class="showcase-filter" data-filter="square" aria-pressed="false">Square 33</button>
    <button type="button" class="showcase-filter" data-filter="landscape" aria-pressed="false">Landscape 12</button>
  </div>

  <p class="visually-hidden" aria-live="polite" data-showcase-status></p>

  <div class="showcase-grid-wrap">
    <div class="showcase-grid" data-showcase-grid>
      <div class="showcase-grid__sizer" aria-hidden="true"></div>
    </div>
  </div>

  <div class="showcase-load-more-wrap">
    <button type="button" class="btn btn--secondary" data-showcase-load-more>Load more worlds</button>
  </div>

  <div class="showcase-page__cta">
    <a class="btn btn--primary" href="<?= $authed ? '/create' : '/sign-in' ?>">Create your world</a>
  </div>
</section>

<dialog class="showcase-dialog" data-showcase-dialog aria-labelledby="showcase-dialog-label">
  <div class="showcase-dialog__inner">
    <div class="showcase-dialog__toolbar">
      <button type="button" class="icon-btn showcase-dialog__close" data-dialog-close aria-label="Close">✕</button>
      <p id="showcase-dialog-label" class="showcase-dialog__label" data-dialog-label>Sample 1 of 77</p>
      <div class="showcase-dialog__nav">
        <button type="button" class="icon-btn" data-dialog-prev aria-label="Previous sample">←</button>
        <button type="button" class="icon-btn" data-dialog-next aria-label="Next sample">→</button>
      </div>
    </div>
    <figure class="showcase-dialog__figure">
      <img class="showcase-dialog__image" data-dialog-image alt="" decoding="async">
      <figcaption class="showcase-dialog__caption" data-dialog-caption></figcaption>
    </figure>
  </div>
</dialog>
