<section class="reveal">
  <h1><?= e($image['title'] ?? 'Shared image') ?></h1>
  <p class="quiet"><?= e(($image['artist'] ?? '') . ' · ' . ($image['styleName'] ?? '')) ?></p>
  <figure class="reveal__figure">
    <img src="<?= e($image['contentUrl'] ?? '') ?>" alt="Shared artwork">
  </figure>
  <a class="btn btn--primary" href="<?= e($image['downloadUrl'] ?? '#') ?>">Download</a>
</section>
