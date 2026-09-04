"use strict";

const { describe, it } = require("node:test");
const assert = require("node:assert/strict");
const fs = require("node:fs");
const path = require("node:path");

const Model = require("../js/model.js");
const Screens = require("../js/screens.js");

const labRoot = path.join(__dirname, "..");

function answers(overrides) {
  return Object.assign(Model.createEmptyAnswers(), {
    goal: "everyday-fitness",
    comfortableMinutes: "30",
    recentActivity: "1-2",
    loadedExperience: "few-times",
    availableMinutes: "30",
    weeklyRhythm: "one",
    packType: "daypack",
    loadType: "water",
    loadSecured: true,
    shoes: "comfortable-walking",
    socks: "synthetic-wool",
    surface: "paved",
    hills: "gentle",
    routeShape: "out-back",
    routePriority: "familiar",
    locationLabel: "Santa Barbara"
  }, overrides);
}

function stateFrom(overrides, safety) {
  const state = Model.createEmptyState();
  state.answers = answers(overrides);
  state.sessionOnly.safetyBranch = safety == null ? "clear" : safety;
  return state;
}

describe("duration", function () {
  it("keeps a 10-minute comfortable walker at 10 minutes", function () {
    const minutes = Model.calculateDuration(answers({
      comfortableMinutes: "10",
      availableMinutes: "45-plus",
      recentActivity: "5-plus"
    }), "clear");
    assert.equal(minutes, 10);
    assert.ok(minutes <= 10);
  });

  it("caps rarely-active walkers at 20 minutes", function () {
    const minutes = Model.calculateDuration(answers({
      comfortableMinutes: "60-plus",
      availableMinutes: "45-plus",
      recentActivity: "rarely"
    }), "clear");
    assert.equal(minutes, 20);
  });

  it("caps duration by available time", function () {
    const minutes = Model.calculateDuration(answers({
      comfortableMinutes: "60-plus",
      availableMinutes: "15",
      recentActivity: "5-plus"
    }), "clear");
    assert.equal(minutes, 15);
  });
});

describe("load and safety", function () {
  it("makes every safety branch other than clear unloaded", function () {
    ["check-first", "walking-pain", "prefer-not"].forEach(function (branch) {
      const load = Model.calculateLoad(answers(), branch);
      assert.equal(load.maxAddedLb, 0, branch);
      assert.equal(load.band, "none", branch);
    });
  });

  it("never recommends more than 5 lb of added load", function () {
    const experiences = ["never", "daily-bag", "few-times", "regular"];
    experiences.forEach(function (loadedExperience) {
      const load = Model.calculateLoad(answers({ loadedExperience: loadedExperience, comfortableMinutes: "60-plus" }), "clear");
      assert.ok(load.maxAddedLb <= 5);
      assert.equal(Model.loadCeilingExceeded(load.label), false);
    });
  });

  it("recommends no added load for an unsecured household load", function () {
    const load = Model.calculateLoad(answers({
      loadType: "household",
      loadSecured: false
    }), "clear");
    assert.equal(load.maxAddedLb, 0);
  });
});

describe("week 3", function () {
  it("never changes both duration and load", function () {
    const variants = [
      answers({ loadType: "household", loadSecured: false, packType: "daypack" }),
      answers({ loadType: "empty", packType: "daypack", comfortableMinutes: "45", availableMinutes: "45-plus" }),
      answers({ comfortableMinutes: "10", availableMinutes: "15", loadType: "empty" })
    ];
    variants.forEach(function (set) {
      const duration = Model.calculateDuration(set, "clear");
      const load = Model.calculateLoad(set, "clear");
      const change = Model.week3Change(set, duration, load, "clear");
      const changedTime = change.minutesDelta > 0;
      const changedLoad = !!change.loadDeltaLb;
      assert.equal(changedTime && changedLoad, false, change.kind);
    });
  });
});

describe("routes", function () {
  it("runs hard hill and duration filters before preference scoring", function () {
    const ranked = Model.scoreRoutes(answers({
      hills: "level",
      surface: "natural",
      routePriority: "scenery",
      comfortableMinutes: "10"
    }), 10, { hillCeiling: "level" });
    const ids = ranked.map(function (route) { return route.id; });
    assert.equal(ids.indexOf("shaded-creek-path"), -1);
  });

  it("does not let route priority override a hill or duration filter", function () {
    const ranked = Model.scoreRoutes(answers({
      hills: "level",
      routePriority: "scenery",
      surface: "natural",
      routeShape: "out-back"
    }), 15, { hillCeiling: "level" });
    ranked.forEach(function (route) {
      if (route.id === "shaded-creek-path") {
        assert.fail("rolling creek path should stay filtered");
      }
    });
    assert.ok(ranked[0].id !== "shaded-creek-path");
  });

  it("labels every route as demonstration data with unknowns", function () {
    const ranked = Model.scoreRoutes(answers(), 20, { hillCeiling: "rolling" });
    assert.ok(ranked.length >= 1);
    ranked.forEach(function (route) {
      assert.equal(route.demonstration, true);
      assert.equal(route.demonstrationLabel, "Demonstration route");
      assert.ok(route.unknowns.indexOf("current access") !== -1);
      assert.ok(route.unknowns.indexOf("closures") !== -1);
      assert.ok(route.unknowns.indexOf("weather") !== -1);
      assert.ok(route.unknowns.indexOf("surface condition") !== -1);
    });
  });
});

