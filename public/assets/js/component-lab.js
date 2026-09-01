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

  root.querySelectorAll('[data-lab-direction]').forEach((card) => {
    card.addEventListener('click', () => {
      root.querySelectorAll('[data-lab-direction]').forEach((other) => {
        const selected = other === card;
        other.classList.toggle('is-selected', selected);
        other.setAttribute('aria-checked', selected ? 'true' : 'false');
        other.setAttribute('aria-selected', selected ? 'true' : 'false');
      });
    });
  });
})();
