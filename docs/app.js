/**
 * Meow Control interactions — light, static, GitHub Pages friendly.
 */
(function () {
  const data = window.MEOW_CONTROL || {};

  function pickCatLine() {
    const lines = data.catSays || ["Paw status: planning mode."];
    return lines[Math.floor(Math.random() * lines.length)];
  }

  function hydrate() {
    const bubble = document.getElementById("cat-says-text");
    if (bubble) bubble.textContent = pickCatLine();

    const stamp = document.getElementById("snapshot-updated");
    if (stamp && data.updated) stamp.textContent = data.updated;

    document.querySelectorAll("[data-meow]").forEach((el) => {
      const key = el.getAttribute("data-meow");
      if (key && data[key] != null) el.textContent = data[key];
    });
  }

  function wireNav() {
    const toggle = document.getElementById("nav-toggle");
    const panel = document.getElementById("side-nav");
    if (!toggle || !panel) return;
    toggle.addEventListener("click", () => {
      const open = panel.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  function wireRefreshCat() {
    const btn = document.getElementById("refresh-cat-says");
    const bubble = document.getElementById("cat-says-text");
    if (!btn || !bubble) return;
    btn.addEventListener("click", () => {
      bubble.textContent = pickCatLine();
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    hydrate();
    wireNav();
    wireRefreshCat();
  });
})();
