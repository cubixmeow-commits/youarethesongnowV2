(function (root, factory) {
  if (typeof module === "object" && module.exports) {
    module.exports = factory();
  } else {
    root.FirstRuckModel = factory();
  }
})(typeof globalThis !== "undefined" ? globalThis : this, function () {
  "use strict";

  var STORAGE_PERSISTENT = "first-ruck-onboarding-lab-v1";
  var STORAGE_SESSION = "first-ruck-onboarding-lab-session-v1";
  var STATE_VERSION = 1;

  var SCREEN_IDS = [
    "welcome",
    "what-rucking-is",
    "equipment-reassurance",
    "goal",
    "comfortable-walk",
    "recent-activity",
    "loaded-experience",
    "available-time",
    "weekly-rhythm",
    "safety-gate",
    "reflection",
    "pack-type",
    "load-available",
    "shoes-socks",
    "pack-setup",
    "surface",
    "hills",
    "route-shape",
    "route-priority",
    "starting-area",
    "analysis",
    "profile",
    "four-weeks",
    "demo-routes",
    "leave-ready"
  ];

  var ANSWER_KEYS = [
    "goal",
    "comfortableMinutes",
    "recentActivity",
    "loadedExperience",
    "availableMinutes",
    "weeklyRhythm",
    "packType",
    "loadType",
    "loadSecured",
    "shoes",
    "socks",
    "surface",
    "hills",
    "routeShape",
    "routePriority",
    "locationLabel"
  ];

  var SESSION_KEYS = ["safetyBranch"];

  var COMFORTABLE_BASE_MINUTES = {
    "10": 10,
    "20": 15,
    "30": 20,
    "45": 25,
    "60-plus": 30
  };

  var COMFORTABLE_CEILING_MINUTES = {
    "10": 10,
    "20": 20,
    "30": 30,
    "45": 45,
    "60-plus": 60
  };

  var AVAILABLE_CAP_MINUTES = {
    "15": 15,
    "20": 20,
    "30": 30,
    "45-plus": 45
  };

  var HILL_RANK = {
    level: 0,
    gentle: 1,
    rolling: 2
  };

  var UNKNOWN_FIELDS = [
    "current access",
    "closures",
    "weather",
    "surface condition",
    "live trail or safety data"
  ];

  var DEMO_ROUTES = [
    {
      id: "neighborhood-greenway",
      name: "Neighborhood greenway",
      minMinutes: 10,
      typicalMinutes: 20,
      hills: "level",
      surface: "paved",
      shape: "out-back",
      earlyTurnaround: true,
      priorities: ["familiar", "facilities"],
      terrainLabel: "Mostly level paved path",
      summary: "A familiar out-and-back on a public neighborhood greenway."
    },
    {
      id: "park-perimeter-loop",
      name: "Park perimeter loop",
      minMinutes: 15,
      typicalMinutes: 25,
      hills: "gentle",
      surface: "compacted",
      shape: "short-loop",
      earlyTurnaround: true,
      priorities: ["quiet", "shade"],
      terrainLabel: "Compacted park path with gentle rises",
      summary: "A short public park loop with easy exits."
    },
    {
      id: "shaded-creek-path",
      name: "Shaded creek path",
      minMinutes: 20,
      typicalMinutes: 30,
      hills: "rolling",
      surface: "natural",
      shape: "out-back",
      earlyTurnaround: true,
      priorities: ["shade", "scenery"],
      terrainLabel: "Natural path with gentle-to-rolling rises",
      summary: "A shaded public path beside a creek, still reversible."
    }
  ];

  var ANSWER_USES = {
    goal: ["resultHeadline", "coachingNote", "learnFocus", "routeTone"],
    comfortableMinutes: ["durationMinutes", "profileLabel", "loadBand", "week3Change"],
    recentActivity: ["durationMinutes", "profileLabel", "ordinaryWalkNote"],
    loadedExperience: ["loadBand", "loadLabel"],
    availableMinutes: ["durationMinutes"],
    weeklyRhythm: ["weeklyLayout", "reflectionCopy"],
    safetyBranch: ["allowLoadedSession", "loadBand", "terrainLabel", "profileLabel"],
    packType: ["loadBand", "packGuidance", "checklist"],
    loadType: ["loadBand", "week3Change", "packGuidance"],
    loadSecured: ["loadBand", "week3Change"],
    shoes: ["footwearNote", "checklist"],
    socks: ["socksNote", "checklist"],
    surface: ["routeRanking", "footwearNote"],
    hills: ["terrainLabel", "routeHardFilter"],
    routeShape: ["routeRanking"],
    routePriority: ["routeTiebreaker"],
    locationLabel: ["locationCopy"]
  };

  function createEmptyAnswers() {
    return {
      goal: null,
      comfortableMinutes: null,
      recentActivity: null,
      loadedExperience: null,
      availableMinutes: null,
      weeklyRhythm: null,
      packType: null,
      loadType: null,
      loadSecured: false,
      shoes: null,
      socks: null,
      surface: null,
      hills: null,
      routeShape: null,
      routePriority: null,
      locationLabel: ""
    };
  }

  function createEmptyState() {
    return {
      version: STATE_VERSION,
      currentScreen: "welcome",
      answers: createEmptyAnswers(),
      sessionOnly: {
        safetyBranch: null
      },
      result: null,
      selectedDemoRouteId: null
    };
  }

  function clone(value) {
    return JSON.parse(JSON.stringify(value));
  }

  function isBlank(value) {
    return value == null || String(value).trim() === "";
  }

  function hasSafetyRestriction(safetyBranch) {
    return safetyBranch === "check-first" || safetyBranch === "walking-pain" || safetyBranch === "prefer-not";
  }

  function comfortableCeiling(comfortableMinutes) {
    return COMFORTABLE_CEILING_MINUTES[comfortableMinutes] || 20;
  }

  function calculateDuration(answers, safetyBranch) {
    var base = COMFORTABLE_BASE_MINUTES[answers.comfortableMinutes];
    if (base == null) {
      base = 15;
    }

    var available = AVAILABLE_CAP_MINUTES[answers.availableMinutes];
    if (available != null) {
      base = Math.min(base, available);
    }

    if (answers.recentActivity === "rarely") {
      base = Math.min(base, 20);
    }

    if (hasSafetyRestriction(safetyBranch)) {
      base = Math.min(base, 20);
    }

    return Math.max(10, Math.min(30, base));
  }

  function loadCeilingExceeded(label) {
    return /(?:^|[^\d])(?:[6-9]|\d{2,})\s*lb/i.test(String(label || ""));
  }

  function calculateLoad(answers, safetyBranch) {
    var restricted = hasSafetyRestriction(safetyBranch);
    var missingPack = answers.packType === "none";
    var unsecured = answers.loadSecured !== true && answers.loadType && answers.loadType !== "empty";
    var emptyLoad = answers.loadType === "empty" || !answers.loadType;
    var tenMinutes = answers.comfortableMinutes === "10";

    if (restricted || missingPack || unsecured || tenMinutes || answers.loadType === "empty") {
      return {
        band: "none",
        maxAddedLb: 0,
        label: "No added load",
        emptyValid: true
      };
    }

    if (answers.loadedExperience === "never") {
      return {
        band: "empty-ok",
        maxAddedLb: 5,
        label: "0–5 lb; beginning empty is valid",
        emptyValid: true
      };
    }

    if (answers.loadedExperience === "daily-bag" && comfortableCeiling(answers.comfortableMinutes) >= 20) {
      return {
        band: "up-to-5",
        maxAddedLb: 5,
        label: "Up to 5 lb",
        emptyValid: true
      };
    }

    if (answers.loadedExperience === "few-times" || answers.loadedExperience === "regular") {
      return {
        band: "up-to-5-assessed",
        maxAddedLb: 5,
        label: "Up to 5 lb for this first assessed session",
        emptyValid: true
      };
    }

    if (emptyLoad) {
      return {
        band: "none",
        maxAddedLb: 0,
        label: "No added load",
        emptyValid: true
      };
    }

    return {
      band: "empty-ok",
      maxAddedLb: 5,
      label: "0–5 lb; beginning empty is valid",
      emptyValid: true
    };
  }

  function calculateTerrain(answers, safetyBranch) {
    if (hasSafetyRestriction(safetyBranch)) {
      return {
        id: "familiar-level",
        label: "Familiar, mostly level, predictable surface",
        hillCeiling: "level",
        noSteep: true
      };
    }

    if (answers.hills === "level") {
      return {
        id: "level",
        label: "Mostly level",
        hillCeiling: "level",
        noSteep: true
      };
    }

    if (answers.hills === "gentle") {
      return {
        id: "gentle",
        label: "Gentle rises only",
        hillCeiling: "gentle",
        noSteep: true
      };
    }

    if (answers.hills === "rolling") {
      return {
        id: "rolling",
        label: "Gentle-to-rolling, with no steep first route",
        hillCeiling: "rolling",
        noSteep: true
      };
    }

    return {
      id: "level",
      label: "Mostly level until a route character is chosen",
      hillCeiling: "level",
      noSteep: true
    };
  }

  function calculateProfileLabel(answers, safetyBranch) {
    if (hasSafetyRestriction(safetyBranch)) {
      return "Fresh start";
    }

    var comfortable = answers.comfortableMinutes;
    var activity = answers.recentActivity;
    var shortWalk = comfortable === "10" || comfortable === "20";
    var rare = activity === "rarely";

    if (shortWalk || rare) {
      return "Fresh start";
    }

    var longWalk = comfortable === "60-plus";
    var regularActivity = activity === "3-4" || activity === "5-plus";
    if (longWalk && regularActivity && safetyBranch === "clear") {
      return "Ready to learn the load";
    }

    return "Steady beginner";
  }

  function week3Change(answers, durationMinutes, load, safetyBranch) {
    var restricted = hasSafetyRestriction(safetyBranch);
    var loadEmpty = load.band === "none" || load.maxAddedLb === 0;
    var securable = answers.loadType === "water" || answers.loadType === "household" || answers.loadType === "purpose-weight";
    var canAddLoad = !restricted && loadEmpty && securable && answers.packType && answers.packType !== "none";

    if (canAddLoad) {
      return {
        kind: "add-load",
        minutesDelta: 0,
        loadDeltaLb: "1–2",
        label: "Add 1–2 lb",
        reason: "Your first session stays empty so you can learn the setup. If that felt comfortable, Week 3 can introduce a small secured load — not more time."
      };
    }

    var comfortable = comfortableCeiling(answers.comfortableMinutes);
    var available = AVAILABLE_CAP_MINUTES[answers.availableMinutes] || 30;
    var nextDuration = durationMinutes + 5;
    var withinComfort = nextDuration <= comfortable && nextDuration <= available;

    if (withinComfort) {
      return {
        kind: "add-time",
        minutesDelta: 5,
        loadDeltaLb: null,
        label: "Add 5 minutes",
        reason: "Time is the single change. Load stays the same if the previous sessions felt comfortable."
      };
    }

    return {
      kind: "repeat",
      minutesDelta: 0,
      loadDeltaLb: null,
      label: "Repeat the baseline",
      reason: "There is no conservative single change that still fits your walking comfort and available time."
    };
  }

  function goalCopy(goal) {
    var map = {
      "everyday-fitness": {
        headline: "A first month that builds everyday fitness, without rushing the load.",
        coaching: "Consistency matters more than intensity. Keep the pack light and the walking form ordinary.",
        learn: "Learn how a small, secure load changes an ordinary walk.",
        routeTone: "Choose a route you could repeat on a weekday without special planning."
      },
      "outdoor-time": {
        headline: "A first month that gets you outside on purpose.",
        coaching: "Protect a realistic outing, then let the pack stay secondary to showing up.",
        learn: "Learn a setup you can take to a park, greenway, or neighborhood loop.",
        routeTone: "Favor a public outdoor path you will actually use."
      },
      "clear-head": {
        headline: "A first month that leaves room to think.",
        coaching: "Keep a conversational pace. The session should clear your head, not empty it.",
        learn: "Learn a calm walking rhythm with a pack that stays quiet on your back.",
        routeTone: "Prefer a simple, low-navigation path so attention can stay on the walk."
      },
      "event-foundation": {
        headline: "A conservative foundation, not a rehearsal for a hard event.",
        coaching: "Build the setup and the habit first. Event mileage and load come later, one change at a time.",
        learn: "Learn the pack, feet, and turnaround habit that later training can grow from.",
        routeTone: "Start on forgiving public ground, not a course that imitates a challenge."
      }
    };
    return map[goal] || map["everyday-fitness"];
  }

  function weeklyLayout(weeklyRhythm) {
    var map = {
      one: {
        id: "one",
        label: "One focused loaded day each week",
        extraDays: []
      },
      two: {
        id: "two",
        label: "One loaded day, plus one ordinary walk",
        extraDays: [{ kind: "ordinary-walk", label: "Ordinary walk" }]
      },
      three: {
        id: "three",
        label: "One loaded day, mixed with ordinary walking",
        extraDays: [
          { kind: "ordinary-walk", label: "Ordinary walk" },
          { kind: "optional-strength", label: "Optional strength or mobility" }
        ]
      },
      flexible: {
        id: "flexible",
        label: "A weekend-first loaded day, with flexible ordinary walking",
        extraDays: [{ kind: "recovery", label: "Flexible ordinary walk or recovery" }]
      }
    };
    return map[weeklyRhythm] || map.one;
  }

  function packGuidance(answers) {
    var pack = answers.packType;
    if (pack === "none") {
      return "Borrow or use a sturdy two-strap backpack that closes securely. A specialized ruck is not required to test the activity.";
    }
    if (pack === "vest") {
      return "A weighted vest is not a backpack. Keep the first session unloaded or very light, check that it sits evenly, and skip this option if it changes your walking form.";
    }
    if (pack === "ruck") {
      return "Use the same beginner rules as any pack: load low enough to learn, keep it close to your back, and adjust both shoulder straps before you go farther.";
    }
    if (pack === "daypack") {
      return "A hiking daypack is plenty. Keep the load compact, centered, and quiet against your back.";
    }
    return "A regular backpack is enough if it is sturdy, uses two comfortable straps, closes securely, and can keep a small load from shifting.";
  }

  function footwearNote(answers) {
    var shoes = answers.shoes;
    var surface = answers.surface;
    var note = "Wear shoes that already feel comfortable on this surface.";
    if (shoes === "unsure") {
      note = "Choose supportive walking or athletic shoes you already walk in. New boots are not required.";
    } else if (shoes === "boots") {
      note = "Boots are fine if they already fit. Do not make the first ruck the place to break in a stiff pair.";
    } else if (shoes === "trail" && (surface === "paved" || surface === "compacted")) {
      note = "Trail shoes can work on pavement, but a comfortable walking or running shoe is enough on predictable surfaces.";
    } else if ((shoes === "comfortable-walking" || shoes === "running") && (surface === "natural" || surface === "mixed")) {
      note = "If the path is wet, loose, or uneven, you may want sturdier rubber-soled footwear next time. For a first familiar route, stay on ground these shoes already handle.";
    }

    var socks = "";
    if (answers.socks === "cotton") {
      socks = " Cotton socks can hold moisture and raise blister risk on longer or warmer walks. They do not stop this plan; switch to synthetic or wool socks before you lengthen the session.";
    } else if (answers.socks === "unsure") {
      socks = " Moisture-wicking synthetic or wool socks are the low-cost upgrade if friction or dampness shows up.";
    } else if (answers.socks === "synthetic-wool") {
      socks = " Synthetic or wool socks are a sound choice for moisture and friction.";
    }

    return note + socks;
  }

  function distanceRange(durationMinutes) {
    var low = Math.max(0.4, Math.round((durationMinutes / 24) * 10) / 10);
    var high = Math.max(low + 0.2, Math.round((durationMinutes / 16) * 10) / 10);
    return {
      lowMiles: low,
      highMiles: high,
      label: low + "–" + high + " miles"
    };
  }

  function locationCopy(locationLabel) {
    var trimmed = String(locationLabel || "").trim();
    if (!trimmed) {
      return "Near your chosen starting area";
    }
    return "Near " + trimmed;
  }

  function routeReasons(route, answers, terrain) {
    var reasons = [];
    if (route.surface === answers.surface || answers.surface === "mixed" || answers.surface === "not-sure") {
      if (answers.surface === "paved" || answers.surface === "compacted" || answers.surface === "natural") {
        reasons.push("Matches the surface you already walk comfortably.");
      } else {
        reasons.push("Stays on public, reversible ground while live route data is still offline.");
      }
    }
    if (answers.routeShape === "either" || route.shape === answers.routeShape) {
      reasons.push(route.shape === "short-loop"
        ? "A short loop returns you to the start without extra navigation."
        : "An out-and-back lets you turn around as soon as the session has done its job.");
    }
    if (route.earlyTurnaround) {
      reasons.push("Early turnaround is available if anything feels off.");
    }
    if (HILL_RANK[route.hills] <= HILL_RANK[terrain.hillCeiling]) {
      reasons.push("Stays inside your hill ceiling.");
    }
    if (answers.routePriority && route.priorities.indexOf(answers.routePriority) !== -1) {
      reasons.push("Also leans toward the extra you said would help you go.");
    }
    var unique = [];
    reasons.forEach(function (reason) {
      if (unique.indexOf(reason) === -1) {
        unique.push(reason);
      }
    });
    return unique.slice(0, 2);
  }

  function scoreRoutes(answers, durationMinutes, terrain) {
    var hillCeiling = terrain.hillCeiling || "level";
    var scored = [];
    var filteredOut = [];

    DEMO_ROUTES.forEach(function (route) {
      var hard = [];
      if (HILL_RANK[route.hills] > HILL_RANK[hillCeiling]) {
        hard.push("hills");
      }
      if (route.minMinutes > durationMinutes) {
        hard.push("duration");
      }
      if (hard.length) {
        filteredOut.push({ id: route.id, reasons: hard });
        return;
      }

      var score = 0;
      if (answers.surface === "not-sure" || answers.surface === "mixed") {
        score += 2;
      } else if (route.surface === answers.surface) {
        score += 6;
      } else if (answers.surface === "paved" && route.surface === "compacted") {
        score += 2;
      }

      if (answers.routeShape === "either" || route.shape === answers.routeShape) {
        score += 5;
      }

      scored.push({
        route: route,
        score: score,
        priorityMatch: answers.routePriority && route.priorities.indexOf(answers.routePriority) !== -1
      });
    });

    scored.sort(function (a, b) {
      if (b.score !== a.score) {
        return b.score - a.score;
      }
      if (a.priorityMatch !== b.priorityMatch) {
        return a.priorityMatch ? -1 : 1;
      }
      return a.route.name.localeCompare(b.route.name);
    });

    if (!scored.length) {
      var fallback = DEMO_ROUTES[0];
      scored.push({ route: fallback, score: 0, priorityMatch: false, fallback: true });
    }

    var badges = ["Best fit", "Good alternative", "Save for later"];
    return scored.map(function (entry, index) {
      var range = distanceRange(durationMinutes);
      var reasons = routeReasons(entry.route, answers, terrain);
      if (entry.fallback) {
        reasons = ["Closest conservative demonstration option after the hard filters."];
      }
      if (reasons.length < 2) {
        reasons.push("Public, reversible, and labeled as demonstration data only.");
      }
      return {
        id: entry.route.id,
        name: entry.route.name,
        badge: badges[Math.min(index, badges.length - 1)],
        demonstration: true,
        demonstrationLabel: "Demonstration route",
        distanceLabel: range.label,
        terrain: entry.route.terrainLabel,
        shape: entry.route.shape === "short-loop" ? "Short loop" : "Out and back",
        earlyTurnaround: entry.route.earlyTurnaround,
        reasons: reasons.slice(0, 2),
        unknowns: UNKNOWN_FIELDS.slice(),
        summary: entry.route.summary,
        score: entry.score,
        usedPriorityAsTiebreaker: index > 0 ? false : entry.priorityMatch
      };
    });
  }

  function pauseCopy(safetyBranch) {
    if (safetyBranch === "check-first") {
      return "Pause before adding load. Consider medical clearance before beginning.";
    }
    if (safetyBranch === "walking-pain") {
      return "Keep this as an unloaded walk on a familiar route and obtain individualized professional advice before adding load.";
    }
    if (safetyBranch === "prefer-not") {
      return "This plan stays conservative and unloaded because a medical history was not collected.";
    }
    return null;
  }

  function buildPlan(state) {
    var answers = state.answers || createEmptyAnswers();
    var safetyBranch = (state.sessionOnly && state.sessionOnly.safetyBranch) || null;
    var durationMinutes = calculateDuration(answers, safetyBranch);
    var load = calculateLoad(answers, safetyBranch);
    var terrain = calculateTerrain(answers, safetyBranch);
    var profileLabel = calculateProfileLabel(answers, safetyBranch);
    var week3 = week3Change(answers, durationMinutes, load, safetyBranch);
    var goal = goalCopy(answers.goal);
    var week = weeklyLayout(answers.weeklyRhythm);
    var routes = scoreRoutes(answers, durationMinutes, terrain);
    var selected = routes.find(function (route) {
      return route.id === state.selectedDemoRouteId;
    }) || routes[0];

    var reasons = [];
    reasons.push("Time stays at " + durationMinutes + " minutes because that is inside both your comfortable walk and the time you can protect.");
    if (answers.recentActivity === "rarely") {
      reasons.push("Recent activity is infrequent, so the first session stays at or under 20 minutes.");
    }
    if (load.maxAddedLb === 0) {
      reasons.push("Added load stays at zero until the pack, the load, or the safety boundary says a light start is appropriate.");
    } else {
      reasons.push("Added load never exceeds 5 lb in this prototype, and beginning empty remains valid.");
    }
    reasons.push("Terrain stays at " + terrain.label.toLowerCase() + " because the first route should be reversible, not a test of hills.");

    if (reasons.length > 3) {
      reasons = reasons.slice(0, 3);
    }

    return {
      profileLabel: profileLabel,
      durationMinutes: durationMinutes,
      load: load,
      terrain: terrain,
      weeklyLayout: week,
      goal: goal,
      locationCopy: locationCopy(answers.locationLabel),
      packGuidance: packGuidance(answers),
      footwearNote: footwearNote(answers),
      pauseCopy: pauseCopy(safetyBranch),
      allowLoadedSession: safetyBranch === "clear",
      week3: week3,
      reasons: reasons,
      routes: routes,
      selectedRoute: selected,
      ordinaryWalkNote: answers.recentActivity === "rarely"
        ? "Optional ordinary walks between loaded days stay short and familiar."
        : "Ordinary walks between loaded days are optional and do not add pack weight.",
      expertReviewNote: "Future plans respond to how the previous session felt. The calendar alone never forces an increase."
    };
  }

  function validateScreen(screenId, state) {
    var answers = state.answers || createEmptyAnswers();
    var safety = state.sessionOnly && state.sessionOnly.safetyBranch;
    var required = {
      goal: ["goal"],
      "comfortable-walk": ["comfortableMinutes"],
      "recent-activity": ["recentActivity"],
      "loaded-experience": ["loadedExperience"],
      "available-time": ["availableMinutes"],
      "weekly-rhythm": ["weeklyRhythm"],
      "safety-gate": [],
      "pack-type": ["packType"],
      "load-available": ["loadType"],
      "shoes-socks": ["shoes", "socks"],
      surface: ["surface"],
      hills: ["hills"],
      "route-shape": ["routeShape"],
      "route-priority": ["routePriority"],
      "demo-routes": []
    }[screenId];

    if (screenId === "safety-gate") {
      if (isBlank(safety)) {
        return { ok: false, message: "Choose one answer to continue.", field: "safetyBranch" };
      }
      return { ok: true };
    }

    if (screenId === "starting-area") {
      return { ok: true };
    }

    if (screenId === "demo-routes") {
      if (isBlank(state.selectedDemoRouteId)) {
        return { ok: false, message: "Choose a demonstration route for this walkthrough.", field: "selectedDemoRouteId" };
      }
      return { ok: true };
    }

    if (!required) {
      return { ok: true };
    }

    for (var i = 0; i < required.length; i += 1) {
      var key = required[i];
      if (isBlank(answers[key])) {
        return { ok: false, message: "Choose one answer to continue.", field: key };
      }
    }
    return { ok: true };
  }

  function screenIndex(id) {
    return SCREEN_IDS.indexOf(id);
  }

  function nextScreenId(currentId) {
    var index = screenIndex(currentId);
    if (index < 0 || index >= SCREEN_IDS.length - 1) {
      return currentId;
    }
    return SCREEN_IDS[index + 1];
  }

  function previousScreenId(currentId) {
    var index = screenIndex(currentId);
    if (index <= 0) {
      return currentId;
    }
    return SCREEN_IDS[index - 1];
  }

  function migrateState(raw) {
    var state = createEmptyState();
    if (!raw || typeof raw !== "object") {
      return state;
    }
    if (SCREEN_IDS.indexOf(raw.currentScreen) !== -1) {
      state.currentScreen = raw.currentScreen;
    }
    if (raw.answers && typeof raw.answers === "object") {
      ANSWER_KEYS.forEach(function (key) {
        if (Object.prototype.hasOwnProperty.call(raw.answers, key)) {
          state.answers[key] = raw.answers[key];
        }
      });
      if (typeof state.answers.loadSecured !== "boolean") {
        state.answers.loadSecured = !!state.answers.loadSecured;
      }
      if (typeof state.answers.locationLabel !== "string") {
        state.answers.locationLabel = state.answers.locationLabel == null ? "" : String(state.answers.locationLabel);
      }
    }
    if (raw.selectedDemoRouteId) {
      state.selectedDemoRouteId = String(raw.selectedDemoRouteId);
    }
    state.version = STATE_VERSION;
    return state;
  }

  function persistentSnapshot(state) {
    return {
      version: STATE_VERSION,
      currentScreen: state.currentScreen,
      answers: clone(state.answers),
      selectedDemoRouteId: state.selectedDemoRouteId
    };
  }

  function allAnswersReferenced() {
    var missing = [];
    ANSWER_KEYS.concat(SESSION_KEYS).forEach(function (key) {
      if (!ANSWER_USES[key] || !ANSWER_USES[key].length) {
        missing.push(key);
      }
    });
    return missing;
  }

  return {
    STORAGE_PERSISTENT: STORAGE_PERSISTENT,
    STORAGE_SESSION: STORAGE_SESSION,
    STATE_VERSION: STATE_VERSION,
    SCREEN_IDS: SCREEN_IDS,
    ANSWER_KEYS: ANSWER_KEYS,
    ANSWER_USES: ANSWER_USES,
    DEMO_ROUTES: DEMO_ROUTES,
    UNKNOWN_FIELDS: UNKNOWN_FIELDS,
    createEmptyState: createEmptyState,
    createEmptyAnswers: createEmptyAnswers,
    clone: clone,
    calculateDuration: calculateDuration,
    calculateLoad: calculateLoad,
    calculateTerrain: calculateTerrain,
    calculateProfileLabel: calculateProfileLabel,
    week3Change: week3Change,
    scoreRoutes: scoreRoutes,
    buildPlan: buildPlan,
    validateScreen: validateScreen,
    nextScreenId: nextScreenId,
    previousScreenId: previousScreenId,
    screenIndex: screenIndex,
    migrateState: migrateState,
    persistentSnapshot: persistentSnapshot,
    hasSafetyRestriction: hasSafetyRestriction,
    locationCopy: locationCopy,
    distanceRange: distanceRange,
    allAnswersReferenced: allAnswersReferenced,
    loadCeilingExceeded: loadCeilingExceeded
  };
});
