# FirstRuck — The first month, carried forward

Design proposal and implemented local review, 2026-09-04. Owner brief authorizes expanding the experience beyond the initial onboarding milestone. The original lab, Flutter milestone, and live application remain separate. This is a concrete design candidate, not a production launch claim.

## The product

FirstRuck helps a beginner get ready, choose an appropriate outing, record it, reflect, and want to go again. The promise becomes **a first month of real walks worth remembering**. A prepared session is the first payoff; the journal is the reason the app becomes personal.

Visual thesis: an outdoor field guide in forest green and warm paper, with confident editorial type, real-world photography, a single orange action, and an illustrated companion.

Content plan: invitation → short question groups → useful field notes → personal preparation → route selection → visible starter plan → membership → one clear daily action → recording → reflection → journal.

Interaction thesis: gentle screen reveals, a route stroke that draws once on arrival, and immediate visible answer selection. No continuous mascot bounce, forced loaders, or automatic screen advancement. Reduced motion removes entrances and route drawing.

## Kip, the trail companion

Proposed name: **Kip**. Species: wombat. A grounded, sturdy little creature makes steady progress relatable. Small ears, a broad nose, warm taupe fur, and an orange civilian backpack give the character a recognizable silhouette.

The first gouache-style illustration is in `experience-lab/assets/kip-wombat.png`. It was generated with the built-in image tool, and is a first character direction rather than a completed animation system. The existing route logo stays the product mark.

Kip appears at learning breaks, completion, empty journal, and Journey. During recording, only requested check-ins appear; the mascot does not cover the map. The voice is gently observant: “If your backpack has a drum solo, repack.” Humor never accompanies symptoms or other urgent guidance.

Future illustration set: introducing, adjusting a pack, looking at a map, resting, taking in a view, celebrating an outing. Keep anatomy and backpack consistent. Journey rewards are patches, memories, and familiar paths. Never make Kip sad after missed days; never tie rewards to heavier loads, calories, or consecutive days.

## Onboarding sequence

Health and fitness rewards relevant preparation and an honest personalized result. Use a personalization loop with teaching interludes: the existing deterministic recommendation engine already consumes these answers, so a generic feature slideshow would leave useful work unused.

| Screens | Experience | Result of the input |
|---|---|---|
| 1–2 | Invitation; meet Kip | Promise and character introduction |
| 3–5 | Goal; comfortable walk; recent activity | Goal framing and duration ceiling |
| 6 | “The first win? Wanting to go again.” | Explain a manageable first outing |
| 7–9 | Load experience; available time; weekly rhythm | Load boundary, duration cap, schedule |
| 10 | “Give your walk a place in your week.” | Explain repeatability |
| 11–13 | Safety boundary; pack; available load | No added load or pause when appropriate; gear-specific instructions |
| 14 | “Less bounce. More birdsong.” | Pack setup lesson |
| 15–17 | Shoes and socks; surface; hills | Gear notes, route ranking and hard filters |
| 18 | “Turning back is a route feature.” | Teach route control |
| 19–21 | Route shape; priority; general starting area | Route preferences and area label |
| 22 | “Not every highlight is a number.” | Preview private photo memories |
| 23–25 | Computed starter plan; example route choice; preparation summary | Tangible payoff before membership |
| 26 | Membership preview | Reflect goal and prepared duration, then explain continued value |

No stretch contains more than three question screens. Shoes and socks are two short groups on one screen; split if usability sessions show friction. Safety choices stay in memory only. General area can be blank; no precise location permission is needed for this review.

The old 25-screen model remains the source of conservative plan rules: duration capped at 30 minutes; no more than 5 lb added load; empty pack valid; no automatic progression. These are prototype assumptions awaiting qualified fitness review, not universal exercise prescriptions. Current medical caution copy is preserved and the check-in stop response is consistent with NHS guidance on stopping for concerning symptoms.

## Membership handoff

