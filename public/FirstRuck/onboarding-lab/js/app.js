(function () {
  "use strict";

  var Model = window.FirstRuckModel;
  var Catalog = window.FirstRuckScreens;
  var ASSETS = "assets/brand";
  var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  var desktopShell = window.matchMedia("(min-width: 1000px)");
  var autoTimer = null;
  var analysisTimer = null;
  var liveTimer = null;
  var state = Model.createEmptyState();
  var packChecks = {};

  function $(id) {
    return document.getElementById(id);
  }

  function prefersReduced() {
    return reducedMotion.matches;
  }

  function el(tag, className, attrs) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (attrs) {
      Object.keys(attrs).forEach(function (key) {
        if (key === "text") node.textContent = attrs[key];
        else if (attrs[key] != null) node.setAttribute(key, attrs[key]);
      });
    }
    return node;
  }

  function svgNode(markup) {
    var wrap = document.createElement("div");
    wrap.innerHTML = markup;
    return wrap.firstElementChild;
  }

  function clearTimers() {
    if (autoTimer) {
      window.clearTimeout(autoTimer);
      autoTimer = null;
    }
    if (analysisTimer) {
      window.clearTimeout(analysisTimer);
      analysisTimer = null;
    }
  }

  function announce(message) {
    var region = $("live-region");
    if (!region) return;
    region.textContent = "";
    window.clearTimeout(liveTimer);
    liveTimer = window.setTimeout(function () {
      region.textContent = message;
    }, 40);
  }

  function loadState() {
    var persistent = null;
    var session = null;
    try {
      persistent = JSON.parse(window.localStorage.getItem(Model.STORAGE_PERSISTENT) || "null");
    } catch (error) {
      persistent = null;
    }
    try {
      session = JSON.parse(window.sessionStorage.getItem(Model.STORAGE_SESSION) || "null");
    } catch (error) {
      session = null;
    }
    state = Model.migrateState(persistent);
    if (session && session.safetyBranch) {
      state.sessionOnly.safetyBranch = session.safetyBranch;
    } else if (Model.hasSafetyRestriction(null) === false && Model.screenIndex(state.currentScreen) > Model.screenIndex("safety-gate")) {
      state.sessionOnly.safetyBranch = "prefer-not";
    }
    if (state.currentScreen !== "welcome" && state.currentScreen !== "what-rucking-is" && state.currentScreen !== "equipment-reassurance") {
      state.result = Model.buildPlan(state);
    }
  }

  function persist() {
    window.localStorage.setItem(Model.STORAGE_PERSISTENT, JSON.stringify(Model.persistentSnapshot(state)));
    if (state.sessionOnly.safetyBranch) {
      window.sessionStorage.setItem(Model.STORAGE_SESSION, JSON.stringify({ safetyBranch: state.sessionOnly.safetyBranch }));
    } else {
      window.sessionStorage.removeItem(Model.STORAGE_SESSION);
    }
  }

  function resetWalkthrough() {
    clearTimers();
    packChecks = {};
    window.localStorage.removeItem(Model.STORAGE_PERSISTENT);
    window.sessionStorage.removeItem(Model.STORAGE_SESSION);
    state = Model.createEmptyState();
    persist();
    render();
    announce("Walkthrough reset.");
  }

  function plan() {
    state.result = Model.buildPlan(state);
    return state.result;
  }

  function currentScreen() {
    return Catalog.getScreen(state.currentScreen);
  }

  function setAnswer(field, value) {
    if (field === "safetyBranch") {
      state.sessionOnly.safetyBranch = value;
    } else if (field === "loadSecured") {
      state.answers.loadSecured = !!value;
    } else if (field === "locationLabel") {
      state.answers.locationLabel = value == null ? "" : String(value);
    } else if (Object.prototype.hasOwnProperty.call(state.answers, field)) {
      state.answers[field] = value;
    }
    persist();
  }

  function goTo(id, options) {
    clearTimers();
    state.currentScreen = id;
    if (Model.screenIndex(id) >= Model.screenIndex("analysis")) {
      plan();
    }
    persist();
    render(options || {});
  }

  function continueForward() {
    var screen = currentScreen();
    var check = Model.validateScreen(screen.id, state);
    if (!check.ok) {
      showError(check);
      return;
    }
    if (screen.id === "leave-ready") return;
    goTo(Model.nextScreenId(screen.id), { entering: true });
  }

  function goBack() {
    var screen = currentScreen();
    if (screen.id === "welcome") return;
    goTo(Model.previousScreenId(screen.id));
  }

  function showError(check) {
    var node = document.getElementById("screen-error");
    if (node) {
      node.textContent = check.message;
    }
    var field = document.querySelector("[data-field='" + check.field + "']") || document.querySelector("[name='" + (check.field || "") + "']");
    if (field) {
      field.setAttribute("aria-invalid", "true");
      if (field.focus) field.focus();
    }
    announce(check.message);
  }

  function header(screen, progress) {
    var bar = el("header", "flow-header");
    var back = el("button", "icon-btn", { type: "button", "aria-label": "Back" });
    back.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>';
    back.addEventListener("click", goBack);
    if (screen.id === "welcome") back.hidden = true;
    var brand = el("div", "brand-mini");
    var mark = el("img", null, {
      src: ASSETS + "/logo/firstruck-mark.svg",
      width: "28",
      height: "28",
      alt: ""
    });
    brand.appendChild(mark);
    brand.appendChild(document.createTextNode("First Ruck"));
    var count = el("p", "chapter-count", { text: progress.label });
    bar.appendChild(back);
    bar.appendChild(brand);
    bar.appendChild(count);
    return bar;
  }

  function progressLine(progress) {
    var line = el("div", "progress-line");
    var fill = el("span");
    fill.style.transform = "scaleX(" + (progress.current / progress.total) + ")";
    line.setAttribute("role", "progressbar");
    line.setAttribute("aria-valuemin", "1");
    line.setAttribute("aria-valuemax", String(progress.total));
    line.setAttribute("aria-valuenow", String(progress.current));
    line.setAttribute("aria-label", progress.label);
    line.appendChild(fill);
    return line;
  }

  function ctaBar(label, onClick, extra) {
    var wrap = el("div", "screen-cta");
    var button = el("button", "primary-btn", { type: "button", text: label });
    button.addEventListener("click", onClick);
    wrap.appendChild(button);
    if (extra) wrap.appendChild(extra);
    return wrap;
  }

  function errorNode() {
    return el("p", "error", { id: "screen-error" });
  }

  function choiceList(screen, selectedValue, onPick) {
    var fieldset = el("fieldset", "choice-list");
    var legend = el("legend", "sr-only", { text: screen.prompt || screen.headline });
    fieldset.appendChild(legend);
    screen.options.forEach(function (option) {
      var label = el("label", "choice-row");
      var input = el("input", null, {
        type: "radio",
        name: screen.name,
        value: option.value,
        "data-field": screen.field
      });
      if (selectedValue === option.value) input.checked = true;
      input.addEventListener("change", function () {
        onPick(option.value);
      });
      var face = el("span", "choice-face");
      face.appendChild(el("span", null, { text: option.label }));
      face.appendChild(el("span", "choice-mark", { "aria-hidden": "true" }));
      label.appendChild(input);
      label.appendChild(face);
      fieldset.appendChild(label);
    });
    return fieldset;
  }

  function scheduleAuto(screen) {
    if (!screen.autoAdvance || prefersReduced()) return;
    autoTimer = window.setTimeout(function () {
      if (Model.validateScreen(screen.id, state).ok) continueForward();
    }, 250);
  }

  function topoMarkup(light) {
    var stroke = light ? "rgba(249,243,230,0.16)" : "rgba(20,51,29,0.12)";
    return '<svg class="topo" viewBox="0 0 390 844" aria-hidden="true" focusable="false"><path d="M-20 210c80-40 140-20 190 20s110 70 220 30" fill="none" stroke="' + stroke + '" stroke-width="1.2"/><path d="M-10 280c90-30 150 10 210 40s140 20 210-20" fill="none" stroke="' + stroke + '" stroke-width="1.2"/><path d="M-30 620c120 40 190-20 250 10s140 80 220 40" fill="none" stroke="' + stroke + '" stroke-width="1.2"/></svg>';
  }

  function renderWelcome(root) {
    var screen = Catalog.getScreen("welcome");
    var frame = el("section", "screen-frame is-forest welcome-screen" + (prefersReduced() ? "" : " is-entering"));
    var photo = el("div", "welcome-photo");
    photo.appendChild(el("img", null, {
      src: ASSETS + "/photography/hero-beginner-greenway.png",
      alt: "A woman beginning a walk on a tree-lined greenway with a compact backpack.",
      width: "390",
      height: "844"
    }));
    photo.appendChild(el("div", "welcome-scrim", { "aria-hidden": "true" }));
    photo.appendChild(svgNode(topoMarkup(true)));
    photo.appendChild(svgNode(
      '<svg class="welcome-route" viewBox="0 0 390 844" aria-hidden="true" focusable="false"><path class="route-path" d="M42 760 C 70 700, 40 640, 86 590 S 70 500, 120 455"/></svg>'
    ));
    var copy = el("div", "welcome-copy enter-copy");
    var brand = el("div", "welcome-brand");
    brand.appendChild(el("img", null, {
      src: ASSETS + "/logo/firstruck-mark.svg",
      width: "40",
      height: "40",
      alt: ""
    }));
    brand.appendChild(el("span", null, { text: "First Ruck" }));
    copy.appendChild(brand);
    var cluster = el("div");
    cluster.appendChild(el("p", "eyebrow", { text: screen.eyebrow }));
    cluster.appendChild(el("h1", "display", { text: screen.headline }));
    cluster.appendChild(el("p", "lede", { text: screen.body }));
    copy.appendChild(cluster);
    copy.appendChild(ctaBar(screen.action, continueForward, el("p", "footnote", { text: screen.footnote })));
    frame.appendChild(photo);
    frame.appendChild(copy);
    root.appendChild(frame);
  }

  function wrapScreen(screen, inner, cta) {
    var progress = Catalog.chapterProgress(screen);
    var frame = el("section", "screen-frame is-" + screen.theme + (prefersReduced() ? "" : " is-entering"));
    if (screen.theme === "forest") frame.classList.add("is-forest");
    frame.appendChild(header(screen, progress));
    frame.appendChild(progressLine(progress));
    var scroll = el("div", "screen-scroll");
    scroll.appendChild(inner);
    frame.appendChild(scroll);
    if (cta) frame.appendChild(cta);
    return frame;
  }

  function renderBeats(root, screen) {
    var inner = document.createDocumentFragment();
    inner.appendChild(svgNode(
      '<svg class="walk-stage walk-art" viewBox="0 0 360 140" aria-hidden="true" focusable="false"><g fill="none" stroke="#fffdfa" stroke-width="3" stroke-linecap="round"><path d="M70 118 V78"/><path d="M70 88 l-18 22"/><path d="M70 88 l16 22"/><circle cx="70" cy="62" r="10"/><path d="M70 74 c12 -8 22 -4 28 6"/></g><g fill="none" stroke="#f45707" stroke-width="3" stroke-linecap="round"><path d="M250 118 V78"/><path d="M250 88 l-18 22"/><path d="M250 88 l16 22"/><circle cx="250" cy="62" r="10"/><path d="M262 80 c-2 -18 8 -28 22 -24 8 2 14 12 12 22z"/><path d="M250 74 c10 -6 18 -2 24 8"/></g><path d="M110 44 C 160 20, 200 20, 236 44" fill="none" stroke="#f45707" stroke-width="2" stroke-dasharray="4 6"/></svg>'
    ));
    inner.appendChild(el("h1", "display", { text: screen.headline }));
    inner.appendChild(el("p", "lede", { text: screen.body }));
    var beats = el("div", "beats");
    screen.beats.forEach(function (beat) {
      var item = el("div", "beat");
      item.appendChild(el("strong", null, { text: beat.title }));
      item.appendChild(el("span", null, { text: beat.text }));
      beats.appendChild(item);
    });
    inner.appendChild(beats);
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, continueForward)));
  }

  function renderFlatlay(root, screen) {
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.headline }));
    inner.appendChild(el("p", "lede", { text: screen.body }));
    inner.appendChild(el("p", "callout", { text: screen.callout }));
    var stage = el("div", "flatlay-stage");
    var image = el("img", null, {
      src: ASSETS + "/photography/equipment-flatlay.png",
      alt: "An ordinary backpack packed with cushioned water bottles beside walking shoes and socks.",
      width: "780",
      height: "585"
    });
    stage.appendChild(image);
    inner.appendChild(stage);
    var chips = el("div", "item-chips");
    screen.items.forEach(function (item, index) {
      var chip = el("button", "item-chip", { type: "button", text: item.label });
      chip.setAttribute("aria-pressed", index === 0 ? "true" : "false");
      chip.addEventListener("click", function () {
        image.style.objectPosition = item.focus;
        chips.querySelectorAll("button").forEach(function (btn) {
          btn.setAttribute("aria-pressed", btn === chip ? "true" : "false");
        });
      });
      chip.addEventListener("focus", function () {
        image.style.objectPosition = item.focus;
      });
      chips.appendChild(chip);
    });
    inner.appendChild(chips);
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, continueForward)));
  }

  function renderChoice(root, screen) {
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.prompt || screen.headline }));
    if (screen.body && screen.kind === "safety") inner.appendChild(el("p", "lede", { text: screen.body }));
    if (screen.help) inner.appendChild(el("p", "help", { text: screen.help }));
    if (screen.photo === "community") {
      var windowed = el("div", "photo-window");
      windowed.appendChild(el("img", null, {
        src: ASSETS + "/photography/community-park-walk.png",
        alt: "Three adults walking together through a park with small backpacks.",
        width: "780",
        height: "240"
      }));
      inner.insertBefore(windowed, inner.firstChild);
    }
    var selected = screen.sessionOnly ? state.sessionOnly.safetyBranch : state.answers[screen.field];
    inner.appendChild(choiceList(screen, selected, function (value) {
      setAnswer(screen.field, value);
      scheduleAuto(screen);
    }));
    inner.appendChild(errorNode());
    var action = screen.autoAdvance ? "Continue" : (screen.action || "Continue");
    root.appendChild(wrapScreen(screen, inner, ctaBar(action, continueForward)));
  }

  function renderLoad(root, screen) {
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.prompt }));
    inner.appendChild(el("p", "help", { text: screen.help }));
    inner.appendChild(choiceList(screen, state.answers.loadType, function (value) {
      setAnswer("loadType", value);
    }));
    var toggle = el("label", "toggle-row");
    var box = el("input", null, { type: "checkbox", name: screen.toggle.name, "data-field": "loadSecured" });
    box.checked = !!state.answers.loadSecured;
    box.addEventListener("change", function () {
      setAnswer("loadSecured", box.checked);
    });
    toggle.appendChild(box);
    toggle.appendChild(el("span", null, { text: screen.toggle.label }));
    inner.appendChild(toggle);
    inner.appendChild(errorNode());
    root.appendChild(wrapScreen(screen, inner, ctaBar("Continue", continueForward)));
  }

  function renderDual(root, screen) {
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.prompt }));
    screen.groups.forEach(function (group) {
      var fieldset = el("fieldset", "choice-list dual-group");
      fieldset.appendChild(el("legend", null, { text: group.legend }));
      group.options.forEach(function (option) {
        var label = el("label", "choice-row");
        var input = el("input", null, {
          type: "radio",
          name: group.name,
          value: option.value,
          "data-field": group.field
        });
        if (state.answers[group.field] === option.value) input.checked = true;
        input.addEventListener("change", function () {
          setAnswer(group.field, option.value);
        });
        var face = el("span", "choice-face");
        face.appendChild(el("span", null, { text: option.label }));
        face.appendChild(el("span", "choice-mark", { "aria-hidden": "true" }));
        label.appendChild(input);
        label.appendChild(face);
        fieldset.appendChild(label);
      });
      inner.appendChild(fieldset);
    });
    inner.appendChild(errorNode());
    root.appendChild(wrapScreen(screen, inner, ctaBar("Continue", continueForward)));
  }

  function reflectionHeadline(result) {
    if (result.load.maxAddedLb === 0) return "We will begin without added load.";
    if (state.answers.weeklyRhythm === "flexible" || state.answers.weeklyRhythm === "one") {
      return "We will build around one reliable weekend session.";
    }
    if (result.durationMinutes <= 20) return "We will keep your first session inside " + result.durationMinutes + " minutes.";
    return "We will keep your first session inside " + result.durationMinutes + " minutes.";
  }

  function renderReflection(root, screen) {
    var result = plan();
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: reflectionHeadline(result) }));
    inner.appendChild(el("p", "lede", { text: screen.body }));
    var diagram = el("div", "plan-diagram");
    [
      ["Time", result.durationMinutes + " minutes"],
      ["Load", result.load.label],
      ["Terrain", result.terrain.label]
    ].forEach(function (row) {
      var line = el("div", "plan-line");
      line.appendChild(el("span", null, { text: row[0] }));
      line.appendChild(el("strong", null, { text: row[1] }));
      diagram.appendChild(line);
    });
    inner.appendChild(diagram);
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, continueForward)));
  }

  function renderPackSetup(root, screen) {
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.headline }));
    inner.appendChild(el("p", "lede", { text: screen.body }));
    var photo = el("div", "pack-photo");
    photo.appendChild(el("img", null, {
      src: ASSETS + "/photography/pack-fit-adjustment.png",
      alt: "A man adjusting the sternum strap of a compact backpack before walking.",
      width: "780",
      height: "624"
    }));
    inner.appendChild(photo);
    var svg = svgNode(
      '<svg class="pack-svg pack-art" viewBox="0 0 220 120" aria-hidden="true" focusable="false"><rect x="70" y="18" width="80" height="86" rx="14" fill="none" stroke="#14331D" stroke-width="3"/><rect data-part="load" x="88" y="38" width="44" height="40" rx="8" fill="#e9e0cf" stroke="#14331D"/><path d="M90 18 C 90 8 130 8 130 18" fill="none" stroke="#14331D" stroke-width="3"/><path d="M78 40 C 48 48 48 86 86 90" fill="none" stroke="#f45707" stroke-width="3"/><path d="M142 40 C 172 48 172 86 134 90" fill="none" stroke="#f45707" stroke-width="3"/></svg>'
    );
    inner.appendChild(svg);
    var list = el("div", "check-list");
    screen.checks.forEach(function (text, index) {
      var label = el("label");
      var box = el("input", null, { type: "checkbox" });
      box.checked = !!packChecks[index];
      box.addEventListener("change", function () {
        packChecks[index] = box.checked;
        var parts = svg.querySelectorAll("[data-part]");
        parts.forEach(function (part) {
          part.classList.toggle("is-on", box.checked);
        });
      });
      label.appendChild(box);
      label.appendChild(el("span", null, { text: text }));
      list.appendChild(label);
    });
    inner.appendChild(list);
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, continueForward)));
  }

  function renderRouteChapter(root, screen) {
    var inner = document.createDocumentFragment();
    var hero = el("div", "route-hero");
    hero.appendChild(el("img", null, {
      src: ASSETS + "/photography/route-choice-greenway.png",
      alt: "A walker approaching a choice between two gentle public park paths.",
      width: "780",
      height: "280"
    }));
    hero.appendChild(el("p", "route-caption", { text: screen.photoCaption }));
    inner.appendChild(hero);
    inner.appendChild(el("h1", "display", { text: screen.prompt }));
    inner.appendChild(choiceList(screen, state.answers.surface, function (value) {
      setAnswer("surface", value);
      scheduleAuto(screen);
    }));
    inner.appendChild(errorNode());
    root.appendChild(wrapScreen(screen, inner, ctaBar("Continue", continueForward)));
  }

  function renderStartingArea(root, screen) {
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.prompt }));
    inner.appendChild(el("p", "help", { text: screen.help }));
    var input = el("input", "area-field", {
      type: "text",
      name: "locationLabel",
      "data-field": "locationLabel",
      autocomplete: "address-level-2",
      placeholder: screen.placeholder,
      maxlength: "80"
    });
    input.value = state.answers.locationLabel || "";
    input.addEventListener("input", function () {
      setAnswer("locationLabel", input.value);
    });
    inner.appendChild(input);
    var later = el("button", "later-btn", { type: "button", text: screen.chooseLater });
    later.addEventListener("click", function () {
      setAnswer("locationLabel", "");
      input.value = "";
      continueForward();
    });
    inner.appendChild(later);
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, continueForward)));
  }

  function renderAnalysis(root, screen) {
    plan();
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.headline }));
    var list = el("div", "analysis-steps");
    screen.steps.forEach(function (step, index) {
      var row = el("div", "analysis-step" + (prefersReduced() ? " is-on" : ""));
      row.appendChild(el("b", null, { "aria-hidden": "true" }));
      row.appendChild(el("span", null, { text: step }));
      row.setAttribute("data-step", String(index));
      list.appendChild(row);
    });
    inner.appendChild(list);
    var details = el("details", "how-details");
    details.appendChild(el("summary", null, { text: screen.howTitle }));
    details.appendChild(el("p", null, { text: screen.howBody }));
    inner.appendChild(details);
    var frame = wrapScreen(screen, inner, ctaBar(screen.action, continueForward));
    root.appendChild(frame);
    if (!prefersReduced()) {
      var steps = frame.querySelectorAll(".analysis-step");
      steps.forEach(function (row, index) {
        window.setTimeout(function () {
          row.classList.add("is-on");
        }, 450 * index);
      });
      analysisTimer = window.setTimeout(continueForward, 1900);
    }
  }

  function renderProfile(root, screen) {
    var result = plan();
    var inner = document.createDocumentFragment();
    inner.appendChild(el("p", "eyebrow", { text: screen.eyebrow }));
    inner.appendChild(el("h1", "display", { text: result.profileLabel }));
    if (result.pauseCopy) inner.appendChild(el("p", "pause-banner", { text: result.pauseCopy }));
    inner.appendChild(el("p", "lede", { text: result.goal.headline }));
    var grid = el("div", "profile-grid");
    [
      ["First session", result.durationMinutes + " min"],
      ["Added load", result.load.label],
      ["Terrain", result.terrain.label],
      ["Weekly rhythm", result.weeklyLayout.label]
    ].forEach(function (item) {
      var cell = el("div", "profile-value");
      cell.appendChild(el("span", null, { text: item[0] }));
      cell.appendChild(el("strong", null, { text: item[1] }));
      grid.appendChild(cell);
    });
    inner.appendChild(grid);
    inner.appendChild(el("h2", null, { text: "We chose this because…" }));
    var reasons = el("ul", "reasons");
    result.reasons.forEach(function (reason) {
      reasons.appendChild(el("li", null, { text: reason }));
    });
    inner.appendChild(reasons);
    inner.appendChild(el("p", "help", { text: result.goal.coaching }));
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, continueForward)));
  }

  function renderWeeks(root, screen) {
    var result = plan();
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.headline }));
    var trail = el("div", "trail");
    var weeks = [
      { title: "Week 1 · Learn the setup", body: result.durationMinutes + " minutes, " + result.load.label.toLowerCase() + ", on a familiar forgiving route." },
      { title: "Week 2 · Repeat the baseline", body: "Same duration, load, and terrain. Improve pack fit or footwear only." },
      { title: "Week 3 · Change one thing", body: result.week3.label + ". " + result.week3.reason },
      { title: "Week 4 · Make it repeatable", body: "Repeat Week 3. No automatic increase." }
    ];
    weeks.forEach(function (week) {
      var card = el("article", "week");
      card.appendChild(el("h3", null, { text: week.title }));
      card.appendChild(el("p", null, { text: week.body }));
      trail.appendChild(card);
    });
    inner.appendChild(trail);
    if (result.weeklyLayout.extraDays.length) {
      var extras = el("ul", "extras");
      result.weeklyLayout.extraDays.forEach(function (day) {
        extras.appendChild(el("li", null, { text: day.label }));
      });
      inner.appendChild(extras);
    }
    inner.appendChild(el("p", "note", { text: result.expertReviewNote }));
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, continueForward)));
  }

  function renderRoutes(root, screen) {
    var result = plan();
    var inner = document.createDocumentFragment();
    inner.appendChild(el("h1", "display", { text: screen.headline }));
    inner.appendChild(el("p", "lede", { text: screen.body }));
    inner.appendChild(el("p", "help", { text: result.locationCopy + ". " + result.goal.routeTone }));
    result.routes.forEach(function (route) {
      var card = el("button", "route-card" + (state.selectedDemoRouteId === route.id ? " is-selected" : ""), { type: "button" });
      card.appendChild(el("span", "route-badge", { text: route.demonstrationLabel }));
      card.appendChild(el("p", "fit-label", { text: route.badge }));
      card.appendChild(el("h3", null, { text: route.name }));
      var meta = el("ul", "meta-list");
      meta.appendChild(el("li", null, { text: route.distanceLabel }));
      meta.appendChild(el("li", null, { text: route.terrain }));
      meta.appendChild(el("li", null, { text: route.shape }));
      meta.appendChild(el("li", null, { text: route.earlyTurnaround ? "Early turnaround available" : "No early turnaround" }));
      card.appendChild(meta);
      var why = el("ul", "reasons");
      route.reasons.forEach(function (reason) {
        why.appendChild(el("li", null, { text: reason }));
      });
      card.appendChild(why);
      var unknown = el("p", "help", { text: "What is unknown: " + route.unknowns.join(", ") + "." });
      card.appendChild(unknown);
      card.addEventListener("click", function () {
        state.selectedDemoRouteId = route.id;
        persist();
        render();
      });
      inner.appendChild(card);
    });
    inner.appendChild(errorNode());
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, continueForward)));
  }

  function openToday() {
    var result = plan();
    var route = result.selectedRoute;
    var backdrop = el("div", "dialog-backdrop");
    var dialog = el("div", "dialog-sheet");
    dialog.setAttribute("role", "dialog");
    dialog.setAttribute("aria-modal", "true");
    dialog.setAttribute("aria-labelledby", "today-title");
    dialog.appendChild(el("p", "today-kicker", { text: "Today preview" }));
    dialog.appendChild(el("h2", null, { id: "today-title", text: "Your next session, when the app is live." }));
    dialog.appendChild(el("p", null, { text: (route && route.name) || "Demonstration route" }));
    dialog.appendChild(el("p", "help", { text: result.durationMinutes + " minutes · " + result.load.label + " · " + result.locationCopy }));
    dialog.appendChild(el("p", null, { text: result.goal.learn }));
    var close = el("button", "primary-btn", { type: "button", text: "Close preview" });
    dialog.appendChild(close);
    backdrop.appendChild(dialog);
    document.body.appendChild(backdrop);
    close.focus();
    function dismiss() {
      backdrop.remove();
      document.removeEventListener("keydown", onKey);
      var trigger = document.querySelector(".primary-btn");
      if (trigger) trigger.focus();
    }
    function onKey(event) {
      if (event.key === "Escape") dismiss();
      if (event.key === "Tab") {
        event.preventDefault();
        close.focus();
      }
    }
    close.addEventListener("click", dismiss);
    backdrop.addEventListener("click", function (event) {
      if (event.target === backdrop) dismiss();
    });
    document.addEventListener("keydown", onKey);
  }

  function renderLeaveReady(root, screen) {
    var result = plan();
    var inner = document.createDocumentFragment();
    var hero = el("div", "finish-hero");
    hero.appendChild(el("img", null, {
      src: ASSETS + "/photography/completion-portrait.png",
      alt: "A man standing calmly after a neighborhood walk with a small backpack.",
      width: "320",
      height: "360"
    }));
    var intro = el("div");
    intro.appendChild(el("h1", "display", { text: screen.headline }));
    hero.appendChild(intro);
    inner.appendChild(hero);
    if (result.pauseCopy) inner.appendChild(el("p", "pause-banner", { text: result.pauseCopy }));
    var items = el("ul", "checklist");
    var routeName = result.selectedRoute ? result.selectedRoute.name : "Demonstration route";
    [
      routeName + " · Demonstration route",
      result.durationMinutes + " minutes · " + result.load.label,
      result.packGuidance,
      result.footwearNote,
      "Check weather, heat, air quality, lightning, daylight, and closures before you leave.",
      "Carry water and a charged phone.",
      "Tell someone when the area is less familiar.",
      "Stop or adjust for a hot spot, rubbing, numbness, or pain that changes how you walk.",
      "Turn around early is always an option."
    ].forEach(function (item) {
      items.appendChild(el("li", null, { text: item }));
    });
    inner.appendChild(items);
    var secondary = el("button", "secondary-btn", { type: "button", text: screen.secondary });
    secondary.addEventListener("click", function () {
      goTo("goal");
    });
    root.appendChild(wrapScreen(screen, inner, ctaBar(screen.action, openToday, secondary)));
  }

  function renderPhone() {
    var root = $("phone-root");
    root.innerHTML = "";
    var screen = currentScreen();
    var status = $("status-bar");
    var home = $("home-bar");
    if (status) status.classList.toggle("is-light", screen.status === "light");
    if (home) home.classList.toggle("is-light", screen.status === "light");

    switch (screen.kind) {
      case "welcome":
        renderWelcome(root);
        break;
      case "edu-beats":
        renderBeats(root, screen);
        break;
      case "equipment-flatlay":
        renderFlatlay(root, screen);
        break;
      case "safety":
      case "choice":
        renderChoice(root, screen);
        break;
      case "load-choice":
        renderLoad(root, screen);
        break;
      case "dual-choice":
        renderDual(root, screen);
        break;
      case "reflection":
        renderReflection(root, screen);
        break;
      case "pack-setup":
        renderPackSetup(root, screen);
        break;
      case "route-chapter":
        renderRouteChapter(root, screen);
        break;
      case "starting-area":
        renderStartingArea(root, screen);
        break;
      case "analysis":
        renderAnalysis(root, screen);
        break;
      case "profile":
        renderProfile(root, screen);
        break;
      case "four-weeks":
        renderWeeks(root, screen);
        break;
      case "demo-routes":
        renderRoutes(root, screen);
        break;
      case "leave-ready":
        renderLeaveReady(root, screen);
        break;
      default:
        renderChoice(root, screen);
    }

    var heading = root.querySelector("h1");
    if (heading) heading.setAttribute("tabindex", "-1");
  }

  function renderReview() {
    var screen = currentScreen();
    var title = $("explain-title");
    var purpose = $("explain-purpose");
    var uses = $("explain-uses");
    var changes = $("explain-changes");
    var progress = $("progress-label");
    if (title) title.textContent = screen.title;
    if (purpose) purpose.textContent = screen.purpose;
    if (uses) uses.textContent = screen.uses;
    if (changes) changes.textContent = screen.changes;
    if (progress) progress.textContent = screen.number + " / 25";

    var toc = $("toc");
    if (!toc) return;
    toc.innerHTML = "";
    var lastChapter = "";
    Catalog.SCREENS.forEach(function (item) {
      if (item.chapter !== lastChapter) {
        toc.appendChild(el("p", "toc-chapter", { text: item.chapterLabel }));
        lastChapter = item.chapter;
      }
      var btn = el("button", "toc-btn", { type: "button", text: item.number + ". " + item.title });
      if (item.id === screen.id) btn.classList.add("is-current");
      if (Model.screenIndex(item.id) < Model.screenIndex(screen.id)) btn.classList.add("is-done");
      btn.addEventListener("click", function () {
        goTo(item.id);
      });
      toc.appendChild(btn);
    });
  }

  function render() {
    renderPhone();
    renderReview();
  }

  function interactiveTarget(target) {
    if (!target || !target.closest) return false;
    return !!target.closest("input, textarea, select, [contenteditable='true']");
  }

  function onKey(event) {
    if (!desktopShell.matches) return;
    if (interactiveTarget(event.target)) return;
    if (event.key === "ArrowRight") {
      event.preventDefault();
      if (state.currentScreen !== "leave-ready") goTo(Model.nextScreenId(state.currentScreen), { entering: true });
    } else if (event.key === "ArrowLeft") {
      event.preventDefault();
      goBack();
    }
  }

  function viewState() {
    var backdrop = el("div", "dialog-backdrop");
    var dialog = el("div", "dialog-sheet");
    dialog.setAttribute("role", "dialog");
    dialog.setAttribute("aria-modal", "true");
    dialog.setAttribute("aria-labelledby", "state-title");
    dialog.appendChild(el("h2", null, { id: "state-title", text: "Current local state" }));
    var pre = el("pre", "state-pre");
    pre.textContent = JSON.stringify({
      persistent: Model.persistentSnapshot(state),
      sessionOnly: state.sessionOnly,
      result: state.result && {
        profileLabel: state.result.profileLabel,
        durationMinutes: state.result.durationMinutes,
        load: state.result.load,
        terrain: state.result.terrain,
        week3: state.result.week3,
        selectedRoute: state.selectedDemoRouteId
      }
    }, null, 2);
    dialog.appendChild(pre);
    var close = el("button", "primary-btn", { type: "button", text: "Close" });
    dialog.appendChild(close);
    backdrop.appendChild(dialog);
    document.body.appendChild(backdrop);
    close.focus();
    function dismiss() {
      backdrop.remove();
      document.removeEventListener("keydown", onEsc);
      $("state-btn").focus();
    }
    function onEsc(event) {
      if (event.key === "Escape") dismiss();
    }
    close.addEventListener("click", dismiss);
    document.addEventListener("keydown", onEsc);
  }

  function bindShell() {
    $("reset-btn").addEventListener("click", resetWalkthrough);
    $("state-btn").addEventListener("click", viewState);
    $("review-back").addEventListener("click", goBack);
    $("review-next").addEventListener("click", function () {
      if (state.currentScreen === "leave-ready") return;
      goTo(Model.nextScreenId(state.currentScreen), { entering: true });
    });
    document.addEventListener("keydown", onKey);
  }

  loadState();
  bindShell();
  render();
})();
