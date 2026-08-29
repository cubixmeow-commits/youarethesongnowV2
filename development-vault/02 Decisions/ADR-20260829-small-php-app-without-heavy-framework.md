---
type: adr
status: accepted
date: 2026-08-29
area: architecture
---

# ADR-20260829 — Small PHP application without a heavy framework for Build 1

## Decision

Implement Private Development Build 1 as a small custom PHP application with plain HTML/CSS/JavaScript, SQLite, a versioned migration runner and replaceable adapters. Do not adopt Laravel, Symfony or another large framework for this slice.

## Why

- Hostinger shared hosting and a one-minute Cron worker favor a straightforward PHP entrypoint and bounded worker script.
- The approved contracts already define API, security and domain boundaries; a framework would add operational surface without clarifying those contracts.
- A small dependency set keeps secrets, private media and SQLite paths easy to keep outside the public document root.

## Consequences

- Routing, sessions, CSRF, migrations and adapters are first-party code under `src/` and `public/`.
- Framework adoption remains possible later if Hostinger/security needs prove that a maintained framework is materially better; that would require a superseding ADR.
- Flutter continues to consume `/api/v1` rather than PHP templates.
