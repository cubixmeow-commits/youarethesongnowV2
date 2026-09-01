(() => {
  if (!document.querySelector('[data-create]')) return;

  let latestSongDna = null;
  let latestLookupLabel = '';
  let selectedDirection = null;
  let exploreActive = false;
  let exploreInFlight = false;
  let lastQuickMode = false;
  const nativeFetch = window.fetch.bind(window);

  const FIXTURE_DIRECTIONS = [
    {
      name: 'Sodium Crossing',
      description: 'Rain-slick overpass, warm lamps, two figures paused mid-step.',
      styleName: 'Gothic Romance',
      styleId: 'style-fixture-a',
    },
    {
      name: 'Quiet Threshold',
      description: 'A dim apartment doorway where the night still has one more hour.',
      styleName: 'Cinematic Realism',
      styleId: 'style-fixture-b',
    },
    {
      name: 'Harbor Afterglow',
      description: 'Wet stone, distant water, a coat catching the last sodium light.',
      styleName: 'Dark Romance',
      styleId: 'style-fixture-c',
    },
  ];

  // Capture the already-derived development Song DNA from the existing song lookup.
  // This avoids sending portraits or lyrics to the Explore call and avoids re-running lyric research.
  window.fetch = async (...args) => {
    const response = await nativeFetch(...args);
    try {
      const url = typeof args[0] === 'string' ? args[0] : args[0]?.url || '';
      const method = String(args[1]?.method || 'GET').toUpperCase();
      if (method === 'POST' && url.includes('/api/v1/song-lookups') && response.ok) {
        const payload = await response.clone().json();
        const lookup = payload?.data;
        const research = lookup?.developmentAnalysis;
        if (research?.analyzed && research?.analysis) {
          latestSongDna = research.analysis;
          latestLookupLabel = `${lookup.title || ''}${lookup.artist ? ` · ${lookup.artist}` : ''}`;
          updateAvailability();
        } else {
          latestSongDna = null;
          latestLookupLabel = '';
          updateAvailability();
        }
      }
    } catch (_) {
      // Explore is additive; never interfere with the existing Create request.
    }
    return response;
  };

  function styleWorldBlock() {
    const grid = document.querySelector('[data-style-grid]');
    if (!grid) return null;
    return grid.closest('div');
  }

  function ensureStyleWorldMarker() {
    const block = styleWorldBlock();
    if (block && !block.hasAttribute('data-style-world')) {
      block.setAttribute('data-style-world', '');
    }
  }

  function setExploreState(state) {
    const panel = document.querySelector('[data-ai-direction-lab]');
    if (panel) panel.dataset.yatsnExploreState = state;
  }

  function setExploreChrome(active) {
    exploreActive = active;
    const controls = document.querySelector('#the-direction .direction-controls');
    if (!controls) return;
    ensureStyleWorldMarker();
    controls.classList.toggle('is-ai-direction-active', active);
    const continueRow = document.querySelector('[data-ai-continue]');
    if (continueRow) continueRow.hidden = !active || !selectedDirection;
  }

  function setExploreCopy(mode) {
    const heading = document.querySelector('[data-ai-heading]');
    const lead = document.querySelector('[data-ai-lead]');
    if (!heading || !lead) return;
    if (mode === 'explore') {
      heading.textContent = 'Explore directions';
      lead.textContent = 'Choose the world that feels right for this Song DNA.';
    } else {
      heading.textContent = 'Let AI shape the direction';
      lead.textContent = 'Generate immediately with the strongest fit, or explore three directions created specifically for this Song DNA.';
    }
  }

  function setBusy(busy) {
    const panel = document.querySelector('[data-ai-direction-lab]');
    const options = document.querySelector('[data-ai-options]');
    const quick = document.querySelector('[data-ai-quick]');
    const explore = document.querySelector('[data-ai-explore]');
    exploreInFlight = busy;
    if (panel) panel.setAttribute('aria-busy', busy ? 'true' : 'false');
    if (options) options.setAttribute('aria-busy', busy ? 'true' : 'false');
    if (quick) {
      quick.disabled = busy || !latestSongDna;
      quick.classList.toggle('is-loading', busy && lastQuickMode);
      if (busy && lastQuickMode) quick.setAttribute('aria-busy', 'true');
      else quick.removeAttribute('aria-busy');
    }
    if (explore) {
      explore.disabled = busy || !latestSongDna;
      explore.classList.toggle('is-loading', busy && !lastQuickMode);
      if (busy && !lastQuickMode) explore.setAttribute('aria-busy', 'true');
      else explore.removeAttribute('aria-busy');
    }
  }

  function buildPanel() {
    const controls = document.querySelector('#the-direction .direction-controls');
    if (!controls || controls.querySelector('[data-ai-direction-lab]')) return;
    ensureStyleWorldMarker();

    const panel = document.createElement('section');
    panel.className = 'ai-direction-lab stack';
    panel.dataset.aiDirectionLab = '';
    panel.dataset.yatsnExploreState = 'idle';
    panel.innerHTML = `
      <div class="ai-direction-lab__top">
        <div class="ai-direction-lab__copy">
          <div class="ai-direction-lab__badge">Song DNA<span data-ai-build class="visually-hidden"></span></div>
          <h3 data-ai-heading>Let AI shape the direction</h3>
          <p class="ai-direction-lab__lead" data-ai-lead>Generate immediately with the strongest fit, or explore three directions created specifically for this Song DNA.</p>
        </div>
        <div class="ai-direction-lab__actions">
          <button class="btn btn--primary yatsn-btn yatsn-btn--primary" type="button" data-ai-quick disabled>Generate for me</button>
          <button class="btn btn--secondary yatsn-btn yatsn-btn--secondary" type="button" data-ai-explore disabled>Explore options</button>
        </div>
      </div>
      <div class="yatsn-status yatsn-status--info" data-ai-status role="status" aria-live="polite">Discover a song to prepare Song DNA.</div>
      <div class="ai-direction-grid" data-ai-options hidden role="radiogroup" aria-label="AI visual directions"></div>
      <div class="ai-direction-lab__continue" data-ai-continue hidden>
        <button class="btn btn--primary yatsn-btn yatsn-btn--primary" type="button" data-ai-create-direction>Create this direction</button>
        <button class="ai-direction-lab__manual yatsn-btn yatsn-btn--quiet" type="button" data-ai-manual>Choose a style manually</button>
      </div>
      <div class="yatsn-status__actions" data-ai-retry-wrap hidden>
        <button class="btn btn--secondary yatsn-btn yatsn-btn--secondary" type="button" data-ai-retry>Try again</button>
      </div>
    `;
    controls.prepend(panel);

    panel.querySelector('[data-ai-explore]').addEventListener('click', () => loadDirections(false));
    panel.querySelector('[data-ai-quick]').addEventListener('click', () => loadDirections(true));
    panel.querySelector('[data-ai-retry]').addEventListener('click', () => loadDirections(lastQuickMode));
    panel.querySelector('[data-ai-create-direction]').addEventListener('click', () => {
      if (!selectedDirection || exploreInFlight) return;
      continueWithDirection(selectedDirection);
    });
    panel.querySelector('[data-ai-manual]').addEventListener('click', restoreManualDirection);

    const buildCommit = document.querySelector('[data-create]')?.dataset.buildCommit;
    const buildLabel = panel.querySelector('[data-ai-build]');
    if (buildLabel && buildCommit) {
      // Keep deploy identity available to AT/private debugging without dominating the customer UI.
      buildLabel.textContent = ` build ${buildCommit}`;
    }
    updateAvailability();
  }

  function updateAvailability() {
    const panel = document.querySelector('[data-ai-direction-lab]');
    if (!panel) return;
    const ready = !!latestSongDna;
    if (!exploreInFlight) {
      const quick = panel.querySelector('[data-ai-quick]');
      const explore = panel.querySelector('[data-ai-explore]');
      if (quick) quick.disabled = !ready;
      if (explore) explore.disabled = !ready;
    }
    const status = panel.querySelector('[data-ai-status]');
    if (status && ready && !status.classList.contains('is-error') && !exploreActive) {
      status.textContent = latestLookupLabel ? `Song DNA ready for ${latestLookupLabel}.` : 'Song DNA ready.';
      setExploreState('ready');
    }
  }

  function renderLoading() {
    const options = document.querySelector('[data-ai-options]');
    if (!options) return;
    options.hidden = false;
    options.innerHTML = '';
    for (let i = 0; i < 3; i += 1) {
      const placeholder = document.createElement('div');
      placeholder.className = 'yatsn-direction-card ai-direction-card is-loading';
      placeholder.setAttribute('aria-hidden', 'true');
      placeholder.innerHTML = '<span class="yatsn-skeleton"></span><span class="yatsn-skeleton yatsn-skeleton--line"></span>';
      options.appendChild(placeholder);
    }
    setExploreState('loading');
  }

  async function loadDirections(quickMode) {
    const panel = document.querySelector('[data-ai-direction-lab]');
    if (!panel || !latestSongDna || exploreInFlight) return;
    lastQuickMode = quickMode;
    const status = panel.querySelector('[data-ai-status]');
    const options = panel.querySelector('[data-ai-options]');
    const continueRow = panel.querySelector('[data-ai-continue]');
    const retryWrap = panel.querySelector('[data-ai-retry-wrap]');
    selectedDirection = null;
    if (continueRow) continueRow.hidden = true;
    if (retryWrap) retryWrap.hidden = true;
    status.classList.remove('is-error');
    status.classList.remove('yatsn-status--error');
    status.classList.add('yatsn-status--info');
    status.removeAttribute('aria-describedby');
    status.textContent = quickMode ? 'Finding the strongest visual fit…' : 'Creating three visual directions from this Song DNA…';
    setExploreCopy(quickMode ? 'default' : 'explore');
    renderLoading();
    setBusy(true);

    try {
      const root = document.querySelector('[data-create]');
      const response = await nativeFetch('/api/v1/explore-directions', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': root?.dataset.csrf || '',
        },
        body: JSON.stringify({ songDna: latestSongDna }),
      });
      const payload = await response.json();
      if (!response.ok) {
        const message = payload?.error?.message || 'Could not create directions.';
        const diagnostic = payload?.error?.fields?.diagnostic;
        const build = payload?.error?.fields?.build;
        const suffix = [diagnostic, build ? `build ${build}` : ''].filter(Boolean).join(' · ');
        throw new Error(suffix ? `${message} (${suffix})` : message);
      }
      const directions = payload?.data?.directions || [];
      if (!directions.length) throw new Error('No visual directions were returned.');

      if (quickMode) {
        options.hidden = true;
        options.innerHTML = '';
        status.textContent = `Using “${directions[0].name}”. Preparing your creation…`;
        applyDirection(directions[0], { autoContinue: true, announce: false });
        setExploreState('selected');
        return;
      }

      renderDirections(directions);
      options.hidden = false;
      setExploreChrome(true);
      status.textContent = 'Choose the direction that feels right.';
      setExploreState('ready');
    } catch (error) {
      setExploreChrome(false);
      options.innerHTML = '';
      options.hidden = true;
      status.textContent = error.message || 'Could not create visual directions.';
      status.classList.add('is-error');
      status.classList.add('yatsn-status--error');
      status.classList.remove('yatsn-status--info');
      if (retryWrap) retryWrap.hidden = false;
      setExploreState('error');
    } finally {
      setBusy(false);
    }
  }

  function renderDirections(directions) {
    const grid = document.querySelector('[data-ai-options]');
    if (!grid) return;
    grid.innerHTML = '';
    grid.hidden = false;
    directions.forEach((direction, index) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'yatsn-direction-card ai-direction-card';
      button.setAttribute('role', 'radio');
      button.setAttribute('aria-checked', 'false');
      button.setAttribute('aria-selected', 'false');
      button.dataset.aiDirectionIndex = String(index);
      // Keep StyleMap bridge out of customer copy; retain for private debugging only.
      if (direction.styleName) button.dataset.styleName = direction.styleName;
      if (direction.styleId) button.dataset.styleId = direction.styleId;
      const recommend = index === 0
        ? '<span class="yatsn-recommend ai-direction-card__recommend">Recommended</span>'
        : '';
      button.innerHTML = `${recommend}<strong>${escapeHtml(direction.name)}</strong><span>${escapeHtml(direction.description)}</span><span class="yatsn-selected-mark" aria-hidden="true"></span>`;
      button.addEventListener('click', () => selectDirection(direction, button));
      button.addEventListener('keydown', (event) => handleDirectionKey(event, button));
      grid.appendChild(button);
    });
  }

  function handleDirectionKey(event, button) {
    const cards = Array.from(document.querySelectorAll('[data-ai-options] .ai-direction-card:not(.is-loading)'));
    const index = cards.indexOf(button);
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      button.click();
      return;
    }
    const map = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 };
    if (!(event.key in map) || index < 0) return;
    event.preventDefault();
    const next = cards[(index + map[event.key] + cards.length) % cards.length];
    next?.focus();
  }

  function selectDirection(direction, button) {
    selectedDirection = direction;
    document.querySelectorAll('[data-ai-options] .ai-direction-card').forEach((card) => {
      const selected = card === button;
      card.classList.toggle('is-selected', selected);
      card.setAttribute('aria-selected', selected ? 'true' : 'false');
      card.setAttribute('aria-checked', selected ? 'true' : 'false');
    });
    applyDirection(direction, { autoContinue: false, announce: true });
    const continueRow = document.querySelector('[data-ai-continue]');
    if (continueRow) continueRow.hidden = false;
    setExploreState('selected');
    document.querySelector('[data-ai-create-direction]')?.focus();
  }

  function applyDirection(direction, { autoContinue, announce }) {
    setExploreChrome(true);

    const styleButtons = Array.from(document.querySelectorAll('.style-option'));
    const styleButton = styleButtons.find((candidate) => candidate.querySelector('strong')?.textContent?.trim() === direction.styleName);
    if (styleButton) styleButton.click();

    const specialToggle = document.querySelector('[data-special-toggle]');
    const special = document.querySelector('[data-special]');
    if (direction.promptHint && specialToggle && special) {
      if (!specialToggle.checked) specialToggle.click();
      special.value = direction.promptHint.slice(0, 500);
      special.dispatchEvent(new Event('input', { bubbles: true }));
      special.dispatchEvent(new Event('change', { bubbles: true }));
    }

    const status = document.querySelector('[data-ai-status]');
    if (status && announce) {
      status.classList.remove('is-error', 'yatsn-status--error');
      status.classList.add('yatsn-status--info');
      status.textContent = `“${direction.name}” is selected.`;
    }

    if (autoContinue) {
      continueWithDirection(direction);
    }
  }

  function continueWithDirection(direction) {
    if (exploreInFlight) return;
    const status = document.querySelector('[data-ai-status]');
    if (status) {
      status.classList.remove('is-error', 'yatsn-status--error');
      status.textContent = `Creating “${direction.name}”…`;
    }
    // Compatibility bridge for the current Build 1 flow. Style selection updates local state immediately;
    // allow its draft patch to settle, then use the existing review/generation pipeline.
    window.setTimeout(() => {
      document.querySelector('[data-review]')?.click();
      window.setTimeout(() => {
        const create = document.querySelector('[data-create-image]');
        if (create && !create.closest('[hidden]')) create.click();
      }, 900);
    }, 700);
  }

  function restoreManualDirection() {
    selectedDirection = null;
    setExploreChrome(false);
    setExploreCopy('default');
    const options = document.querySelector('[data-ai-options]');
    if (options) {
      options.querySelectorAll('.ai-direction-card').forEach((card) => {
        card.classList.remove('is-selected');
        card.setAttribute('aria-selected', 'false');
        card.setAttribute('aria-checked', 'false');
      });
    }
    const continueRow = document.querySelector('[data-ai-continue]');
    if (continueRow) continueRow.hidden = true;
    const status = document.querySelector('[data-ai-status]');
    if (status) {
      status.classList.remove('is-error', 'yatsn-status--error');
      status.classList.add('yatsn-status--info');
      status.textContent = 'Manual style selection is available below.';
    }
    setExploreState('manual');
    document.querySelector('[data-style-grid] .style-option')?.focus?.();
  }

  function showError(message) {
    const status = document.querySelector('[data-ai-status]');
    const options = document.querySelector('[data-ai-options]');
    const retryWrap = document.querySelector('[data-ai-retry-wrap]');
    const continueRow = document.querySelector('[data-ai-continue]');
    if (options) {
      options.innerHTML = '';
      options.hidden = true;
    }
    if (continueRow) continueRow.hidden = true;
    if (retryWrap) retryWrap.hidden = false;
    if (status) {
      status.textContent = message || 'Could not create visual directions.';
      status.classList.add('is-error', 'yatsn-status--error');
      status.classList.remove('yatsn-status--info');
    }
    setExploreChrome(false);
    setExploreState('error');
  }

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>'"]/g, (char) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
    })[char]);
  }

  window.YatsnExploreFixtures = {
    showLoading() {
      buildPanel();
      const direction = document.querySelector('#the-direction');
      if (direction) direction.hidden = false;
      lastQuickMode = false;
      setExploreCopy('explore');
      renderLoading();
      const status = document.querySelector('[data-ai-status]');
      if (status) {
        status.classList.remove('is-error', 'yatsn-status--error');
        status.textContent = 'Creating three visual directions from this Song DNA…';
      }
    },
    showReady() {
      buildPanel();
      const direction = document.querySelector('#the-direction');
      if (direction) direction.hidden = false;
      selectedDirection = null;
      setExploreCopy('explore');
      renderDirections(FIXTURE_DIRECTIONS);
      setExploreChrome(true);
      const status = document.querySelector('[data-ai-status]');
      if (status) {
        status.classList.remove('is-error', 'yatsn-status--error');
        status.classList.add('yatsn-status--info');
        status.textContent = 'Choose the direction that feels right.';
      }
      const continueRow = document.querySelector('[data-ai-continue]');
      if (continueRow) continueRow.hidden = true;
      const retryWrap = document.querySelector('[data-ai-retry-wrap]');
      if (retryWrap) retryWrap.hidden = true;
      setExploreState('ready');
    },
    showSelected() {
      this.showReady();
      const first = document.querySelector('[data-ai-options] .ai-direction-card');
      if (first) selectDirection(FIXTURE_DIRECTIONS[0], first);
    },
    showError() {
      buildPanel();
      const direction = document.querySelector('#the-direction');
      if (direction) direction.hidden = false;
      showError('Could not create visual directions. (explore_unavailable)');
    },
    showManual() {
      this.showSelected();
      restoreManualDirection();
      const direction = document.querySelector('#the-direction');
      if (direction) direction.hidden = false;
    },
    focusFirstCard() {
      this.showReady();
      document.querySelector('[data-ai-options] .ai-direction-card')?.focus();
    },
  };

  // This script is deliberately loaded before app.js so it can observe song lookup responses.
  // UI injection can happen immediately because defer scripts run after parsing.
  buildPanel();
  const observer = new MutationObserver(() => buildPanel());
  observer.observe(document.body, { childList: true, subtree: true });
})();
