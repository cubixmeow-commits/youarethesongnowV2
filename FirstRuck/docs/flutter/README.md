# First Ruck Flutter documentation

This directory is the source of truth for beginning the Flutter/Dart iOS client. It translates the approved web prototype into native-mobile contracts without treating the web implementation as code to port.

## Document map

| File | Purpose |
| --- | --- |
| [`product-contract.md`](./product-contract.md) | Product promise, audience, safety boundaries, and MVP scope |
| [`screen-flow.md`](./screen-flow.md) | Current screens, onboarding fields, and state transitions |
| [`design-system.md`](./design-system.md) | Flutter-ready colors, type, spacing, components, motion, and accessibility |
| [`architecture.md`](./architecture.md) | Proposed mobile folder structure, layers, models, and state boundaries |
| [`api-contract.md`](./api-contract.md) | Current prototype endpoint and the mobile-safe contract required later |
| [`build-plan.md`](./build-plan.md) | Ordered milestones and acceptance criteria |
| [`ios-runbook.md`](./ios-runbook.md) | Local tool checks, Simulator launch, signing, and physical-device testing |

Cursor starts with [`../../CURSOR-FLUTTER.md`](../../CURSOR-FLUTTER.md).

## Authority and scope

For First Ruck, use this order when information conflicts:

1. Direct instruction from CuBiX Meow or Brut
2. This Flutter handoff
3. Verified behavior in `FirstRuck/public`
4. Verified PHP recommendation and data behavior in `FirstRuck/src` and `FirstRuck/database`
5. External research and brainstorming, including Grok suggestions

This handoff authorizes only the beginning of the First Ruck mobile client. It does not authorize App Store submission, live subscriptions, production AI, unrestricted location collection, or health claims.
