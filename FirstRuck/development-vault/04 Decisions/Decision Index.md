# Decision index

This file records accepted FirstRuck decisions. Add a dated entry when a choice changes product or architecture. Mark superseded decisions rather than deleting them.

## D-001 — Web-first product reference

- **Status:** Accepted
- **Date:** 2026-09-04
- **Decision:** Refine the complete mobile web experience before continuing broad Flutter implementation.
- **Reason:** Web iteration is faster for product, onboarding, route, and membership decisions. Flutter will reproduce an approved experience with native behavior.

## D-002 — Progressive onboarding

- **Status:** Accepted
- **Date:** 2026-09-04
- **Decision:** Alternate no more than three consecutive question screens with useful field notes or a visible personalized payoff.
- **Reason:** The user should learn and feel progress rather than face a long questionnaire.

## D-003 — Plan and route before membership

- **Status:** Accepted
- **Date:** 2026-09-04
- **Decision:** Show a prepared starter plan, route choice, and readiness summary before the membership screen.
- **Reason:** FirstRuck should demonstrate value before asking someone to pay.

## D-004 — Kip the wombat

- **Status:** Accepted direction
- **Date:** 2026-09-04
- **Decision:** Use Kip as the friendly companion across preparation, learning, reflection, and Journey. Keep the route mark as the product logo.
- **Reason:** A grounded animal companion makes steady progress and returning to the app emotionally coherent.

## D-005 — Field journal as a core differentiator

- **Status:** Accepted direction
- **Date:** 2026-09-04
- **Decision:** Treat photos, reflections, milestones, and shareable postcards as a core product loop rather than an accessory.
- **Reason:** Memories and exploration differentiate FirstRuck from generic workout trackers and create long-term personal value.

## D-006 — MapLibre and Geoapify starting stack

- **Status:** Accepted for development evaluation
- **Date:** 2026-09-04
- **Decision:** Use vendored MapLibre for rendering and evaluate Geoapify's free tier for tiles, place search, and walking routes. Keep providers replaceable.
- **Reason:** It supports user-selected or local searches without exposing a key and offers a practical free development allowance.

## D-007 — Geographic facts before AI

- **Status:** Accepted
- **Date:** 2026-09-04
- **Decision:** Generate and validate candidates from geographic sources, apply deterministic eligibility and scoring, then optionally let an LLM reorder eligible IDs or select approved reason codes.
- **Reason:** LLMs must not invent routes, coordinates, conditions, access, or safety facts.

## D-008 — Bounded multi-provider AI adapter

- **Status:** Accepted technical direction, not production-connected
- **Date:** 2026-09-04
- **Decision:** Support Gemini then Groq behind a server adapter with structured output, a two-call lifetime budget, strict ID/reason validation, and deterministic fallback.
- **Reason:** Provider choice remains flexible and failure cannot block a safe rules-based result.

## D-009 — Privacy-first sharing

- **Status:** Accepted
- **Date:** 2026-09-04
- **Decision:** Keep activity local by default. Postcards omit coordinates, start points, traces, and original metadata. Public community publishing is not connected.
- **Reason:** Routes and repeated routines can reveal sensitive location information.

## D-010 — Current welcome message

- **Status:** Accepted current copy
- **Date:** 2026-09-04
- **Decision:** Headline: “A little weight goes a long way.” Supporting caption: “A first ruck built around you. A little companion along the way.”
- **Reason:** It is concise, specific to rucking, and supports the gentle companion-led tone.
