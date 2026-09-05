# Flutter

## Current state

`mobile/` is an iOS-focused Flutter shell using Dart SDK `^3.13.2`. It currently has only Flutter plus `flutter_lints`; there are no map, network, database, purchase, location, camera, or state-management packages.

Implemented native work includes the theme and color tokens, safe-area scaffold, topographic background and route stroke, welcome screen, first onboarding question, answer cards, validation, forward/back navigation, reduced-motion handling, and widget tests.

The Flutter screen flow and headline predate the current 26-screen experience. Treat the mobile web app as the visual and behavioral reference. Update native contracts before continuing broad implementation.

## Porting rules

- Port domain behavior and design intent, not JavaScript or DOM structure.
- Use typed answer values and typed plan/route models.
- Keep UI independent of provider JSON and URLs through repositories/controllers.
- Start with fixture repositories, then add a versioned API adapter.
- Keep map, location, storage, purchases, media, and analytics behind replaceable interfaces.
- Request location only at the moment of clear benefit and support manual area search.
- Foreground recording comes before background location.
- Preserve semantic labels, 44-point controls, Dynamic Type, Reduce Motion, and small/short iPhone layouts.

## Recommended milestones

1. Synchronize the Flutter product contract and implement complete local onboarding parity.
2. Add Today, Routes, route detail, and fixture plan state.
3. Define and connect a versioned PHP API.
4. Add native foreground recording and interruption recovery.
5. Add reflection, journal, local photo derivatives, and postcard sharing.
6. Add real route candidates after the server pipeline is trustworthy.
7. Add real purchases after membership products are decided.

Background GPS, offline maps, HealthKit, Apple Watch, notifications, Android, community, and App Store release each need explicit scope and acceptance criteria.
