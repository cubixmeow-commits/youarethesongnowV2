<section class="panel narrow">
  <h1>Sign in</h1>
  <p class="lede">Access is invite-only. Use a one-time email link or your optional password.</p>

  <form id="magic-form" class="stack">
    <label>
      <span>Email</span>
      <input type="email" name="email" autocomplete="email" required placeholder="you@example.com">
    </label>
    <button class="btn btn--primary" type="submit">Email me a sign-in link</button>
    <p class="status" data-magic-status role="status" aria-live="polite"></p>
  </form>

  <hr class="divider">

  <form id="password-form" class="stack">
    <label>
      <span>Email</span>
      <input type="email" name="email" autocomplete="username" required>
    </label>
    <label>
      <span>Password</span>
      <input type="password" name="password" autocomplete="current-password" required minlength="10">
    </label>
    <button class="btn btn--secondary" type="submit">Sign in with password</button>
    <p class="status" data-password-status role="status" aria-live="polite"></p>
  </form>
</section>