describe("answer usage and flow", function () {
  it("references every collected answer in a result or branch rule", function () {
    assert.deepEqual(Model.allAnswersReferenced(), []);
  });

  it("can reach all 25 screens on a valid path", function () {
    assert.equal(Model.SCREEN_IDS.length, 25);
    assert.equal(Screens.SCREENS.length, 25);
    let id = "welcome";
    const seen = [id];
    while (id !== "leave-ready") {
      id = Model.nextScreenId(id);
      seen.push(id);
    }
    assert.deepEqual(seen, Model.SCREEN_IDS);
  });

  it("preserves non-sensitive answers when moving back", function () {
    const current = stateFrom({ goal: "clear-head" });
    current.currentScreen = "comfortable-walk";
    const previous = Model.previousScreenId(current.currentScreen);
    assert.equal(previous, "goal");
    assert.equal(current.answers.goal, "clear-head");
  });

  it("resumes persistent walkthrough state after a simulated refresh", function () {
    const current = stateFrom({ locationLabel: "Santa Barbara" });
    current.currentScreen = "pack-type";
    current.selectedDemoRouteId = "neighborhood-greenway";
    const resumed = Model.migrateState(Model.persistentSnapshot(current));
    assert.equal(resumed.currentScreen, "pack-type");
    assert.equal(resumed.answers.locationLabel, "Santa Barbara");
    assert.equal(resumed.selectedDemoRouteId, "neighborhood-greenway");
  });

  it("omits the safety response from persistent storage", function () {
    const current = stateFrom({}, "check-first");
    const snapshot = Model.persistentSnapshot(current);
    assert.equal(Object.prototype.hasOwnProperty.call(snapshot, "sessionOnly"), false);
    assert.equal(JSON.stringify(snapshot).indexOf("check-first"), -1);
    const resumed = Model.migrateState(snapshot);
    assert.equal(resumed.sessionOnly.safetyBranch, null);
  });

  it("blocks advancing without required input", function () {
    const empty = Model.createEmptyState();
    empty.currentScreen = "goal";
    const blocked = Model.validateScreen("goal", empty);
    assert.equal(blocked.ok, false);
    empty.answers.goal = "everyday-fitness";
    assert.equal(Model.validateScreen("goal", empty).ok, true);
  });
});

describe("plan integrity", function () {
  it("uses the typed location label in result copy", function () {
    const result = Model.buildPlan(stateFrom({ locationLabel: "Santa Barbara" }));
    assert.equal(result.locationCopy, "Near Santa Barbara");
    const deferred = Model.buildPlan(stateFrom({ locationLabel: "" }));
    assert.equal(deferred.locationCopy, "Near your chosen starting area");
  });

  it("does not describe a vest as a backpack", function () {
    const result = Model.buildPlan(stateFrom({ packType: "vest" }));
    assert.match(result.packGuidance, /not a backpack/i);
  });

  it("keeps cotton socks from failing the plan", function () {
    const result = Model.buildPlan(stateFrom({ socks: "cotton" }));
    assert.ok(result.durationMinutes >= 10);
    assert.match(result.footwearNote, /do not stop this plan/i);
  });
});

describe("lab isolation", function () {
  const files = [
    "index.html",
    "css/app.css",
    "js/model.js",
    "js/screens.js",
    "js/app.js",
    "README.md",
    "assets/README.md"
  ].map(function (file) {
    return fs.readFileSync(path.join(labRoot, file), "utf8");
  }).join("\n");

  it("uses only relative local asset paths", function () {
    assert.match(files, /assets\/brand\/logo\/firstruck-mark\.svg/);
    assert.equal(files.indexOf("http://"), -1);
    assert.equal(files.indexOf("https://"), -1);
    assert.equal(files.indexOf("file:///"), -1);
  });

  it("does not copy STOPPR assets, branding, or wording", function () {
    assert.equal(/stoppr/i.test(files), false);
    assert.equal(/quit sugar/i.test(files), false);
    assert.equal(/250,000/i.test(files), false);
    assert.equal(/calorie/i.test(files), false);
  });

  it("keeps native choice controls and visible headings in the catalog", function () {
    const choiceScreens = Screens.SCREENS.filter(function (screen) {
      return screen.options || screen.groups;
    });
    assert.ok(choiceScreens.length >= 12);
    Screens.SCREENS.forEach(function (screen) {
      const hasHeading = !!(screen.headline || screen.prompt || screen.eyebrow);
      assert.equal(hasHeading, true, screen.id);
    });
  });

  it("lets Reduce Motion skip timed analysis", function () {
    const analysis = Screens.getScreen("analysis");
    assert.equal(analysis.autoAdvance, false);
    assert.equal(analysis.action, "Continue");
  });
});
