(() => {
  const root = document.querySelector('[data-component-lab]');
  if (!root) return;

  let lastFocus = null;

  function openDialog(selector) {
    const dialog = root.querySelector(selector);
    if (!dialog) return;
    lastFocus = document.activeElement;
    if (typeof dialog.showModal === 'function') {
      dialog.showModal();
    } else {
      dialog.setAttribute('open', 'open');
    }
  }

  function bindRestore(selector) {
    const dialog = root.querySelector(selector);
    if (!dialog) return;
    dialog.addEventListener('close', () => {
      if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
    });
  }

  root.querySelector('[data-lab-open-sheet]')?.addEventListener('click', () => openDialog('[data-lab-sheet]'));
  root.querySelector('[data-lab-open-dialog]')?.addEventListener('click', () => openDialog('[data-lab-dialog]'));
  root.querySelector('[data-lab-open-confirm]')?.addEventListener('click', () => openDialog('[data-lab-confirm]'));
  bindRestore('[data-lab-sheet]');
  bindRestore('[data-lab-dialog]');
  bindRestore('[data-lab-confirm]');

  root.querySelectorAll('[data-lab-dna]').forEach((card) => {
    card.addEventListener('click', () => {
      const selected = card.getAttribute('aria-checked') === 'true';
      card.setAttribute('aria-checked', selected ? 'false' : 'true');
      card.classList.toggle('is-selected', !selected);
    });
  });

  function labDirectionCards() {
    return Array.from(root.querySelectorAll('[data-lab-direction]'));
  }

  function selectLabDirection(card, options = {}) {
    labDirectionCards().forEach((other) => {
      const selected = other === card;
      other.classList.toggle('is-selected', selected);
      other.setAttribute('aria-checked', selected ? 'true' : 'false');
      other.tabIndex = selected ? 0 : -1;
    });
    if (options.moveFocus) card.focus();
  }

  labDirectionCards().forEach((card) => {
    card.addEventListener('click', () => selectLabDirection(card));
    card.addEventListener('keydown', (event) => {
      const cards = labDirectionCards();
      const index = cards.indexOf(card);
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        selectLabDirection(card);
        return;
      }
      const map = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 };
      if (!(event.key in map) || index < 0) return;
      event.preventDefault();
      const next = cards[(index + map[event.key] + cards.length) % cards.length];
      if (next) selectLabDirection(next, { moveFocus: true });
    });
  });
})();
