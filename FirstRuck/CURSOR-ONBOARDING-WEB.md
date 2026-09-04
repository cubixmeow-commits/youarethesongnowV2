# Cursor build brief: First Ruck beginner onboarding lab

**Status:** Authorized web prototype after research  
**Prepared:** 2026-09-04  
**Builder:** Cursor  
**Reviewers:** CuBiX Meow, Brut, and Codex  
**Reference quality:** the local `stoppr/` onboarding walkthrough  
**Target:** a self-contained First Ruck web walkthrough for product and visual review before the complete Flutter/iOS implementation

---

## 1. Read this first

Build a new, original First Ruck onboarding experience with the visual care, pacing, motion, and reviewability demonstrated by `stoppr/`. STOPPR is a reference for production quality and funnel mechanics. It is not a template to reskin.

Do not reuse STOPPR copy, images, videos, fonts, logos, health claims, testimonials, statistics, colors, or brand identity. Do not convert quitting sugar questions into rucking questions one for one. First Ruck has a different job: help a new adult move from curiosity to a conservative, understandable first loaded walk and an appropriate starter route.

Read these repository files before editing:

1. `AGENTS.md`
2. `FirstRuck/README.md`
3. `FirstRuck/docs/flutter/product-contract.md`
4. `FirstRuck/docs/flutter/screen-flow.md`
5. `FirstRuck/docs/flutter/design-system.md`
6. `FirstRuck/brand/BRAND-SYSTEM.md`
7. `FirstRuck/brand/IMAGE-PROMPTS.md`
8. `FirstRuck/src/RecommendationEngine.php`
9. `stoppr/index.html`
10. `stoppr/js/screens.js`
11. `stoppr/js/app.js`
12. `stoppr/css/app.css`

The current First Ruck web application and Flutter Milestone 1 are working. Preserve both.

## 2. Exact deliverable and isolation boundary

Work only inside the existing repository folder named `FirstRuck`. Create a self-contained review prototype here:

```text
FirstRuck/onboarding-lab/
  index.html
  css/
    app.css
  js/
    model.js
    screens.js
    app.js
  assets/
    README.md
    brand/ (copy approved assets used from `../../brand/assets/`)
  tests/
    onboarding.test.js
  README.md
```

Requirements:

- Opening `FirstRuck/onboarding-lab/index.html` directly in Safari, Chrome, or Vivaldi must work without a web server.
- Use only relative local paths.
- Use HTML, CSS, and vanilla JavaScript.
- Do not add npm, a bundler, a framework, a CDN, analytics, trackers, or remote fonts.
- Do not modify `FirstRuck/public/`, `FirstRuck/src/`, `FirstRuck/database/`, `FirstRuck/mobile/`, `public/FirstRuck/`, or `stoppr/` in this build.
- Do not create a sibling `FirstRuck`, `First-Ruck`, `firstruck`, or `stoppr` project. The exact working boundary is `<repository root>/FirstRuck/`.
- Do not connect the PHP API, a map provider, geolocation, AI, Adapty, or any purchase SDK.
- Do not request browser permissions.
- Do not present any route as real. Route cards must say `Demonstration route`.
- Do not publish or deploy the lab in this pass.

This is a product-design prototype whose state and calculations run locally. Its purpose is to let the owners experience the entire proposed onboarding, compare it with STOPPR’s production quality, and decide what belongs in the real app.

---

## 3. Product decision from the research

### 3.1 Positioning

**First Ruck is a first-month coach for civilians who are new to walking with load.**

It answers four questions:

1. Can I begin with what I already own?
2. What should my first session look like?
3. How do I carry the load and choose a route without making avoidable mistakes?
4. What should I do over the first four weeks?

The onboarding must leave the user with a real, inspectable starter plan. It must not end at a generic promise or paywall.

### 3.2 Onboarding skeleton

Use a **personalization loop with education woven into it**:

```text
Welcome
  -> explain rucking and remove equipment anxiety
  -> collect only inputs that change the recommendation
  -> teach pack, feet, and route fundamentals
  -> run an honest deterministic analysis
  -> reveal the exact first session and four-week shape
  -> show demonstration routes
  -> finish with a leave-ready checklist
```

This shape is justified because the prototype can really use the answers to produce a starter duration, load ceiling, terrain target, schedule, equipment checklist, and route-ranking explanation. A simple value walkthrough was rejected because First Ruck’s central promise is personalization, not just education.

### 3.3 What “that quality” means

Match STOPPR’s level of deliberate craft through:

- a strong full-screen opening;
- distinct visual chapters rather than 25 identical forms;
- original motion that reinforces meaning;
- one clear action per screen;
- helpful feedback after meaningful choices;
- progress cues and section transitions;
- a tangible personalized payoff;
- polished desktop review controls around an authentic phone viewport;
- complete responsive, keyboard, screen-reader, and reduced-motion behavior;
- screenshots and automated interaction checks.

Quality does not mean copying STOPPR’s length, pressure tactics, unverifiable claims, or visual assets.

---

## 4. Beginner rucking research synthesis

This section is the evidence boundary for the prototype. Cursor must not replace it with influencer advice or military challenge standards.

### 4.1 What rucking is

Rucking is walking while carrying a backpack or rucksack with some load. Load, time/distance, pace, terrain, heat, altitude, footwear, pack fit, and training frequency all change the demand.

Use plain language such as:

> Rucking is walking with weight you can control. Your first session should feel like learning the setup, not passing a test.

Do not market the first session as a calorie-burning contest, military simulation, or grit challenge.

### 4.2 Starting load

The most defensible civilian beginner guidance found for this brief is conservative:

- Cleveland Clinic recommends easing in, walking first if needed, and suggests that an initial added load may be about 5 lb even for someone who strength trains.
- Defense public-health guidance says to reduce load and distance, individualize loads, increase gradually, and account for fitness, size, terrain, equipment, and prior injury.
- Military field-manual progressions begin substantially heavier, but they are designed for trained military populations and mission requirements. They are not a safe default for a consumer beginner app.
- Research confirms that increasing backpack load increases energetic and biomechanical demands. It does not establish one universal civilian starting percentage of body weight.

