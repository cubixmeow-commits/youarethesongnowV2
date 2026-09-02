(() => {
  const panel = document.querySelector('[data-yatsn-song-search]');
  if (!panel) return;

  let handlers = {};
  let inFlight = false;
  let pendingLookup = null;
  let confirmedLookup = null;
  let formWired = false;

  function privateBuildAllowsFixtures() {
    return document.querySelector('[data-create]')?.dataset.privateBuild === '1';
  }

  function setState(state) {
    panel.dataset.yatsnSongState = state;
  }

  function $(sel) {
    return panel.querySelector(sel);
  }

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>'"]/g, (char) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;',
    })[char]);
  }

  function matchQualifier(state) {
    if (state === 'found') return 'Match confirmed';
    if (state === 'fallbackFound') return 'Inspired by available information';
    if (state === 'notFound') return 'No reliable match';
    return '';
  }

  function matchMessage(state) {
    if (state === 'notFound') {
      return 'We could not find enough reliable information about that song. Check the artist and title, or choose another song. No generation credits were used.';
    }
    if (state === 'fallbackFound') {
      return 'We found reliable information about your song and will use its themes and feeling to inspire your image.';
    }
    if (state === 'found') {
      return 'We found your song and will use its themes and feeling to inspire your image.';
    }
    return '';
  }

  function artworkMarkup(lookup) {
    const url = lookup?.artworkUrl || lookup?.thumbnailUrl;
    if (url) {
      return `<img class="yatsn-song-result__img" src="${escapeHtml(url)}" alt="" decoding="async">`;
    }
    return '<span class="yatsn-song-result__fallback" aria-hidden="true"></span>';
  }

  function setStatus(message, tone = 'info', { hidden = false } = {}) {
    const status = $('[data-song-status]');
    if (!status) return;
    status.hidden = hidden && !message;
    status.textContent = message || '';
    status.classList.remove('yatsn-status--info', 'yatsn-status--success', 'yatsn-status--warning', 'yatsn-status--error', 'is-error');
    if (!message) return;
    if (tone === 'error') {
      status.classList.add('yatsn-status--error', 'is-error');
    } else if (tone === 'success') {
      status.classList.add('yatsn-status--success');
    } else if (tone === 'warning') {
      status.classList.add('yatsn-status--warning');
    } else {
      status.classList.add('yatsn-status--info');
    }
  }

  function setFormDisabled(disabled) {
    const form = $('#song-form');
    if (!form) return;
    form.querySelectorAll('input, button, textarea, select').forEach((el) => {
      el.disabled = disabled;
    });
    form.classList.toggle('is-busy', disabled);
  }

  function showLoading() {
    setState('loading');
    setFormDisabled(true);
    const submit = $('.yatsn-song-search__submit');
    if (submit) submit.classList.add('is-loading');
    setStatus('Finding your song…', 'info');
    const loading = $('[data-song-result-loading]');
    const results = $('[data-song-results]');
    const selected = $('[data-song-selected]');
    const retry = $('[data-song-retry-wrap]');
    if (loading) loading.hidden = false;
    if (results) {
      results.hidden = true;
      results.innerHTML = '';
    }
    if (selected) selected.hidden = true;
    if (retry) retry.hidden = true;
    panel.setAttribute('aria-busy', 'true');
  }

  function hideLoading() {
    const loading = $('[data-song-result-loading]');
    const submit = $('.yatsn-song-search__submit');
    if (submit) submit.classList.remove('is-loading');
    if (loading) loading.hidden = true;
    panel.removeAttribute('aria-busy');
  }

  function resultAccessibleName(lookup) {
    const qualifier = matchQualifier(lookup.state);
    return `${lookup.title}, ${lookup.artist}${qualifier ? `, ${qualifier}` : ''}`;
  }

  function renderResultCard(lookup, { selectable = true } = {}) {
    const results = $('[data-song-results]');
    if (!results) return null;
    results.innerHTML = '';
    const isSelectable = selectable && ['found', 'fallbackFound'].includes(lookup.state);
    const row = document.createElement(isSelectable ? 'button' : 'div');
    row.type = isSelectable ? 'button' : undefined;
    row.className = 'yatsn-song-result';
    if (lookup.state === 'notFound') row.classList.add('yatsn-song-result--unavailable');
    row.dataset.songResult = '';
    if (isSelectable) {
      row.setAttribute('aria-label', resultAccessibleName(lookup));
    }
    row.innerHTML = `
      <span class="yatsn-song-result__art yatsn-artwork__stage">${artworkMarkup(lookup)}</span>
      <span class="yatsn-song-result__copy">
        <strong class="yatsn-song-result__title">${escapeHtml(lookup.title)}</strong>
        <span class="yatsn-song-result__artist">${escapeHtml(lookup.artist)}</span>
        <span class="yatsn-song-result__qualifier">${escapeHtml(matchQualifier(lookup.state))}</span>
      </span>
      ${isSelectable ? '<span class="yatsn-song-result__affordance" aria-hidden="true"></span>' : ''}
    `;
    if (isSelectable) {
      row.addEventListener('click', () => selectLookup(lookup));
    }
    results.appendChild(row);
    results.hidden = false;
    return row;
  }

  function renderSelected(lookup) {
    const selected = $('[data-song-selected]');
    const results = $('[data-song-results]');
    if (!selected) return;
    if (results) results.hidden = true;
    selected.hidden = false;
    selected.innerHTML = `
      <div class="yatsn-song-selected__card">
        <span class="yatsn-song-result__art yatsn-artwork__stage is-ready">${artworkMarkup(lookup)}</span>
        <div class="yatsn-song-selected__copy">
          <p class="yatsn-song-selected__label">Your song</p>
          <strong class="yatsn-song-result__title">${escapeHtml(lookup.title)}</strong>
          <span class="yatsn-song-result__artist">${escapeHtml(lookup.artist)}</span>
          <span class="yatsn-song-result__qualifier">${escapeHtml(matchQualifier(lookup.state))}</span>
        </div>
      </div>
      <button type="button" class="yatsn-btn yatsn-btn--quiet yatsn-song-selected__change" data-song-change>Change song</button>
    `;
    selected.querySelector('[data-song-change]')?.addEventListener('click', () => {
      confirmedLookup = null;
      pendingLookup = lookup;
      setState('result');
      selected.hidden = true;
      renderResultCard(lookup);
      setStatus('Choose this song or search again.', 'info');
      setFormDisabled(false);
      handlers.onClear?.(lookup);
    });
  }

  function selectLookup(lookup) {
    if (inFlight) return;
    pendingLookup = lookup;
    confirmedLookup = lookup;
    setState('selected');
    setStatus(matchMessage(lookup.state), lookup.state === 'notFound' ? 'error' : 'success');
    renderSelected(lookup);
    handlers.onConfirm?.(lookup);
  }

  function presentLookup(lookup) {
    hideLoading();
    setFormDisabled(false);
    pendingLookup = lookup;
    confirmedLookup = null;
    const retry = $('[data-song-retry-wrap]');
    if (retry) retry.hidden = true;

    if (!lookup) {
      setState('empty');
      setStatus('', 'info', { hidden: true });
      return;
    }

    if (lookup.state === 'notFound') {
      setState('empty');
      renderResultCard(lookup, { selectable: false });
      setStatus(matchMessage('notFound'), 'error');
      if (retry) retry.hidden = false;
      return;
    }

    setState('result');
    renderResultCard(lookup);
    setStatus(matchMessage(lookup.state), 'success');
  }

  function presentError(message, { retryable = true } = {}) {
    hideLoading();
    setFormDisabled(false);
    setState('error');
    const results = $('[data-song-results]');
    const selected = $('[data-song-selected]');
    const retry = $('[data-song-retry-wrap]');
    if (results) {
      results.hidden = true;
      results.innerHTML = '';
    }
    if (selected) selected.hidden = true;
    setStatus(message || 'Could not find your song. Try again.', 'error');
    if (retry) retry.hidden = !retryable;
  }

  function restoreConfirmed(lookup) {
    if (!lookup) return;
    pendingLookup = lookup;
    confirmedLookup = lookup;
    hideLoading();
    setFormDisabled(false);
    const form = $('#song-form');
    if (form) {
      const artist = form.querySelector('[name="artist"]');
      const title = form.querySelector('[name="title"]');
      if (artist) artist.value = lookup.artist || '';
      if (title) title.value = lookup.title || '';
    }
    renderSelected(lookup);
    setState('selected');
    setStatus(matchMessage(lookup.state), lookup.state === 'notFound' ? 'error' : 'success');
  }

  function getFormValues() {
    const form = $('#song-form');
    if (!form) return { artist: '', title: '' };
    return {
      artist: String(new FormData(form).get('artist') || '').trim(),
      title: String(new FormData(form).get('title') || '').trim(),
    };
  }

  async function submitFind() {
    if (inFlight) return;
    const { artist, title } = getFormValues();
    if (!artist || !title) {
      setStatus('Enter both the artist and song title.', 'error');
      setState('typing');
      return;
    }
    if (!handlers.onFind) {
      setStatus('Search is still loading. Wait a moment and try again.', 'error');
      setState('idle');
      return;
    }
    inFlight = true;
    confirmedLookup = null;
    showLoading();
    try {
      const lookup = await handlers.onFind({ artist, title });
      if (!lookup) {
        presentError('Could not find your song. Try again.');
        return;
      }
      presentLookup(lookup);
    } catch (error) {
      presentError(error?.message || 'Could not find your song. Try again.');
    } finally {
      inFlight = false;
      setFormDisabled(false);
      const submit = $('.yatsn-song-search__submit');
      if (submit) submit.classList.remove('is-loading');
      panel.removeAttribute('aria-busy');
    }
  }

  function wireForm() {
    if (formWired) return;
    const form = $('#song-form');
    if (!form) return;
    formWired = true;
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      submitFind();
    });
    form?.querySelectorAll('input').forEach((input) => {
      input.addEventListener('input', () => {
        if (!inFlight) setState('typing');
      });
    });
    $('[data-song-retry]')?.addEventListener('click', () => {
      if (inFlight) return;
      submitFind();
    });
  }

  function renderResume({ label, href }) {
    const row = document.querySelector('[data-song-resume]');
    if (!row) return;
    row.hidden = false;
    row.innerHTML = `
      <a class="yatsn-resume-row__link" href="${escapeHtml(href)}">
        <span class="yatsn-resume-row__label">Continue your creation</span>
        <span class="yatsn-resume-row__detail">${escapeHtml(label)}</span>
      </a>
    `;
  }

  function renderRecent(images) {
    const row = document.querySelector('[data-song-recent]');
    if (!row || !Array.isArray(images) || !images.length) return;
    row.hidden = false;
    row.innerHTML = `
      <p class="yatsn-recent-row__heading">Recent creations</p>
      <ul class="yatsn-recent-row__list">
        ${images.slice(0, 4).map((img) => `
          <li class="yatsn-recent-row__item-wrap">
            <a class="yatsn-recent-row__item" href="/images/${escapeHtml(img.id)}">
              <span class="yatsn-recent-row__art">
                <img src="${escapeHtml(img.thumbnailUrl)}" alt="" decoding="async" loading="lazy">
              </span>
              <span class="yatsn-recent-row__copy">
                <strong>${escapeHtml(img.title)}</strong>
                <span>${escapeHtml(img.artist)}</span>
              </span>
            </a>
          </li>
        `).join('')}
      </ul>
    `;
  }

  wireForm();

  window.YatsnSongSearch = {
    init(nextHandlers = {}) {
      handlers = nextHandlers;
      setState('idle');
      setStatus('', 'info', { hidden: true });
    },
    isInFlight() {
      return inFlight;
    },
    isConfirmed() {
      return !!confirmedLookup;
    },
    getConfirmed() {
      return confirmedLookup;
    },
    presentLookup,
    presentError,
    restoreConfirmed,
    renderResume,
    renderRecent,
    setState,
    submitFind,
    resetSelection() {
      confirmedLookup = null;
      pendingLookup = null;
      const selected = $('[data-song-selected]');
      const results = $('[data-song-results]');
      const retry = $('[data-song-retry-wrap]');
      if (selected) {
        selected.hidden = true;
        selected.innerHTML = '';
      }
      if (results) {
        results.hidden = true;
        results.innerHTML = '';
      }
      if (retry) retry.hidden = true;
      hideLoading();
      setFormDisabled(false);
      setStatus('', 'info', { hidden: true });
      setState('idle');
    },
  };

  if (privateBuildAllowsFixtures()) {
    const FIXTURE_LOOKUP = {
      id: 'fixture-song-lookup',
      artist: 'Owner Test Band',
      title: 'Midnight Harbor',
      state: 'found',
      classification: 'themes_and_feeling',
    };
    const FIXTURE_FALLBACK = {
      id: 'fixture-song-fallback',
      artist: 'Owner Test Band',
      title: 'Paper Lanterns',
      state: 'fallbackFound',
      classification: 'inspired_by_available_information',
    };
    const FIXTURE_NOT_FOUND = {
      id: 'fixture-song-miss',
      artist: 'Unknown Artist',
      title: 'Missing Track',
      state: 'notFound',
      classification: 'not_found',
    };

    window.YatsnSongSearchFixtures = {
      showLoading() {
        showLoading();
      },
      showResult() {
        hideLoading();
        setFormDisabled(false);
        const form = $('#song-form');
        if (form) {
          form.querySelector('[name="artist"]').value = FIXTURE_LOOKUP.artist;
          form.querySelector('[name="title"]').value = FIXTURE_LOOKUP.title;
        }
        presentLookup(FIXTURE_LOOKUP);
      },
      showNoResults() {
        hideLoading();
        setFormDisabled(false);
        presentLookup(FIXTURE_NOT_FOUND);
      },
      showError() {
        presentError('Could not find your song right now. Try again.');
      },
      showSelected() {
        this.showResult();
        selectLookup(FIXTURE_LOOKUP);
      },
      showFallback() {
        hideLoading();
        setFormDisabled(false);
        presentLookup(FIXTURE_FALLBACK);
      },
      focusSubmit() {
        $('#song-form .yatsn-song-search__submit')?.focus();
      },
      focusResult() {
        this.showResult();
        document.querySelector('[data-song-results] .yatsn-song-result')?.focus();
      },
    };
  }
})();
