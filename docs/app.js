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

  function mountDeliveryBanner() {
    const overview = document.getElementById("overview");
    if (!overview || !data.deliveryPath || document.getElementById("delivery-path-banner")) return;

    const style = document.createElement("style");
    style.textContent = `
      .delivery-path-banner{grid-column:1/-1;display:grid;grid-template-columns:auto 1fr auto;gap:16px;align-items:center;margin:0 0 20px;padding:14px 18px;border:1px solid rgba(56,189,248,.35);border-radius:16px;background:linear-gradient(90deg,rgba(124,58,237,.18),rgba(56,189,248,.1));box-shadow:0 0 28px rgba(56,189,248,.08)}
      .delivery-path-banner .delivery-kicker{font:600 11px/1.2 "IBM Plex Mono",monospace;letter-spacing:.12em;text-transform:uppercase;color:#7dd3fc;white-space:nowrap}
      .delivery-path-banner strong{display:block;font:700 clamp(16px,2vw,22px)/1.2 "Outfit",sans-serif;color:#f8fafc;margin-bottom:3px}
      .delivery-path-banner p{margin:0;color:#cbd5e1;font-size:14px;line-height:1.45}
      .delivery-path-banner .delivery-stack{font:600 12px/1.2 "IBM Plex Mono",monospace;color:#c4b5fd;white-space:nowrap}
      @media(max-width:760px){.delivery-path-banner{grid-template-columns:1fr;gap:7px}.delivery-path-banner .delivery-stack{white-space:normal}}
    `;
    document.head.appendChild(style);

    const banner = document.createElement("div");
    banner.id = "delivery-path-banner";
    banner.className = "delivery-path-banner";
    banner.setAttribute("role", "note");
    banner.innerHTML = `
      <div class="delivery-kicker">DELIVERY PATH // ACCEPTED PAWPRINT</div>
      <div>
        <strong>${data.deliveryPath}</strong>
        <p>${data.deliveryDetail || "The web rebuild establishes the shared backend for the later mobile client."}</p>
      </div>
      <div class="delivery-stack">PHP + SQLite → Flutter + Dart</div>
    `;
    overview.insertBefore(banner, overview.firstChild);
  }

  function mountPromptSystemUpdate() {
    const list = document.querySelector("#library .docs-list");
    if (!list || document.getElementById("prompt-functionality-reference-link")) return;

    const refUrl = data.promptReferenceUrl || "https://github.com/cubixmeow-commits/youarethesongnowV2/blob/main/docs/rebuild/13-prompting-functionality-reference.md";
    const analysisUrl = data.promptAnalysisUrl || "https://github.com/cubixmeow-commits/youarethesongnowV2/blob/main/docs/rebuild/14-prompt-quality-and-refinement-analysis.md";
    const vaultUrl = data.promptVaultUrl || "https://github.com/cubixmeow-commits/youarethesongnowV2/blob/main/development-vault/04%20Prompt%20Lab/V1%20Prompt%20Functionality%20Map.md";

    const ref = document.createElement("a");
    ref.id = "prompt-functionality-reference-link";
    ref.href = refUrl;
    ref.innerHTML = '<b>13</b><span><strong>Prompt Functionality Reference</strong><small>Five prompt sources, DB-managed styles, runtime assembly, model artifacts and retries.</small></span>';

    const analysis = document.createElement("a");
    analysis.href = analysisUrl;
    analysis.innerHTML = '<b>14</b><span><strong>Prompt Quality &amp; Refinement</strong><small>What to preserve, conflicts to remove, and how to modernize prompt behavior.</small></span>';

    const vault = document.createElement("a");
    vault.href = vaultUrl;
    vault.innerHTML = '<b>V</b><span><strong>V1 Prompt Functionality Map</strong><small>Vault working map, historical evolution, database recovery notes and V2 implications.</small></span>';

    list.appendChild(ref);
    list.appendChild(analysis);
    list.appendChild(vault);
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
    const backdrop = document.getElementById("nav-backdrop");
    if (!toggle || !panel) return;

    const isMobileNav = () => window.matchMedia(MOBILE_NAV_MQ).matches;

    function setOpen(open) {
      panel.classList.toggle("is-open", open);
      document.body.classList.toggle("nav-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
      if (backdrop) {
        if (open) backdrop.removeAttribute("hidden");
        else backdrop.setAttribute("hidden", "");
        backdrop.classList.toggle("is-visible", open);
      }
    }

    function closeNav() {
      setOpen(false);
    }

    function toggleNav() {
      setOpen(!panel.classList.contains("is-open"));
    }

    toggle.addEventListener("click", (event) => {
      event.preventDefault();
      event.stopPropagation();
      toggleNav();
    });

    if (backdrop) {
      backdrop.addEventListener("click", (event) => {
        event.preventDefault();
        closeNav();
      });
    }

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

    const mq = window.matchMedia(MOBILE_NAV_MQ);
    const onMq = (event) => {
      if (!event.matches) closeNav();
    };
    if (typeof mq.addEventListener === "function") mq.addEventListener("change", onMq);
    else if (typeof mq.addListener === "function") mq.addListener(onMq);
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
    mountDeliveryBanner();
    mountPromptSystemUpdate();
    hydrate();
    wireNav();
    wireRefreshCat();
  });
})();
