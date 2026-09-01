<section class="owner" data-owner data-csrf="<?= e($csrf ?? '') ?>">
  <h1>Owner operations</h1>
  <p class="quiet">Private development controls. No impersonation.</p>
  <p class="quiet"><a href="/owner/component-lab">Private component lab</a> for Luminous Night Studio fixtures. Owner and private-build only.</p>

  <section>
    <h2>Setup status</h2>
    <pre data-setup-status class="code-block">Loading...</pre>
  </section>

  <section>
    <h2>Invite someone</h2>
    <form id="invite-form" class="stack narrow-form">
      <label>
        <span>Email</span>
        <input type="email" name="email" required>
      </label>
      <label>
        <span>Access</span>
        <select name="commercialAccess">
          <option value="paidBeta">Paid beta</option>
          <option value="complimentaryReviewer">Complimentary reviewer</option>
        </select>
      </label>
      <button class="btn btn--primary" type="submit">Send invitation</button>
      <p class="status" data-invite-status role="status" aria-live="polite"></p>
    </form>
  </section>

  <section>
    <h2>Totals</h2>
    <pre data-totals class="code-block">Loading...</pre>
  </section>

  <section>
    <h2>Invitations</h2>
    <div data-invitations></div>
  </section>

  <section>
    <h2>Users</h2>
    <div data-users></div>
  </section>

  <section>
    <h2>Jobs</h2>
    <div data-jobs></div>
  </section>

  <section>
    <h2>Styles</h2>
    <p class="quiet">Active launch styles appear to customers. Inactive recovered styles remain owner-visible.</p>
    <div data-styles></div>
  </section>
</section>
