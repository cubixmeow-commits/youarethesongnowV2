(() => {
  if (!document.querySelector('[data-create]')) return;

  let latestSongDna = null;
  let latestLookupLabel = '';
  let selectedDirection = null;
  let exploreActive = false;
  const nativeFetch = window.fetch.bind(window);

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

  const css = document.createElement('style');
  css.textContent = `
    .ai-direction-lab{padding:1.1rem 1rem 1.15rem;border:1px solid var(--color-border);border-radius:var(--radius-lg);background:color-mix(in oklab,var(--color-surface-elevated) 82%,transparent);box-shadow:var(--shadow-1)}
    .ai-direction-lab__top{display:flex;gap:.85rem;align-items:flex-start;justify-content:space-between;flex-wrap:wrap}
    .ai-direction-lab__copy{flex:1 1 16rem;min-width:0}
    .ai-direction-lab__actions{display:flex;gap:.6rem;flex-wrap:wrap;align-items:center}
    .ai-direction-lab__badge{font-size:.72rem;letter-spacing:.06em;text-transform:uppercase;color:var(--color-text-tertiary)}
    .ai-direction-lab h3{margin:.2rem 0 .35rem;font-size:1.2rem;line-height:1.25}
    .ai-direction-lab__lead{margin:0;color:var(--color-text-secondary);font-size:.95rem;line-height:1.45;max-width:38rem}
    .ai-direction-grid{display:grid;gap:.85rem;margin-top:1rem}
    .ai-direction-card{position:relative;display:flex;flex-direction:column;gap:.55rem;width:100%;min-height:7.5rem;padding:1.1rem 1rem 1.15rem;text-align:left;border:1px solid var(--color-border);border-radius:var(--radius-md);background:var(--color-surface);color:var(--color-text);cursor:pointer;transition:border-color .15s ease,background-color .15s ease,box-shadow .15s ease;touch-action:manipulation;-webkit-tap-highlight-color:transparent}
    .ai-direction-card:hover,.ai-direction-card:focus-visible{border-color:var(--color-border-strong);background:var(--color-surface-elevated);outline:none}
    .ai-direction-card:focus-visible{box-shadow:0 0 0 3px color-mix(in oklab,var(--color-accent-sapphire) 35%,transparent)}
    .ai-direction-card.is-selected{border-color:color-mix(in oklab,var(--color-accent-sapphire) 55%,transparent);background:color-mix(in oklab,var(--color-accent-sapphire) 10%,var(--color-surface));box-shadow:0 0 0 1px color-mix(in oklab,var(--color-accent-sapphire) 35%,transparent)}
    .ai-direction-card__recommend{display:inline-flex;align-self:flex-start;padding:.18rem .5rem;border-radius:999px;font-size:.68rem;letter-spacing:.04em;text-transform:uppercase;color:var(--color-text);background:color-mix(in oklab,var(--color-accent-warm, var(--color-accent-sapphire)) 18%,transparent);border:1px solid color-mix(in oklab,var(--color-accent-warm, var(--color-accent-sapphire)) 28%,transparent)}
    .ai-direction-card strong{display:block;font-size:1.05rem;line-height:1.25;margin:0}
    .ai-direction-card span{display:block;color:var(--color-text-secondary);font-size:.92rem;line-height:1.45}
    .ai-direction-lab__continue{display:flex;flex-wrap:wrap;gap:.65rem;align-items:center;margin-top:1rem}
    .ai-direction-lab__manual{appearance:none;border:0;background:transparent;color:var(--color-text-secondary);font:inherit;font-size:.9rem;text-decoration:underline;text-underline-offset:.18em;padding:.35rem .15rem;cursor:pointer;min-height:44px}
    .ai-direction-lab__manual:hover,.ai-direction-lab__manual:focus-visible{color:var(--color-text)}
    .ai-direction-lab [disabled]{opacity:.55;cursor:not-allowed}
    .direction-controls.is-ai-direction-active [data-style-world]{display:none}
    .direction-controls.is-ai-direction-active [data-review]{display:none}
    @media(min-width:700px){.ai-direction-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media(max-width:699px){
      .ai-direction-lab__actions{width:100%}
      .ai-direction-lab__actions .btn{flex:1 1 auto;min-height:48px}
      .ai-direction-card{min-height:8rem;padding:1.15rem 1.05rem}
      .ai-direction-lab__continue .btn{width:100%;min-height:48px}
    }
  `;
  document.head.appendChild(css);

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

  function setExploreChrome(active) {
    exploreActive = active;
    const controls = document.querySelector('#the-direction .direction-controls');
    if (!controls) return;
    ensureStyleWorldMarker();
    controls.classList.toggle('is-ai-direction-active', active);
    const continueRow = document.querySelector('[data-ai-continue]');
    if (continueRow) continueRow.hidden = !active || !selectedDirection;
  }

  function buildPanel() {
    const controls = document.querySelector('#the-direction .direction-controls');
    if (!controls || controls.querySelector('[data-ai-direction-lab]')) return;
    ensureStyleWorldMarker();

    const panel = document.createElement('section');
    panel.className = 'ai-direction-lab stack';
    panel.dataset.aiDirectionLab = '';
    panel.innerHTML = `
      <div class="ai-direction-lab__top">
        <div class="ai-direction-lab__copy">
          <div class="ai-direction-lab__badge">Song DNA<span data-ai-build class="visually-hidden"></span></div>
          <h3>Let AI shape the direction</h3>
          <p class="ai-direction-lab__lead">Generate immediately with the strongest fit, or explore three directions created specifically for this Song DNA.</p>
        </div>
        <div class="ai-direction-lab__actions">
          <button class="btn btn--primary" type="button" data-ai-quick disabled>Generate for me</button>
          <button class="btn btn--secondary" type="button" data-ai-explore disabled>Explore options</button>
        </div>
      </div>
      <p class="status" data-ai-status role="status" aria-live="polite">Discover a song to prepare Song DNA.</p>
      <div class="ai-direction-grid" data-ai-options hidden role="listbox" aria-label="AI visual directions"></div>
      <div class="ai-direction-lab__continue" data-ai-continue hidden>
        <button class="btn btn--primary" type="button" data-ai-create-direction>Create this direction</button>
        <button class="ai-direction-lab__manual" type="button" data-ai-manual>Choose a style manually</button>
      </div>
    `;
    controls.prepend(panel);

    panel.querySelector('[data-ai-explore]').addEventListener('click', () => loadDirections(false));
    panel.querySelector('[data-ai-quick]').addEventListener('click', () => loadDirections(true));
    panel.querySelector('[data-ai-create-direction]').addEventListener('click', () => {
      if (!selectedDirection) return;
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
    const quick = panel.querySelector('[data-ai-quick]');
    const explore = panel.querySelector('[data-ai-explore]');
    if (quick) quick.disabled = !ready;
    if (explore) explore.disabled = !ready;
    const status = panel.querySelector('[data-ai-status]');
    if (status && ready && !status.classList.contains('is-error') && !exploreActive) {
      status.textContent = latestLookupLabel ? `Song DNA ready for ${latestLookupLabel}.` : 'Song DNA ready.';
    }
  }

  async function loadDirections(quickMode) {
    const panel = document.querySelector('[data-ai-direction-lab]');
    if (!panel || !latestSongDna) return;
    const status = panel.querySelector('[data-ai-status]');
    const quick = panel.querySelector('[data-ai-quick]');
    const explore = panel.querySelector('[data-ai-explore]');
    const options = panel.querySelector('[data-ai-options]');
    const continueRow = panel.querySelector('[data-ai-continue]');
    selectedDirection = null;
    quick.disabled = true;
    explore.disabled = true;
    options.hidden = true;
    options.innerHTML = '';
    if (continueRow) continueRow.hidden = true;
    status.classList.remove('is-error');
    status.textContent = quickMode ? 'Finding the strongest visual fit…' : 'Creating three visual directions from this Song DNA…';

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
      if (!directions.length) throw new Error('Gemini returned no visual directions.');

      if (quickMode) {
        status.textContent = `Using “${directions[0].name}”. Preparing your creation…`;
        applyDirection(directions[0], { autoContinue: true, announce: false });
        return;
      }

      renderDirections(directions);
      options.hidden = false;
      setExploreChrome(true);
      status.textContent = 'Choose the direction that feels right.';
    } catch (error) {
      setExploreChrome(false);
      status.textContent = error.message || 'Could not create visual directions.';
      status.classList.add('is-error');
    } finally {
      quick.disabled = !latestSongDna;
      explore.disabled = !latestSongDna;
    }
  }

  function renderDirections(directions) {
    const grid = document.querySelector('[data-ai-options]');
    grid.innerHTML = '';
    directions.forEach((direction, index) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'ai-direction-card';
      button.setAttribute('role', 'option');
      button.setAttribute('aria-selected', 'false');
      button.dataset.aiDirectionIndex = String(index);
      // Keep StyleMap bridge out of customer copy; retain for private debugging only.
      if (direction.styleName) button.dataset.styleName = direction.styleName;
      if (direction.styleId) button.dataset.styleId = direction.styleId;
      button.innerHTML = `${index === 0 ? '<span class="ai-direction-card__recommend">Recommended</span>' : ''}<strong>${escapeHtml(direction.name)}</strong><span>${escapeHtml(direction.description)}</span>`;
      button.addEventListener('click', () => selectDirection(direction, button));
      button.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          selectDirection(direction, button);
        }
      });
      grid.appendChild(button);
    });
  }

  function selectDirection(direction, button) {
    selectedDirection = direction;
    document.querySelectorAll('[data-ai-options] .ai-direction-card').forEach((card) => {
      const selected = card === button;
      card.classList.toggle('is-selected', selected);
      card.setAttribute('aria-selected', selected ? 'true' : 'false');
    });
    applyDirection(direction, { autoContinue: false, announce: true });
    const continueRow = document.querySelector('[data-ai-continue]');
    if (continueRow) continueRow.hidden = false;
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
      status.classList.remove('is-error');
      status.textContent = `“${direction.name}” is selected.`;
    }

    if (autoContinue) {
      continueWithDirection(direction);
    }
  }

  function continueWithDirection(direction) {
    const status = document.querySelector('[data-ai-status]');
    if (status) {
      status.classList.remove('is-error');
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
    const options = document.querySelector('[data-ai-options]');
    if (options) {
      options.querySelectorAll('.ai-direction-card').forEach((card) => {
        card.classList.remove('is-selected');
        card.setAttribute('aria-selected', 'false');
      });
    }
    const continueRow = document.querySelector('[data-ai-continue]');
    if (continueRow) continueRow.hidden = true;
    const status = document.querySelector('[data-ai-status]');
    if (status) {
      status.classList.remove('is-error');
      status.textContent = 'Manual style selection is available below.';
    }
    document.querySelector('[data-style-grid] .style-option')?.focus?.();
  }

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>'"]/g, (char) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
    })[char]);
  }

  // This script is deliberately loaded before app.js so it can observe song lookup responses.
  // UI injection can happen immediately because defer scripts run after parsing.
  buildPanel();
  const observer = new MutationObserver(() => buildPanel());
  observer.observe(document.body, { childList: true, subtree: true });
})();