**Prototype rule:** never prescribe more than 5 lb of added load for the first ruck. An empty backpack is a valid recommendation. Do not ask body weight merely to calculate a percentage. If future expert review establishes a better validated rule, replace this provisional rule on the server.

Visible copy:

> Starting lighter is not failing. Your first job is to learn how the pack feels while you walk normally.

### 4.3 Progression

The evidence supports gradual adaptation and warns against rapidly increasing load, speed, distance, or frequency.

- Cleveland Clinic advises changing one component at a time and gives roughly 10% as an example, dependent on the person’s fitness and goals.
- Defense public-health guidance identifies load, speed, distance, frequency, and terrain as interacting risk factors and recommends slow adaptive progression.
- Defense guidance for initiated trainees says to space loaded march sessions at least weekly.

**Prototype first-month rule:** one planned loaded session per week for the first four weeks, with optional ordinary walks between. Week 1 establishes the baseline. Week 2 repeats it. Week 3 may add either five minutes or 1–2 lb, never both, and only after comfortable feedback. Week 4 repeats Week 3 to consolidate. This is a conservative product rule that still requires qualified expert review before a public health-and-fitness launch.

Do not use “no pain, no gain.” Do not increase a plan automatically because a calendar week passed.

### 4.4 Pack choice and loading

A new user does not need a branded ruck to test the activity.

Recommend a current backpack only when it is sturdy, uses two comfortable shoulder straps, closes securely, and can keep the load from shifting or poking the user. A sternum strap and a well-fitted hip belt can improve stability or redistribute some load, but fit varies.

Loading guidance:

- cushion hard objects;
- keep the load balanced left to right;
- prevent it from shifting;
- on even terrain, a compact load higher and closer to the back may help posture;
- on uneven terrain, stability matters and an excessively high load can feel less stable;
- adjust straps if the pack bounces, rubs, or causes numbness;
- learn how to lift and put on the pack without jerking it by one shoulder strap.

Prefer water bottles, a hydration bladder, or wrapped household items for the prototype illustration. Do not show loose dumbbells or sharp-edged plates bouncing in a school backpack.

### 4.5 Shoes, socks, and blister prevention

The route surface matters more than an expensive boot.

- Paved or predictable routes can use comfortable supportive walking or athletic shoes.
- Dirt, gravel, wet, steep, or uneven routes may require sturdier rubber-soled footwear appropriate to conditions.
- Shoes should fit, hold the heel without excessive slipping, and leave toe room.
- Synthetic or wool moisture-wicking socks are preferable to cotton when friction and moisture are concerns.
- New users should respond to a hot spot early rather than walk until it becomes a blister.

The app must not sell one footwear category as universally necessary.

### 4.6 Walking technique and effort

Use these beginner cues:

- walk rather than run;
- choose a pace at which a short conversation remains possible;
- keep the torso comfortably tall instead of exaggerating a forward hunch;
- use a natural stride and shorten it on hills or uneven ground;
- keep the pack stable and make small strap adjustments when needed;
- turn around early rather than letting the route dictate the effort.

Do not coach military pace standards. Do not encourage ruck running in the beginner plan.

### 4.7 Route choice

The first route should prioritize reversibility and verified access over scenery or difficulty.

Favor:

- a familiar neighborhood, greenway, park path, or other public route;
- an out-and-back or short loop with easy exits;
- level or gentle terrain;
- a surface the user already walks comfortably;
- daylight;
- low navigation complexity;
- known public access and current closure information;
- parking, toilets, water, shade, or cell coverage when relevant;
- a Plan B if conditions change.

Avoid recommending a remote, technical, exposed, steep, poorly marked, or access-uncertain trail for a first ruck.

The future recommendation system must separate verified facts from inference. AI may explain or rerank verified candidates. It must never invent geometry, access, closures, weather, water, lighting, cell coverage, or safety.

### 4.8 Weather, heat, air, and outdoor preparation

Before departure, the final product should check or ask the user to check:

- current weather and alerts;
- heat risk and the time of day;
- air quality;
- thunderstorms and lightning risk;
- daylight remaining;
- route closures, permits, and access rules;
- water appropriate to time and conditions;
- a charged phone and a shared trip plan for less familiar routes.

CDC guidance says people exercising in heat should start slowly, schedule activity for cooler parts of the day when possible, drink more water than usual, and stop if faint or weak. The National Weather Service says there is no safe place outside during a thunderstorm; hearing thunder is a reason to move to a substantial building or hard-topped vehicle. NPS guidance emphasizes weather, alerts/closures, route planning, equipment, water, and the Ten Essentials as trip context requires.

The prototype shows a checklist. It does not fetch current conditions.

### 4.9 Stop, adjust, or seek help

The app is not a diagnostic tool. It should make three levels clear:

**Adjust or end the session:** a developing hot spot, pack rubbing, unusual pressure, new numbness or tingling, increasing musculoskeletal pain, or pain that changes walking form.

**Stop and obtain medical evaluation before continuing exercise:** chest discomfort with exertion, unreasonable breathlessness, dizziness, fainting/blackouts, or other concerning exertional symptoms. Use careful language based on Exercise is Medicine and American Heart Association screening guidance.

**Urgent help:** symptoms that may represent a medical emergency, severe heat illness, or a lightning strike require emergency services. Do not ask the app to diagnose which emergency is present.

### 4.10 Environmental and trail behavior

Teach a small starter code:

- check local rules and stay on designated durable routes;
- pack out waste;
- leave natural and cultural items where they are;
- observe wildlife from a distance and never feed it;
- keep noise considerate and yield according to posted/local trail etiquette.

If the exact Leave No Trace 7 Principles are reproduced later, follow Leave No Trace’s attribution and copyright requirements. For this prototype, paraphrase only the rucking-relevant behaviors above.

