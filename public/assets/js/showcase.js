(() => {
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function $(sel, root = document) {
    return root.querySelector(sel);
  }

  async function loadManifest() {
    const res = await fetch('/assets/data/v1-showcase.json', { credentials: 'same-origin' });
    if (!res.ok) {
      throw new Error('Failed to load showcase manifest');
    }
    return res.json();
  }

  function columnCount() {
    const w = window.innerWidth;
    if (w < 390) return 1;
    if (w < 768) return 2;
    if (w < 1100) return 3;
    return 4;
  }

  function gutterSize() {
    const w = window.innerWidth;
    if (w < 390) return 10;
    if (w < 768) return 11;
    if (w < 1100) return 15;
    return 19;
  }

  function initHomeCarousel(root, manifest) {
    const track = $('[data-carousel-track]', root);
    const prevBtn = $('[data-carousel-prev]', root);
    const nextBtn = $('[data-carousel-next]', root);
    const counter = $('[data-carousel-counter]', root);
    const status = $('[data-carousel-status]', root);
    const items = manifest.items;
    const INITIAL = 9;
    const BATCH = 10;
    let rendered = 0;
    let appending = false;

    function createTile(item, index) {
      const article = document.createElement('article');
      article.className = 'world-carousel__item';
      article.setAttribute('role', 'listitem');
      article.dataset.orientation = item.orientation;

      const figure = document.createElement('figure');
      figure.className = 'world-carousel__figure';

      const link = document.createElement('a');
      link.className = 'world-carousel__link';
      link.href = '/showcase';
      link.setAttribute('aria-label', item.alt);

      const img = document.createElement('img');
      img.src = item.thumb;
      img.width = item.width;
      img.height = item.height;
      img.alt = item.alt;
      img.decoding = 'async';
      img.loading = index === 0 ? 'eager' : 'lazy';
      img.style.aspectRatio = `${item.width} / ${item.height}`;

      link.appendChild(img);
      figure.appendChild(link);
      article.appendChild(figure);
      return article;
    }

    function appendBatch(count) {
      if (appending || rendered >= items.length) return;
      appending = true;
      const slice = items.slice(rendered, rendered + count);
      const frag = document.createDocumentFragment();
      slice.forEach((item, offset) => {
        frag.appendChild(createTile(item, rendered + offset));
      });
      track.appendChild(frag);
      rendered += slice.length;
      appending = false;
      if (status) {
        status.textContent = `${rendered} of ${items.length} worlds available in carousel`;
      }
      updateCounter();
    }

    function cards() {
      return Array.from(track.querySelectorAll('.world-carousel__item'));
    }

    function updateCounter() {
      if (!counter) return;
      const list = cards();
      if (!list.length) return;
      const trackRect = track.getBoundingClientRect();
      let closest = 0;
      let minDist = Infinity;
      list.forEach((card, idx) => {
        const rect = card.getBoundingClientRect();
        const dist = Math.abs(rect.left - trackRect.left);
        if (dist < minDist) {
          minDist = dist;
          closest = idx;
        }
      });
      counter.textContent = `${closest + 1} / ${items.length}`;
    }

    function scrollByCard(direction) {
      const list = cards();
      if (!list.length) return;
      const trackRect = track.getBoundingClientRect();
      let current = 0;
      let minDist = Infinity;
      list.forEach((card, idx) => {
        const rect = card.getBoundingClientRect();
        const dist = Math.abs(rect.left - trackRect.left);
        if (dist < minDist) {
          minDist = dist;
          current = idx;
        }
      });
      const target = list[Math.min(list.length - 1, Math.max(0, current + direction))];
      if (!target) return;
      const behavior = reducedMotion ? 'auto' : 'smooth';
      track.scrollTo({ left: target.offsetLeft - track.offsetLeft, behavior });
    }

    function maybeAppendMore() {
      if (rendered >= items.length) return;
      const nearEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - track.clientWidth * 0.45;
      if (nearEnd) {
        appendBatch(Math.min(BATCH, items.length - rendered));
      }
    }

    appendBatch(INITIAL);
    track.addEventListener('scroll', () => {
      updateCounter();
      maybeAppendMore();
    }, { passive: true });

    prevBtn?.addEventListener('click', () => scrollByCard(-1));
    nextBtn?.addEventListener('click', () => scrollByCard(1));

    track.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowRight') {
        event.preventDefault();
        scrollByCard(1);
      } else if (event.key === 'ArrowLeft') {
        event.preventDefault();
        scrollByCard(-1);
      }
    });
  }

  function initShowcasePage(root, manifest) {
    const grid = $('[data-showcase-grid]', root);
    const sizer = $('.showcase-grid__sizer', grid);
    const loadMoreBtn = $('[data-showcase-load-more]', root);
    const status = $('[data-showcase-status]', root);
    const dialog = $('[data-showcase-dialog]');
    const dialogImage = $('[data-dialog-image]', dialog);
    const dialogCaption = $('[data-dialog-caption]', dialog);
    const dialogLabel = $('[data-dialog-label]', dialog);
    const filterButtons = Array.from(root.querySelectorAll('[data-filter]'));

    const INITIAL = 18;
    const BATCH = 12;
    let filter = 'all';
    let renderedCount = 0;
    let observer = null;
    let masonry = null;
    let lastTrigger = null;
    let dialogIndex = -1;

    function filteredItems() {
      if (filter === 'all') return manifest.items;
      return manifest.items.filter((item) => item.orientation === filter);
    }

    function visibleItems() {
      return filteredItems().slice(0, renderedCount);
    }

    function updateStatus() {
      const total = filteredItems().length;
      const shown = Math.min(renderedCount, total);
      if (status) {
        status.textContent = `Showing ${shown} of ${total} worlds`;
      }
    }

    function applyGridColumns() {
      const cols = columnCount();
      const gutter = gutterSize();
      const width = `calc((100% - ${(cols - 1) * gutter}px) / ${cols})`;
      sizer.style.width = width;
      grid.style.setProperty('--showcase-gutter', `${gutter}px`);
      grid.querySelectorAll('.showcase-tile').forEach((tile) => {
        tile.style.width = width;
      });
    }

    function createTile(item, index) {
      const tile = document.createElement('article');
      tile.className = 'showcase-tile';
      tile.dataset.id = item.id;
      tile.dataset.index = String(index);

      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'showcase-tile__button';
      button.setAttribute('aria-label', `Open sample ${index + 1} of ${filteredItems().length}: ${item.alt}`);

      const img = document.createElement('img');
      img.src = item.thumb;
      img.width = item.width;
      img.height = item.height;
      img.alt = '';
      img.decoding = 'async';
      img.loading = 'lazy';
      img.style.aspectRatio = `${item.width} / ${item.height}`;

      button.appendChild(img);
      button.addEventListener('click', () => openDialog(index, button));
      tile.appendChild(button);
      return tile;
    }

    function animateBatch(nodes) {
      if (reducedMotion) return;
      nodes.forEach((node, i) => {
        node.style.opacity = '0';
        node.style.transform = 'translateY(16px)';
        const delay = Math.min(i * 35, 280);
        requestAnimationFrame(() => {
          node.style.transition = `opacity 360ms var(--ease-standard) ${delay}ms, transform 360ms var(--ease-standard) ${delay}ms`;
          node.style.opacity = '1';
          node.style.transform = 'translateY(0)';
        });
      });
    }

    function layoutAfterImages(nodes) {
      applyGridColumns();
      if (!window.imagesLoaded || !window.Masonry) return;
      window.imagesLoaded(nodes, () => {
        if (!masonry) {
          masonry = new window.Masonry(grid, {
            itemSelector: '.showcase-tile',
            columnWidth: '.showcase-grid__sizer',
            percentPosition: true,
            horizontalOrder: true,
            gutter: gutterSize(),
          });
        } else {
          masonry.reloadItems();
          masonry.layout();
        }
      });
    }

    function appendBatch(count) {
      const pool = filteredItems();
      if (renderedCount >= pool.length) {
        disableLoadMore();
        return;
      }
      const slice = pool.slice(renderedCount, renderedCount + count);
      const nodes = slice.map((item, offset) => createTile(item, renderedCount + offset));
      const frag = document.createDocumentFragment();
      nodes.forEach((node) => frag.appendChild(node));
      grid.appendChild(frag);
      renderedCount += slice.length;
      animateBatch(nodes);
      layoutAfterImages(nodes);
      updateStatus();
      if (renderedCount >= pool.length) {
        disableLoadMore();
      }
    }

    function disableLoadMore() {
      if (observer) {
        observer.disconnect();
        observer = null;
      }
      if (loadMoreBtn) {
        loadMoreBtn.disabled = true;
        loadMoreBtn.hidden = true;
      }
      const sentinel = $('.showcase-sentinel', root);
      sentinel?.remove();
    }

    function ensureSentinel() {
      let sentinel = $('.showcase-sentinel', root);
      if (!sentinel) {
        sentinel = document.createElement('div');
        sentinel.className = 'showcase-sentinel';
        sentinel.setAttribute('aria-hidden', 'true');
        root.querySelector('.showcase-load-more-wrap')?.before(sentinel);
      }
      return sentinel;
    }

    function setupObserver() {
      const sentinel = ensureSentinel();
      if (observer) observer.disconnect();
      observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            appendBatch(BATCH);
          }
        });
      }, { rootMargin: '240px 0px' });
      observer.observe(sentinel);
    }

    function resetAndRender() {
      grid.querySelectorAll('.showcase-tile').forEach((node) => node.remove());
      if (masonry) {
        masonry.destroy();
        masonry = null;
      }
      renderedCount = 0;
      if (loadMoreBtn) {
        loadMoreBtn.disabled = false;
        loadMoreBtn.hidden = false;
      }
      appendBatch(INITIAL);
      setupObserver();
    }

    function openDialog(index, trigger) {
      const pool = visibleItems();
      if (!pool[index]) return;
      dialogIndex = index;
      lastTrigger = trigger;
      const item = pool[index];
      dialogImage.src = item.display;
      dialogImage.alt = item.alt;
      dialogImage.width = item.width;
      dialogImage.height = item.height;
      dialogCaption.textContent = item.alt;
      dialogLabel.textContent = `Sample ${index + 1} of ${pool.length}`;
      if (!dialog.open) {
        dialog.showModal();
        $('[data-dialog-close]', dialog)?.focus();
      }
    }

    function closeDialog() {
      if (!dialog.open) return;
      dialog.close();
      dialogImage.removeAttribute('src');
      if (lastTrigger) {
        lastTrigger.focus();
      }
    }

    function stepDialog(delta) {
      const pool = visibleItems();
      const next = (dialogIndex + delta + pool.length) % pool.length;
      const trigger = grid.querySelector(`[data-index="${next}"] .showcase-tile__button`);
      openDialog(next, trigger || lastTrigger);
    }

    filterButtons.forEach((button) => {
      button.addEventListener('click', () => {
        const next = button.dataset.filter || 'all';
        if (next === filter) return;
        if (dialog?.open) {
          closeDialog();
        }
        filter = next;
        filterButtons.forEach((btn) => {
          const active = btn === button;
          btn.classList.toggle('is-active', active);
          btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        resetAndRender();
      });
    });

    loadMoreBtn?.addEventListener('click', () => appendBatch(BATCH));
    $('[data-dialog-close]', dialog)?.addEventListener('click', closeDialog);
    $('[data-dialog-prev]', dialog)?.addEventListener('click', () => stepDialog(-1));
    $('[data-dialog-next]', dialog)?.addEventListener('click', () => stepDialog(1));

    dialog?.addEventListener('cancel', (event) => {
      event.preventDefault();
      closeDialog();
    });

    dialog?.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        event.preventDefault();
        closeDialog();
      } else if (event.key === 'ArrowLeft') {
        event.preventDefault();
        stepDialog(-1);
      } else if (event.key === 'ArrowRight') {
        event.preventDefault();
        stepDialog(1);
      }
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        applyGridColumns();
        masonry?.layout();
      }, 120);
    });

    resetAndRender();
  }

  document.addEventListener('DOMContentLoaded', async () => {
    const home = $('[data-home-carousel]');
    const page = $('[data-showcase-page]');
    if (!home && !page) return;

    try {
      const manifest = await loadManifest();
      if (home) initHomeCarousel(home, manifest);
      if (page) initShowcasePage(page, manifest);
    } catch (err) {
      console.error(err);
    }
  });
})();
