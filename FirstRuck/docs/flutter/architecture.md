# Flutter architecture

## Location and identifiers

- Flutter project root: `FirstRuck/mobile`
- Dart project name: `first_ruck`
- iOS display name: `First Ruck`
- Target bundle identifier: `com.youarethesongnow.firstruck`
- Initial platform: iOS
- Initial minimum target: iOS 16 or the current Flutter stable default if higher

Do not nest another `first_ruck` directory inside `mobile`.

## Dependency rule for the first slice

Milestone 1 uses Flutter SDK packages only. Do not add routing, state management, networking, database, maps, analytics, purchases, or code-generation packages until their need is demonstrated.

Use `Navigator` and immutable local screen state for the first transition. Later package choices should be recorded here before adoption.

## Proposed source structure

```text
FirstRuck/mobile/
  lib/
    main.dart
    app/
      first_ruck_app.dart
      first_ruck_theme.dart
    core/
      accessibility/
      errors/
      network/
    features/
      onboarding/
        domain/
          onboarding_answer.dart
          onboarding_question.dart
          starter_profile.dart
        data/
          onboarding_fixture_repository.dart
        presentation/
          welcome_screen.dart
          onboarding_screen.dart
          widgets/
      recommendations/
        domain/
          route_candidate.dart
          route_recommendation.dart
        data/
        presentation/
    shared/
      widgets/
        brand_lockup.dart
        first_ruck_scaffold.dart
        primary_action_button.dart
        topo_background.dart
  test/
    app_smoke_test.dart
    features/onboarding/
  ios/
  pubspec.yaml
```

Create only directories needed by the current milestone. This tree is a direction, not permission to generate empty architecture.

## Layers

- `presentation`: widgets and UI-only state
- `domain`: stable app concepts with no Flutter imports where practical
- `data`: fixture and later HTTP implementations behind small repository interfaces
- `core`: cross-feature services only after two features actually share them

Widgets must not call PHP endpoints, location services, AI vendors, or purchase SDKs directly.

## Initial navigation

Milestone 1 can use standard Navigator routes:

- `/` → Welcome
- `/onboarding` → Onboarding

Introduce a routing package only when deep links, nested tabs, or restoration make it worthwhile.

## Future state boundaries

Keep these distinct:

- onboarding draft answers;
- recommendation request state: idle, submitting, success, failure;
- returned starter profile;
- route data mode: demo or live;
- route source and freshness;
- location permission state;
- tracked-session state.

Do not call a deterministic recommendation “AI” in code or copy. When an AI explanation layer is added, identify it separately from verified route facts and deterministic safety filters.

## Persistence

No persistence is required in Milestone 1. Later, use secure storage only for opaque revocable credentials. Preferences and resumable onboarding answers can use a non-secret local store. Never ship server, trail-provider, AI-provider, or Adapty private credentials in the app.