### 4.11 Evidence confidence

| Topic | Product confidence | Prototype treatment |
| --- | --- | --- |
| Start slowly and light | High | Core rule |
| Walk before adding load when needed | High | Core branch |
| Do not change several training variables at once | Moderate to high | Core progression rule |
| Universal body-weight percentage | Low for civilian beginners | Do not use |
| Exact first-month frequency | Limited direct civilian evidence | Conservative provisional rule and label for expert review |
| Pack fit, secure load, and proper footwear matter | High | Required education |
| 5 lb maximum added load for first exposure | Moderate, conservative clinical guidance | Prototype ceiling, not a universal medical prescription |
| Exact calorie-burn multiplier | Too dependent on person, load, grade, pace, and method | Exclude from onboarding |
| AI can determine route safety | False | Explicitly prohibit |

---

## 5. STOPPR onboarding analysis

### 5.1 Reconstructed sequence

The local STOPPR main walkthrough contains **54 screens**:

```text
Welcome video
  -> product video and social proof
  -> hardcoded FOMO statistics
  -> account gate
  -> empty radar promise
  -> four projected transformation weeks
  -> questionnaire and progress interruption
  -> consumption estimate and 30-day claims
  -> remaining questionnaire
  -> name and age
  -> simulated analysis and hardcoded score
  -> symptom collection
  -> nutrition-product detour and body metrics
  -> expert/celebrity proof and performance claim
  -> accountability preview
  -> notification permission
  -> science/rating/goals/referral
  -> future-self letter and vow
  -> pre-paywall and purchase
  -> eight congratulations screens
  -> product home
```

Evidence locations include `stoppr/js/screens.js:190` for the main path, `:229` for the early account gate, `:240` for the radar promise, `:319` for the quiz break, `:371` for simulated analysis, `:462` for expert proof, `:498` for permission timing, `:553` for the future-self letter, and `:575` for the paywall transition.

### 5.2 What works and should influence First Ruck

1. **It feels like a journey.** Welcome, projection, questions, analysis, proof, commitment, purchase, and activation are visually distinct chapters.
2. **The product is demonstrated.** Video and device mockups make an abstract service tangible.
3. **Questions can answer back.** Selected answers reveal coaching language rather than feeling like a silent survey.
4. **Long-form pacing is broken up.** Progress graphics and insight screens interrupt question fatigue.
5. **There is a perceived payoff.** The analysis, plan, and future-self material attempt to repay the user’s effort.
6. **The primary action stays obvious.** Large bottom actions create consistent forward momentum.
7. **The local recreation is reviewable.** The desktop shell explains each screen and allows direct jumping without weakening the phone-sized experience.

### 5.3 What must not transfer

| STOPPR pattern | Why it is unsuitable | First Ruck replacement |
| --- | --- | --- |
| Unskippable opening video | Delays agency and can fail or overwhelm | Short original motion that never blocks Continue |
| Account gate at screen 4 | Requests identity before delivering value | No account in the prototype; any later account follows the plan reveal |
| Hardcoded community/FOMO numbers | Not verified live proof | No user counts, ratings, or fabricated testimonials |
| Projected “peak transformation” before input | Unearned and medically broad | Explain the modest first-month process without outcome promises |
| Many shame- or inadequacy-framed questions | Can manipulate vulnerability | Neutral, ability-based, nonjudgmental choices |
| Fake analysis time and hardcoded 73% score | Breaks the personalization promise | Deterministic computation with an expandable “How this was chosen” explanation |
| Body metrics collected for a nutrition detour | Irrelevant and sensitive for first ruck | Ask no height, weight, gender, or age unless a later validated rule truly needs it |
| Celebrity/expert cards and 76% claim | Quotes and comparison are not verified | Source methodology in the research/about layer, not invented proof |
| Notification request before the value/paywall seam | Interrupts momentum and is not needed for first value | Ask contextually after a user schedules a real session, outside this prototype |
| Rating request during onboarding | Asks for endorsement before experience | Ask only after meaningful use, not in this flow |
| 90-day vow and signature | Too coercive for a long setup and not required for planning | One practical commitment: choose the day for the first session |
| Eight post-purchase congratulations pages | Delays activation | One success moment, then Today |
| 54-screen default path | Contains repeated marketing and unrelated nutrition setup | 25 screens with branching and every input used |

### 5.4 Funnel conclusion

STOPPR demonstrates strong production ambition but contains a broken trust seam: it collects substantial emotional and physical information, simulates analysis, and uses unsupported projections and hardcoded scores. First Ruck must preserve the high-craft feeling while making the analysis honest and the payoff specific.

There is no paywall in this First Ruck prototype. Monetization will be designed only after the owners can judge the real plan, first-session loop, retention value, and validated pricing model.

---

## 6. First Ruck onboarding: exact 25-screen specification

### Shared behavior

- Phone content uses one full viewport per screen.
- Back is available after the welcome screen.
- Show chapter progress, not misleading global percentages when branches differ.
- Choice screens use explicit Continue for safety and equipment decisions. Low-stakes preference screens may auto-advance after a 250 ms selected-state confirmation.
- Preserve answers when moving backward.
- A visible `Reset walkthrough` control exists only in the desktop review shell.
- Answers may use `localStorage` for the local lab, with a clear reset. Do not store the safety-screen answer beyond the current browser session.
- Every answer listed below must influence copy, plan logic, branching, checklist, or route ranking.

### Phase A: understand the promise

#### Screen 1: Welcome

**Purpose:** establish the product and emotional tone.

**Copy:**

- Eyebrow: `Your first ruck starts here`
- Headline: `Start where you are. Carry forward.`
- Body: `Build a first session around your walking comfort, the gear you already own, and a route you can change your mind on.`
- Primary action: `Build my first ruck`
- Footnote: `About 4 minutes · No account required`

