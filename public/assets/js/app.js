(() => {
  const state = {
    csrf: null,
    draftId: null,
    songLookup: null,
    portraits: [],
    selectedPortraitIds: [],
    styles: [],
    selectedStyleId: null,
    quality: 'medium',
    orientation: 'square',
    noText: false,
    special: '',
    options: null,
    pollTimer: null,
  };

  function $(sel, root = document) {
    return root.querySelector(sel);
  }

  function $all(sel, root = document) {
    return Array.from(root.querySelectorAll(sel));
  }

  async function api(path, options = {}) {
    const headers = Object.assign({}, options.headers || {});
    if (!(options.body instanceof FormData)) {
      headers['Content-Type'] = headers['Content-Type'] || 'application/json';
    }
    if (state.csrf && options.method && options.method !== 'GET') {
      headers['X-CSRF-Token'] = state.csrf;
    }
    if (options.idempotencyKey) {
      headers['Idempotency-Key'] = options.idempotencyKey;
    }
    const res = await fetch(path, {
      credentials: 'same-origin',
      ...options,
      headers,
      body: options.body instanceof FormData || typeof options.body === 'string' || options.body == null
        ? options.body
        : JSON.stringify(options.body),
    });
    const text = await res.text();
    let payload = null;
    try {
      payload = text ? JSON.parse(text) : null;
    } catch (_) {
      payload = { error: { message: text || 'Unexpected response' } };
    }
    if (!res.ok) {
      const err = new Error(payload?.error?.message || 'Request failed');
      err.code = payload?.error?.code;
      err.status = res.status;
      err.payload = payload;
      throw err;
    }
    return payload;
  }

  function setStatus(el, message, isError = false) {
    if (!el) return;
    el.textContent = message || '';
    el.classList.toggle('is-error', !!isError);
    el.dataset.error = isError ? 'true' : 'false';
  }

  function idem() {
    return crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + Math.random();
  }

  async function ensureDraft() {
    if (state.draftId) return state.draftId;
    const res = await api('/api/v1/creation-drafts', { method: 'POST', body: {} });
    state.draftId = res.data.id;
    return state.draftId;
  }

  async function patchDraft(partial) {
    await ensureDraft();
    const res = await api(`/api/v1/creation-drafts/${state.draftId}`, {
      method: 'PATCH',
      body: partial,
    });
    return res.data;
  }

  function updateSummary() {
    const song = $('[data-sum-song]');
    const people = $('[data-sum-people]');
    const style = $('[data-sum-style]');
    const quality = $('[data-sum-quality]');
    const orientation = $('[data-sum-orientation]');
    const credits = $('[data-sum-credits]');
    if (!song) return;

    song.textContent = state.songLookup
      ? `${state.songLookup.title} · ${state.songLookup.artist}`
      : 'Not chosen yet';
    people.textContent = state.selectedPortraitIds.length
      ? `${state.selectedPortraitIds.length} selected`
      : 'None selected';
    const styleObj = state.styles.find((s) => s.id === state.selectedStyleId);
    style.textContent = styleObj ? styleObj.name : 'Not chosen yet';
    quality.textContent = state.quality[0].toUpperCase() + state.quality.slice(1);
    orientation.textContent = state.orientation[0].toUpperCase() + state.orientation.slice(1);
    const price = state.options?.qualities?.find((q) => q.id === state.quality)?.credits;
    credits.textContent = price != null ? String(price) : '—';
  }

  function renderPortraits() {
    const grid = $('[data-portrait-grid]');
    if (!grid) return;
    grid.innerHTML = '';
    state.portraits.forEach((p) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'portrait-chip' + (state.selectedPortraitIds.includes(p.id) ? ' is-selected' : '');
      btn.setAttribute('aria-pressed', state.selectedPortraitIds.includes(p.id) ? 'true' : 'false');
      btn.innerHTML = `<img src="${p.thumbnailUrl}" alt="Saved portrait">`;
      btn.addEventListener('click', async () => {
        if (state.selectedPortraitIds.includes(p.id)) {
          state.selectedPortraitIds = state.selectedPortraitIds.filter((id) => id !== p.id);
        } else if (state.selectedPortraitIds.length < 2) {
          state.selectedPortraitIds = [...state.selectedPortraitIds, p.id];
        } else {
          state.selectedPortraitIds = [state.selectedPortraitIds[1], p.id];
        }
        await patchDraft({ portraitIds: state.selectedPortraitIds });
        renderPortraits();
        updateSummary();
        maybeShowDirection();
      });
      grid.appendChild(btn);
    });
  }

  function renderStyles() {
    const grid = $('[data-style-grid]');
    if (!grid) return;
    grid.innerHTML = '';
    state.styles.forEach((s) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'style-option' + (state.selectedStyleId === s.id ? ' is-selected' : '');
      btn.setAttribute('role', 'option');
      btn.setAttribute('aria-selected', state.selectedStyleId === s.id ? 'true' : 'false');
      btn.innerHTML = `<strong>${s.name}</strong><span class="quiet">${s.description}</span>`;
      btn.addEventListener('click', async () => {
        state.selectedStyleId = s.id;
        await patchDraft({ styleId: s.id });
        renderStyles();
        updateSummary();
      });
      grid.appendChild(btn);
    });
  }

  function renderChoices() {
    const qRow = $('[data-quality-row]');
    const oRow = $('[data-orientation-row]');
    if (!qRow || !oRow || !state.options) return;
    qRow.innerHTML = '';
    oRow.innerHTML = '';
    state.options.qualities.forEach((q) => {
      const label = document.createElement('label');
      label.innerHTML = `<input type="radio" name="quality" value="${q.id}" ${state.quality === q.id ? 'checked' : ''}> <span>${q.label} (${q.credits})</span>`;
      label.querySelector('input').addEventListener('change', async () => {
        state.quality = q.id;
        await patchDraft({ quality: q.id });
        updateSummary();
      });
      qRow.appendChild(label);
    });
    state.options.orientations.forEach((o) => {
      const label = document.createElement('label');
      label.innerHTML = `<input type="radio" name="orientation" value="${o.id}" ${state.orientation === o.id ? 'checked' : ''}> <span>${o.label}</span>`;
      label.querySelector('input').addEventListener('change', async () => {
        state.orientation = o.id;
        await patchDraft({ orientation: o.id });
        updateSummary();
      });
      oRow.appendChild(label);
    });
  }

  function maybeShowDirection() {
    const people = $('#the-people');
    const direction = $('#the-direction');
    if (state.songLookup && ['found', 'fallbackFound'].includes(state.songLookup.state)) {
      if (people) people.hidden = false;
    }
    if (state.selectedPortraitIds.length > 0 && direction) {
      direction.hidden = false;
    }
  }

  function renderDevelopmentAnalysis(lookup) {
    const panel = $('[data-development-analysis-panel]');
    const research = lookup?.developmentAnalysis;
    if (!panel || !research?.enabled) return;
    panel.hidden = false;
    const song = $('[data-development-analysis-song]');
    const status = $('[data-development-analysis-status]');
    const excerpt = $('[data-development-analysis-excerpt]');
    const wrap = $('[data-development-analysis-wrap]');
    const preview = $('[data-development-analysis-preview]');
    const sources = $('[data-development-analysis-sources]');
    const sourceList = $('[data-development-analysis-source-list]');

    if (song) song.textContent = `${research.matchedTitle || lookup.title} by ${research.matchedArtist || lookup.artist}`;
    if (research.analyzed && research.preview) {
      const confidence = Math.round((Number(research.matchConfidence) || 0) * 100);
      if (status) status.textContent = `Gemini used Google Search to locate the lyrics and created grounded Song DNA with ${confidence}% match confidence. The worker will repeat this same analysis when you generate the image.`;
      if (excerpt) {
        excerpt.textContent = research.verificationExcerpt ? `Short verification fingerprint: “${research.verificationExcerpt}”` : '';
        excerpt.hidden = !research.verificationExcerpt;
      }
      if (preview) {
        preview.innerHTML = '';
        const rows = [
          ['Essence', research.preview.essence],
          ['Themes', (research.preview.themes || []).join(', ')],
          ['Mood', (research.preview.mood || []).join(', ')],
          ['Narrative', research.preview.narrativeArchetype],
          ['Original visual moment', research.preview.originalVisualMoment],
        ];
        rows.forEach(([label, value]) => {
          if (!value) return;
          const p = document.createElement('p');
          const strong = document.createElement('strong');
          strong.textContent = `${label}: `;
          p.append(strong, document.createTextNode(value));
          preview.appendChild(p);
        });
      }
      if (wrap) wrap.hidden = false;
    } else {
      if (status) {
        const failureMessages = {
          'grounding-request-rejected': 'Gemini rejected the Google Search request before analyzing the song. This is a provider-request problem, not a song-not-found result.',
          'provider-auth-or-permission-failed': 'Gemini rejected the API key or this project does not have permission for Google Search grounding.',
          'provider-model-unavailable': 'The configured Gemini model is not available to this API project.',
          'provider-rate-limited': 'The Gemini project has reached a request or Google Search grounding limit. Wait briefly, then try again or inspect the project quota in Google AI Studio.',
          'provider-timeout': 'Gemini Search took longer than the server allows. The song was not classified as missing.',
          'provider-network-failed': 'The Hostinger server could not connect reliably to Gemini. The song was not classified as missing.',
          'provider-temporarily-unavailable': 'Gemini is temporarily unavailable. The song was not classified as missing.',
          'search-failed': 'Gemini or Google Search could not complete the request. This is a provider failure, not a confirmed song-not-found result.',
        };
        status.textContent = failureMessages[research.status]
          || 'Gemini could not complete a grounded lyric analysis for this exact artist and song. Generation will stop rather than pretend that metadata-only Song DNA came from the lyrics.';
      }
      if (excerpt) {
        excerpt.textContent = '';
        excerpt.hidden = true;
      }
      if (preview) preview.innerHTML = '';
      if (wrap) wrap.hidden = true;
    }

    if (sourceList) {
      sourceList.innerHTML = '';
      (research.sources || []).forEach((source) => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = source.url;
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        a.textContent = source.title || 'Grounding source';
        li.appendChild(a);
        sourceList.appendChild(li);
      });
    }
    if (sources) sources.hidden = !(research.sources || []).length;
  }

  async function initCreate() {
    const root = $('[data-create]');
    if (!root) return;
    state.csrf = root.dataset.csrf;

    const me = await api('/api/v1/me');
    state.csrf = me.data.csrfToken || state.csrf;
    const styles = await api('/api/v1/styles');
    state.styles = styles.data;
    const options = await api('/api/v1/product-options');
    state.options = options.data;
    const portraits = await api('/api/v1/portraits');
    state.portraits = portraits.data;
    const params = new URLSearchParams(window.location.search);
    const existingDraft = params.get('draft');
    if (existingDraft) {
      state.draftId = existingDraft;
      const draftRes = await api(`/api/v1/creation-drafts/${existingDraft}`);
      const d = draftRes.data;
      state.songLookup = d.songLookup;
      state.selectedPortraitIds = (d.portraits || []).map((p) => p.id);
      state.selectedStyleId = d.style?.id || null;
      state.quality = d.quality || 'medium';
      state.orientation = d.orientation || 'square';
      state.noText = !!d.noTextInImage;
      state.special = d.specialInstructions || '';
      if ($('[data-no-text]')) $('[data-no-text]').checked = state.noText;
      if (state.special) {
        if ($('[data-special-toggle]')) $('[data-special-toggle]').checked = true;
        if ($('[data-special-wrap]')) $('[data-special-wrap]').hidden = false;
        if ($('[data-special]')) $('[data-special]').value = state.special;
      }
    } else {
      await ensureDraft();
    }
    renderStyles();
    renderChoices();
    renderPortraits();
    updateSummary();
    maybeShowDirection();

    $('#song-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const status = $('[data-song-status]');
      setStatus(status, 'Finding your song...');
      try {
        const res = await api('/api/v1/song-lookups', {
          method: 'POST',
          body: { artist: fd.get('artist'), title: fd.get('title') },
        });
        state.songLookup = res.data;
        renderDevelopmentAnalysis(res.data);
        await patchDraft({ songLookupId: res.data.id });
        if (res.data.state === 'notFound') {
          setStatus(status, 'We could not find enough reliable information about that song. Check the artist and title, or choose another song. No generation credits were used.', true);
        } else if (res.data.state === 'fallbackFound') {
          setStatus(status, 'We found reliable information about your song and will use its themes and feeling to inspire your image.');
        } else {
          setStatus(status, 'We found your song and will use its themes and feeling to inspire your image.');
        }
        updateSummary();
        maybeShowDirection();
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    $('#portrait-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const status = $('[data-portrait-status]');
      setStatus(status, 'Uploading...');
      try {
        const fd = new FormData(e.target);
        const res = await api('/api/v1/portraits', { method: 'POST', body: fd });
        state.portraits = [res.data, ...state.portraits];
        if (state.selectedPortraitIds.length < 2) {
          state.selectedPortraitIds.push(res.data.id);
          await patchDraft({ portraitIds: state.selectedPortraitIds });
        }
        e.target.reset();
        setStatus(status, 'Portrait uploaded.');
        renderPortraits();
        updateSummary();
        maybeShowDirection();
      } catch (err) {
        setStatus(status, 'We could not upload this photo. Choose another photo or try again.', true);
      }
    });

    $('[data-special-toggle]')?.addEventListener('change', (e) => {
      const wrap = $('[data-special-wrap]');
      if (wrap) wrap.hidden = !e.target.checked;
    });

    $('[data-review]')?.addEventListener('click', async () => {
      const status = $('[data-direction-status]');
      try {
        state.noText = !!$('[data-no-text]')?.checked;
        state.special = $('[data-special-toggle]')?.checked ? ($('[data-special]')?.value || '') : '';
        await patchDraft({
          noTextInImage: state.noText,
          specialInstructions: state.special || null,
          styleId: state.selectedStyleId,
          portraitIds: state.selectedPortraitIds,
          quality: state.quality,
          orientation: state.orientation,
        });
        const summary = await api(`/api/v1/creation-drafts/${state.draftId}/summary`, { method: 'POST', body: {} });
        if (!summary.data.ready) {
          const first = Object.values(summary.data.issues || {})[0] || 'Finish your creation before continuing.';
          setStatus(status, first, true);
          return;
        }
        setStatus(status, '');
        $('[data-summary-actions]').hidden = false;
        $('[data-paywall]').hidden = true;
        updateSummary();
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    $('[data-create-image]')?.addEventListener('click', async () => {
      const summary = await api(`/api/v1/creation-drafts/${state.draftId}/summary`, { method: 'POST', body: {} });
      if (summary.data.requiresMembership) {
        $('[data-summary-actions]').hidden = true;
        $('[data-paywall]').hidden = false;
        return;
      }
      await startGeneration();
    });

    $('[data-dev-activate]')?.addEventListener('click', async () => {
      const status = $('[data-paywall-status]');
      setStatus(status, 'Activating local development membership...');
      try {
        await api('/api/v1/billing/dev-activate', {
          method: 'POST',
          body: {},
          idempotencyKey: idem(),
        });
        setStatus(status, 'Local development membership active. Continuing...');
        $('[data-paywall]').hidden = true;
        await startGeneration();
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    $('[data-checkout]')?.addEventListener('click', async () => {
      const status = $('[data-paywall-status]');
      setStatus(status, 'Opening checkout...');
      try {
        const checkout = await api('/api/v1/billing/checkout-sessions', {
          method: 'POST',
          body: { draftId: state.draftId },
          idempotencyKey: idem(),
        });
        if (!checkout.data?.url) throw new Error('Stripe did not return a checkout URL.');
        window.location.href = checkout.data.url;
      } catch (err) {
        setStatus(status, err.message || 'Checkout is unavailable. Your creation is saved.', true);
      }
    });
  }

  async function startGeneration() {
    $('[data-summary-actions]').hidden = true;
    $('[data-progress]').hidden = false;
    const progressNote = $('[data-progress-note]');
    if (progressNote) progressNote.hidden = false;
    const copy = $('[data-progress-copy]');
    try {
      const res = await api('/api/v1/generation-jobs', {
        method: 'POST',
        body: { draftId: state.draftId },
        idempotencyKey: idem(),
      });
      pollJob(res.data.id, copy);
    } catch (err) {
      if (err.code === 'membership_required') {
        $('[data-progress]').hidden = true;
        $('[data-paywall]').hidden = false;
        return;
      }
      if (copy) copy.textContent = err.message;
    }
  }

  function pollJob(jobId, copyEl) {
    const tick = async () => {
      try {
        const res = await api(`/api/v1/generation-jobs/${jobId}`);
        const job = res.data;
        if (copyEl && job.progressStage) copyEl.textContent = job.progressStage;
        if (job.status === 'completed' && job.generatedImageId) {
          window.location.href = `/images/${job.generatedImageId}`;
          return;
        }
        if (job.status === 'failed') {
          if (copyEl) copyEl.textContent = job.message || 'We could not deliver a usable image. Your credits were returned. You can try again.';
          const progressNote = $('[data-progress-note]');
          if (progressNote) progressNote.hidden = true;
          return;
        }
        state.pollTimer = setTimeout(tick, 1200);
      } catch (err) {
        if (copyEl) copyEl.textContent = err.message;
      }
    };
    // Kick local worker while polling in development.
    fetch('/api/v1/health').finally(() => {
      // best-effort: worker is CLI; UI just polls.
      tick();
    });
  }

  async function initGallery() {
    const root = $('[data-gallery]');
    if (!root) return;
    state.csrf = root.dataset.csrf;
    const status = $('[data-gallery-status]');
    try {
      const me = await api('/api/v1/me');
      state.csrf = me.data.csrfToken || state.csrf;
      const res = await api('/api/v1/images');
      const grid = $('[data-gallery-grid]');
      if (!res.data.length) {
        setStatus(status, 'No images yet. Create your first cinematic world.');
        return;
      }
      grid.innerHTML = '';
      res.data.forEach((img) => {
        const a = document.createElement('a');
        a.href = `/images/${img.id}`;
        a.className = 'gallery-item';
        a.innerHTML = `<img src="${img.thumbnailUrl}" alt=""><span>${img.title} · ${img.artist}</span>`;
        grid.appendChild(a);
      });
    } catch (err) {
      setStatus(status, err.message, true);
    }
  }

  async function initImage() {
    const root = $('[data-image-page]');
    if (!root) return;
    state.csrf = root.dataset.csrf;
    const imageId = root.dataset.imageId;
    const status = $('[data-image-status]');
    const me = await api('/api/v1/me');
    state.csrf = me.data.csrfToken || state.csrf;
    const res = await api(`/api/v1/images/${imageId}`);
    const img = res.data;
    const full = $('[data-image-full]');
    full.src = img.contentUrl;
    full.alt = `${img.title} inspired artwork`;
    $('[data-image-meta]').textContent = `${img.title} · ${img.artist} · ${img.styleName} · ${img.orientation}`;
    $('[data-download]').href = img.downloadUrl;

    $('[data-share-link]')?.addEventListener('click', async () => {
      try {
        const share = await api(`/api/v1/images/${imageId}/link-share`, { method: 'POST', body: {} });
        await navigator.clipboard.writeText(share.data.url);
        setStatus(status, 'Share link copied.');
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    $('[data-stop-share]')?.addEventListener('click', async () => {
      try {
        await api(`/api/v1/images/${imageId}/link-share`, { method: 'DELETE', body: {} });
        setStatus(status, 'Sharing stopped.');
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    $('[data-create-another]')?.addEventListener('click', async () => {
      try {
        const res = await api(`/api/v1/images/${imageId}/regenerations`, { method: 'POST', body: {} });
        window.location.href = `/create?draft=${encodeURIComponent(res.data.draftId)}`;
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    $('[data-delete-image]')?.addEventListener('click', async () => {
      if (!confirm('Delete this image permanently? Sharing links will stop working immediately.')) return;
      try {
        await api(`/api/v1/images/${imageId}`, { method: 'DELETE', body: {} });
        window.location.href = '/gallery';
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    $('[data-email-share]')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = new FormData(e.target).get('email');
      try {
        await api(`/api/v1/images/${imageId}/email-shares`, {
          method: 'POST',
          body: { email },
          idempotencyKey: idem(),
        });
        setStatus(status, 'Share email logged for delivery.');
        e.target.reset();
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });
  }

  async function initAccount() {
    const root = $('[data-account]');
    if (!root) return;
    state.csrf = root.dataset.csrf;
    const body = $('[data-account-body]');
    const me = await api('/api/v1/me');
    state.csrf = me.data.csrfToken || state.csrf;
    const u = me.data.user;
    const c = me.data.credits;
    const m = me.data.membership;
    body.innerHTML = `
      <p><strong>${u.displayName}</strong><br>${u.email}</p>
      <p>Membership: ${m.status}<br>Credits: ${c.balance}<br>Access: ${u.commercialAccess}</p>
    `;
    const profileName = document.querySelector('#profile-form [name="displayName"]');
    if (profileName) profileName.value = u.displayName || '';

    const sessions = await api('/api/v1/me/sessions');
    const sessEl = $('[data-sessions]');
    if (sessEl) {
      sessEl.innerHTML = sessions.data.map((s) => `<p class="quiet">${s.id.slice(0, 8)}… ${s.active ? 'active' : 'ended'} · ${s.lastSeenAt}</p>`).join('');
    }

    $('#profile-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        await api('/api/v1/me/profile', { method: 'PATCH', body: { displayName: new FormData(e.target).get('displayName') } });
        setStatus($('[data-account-status]'), 'Profile saved.');
      } catch (err) {
        setStatus($('[data-account-status]'), err.message, true);
      }
    });

    $('#email-change-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        await api('/api/v1/me/email-changes', { method: 'POST', body: { email: new FormData(e.target).get('email') } });
        setStatus($('[data-account-status]'), 'Check the new address for a confirmation link.');
      } catch (err) {
        setStatus($('[data-account-status]'), err.message, true);
      }
    });

    $('#password-set-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const password = new FormData(e.target).get('password');
      try {
        await api('/api/v1/me/password', { method: 'PUT', body: { password } });
        setStatus($('[data-account-status]'), 'Password saved.');
        e.target.reset();
      } catch (err) {
        setStatus($('[data-account-status]'), err.message, true);
      }
    });

    $('[data-logout-all]')?.addEventListener('click', async () => {
      await api('/api/v1/auth/logout-all', { method: 'POST', body: {} });
      window.location.href = '/';
    });

    $('[data-delete-preview]')?.addEventListener('click', async () => {
      const preview = await api('/api/v1/me/deletion-preview', { method: 'POST', body: {} });
      $('[data-delete-preview-copy]').textContent = preview.data.consequences.join(' ');
      $('#delete-form').hidden = false;
    });

    $('#delete-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        await api('/api/v1/me/deletion-confirmation', {
          method: 'POST',
          body: { confirmation: new FormData(e.target).get('confirmation') },
        });
        window.location.href = '/';
      } catch (err) {
        setStatus($('[data-account-status]'), err.message, true);
      }
    });

    $('[data-sign-out]')?.addEventListener('click', async () => {
      await api('/api/v1/auth/logout', { method: 'POST', body: {} });
      window.location.href = '/';
    });
  }

  async function initOwner() {
    const root = $('[data-owner]');
    if (!root) return;
    state.csrf = root.dataset.csrf;
    const me = await api('/api/v1/me');
    state.csrf = me.data.csrfToken || state.csrf;

    const setup = await api('/api/v1/owner/setup-status');
    $('[data-setup-status]').textContent = JSON.stringify(setup.data, null, 2);
    const totals = await api('/api/v1/owner/totals');
    $('[data-totals]').textContent = JSON.stringify(totals.data, null, 2);

    const renderTable = (el, rows, cols) => {
      if (!rows.length) {
        el.textContent = 'None yet.';
        return;
      }
      const table = document.createElement('table');
      table.innerHTML = `<thead><tr>${cols.map((c) => `<th>${c.label}</th>`).join('')}</tr></thead>`;
      const tbody = document.createElement('tbody');
      rows.forEach((row) => {
        const tr = document.createElement('tr');
        tr.innerHTML = cols.map((c) => `<td>${row[c.key] ?? ''}</td>`).join('');
        tbody.appendChild(tr);
      });
      table.appendChild(tbody);
      el.innerHTML = '';
      el.appendChild(table);
    };

    const invitations = await api('/api/v1/owner/invitations');
    renderTable($('[data-invitations]'), invitations.data, [
      { key: 'email', label: 'Email' },
      { key: 'commercialAccess', label: 'Access' },
      { key: 'status', label: 'Status' },
      { key: 'expiresAt', label: 'Expires' },
    ]);

    const users = await api('/api/v1/owner/users');
    renderTable($('[data-users]'), users.data, [
      { key: 'email', label: 'Email' },
      { key: 'role', label: 'Role' },
      { key: 'accountState', label: 'State' },
      { key: 'membershipStatus', label: 'Membership' },
    ]);

    const jobs = await api('/api/v1/owner/jobs');
    renderTable($('[data-jobs]'), jobs.data, [
      { key: 'id', label: 'Job' },
      { key: 'userEmail', label: 'User' },
      { key: 'status', label: 'Status' },
      { key: 'creditCost', label: 'Credits' },
    ]);

    const styles = await api('/api/v1/owner/styles');
    renderTable($('[data-styles]'), styles.data, [
      { key: 'name', label: 'Name' },
      { key: 'status', label: 'Status' },
      { key: 'category', label: 'Category' },
      { key: 'styleKey', label: 'Key' },
    ]);

    $('#invite-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const status = $('[data-invite-status]');
      try {
        const res = await api('/api/v1/owner/invitations', {
          method: 'POST',
          body: {
            email: fd.get('email'),
            commercialAccess: fd.get('commercialAccess'),
          },
        });
        let msg = `Invitation created for ${res.data.email}.`;
        if (res.data.activationToken) {
          msg += ` Local activation token: ${res.data.activationToken}`;
        }
        setStatus(status, msg);
        e.target.reset();
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });
  }

  async function initAuthPages() {
    $('#magic-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = new FormData(e.target).get('email');
      const status = $('[data-magic-status]');
      try {
        await api('/api/v1/auth/magic-links', { method: 'POST', body: { email } });
        setStatus(status, 'If that email can sign in, a one-time link has been sent.');
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    $('#password-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const status = $('[data-password-status]');
      try {
        await api('/api/v1/auth/password-sessions', {
          method: 'POST',
          body: { email: fd.get('email'), password: fd.get('password') },
        });
        window.location.href = '/create';
      } catch (err) {
        setStatus(status, 'Those sign-in details did not work.', true);
      }
    });

    $('#activate-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const status = $('[data-activate-status]');
      try {
        await api('/api/v1/auth/activations/complete', {
          method: 'POST',
          body: {
            token: fd.get('token'),
            displayName: fd.get('displayName'),
            acceptTerms: fd.get('acceptTerms') === 'on',
            acceptPrivacy: fd.get('acceptPrivacy') === 'on',
          },
        });
        window.location.href = '/create';
      } catch (err) {
        setStatus(status, err.message, true);
      }
    });

    if (window.__YATSN_SIGNIN_TOKEN__) {
      const status = $('[data-complete-status]');
      try {
        await api('/api/v1/auth/magic-links/complete', {
          method: 'POST',
          body: { token: window.__YATSN_SIGNIN_TOKEN__ },
        });
        window.location.href = '/create';
      } catch (err) {
        setStatus(status, err.message || 'That link is invalid.', true);
      }
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    initAuthPages();
    initCreate();
    initGallery();
    initImage();
    initAccount();
    initOwner();
  });
})();
