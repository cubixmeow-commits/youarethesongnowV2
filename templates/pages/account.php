<section class="panel narrow" data-account data-csrf="<?= e($csrf ?? '') ?>">
  <h1>Account</h1>
  <div data-account-body>
    <p class="status">Loading account...</p>
  </div>

  <form id="profile-form" class="stack">
    <h2>Profile</h2>
    <label>
      <span>Display name</span>
      <input type="text" name="displayName" required maxlength="80" autocomplete="nickname">
    </label>
    <button class="btn btn--secondary" type="submit">Save profile</button>
  </form>

  <form id="email-change-form" class="stack">
    <h2>Change email</h2>
    <label>
      <span>New email</span>
      <input type="email" name="email" required autocomplete="email">
    </label>
    <button class="btn btn--secondary" type="submit">Send verification</button>
  </form>

  <form id="password-set-form" class="stack">
    <h2>Optional password</h2>
    <label>
      <span>New password</span>
      <input type="password" name="password" minlength="10" required autocomplete="new-password">
    </label>
    <button class="btn btn--secondary" type="submit">Save password</button>
  </form>

  <div class="stack">
    <h2>Sessions</h2>
    <div data-sessions></div>
    <button class="btn btn--secondary" type="button" data-logout-all>Sign out of all devices</button>
  </div>

  <div class="stack">
    <h2>Delete account</h2>
    <p class="quiet">Permanent deletion removes your portraits, images, shares and unused credits immediately.</p>
    <button class="btn btn--danger" type="button" data-delete-preview>Review deletion</button>
    <form id="delete-form" class="stack" hidden>
      <p data-delete-preview-copy></p>
      <label>
        <span>Type DELETE MY ACCOUNT</span>
        <input type="text" name="confirmation" required autocomplete="off">
      </label>
      <button class="btn btn--danger" type="submit">Delete permanently</button>
    </form>
  </div>

  <p class="status" data-account-status role="status" aria-live="polite"></p>
  <button class="btn btn--secondary" type="button" data-sign-out>Sign out</button>
</section>