**Visual:** use `hero-beginner-greenway.png` as a full-bleed photograph with the walker held on the right. Add a directional forest scrim on the left and bottom so all warm-white copy and the action meet contrast requirements. Place the First Ruck mark and live-text product name at the top; they must read clearly without looking like an advertisement pasted over the photograph. Add only a very quiet original contour overlay and short orange route stroke. The route must avoid the face, body copy, and action zones.

**Motion:** route draws once. Copy enters in restrained stages. Continue is available immediately. Reduce Motion shows the final composition.

#### Screen 2: What rucking is

**Purpose:** orient a complete beginner without military framing.

**Copy:**

- Headline: `It is walking, with a little more to carry.`
- Body: `Load changes the effort. Your first session is for learning the pack, your pace, and how your body responds.`
- Three beats: `Walk, do not run` · `Start light` · `Change one thing at a time`
- Action: `That makes sense`

**Visual:** a side-on original illustration or code-native silhouette moves from an ordinary walk to the same walk with a compact backpack. Do not use military clothing or plates.

#### Screen 3: Equipment reassurance

**Purpose:** remove the belief that a specialized ruck is required.

**Copy:**

- Headline: `You may already have enough.`
- Body: `A sturdy backpack, comfortable shoes, moisture-wicking socks, and a small secure load are enough to test your first ruck.`
- Callout: `An empty backpack counts.`
- Action: `Use what I have`

**Visual:** original flat-lay of an ordinary backpack, water bottle, socks, shoes, and phone. Each item receives a short label on tap/focus.

**Approved asset:** use `equipment-flatlay.png`. Pan or crop gently between item details using CSS; labels remain accessible live HTML and never become part of the image.

### Phase B: choose a realistic starting point

#### Screen 4: Goal

**Prompt:** `What would make your first month worthwhile?`

**Single-select values:**

- `everyday-fitness` · `Build everyday fitness`
- `outdoor-time` · `Spend more time outside`
- `clear-head` · `Clear my head`
- `event-foundation` · `Build a foundation for a future challenge`

**Uses:** result headline, coaching emphasis, Learn recommendation, and route-description tone. Do not promise the goal will be achieved in a fixed period.

#### Screen 5: Comfortable unloaded walk

**Prompt:** `How long can you walk comfortably today?`

**Help:** `Choose a duration that does not noticeably change how you move or leave you unusually sore the next day.`

**Values:** `10`, `20`, `30`, `45`, `60-plus` minutes.

**Uses:** starter-duration ceiling and route-distance target.

#### Screen 6: Recent activity

**Prompt:** `How often have you been active lately?`

**Help:** `Walking, workouts, sports, and active work all count.`

**Values:** `rarely`, `1-2`, `3-4`, `5-plus` days per week.

**Uses:** conservative duration cap, optional unloaded-walk frequency, and explanation.

#### Screen 7: Loaded walking experience

**Prompt:** `Have you walked for exercise with weight before?`

**Values:**

- `never` · `No, this is completely new`
- `daily-bag` · `Only with an everyday bag`
- `few-times` · `A few intentional loaded walks`
- `regular` · `Yes, but I want a careful restart`

**Uses:** starting-load recommendation. The result still caps the first prototype recommendation at 5 lb added load.

#### Screen 8: Available time

**Prompt:** `How much time can you comfortably protect for a session?`

**Values:** `15`, `20`, `30`, `45-plus` minutes.

**Uses:** final duration is no longer than both the comfortable-walk and time-available constraints.

#### Screen 9: Weekly rhythm

**Prompt:** `Which rhythm could you actually keep?`

**Values:**

- `one` · `One focused day each week`
- `two` · `Two days, with recovery between`
- `three` · `Three active days, mixing rucks and ordinary walks`
- `flexible` · `A flexible weekend-first plan`

**Uses:** calendar layout. The first four-week prototype still schedules no more than one loaded session per week until expert review; additional days are ordinary walks or optional mobility/strength.

**Visual:** use `community-park-walk.png` as a shallow top or lower-third editorial window. Keep the three walkers visible and do not attach names, quotes, or claims to them.

#### Screen 10: Exercise safety gate

**Purpose:** establish boundaries without diagnosing or collecting a medical history.

**Headline:** `Before we add weight`

**Body:** `Loaded walking should not be the place to test concerning exercise symptoms.`

**Choices:**

- `clear` · `None of these apply to me right now`
- `check-first` · `I have chest discomfort with effort, unusual breathlessness, dizziness, fainting, or another reason to check first`
- `walking-pain` · `Pain, numbness, balance, or a recent injury already changes how I walk`
- `prefer-not` · `I would rather not answer`

**Privacy:** keep this value in session memory only in the lab. Do not place it in persistent `localStorage`.

**Branching:**

- `clear` continues normally.
- `check-first` creates a no-loaded-session result: `Pause before adding load. Consider medical clearance before beginning.` It may still show equipment education.
- `walking-pain` creates an unloaded, familiar-route result and says to obtain individualized professional advice before adding load.
- `prefer-not` defaults to the most conservative unloaded plan without implying a diagnosis.

#### Screen 11: First reflection break

**Purpose:** prove that the answers have meaning and relieve question fatigue.

**Dynamic headline examples:**

- `We will keep your first session inside 20 minutes.`
- `We will build around one reliable weekend session.`
- `We will begin without added load.`

**Body:** `Next, we will check the pack, your feet, and the kind of route that feels manageable.`

**Visual:** three-line plan diagram labeled `Time`, `Load`, and `Terrain`. Values animate from the actual answers. No score or percentage.

### Phase C: make current equipment workable

#### Screen 12: Pack type

**Prompt:** `What could you carry on your first ruck?`

**Values:**

- `school-backpack` · `A regular backpack`
- `daypack` · `A hiking daypack`
- `ruck` · `A purpose-built rucksack`
- `vest` · `A weighted vest`
- `none` · `I need help choosing something`

**Uses:** tailored fit instructions and shopping guidance. A vest receives different fit copy and must not be described as a backpack.