Use an editorial benefit list: continuing preparation, practical help during walks, and a personal record of progress. There is no established free-tier matrix, real testimonial set, or verified trial product, so comparison tables, ratings, and trial timelines are not appropriate yet.

The review has no invented price, savings percentage, user count, or purchase action. “Explore the app preview” is explicitly a preview. Production must load actual store products and show full billing period/total, renewal terms, eligible trial details, restore purchases, terms and privacy links, and purchase failure/cancellation states. Preserve the selected route and answers after cancellation or interruption. Membership dismissal returns to the plan. Decide the free-versus-paid boundary before implementation; do not accidentally hold already captured personal records hostage.

Unresolved owner inputs: subscription products and billing periods; trial decision. Route search follows the user’s current location or any selected search area, per the owner’s clarification. They do not block design review.

## App structure

| Destination | Main job | Primary action | Secondary content |
|---|---|---|---|
| Today | Know the next manageable outing | Get ready to walk / resume session | Weekly rhythm, pack reminder, change route |
| Routes | Find an outing that fits | Choose route | Why it fits, evidence, access unknowns, return options |
| Journal | Remember and reflect | Open outing / make postcard | Photos, duration, distance, feeling, deletion |
| Journey | Understand personal progress | Revisit plan | Kip, milestones, lessons, settings in native build |

Recording is a focused full-screen task outside bottom navigation. Large time, clear pause/resume and finish, restrained distance display, optional photo, and requested check-in. No chat composer as the default walking screen. If the user leaves the recording screen, Today offers “Return to recording.”

Native preparation sheet: confirm load, route, current access/conditions, water and phone. Request location at Start, camera/photo selection at Add photo, notifications only when the user enables a reminder. Never stack system prompts before the paywall.

## The differentiator: Field Journal

A session becomes a memory: actual recorded duration and distance, optional feeling, a photo, and one thing noticed. The local review records summary metadata in browser storage; photos are tab-only and can be exported. The postcard is a real PNG composed from the selected entry and optional photo. It omits route geometry and original photo metadata. A demo session stays labeled Demo in the journal and export.

Phase one sharing: private journal and OS share sheet / downloadable postcard. Photo-first postcards feel more human than another performance tile. Users choose the destination; no automatic posting.

Phase two community: a small “Outside together” area for opt-in finished walk stories, with broad area labels only. No live nearby walkers, public home starts, or full route publication by default. Add report, block, content moderation, deletion, audience controls, and rate limits before enabling this surface. Do not simulate community with invented people or quotes.

## Design acceptance

Check the full sequence, not disconnected screenshots. A beginner should be able to explain what rucking is, what to prepare, why their first outing fits, and what membership will continue doing. Every captured preference has an identified effect. Demonstration routes must remain labeled on selection, Today, and the map. Character warmth should survive removal of all animation.

Current test evidence is in `review/`. Verify keyboard operation and screen-reader behavior on real iOS in the native build; a browser test is not a VoiceOver certification.

Patterns are drawn from Adapty's teardowns of top apps across numerous verticals; impact ranges are expected effect, not measured lift for your app. No conversion uplift is claimed for this design.

## Sources checked for the technical handoff

- [Mapbox Directions](https://docs.mapbox.com/api/navigation/directions/): walking routing; route metadata alone does not establish current access or safety.
- [Apple background location](https://developer.apple.com/documentation/corelocation/handling-location-updates-in-the-background): native recording requirements.
- [Gemini structured output](https://ai.google.dev/gemini-api/docs/structured-output): schema-constrained provider responses.
- [Groq structured outputs](https://console.groq.com/docs/structured-outputs): model-specific JSON schema support.
- [Apple App Review Guidelines](https://developer.apple.com/app-store/review/guidelines/): subscription and user-generated content requirements.
- [University Hospitals Sussex exercise guidance](https://www.uhsussex.nhs.uk/resources/cardiac-rehabilitation/): stop exercise for concerning symptoms; this is not validation of the prototype's rucking load rules.
