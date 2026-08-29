<section class="panel narrow">
  <h1>Activate your invitation</h1>
  <p class="lede">Accept the terms, then step into your first creation.</p>
  <form id="activate-form" class="stack">
    <input type="hidden" name="token" value="<?= e($token ?? '') ?>">
    <label>
      <span>Display name</span>
      <input type="text" name="displayName" autocomplete="nickname" required>
    </label>
    <label class="check">
      <input type="checkbox" name="acceptTerms" required>
      <span>I accept the <a href="/terms">Terms of Service</a></span>
    </label>
    <label class="check">
      <input type="checkbox" name="acceptPrivacy" required>
      <span>I accept the <a href="/privacy">Privacy Policy</a></span>
    </label>
    <button class="btn btn--primary" type="submit">Activate and continue</button>
    <p class="status" data-activate-status role="status" aria-live="polite"></p>
  </form>
</section>