#### Screen 13: Load available

**Prompt:** `What safe, secure load do you already have?`

**Help:** `This is an equipment ceiling, not a target.`

**Values:**

- `empty` · `Nothing yet or an empty pack`
- `water` · `Water bottles or a hydration bladder`
- `household` · `Books or household items I can cushion`
- `purpose-weight` · `A purpose-built weight or plate`

**Follow-up toggle:** `I can keep it from shifting or poking me.`

**Uses:** if the toggle is false, recommend empty until the load can be secured. Added load never exceeds 5 lb in the first-session prototype.

#### Screen 14: Shoes and socks

**Prompt:** `What will be underfoot?`

Use two short selections on one screen:

**Shoes:** `comfortable-walking`, `running`, `trail`, `boots`, `unsure`  
**Socks:** `synthetic-wool`, `cotton`, `unsure`

**Uses:** gear checklist and surface warning. Never fail the whole plan because someone owns cotton socks; explain the friction/moisture tradeoff and recommend a low-cost upgrade before a longer session.

#### Screen 15: Pack setup lesson

**Purpose:** teach the minimum viable setup using the selected pack and load.

**Interactive checklist:**

1. `Cushion hard edges.`
2. `Center and secure the load.`
3. `Tighten both shoulder straps until the pack stays close without pinching.`
4. `Use the sternum or hip strap if it improves stability and comfort.`
5. `Walk around the room. Adjust if it bounces, rubs, or causes numbness.`

**Visual:** use `pack-fit-adjustment.png` as the dominant human reference, cropped so the sternum strap, both hands, and pack remain legible. Pair it with a small original pack cross-section whose selected items highlight in sequence. Action remains available without forcing all checks because this is education, not proof that the user physically completed them.

### Phase D: choose the first-route character

#### Screen 16: Surface

**Prompt:** `Which surface already feels comfortable?`

**Values:** `paved`, `compacted`, `natural`, `mixed`, `not-sure`.

**Uses:** route ranking and footwear note.

**Visual:** introduce `route-choice-greenway.png` as a wide edge-to-edge chapter image before the surface choices. The visible path fork expresses choice, but a nearby label must state `Route-character example · Not a live route`.

#### Screen 17: Hills

**Prompt:** `How should the first route handle hills?`

**Values:**

- `level` · `Keep it mostly level`
- `gentle` · `A few gentle rises are fine`
- `rolling` · `I already walk rolling hills comfortably`

**Uses:** elevation ceiling and route explanation. The first plan does not recommend steep terrain.

#### Screen 18: Route shape

**Prompt:** `Which first-route shape feels easiest to control?`

**Values:**

- `out-back` · `Out and back, with an early turnaround`
- `short-loop` · `A short loop`
- `either` · `Either is fine`

**Uses:** route score and explanation.

#### Screen 19: Route priority

**Prompt:** `What would make you more likely to go?`

**Values:** `familiar`, `quiet`, `shade`, `facilities`, `scenery`.

**Uses:** route tiebreaker only. Never let scenery override access, distance, terrain, or current safety conditions.

#### Screen 20: Starting area

**Prompt:** `Where should we look when live routes arrive?`

**Options:**

- Manual city, neighborhood, or ZIP field
- `Choose later`

**Prototype behavior:** do not call geolocation or geocode the entry. Store only the typed label locally and use it in result copy such as `Near Santa Barbara`. If blank or deferred, say `Near your chosen starting area`.

**Future iOS behavior:** offer a contextual one-time or When In Use location action only when the user asks for nearby routes. Manual area entry remains available.

### Phase E: repay the effort

#### Screen 21: Honest analysis transition

**Purpose:** make the computation legible, not mysterious.

**Headline:** `Building your first ruck`

**Three actual steps:**

1. `Keeping time inside your comfortable walk`
2. `Setting a light equipment ceiling`
3. `Matching terrain and route shape`

The transition may take 1.4–2.2 seconds for rhythm, but the computation must already be complete. Add `How this works` to reveal: `This prototype uses fixed rules from your answers. It is not an AI or medical assessment.`

Reduce Motion shows all three checks immediately and continues on explicit tap rather than timing the user.

#### Screen 22: Starting profile reveal

**Dynamic eyebrow:** `Your starting point`

**Profile labels:**

- `Fresh start` for comfortable walk up to 20 minutes, rare recent activity, or conservative safety branch
- `Steady beginner` for 30–45 comfortable minutes and some recent activity
- `Ready to learn the load` for 60+ comfortable minutes, regular activity, and no safety branch

These are coaching labels, not health scores.

**Show four values:**

- first-session minutes;
- added-load range;
- terrain target;
- weekly rhythm.

**Required explanation:** `We chose this because…` followed by three exact answer-to-result reasons.

**Action:** `See my four weeks`

#### Screen 23: First four weeks

Show a vertical trail timeline:

- **Week 1 · Learn the setup:** first-session duration, selected load, familiar forgiving route.
- **Week 2 · Repeat the baseline:** same duration, load, and terrain. Improve pack fit or footwear only.
- **Week 3 · Change one thing:** if prior feedback is comfortable, either add 5 minutes or 1–2 lb. The card must say which one is proposed and why.
- **Week 4 · Make it repeatable:** repeat Week 3. No automatic increase.

Additional activity days from Screen 9 appear as `ordinary walk`, `optional strength`, or `recovery`, not extra loaded sessions.

Add a visible note: `Future plans respond to how the previous session felt. The calendar alone never forces an increase.`

#### Screen 24: Demonstration route matches

Show three original, clearly fictional candidates that match the chosen values:

- `Neighborhood greenway`
- `Park perimeter loop`
- `Shaded creek path`

Every card displays:

- `Demonstration route` badge;
- distance range derived from starter time, not fake precision;
- terrain;
- route shape;
- early-turnaround availability;
- two match reasons;
- a `What is unknown` disclosure.

Unknowns must include current access, closures, weather, surface condition, and any live-data field not actually connected.

