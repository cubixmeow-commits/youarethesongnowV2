# YouAreTheSongNow — Permanent Design System

This tree is the **permanent design-engineering operating system** for YouAreTheSongNow.

It governs how design and engineering collaborate on the current PHP web client and the future Flutter iOS/Android apps.

## Authority

1. Explicit owner instruction (CuBiX Meow / Brut)
2. Accepted ADRs and Current Project vault notes
3. This operating system (`docs/design/`)
4. Verified implementation and tests
5. Round-based working notes in `/design/` (ChatGPT ↔ Cursor history)
6. Verified V1 product behavior

If a round handoff in `/design/` conflicts with this tree, update this tree deliberately. Do not let ephemeral round notes silently override permanent contracts.

## Product posture

- Long-term target: **premium Flutter iOS and Android app**
- Current website: **desktop/responsive expression of a mobile-first product system**
- Canonical viewport order: **phone → tablet → desktop**
- Approved baseline: **Luminous Night Studio**
- Feel: premium creative media application (listening room / gallery / album sleeve)
- Not: generic SaaS dashboard, AI utility console, or marketing microsite with a separate “app mode”

Premium comes from hierarchy, restraint, typography, spacing, image treatment, excellent state design, consistency, and purposeful motion — not from gradients, glassmorphism, glowing borders, or endless cards.

## Start here

| Doc | Purpose |
| --- | --- |
| [DESIGN-OPERATING-SYSTEM.md](./DESIGN-OPERATING-SYSTEM.md) | Governing principles, phases, roles |
| [CURSOR-HANDOFF.md](./CURSOR-HANDOFF.md) | Latest agent handoff (read before continuing) |
| [foundations/](./foundations/) | Tokens, color, type, space, motion, imagery |
| [components/](./components/) | Component inventory + production state contracts |
| [screens/](./screens/) | Route inventory, Create architecture, and nine production screen specs |
| [audits/](./audits/) | UI audits (Phase 1) |
| [flutter/](./flutter/) | Flutter portability, component, and screen maps |
| [process/](./process/) | Phases, approved roadmap, and review gates |

Design artifacts (token exports, audit packs, references): `/assets/design/`

Working round history (ChatGPT ↔ Cursor): `/design/` (kept; not deleted)

## Current phase

**Phase 1 runtime is implemented and awaiting screenshot review.**

Cursor should stop for Codex/GPT review of [CURSOR-HANDOFF.md](./CURSOR-HANDOFF.md) and `design/review/round-010/`. The next gated slice is Phase 2 Create entry. Song DNA API/database work remains contract-first.
