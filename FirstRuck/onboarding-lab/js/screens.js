(function (root, factory) {
  if (typeof module === "object" && module.exports) {
    module.exports = factory();
  } else {
    root.FirstRuckScreens = factory();
  }
})(typeof globalThis !== "undefined" ? globalThis : this, function () {
  "use strict";

  var SCREENS = [
    {
      id: "welcome",
      number: 1,
      chapter: "promise",
      chapterLabel: "Promise",
      title: "Welcome",
      purpose: "Establish the product and emotional tone.",
      uses: "Approved hero photograph, First Ruck mark, and original route motion.",
      changes: "Starts the walkthrough. No answers yet.",
      kind: "welcome",
      theme: "forest",
      status: "light",
      autoAdvance: false,
      eyebrow: "Your first ruck starts here",
      headline: "Start where you are. Carry forward.",
      body: "Build a first session around your walking comfort, the gear you already own, and a route you can change your mind on.",
      action: "Build my first ruck",
      footnote: "About 4 minutes · No account required"
    },
    {
      id: "what-rucking-is",
      number: 2,
      chapter: "promise",
      chapterLabel: "Promise",
      title: "What rucking is",
      purpose: "Orient a complete beginner without military framing.",
      uses: "An original walk-to-pack silhouette.",
      changes: "Sets the teaching frame: walk, start light, change one thing.",
      kind: "edu-beats",
      theme: "forest",
      status: "light",
      autoAdvance: false,
      headline: "It is walking, with a little more to carry.",
      body: "Load changes the effort. Your first session is for learning the pack, your pace, and how your body responds.",
      beats: [
        { title: "Walk, do not run", text: "Keep a pace where a short conversation stays possible." },
        { title: "Start light", text: "The first job is to feel the pack while you walk normally." },
        { title: "Change one thing at a time", text: "Time, load, and terrain do not all move together." }
      ],
      action: "That makes sense"
    },
    {
      id: "equipment-reassurance",
      number: 3,
      chapter: "promise",
      chapterLabel: "Promise",
      title: "Equipment reassurance",
      purpose: "Remove the belief that a specialized ruck is required.",
      uses: "equipment-flatlay.png with live HTML labels.",
      changes: "Frames later pack, load, and footwear questions.",
      kind: "equipment-flatlay",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      headline: "You may already have enough.",
      body: "A sturdy backpack, comfortable shoes, moisture-wicking socks, and a small secure load are enough to test your first ruck.",
      callout: "An empty backpack counts.",
      action: "Use what I have",
      items: [
        { id: "pack", label: "Sturdy backpack", focus: "18% 42%" },
        { id: "water", label: "Cushioned water", focus: "48% 38%" },
        { id: "shoes", label: "Walking shoes", focus: "72% 78%" },
        { id: "socks", label: "Wicking socks", focus: "78% 58%" },
        { id: "phone", label: "Phone", focus: "62% 62%" }
      ]
    },
    {
      id: "goal",
      number: 4,
      chapter: "starting-point",
      chapterLabel: "Starting point",
      title: "Goal",
      purpose: "Capture the first-month emphasis.",
      uses: "goal",
      changes: "Result headline, coaching note, Learn focus, and route tone.",
      kind: "choice",
      theme: "paper",
      status: "dark",
      autoAdvance: true,
      prompt: "What would make your first month worthwhile?",
      field: "goal",
      name: "goal",
      options: [
        { value: "everyday-fitness", label: "Build everyday fitness" },
        { value: "outdoor-time", label: "Spend more time outside" },
        { value: "clear-head", label: "Clear my head" },
        { value: "event-foundation", label: "Build a foundation for a future challenge" }
      ]
    },
    {
      id: "comfortable-walk",
      number: 5,
      chapter: "starting-point",
      chapterLabel: "Starting point",
      title: "Comfortable unloaded walk",
      purpose: "Set the duration ceiling from current walking comfort.",
      uses: "comfortableMinutes",
      changes: "Starter duration and route-distance target.",
      kind: "choice",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      prompt: "How long can you walk comfortably today?",
      help: "Choose a duration that does not noticeably change how you move or leave you unusually sore the next day.",
      field: "comfortableMinutes",
      name: "comfortable-minutes",
      options: [
        { value: "10", label: "About 10 minutes" },
        { value: "20", label: "About 20 minutes" },
        { value: "30", label: "About 30 minutes" },
        { value: "45", label: "About 45 minutes" },
        { value: "60-plus", label: "60 minutes or more" }
      ]
    },
    {
      id: "recent-activity",
      number: 6,
      chapter: "starting-point",
      chapterLabel: "Starting point",
      title: "Recent activity",
      purpose: "Keep the first plan conservative when activity has been infrequent.",
      uses: "recentActivity",
      changes: "Duration cap, profile label, and optional ordinary-walk note.",
      kind: "choice",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      prompt: "How often have you been active lately?",
      help: "Walking, workouts, sports, and active work all count.",
      field: "recentActivity",
      name: "recent-activity",
      options: [
        { value: "rarely", label: "Rarely" },
        { value: "1-2", label: "1–2 days each week" },
        { value: "3-4", label: "3–4 days each week" },
        { value: "5-plus", label: "5 or more days each week" }
      ]
    },
    {
      id: "loaded-experience",
      number: 7,
      chapter: "starting-point",
      chapterLabel: "Starting point",
      title: "Loaded walking experience",
      purpose: "Shape the first added-load recommendation.",
      uses: "loadedExperience",
      changes: "Starting-load copy, still capped at 5 lb.",
      kind: "choice",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      prompt: "Have you walked for exercise with weight before?",
      field: "loadedExperience",
      name: "loaded-experience",
      options: [
        { value: "never", label: "No, this is completely new" },
        { value: "daily-bag", label: "Only with an everyday bag" },
        { value: "few-times", label: "A few intentional loaded walks" },
        { value: "regular", label: "Yes, but I want a careful restart" }
      ]
    },
    {
      id: "available-time",
      number: 8,
      chapter: "starting-point",
      chapterLabel: "Starting point",
      title: "Available time",
      purpose: "Prevent a plan longer than the protected window.",
      uses: "availableMinutes",
      changes: "Final duration cap.",
      kind: "choice",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      prompt: "How much time can you comfortably protect for a session?",
      field: "availableMinutes",
      name: "available-minutes",
      options: [
        { value: "15", label: "15 minutes" },
        { value: "20", label: "20 minutes" },
        { value: "30", label: "30 minutes" },
        { value: "45-plus", label: "45 minutes or more" }
      ]
    },
    {
      id: "weekly-rhythm",
      number: 9,
      chapter: "starting-point",
      chapterLabel: "Starting point",
      title: "Weekly rhythm",
      purpose: "Lay out a calendar the person could actually keep.",
      uses: "weeklyRhythm and community-park-walk.png as atmosphere only.",
      changes: "Four-week extra days. Still only one loaded session each week.",
      kind: "choice",
      theme: "paper",
      status: "dark",
      autoAdvance: true,
      photo: "community",
      prompt: "Which rhythm could you actually keep?",
      field: "weeklyRhythm",
      name: "weekly-rhythm",
      options: [
        { value: "one", label: "One focused day each week" },
        { value: "two", label: "Two days, with recovery between" },
        { value: "three", label: "Three active days, mixing rucks and ordinary walks" },
        { value: "flexible", label: "A flexible weekend-first plan" }
      ]
    },
    {
      id: "safety-gate",
      number: 10,
      chapter: "starting-point",
      chapterLabel: "Starting point",
      title: "Exercise safety gate",
      purpose: "Establish boundaries without diagnosing or collecting a medical history.",
      uses: "sessionOnly.safetyBranch, kept out of persistent storage.",
      changes: "Can force an unloaded, familiar-route, or pause-before-load result.",
      kind: "safety",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      headline: "Before we add weight",
      body: "Loaded walking should not be the place to test concerning exercise symptoms.",
      field: "safetyBranch",
      name: "safety-branch",
      sessionOnly: true,
      options: [
        { value: "clear", label: "None of these apply to me right now" },
        { value: "check-first", label: "I have chest discomfort with effort, unusual breathlessness, dizziness, fainting, or another reason to check first" },
        { value: "walking-pain", label: "Pain, numbness, balance, or a recent injury already changes how I walk" },
        { value: "prefer-not", label: "I would rather not answer" }
      ]
    },
    {
      id: "reflection",
      number: 11,
      chapter: "starting-point",
      chapterLabel: "Starting point",
      title: "First reflection",
      purpose: "Prove the answers already have meaning and break question fatigue.",
      uses: "Duration, load preview, weekly rhythm, and safety branch.",
      changes: "No new answers. Previews Time, Load, and Terrain.",
      kind: "reflection",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      headline: "Your answers already shape the first session.",
      body: "Next, we will check the pack, your feet, and the kind of route that feels manageable.",
      action: "Continue"
    },
    {
      id: "pack-type",
      number: 12,
      chapter: "equipment",
      chapterLabel: "Equipment",
      title: "Pack type",
      purpose: "Tailor fit instructions to the thing they can actually carry.",
      uses: "packType",
      changes: "Pack guidance, shopping help, and whether a vest is treated as a backpack.",
      kind: "choice",
      theme: "equipment",
      status: "dark",
      autoAdvance: false,
      prompt: "What could you carry on your first ruck?",
      field: "packType",
      name: "pack-type",
      options: [
        { value: "school-backpack", label: "A regular backpack" },
        { value: "daypack", label: "A hiking daypack" },
        { value: "ruck", label: "A purpose-built rucksack" },
        { value: "vest", label: "A weighted vest" },
        { value: "none", label: "I need help choosing something" }
      ]
    },
    {
      id: "load-available",
      number: 13,
      chapter: "equipment",
      chapterLabel: "Equipment",
      title: "Load available",
      purpose: "Treat existing weight as a ceiling, not a target.",
      uses: "loadType and loadSecured",
      changes: "Unsecured load becomes an empty first session. Added load still never exceeds 5 lb.",
      kind: "load-choice",
      theme: "equipment",
      status: "dark",
      autoAdvance: false,
      prompt: "What safe, secure load do you already have?",
      help: "This is an equipment ceiling, not a target.",
      field: "loadType",
      name: "load-type",
      toggle: {
        field: "loadSecured",
        name: "load-secured",
        label: "I can keep it from shifting or poking me."
      },
      options: [
        { value: "empty", label: "Nothing yet or an empty pack" },
        { value: "water", label: "Water bottles or a hydration bladder" },
        { value: "household", label: "Books or household items I can cushion" },
        { value: "purpose-weight", label: "A purpose-built weight or plate" }
      ]
    },
    {
      id: "shoes-socks",
      number: 14,
      chapter: "equipment",
      chapterLabel: "Equipment",
      title: "Shoes and socks",
      purpose: "Match underfoot gear to surface without failing the plan.",
      uses: "shoes and socks",
      changes: "Checklist and surface warning. Cotton is a note, not a stop.",
      kind: "dual-choice",
      theme: "equipment",
      status: "dark",
      autoAdvance: false,
      prompt: "What will be underfoot?",
      groups: [
        {
          legend: "Shoes",
          field: "shoes",
          name: "shoes",
          options: [
            { value: "comfortable-walking", label: "Comfortable walking shoes" },
            { value: "running", label: "Running shoes" },
            { value: "trail", label: "Trail shoes" },
            { value: "boots", label: "Boots I already walk in" },
            { value: "unsure", label: "Not sure yet" }
          ]
        },
        {
          legend: "Socks",
          field: "socks",
          name: "socks",
          options: [
            { value: "synthetic-wool", label: "Synthetic or wool" },
            { value: "cotton", label: "Cotton" },
            { value: "unsure", label: "Not sure yet" }
          ]
        }
      ]
    },
    {
      id: "pack-setup",
      number: 15,
      chapter: "equipment",
      chapterLabel: "Equipment",
      title: "Pack setup lesson",
      purpose: "Teach the minimum viable setup using the selected pack and load.",
      uses: "pack-fit-adjustment.png plus an original pack cross-section.",
      changes: "Education only. Action stays available without completing the checks.",
      kind: "pack-setup",
      theme: "equipment",
      status: "dark",
      autoAdvance: false,
      headline: "Make the pack quiet before you go farther.",
      body: "This is a lesson, not a test that you already did it.",
      action: "I understand the setup",
      checks: [
        "Cushion hard edges.",
        "Center and secure the load.",
        "Tighten both shoulder straps until the pack stays close without pinching.",
        "Use the sternum or hip strap if it improves stability and comfort.",
        "Walk around the room. Adjust if it bounces, rubs, or causes numbness."
      ]
    },
    {
      id: "surface",
      number: 16,
      chapter: "route",
      chapterLabel: "Route",
      title: "Surface",
      purpose: "Rank demonstration routes from ground that already feels comfortable.",
      uses: "surface and route-choice-greenway.png as non-live atmosphere.",
      changes: "Route ranking and footwear note.",
      kind: "route-chapter",
      theme: "route",
      status: "dark",
      autoAdvance: true,
      photoCaption: "Route-character example · Not a live route",
      prompt: "Which surface already feels comfortable?",
      field: "surface",
      name: "surface",
      options: [
        { value: "paved", label: "Paved paths" },
        { value: "compacted", label: "Compacted park paths" },
        { value: "natural", label: "Natural dirt or gravel I already walk" },
        { value: "mixed", label: "A mix is fine" },
        { value: "not-sure", label: "Not sure yet" }
      ]
    },
    {
      id: "hills",
      number: 17,
      chapter: "route",
      chapterLabel: "Route",
      title: "Hills",
      purpose: "Set an elevation ceiling. The first plan never recommends steep terrain.",
      uses: "hills",
      changes: "Terrain target and hard route filter.",
      kind: "choice",
      theme: "route",
      status: "dark",
      autoAdvance: true,
      prompt: "How should the first route handle hills?",
      field: "hills",
      name: "hills",
      options: [
        { value: "level", label: "Keep it mostly level" },
        { value: "gentle", label: "A few gentle rises are fine" },
        { value: "rolling", label: "I already walk rolling hills comfortably" }
      ]
    },
    {
      id: "route-shape",
      number: 18,
      chapter: "route",
      chapterLabel: "Route",
      title: "Route shape",
      purpose: "Prefer a shape that is easy to control and reverse.",
      uses: "routeShape",
      changes: "Route score and explanation.",
      kind: "choice",
      theme: "route",
      status: "dark",
      autoAdvance: true,
      prompt: "Which first-route shape feels easiest to control?",
      field: "routeShape",
      name: "route-shape",
      options: [
        { value: "out-back", label: "Out and back, with an early turnaround" },
        { value: "short-loop", label: "A short loop" },
        { value: "either", label: "Either is fine" }
      ]
    },
    {
      id: "route-priority",
      number: 19,
      chapter: "route",
      chapterLabel: "Route",
      title: "Route priority",
      purpose: "Break ties only. Scenery cannot override access, distance, or safety.",
      uses: "routePriority",
      changes: "Tiebreaker after hard filters.",
      kind: "choice",
      theme: "route",
      status: "dark",
      autoAdvance: true,
      prompt: "What would make you more likely to go?",
      field: "routePriority",
      name: "route-priority",
      options: [
        { value: "familiar", label: "A familiar area" },
        { value: "quiet", label: "A quieter path" },
        { value: "shade", label: "More shade" },
        { value: "facilities", label: "Nearby facilities" },
        { value: "scenery", label: "A nicer view, if the route is still easy to reverse" }
      ]
    },
    {
      id: "starting-area",
      number: 20,
      chapter: "route",
      chapterLabel: "Route",
      title: "Starting area",
      purpose: "Store a local label only. No geolocation or geocoding.",
      uses: "locationLabel",
      changes: "Result copy such as Near Santa Barbara.",
      kind: "starting-area",
      theme: "route",
      status: "dark",
      autoAdvance: false,
      prompt: "Where should we look when live routes arrive?",
      help: "Type a city, neighborhood, or ZIP if you want. This prototype does not use your device location.",
      field: "locationLabel",
      placeholder: "City, neighborhood, or ZIP",
      chooseLater: "Choose later",
      action: "Continue"
    },
    {
      id: "analysis",
      number: 21,
      chapter: "payoff",
      chapterLabel: "Payoff",
      title: "Honest analysis",
      purpose: "Make the computation legible, not mysterious.",
      uses: "All answers collected so far. Computation is already complete.",
      changes: "No new answers. Reveals the plan next.",
      kind: "analysis",
      theme: "forest",
      status: "light",
      autoAdvance: false,
      headline: "Building your first ruck",
      steps: [
        "Keeping time inside your comfortable walk",
        "Setting a light equipment ceiling",
        "Matching terrain and route shape"
      ],
      howTitle: "How this works",
      howBody: "This prototype uses fixed rules from your answers. It is not an AI or medical assessment.",
      action: "Continue"
    },
    {
      id: "profile",
      number: 22,
      chapter: "payoff",
      chapterLabel: "Payoff",
      title: "Starting profile",
      purpose: "Repay the questions with an inspectable starting point.",
      uses: "The full deterministic plan.",
      changes: "No new answers.",
      kind: "profile",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      eyebrow: "Your starting point",
      action: "See my four weeks"
    },
    {
      id: "four-weeks",
      number: 23,
      chapter: "payoff",
      chapterLabel: "Payoff",
      title: "First four weeks",
      purpose: "Show a conservative month that never auto-increases.",
      uses: "Duration, load, terrain, weekly rhythm, and Week 3 rule.",
      changes: "No new answers.",
      kind: "four-weeks",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      headline: "Four weeks, one loaded day at a time.",
      action: "See demonstration routes"
    },
    {
      id: "demo-routes",
      number: 24,
      chapter: "payoff",
      chapterLabel: "Payoff",
      title: "Demonstration routes",
      purpose: "Show fictional matches with unknowns, never live trails.",
      uses: "Surface, hills, shape, priority, duration, and location label.",
      changes: "selectedDemoRouteId for this walkthrough only.",
      kind: "demo-routes",
      theme: "route",
      status: "dark",
      autoAdvance: false,
      headline: "Demonstration matches, not live trails.",
      body: "These candidates are original examples. Selection does not start tracking.",
      action: "Choose for this walkthrough"
    },
    {
      id: "leave-ready",
      number: 25,
      chapter: "payoff",
      chapterLabel: "Payoff",
      title: "Leave-ready plan",
      purpose: "Hand off a checklist the person can inspect, not blindly follow.",
      uses: "completion-portrait.png plus the computed plan and chosen demonstration route.",
      changes: "End of onboarding. Today is a preview, not a 26th page.",
      kind: "leave-ready",
      theme: "paper",
      status: "dark",
      autoAdvance: false,
      headline: "Your first ruck is ready to check, not blindly follow.",
      action: "Open Today preview",
      secondary: "Change my answers"
    }
  ];

  var BY_ID = {};
  SCREENS.forEach(function (screen) {
    BY_ID[screen.id] = screen;
  });

  var CHAPTERS = [
    { id: "promise", label: "Promise", start: 1, count: 3 },
    { id: "starting-point", label: "Starting point", start: 4, count: 8 },
    { id: "equipment", label: "Equipment", start: 12, count: 4 },
    { id: "route", label: "Route", start: 16, count: 5 },
    { id: "payoff", label: "Payoff", start: 21, count: 5 }
  ];

  function getScreen(id) {
    return BY_ID[id] || SCREENS[0];
  }

  function chapterProgress(screen) {
    var siblings = SCREENS.filter(function (item) {
      return item.chapter === screen.chapter;
    });
    var index = siblings.findIndex(function (item) {
      return item.id === screen.id;
    });
    return {
      chapter: screen.chapterLabel,
      current: index + 1,
      total: siblings.length,
      label: screen.chapterLabel + " · " + (index + 1) + " of " + siblings.length
    };
  }

  return {
    SCREENS: SCREENS,
    CHAPTERS: CHAPTERS,
    getScreen: getScreen,
    chapterProgress: chapterProgress
  };
});