Action: `Choose for this walkthrough`. Selection does not start tracking.

#### Screen 25: Leave-ready plan

**Headline:** `Your first ruck is ready to check, not blindly follow.`

Show:

- chosen demonstration route;
- time and load;
- pack-fit check;
- shoes/socks note;
- weather, heat, air-quality, lightning, daylight, and closure check;
- water and phone;
- tell someone when appropriate;
- stop/adjust signs;
- `Turn around early is always an option.`

Primary action: `Open Today preview`  
Secondary action: `Change my answers`

The Today preview is a final state in the same screen or a lightweight overlay, not a 26th onboarding page. It shows how the plan will hand off into the future app.

**Visual:** use `completion-portrait.png` as a quiet upper-section portrait, keeping the person on the left and result copy on the open right. The model represents the feeling of calm readiness only; do not add a name, quote, star rating, before/after comparison, or outcome claim.

---

## 7. Deterministic prototype model

Put pure, testable functions in `js/model.js`. No DOM access in the model.

### 7.1 Duration

Map comfortable unloaded walking to a conservative starting duration:

| Comfortable answer | Base start |
| --- | --- |
| 10 min | 10 min |
| 20 min | 15 min |
| 30 min | 20 min |
| 45 min | 25 min |
| 60+ min | 30 min |

Then:

1. cap by available session time;
2. cap at 20 minutes when recent activity is `rarely`;
3. use an unloaded recommendation for `check-first`, `walking-pain`, or `prefer-not`;
4. never output less than 10 minutes or more than 30 minutes for the first session.

### 7.2 Added load

- `check-first`, `walking-pain`, `prefer-not`, no pack, unsecured load, or only 10 comfortable walking minutes → `No added load`.
- completely new loaded walker → `0–5 lb; beginning empty is valid`.
- everyday-bag experience with at least 20 comfortable minutes → `Up to 5 lb`.
- a few intentional loaded walks or careful restart → `Up to 5 lb for this first assessed session`.
- never output more than 5 lb.

Do not calculate from body weight.

### 7.3 Terrain

- conservative safety branch → familiar, mostly level, predictable surface;
- `level` → mostly level;
- `gentle` → gentle rises only;
- `rolling` → gentle-to-rolling, with an explicit no-steep-first-route note.

### 7.4 Week 3 change

For demonstration only:

- if starting load is empty and the user has a securable load, propose `add 1–2 lb`;
- otherwise propose `add 5 minutes` when the result remains within the comfortable-walk answer;
- if neither is appropriate, propose `repeat the baseline`;
- never change time and load together;
- label the change as conditional on comfortable post-session feedback.

### 7.5 Route scoring

Score demonstration candidates transparently:

1. hard-filter anything above the selected hill ceiling;
2. hard-filter duration/distance beyond the starter range;
3. score surface match;
4. score route-shape match;
5. use the route priority only as a tiebreaker;
6. attach two human-readable reasons;
7. attach an unknowns list to every result.

Do not display a pseudo-scientific match percentage. Use `Best fit`, `Good alternative`, and `Save for later`.

---

## 8. Visual and interaction direction

### 8.1 Brand system

Use the established First Ruck tokens:

| Token | Value |
| --- | --- |
| Forest | `#14331D` |
| Forest deep | `#051609` |
| Paper | `#F9F3E6` |
| Paper deep | `#E9E0CF` |
| Warm white | `#FFFDFA` |
| Ink | `#131F16` |
| Ink soft | `#3C493E` |
| Safety orange | `#F45707` |
| Orange deep | `#C73700` |
| Muted | `#637063` |
| Positive | `#347D48` |

Display typography may use Georgia for this no-download lab. Interface typography uses the system sans stack. Keep the editorial field-guide character from the native Milestone 1 screenshots.

### 8.2 Chapter treatment

- **Promise:** deep forest, topographic atmosphere, warm-white type.
- **Starting point:** paper background, large editorial question, quiet progress line.
- **Equipment:** warmer field-note surfaces, original diagrams, orange callouts.
- **Route:** map-inspired contours and route geometry, never a fake satellite map.
- **Payoff:** forest-to-paper transition, larger values, clear reasons.

Do not make every screen a grid of generic cards. Use full-bleed visual moments between focused choice screens.

### 8.3 Approved First Ruck asset system

Use the original brand assets in `FirstRuck/brand/assets/`. Read `FirstRuck/brand/BRAND-SYSTEM.md` before laying out any screen. The photographs are approved source assets for this prototype and must remain associated with First Ruck, not STOPPR.

Copy only the files actually used into `FirstRuck/onboarding-lab/assets/brand/` so the lab remains portable over `file://`. Do not use absolute paths or climb out of the lab directory at runtime.

| Asset | Screens | Treatment |
| --- | --- | --- |
| `logo/firstruck-mark.svg` | 1 and review shell | Use at 32–44 px in-product; it anchors rather than dominates |
| `photography/hero-beginner-greenway.png` | 1 | Full-bleed crop; walker remains right and copy sits over a controlled dark left scrim |
| `photography/community-park-walk.png` | 9 | Shallow editorial window; never present these models as customers or coaches |
| `photography/equipment-flatlay.png` | 3 | Natural/progressive-detail crop; use live HTML for every label |
| `photography/pack-fit-adjustment.png` | 15 | Keep strap, hands, and backpack visible so it supports the lesson |
| `photography/route-choice-greenway.png` | 16 | Route atmosphere only; explicitly label it as non-live example imagery |
| `photography/completion-portrait.png` | 25 | Quiet accomplishment; never a testimonial or transformation comparison |

Use the six photographs on the six screens assigned above. Use no more than one dominant photograph per screen and do not repeat them elsewhere. Let typography, choice controls, computed values, pack diagrams, contour lines, and route graphics provide contrast between those photo moments.

