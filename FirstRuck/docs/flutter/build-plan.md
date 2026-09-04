# Flutter build plan

Each milestone ends with owner review. Do not silently roll several milestones into one large build.

## Milestone 1: native shell and first interaction — authorized now

Goal: prove that First Ruck runs natively on iOS and that its core visual direction translates to Flutter.

Deliver:

- Flutter app in `FirstRuck/mobile`
- iOS-only initial scaffold
- app theme and semantic color tokens
- safe-area-aware welcome screen
- decorative native topographic background
- responsive headline and action area
- first onboarding question with local fixture choices
- forward and back navigation
- widget test for the transition
- clean analyzer and tests
- screenshots from an iOS Simulator

Acceptance:

- app launches without a red error screen;
- welcome screen remains usable on a small/short iPhone Simulator;
- no headline, summary, button, or footnote overlaps;
- `Build my plan` opens question 1;
- Back returns to Welcome;
- controls are at least 44 points;
- VoiceOver names the primary action and Back button;
- Reduce Motion does not run route-drawing animation;
- `flutter analyze` and `flutter test` pass.

Stop for visual review after this milestone.

## Milestone 2: complete local onboarding — requires review

- all 12 questions from `screen-flow.md`;
- typed draft model using stable answer values;
- validation and answer preservation;
- local fixture analysis, profile, Today, and route detail screens;
- demo-data labeling throughout;
- widget tests for progress, validation, Back, and completion.

This milestone can use fixture results. It must not duplicate or silently change server-side recommendation rules.

## Milestone 3: mobile API contract — requires review

- add and test versioned PHP JSON endpoints;
- define stable response/error models;
- implement Flutter HTTP repository;
- handle offline, timeout, retry, and server-error states;
- confirm demo/live provenance in the UI;
- keep provider secrets server-side.

## Milestone 4: real route candidates — requires data decision

- connect approved geographic sources on the server;
- validate access, route geometry, distance, elevation, surface, and freshness;
- apply deterministic filters before optional AI;
- show sources, unknowns, and stale-data handling;
- obtain location only after explaining its benefit and supporting manual area entry.

## Milestone 5: first-session coaching — requires product review

- pack-fit education;
- planned session;
- timer and user-controlled tracking scope;
- post-session feedback;
- next-session recommendation;
- appropriate privacy and safety review.

## Later, not assumed

Accounts, Adapty/paywall, subscriptions, notifications, HealthKit, Apple Watch, social features, background GPS, analytics, and App Store release each require their own decision and acceptance criteria.
