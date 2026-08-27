---
type: decision
status: accepted
date: 2026-08-27
area: delivery
owners:
  - CuBiX Meow
  - Brut
---

# ADR — Web first, then Flutter/Dart iOS

## Decision

YouAreTheSongNow V2 will be delivered in two major client phases:

1. **Rebuild and refine the web application first** using the V2 PHP backend and initial SQLite database direction.
2. **Build the iOS mobile application second** using **Flutter + Dart**.

The Flutter application should consume the same backend capabilities through clean HTTP/JSON APIs rather than duplicating core business logic in Dart.

## Why

The web rebuild provides the fastest place to validate the refined V1 functionality, creative engine, accounts, gallery, generation workflow, payments/credits, media handling, and operational behavior.

Building the mobile client after the backend contracts are proven reduces duplication and gives the Flutter application a stable service layer to consume.

## Architectural consequences

Core product/business behavior should remain server-side, including where applicable:

- authentication and authorization;
- projects and generation state;
- Song Interpretation / Song DNA orchestration;
- prompt compilation and provider interaction;
- generation retries and quality/safety policy;
- credit/entitlement accounting;
- payment fulfillment;
- media ownership and storage metadata;
- gallery/history records.

The Flutter/Dart iOS client should focus on native mobile UX, including:

- onboarding and account experience;
- image/portrait upload;
- creation controls;
- job status/progress;
- gallery/history;
- sharing/export;
- purchases/entitlements where platform rules permit.

## Important principle

The web application is **not a throwaway prototype**. The PHP backend created for the web rebuild should become the shared backend for both the web and Flutter clients.

## Status

Accepted project direction. This does not lift the current build freeze.