Every `<img>` needs explicit dimensions or an `aspect-ratio` container. Use `object-fit: cover` and deliberate `object-position` values. Use the approved alt text from `BRAND-SYSTEM.md` when the image contributes meaning and `alt=""` when it is redundant atmosphere.

Cursor may additionally create:

- code-native SVG line illustrations;
- CSS gradients and topographic contours;
- original abstract route paths;
- simple pack cross-sections;
- iconography drawn specifically for First Ruck.

Cursor must not copy anything from `stoppr/assets/`, download stock media, or invent names, credentials, quotes, ratings, or outcomes for generated people. They are visual models, not real First Ruck users or coaches.

### 8.4 Motion

- One route-draw entrance on welcome
- Small selected-state acknowledgement on choices
- Chapter transitions using opacity and short fixed translation
- Analysis checklist reveals tied to actual computed steps
- Four-week trail line draws once on reveal
- All animation interruptible or skippable
- No continuous decorative loops
- `prefers-reduced-motion: reduce` removes route drawing, staged delays, parallax, and scale effects

### 8.5 Desktop review shell

At widths above 1000 px, show:

- center: authentic 390 × 844 phone viewport;
- left: `Why this screen exists`, `What it uses`, and `What it changes`;
- right: chapter-grouped screen index with completed/current states;
- controls: Back, Next, jump to screen, restart, and view current local state;
- badge: `Research prototype · No live route data`.

At mobile widths, remove the review panels and phone chrome. The onboarding itself fills the viewport.

Keyboard shortcuts Left/Right may navigate only in desktop review mode and must not steal arrow-key behavior from radio controls, text inputs, or scrollable content.

---

## 9. Accessibility and responsive requirements

- Semantic `main`, headings, forms, fieldsets, legends, labels, buttons, and progress.
- Every visual choice is a native radio or checkbox under the styling.
- One visible page-level heading per screen.
- Full option row activates its control.
- Selected state is not color-only.
- Visible focus indicator meets contrast on forest and paper.
- Error text is connected with `aria-describedby`, uses `aria-invalid`, and receives appropriate focus/announcement after Continue.
- Dynamic status uses a stable polite live region; urgent exercise-safety messaging does not rely only on a toast.
- Dialogs, if used, trap focus, close with Escape when dismissal is allowed, and restore focus.
- Touch targets aim for 44 × 44 CSS px.
- The flow works at 320 CSS px, 200% zoom, portrait, and landscape without horizontal page scrolling or hidden actions.
- Sticky actions respect `env(safe-area-inset-bottom)`.
- No text is baked into images.
- Decorative route/contour art is ignored by assistive technology.
- Do not autoplay audio.
- Any future video requires captions, a visible pause control, and a non-video route through the content.

---

## 10. Prototype state schema

Use one serializable state object. Stable values must not depend on visible copy.

```js
{
  version: 1,
  currentScreen: "welcome",
  answers: {
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
  },
  sessionOnly: {
    safetyBranch: null
  },
  result: null,
  selectedDemoRouteId: null
}
```

Persist `answers`, `currentScreen`, and selected demonstration route in `localStorage`. Keep `sessionOnly.safetyBranch` in memory or `sessionStorage`, not persistent storage. Provide `Reset walkthrough` in the review shell and remove all related keys on reset.

Never store exact coordinates, medical details, account information, or secrets in this lab.

---

## 11. Technical implementation rules

### `model.js`

- question/result value constants;
- validation functions;
- starter-duration calculation;
- starting-load calculation;
- plan/profile calculation;
- week-three change rule;
- demonstration route filtering/ranking;
- no DOM access.

### `screens.js`

- ordered screen catalog;
- visible copy;
- chapter, purpose, required answer keys, and result fields;
- render functions or declarative data;
- no external data access.

### `app.js`

- state initialization and safe migration/reset;
- navigation and branching;
- DOM updates;
- focus management;
- keyboard behavior;
- reduced-motion behavior;
- review-shell controls;
- no business-rule calculations beyond calling `model.js`.

### `app.css`

- semantic tokens at `:root`;
- mobile onboarding first;
- desktop review shell as enhancement;
- natural document flow inside the phone viewport;
- no fixed heights for text-bearing cards;
- no `transition: all`;
- responsive headline sizes with `clamp()`;
- reduced-motion media query;
- forced-colors/focus support where practical.

### HTML safety

- Prefer `textContent` for answer-derived content.
- If templates use strings, escape all inserted user values.
- Do not use `innerHTML` with raw location text.
- No remote requests when the walkthrough loads or runs.

---

## 12. Automated and manual verification

Use Node’s built-in test runner so no package installation is required.

Required model tests:

1. 10-minute comfortable walker receives no more than a 10-minute first session.
2. `rarely` caps the first session at 20 minutes.
3. available time caps duration.
4. every safety branch other than `clear` produces an unloaded result.
5. first added load never exceeds 5 lb.
6. unsecured household load produces an unloaded recommendation.
7. Week 3 never changes both duration and load.
8. hard route filters run before preference scoring.
9. route priority cannot override a hill or duration filter.
10. every route contains `Demonstration route` and unknowns.
11. every collected answer is referenced by at least one result or branch rule.

Required walkthrough tests/checks:

1. all 25 screens are reachable on at least one valid path;
2. Back preserves non-sensitive answers;
3. browser refresh resumes the local walkthrough;
4. reset removes stored state;
5. safety response is absent after a full browser restart/session reset;
6. validation prevents advancing without required input;
7. keyboard-only user can complete the flow;
8. screen-reader names and states are meaningful;
9. Reduce Motion removes timed/staged dependencies;
10. direct `file://` opening works with all visual assets;
11. no network request occurs;
12. no STOPPR asset path or copied STOPPR copy appears in the output.

Run:

```bash
node --test FirstRuck/onboarding-lab/tests/onboarding.test.js
node --check FirstRuck/onboarding-lab/js/model.js
node --check FirstRuck/onboarding-lab/js/screens.js
node --check FirstRuck/onboarding-lab/js/app.js
```

