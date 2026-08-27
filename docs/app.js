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
      .workshop-panel{margin:26px 0;padding:24px;border:1px solid rgba(167,139,250,.32);border-radius:20px;background:linear-gradient(135deg,rgba(91,33,182,.14),rgba(15,23,42,.74));box-shadow:0 0 34px rgba(124,58,237,.08)}
      .workshop-panel .workshop-grid{display:grid;grid-template-columns:1fr auto;gap:20px;align-items:center}
      .workshop-panel .workshop-kicker{font:600 11px/1.2 "IBM Plex Mono",monospace;letter-spacing:.12em;text-transform:uppercase;color:#c4b5fd;margin:0 0 7px}
      .workshop-panel h2{margin:0 0 8px;font:700 clamp(22px,3vw,32px)/1.1 "Outfit",sans-serif;color:#f8fafc}
      .workshop-panel p{margin:0;color:#cbd5e1;line-height:1.55;max-width:820px}
      .workshop-meta{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}
      .workshop-meta span{font:600 11px/1.2 "IBM Plex Mono",monospace;color:#bae6fd;border:1px solid rgba(125,211,252,.25);border-radius:999px;padding:7px 9px;background:rgba(2,6,23,.34)}
      .workshop-actions{display:flex;flex-direction:column;gap:9px;min-width:190px}
      .workshop-actions a{text-align:center;text-decoration:none;font:600 12px/1.2 "IBM Plex Mono",monospace;border-radius:12px;padding:11px 14px}
      .workshop-actions .primary{color:#fff;border:1px solid rgba(125,211,252,.42);background:linear-gradient(90deg,rgba(124,58,237,.35),rgba(56,189,248,.18))}
      .workshop-actions .secondary{color:#d8b4fe;border:1px solid rgba(196,181,253,.25);background:rgba(15,23,42,.45)}
      @media(max-width:760px){.delivery-path-banner{grid-template-columns:1fr;gap:7px}.delivery-path-banner .delivery-stack{white-space:normal}.workshop-panel .workshop-grid{grid-template-columns:1fr}.workshop-actions{min-width:0}}
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

  function mountFeatureWorkshop() {
    const focus = document.getElementById("focus");
    if (!focus || document.getElementById("feature-workshop")) return;

    const section = document.createElement("section");
    section.id = "feature-workshop";
    section.className = "workshop-panel";
    section.innerHTML = `
      <div class="workshop-grid">
        <div>
          <p class="workshop-kicker">TONIGHT // FEATURE DEFINITION WORKSHOP</p>
          <h2>Turn 515 exploratory questions into the exact first-build contract.</h2>
          <p>A guided facilitator prompt now walks CuBiX Meow and Brut through one owner-gate question at a time, classifying each feature as FIRST BUILD, SOON AFTER, LATER, MAYBE / RESEARCH, or NO / RETIRE. The final output is a concrete build-one feature contract, not a wish list.</p>
          <div class="workshop-meta">
            <span>515-question vault source</span>
            <span>one question at a time</span>
            <span>copy-ready prompt</span>
            <span>build freeze stays active</span>
          </div>
        </div>
        <div class="workshop-actions">
          <a class="primary" href="feature-workshop.html">Open + Copy Prompt →</a>
          <a class="secondary" href="https://github.com/cubixmeow-commits/youarethesongnowV2/blob/main/development-vault/05%20Product%20Design/First%20Build%20Feature%20Workshop.md">Full Vault Workshop ↗</a>
        </div>
      </div>
    `;
    focus.insertAdjacentElement("afterend", section);

    const nav = document.querySelector(".rail-nav");
    if (nav && !nav.querySelector('a[href="#feature-workshop"]')) {
      const link = document.createElement("a");
      link.href = "#feature-workshop";
      link.textContent = "Feature Workshop";
      const decisionsLink = nav.querySelector('a[href="#decisions"]');
      if (decisionsLink) nav.insertBefore(link, decisionsLink);
      else nav.appendChild(link);
    }
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
    mountFeatureWorkshop();
    mountPromptSystemUpdate();
    hydrate();
    wireNav();
    wireRefreshCat();
  });
})();
