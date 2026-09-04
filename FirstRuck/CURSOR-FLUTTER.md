# Cursor handoff: First Ruck Flutter beginning

This is the entry point for Cursor. The owner has authorized the beginning of the First Ruck Flutter iOS app so it can be tested locally in the iOS Simulator.

## Read first

Read these files in order before editing:

1. `FirstRuck/docs/flutter/README.md`
2. `FirstRuck/docs/flutter/product-contract.md`
3. `FirstRuck/docs/flutter/screen-flow.md`
4. `FirstRuck/docs/flutter/design-system.md`
5. `FirstRuck/docs/flutter/architecture.md`
6. `FirstRuck/docs/flutter/api-contract.md`
7. `FirstRuck/docs/flutter/build-plan.md`
8. `FirstRuck/docs/flutter/ios-runbook.md`

Use `FirstRuck/public/index.php`, `FirstRuck/public/assets/app.css`, and `FirstRuck/public/assets/app.js` as the verified reference for current copy, visual intent, and state transitions. Recreate those roles with native Flutter widgets. Do not embed the website in a WebView and do not mechanically translate HTML or CSS.

## Implement only Milestone 1

Create the Flutter app at `FirstRuck/mobile` and implement only Milestone 1 from `build-plan.md`:

- iOS platform only for the initial scaffold;
- bundle identifier target `com.youarethesongnow.firstruck`;
- app display name `First Ruck`;
- portrait-first app shell that respects safe areas;
- native welcome screen matching the current First Ruck visual direction;
- `Build my plan` opens the first native onboarding question;
- Back returns to the welcome screen;
- at least one widget test for this navigation;
- `flutter analyze` and `flutter test` pass;
- app launches in an available iOS Simulator.

The first slice uses local fixture state. Do not call the PHP endpoint yet. Do not add location, health, maps, AI, accounts, Adapty, subscriptions, analytics, or third-party state-management packages in this milestone.

## Implementation boundaries

- Do not modify the working PHP website while scaffolding Flutter.
- Do not commit generated build output, CocoaPods caches, signing material, API keys, or `.env` files.
- Do not put medical claims or fixed injury-prevention promises in interface copy.
- Keep touch targets at least 44 points and support large text without clipping.
- Honor Reduce Motion from the first slice.
- Prefer small, named widgets and immutable view data over a single large screen file.
- Keep product/business decisions outside widgets so the later API repository can replace fixtures cleanly.

## Stop point

Stop after Milestone 1 is running and tested. Report:

- files created;
- simulator/device used;
- analyzer and test results;
- a screenshot of the welcome screen and first question;
- any environment or signing issue that remains.

Do not continue into all 12 questions or backend integration without the owners reviewing the first native screen.