Manual screenshot matrix:

- desktop review shell at approximately 1440 × 1000;
- phone content at 390 × 844;
- small phone at 320 × 568;
- welcome;
- one question;
- safety branch;
- equipment lesson;
- analysis;
- profile reveal;
- four-week plan;
- route matches;
- final leave-ready screen;
- 200% zoom;
- Reduce Motion.

Store evidence in:

```text
FirstRuck/docs/onboarding/review/round-001/
```

Include a short `README.md` there with browser, viewport, date, tests, limitations, and screenshot names.

---

## 13. Acceptance criteria

The build is ready for owner review only when:

- the lab opens directly from `index.html` with no server;
- the live First Ruck site and Flutter app are unchanged;
- the experience feels like an original First Ruck product, not STOPPR with green paint;
- all 25 screens and intended branches work;
- every question changes a visible result, branch, checklist, or ranking reason;
- the analysis describes fixed rules honestly and produces a real personalized result;
- no unsupported health outcome, transformation time, user count, rating, testimonial, celebrity endorsement, calorie multiplier, or medical score appears;
- no first-session result exceeds 30 minutes or 5 lb added load;
- safety branches cannot receive a loaded recommendation;
- all route candidates are visibly fictional demonstration data with unknowns;
- location remains manual/deferred and no permission prompt appears;
- required automated checks pass;
- keyboard, focus, validation, 320 px layout, 200% zoom, and Reduce Motion are verified;
- the screenshot review pack is complete;
- `git diff --check` is clean except for documented unchanged third-party/reference assets outside the new lab;
- Cursor reports exact files changed, tests, screenshots, and remaining uncertainties.

## 14. Stop point

Stop after the self-contained onboarding lab and review evidence are complete.

Do not:

- merge the lab into the live First Ruck website;
- port it into Flutter;
- add real trail data;
- add AI;
- add authentication;
- add Adapty or a paywall;
- add location or notification permissions;
- publish or deploy.

The owners and Codex will first review sequence, copy, safety, personalization, visual quality, and screenshots. The complete Flutter/iOS onboarding comes only after that review.

---

## 15. Research sources

Use these sources as the current evidence set. Re-check them before public launch because health, platform, and agency guidance can change.

### Exercise, load carriage, and progression

- [Cleveland Clinic: Should You Add Rucking to Your Workout?](https://health.clevelandclinic.org/what-is-rucking)
- [Defense Centers for Public Health: Foot Marching and Load-Carriage Injuries](https://ph.health.mil/topics/discond/ptsaip/Pages/Foot-Marching.aspx)
- [Defense public-health foot marching and load-carriage fact sheet](https://ph.health.mil/PHC%20Resource%20Library/cphe-ip-road-marching-injuries.pdf)
- [U.S. Army FM 7-22: Holistic Health and Fitness](https://rdl.train.army.mil/catalog-ws/view/100.ATSC/0E6A6894-6C7A-4CC2-8AF2-27FCCE205E84-1352122664001/fm7_22.pdf)
- [PMC review: Impact of Backpacks on Ergonomics](https://pmc.ncbi.nlm.nih.gov/articles/PMC9180465/)
- [PMC study: Mechanics and energetics of load carriage during human walking](https://pmc.ncbi.nlm.nih.gov/articles/PMC3922835/)
- [CDC: Adding Physical Activity as an Adult](https://www.cdc.gov/physical-activity-basics/adding-adults/index.html)
- [Physical Activity Guidelines for Americans, 2nd edition](https://odphp.health.gov/sites/default/files/2019-09/Physical_Activity_Guidelines_2nd_edition.pdf)

### Screening and warning symptoms

- [Exercise is Medicine: Exercise Preparticipation Health Screening Questionnaire](https://www.exerciseismedicine.org/wp-content/uploads/2021/04/EIM-exercise-preparticipation-screening.pdf)
- [American Heart Association: Slow, steady increase in exercise intensity](https://newsroom.heart.org/news/slow-steady-increase-in-exercise-intensity-is-best-for-heart-health-much-more-is-not-always-much-better)

### Pack fit, feet, and equipment

- [REI Expert Advice: How to Pack and Hoist a Backpack](https://www.rei.com/learn/expert-advice/loading-backpack.html)
- [REI Expert Advice: How to Size and Fit a Backpack](https://www.rei.com/learn/expert-advice/backpacks-adjusting-fit.html)
- [Defense public-health blister prevention fact sheet](https://ph.health.mil/resources/cphe-ip-blister-prevention-factsheet.pdf)

### Outdoor preparation

- [National Park Service: Hike Smart](https://www.nps.gov/articles/hiking-safety.htm)
- [National Park Service: Ten Essentials](https://www.nps.gov/articles/10essentials.htm)
- [National Park Service: Trip Planning Guide](https://www.nps.gov/subjects/healthandsafety/trip-planning-guide.htm)
- [CDC: Heat and Athletes](https://www.cdc.gov/heat-health/risk-factors/heat-and-athletes.html)
- [National Weather Service: Lightning Safety and Outdoor Activities](https://www.weather.gov/safety/lightning-sports)
- [AirNow: Using the Air Quality Index](https://www.airnow.gov/aqi/aqi-basics/using-air-quality-index/)
- [Leave No Trace: The 7 Principles](https://lnt.org/why/7-principles/)

### Apple safety and privacy boundary for the later iOS build

- [Apple App Review Guidelines](https://developer.apple.com/app-store/review/guidelines/)
- [Apple: Requesting authorization to use location services](https://developer.apple.com/documentation/corelocation/requesting-authorization-to-use-location-services)
- [Apple Human Interface Guidelines: Privacy](https://developer.apple.com/design/human-interface-guidelines/privacy)

---

*Onboarding-pattern guidance was informed by Adapty’s teardown library. Its impact ranges are category-level expectations, not measured lift for First Ruck. This prototype intentionally excludes a paywall until the product’s real activation and retention value are demonstrated.*
