(() => {
  if (!document.querySelector('[data-create]')) return;

  let latestSongDna = null;
  let latestLookupLabel = '';
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
    .ai-direction-lab{padding:1rem;border:1px solid var(--color-border);border-radius:var(--radius-lg);background:color-mix(in oklab,var(--color-surface-elevated) 82%,transparent);box-shadow:var(--shadow-1)}
    .ai-direction-lab__top{display:flex;gap:.75rem;align-items:flex-start;justify-content:space-between;flex-wrap:wrap}
    .ai-direction-lab__actions{display:flex;gap:.6rem;flex-wrap:wrap}
    .ai-direction-lab__badge{font-size:.75rem;letter-spacing:.05em;text-transform:uppercase;color:var(--color-text-tertiary)}
    .ai-direction-grid{display:grid;gap:.75rem;margin-top:.9rem}
    .ai-direction-card{padding:1rem;text-align:left;border:1px solid var(--color-border);border-radius:var(--radius-md);background:var(--color-surface);color:var(--color-text)}
    .ai-direction-card:hover,.ai-direction-card:focus-visible{border-color:var(--color-border-strong);background:var(--color-surface-elevated)}
    .ai-direction-card strong{display:block;font-size:1rem;margin-bottom:.35rem}
    .ai-direction-card span{display:block;color:var(--color-text-secondary);font-size:.9rem;line-height:1.45}
    .ai-direction-card em{display:block;color:var(--color-text-tertiary);font-size:.75rem;margin-top:.55rem;font-style:normal}
    .ai-direction-lab [disabled]{opacity:.55;cursor:not-allowed}
    @media(min-width:700px){.ai-direction-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
  `;
  document.head.appendChild(css);

  function buildPanel() {
    const controls = document.querySelector('#the-direction .direction-controls');
    if (!controls || controls.querySelector('[data-ai-direction-lab]')) return;

    const panel = document.createElement('section');
    panel.className = 'ai-direction-lab stack';
    panel.dataset.aiDirectionLab = '';
    panel.innerHTML = `
      <div class="ai-direction-lab__top">
        <div>
          <div class="ai-direction-lab__badge">First build · Song DNA</div>
          <h3>Let AI shape the direction</h3>
          <p class="quiet">Generate immediately with the strongest fit, or explore three directions created specifically for this Song DNA.</p>
        </div>
        <div class="ai-direction-lab__actions">
          <button class="btn btn--primary" type="button" data-ai-quick disabled>Generate for me</button>
          <button class="btn btn--secondary" type="button" data-ai-explore disabled>Explore options</button>
        </div>
      </div>
      <p class="status" data-ai-status role="status" aria-live="polite">Discover a song to prepare Song DNA.</p>
      <div class="ai-direction-grid" data-ai-options hidden></div>
    `;
    controls.prepend(panel);

    panel.querySelector('[data-ai-explore]').addEventListener('click', () => loadDirections(false));
    panel.querySelector('[data-ai-quick]').addEventListener('click', () => loadDirections(true));
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
    if (status && ready) status.textContent = latestLookupLabel ? `Song DNA ready for ${latestLookupLabel}.` : 'Song DNA ready.';
  }

  async function loadDirections(quickMode) {
    const panel = document.querySelector('[data-ai-direction-lab]');
    if (!panel || !latestSongDna) return;
    const status = panel.querySelector('[data-ai-status]');
    const quick = panel.querySelector('[data-ai-quick]');
    const explore = panel.querySelector('[data-ai-explore]');
    const options = panel.querySelector('[data-ai-options]');
    quick.disabled = true;
    explore.disabled = true;
    options.hidden = true;
    options.innerHTML = '';
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
        throw new Error(diagnostic ? `${message} (${diagnostic})` : message);
      }
      const directions = payload?.data?.directions || [];
      if (!directions.length) throw new Error('Gemini returned no visual directions.');

      if (quickMode) {
        status.textContent = `AI chose “${directions[0].name}”. Applying it and preparing generation…`;
        applyDirection(directions[0], true);
        return;
      }

      renderDirections(directions);
      options.hidden = false;
      status.textContent = 'Choose the direction that feels right. The first option is Gemini’s strongest fit.';
    } catch (error) {
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
      button.innerHTML = `<strong>${escapeHtml(direction.name)}</strong><span>${escapeHtml(direction.description)}</span><em>${index === 0 ? 'Recommended · ' : ''}Uses ${escapeHtml(direction.styleName)} internally</em>`;
      button.addEventListener('click', () => applyDirection(direction, false));
      grid.appendChild(button);
    });
  }

  function applyDirection(direction, autoContinue) {
    const styleButtons = Array.from(document.querySelectorAll('.style-option'));
    const styleButton = styleButtons.find((button) => button.querySelector('strong')?.textContent?.trim() === direction.styleName);
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
    if (status) {
      status.classList.remove('is-error');
      status.textContent = autoContinue
        ? `Using “${direction.name}”. Finishing the existing generation handoff…`
        : `“${direction.name}” is selected. You can fine tune below or review your creation.`;
    }

    if (autoContinue) {
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
