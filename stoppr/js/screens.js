/* STOPPR onboarding screen catalog.
   Copy and order traced from lib/features/onboarding and assets/l10n/en.json. */
window.STOPPR_PATHS = {
  main: null,
  all: null,
};

(function () {
  var Q = [
    {
      id: 1, multi: true,
      text: "What frustrates you most about your relationship with sugar?",
      options: [
        ["I know I should eat less but it's hard to resist", "You're not alone in this struggle! Learn simple strategies to make resisting easier by understanding your triggers and building healthier habits."],
        ["Sugar is my go-to for comfort or energy", "Find better energy sources! Explore healthier alternatives that truly comfort and energize you without the sugar crash."],
        ["I keep telling myself I'll cut back, but never do", "Turn intention into action! Learn practical steps to finally follow through on your commitment to cut back on sugar."]
      ]
    },
    {
      id: 2, multi: false,
      text: "How do you typically feel after eating sugar?",
      options: [
        ["I feel regretful and wish I hadn't eaten it", "Transform regret into awareness! Learn to recognize patterns before they happen and build the skills to make choices you'll feel good about."],
        ["I feel fine at first, but then crash and feel sluggish", "Break free from the energy rollercoaster! Discover how to maintain stable energy throughout the day without relying on sugar spikes."],
        ["It triggers more cravings and I want even more", "End the craving cycle! Understand why sugar triggers more cravings and learn effective strategies to break this addictive pattern."]
      ]
    },
    {
      id: 3, multi: true,
      text: "Which area of your life has been most affected by your sugar habits?",
      options: [
        ["My confidence and self-image", "Rebuild your self-worth! Discover how conquering sugar can restore confidence and transform how you see yourself."],
        ["My energy levels and mood", "Reclaim your vitality! Learn how stable blood sugar leads to consistent energy and balanced emotions throughout the day."],
        ["My physical health and appearance", "Transform from the inside out! Experience the visible and invisible health improvements that come with reducing sugar."],
        ["My relationships with food and eating", "Heal your relationship with food! Create a peaceful, balanced approach to eating that serves your wellbeing."]
      ]
    },
    {
      id: 4, multi: true,
      text: "When do you most struggle with sugar cravings?",
      options: [
        ["When I'm stressed or overwhelmed", "Stress relief without the sugar crash! Learn powerful coping strategies that actually reduce stress instead of adding to it."],
        ["When I'm feeling sad or lonely", "Emotional comfort that truly heals! Discover healthier ways to soothe difficult feelings and build emotional resilience."],
        ["Late at night", "Break the late-night sugar cycle! Discover healthy evening routines and alternatives that satisfy you without the sugar crash."],
        ["When I'm bored or procrastinating", "Turn boredom into breakthrough! Replace mindless snacking with engaging activities that energize and motivate you."]
      ]
    },
    {
      id: 6, multi: true,
      text: "What's the biggest consequence you've experienced from sugar consumption?",
      options: [
        ["Feeling disappointed in myself repeatedly", "End the disappointment cycle! Learn how to set achievable goals and celebrate progress that builds lasting self-respect."],
        ["Physical symptoms that interfere with my day", "Reclaim your physical wellbeing! Address the root cause and watch troublesome symptoms improve as you reduce sugar."],
        ["Avoiding social situations because of my eating habits", "Reconnect with confidence! Learn strategies to enjoy social situations without compromising your health or happiness."],
        ["Feeling like I'm not living up to my potential", "Unleash your true potential! Discover how conquering sugar unlocks energy, focus, and motivation you didn't know you had."]
      ]
    },
    {
      id: 7, multi: true,
      text: "How would you feel if you had complete control over sugar cravings?",
      options: [
        ["Proud and confident in my self-discipline", "That pride is within reach! Build unshakeable confidence through proven strategies that strengthen your self-discipline daily."],
        ["Free and liberated from constant mental battles", "Freedom awaits! Break free from the mental exhaustion of constant cravings and reclaim your headspace for what matters."],
        ["Excited about reaching my health goals", "Your excitement is contagious! Channel that energy into concrete steps that bring your health goals closer every day."],
        ["Calm and at peace with my food choices", "That peace is possible! Develop a calm, balanced relationship with food that feels natural and effortless."]
      ]
    },
    {
      id: 8, multi: true,
      text: "What would achieving your sugar goals mean to you personally?",
      options: [
        ["Proving to myself that I can stick to my commitments", "Your word to yourself matters! Build unbreakable self-trust through small wins that prove your incredible commitment power."],
        ["Setting a good example for my family/loved ones", "Lead by example! Your journey inspires others and creates a ripple effect of positive change in your family's health."],
        ["Finally feeling comfortable in my own skin", "That comfort is your birthright! Discover how conquering sugar brings the self-acceptance you've been searching for."],
        ["Having the energy to pursue my dreams", "Dreams need energy to become reality! Unlock the sustained vitality that fuels your biggest aspirations and goals."]
      ]
    },
    {
      id: 9, multi: false,
      text: "How do you typically handle social situations with sugary foods?",
      options: [
        ["I give in to avoid making things awkward", "Your health isn't difficult - it's essential! Learn confident ways to prioritize your wellbeing without feeling awkward or apologetic."],
        ["I feel anxious and uncomfortable the whole time", "Turn anxiety into confidence! Master social situations with calm, effective strategies that keep you comfortable and committed."],
        ["I stick to my goals but feel left out", "Belong while staying true to yourself! Learn how to feel included and connected while honoring your health commitments."]
      ]
    },
    {
      id: 10, multi: false,
      text: "How do you currently see yourself in relation to sugar?",
      options: [
        ["As someone who lacks willpower", "You have more willpower than you think! Discover how to tap into your inner strength and build unshakeable self-control."],
        ["As someone who's trying but struggling", "Your effort is already victory! Transform that struggle into success with proven strategies that actually work."],
        ["As someone who's on a journey of improvement", "Perfect mindset for lasting change! Continue your growth journey with tools that accelerate your progress and success."],
        ["On the right track, just need extra tools and support", "You know what you need - that's wisdom! Get the exact tools and support system designed for your success."]
      ]
    },
    {
      id: 11, multi: true,
      text: "What kind of support would make the biggest difference for you?",
      options: [
        ["Understanding why I crave sugar in the first place", "Knowledge brings freedom! Uncover the root causes behind your cravings and gain the power to address them effectively."],
        ["Get healthy alternatives to my sugary foods", "Be prepared when cravings strike! Get instant healthy food alternatives that satisfy your cravings without the sugar."],
        ["Connecting with others who understand my struggle", "Community changes everything! Connect with people who truly understand your journey and cheer you on to success."],
        ["Tracking my progress to stay motivated", "See your wins add up! Visual progress tracking keeps you motivated and celebrates every step forward on your journey."]
      ]
    },
    {
      id: 12, multi: false, acquisition: true,
      text: "How did you know about STOPPR?",
      options: [
        ["TikTok", "Great! We'll provide content similar to what you've seen on TikTok to keep you engaged and motivated.", "tiktok.svg"],
        ["Instagram", "Perfect! Our visual approach to tracking progress will feel familiar to your Instagram experience.", "instagram.svg"],
        ["Google", "Excellent! We'll provide the comprehensive information and resources you're looking for.", "google.svg"],
        ["Reddit", "Great! We'll share community-driven tips and resources to support you.", "reddit.svg"],
        ["Youtube", "Awesome! We'll include video content to help you on your sugar-free journey.", "youtube.svg"],
        ["Friend or Family", "Wonderful! Community support is important, and we'll help you connect with others on this journey.", "friends-or-family.svg"],
        ["Other", "Thanks for joining us! We're glad you found STOPPR and are ready to help you reduce your sugar intake.", "other.svg"]
      ]
    },
    {
      id: 13, multi: false,
      text: "What is your gender?",
      options: [
        ["Male", "We'll tailor your sugar-reduction experience to address typical challenges faced by men."],
        ["Female", "We'll customize your experience to address hormonal fluctuations and other factors specific to women."],
        ["Other", "We'll provide a personalized experience focused on your unique health goals and challenges."]
      ]
    }
  ];

  function qScreen(q, page, total) {
    return {
      id: "q" + q.id,
      phase: "Questionnaire",
      title: "Question " + q.id,
      kind: "question",
      source: "lib/features/onboarding/presentation/screens/questionnaire_screen.dart",
      why: "The quiz is the core of the funnel. Answers are saved locally and later written to Firestore as users/{uid}/onboarding/questionnaire (plus consumption and acquisition docs). Coaching blurbs appear under a selected option to reframe the struggle as solvable.",
      does: "User picks one or more answers, then Continue. Skip test shows a confirmation dialog.",
      saves: "SharedPreferences questionnaire index + answers; Firestore onboarding/questionnaire",
      data: { q: q, page: page, total: total }
    };
  }

  function pain(id, title, body, color, nextNote) {
    return {
      id: id,
      phase: "Legacy: sugar pain points",
      title: title,
      kind: "pain",
      legacy: true,
      source: "lib/features/onboarding/presentation/screens/onboarding_sugar_painpoints_page_view.dart",
      why: "Older first-launch path used five red/blue education cards after symptoms. Current source now routes symptoms → recipe video → nutrition instead. These screens still exist for resume.",
      does: nextNote,
      saves: "painpoints page index in SharedPreferences",
      data: { title: title, body: body, color: color }
    };
  }

  function benefit(id, title, body) {
    return {
      id: id,
      phase: "Legacy: benefits",
      title: title,
      kind: "benefit",
      legacy: true,
      source: "lib/features/onboarding/presentation/screens/benefits_page_view.dart",
      why: "Six dark benefit pages sold the product after pain points. Last page used to continue into RewireBenefits, which is still on the live path.",
      does: "Swipe/Next through feature promises with Lottie heroes.",
      saves: "benefits page index",
      data: { title: title, body: body }
    };
  }

  function congrats(n, text, emoji) {
    return {
      id: "congrats-" + n,
      phase: "After purchase",
      title: "Congratulations " + n + " of 8",
      kind: "congrats",
      source: "lib/features/onboarding/presentation/screens/congratulations/congratulations_screen_" + n + ".dart",
      why: n === 1
        ? "Purchase success lands here. Streak is reset, first-use date is stored, onboarding music stops. SKIP jumps to the main app."
        : "Short emotional carousel after payment before the home shell.",
      does: n === 8 ? "Let's go → MainScaffold(fromCongratulations: true)." : "Tap anywhere / TAP TO CONTINUE.",
      saves: n === 1 ? "coming_from_congratulations, streak reset, first_app_use_date_iso" : "none",
      data: { text: text, emoji: emoji, last: n === 8 }
    };
  }

  var main = [
    {
      id: "welcome-video",
      phase: "Welcome",
      title: "Welcome video",
      kind: "welcome-video",
      source: "lib/features/onboarding/presentation/screens/welcome_video_screen.dart",
      why: "First surface after splash. Cold start always wraps the next target in WelcomeVideoScreen. There is no skip control — it auto-advances after the video (~5s) or on error.",
      does: "Plays a full-screen video, then hands off to OnboardingPage.",
      saves: "none (unless redownload feedback ran first)",
      data: {
        video: "videos/daily_widget.mp4",
        lines: ["Embrace this pause.", "Reflect before you relapse."]
      }
    },
    {
      id: "start-quiz",
      phase: "Welcome",
      title: "Start a healthier life",
      kind: "start-quiz",
      source: "lib/features/onboarding/presentation/screens/onboarding_screen2.dart",
      why: "First marketing card. Social proof (250,000+), 528Hz onboarding audio, language selector, and a mockup video of the app. Continue is labeled Continue (not the older Find Out Now key).",
      does: "Continue advances the OnboardingPage PageView to FOMO stats. Sound + language stay as overlays.",
      saves: "none yet",
      data: {}
    },
    {
      id: "fomo",
      phase: "Welcome",
      title: "FOMO stats",
      kind: "fomo",
      source: "lib/features/onboarding/presentation/screens/onboarding_fomo_stats_screen.dart",
      why: "Social-proof screen before asking for an account. Numbers are hardcoded marketing copy, not live analytics.",
      does: "Continue → auth / Become a STOPPR.",
      saves: "none",
      data: {}
    },
    {
      id: "auth",
      phase: "Welcome",
      title: "Become a STOPPR (auth)",
      kind: "auth",
      source: "lib/features/onboarding/presentation/screens/onboarding_screen3.dart",
      why: "Account gate. Apple/Google/Email or Skip. Skip and free Apple/Google both go to the radar screen. Paid returning users jump to MainScaffold. Email signup can skip radar+weeks and land on the quiz.",
      does: "This walkthrough follows Skip → radar, the unpaid first-launch path.",
      saves: "Firebase Auth if they sign in; skip creates anonymous auth later on profile",
      data: {}
    },
    {
      id: "radar",
      phase: "Progress promise",
      title: "Radar progress tracker",
      kind: "radar",
      source: "lib/features/onboarding/presentation/screens/onboarding_screen5_radar.dart",
      why: "Shows an empty (0%) life-domain radar so later week screens can animate growth. Introduces the tracker metaphor used in the rest of the product.",
      does: "Continue → WeeksProgressionScreen (pushAndRemoveUntil).",
      saves: "none",
      data: { week: "now", values: [0.18, 0.16, 0.2, 0.15, 0.17, 0.14] }
    },
    {
      id: "week-1",
      phase: "Progress promise",
      title: "Week 1 — starting out",
      kind: "weeks",
      source: "lib/features/onboarding/presentation/screens/weeks_progression_screen.dart",
      why: "Four-step animation of the same radar filling in. Sells a 13-week transformation before any questions.",
      does: "Continue through Week 1 → 5 → 10 → 13, then QuestionnaireScreen.",
      saves: "OnboardingScreen.weeksProgressionScreen",
      data: {
        week: "Week 1",
        desc: "You're just starting out—your beginning is a bit weak.",
        color: "#E53E3E",
        values: [0.22, 0.2, 0.24, 0.18, 0.21, 0.19]
      }
    },
    {
      id: "week-5",
      phase: "Progress promise",
      title: "Week 5 — improving",
      kind: "weeks",
      source: "lib/features/onboarding/presentation/screens/weeks_progression_screen.dart",
      why: "Same radar, stronger scores. Keeps the user imagining future self before the quiz.",
      does: "Continue to week 10.",
      saves: "weeks page index",
      data: {
        week: "Week 5",
        desc: "You're improving now—keep up the great work!",
        color: "#ED8936",
        values: [0.45, 0.5, 0.48, 0.42, 0.47, 0.44]
      }
    },
    {
      id: "week-10",
      phase: "Progress promise",
      title: "Week 10 — mastered",
      kind: "weeks",
      source: "lib/features/onboarding/presentation/screens/weeks_progression_screen.dart",
      why: "Near-complete radar. Copy claims mastery across domains.",
      does: "Continue to week 13.",
      saves: "weeks page index",
      data: {
        week: "Week 10",
        desc: "You've mastered all areas—your improvement is clear!",
        color: "#6B46C1",
        values: [0.78, 0.82, 0.8, 0.76, 0.81, 0.77]
      }
    },
    {
      id: "week-13",
      phase: "Progress promise",
      title: "Week 13 — peak",
      kind: "weeks",
      source: "lib/features/onboarding/presentation/screens/weeks_progression_screen.dart",
      why: "Peak transformation claim, then the quiz begins.",
      does: "Continue → QuestionnaireScreen.",
      saves: "weeks page index",
      data: {
        week: "Week 13",
        desc: "You've reached peak transformation—all life domains are maximized!",
        color: "#38A169",
        values: [0.96, 0.98, 0.97, 0.95, 0.99, 0.96]
      }
    },
    qScreen(Q[0], 1, 16),
    qScreen(Q[1], 2, 16),
    qScreen(Q[2], 3, 16),
    qScreen(Q[3], 4, 16),
    {
      id: "break",
      phase: "Questionnaire",
      title: "Progress break graph",
      kind: "break",
      source: "lib/features/onboarding/presentation/screens/sugar_progress_break_screen.dart",
      why: "Interrupts the quiz after Q4 with a future-self graph (3 / 7 / 30 days). Health copy is marketing; VibeKB treats medical accuracy as out of scope.",
      does: "Continue resumes questions via consumption summary next.",
      saves: "questionnaire index 4",
      data: {}
    },
    {
      id: "consumption",
      phase: "Questionnaire",
      title: "Typical week (consumption)",
      kind: "consumption",
      source: "lib/features/onboarding/presentation/screens/consumption_summary_screen.dart",
      why: "Collects pattern + treat size, then estimates weekly/quarter/year calories from sugar. There is no Q5 in the model — this is the 5th quiz page. Continue opens Insights as a push.",
      does: "Pick Daily or Every few days, pick treat size, Continue → Insights.",
      saves: "Firestore onboarding/consumption",
      data: {}
    },
    {
      id: "insights",
      phase: "Questionnaire",
      title: "Your first 30 days",
      kind: "insights",
      source: "lib/features/onboarding/presentation/screens/insights_screen.dart",
      why: "Personalizes promised outcomes (weight, sleep, skin) from the consumption answers. Continue returns to the questionnaire PageView (Q6).",
      does: "Continue to question 6.",
      saves: "none extra",
      data: {}
    },
    qScreen(Q[4], 7, 16),
    qScreen(Q[5], 8, 16),
    qScreen(Q[6], 9, 16),
    qScreen(Q[7], 10, 16),
    qScreen(Q[8], 11, 16),
    qScreen(Q[9], 12, 16),
    qScreen(Q[10], 13, 16),
    qScreen(Q[11], 14, 16),
    {
      id: "profile",
      phase: "Profile",
      title: "Name and age",
      kind: "profile",
      source: "lib/features/onboarding/presentation/screens/profile_info_screen.dart",
      why: "Last questionnaire page. May create anonymous Firebase Auth. Writes profile fields, then clears earlier local progress and continues. If answers exist → CalculatingScreen; if none → Symptoms.",
      does: "Complete Quiz.",
      saves: "users/{uid} first name/age; clears onboarding progress keys",
      data: {}
    },
    {
      id: "calculating",
      phase: "Analysis",
      title: "Analyzing your sweet tooth",
      kind: "calculating",
      source: "lib/features/onboarding/presentation/screens/calculating_screen.dart",
      why: "Fake analysis animation (~6s) that makes the next score feel computed. Steps cycle through understanding / relapse triggers / finalizing.",
      does: "Auto-advances to AnalysisResultScreen.",
      saves: "OnboardingScreen.analysisResultScreen",
      data: {}
    },
    {
      id: "analysis",
      phase: "Analysis",
      title: "Analysis complete",
      kind: "analysis",
      source: "lib/features/onboarding/presentation/screens/analysis_result_screen.dart",
      why: "Presents a hardcoded dependence chart (Your Score 73%, Average 32%, safety line 41%). Copy says it is not a medical diagnosis.",
      does: "Check your symptoms → SymptomsScreen.",
      saves: "none extra",
      data: {}
    },
    {
      id: "symptoms",
      phase: "Analysis",
      title: "Day-to-day symptoms",
      kind: "symptoms",
      source: "lib/features/onboarding/presentation/screens/symptoms_screen.dart",
      why: "Multi-select mental / physical / social / faith symptoms saved under onboarding/symptoms. Current forward path then goes to the recipes mockup, not the old pain-points carousel.",
      does: "Continue → MockRecipesVideoScreen.",
      saves: "users/{uid}/onboarding/symptoms",
      data: {}
    },
    {
      id: "recipes",
      phase: "Nutrition onboarding",
      title: "Healthy meals mockup",
      kind: "video-cta",
      source: "lib/features/onboarding/presentation/screens/mock_recipes_video_screen.dart",
      why: "Sells the calorie tracker with a product video before collecting body metrics.",
      does: "Continue → WorkoutsPerWeekScreen.",
      saves: "none",
      data: {
        video: "videos/mock_ob_recipes.mp4",
        title: "Add healthy meals to your calories tracker"
      }
    },
    {
      id: "workouts",
      phase: "Nutrition onboarding",
      title: "Workouts per week",
      kind: "workouts",
      source: "lib/features/nutrition/presentation/onboarding/screens/workouts_per_week_screen.dart",
      why: "Step 1 of 4 calorie-plan inputs. Activity level feeds TDEE-style goal math.",
      does: "Next → Height & weight.",
      saves: "later written with body metrics / daily_goals",
      data: {}
    },
    {
      id: "height-weight",
      phase: "Nutrition onboarding",
      title: "Height & weight",
      kind: "height-weight",
      source: "lib/features/nutrition/presentation/onboarding/screens/height_weight_screen.dart",
      why: "Step 2 of 4. Metric/imperial. Used to calibrate the custom plan.",
      does: "Next → goal selection.",
      saves: "body metrics",
      data: {}
    },
    {
      id: "goal-select",
      phase: "Nutrition onboarding",
      title: "Calorie goal",
      kind: "goal-select",
      source: "lib/features/nutrition/presentation/onboarding/screens/goal_selection_screen.dart",
      why: "Step 3 of 4. Lose / maintain / gain. isOnboarding: true routes to ResultsCaloriesOnboardingScreen.",
      does: "Continue → results.",
      saves: "nutrition goal",
      data: {}
    },
    {
      id: "calories",
      phase: "Nutrition onboarding",
      title: "Personalized meals plan",
      kind: "calories",
      source: "lib/features/nutrition/presentation/onboarding/screens/results_calories_onboarding_screen.dart",
      why: "Step 4 of 4. Shows computed calories/macros/processed sugar/health score and stores daily_goals under users/{uid}/nutrition_profile/.",
      does: "Continue → RewireBenefitsScreen.",
      saves: "users/{uid}/nutrition_profile/daily_goals",
      data: {}
    },
    {
      id: "experts",
      phase: "Social proof",
      title: "Experts & influencers",
      kind: "experts",
      source: "lib/features/onboarding/presentation/screens/rewiring_benefits.dart",
      why: "Celebrity/expert-style cards (Huberman, Lovato, Lizzo, plus user photos). Not in-app verified quotes — marketing copy from l10n.",
      does: "Continue → chart screen.",
      saves: "none",
      data: {}
    },
    {
      id: "chart",
      phase: "Social proof",
      title: "76% faster chart",
      kind: "chart",
      source: "lib/features/onboarding/presentation/screens/rewiring_benefits_2_chart.dart",
      why: "Claims STOPPR helps quit 76% faster than willpower, plus four percentage benefits and six paper titles. Copy only — not computed from the user's answers.",
      does: "Continue → accountability mockup.",
      saves: "none",
      data: {}
    },
    {
      id: "buddy",
      phase: "Social proof",
      title: "Accountability buddy",
      kind: "video-cta",
      source: "lib/features/onboarding/presentation/screens/mockup_ob_accountability_screen.dart",
      why: "Sells pairing with a Stoppr buddy before asking for notifications.",
      does: "Continue → notifications permission.",
      saves: "none",
      data: {
        video: "videos/mockup_accountability.mp4",
        title: "We'll help you find your Stoppr buddy to quit sugar"
      }
    },
    {
      id: "notifications",
      phase: "Permissions",
      title: "Notification permission",
      kind: "notifications",
      source: "lib/features/onboarding/presentation/screens/onboarding_notifications_permission.dart",
      why: "Aggressive permission ask with an iOS-style dialog mock and schedule pickers for Motivation and Pledge reminder. Allow or Don't Allow both continue.",
      does: "Advances to StopprScienceBackedPlanScreen.",
      saves: "notification times in prefs; FCM permission on device",
      data: {}
    },
    {
      id: "science",
      phase: "Commitment",
      title: "Science-backed plan",
      kind: "science",
      source: "lib/features/onboarding/presentation/screens/stoppr_science_backed_plan.dart",
      why: "Harvard / UCL / Atomic Habits quotes to justify a ~90-day habit window before ratings and paywall.",
      does: "Next → GiveUsRatingsScreen.",
      saves: "OnboardingScreen.stopprScienceBackedPlanScreen",
      data: {}
    },
    {
      id: "ratings",
      phase: "Commitment",
      title: "Help us help more people",
      kind: "ratings",
      source: "lib/features/onboarding/presentation/screens/give_us_ratings_screen.dart",
      why: "Asks for a 5-star rating mid-funnel. Continue also registers a Superwall soft paywall; dismiss/error still proceeds to goals.",
      does: "Continue → ChooseGoalsOnboardingScreen.",
      saves: "OnboardingScreen.giveUsRatingsScreen",
      data: {}
    },
    {
      id: "goals",
      phase: "Commitment",
      title: "Choose goals",
      kind: "goals",
      source: "lib/features/onboarding/presentation/screens/choose_goals_onboarding.dart",
      why: "Multi-select life goals stored as onboarding/goals. These feed later home tracking, not the calorie plan.",
      does: "Track these goals → ReferralCodeScreen.",
      saves: "users/{uid}/onboarding/goals",
      data: {}
    },
    {
      id: "referral",
      phase: "Commitment",
      title: "Referral code",
      kind: "referral",
      source: "lib/features/onboarding/presentation/screens/referral_code_screen.dart",
      why: "Optional promo. Empty Skip or valid Continue both go to the letter. Some Apple promo codes can jump to congratulations.",
      does: "Skip / Continue → LetterFromFutureScreen.",
      saves: "promo if valid",
      data: {}
    },
    {
      id: "letter",
      phase: "Commitment",
      title: "Letter from future you",
      kind: "letter",
      source: "lib/features/onboarding/presentation/screens/letter_from_future_screen.dart",
      why: "Personalizes with the name from profile. Emotional close before the vow.",
      does: "Continue → ReadTheVowScreen.",
      saves: "OnboardingScreen.letterFromFutureScreen",
      data: {}
    },
    {
      id: "vow",
      phase: "Commitment",
      title: "Read the vow",
      kind: "vow",
      source: "lib/features/onboarding/presentation/screens/read_the_vow_screen.dart",
      why: "90-day no-processed-sugar vow. Signature is local UI only — copy says it is not saved.",
      does: "Sign, then Continue → OnboardingCard2Screen.",
      saves: "none (signature explicitly not stored)",
      data: {}
    },
    {
      id: "card2",
      phase: "Paywall",
      title: "Plan for you / invest in yourself",
      kind: "card2",
      source: "lib/features/onboarding/presentation/screens/onboarding_card_2.dart",
      why: "Auto-playing text sequence that names the user and frames the upcoming Superwall purchase as investing in yourself.",
      does: "Auto-advances to PrePaywallScreen.",
      saves: "OnboardingScreen.prePaywallScreen",
      data: {}
    },
    {
      id: "paywall",
      phase: "Paywall",
      title: "Pre-paywall / Become a STOPPR",
      kind: "paywall",
      source: "lib/features/onboarding/presentation/screens/pre_paywall.dart",
      why: "Last owned UI before Superwall. Lists features, a target quit date, reviews, cancel/money-back, then registers Superwall placements (many are INSERT_YOUR_* placeholders in source). Purchase success → Congratulations 1. This recreation shows the in-app shell, not the live Superwall dashboard UI.",
      does: "Become a STOPPR would open Superwall. Here it continues the walkthrough as if purchase succeeded.",
      saves: "subscription via RevenueCat on success; onboardingCompleted after paid verify",
      data: {}
    },
    congrats(1, "Whenever you need us,\nwe're right here, {name}.", "🚀"),
    congrats(2, "Every step you take is a\nstep toward growth", "🌱"),
    congrats(3, "Nice work, {name}! You're\noff to a great start.", "🎉"),
    congrats(4, "... and sometimes there are\nbumps along the way", "🎸"),
    congrats(5, "Either way, this is the\nstart of something\nbrave.", "🤝"),
    congrats(6, "Keep yourself centered\nand in the moment.", "🧘"),
    congrats(7, "We're proud of how\nfar you've come", "🦊"),
    congrats(8, "Whenever you need us,\nwe're right here", "💛"),
    {
      id: "home",
      phase: "Main app",
      title: "Main scaffold (home)",
      kind: "home",
      source: "lib/features/app/presentation/screens/main_scaffold.dart",
      why: "Post-paywall shell. Tabs: Home, Learn Videos, Rewire Brain, Community, Profile. Onboarding is marked complete for authenticated paid users.",
      does: "Daily loop: streak, pledge, panic. This walkthrough stops here.",
      saves: "onboarding_completed / onboardingCompleted",
      data: {}
    }
  ];

  var legacy = [
    {
      id: "legacy-note",
      phase: "Legacy / resume",
      title: "Screens not on current first launch",
      kind: "legacy-note",
      legacy: true,
      source: "symptoms_screen.dart now navigates to MockRecipesVideoScreen",
      why: "These screens still exist in the app and can appear if onboarding is resumed from an old checkpoint. They are not on the current unpaid first-launch Navigator chain after symptoms.",
      does: "Use the list to inspect the old education + rating path.",
      saves: "n/a",
      data: {}
    },
    pain("pain-drug", "Sugar is a drug", "Like other addictive substances, sugar triggers the release of dopamine, the \"feel good\" chemical that activates the brain's reward and pleasure centers.", "red", "Next → relationships."),
    pain("pain-rel", "Sugar destroys relationships", "Sugar can impact your mood and energy levels, leading to irritability and fatigue, which can strain your relationships and reduce your ability to connect meaningfully with others.", "red", "Next → sex drive."),
    pain("pain-sex", "Sugar shatters sex drive", "Research links high sugar intake to hormonal imbalances, insulin resistance, and reduced blood flow, all of which can negatively impact sex drive and performance.", "red", "Next → unhappiness."),
    pain("pain-unhappy", "Feeling unhappy?", "Elevated dopamine levels means you need more dopamine to feel good. This is why many heavy sugar consumers report feeling depressed, unmotivated, and socially withdrawn.", "red", "Next → recovery path."),
    pain("pain-recover", "Path to recovery", "Recovery is possible. By reducing sugar consumption, your brain can reset its dopamine sensitivity, leading to better mood, increased energy, and improved overall health.", "blue", "Next used to go to BenefitsPageView."),
    benefit("ben-welcome", "Welcome to STOPPR", "With over 250,000 users, STOPPR is class-leading and based on years of research and user-interaction."),
    benefit("ben-rewire", "Rewire your brain", "Science-backed exercises help you rewire your brain, rebuild your dopamine receptors, and avoid setbacks."),
    benefit("ben-mot", "Staying motivated", "Guided exercises, daily reminders and new techniques help you stay motivated and take control of your cravings."),
    benefit("ben-set", "Avoid setbacks", "Learn proven techniques to beat cravings, identify triggers, and maintain your progress even during challenging times."),
    benefit("ben-conq", "Conquer yourself", "Master your willpower and discover the strength within you to overcome tough challenges and addictive behaviors."),
    benefit("ben-level", "Level up life", "Experience improved confidence, energy, focus, and productivity as you transform your relationship with sugar."),
    {
      id: "blocks",
      phase: "Legacy / resume",
      title: "Current STOPPR rating",
      kind: "blocks",
      legacy: true,
      source: "lib/features/onboarding/presentation/screens/current_6_blocks_rating_screen.dart",
      why: "Enum exists; no constructor call on the current first-launch chain. Would show 10 lifestyle scores.",
      does: "See potential rating → PotentialRatingScreen.",
      saves: "OnboardingScreen.current6BlocksRatingScreen",
      data: {}
    },
    {
      id: "potential",
      phase: "Legacy / resume",
      title: "Potential rating 85",
      kind: "potential",
      legacy: true,
      source: "lib/features/onboarding/presentation/screens/potential_rating_screen.dart",
      why: "Promise of 85+ after a 10+ week no-processed-sugar program.",
      does: "Continue (historically toward referral).",
      saves: "OnboardingScreen.potentialRatingScreen",
      data: {}
    },
    {
      id: "email-screen4",
      phase: "Legacy / resume",
      title: "Onboarding screen 4 (email path)",
      kind: "radar",
      legacy: true,
      source: "lib/features/onboarding/presentation/screens/onboarding_screen4.dart",
      why: "Used after email signup. With onNext it jumps to the questionnaire and skips radar + weeks. Without onNext it falls through to radar.",
      does: "Continue would call the provided onNext or radar.",
      saves: "OnboardingScreen.onboardingScreen4",
      data: { week: "now", values: [0.18, 0.16, 0.2, 0.15, 0.17, 0.14], email: true }
    }
  ];

  window.STOPPR_QUESTIONS = Q;
  window.STOPPR_MAIN = main;
  window.STOPPR_LEGACY = legacy;
})();
