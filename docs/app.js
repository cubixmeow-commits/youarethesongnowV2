/**
 * Meow Control interactions — light, static, GitHub Pages friendly.
 */
(function () {
  const data = window.MEOW_CONTROL || {};
  const MOBILE_NAV_MQ = "(max-width: 980px)";

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
    const closeBtn = document.getElementById("nav-close");
    const panel = document.getElementById("side-nav");
    const backdrop = document.getElementById("nav-backdrop");
    if (!toggle || !panel) return;

    const isMobileNav = () => window.matchMedia(MOBILE_NAV_MQ).matches;

    function setOpen(open) {
      panel.classList.toggle("is-open", open);
      document.body.classList.toggle("nav-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.textContent = open ? "Close" : "Menu";
      if (backdrop) {
        backdrop.hidden = !open;
        backdrop.classList.toggle("is-visible", open);
      }
      if (open && isMobileNav()) {
        closeBtn?.focus();
      } else if (!open) {
        toggle.focus({ preventScroll: true });
      }
    }

    function openNav() {
      setOpen(true);
    }

    function closeNav() {
      setOpen(false);
    }

    function toggleNav() {
      setOpen(!panel.classList.contains("is-open"));
    }

    toggle.addEventListener("click", (event) => {
      event.stopPropagation();
      toggleNav();
    });

    closeBtn?.addEventListener("click", (event) => {
      event.stopPropagation();
      closeNav();
    });

    backdrop?.addEventListener("click", closeNav);

    panel.querySelectorAll(".rail-nav a").forEach((link) => {
      link.addEventListener("click", () => {
        if (isMobileNav()) closeNav();
      });
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && panel.classList.contains("is-open")) {
        closeNav();
      }
    });

    window.matchMedia(MOBILE_NAV_MQ).addEventListener("change", (event) => {
      if (!event.matches) closeNav();
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
