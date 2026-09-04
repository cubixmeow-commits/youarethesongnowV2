(function () {
  var A = "assets";
  var state = {
    path: "main",
    index: 0,
    firstName: "Alex",
    age: "28",
    answers: {},
    help: {},
    symptoms: {},
    goals: {},
    pattern: "daily",
    size: "medium",
    activity: "moderate",
    unit: "metric",
    nutrGoal: "lose",
    signed: false,
    dialog: null,
    heightCm: 170,
    weightKg: 70
  };

  function screens() {
    return state.path === "all"
      ? STOPPR_MAIN.concat(STOPPR_LEGACY)
      : STOPPR_MAIN;
  }

  function $(id) { return document.getElementById(id); }

  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }

  function name() {
    return (state.firstName || "there").trim() || "there";
  }

  function quitDate() {
    var d = new Date();
    d.setDate(d.getDate() + 90);
    return d.toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });
  }

  function calEstimates() {
    var per = { small: 150, medium: 250, large: 425 }[state.size] || 250;
    var n = state.pattern === "daily" ? 7 : 3;
    var week = per * n;
    return { week: week, quarter: week * 13, year: week * 52 };
  }

  function caloriePlan() {
    var base = 2000;
    if (state.nutrGoal === "lose") base = 1700;
    if (state.nutrGoal === "gain") base = 2300;
    if (state.activity === "light") base -= 100;
    if (state.activity === "intense") base += 200;
    return {
      cal: base,
      protein: Math.round(base * 0.3 / 4),
      carbs: Math.round(base * 0.4 / 4),
      fats: Math.round(base * 0.3 / 9),
      sugar: 25,
      score: 82
    };
  }

  function statusLight(on) {
    $("status-bar").classList.toggle("light", !!on);
    $("home-bar").classList.toggle("light", !!on);
  }

  function cta(label, extraClass) {
    return '<button class="cta ' + (extraClass || "") + '" data-act="next" type="button">' +
      esc(label) + " →</button>";
  }

  function radarSvg(values, color) {
    var cx = 140, cy = 140, r = 96, n = 6;
    var grid = "";
    for (var g = 1; g <= 4; g++) {
      var pts = [];
      for (var i = 0; i < n; i++) {
        var a = -Math.PI / 2 + i * Math.PI * 2 / n;
        var rr = r * g / 4;
        pts.push((cx + Math.cos(a) * rr).toFixed(1) + "," + (cy + Math.sin(a) * rr).toFixed(1));
      }
      grid += '<polygon points="' + pts.join(" ") + '" fill="none" stroke="#ddd" stroke-width="1"/>';
    }
    var poly = [];
    for (var j = 0; j < n; j++) {
      var ang = -Math.PI / 2 + j * Math.PI * 2 / n;
      var rv = r * values[j];
      poly.push((cx + Math.cos(ang) * rv).toFixed(1) + "," + (cy + Math.sin(ang) * rv).toFixed(1));
    }
    var labels = ["Physical Beauty", "Energy", "Glowing Skin", "Mood", "Fit Body", "Hormones"];
    var labs = "";
    for (var k = 0; k < n; k++) {
      var aa = -Math.PI / 2 + k * Math.PI * 2 / n;
      var lx = cx + Math.cos(aa) * 122;
      var ly = cy + Math.sin(aa) * 122;
      labs += '<text x="' + lx.toFixed(1) + '" y="' + ly.toFixed(1) +
        '" text-anchor="middle" font-size="9" font-weight="700" fill="#444">' +
        labels[k] + "</text>";
    }
    return '<svg width="280" height="280" viewBox="0 0 280 280">' + grid +
      '<polygon points="' + poly.join(" ") + '" fill="' + color +
      '55" stroke="' + color + '" stroke-width="2"/>' + labs + "</svg>";
  }

  function overlayLang() {
    return '<div class="lang-sound"><div class="chip">🔊</div><div class="chip">EN ▾</div></div>';
  }

  function renderKind(s) {
    var d = s.data || {};
    statusLight(false);
    switch (s.kind) {
      case "welcome-video":
        statusLight(true);
        return '<video class="video-fill" src="' + A + "/" + d.video +
          '" autoplay muted loop playsinline></video>' +
          '<div class="bottom-sheet" style="background:transparent;color:#fff;text-align:center">' +
          '<div style="font-size:22px;font-weight:800;text-shadow:0 2px 8px #000">' +
          esc(d.lines[0]) + "<br>" + esc(d.lines[1]) + "</div></div>";
      case "start-quiz":
        statusLight(true);
        return overlayLang() +
          '<video class="video-fill" src="' + A + '/videos/onboarding_mockup.mp4" autoplay muted loop playsinline></video>' +
          '<div class="bottom-sheet">' +
          '<h2 class="headline" style="font-size:22px;text-align:center">Let\'s start a healthier life today</h2>' +
          '<div class="social-proof"><img class="stars" src="' + A +
          '/images/svg/stars-onboarding-screen-2.svg" alt="stars"><div>Join <span class="count">250,000+</span> women</div>' +
          "<div>who transformed their health</div></div>" +
          '<div style="height:16px"></div>' + cta("Continue") + "</div>";
      case "fomo":
        return '<div class="pad"><h2 class="headline">How many quit sugar\nthis week with Stoppr</h2>' +
          '<p class="subhead">Quitting sugar is possible for you</p>' +
          fomoStat("251,033+", "downloads") +
          fomoStat("100%", "methodology backed by nutritionists and behavioral scientists") +
          fomoStat("897+", "started their journey this week") +
          '<div style="height:18px"></div>' + cta("Continue") + "</div>";
      case "auth":
        return '<div class="screen-scroll"><div class="pad">' +
          '<img src="' + A + '/images/onboarding/onboarding-screen3-coach.png" alt="" style="width:100%;border-radius:18px;margin-bottom:12px">' +
          '<h2 class="headline" style="text-align:center">Become a STOPPR</h2>' +
          '<p class="subhead" style="text-align:center">Join over 250,000 users! Become sugar free and regain control over your life</p>' +
          '<button class="auth-btn apple" data-act="next" type="button"> Continue with Apple</button>' +
          '<button class="auth-btn" data-act="next" type="button"><img src="' + A +
          '/images/svg/google.svg" alt="" height="18"> Continue with Google</button>' +
          '<button class="auth-btn" data-act="next" type="button"> Continue with Email</button>' +
          '<button class="skip-link" data-act="skip-dialog" type="button" style="margin-bottom:24px">Skip for now</button>' +
          "</div></div>" + skipDialogHtml();
      case "radar":
      case "weeks":
        return weeksHtml(s);
      case "question":
        return questionHtml(s);
      case "break":
        return breakHtml();
      case "consumption":
        return consumptionHtml();
      case "insights":
        return insightsHtml();
      case "profile":
        return profileHtml();
      case "calculating":
        return calculatingHtml();
      case "analysis":
        return analysisHtml();
      case "symptoms":
        return symptomsHtml();
      case "video-cta":
        statusLight(true);
        return '<video class="video-fill" src="' + A + "/" + d.video +
          '" autoplay muted loop playsinline></video>' +
          '<div class="bottom-sheet"><h2 class="headline" style="font-size:22px">' +
          esc(d.title) + "</h2>" + cta("Continue") + "</div>";
      case "workouts":
        return workoutsHtml();
      case "height-weight":
        return hwHtml();
      case "goal-select":
        return goalSelectHtml();
      case "calories":
        return caloriesHtml();
      case "experts":
        return expertsHtml();
      case "chart":
        return chartHtml();
      case "notifications":
        return notifHtml();
      case "science":
        return scienceHtml();
      case "ratings":
        return ratingsHtml();
      case "goals":
        return goalsHtml();
      case "referral":
        return '<div class="pad"><h2 class="headline">Any referral code?</h2>' +
          '<p class="subhead">You can skip this step</p>' +
          '<input class="field" placeholder="Referral code">' +
          cta("Continue") +
          '<button class="skip-link" data-act="next" type="button">Skip</button></div>';
      case "letter":
        return letterHtml();
      case "vow":
        return vowHtml();
      case "card2":
        return card2Html();
      case "paywall":
        return paywallHtml();
      case "congrats":
        return congratsHtml(s);
      case "home":
        return homeHtml();
      case "pain":
        statusLight(true);
        return painHtml(s);
      case "benefit":
        statusLight(true);
        return benefitHtml(s);
      case "legacy-note":
        return '<div class="pad"><h2 class="headline">Legacy screens</h2>' +
          '<p class="subhead">The live first-launch path no longer visits these after symptoms. They remain in the codebase for resume from an old checkpoint.</p>' +
          cta("Show them") + "</div>";
      case "blocks":
        return blocksHtml(false);
      case "potential":
        return blocksHtml(true);
      default:
        return '<div class="pad"><p>Unknown screen</p></div>';
    }
  }

  function fomoStat(n, t) {
    return '<div style="padding:14px 0;border-bottom:1px solid #f0ecee"><div style="font-size:28px;font-weight:800">' +
      n + '</div><div style="color:#666;font-size:14px">' + t + "</div></div>";
  }

  function skipDialogHtml() {
    if (state.dialog !== "skip") return "";
    return '<div class="modal"><div class="dialog"><h3>Are you sure?</h3>' +
      "<p>We think the welcome experience is crucial to your success.\nIf you go through it we have a surprise for you at the end</p>" +
      '<div class="dialog-actions"><button class="ghost-btn" data-act="dialog-cancel" type="button">Cancel</button>' +
      '<button class="nav-btn" data-act="next" type="button">Skip</button></div></div></div>';
  }

  function weeksHtml(s) {
    var d = s.data;
    var dark = s.kind === "weeks";
    if (dark) statusLight(true);
    var wrap = dark
      ? 'class="screen-scroll dark-screen" style="height:100%"'
      : 'class="screen-scroll" style="height:100%;background:#fff"';
    var title = d.email ? "We've built your profile." : (d.week === "now" ? "Welcome to your Journey!" : d.week);
    var sub = d.email
      ? "Now let's find out why you're struggling"
      : (d.week === "now"
        ? "Your journey is just beginning.\nLet's learn how to make progress starting today."
        : d.desc);
    return "<div " + wrap + '><div class="pad">' +
      '<h2 class="headline" style="' + (dark ? "color:#fff" : "") + '">' + esc(title) + "</h2>" +
      '<p class="subhead" style="' + (dark ? "color:#cbb" : "") + '">' + esc(sub) + "</p>" +
      (d.week === "now" ? '<p class="subhead">This is your Stoppr progress tracker</p>' : "") +
      '<div class="radar-wrap">' + radarSvg(d.values, d.color || "#ed3272") + "</div>" +
      cta("Continue") + "</div></div>";
  }

  function questionHtml(s) {
    var q = s.data.q;
    var selected = state.answers[q.id] || [];
    var html = '<div class="screen-scroll" style="height:100%"><div class="pad-tight">' +
      '<div class="progress-track"><div class="progress-fill" style="width:' +
      ((s.data.page / s.data.total) * 100) + '%"></div></div>' +
      overlayLang() +
      '<div style="height:18px"></div>' +
      '<div style="font-size:13px;font-weight:800;color:#1a1a1a">Question #' + q.id + "</div>" +
      (q.multi ? '<div style="font-size:12px;color:#666;margin:4px 0 8px">(multiple answers possible)</div>' : '<div style="height:8px"></div>') +
      '<h2 class="headline" style="font-size:22px">' + esc(q.text) + "</h2>";
    q.options.forEach(function (opt, i) {
      var on = selected.indexOf(i) !== -1;
      var icon = opt[2] ? '<img src="' + A + "/images/svg/" + opt[2] + '" alt="" height="18" style="margin-right:6px">' : "";
      html += '<button class="option' + (on ? " selected" : "") + '" data-act="opt" data-q="' +
        q.id + '" data-i="' + i + '" data-multi="' + (q.multi ? "1" : "0") + '" type="button">' +
        '<span class="num">' + (i + 1) + '</span><span class="label">' + icon + esc(opt[0]) +
        "</span></button>";
      if (on && !q.acquisition) {
        html += '<div class="help">' + esc(opt[1]) + "</div>";
      }
    });
    html += '<div style="height:8px"></div>' + cta("Continue", "square") +
      '<button class="skip-link" data-act="skip-dialog" type="button">Skip test</button></div></div>' +
      skipDialogHtml();
    return html;
  }

  function breakHtml() {
    return '<div class="screen-scroll" style="height:100%"><div class="pad" style="text-align:center">' +
      '<h2 class="headline">See how a few minutes a day can change your life</h2>' +
      '<p class="subhead">Look here at your future</p>' +
      '<div style="background:#f8f9fa;border:1px solid #e0e0e0;border-radius:16px;padding:16px;height:180px;position:relative">' +
      '<svg width="100%" height="148" viewBox="0 0 300 148" preserveAspectRatio="none">' +
      '<path d="M10 120 C 80 118, 110 110, 140 70 S 220 20, 290 12" fill="none" stroke="#ed3272" stroke-width="4"/>' +
      '<circle cx="140" cy="70" r="6" fill="#FFD700"/><circle cx="290" cy="12" r="6" fill="#ed3272"/>' +
      "</svg></div>" +
      '<div style="display:flex;justify-content:space-between;margin:8px 4px 16px;color:#666;font-size:13px;font-weight:600"><span>3 Days</span><span>7 Days</span><span>30 Days</span></div>' +
      '<p class="subhead">Based on scientific data, sugar cravings usually persist at first, but after 7 days, your brain starts to rewire and freedom becomes easier!</p>' +
      cta("Continue", "square") + "</div></div>";
  }

  function consumptionHtml() {
    var est = calEstimates();
    function pill(id, label, group, val) {
      var on = state[group] === val;
      return '<button class="chip-opt' + (on ? " on" : "") + '" data-act="set" data-k="' +
        group + '" data-v="' + val + '" type="button">' + esc(label) + "</button>";
    }
    return '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<h2 class="headline" style="font-size:24px">What does a typical week look like for you?</h2>' +
      '<p class="subhead">What\'s your general pattern with sweet foods?</p>' +
      pill("p1", "Daily", "pattern", "daily") +
      pill("p2", "Every few days", "pattern", "few") +
      '<p class="subhead" style="margin-top:16px">Average treat size</p>' +
      pill("s1", "🍪 Small (150 cal)", "size", "small") +
      pill("s2", "🍦 Medium (250 cal)", "size", "medium") +
      pill("s3", "🍰 Large (425 cal)", "size", "large") +
      '<div class="info-box" style="margin-top:12px">Your total calorie intake from sugar:<br><b>' +
      est.week.toLocaleString() + "</b> per week · <b>" + est.year.toLocaleString() + "</b> per year</div>" +
      '<button class="skip-link" data-act="why" type="button">Why do we ask?</button>' +
      cta("Continue", "square") + "</div></div>" +
      (state.dialog === "why" ? '<div class="modal"><div class="dialog"><h3>Why do we ask?</h3><p>We ask about your sugar consumption habits to help personalize your journey and show you the potential impact of making a change. This information helps us create a more effective plan for you.</p><div class="dialog-actions"><button class="nav-btn" data-act="dialog-cancel" type="button">Got It</button></div></div></div>' : "");
  }

  function insightsHtml() {
    return '<div class="pad"><h2 class="headline">Your first 30 days</h2>' +
      '<p class="subhead">Stick with it and you could get:</p>' +
      insightRow("2 weeks", "TO VISIBLY CLEARER SKIN", "Sugar-free skin that actually glows") +
      insightRow("3X", "BETTER SLEEP QUALITY", "Wake up feeling refreshed, not groggy") +
      insightRow("~1 lb", "OF WEIGHT LOSS PER WEEK", "That's like dropping a jeans size every month") +
      '<p class="subhead">Estimates based on your pattern. Actual results vary.</p>' +
      cta("Continue") + "</div>";
  }
  function insightRow(v, k, c) {
    return '<div style="padding:12px 0;border-bottom:1px solid #f0ecee"><div style="font-size:28px;font-weight:800;background:var(--cta);-webkit-background-clip:text;color:transparent">' +
      v + '</div><div style="font-size:12px;font-weight:800;letter-spacing:0.04em">' + k +
      '</div><div style="font-size:13px;color:#666">' + c + "</div></div>";
  }

  function profileHtml() {
    return '<div class="pad"><h2 class="headline">Finally</h2>' +
      '<p class="subhead">A little more about you</p>' +
      '<input class="field" id="name-input" placeholder="First name" value="' + esc(state.firstName) + '">' +
      '<input class="field" id="age-input" placeholder="Age" inputmode="numeric" value="' + esc(state.age) + '">' +
      cta("Complete Quiz", "square") + "</div>";
  }

  function calculatingHtml() {
    return '<div class="pad" style="text-align:center;padding-top:90px">' +
      '<div class="laurels"><img src="' + A + '/images/onboarding/left_laurel_icon.png" alt=""><div style="font-size:18px;font-weight:800">STOPPR</div><img src="' + A + '/images/onboarding/right_laurel_icon.png" alt=""></div>' +
      '<h2 class="headline">Analyzing your sweet tooth</h2>' +
      '<div class="calc-step on">Understanding responses</div>' +
      '<div class="calc-step">Learning relapse triggers</div>' +
      '<div class="calc-step">Finalizing</div>' +
      '<div class="progress-track" style="margin:20px 0"><div class="progress-fill" id="calc-bar" style="width:15%"></div></div>' +
      '<div style="font-size:22px;font-weight:800">Over 600,000+</div>' +
      '<div style="color:#666">Sugar cravings prevented</div>' +
      '<div style="height:24px"></div>' + cta("Continue") + "</div>";
  }

  function analysisHtml() {
    return '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<img src="' + A + '/images/svg/green-checkmark.svg" alt="" height="36">' +
      '<h2 class="headline">Analysis Complete</h2>' +
      '<p class="subhead">We\'ve got some news to break to you...</p>' +
      '<p style="font-weight:700">Your responses indicate a clear dependance on sugar*</p>' +
      '<div class="bar-chart"><div class="bar-row"><div class="bar you">73%</div><div class="bar avg">32%</div></div>' +
      '<div style="display:flex;justify-content:space-around;font-size:12px;font-weight:700;margin-top:6px"><span>Your Score</span><span>Average</span></div>' +
      '<div class="safety">mental & physical<br>health safety line</div></div>' +
      '<p class="subhead">* This result is an indication only, not a medical diagnosis.\nFor a definitive assessment, please contact your healthcare provider.</p>' +
      cta("Check your symptoms", "square") + "</div></div>";
  }

  function symptomsHtml() {
    var cats = [
      ["Mental", ["Feeling unmotivated", "Lack of ambition to pursue goals", "Difficulty concentrating", "Poor memory or 'brain fog'", "General anxiety"]],
      ["Physical", ["Tiredness and lethargy", "Low libido or sex drive", "Low energy without sugar"]],
      ["Social", ["Low self-confidence", "Feeling unattractive or unworthy of love", "Unsuccessful or unenjoyable sex", "Reduced desire to socialize", "Feeling isolated from others"]],
      ["Faith", ["Feeling distant from god"]]
    ];
    var html = '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<h2 class="headline" style="font-size:24px">How do you usually feel day to day?</h2>' +
      '<div class="info-box">Excessive sugar consumption can have negative impacts psychologically.</div>' +
      "<p>Select any symptoms below:</p>";
    cats.forEach(function (c) {
      html += '<div class="symptom-cat">' + c[0] + "</div>";
      c[1].forEach(function (item) {
        var on = !!state.symptoms[item];
        html += '<button class="chip-opt' + (on ? " on" : "") + '" data-act="sym" data-v="' +
          item.replace(/"/g, "&quot;") + '" type="button">' + esc(item) + "</button>";
      });
    });
    html += '<div style="height:12px"></div>' + cta("Continue", "square") + "</div></div>";
    return html;
  }

  function workoutsHtml() {
    function card(val, big, small) {
      return '<button class="choice-card' + (state.activity === val ? " on" : "") +
        '" data-act="set" data-k="activity" data-v="' + val + '" type="button"><div class="big">' +
        big + '</div><div class="small">' + small + "</div></button>";
    }
    return '<div class="pad"><div class="progress-track"><div class="progress-fill" style="width:25%"></div></div>' +
      '<div style="height:16px"></div>' +
      '<h2 class="headline">How many workouts do you do per week?</h2>' +
      '<p class="subhead">This will be used to calibrate your custom plan.</p>' +
      card("light", "0–2", "Workouts now and then") +
      card("moderate", "3–5", "A few workouts per week") +
      card("intense", "6+", "Dedicated athlete") +
      cta("Next", "square") + "</div>";
  }

  function hwHtml() {
    return '<div class="pad"><div class="progress-track"><div class="progress-fill" style="width:50%"></div></div>' +
      '<div style="height:16px"></div>' +
      '<h2 class="headline">Height & weight</h2>' +
      '<p class="subhead">This will be used to calibrate your custom plan.</p>' +
      '<div class="unit-toggle"><button class="' + (state.unit === "imperial" ? "on" : "") +
      '" data-act="set" data-k="unit" data-v="imperial" type="button">Imperial</button>' +
      '<button class="' + (state.unit === "metric" ? "on" : "") +
      '" data-act="set" data-k="unit" data-v="metric" type="button">Metric</button></div>' +
      '<label>Height</label><input class="field" value="' + (state.unit === "metric" ? "170 cm" : "5 ft 7 in") + '">' +
      '<label>Weight</label><input class="field" value="' + (state.unit === "metric" ? "70 kg" : "154 lb") + '">' +
      cta("Next", "square") + "</div>";
  }

  function goalSelectHtml() {
    function card(val, t) {
      return '<button class="choice-card' + (state.nutrGoal === val ? " on" : "") +
        '" data-act="set" data-k="nutrGoal" data-v="' + val + '" type="button"><b>' + t + "</b></button>";
    }
    return '<div class="pad"><div class="progress-track"><div class="progress-fill" style="width:75%"></div></div>' +
      '<div style="height:16px"></div>' +
      '<h2 class="headline">What is your goal?</h2>' +
      '<p class="subhead">This helps us generate a plan for your calorie intake.</p>' +
      card("lose", "Lose weight") + card("maintain", "Maintain") + card("gain", "Gain weight") +
      cta("Auto Generate Goals", "square") + "</div>";
  }

  function caloriesHtml() {
    var p = caloriePlan();
    function row(k, v) {
      return '<div class="metric-row"><span>' + k + "</span><b>" + v + "</b></div>";
    }
    return '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<div class="progress-track"><div class="progress-fill" style="width:100%"></div></div>' +
      '<div style="height:16px"></div>' +
      '<h2 class="headline" style="font-size:24px">Congratulations your personalized meals plan is ready!</h2>' +
      '<p class="subhead">You should maintain:</p>' +
      row("Calories", p.cal) + row("Protein", p.protein + " g") + row("Carbs", p.carbs + " g") +
      row("Fats", p.fats + " g") + row("Processed Sugar", p.sugar + " g") + row("Health Score", p.score) +
      '<p class="subhead">You can edit this anytime in the app</p>' +
      cta("Continue", "square") + "</div></div>";
  }

  function expertsHtml() {
    var people = [
      ["andrew_huberman.jpg", "Andrew Huberman", "The Science of Breaking Sugar Addiction", "Processed sugar hijacks your dopamine pathways, creating addiction-like patterns. Understanding the neuroscience behind cravings is the first step to freedom."],
      ["demi_lovato.jpg", "Demi Lovato", "Recovery Includes Breaking All Harmful Patterns", "Part of my healing journey was recognizing how sugar was another way I was numbing emotions. Breaking free from processed sugar gave me real clarity."],
      ["lizzo.jpg", "Lizzo", "Self-Love Means Ditching What Doesn't Serve You", "Real self-care isn't just bubble baths—it's saying no to processed sugar that makes you crash. Your body deserves better, and so do you!"],
      ["tess.jpg", "Tess", "Quitting Sugar Transformed My Wellbeing", "Sleep's deeper, and my mind's sharper without sugar. Dropped the bloat and feel lighter every day—amazing!"]
    ];
    var html = '<div class="screen-scroll" style="height:100%"><div class="pad"><h2 class="headline" style="font-size:22px">Read what top experts & influencers say:</h2>';
    people.forEach(function (p) {
      html += '<div class="testimonial"><img src="' + A + "/images/testimonials/" + p[0] +
        '" alt=""><div><h4>' + esc(p[1]) + "</h4><b style='font-size:12px'>" + esc(p[2]) +
        "</b><p>" + esc(p[3]) + "</p></div></div>";
    });
    html += cta("Continue") + "</div></div>";
    return html;
  }

  function chartHtml() {
    return '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<img src="' + A + '/images/onboarding/rewiring_benefits_chart_3.png" alt="chart" style="width:100%;border-radius:12px">' +
      '<h2 class="headline" style="font-size:22px;margin-top:12px">STOPPR helps you quit sugar <span style="color:#ed3272">76% faster than willpower alone. 📈</span></h2>' +
      '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:12px 0">' +
      [["41%", "Boost Your Energy"], ["28%", "Improve Sleep Quality"], ["45%", "Enhance Mental Clarity"], ["30%", "Improve Mood"]].map(function (x) {
        return '<div style="background:#fff6f0;border-radius:12px;padding:10px"><b style="font-size:22px">' +
          x[0] + "</b><div style='font-size:12px'>" + x[1] + "</div></div>";
      }).join("") + "</div>" +
      "<p><b>Scientific research</b></p>" +
      "<p class='subhead'>Sugar addiction: pushing the drug-sugar analogy to the limit — pubmed.ncbi.nlm.nih.gov</p>" +
      cta("Continue") + "</div></div>";
  }

  function notifHtml() {
    return '<div class="screen-scroll" style="height:100%;background:#fbfbfb"><div class="pad">' +
      '<img src="' + A + '/images/logo/180.png" alt="" width="56" style="border-radius:12px">' +
      '<h2 class="headline" style="font-size:24px;margin-top:12px">Make Stoppr work around your schedule</h2>' +
      '<div class="ios-alert"><h3>“Stoppr” Would Like to Send You Notifications</h3>' +
      "<p>Enabling notifications increase by 63% your chances to resist your sugar cravings. Notifications may include alerts, sounds, and icon badges. These can be configured in Settings.</p>" +
      '<div class="ios-row"><button data-act="next" type="button">Don\'t Allow</button>' +
      '<button data-act="next" type="button">Allow</button></div></div>' +
      '<p style="font-size:14px;font-weight:600">Hey ' + esc(name()) + ", how are you feeling?</p>" +
      '<h3 style="font-size:16px">Stay on track, your way</h3>' +
      '<div class="choice-card"><b>Motivation</b><div class="small">Daily motivation to start strong · 8:00 AM</div></div>' +
      '<div class="choice-card"><b>Pledge reminder</b><div class="small">Get reminded to keep your daily pledge · 8:00 PM</div></div>' +
      "</div></div>";
  }

  function scienceHtml() {
    return '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<h2 class="headline" style="font-size:24px">STOPPR\'s Science-Backed Plan</h2>' +
      quote("harvard.png", '"Habits form through repeated actions in stable contexts, taking weeks to months to become automatic."', "Wood, W., & Rünger, D. (2016). Psychology of Habit.") +
      quote("ucl.png", '"96 participants performed a daily behavior, and automaticity was modeled to plateau at an average of 90 days, with a range of 18-254 days."', "Lally, P., et al. (2010).") +
      quote("atomic_habits.png", '"On average, it takes more than three months before a new behavior becomes automatic—90 days to be exact."', "Clear, J. (2018). Atomic Habits.") +
      '<div style="display:flex;gap:10px;margin:12px 0"><div class="info-box" style="flex:1;text-align:center"><b>1,500+</b><div>CITED GOOGLE SCHOLAR</div></div><div class="info-box" style="flex:1;text-align:center"><b>1,000,000+</b><div>sold NY TIMES BEST SELLER</div></div></div>' +
      cta("Next") + "</div></div>";
  }
  function quote(img, q, src) {
    return '<div class="quote-card"><img src="' + A + "/images/onboarding/" + img + '" alt=""><p>' +
      esc(q) + "</p><small>" + esc(src) + "</small></div>";
  }

  function ratingsHtml() {
    var reviews = [
      ["sarah_profile.png", "Sarah Johnson", "@SarahJ89", "STOPPR transformed my life! I've been sugar-free for 3 months thanks to its daily tips and tracking tools."],
      ["mark_profile.png", "Mark Harris", "@MarkHarris", "STOPPR is the best app to ditch sugar completely! Two months in, I've lost weight and feel sharper."],
      ["emily_profile.png", "Emily Brooks", "@EmBrooks", "It guides you off sugar with step-by-step challenges and rewarding progress charts."],
      ["jacob_profile.png", "Jacob Peterson", "@JakePete", "I never thought I'd quit sugar, but STOPPR's simple design and timely reminders proved me wrong."]
    ];
    var html = '<div class="screen-scroll" style="height:100%"><div class="pad" style="text-align:center">' +
      '<h2 class="headline">Help us help more people</h2>' +
      '<img src="' + A + '/images/svg/stars_laurels.svg" alt="" style="height:48px">' +
      '<div style="font-weight:800;margin:6px 0">+ 250,000 people</div>' +
      '<img src="' + A + '/images/ratings/3%20people%20image.png" alt="" style="width:70%;margin:8px auto;display:block">' +
      '<p class="subhead">STOPPR is designed for people like you, who want to <b>rebuild their habits and become better</b>. Giving us a 5-star rating helps us to further build our vision and help more people in this world.</p>';
    reviews.forEach(function (r) {
      html += '<div class="testimonial" style="text-align:left"><img src="' + A + "/images/ratings/" +
        r[0] + '" alt=""><div><h4>' + r[1] + " " + r[2] + "</h4><p>" + r[3] + "</p></div></div>";
    });
    html += cta("Continue") + "</div></div>";
    return html;
  }

  function goalsHtml() {
    var goals = [
      ["Stronger relationships", "#ed3272"],
      ["Improved self-confidence", "#2196F3"],
      ["Improved mood and happiness", "#FFC107"],
      ["More energy and motivation", "#fd5d32"],
      ["Improved libido and sex life", "#F44336"],
      ["Improved self-control", "#00BCD4"],
      ["Improved focus and clarity", "#9C27B0"],
      ["Pure and healthy thoughts", "#4CAF50"]
    ];
    var html = '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<h2 class="headline" style="font-size:22px">Choose the goals you\'d like to improve</h2>' +
      '<p class="subhead">Select goals you wish to track during your reboot.</p>';
    goals.forEach(function (g) {
      var on = !!state.goals[g[0]];
      html += '<button class="goal-card' + (on ? " on" : "") + '" style="background:' + g[1] +
        '" data-act="goal" data-v="' + esc(g[0]) + '" type="button">' + esc(g[0]) + "</button>";
    });
    html += cta("Track these goals", "square") + "</div></div>";
    return html;
  }

  function letterHtml() {
    var today = new Date().toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });
    var year = new Date().getFullYear();
    var body = "Hey " + name() + ", it's your future self. I'm reaching out from " + year +
      " because today matters a lot for us!\n\n" + today + " is when I turned things around.\n\n" +
      "Now, I'm thriving and living my best life, all because of the growth step you're about to take.\n\nSee you soon.";
    return '<div class="pad"><h2 class="headline">A Letter From Future</h2>' +
      '<p class="subhead">It\'s me — Future You</p>' +
      '<div class="letter">' + esc(body) + "</div>" +
      '<div style="height:16px"></div>' + cta("Continue") + "</div>";
  }

  function vowHtml() {
    var vow = "Over the next 90 days, I, " + name() +
      ", vow to stay humble, disciplined, and resilient, no matter how tough it gets.\n\n" +
      "This No Processed Sugar program is my promise and a commitment to take back control and move forward.\n\n" +
      "Even if I stumble, I will rise again and keep going.\n\n" +
      "The next 90 days are not done for anyone else. They are a gift to myself— a step toward growth and transformation.";
    return '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<h2 class="headline">Read the Vow</h2>' +
      '<p class="subhead">Scroll to the bottom and sign below to continue</p>' +
      '<div class="vow">' + esc(vow) + "</div>" +
      '<div class="sign-box' + (state.signed ? " signed" : "") + '" data-act="sign">' +
      (state.signed ? "Signed!" : "Read the STOPPR vow and sign with your finger.") + "</div>" +
      '<p class="tiny-legal">*Your signature will not be registered/saved.</p>' +
      (state.signed ? cta("Continue") : "") + "</div></div>";
  }

  function card2Html() {
    return '<div class="congrats"><h2>Welcome to STOPPR,</h2>' +
      '<div class="lottie-standin">✨</div>' +
      "<h2>Hey, " + esc(name()) + ".</h2>" +
      '<p class="subhead">We\'ve built a plan just for you\nIt\'s designed to help you\nquit sugar forever.\n\nNow, it\'s time to invest\nin yourself.</p>' +
      cta("Continue") + "</div>";
  }

  function paywallHtml() {
    return '<div class="screen-scroll" style="height:100%;background:#120814;color:#fff">' +
      '<div class="pad" style="padding-top:62px">' +
      '<img src="' + A + '/images/onboarding/stars_wings.png" alt="" style="width:120px;display:block;margin:0 auto 8px">' +
      '<h2 class="headline" style="color:#fff;text-align:center;font-size:22px">We\'ve created a custom plan just for you, ' +
      esc(name()) + "</h2>" +
      '<p class="subhead" style="color:#cbb;text-align:center">You will quit sugar by:</p>' +
      '<div style="text-align:center"><span class="date-pill">' + quitDate() + "</span></div>" +
      '<p style="text-align:center;font-weight:800">Stronger. Healthier. Happier.</p>' +
      feat("Rewire your brain to prefer real food") +
      feat("Press the panic button when feeling tempted") +
      feat("Pledge daily to not relapse") +
      feat("Track progress towards betterment") +
      feat("Build unbreakable self control") +
      '<p class="subhead" style="color:#cbb;margin-top:16px">Simple, daily habits. STOPPR teaches 100% science-based habits that make lasting, life-long freedom from sugar possible.</p>' +
      '<div style="text-align:center;color:#ffd36a;letter-spacing:2px;margin:8px 0">★★★★★</div>' +
      cta("Become a STOPPR") +
      '<p class="tiny-legal" style="color:#aaa">Cancel Anytime · Money back guarantee<br>Purchase appears as \'STOPPR\' · Privacy Policy · Terms of Use<br>In the real app this button opens Superwall.</p>' +
      "</div></div>";
  }
  function feat(t) {
    return '<div class="pay-feature">✓ <span>' + t + "</span></div>";
  }

  function congratsHtml(s) {
    var text = s.data.text.replace("{name}", name());
    return '<button class="skip-top" data-act="jump-home" type="button">SKIP</button>' +
      '<div class="congrats" data-act="next"><h2>' + esc(text) + "</h2>" +
      '<div class="lottie-standin">' + s.data.emoji + "</div>" +
      (s.data.last ? cta("Let's go") : '<div class="tap-hint">TAP TO CONTINUE</div>') +
      "</div>";
  }

  function homeHtml() {
    return '<div style="height:100%;background:#fff;display:flex;flex-direction:column">' +
      '<div class="pad" style="flex:1"><p class="subhead">STOPPR</p>' +
      '<h2 class="headline">You\'ve been sugar-free for:</h2>' +
      '<div style="font-size:56px;font-weight:900">0 days</div>' +
      '<p class="subhead">Home tab after onboarding. Pledge, panic button, Melinda chat, and streak live here.</p></div>' +
      '<div style="display:flex;justify-content:space-around;padding:10px 8px 24px;border-top:1px solid #eee;font-size:11px;font-weight:700;color:#666">' +
      "<span style='color:#ed3272'>Home</span><span>Learn</span><span>Rewire</span><span>Community</span><span>Profile</span></div></div>";
  }

  function painHtml(s) {
    var d = s.data;
    return '<div class="pain ' + d.color + '"><div style="text-align:center;font-weight:800;letter-spacing:-0.04em;font-size:22px">STOPPR</div>' +
      '<div class="lottie-standin" style="margin:24px auto;background:rgba(255,255,255,0.12);color:#fff">' +
      (d.color === "blue" ? "🌱" : "🧠") + "</div>" +
      "<h2>" + esc(d.title) + "</h2><p>" + esc(d.body) + "</p>" +
      '<div style="margin-top:auto">' + dots(5) + cta("Next", "dark") + "</div></div>";
  }

  function benefitHtml(s) {
    var d = s.data;
    return '<div class="pain" style="background:linear-gradient(180deg,#1C072E,#09050C)">' +
      '<div style="text-align:center;font-weight:800;letter-spacing:-0.04em;font-size:22px">STOPPR</div>' +
      '<div class="lottie-standin" style="margin:24px auto">💜</div>' +
      "<h2>" + esc(d.title) + "</h2><p>" + esc(d.body) + "</p>" +
      '<div style="margin-top:auto">' + dots(6) + cta("Next", "dark") + "</div></div>";
  }

  function dots(n) {
    var h = '<div class="dots">';
    for (var i = 0; i < n; i++) h += '<span class="dot' + (i === 0 ? " on" : "") + '"></span>';
    return h + "</div>";
  }

  function blocksHtml(potential) {
    var keys = ["Overall", "Focus", "Confidence", "Energy", "Motivation", "Mood", "Relationships", "Libido", "Self-Control", "Pure Thoughts"];
    var html = '<div class="screen-scroll" style="height:100%"><div class="pad">' +
      '<h2 class="headline">' + (potential ? "Potential Rating" : "Your STOPPR Rating") + "</h2>" +
      '<p class="subhead">' + (potential
        ? "Based on your information, we believe you could reach at least a potential rating of 85 by completing a customised 10+ week no processed-sugar program."
        : "Based on your answers, this is your current STOPPR rating, which reflects your lifestyle and habits now.") + "</p>";
    keys.forEach(function (k, i) {
      var v = potential ? 80 + (i % 8) : 32 + (i * 3) % 28;
      html += '<div class="metric-row"><span>' + k + "</span><b>" + v + "</b></div>";
    });
    html += cta(potential ? "Continue" : "→ See potential rating", "square") + "</div></div>";
    return html;
  }

  function explain(s) {
    $("phase-pill").textContent = s.phase;
    $("explain-title").textContent = s.title;
    $("explain-body").innerHTML = "<p>" + esc(s.why) + "</p><p>" + esc(s.does) + "</p>";
    $("explain-kv").innerHTML =
      '<div><strong>What it stores</strong><span>' + esc(s.saves) + "</span></div>" +
      '<div><strong>Source file</strong><span>' + esc(s.source) + "</span></div>";
    $("explain-meta").innerHTML = "English copy is from STOPPR <code>assets/l10n/en.json</code> and the Dart onboarding screens. Videos, images, and Elza Round fonts are bundled in this <code>stoppr/assets</code> folder, so you can open <code>index.html</code> locally with no server.";
  }

  function buildToc() {
    var list = screens();
    var groups = [];
    var last = "";
    list.forEach(function (s, i) {
      if (s.phase !== last) {
        groups.push({ phase: s.phase, items: [] });
        last = s.phase;
      }
      groups[groups.length - 1].items.push({ s: s, i: i });
    });
    $("toc").innerHTML = groups.map(function (g) {
      return '<div class="toc-group"><h3>' + esc(g.phase) + "</h3>" +
        g.items.map(function (it) {
          return '<a href="#" data-jump="' + it.i + '" class="' +
            (it.i === state.index ? "current" : "") + (it.s.legacy ? " legacy" : "") + '">' +
            esc(it.s.title) + "</a>";
        }).join("") + "</div>";
    }).join("");
  }

  function persistProfile() {
    var n = $("name-input");
    var a = $("age-input");
    if (n) state.firstName = n.value;
    if (a) state.age = a.value;
  }

  function go(i) {
    persistProfile();
    var list = screens();
    if (i < 0) i = 0;
    if (i >= list.length) i = list.length - 1;
    if (i !== state.index) state.dialog = null;
    state.index = i;
    var s = list[i];
    $("screen-root").innerHTML = renderKind(s);
    explain(s);
    $("progress-label").textContent = (i + 1) + " / " + list.length;
    $("prev-btn").disabled = i === 0;
    $("next-btn").disabled = i === list.length - 1;
    buildToc();
    var bar = $("calc-bar");
    if (bar) {
      setTimeout(function () { bar.style.width = "100%"; bar.style.transition = "width 1.6s ease"; }, 40);
    }
  }

  function next() { go(state.index + 1); }
  function prev() { go(state.index - 1); }

  document.addEventListener("click", function (e) {
    var t = e.target.closest("[data-act], [data-jump]");
    if (!t) return;
    if (t.hasAttribute("data-jump")) {
      e.preventDefault();
      go(parseInt(t.getAttribute("data-jump"), 10));
      return;
    }
    var act = t.getAttribute("data-act");
    if (act === "next") next();
    if (act === "skip-dialog") { state.dialog = "skip"; go(state.index); }
    if (act === "dialog-cancel") { state.dialog = null; go(state.index); }
    if (act === "why") { state.dialog = "why"; go(state.index); }
    if (act === "sign") { state.signed = true; go(state.index); }
    if (act === "jump-home") {
      var list = screens();
      var hi = list.findIndex(function (s) { return s.id === "home"; });
      go(hi >= 0 ? hi : list.length - 1);
    }
    if (act === "opt") {
      var qid = parseInt(t.getAttribute("data-q"), 10);
      var i = parseInt(t.getAttribute("data-i"), 10);
      var multi = t.getAttribute("data-multi") === "1";
      var cur = state.answers[qid] ? state.answers[qid].slice() : [];
      var ix = cur.indexOf(i);
      if (multi) {
        if (ix >= 0) cur.splice(ix, 1); else cur.push(i);
      } else {
        cur = [i];
      }
      state.answers[qid] = cur;
      persistProfile();
      go(state.index);
    }
    if (act === "set") {
      state[t.getAttribute("data-k")] = t.getAttribute("data-v");
      go(state.index);
    }
    if (act === "sym") {
      var v = t.getAttribute("data-v");
      state.symptoms[v] = !state.symptoms[v];
      go(state.index);
    }
    if (act === "goal") {
      var g = t.getAttribute("data-v");
      state.goals[g] = !state.goals[g];
      go(state.index);
    }
  });

  $("prev-btn").addEventListener("click", prev);
  $("next-btn").addEventListener("click", next);
  $("path-main").addEventListener("click", function () {
    state.path = "main";
    this.classList.add("active");
    $("path-all").classList.remove("active");
    go(Math.min(state.index, STOPPR_MAIN.length - 1));
  });
  $("path-all").addEventListener("click", function () {
    state.path = "all";
    this.classList.add("active");
    $("path-main").classList.remove("active");
    go(state.index);
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "ArrowRight") next();
    if (e.key === "ArrowLeft") prev();
  });

  go(0);
})();
