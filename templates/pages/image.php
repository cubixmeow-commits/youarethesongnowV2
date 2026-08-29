<section class="reveal" data-image-page data-image-id="<?= e($imageId ?? '') ?>" data-csrf="<?= e($csrf ?? '') ?>">
  <h1>You are the song now.</h1>
  <p class="quiet">Your image has been saved to your gallery.</p>
  <figure class="reveal__figure">
    <img data-image-full alt="Generated artwork" width="1024" height="1024">
  </figure>
  <p data-image-meta class="quiet"></p>
  <div class="action-row">
    <a class="btn btn--primary" data-download href="#">Download</a>
    <button class="btn btn--secondary" type="button" data-share-link>Share link</button>
    <button class="btn btn--secondary" type="button" data-stop-share>Stop sharing</button>
    <button class="btn btn--secondary" type="button" data-create-another>Create another image</button>
    <button class="btn btn--danger" type="button" data-delete-image>Delete</button>
  </div>
  <form class="stack narrow-form" data-email-share>
    <label>
      <span>Email once to one recipient</span>
      <input type="email" name="email" required placeholder="friend@example.com">
    </label>
    <button class="btn btn--secondary" type="submit">Send share email</button>
  </form>
  <p class="status" data-image-status role="status" aria-live="polite"></p>
</section>
