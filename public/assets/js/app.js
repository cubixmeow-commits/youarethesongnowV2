(() => {
  const state = {
    csrf: null,
    draftId: null,
    songLookup: null,
    songConfirmed: false,
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

  const generation = {
    reviewed: false,
    pending: false,
    issue: null,
    directionPrepared: false,
    directionPath: null,
    directionLabel: null,
  };
  let reviewTimer = null;
  let generationSubmitLock = false;
  let flowStep = 'song';
  let portraitsLoadState = 'idle';
  let portraitsLoadError = '';
  let peopleCardWired = false;
  let stickyActionsWired = false;

  const FLOW_STEPS = ['song', 'people', 'direction', 'review', 'generating'];
  const FLOW_HEADINGS = {
    song: '#song-heading',
    people: '#people-heading',
    direction: '#direction-heading',
    review: '#review-heading',
    generating: '#generating-heading',
  };
  const FLOW_EYEBROWS = {
    song: '01 · SONG',
    people: '02 · PEOPLE',
    direction: '03 · DIRECTION',
    review: '04 · REVIEW',
    generating: 'CREATING',
  };
  const FLOW_TITLES = {
    song: 'What are we listening to?',
    people: 'Who belongs in this world?',
    direction: 'How should it feel?',
    review: 'Ready to generate',
    generating: 'Creating your image',
  };
  const FLOW_LEADS = {
    song: 'Start with the song. We’ll create its visual DNA.',
    people: 'Choose up to two portraits—or continue without people.',
    direction: 'Let AI lead, or build the scene yourself.',
    review: 'Confirm your creation before generating.',
    generating: 'Your artwork is being created in the background.',
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

  function getReadinessIssues() {
    const issues = [];
    if (!state.songLookup || !['found', 'fallbackFound'].includes(state.songLookup.state)) {
      issues.push('Choose and confirm a song.');
    } else if (!state.songConfirmed) {
      issues.push('Confirm your song match before generating.');
    }
    if (!state.selectedStyleId) {
      issues.push('Choose a style.');
    }
    return issues;
  }

  function setDirectionPrepared(path, label) {
    generation.directionPrepared = true;
    generation.directionPath = path || generation.directionPath || 'manual';
    if (label) {
      generation.directionLabel = label;
    } else if (path === 'manual' || !generation.directionLabel) {
      const styleObj = state.styles.find((s) => s.id === state.selectedStyleId);
      if (styleObj) generation.directionLabel = styleObj.name;
    }
    updateReviewPanel();
    updateGenerateAction();
  }

  function clearDirectionPrepared() {
    generation.directionPrepared = false;
    generation.directionPath = null;
    generation.directionLabel = null;
    generation.reviewed = false;
    generation.issue = null;
    updateGenerateAction();
  }

  function getWizardPrimaryButton() {
    return $('[data-create-sticky-primary]');
  }

  function syncSongStickyPrimary() {
    const primary = getWizardPrimaryButton();
    const action = window.YatsnSongSearch?.getStickyAction?.();
    if (!primary || !action) return;
    primary.textContent = action.label;
    primary.disabled = action.disabled;
    primary.classList.toggle('is-loading', action.loading);
    if (action.loading) primary.setAttribute('aria-busy', 'true');
    else primary.removeAttribute('aria-busy');
  }

  function updateStickyActions() {
    const primaryWrap = $('[data-create-sticky-primary-wrap]');
    const peopleWrap = $('[data-create-sticky-people]');
    const footnote = $('[data-create-footnote]');
    const scroll = $('[data-create-scroll]');

    if (primaryWrap) primaryWrap.hidden = flowStep !== 'song' && flowStep !== 'review';
    if (peopleWrap) peopleWrap.hidden = flowStep !== 'people';
    if (footnote) footnote.hidden = flowStep !== 'song';

    if (flowStep === 'song') {
      syncSongStickyPrimary();
    }

    if (scroll) {
      scroll.classList.toggle('is-scrollable', flowStep === 'direction' || flowStep === 'review');
    }
  }

  function shouldShowGenerateBar() {
    const paywall = $('[data-paywall]');
    return generation.directionPrepared
      && flowStep === 'review'
      && (!paywall || paywall.hidden);
  }

  function setFlowStep(step, options = {}) {
    if (!FLOW_STEPS.includes(step)) return;
    flowStep = step;
    const root = $('[data-create]');
    root?.classList.toggle('is-generating', step === 'generating');
    document.body.classList.toggle('is-create-generating', step === 'generating');

    $all('[data-create-card]').forEach((card) => {
      const name = card.getAttribute('data-create-card');
      card.hidden = name !== step;
    });

    const back = $('[data-create-back]');
    if (back) back.hidden = step === 'song' || step === 'generating';

    const titleEl = $('[data-create-focus-title]');
    if (titleEl) titleEl.textContent = FLOW_TITLES[step] || FLOW_TITLES.song;

    const eyebrow = $('[data-create-eyebrow]');
    if (eyebrow) eyebrow.textContent = FLOW_EYEBROWS[step] || FLOW_EYEBROWS.song;

    const leadEl = $('[data-create-focus-lead]');
    if (leadEl) leadEl.textContent = FLOW_LEADS[step] || '';

    const segmentOrder = ['song', 'people', 'direction', 'review'];
    const progressSegments = $all('[data-progress-segment]');
    const currentIndex = segmentOrder.indexOf(step === 'generating' ? 'review' : step);
    progressSegments.forEach((el) => {
      const name = el.getAttribute('data-progress-segment');
      const segIndex = segmentOrder.indexOf(name);
      el.classList.toggle('is-current', segIndex === currentIndex);
      el.classList.toggle('is-complete', segIndex >= 0 && segIndex < currentIndex);
    });

    if (options.announce) {
      const announcer = $('[data-create-announcer]');
      if (announcer) announcer.textContent = options.announce;
    }

    if (step === 'people') {
      loadPortraits({ refresh: portraitsLoadState === 'error' || portraitsLoadState === 'idle' }).catch(() => {
        // loadPortraits updates visible error state.
      });
    }

    updatePeoplePortraitRegions();
    updatePeopleContinue();
    updateReviewPanel();
    updateSummary();
    updateStickyActions();
    updateGenerateAction();

    if (options.focus !== false) {
      const heading = titleEl || $(FLOW_HEADINGS[step]);
      if (heading && !options.skipFocus) {
        if (!heading.hasAttribute('tabindex')) heading.setAttribute('tabindex', '-1');
        requestAnimationFrame(() => {
          try {
            heading.focus({ preventScroll: true });
          } catch (_) {
            heading.focus();
          }
        });
      }
    }
  }

  function resolveFlowStepFromState() {
    if (flowStep === 'generating') return 'generating';
    if (!state.songLookup || !state.songConfirmed || !['found', 'fallbackFound'].includes(state.songLookup.state)) {
      return 'song';
    }
    if (!generation.directionPrepared) return 'direction';
    return 'review';
  }

  function advanceToReview(announce = 'Review your creation') {
    setFlowStep('review', { announce, focus: true });
    scheduleGenerationReview();
  }

  function updatePeoplePortraitRegions() {
    const loading = $('[data-portrait-loading]');
    const errorWrap = $('[data-portrait-load-error]');
    const errorText = $('[data-portrait-load-error-text]');
    const empty = $('[data-portrait-empty]');
    const grid = $('[data-portrait-grid]');
    const hint = $('[data-people-hint]');
    const onPeople = flowStep === 'people';
    const hasTiles = state.portraits.length > 0;

    if (loading) loading.hidden = !onPeople || portraitsLoadState !== 'loading';
    if (errorWrap) errorWrap.hidden = !onPeople || portraitsLoadState !== 'error';
    if (errorText && portraitsLoadError) errorText.textContent = portraitsLoadError;
    if (empty) {
      empty.hidden = !onPeople || portraitsLoadState !== 'empty' || hasTiles;
    }
    if (grid) {
      grid.hidden = !hasTiles;
      if (hasTiles) grid.removeAttribute('aria-hidden');
      else grid.setAttribute('aria-hidden', 'true');
    }
    if (hint) {
      if (!onPeople) {
        hint.hidden = true;
      } else if (state.selectedPortraitIds.length >= 1) {
        hint.hidden = true;
      } else if (portraitsLoadState === 'loading') {
        hint.hidden = false;
        hint.textContent = 'Loading your portraits…';
      } else if (portraitsLoadState === 'error') {
        hint.hidden = false;
        hint.textContent = 'We could not load your saved portraits. Try again or continue without people.';
      } else if (portraitsLoadState === 'empty') {
        hint.hidden = false;
        hint.textContent = 'Add a portrait below or continue without people.';
      } else {
        hint.hidden = false;
        hint.textContent = 'Select up to two portraits, or continue without people.';
      }
    }
  }

  function updatePeopleContinue() {
    const btn = $('[data-people-continue]');
    const withoutBtn = $('[data-people-continue-without]');
    const count = state.selectedPortraitIds.length;
    if (btn) {
      btn.disabled = count < 1;
      btn.textContent = count === 1 ? 'Continue with 1 person' : count >= 2 ? 'Continue with 2 people' : 'Continue with selected people';
    }
    if (withoutBtn) {
      withoutBtn.disabled = portraitsLoadState === 'loading';
    }
    updatePeoplePortraitRegions();
  }

  let portraitsLoadPromise = null;

  async function loadPortraits(options = {}) {
    if (!options.refresh && (portraitsLoadState === 'loaded' || portraitsLoadState === 'empty')) {
      renderPortraits();
      updatePeoplePortraitRegions();
      updatePeopleContinue();
      return;
    }
    if (portraitsLoadPromise && !options.refresh) {
      return portraitsLoadPromise;
    }
    portraitsLoadState = 'loading';
    portraitsLoadError = '';
    updatePeoplePortraitRegions();
    portraitsLoadPromise = (async () => {
      try {
        const res = await api('/api/v1/portraits');
        state.portraits = Array.isArray(res.data) ? res.data : [];
        portraitsLoadState = state.portraits.length ? 'loaded' : 'empty';
        portraitsLoadError = '';
        renderPortraits();
        updateSummary();
      } catch (err) {
        portraitsLoadState = 'error';
        portraitsLoadError = err.message || 'We could not load your saved portraits.';
        renderPortraits();
      } finally {
        updatePeopleContinue();
      }
    })();
    try {
      await portraitsLoadPromise;
    } finally {
      portraitsLoadPromise = null;
    }
  }

  function updateReviewPanel() {
    const styleObj = state.styles.find((s) => s.id === state.selectedStyleId);
    const directionName = $('[data-review-direction-name]');
    if (directionName) {
      directionName.textContent = generation.directionLabel
        || (styleObj ? styleObj.name : 'Not chosen yet');
    }
    const output = $('[data-review-output]');
    if (output) {
      const q = state.quality ? state.quality[0].toUpperCase() + state.quality.slice(1) : 'Medium';
      const o = state.orientation ? state.orientation[0].toUpperCase() + state.orientation.slice(1) : 'Square';
      output.textContent = `${q} · ${o}`;
    }
    const fineStyle = $('[data-fine-tune-style]');
    const allowManualStyle = generation.directionPath === 'manual' || !generation.directionPath;
    if (fineStyle) fineStyle.hidden = !allowManualStyle;

    const reviewPortraits = $('[data-review-portraits]');
    if (reviewPortraits) {
      reviewPortraits.innerHTML = '';
      const selected = state.portraits.filter((p) => state.selectedPortraitIds.includes(p.id));
      if (selected.length && flowStep === 'review') {
        reviewPortraits.hidden = false;
        reviewPortraits.removeAttribute('aria-hidden');
        selected.forEach((p) => {
          const img = document.createElement('img');
          img.src = p.thumbnailUrl;
          img.alt = '';
          img.className = 'create-review__portrait-thumb';
          reviewPortraits.appendChild(img);
        });
      } else {
        reviewPortraits.hidden = true;
        reviewPortraits.setAttribute('aria-hidden', 'true');
      }
    }
  }

  function goBackOneStep() {
    if (flowStep === 'people') {
      setFlowStep('song', { announce: 'Song step' });
      return;
    }
    if (flowStep === 'direction') {
      setFlowStep('people', { announce: 'People step' });
      return;
    }
    if (flowStep === 'review') {
      setFlowStep('direction', { announce: 'Direction step', skipFocus: false });
      updateGenerateAction();
    }
  }

  async function syncDraftFromForm() {
    state.noText = !!$('[data-no-text]')?.checked;
    state.special = $('[data-special-toggle]')?.checked ? ($('[data-special]')?.value || '') : '';
    return patchDraft({
      noTextInImage: state.noText,
      specialInstructions: state.special || null,
      styleId: state.selectedStyleId,
      portraitIds: state.selectedPortraitIds,
      quality: state.quality,
      orientation: state.orientation,
    });
  }

  function invalidateGenerationReview() {
    generation.reviewed = false;
    generation.issue = null;
    updateGenerateAction();
  }

  function scheduleGenerationReview() {
    clearTimeout(reviewTimer);
    reviewTimer = setTimeout(() => {
      refreshGenerationReadiness().catch(() => {
        // updateGenerateAction already reflects the failure state.
      });
    }, 250);
  }

  function updateGenerateAction() {
    const root = $('[data-create]');
    const hint = $('[data-generate-hint]');
    const button = getWizardPrimaryButton();
    const showReview = shouldShowGenerateBar();

    root?.classList.toggle('has-generate-bar', showReview);
    updateStickyActions();

    if (flowStep === 'review' && button) {
      button.textContent = 'Generate image';
      const issues = getReadinessIssues();
      const ready = showReview && issues.length === 0 && generation.reviewed && !generation.pending;
      button.disabled = !ready;
      button.setAttribute('aria-disabled', ready ? 'false' : 'true');
      button.classList.toggle('is-loading', generation.pending);
      if (generation.pending) button.setAttribute('aria-busy', 'true');
      else button.removeAttribute('aria-busy');
    }

    let hintText = '';
    if (!showReview) {
      hintText = '';
    } else if (generation.pending) {
      hintText = 'Starting generation…';
    } else if (getReadinessIssues().length) {
      hintText = getReadinessIssues()[0];
    } else if (generation.issue) {
      hintText = generation.issue;
    } else if (!generation.reviewed) {
      hintText = 'Checking your creation is ready…';
    }
    if (hint) {
      hint.textContent = hintText;
    }
  }

  async function refreshGenerationReadiness() {
    if (flowStep !== 'review' && flowStep !== 'direction') return { ready: false };
    const issues = getReadinessIssues();
    if (issues.length) {
      generation.reviewed = false;
      generation.issue = issues[0];
      updateGenerateAction();
      return { ready: false, issue: issues[0] };
    }
    try {
      await syncDraftFromForm();
      const summary = await api(`/api/v1/creation-drafts/${state.draftId}/summary`, { method: 'POST', body: {} });
      if (!summary.data.ready) {
        const first = Object.values(summary.data.issues || {})[0] || 'Finish your creation before continuing.';
        generation.reviewed = false;
        generation.issue = first;
        updateGenerateAction();
        return { ready: false, issue: first };
      }
      generation.reviewed = true;
      generation.issue = null;
      updateGenerateAction();
      return { ready: true };
    } catch (err) {
      generation.reviewed = false;
      generation.issue = err.message || 'Could not verify your creation.';
      updateGenerateAction();
      return { ready: false, issue: generation.issue };
    }
  }

  async function runReview() {
    const status = $('[data-direction-status]');
    const result = await refreshGenerationReadiness();
    if (result.ready) {
      const styleObj = state.styles.find((s) => s.id === state.selectedStyleId);
      setDirectionPrepared('manual', styleObj?.name);
      setStatus(status, '');
      $('[data-paywall]').hidden = true;
      advanceToReview('Review your creation');
    } else if (status) {
      setStatus(status, result.issue || 'Finish your creation before continuing.', true);
    }
    return result;
  }

  async function submitGeneration() {
    const button = getWizardPrimaryButton();
    if (!button || button.disabled || generation.pending || generationSubmitLock || flowStep !== 'review') {
      return { started: false };
    }
    generationSubmitLock = true;
    try {
      const summary = await api(`/api/v1/creation-drafts/${state.draftId}/summary`, { method: 'POST', body: {} });
      if (!summary.data.ready) {
        const first = Object.values(summary.data.issues || {})[0] || 'Finish your creation before continuing.';
        generation.reviewed = false;
        generation.issue = first;
        updateGenerateAction();
        return { started: false, issue: first };
      }
      if (summary.data.requiresMembership) {
        $('[data-paywall]').hidden = false;
        return { started: false, requiresMembership: true };
      }
      generation.pending = true;
      updateGenerateAction();
      await startGeneration();
      return { started: true };
    } catch (err) {
      generation.pending = false;
      generation.reviewed = true;
      generation.issue = err.message || 'Could not start generation.';
      updateGenerateAction();
      return { started: false, issue: generation.issue };
    } finally {
      generationSubmitLock = false;
    }
  }

  function restoreGenerateActionAfterFailure(message) {
    generation.pending = false;
    generation.reviewed = true;
    generation.issue = message || null;
    setFlowStep('review', { announce: 'Review your creation', focus: false });
    updateGenerateAction();
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
    const songNodes = $all('[data-sum-song]');
    if (!songNodes.length) return;

    const songLabel = state.songLookup
      ? `${state.songLookup.title} · ${state.songLookup.artist}`
      : 'Not chosen yet';
    songNodes.forEach((node) => {
      node.textContent = songLabel;
    });
    const people = $('[data-sum-people]');
    const style = $('[data-sum-style]');
    const quality = $('[data-sum-quality]');
    const orientation = $('[data-sum-orientation]');
    const credits = $('[data-sum-credits]');
    if (people) {
      people.textContent = state.selectedPortraitIds.length
        ? `${state.selectedPortraitIds.length} selected`
        : 'None selected';
    }
    const styleObj = state.styles.find((s) => s.id === state.selectedStyleId);
    if (style) style.textContent = styleObj ? styleObj.name : 'Not chosen yet';
    if (quality) quality.textContent = state.quality[0].toUpperCase() + state.quality.slice(1);
    if (orientation) orientation.textContent = state.orientation[0].toUpperCase() + state.orientation.slice(1);
    const price = state.options?.qualities?.find((q) => q.id === state.quality)?.credits;
    if (credits) credits.textContent = price != null ? String(price) : '—';

    const createRoot = $('[data-create]');
    if (createRoot) {
      createRoot.classList.toggle('has-song', Boolean(state.songConfirmed && state.songLookup));
    }
    updatePeopleContinue();
    updateReviewPanel();
    updateGenerateAction();
  }

  function renderPortraits() {
    const grid = $('[data-portrait-grid]');
    if (!grid) return;
    grid.innerHTML = '';
    if (!Array.isArray(state.portraits)) state.portraits = [];
    state.portraits.forEach((p) => {
      const tile = document.createElement('div');
      tile.className = 'portrait-chip' + (state.selectedPortraitIds.includes(p.id) ? ' is-selected' : '');
      tile.setAttribute('role', 'listitem');

      const selectBtn = document.createElement('button');
      selectBtn.type = 'button';
      selectBtn.className = 'portrait-chip__select';
      selectBtn.setAttribute('aria-pressed', state.selectedPortraitIds.includes(p.id) ? 'true' : 'false');
      selectBtn.setAttribute('aria-label', state.selectedPortraitIds.includes(p.id) ? 'Deselect portrait' : 'Include portrait in session');
      const img = document.createElement('img');
      img.src = p.thumbnailUrl || '';
      img.alt = '';
      img.addEventListener('error', () => {
        tile.classList.add('is-image-error');
        selectBtn.setAttribute('aria-label', 'Portrait image could not load');
      });
      selectBtn.appendChild(img);
      selectBtn.addEventListener('click', async () => {
        if (state.selectedPortraitIds.includes(p.id)) {
          state.selectedPortraitIds = state.selectedPortraitIds.filter((id) => id !== p.id);
        } else if (state.selectedPortraitIds.length < 2) {
          state.selectedPortraitIds = [...state.selectedPortraitIds, p.id];
        } else {
          state.selectedPortraitIds = [state.selectedPortraitIds[1], p.id];
        }
        await patchDraft({ portraitIds: state.selectedPortraitIds });
        if (generation.directionPrepared) {
          clearDirectionPrepared();
        }
        renderPortraits();
        updateSummary();
        updatePeopleContinue();
        invalidateGenerationReview();
      });

      const deleteBtn = document.createElement('button');
      deleteBtn.type = 'button';
      deleteBtn.className = 'portrait-chip__delete';
      deleteBtn.setAttribute('aria-label', 'Delete portrait');
      deleteBtn.innerHTML = '<span aria-hidden="true">×</span>';
      deleteBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        openPortraitDeleteDialog(p.id);
      });

      tile.appendChild(selectBtn);
      tile.appendChild(deleteBtn);
      grid.appendChild(tile);
    });
  }

  let pendingPortraitDeleteId = null;

  function openPortraitDeleteDialog(portraitId) {
    pendingPortraitDeleteId = portraitId;
    const dialog = $('[data-portrait-delete-dialog]');
    if (!dialog) return;
    if (typeof dialog.showModal === 'function') {
      dialog.showModal();
    } else {
      dialog.setAttribute('open', 'open');
    }
  }

  async function deletePortrait(portraitId) {
    const status = $('[data-portrait-status]');
    try {
      await api(`/api/v1/portraits/${encodeURIComponent(portraitId)}`, { method: 'DELETE' });
      state.portraits = state.portraits.filter((p) => p.id !== portraitId);
      portraitsLoadState = state.portraits.length ? 'loaded' : 'empty';
      const wasSelected = state.selectedPortraitIds.includes(portraitId);
      state.selectedPortraitIds = state.selectedPortraitIds.filter((id) => id !== portraitId);
      if (wasSelected) {
        await patchDraft({ portraitIds: state.selectedPortraitIds });
      }
      renderPortraits();
      updateSummary();
      updatePeopleContinue();
      invalidateGenerationReview();
      if (generation.directionPrepared) scheduleGenerationReview();
    } catch (err) {
      setStatus(status, err.message || 'Could not delete this portrait.', true);
    }
  }

  function showManualDirectionControls() {
    const manual = $('[data-direction-manual]');
    if (manual) manual.hidden = false;
    const controls = $('#the-direction .direction-controls');
    if (controls) controls.classList.add('is-manual-active');
  }

  function hideManualDirectionControls() {
    const manual = $('[data-direction-manual]');
    if (manual) manual.hidden = true;
    const controls = $('#the-direction .direction-controls');
    if (controls) controls.classList.remove('is-manual-active');
  }

  function renderStyles() {
    const grids = [$('[data-style-grid]'), $('[data-style-grid-fine]')].filter(Boolean);
    if (!grids.length) return;
    grids.forEach((grid) => {
      grid.innerHTML = '';
      state.styles.forEach((s) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'style-option' + (state.selectedStyleId === s.id ? ' is-selected' : '');
        btn.setAttribute('role', 'option');
        btn.setAttribute('aria-selected', state.selectedStyleId === s.id ? 'true' : 'false');
        btn.dataset.styleId = String(s.id);
        btn.innerHTML = `<strong>${s.name}</strong><span class="quiet">${s.description}</span>`;
        btn.addEventListener('click', async () => {
          state.selectedStyleId = s.id;
          generation.directionLabel = s.name;
          await patchDraft({ styleId: s.id });
          renderStyles();
          updateSummary();
          invalidateGenerationReview();
          scheduleGenerationReview();
        });
        grid.appendChild(btn);
      });
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
        invalidateGenerationReview();
        scheduleGenerationReview();
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
        invalidateGenerationReview();
        scheduleGenerationReview();
      });
      oRow.appendChild(label);
    });
  }

  function clearDownstreamFromSong() {
    clearDirectionPrepared();
    hideManualDirectionControls();
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
      if (status) {
        status.textContent = research.analysisBasis === 'lyrics'
          ? `Gemini used Google Search to locate the lyrics and created lyric-based Song DNA with ${confidence}% match confidence. The worker will repeat this analysis when you generate the image.`
          : research.analysisBasis === 'song-context'
            ? `Gemini could not confirm the exact lyrics, so it created grounded Song DNA from reliable public information about the song with ${confidence}% match confidence. This is the approved song-context fallback, not a claim that lyrics were analyzed.`
            : `Gemini created complete Song DNA using the proven V1 development method with ${confidence}% match confidence. Google Search verification metadata was unavailable, so this result is not labeled as confirmed lyric analysis.`;
      }
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
          'grounded-response-unparseable': 'Gemini completed Google Search but did not return Song DNA in the required structure.',
          'grounded-analysis-incomplete': 'Gemini completed Google Search but did not return enough reliable Song DNA to generate an image.',
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

  async function loadCreateEntryContext() {
    // Phase 2: no resume row. There is no list-drafts or alternate-draft contract to
    // distinguish a genuinely resumable creation from the current empty draft.
    try {
      const images = await api('/api/v1/images');
      if (images.data?.length) {
        window.YatsnSongSearch?.renderRecent?.(images.data);
      }
    } catch (_) {
      // Recent work is optional presentation; never block Create entry.
    }
  }

  function initSongSearch() {
    if (!window.YatsnSongSearch) return;
    window.YatsnSongSearch.init({
      onFind: async ({ artist, title }) => {
        const res = await api('/api/v1/song-lookups', {
          method: 'POST',
          body: { artist, title },
        });
        state.songLookup = res.data;
        state.songConfirmed = false;
        clearDownstreamFromSong();
        renderDevelopmentAnalysis(res.data);
        await patchDraft({ songLookupId: res.data.id });
        updateSummary();
        return res.data;
      },
      onConfirm: async (lookup) => {
        state.songLookup = lookup;
        state.songConfirmed = true;
        await patchDraft({ songLookupId: lookup.id });
        updateSummary();
        setFlowStep('people', { announce: 'Choose your people' });
      },
      onClear: () => {
        state.songConfirmed = false;
        clearDownstreamFromSong();
        setFlowStep('song', { announce: 'Song step' });
      },
      onStickyActionChange: () => {
        if (flowStep === 'song') syncSongStickyPrimary();
      },
    });
  }

  function wireStickyActions() {
    if (stickyActionsWired) return;
    stickyActionsWired = true;
    getWizardPrimaryButton()?.addEventListener('click', async () => {
      if (flowStep === 'song') {
        if (window.YatsnSongSearch?.isInFlight?.()) return;
        const action = window.YatsnSongSearch?.getStickyAction?.();
        if (action?.intent === 'find') await window.YatsnSongSearch?.submitFind?.();
        else if (action?.intent === 'use') await window.YatsnSongSearch?.confirmPending?.();
        return;
      }
      if (flowStep === 'review') await submitGeneration();
    });
  }

  function wirePeopleCard() {
    if (peopleCardWired) return;
    peopleCardWired = true;

    $('[data-create-back]')?.addEventListener('click', () => goBackOneStep());

    $('[data-people-continue]')?.addEventListener('click', () => {
      if (state.selectedPortraitIds.length < 1) return;
      hideManualDirectionControls();
      setFlowStep('direction', { announce: 'Choose your direction' });
    });

    $('[data-people-continue-without]')?.addEventListener('click', () => {
      hideManualDirectionControls();
      setFlowStep('direction', { announce: 'Choose your direction' });
    });

    $('[data-portrait-retry]')?.addEventListener('click', () => {
      loadPortraits({ refresh: true }).catch(() => {
        // loadPortraits updates visible error state.
      });
    });

    $('[data-portrait-upload-toggle]')?.addEventListener('click', () => {
      const panel = $('[data-portrait-upload-panel]');
      const toggle = $('[data-portrait-upload-toggle]');
      if (!panel) return;
      const show = panel.hidden;
      panel.hidden = !show;
      if (toggle) toggle.setAttribute('aria-expanded', show ? 'true' : 'false');
    });

    $('#portrait-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const status = $('[data-portrait-status]');
      setStatus(status, 'Uploading...');
      try {
        const fd = new FormData(e.target);
        const res = await api('/api/v1/portraits', { method: 'POST', body: fd });
        state.portraits = [res.data, ...state.portraits];
        portraitsLoadState = state.portraits.length ? 'loaded' : 'empty';
        portraitsLoadError = '';
        if (state.selectedPortraitIds.length < 2) {
          state.selectedPortraitIds.push(res.data.id);
          await patchDraft({ portraitIds: state.selectedPortraitIds });
        }
        e.target.reset();
        setStatus(status, 'Portrait uploaded.');
        renderPortraits();
        updateSummary();
        updatePeopleContinue();
        invalidateGenerationReview();
        if (generation.directionPrepared) clearDirectionPrepared();
      } catch (err) {
        setStatus(status, 'We could not upload this photo. Choose another photo or try again.', true);
      }
    });

    const deleteDialog = $('[data-portrait-delete-dialog]');
    deleteDialog?.addEventListener('close', async () => {
      const portraitId = pendingPortraitDeleteId;
      pendingPortraitDeleteId = null;
      if (!portraitId || deleteDialog.returnValue !== 'confirm') return;
      await deletePortrait(portraitId);
    });
    deleteDialog?.querySelector('[data-portrait-delete-confirm]')?.addEventListener('click', () => {
      deleteDialog.returnValue = 'confirm';
    });
  }

  async function initCreate() {
    const root = $('[data-create]');
    if (!root) return;
    state.csrf = root.dataset.csrf;
    initSongSearch();
    wireStickyActions();
    wirePeopleCard();

    const me = await api('/api/v1/me');
    state.csrf = me.data.csrfToken || state.csrf;
    const styles = await api('/api/v1/styles');
    state.styles = styles.data;
    const options = await api('/api/v1/product-options');
    state.options = options.data;
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
    try {
      await loadPortraits({ refresh: true });
    } catch (_) {
      // loadPortraits records error state for the People card.
    }
    if (state.songLookup && ['found', 'fallbackFound'].includes(state.songLookup.state)) {
      state.songConfirmed = true;
      window.YatsnSongSearch?.restoreConfirmed?.(state.songLookup);
    }
    if (state.selectedStyleId && state.songConfirmed) {
      generation.directionPrepared = true;
      generation.directionPath = 'manual';
      generation.reviewed = false;
    }
    const initialStep = resolveFlowStepFromState();
    setFlowStep(initialStep, { focus: false });
    if (initialStep === 'review') {
      scheduleGenerationReview();
    }

    updateSummary();
    updateGenerateAction();
    syncSongStickyPrimary();
    loadCreateEntryContext().catch(() => {
      // Recent work is optional presentation.
    });

    $('[data-special-toggle]')?.addEventListener('change', async (e) => {
      const wrap = $('[data-special-wrap]');
      if (wrap) wrap.hidden = !e.target.checked;
      await syncDraftFromForm();
      invalidateGenerationReview();
      scheduleGenerationReview();
    });
    $('[data-special]')?.addEventListener('input', async () => {
      await syncDraftFromForm();
      invalidateGenerationReview();
      scheduleGenerationReview();
    });
    $('[data-special]')?.addEventListener('focus', () => {
      window.setTimeout(() => {
        $('[data-create-sticky-actions]')?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      }, 250);
    });
    $('[data-no-text]')?.addEventListener('change', async () => {
      await syncDraftFromForm();
      invalidateGenerationReview();
      scheduleGenerationReview();
    });

    $('[data-review]')?.addEventListener('click', async () => {
      await runReview();
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
    setFlowStep('generating', { announce: 'Creating your image', focus: true });
    const progress = $('[data-progress]');
    if (progress) progress.hidden = false;
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
      generation.pending = false;
      if (err.code === 'membership_required') {
        setFlowStep('review', { focus: false });
        $('[data-paywall]').hidden = false;
        updateGenerateAction();
        return;
      }
      restoreGenerateActionAfterFailure(err.message);
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
          restoreGenerateActionAfterFailure(job.message || 'We could not deliver a usable image. Your credits were returned. You can try again.');
          if (copyEl) copyEl.textContent = job.message || 'We could not deliver a usable image. Your credits were returned. You can try again.';
          const progressNote = $('[data-progress-note]');
          if (progressNote) progressNote.hidden = true;
          return;
        }
        state.pollTimer = setTimeout(tick, 1200);
      } catch (err) {
        restoreGenerateActionAfterFailure(err.message);
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

    const renderStylesTable = (el, rows) => {
      if (!rows.length) {
        el.textContent = 'None yet.';
        return;
      }
      const table = document.createElement('table');
      table.innerHTML = '<thead><tr><th>Name</th><th>Status</th><th>Category</th><th>Key</th></tr></thead>';
      const tbody = document.createElement('tbody');
      rows.forEach((row) => {
        const tr = document.createElement('tr');

        const nameTd = document.createElement('td');
        nameTd.textContent = row.name ?? '';

        const statusTd = document.createElement('td');
        const statusWrap = document.createElement('div');
        statusWrap.className = 'owner-style-status';
        const statusText = document.createElement('span');
        statusText.className = 'owner-style-status__label';
        statusText.textContent = row.status ?? '';
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'btn btn--ghost owner-style-toggle';
        const setToggleLabel = (status) => {
          toggleBtn.textContent = status === 'active' ? 'Deactivate' : 'Activate';
          toggleBtn.title = status === 'active' ? 'Deactivate this style' : 'Activate this style';
        };
        setToggleLabel(row.status);
        toggleBtn.addEventListener('click', async () => {
          const next = row.status === 'active' ? 'inactive' : 'active';
          toggleBtn.disabled = true;
          try {
            const res = await api(`/api/v1/owner/styles/${row.id}`, {
              method: 'PATCH',
              body: { status: next },
            });
            row.status = res.data.status;
            statusText.textContent = row.status;
            setToggleLabel(row.status);
          } catch (err) {
            toggleBtn.textContent = 'Failed';
            toggleBtn.title = err.message || 'Could not update style.';
            setTimeout(() => setToggleLabel(row.status), 1800);
          } finally {
            toggleBtn.disabled = false;
          }
        });
        statusWrap.appendChild(statusText);
        statusWrap.appendChild(toggleBtn);
        statusTd.appendChild(statusWrap);

        const categoryTd = document.createElement('td');
        categoryTd.textContent = row.category ?? '';

        const keyTd = document.createElement('td');
        keyTd.textContent = row.styleKey ?? '';

        tr.append(nameTd, statusTd, categoryTd, keyTd);
        tbody.appendChild(tr);
      });
      table.appendChild(tbody);
      el.innerHTML = '';
      el.appendChild(table);
    };

    const styles = await api('/api/v1/owner/styles');
    renderStylesTable($('[data-styles]'), styles.data);

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

  window.YatsnCreate = {
    prepareAndReview: runReview,
    refreshGenerationReadiness,
    submitGeneration,
    setDirectionPrepared,
    clearDirectionPrepared,
    advanceToReview,
    setFlowStep,
    getFlowStep: () => flowStep,
    showManualDirectionControls,
    hideManualDirectionControls,
    shouldShowGenerateBar,
    isGenerateReady: () => shouldShowGenerateBar() && getReadinessIssues().length === 0 && generation.reviewed && !generation.pending,
    getGenerateBarState: () => {
      const button = getWizardPrimaryButton();
      const hint = $('[data-generate-hint]');
      const showReview = shouldShowGenerateBar();
      return {
        prepared: generation.directionPrepared,
        path: generation.directionPath,
        hidden: !showReview,
        display: showReview ? 'block' : 'none',
        hint: hint?.textContent || '',
        disabled: button?.disabled ?? true,
        reviewed: generation.reviewed,
        pending: generation.pending,
      };
    },
    setGenerationFixtureState: (fixture = {}) => {
      if (typeof fixture.reviewed === 'boolean') generation.reviewed = fixture.reviewed;
      if (typeof fixture.pending === 'boolean') generation.pending = fixture.pending;
      if (Object.prototype.hasOwnProperty.call(fixture, 'issue')) generation.issue = fixture.issue;
      if (typeof fixture.directionPrepared === 'boolean') generation.directionPrepared = fixture.directionPrepared;
      if (Object.prototype.hasOwnProperty.call(fixture, 'directionPath')) generation.directionPath = fixture.directionPath;
      updateGenerateAction();
    },
  };

  if (document.querySelector('[data-create]')?.dataset.privateBuild === '1') {
    window.YatsnCreateFixtures = {
      showPeopleStage() {
        state.songConfirmed = true;
        state.songLookup = state.songLookup || { title: 'Seven Pillars of Wisdom', artist: 'Sabaton', state: 'found' };
        state.selectedPortraitIds = [];
        state.selectedStyleId = null;
        clearDirectionPrepared();
        hideManualDirectionControls();
        setFlowStep('people', { focus: false });
      },
      showDirectionChoice() {
        state.songConfirmed = true;
        state.songLookup = state.songLookup || { title: 'Seven Pillars of Wisdom', artist: 'Sabaton', state: 'found' };
        state.selectedPortraitIds = state.selectedPortraitIds.length ? state.selectedPortraitIds : ['fixture-portrait'];
        state.selectedStyleId = null;
        clearDirectionPrepared();
        hideManualDirectionControls();
        setFlowStep('direction', { focus: false });
      },
      showPreparedReady() {
        this.showDirectionChoice();
        state.selectedStyleId = state.selectedStyleId || state.styles[0]?.id || null;
        generation.directionPrepared = true;
        generation.directionPath = 'ai-quick';
        generation.directionLabel = 'Static Revolt';
        generation.reviewed = true;
        generation.pending = false;
        generation.issue = null;
        setFlowStep('review', { focus: false });
      },
      showPreparedMissingStyle() {
        this.showDirectionChoice();
        state.selectedStyleId = null;
        generation.directionPrepared = true;
        generation.directionPath = 'ai-explore';
        generation.reviewed = false;
        generation.pending = false;
        generation.issue = null;
        setFlowStep('review', { focus: false });
      },
      showPending() {
        this.showPreparedReady();
        generation.pending = true;
        updateGenerateAction();
      },
      showRecoverableError() {
        this.showPreparedReady();
        generation.pending = false;
        generation.reviewed = true;
        generation.issue = 'We could not deliver a usable image. Your credits were returned. You can try again.';
        updateGenerateAction();
      },
      showRestoredDraft() {
        state.songConfirmed = true;
        state.songLookup = { title: 'Seven Pillars of Wisdom', artist: 'Sabaton', state: 'found' };
        state.selectedPortraitIds = ['fixture-portrait'];
        state.selectedStyleId = state.styles[0]?.id || null;
        generation.directionPrepared = true;
        generation.directionPath = 'manual';
        generation.reviewed = true;
        generation.pending = false;
        generation.issue = null;
        setFlowStep('review', { focus: false });
      },
      showSongStage() {
        state.songConfirmed = false;
        state.songLookup = null;
        state.selectedPortraitIds = [];
        clearDirectionPrepared();
        setFlowStep('song', { focus: false });
      },
    };
  }
})();
