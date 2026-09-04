# Product contract

## Product promise

First Ruck helps a person who is new to rucking choose a manageable first session, learn the basics, and find nearby route options that fit their preferences.

Current headline:

> Start where you are. Carry forward.

Current primary action:

> Build my plan

## Intended user

- Curious about rucking but not yet confident about load, duration, terrain, or equipment
- May own only a normal backpack
- Wants a calm, civilian, beginner-friendly experience
- Benefits from clear choices rather than tactical jargon or competitive pressure

## Product principles

1. Personalize before prescribing.
2. Prefer conservative, explainable recommendations.
3. Teach one concept at the moment it becomes useful.
4. Show why a route or session fits.
5. Let users provide a general area without requiring precise location.
6. Use verified route facts; AI may rank and explain them but must never invent a trail or safety condition.
7. Keep progress encouraging without promising health outcomes.

## Safety and trust boundary

First Ruck provides general fitness education, not medical diagnosis or treatment. The interface may encourage a user to stop when pain changes how they move and to seek qualified help when appropriate. It must not claim that a recommended load, route, technique, or schedule prevents injury.

Never infer accessibility, trail access, weather safety, closure status, lighting, or surface condition without a current identified source. Demo routes must remain visibly labeled as demo data.

## Current web-prototype scope

- Welcome screen
- 12-step onboarding
- Analysis/loading state
- Starter profile reveal
- Ranked demonstration routes
- Route detail dialog
- Local PHP/SQLite persistence

## Initial Flutter scope

The first native milestone includes only the app shell, welcome screen, and first onboarding question. Later milestones can reproduce the full local flow, then connect a versioned mobile-safe API.

Not in the initial Flutter milestone:

- Accounts or authentication
- GPS recording or background location
- Live maps or trail providers
- AI-provider calls
- HealthKit or Apple Watch
- Notifications
- Adapty, paywalls, or purchases
- Analytics or advertising SDKs
- App Store submission
