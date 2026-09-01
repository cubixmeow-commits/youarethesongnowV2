# YouAreTheSongNow — Cursor Implementation Instructions

**Round:** 012  
**Written by:** ChatGPT  
**Date:** 2026-09-01  
**Status:** Ready for Cursor  
**Repository:** `cubixmeow-commits/youarethesongnowV2`  
**Working branch:** `main`  
**Scope:** Visual Narrative Planning Layer, Slice A — contract only

## Start

1. Pull the latest `main`.
2. Read:
   - `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`
   - `docs/design/CURSOR-HANDOFF.md`
   - `docs/design/process/LUMINOUS-NIGHT-STUDIO-IMPLEMENTATION-ROADMAP.md`
   - `docs/design/process/VISUAL-NARRATIVE-PLANNING-LAYER.md`
3. Inspect the POV Campaign Engine:
   - https://github.com/cubixmeow-commits/POV-Campaign-Engine
   - `SKILL.md`
   - `references/board-components.md`
   - `references/campaign-design.md`
   - `references/scene-rules.md`
   - `examples/crowdstrike-outage/campaign.md`
   - `examples/crowdstrike-outage/scenes.md`

Use the POV repository only as a reasoning reference for staged structural extraction, information budgeting, viewpoint, pivots, scene contracts, motifs, and controlled assembly. Do not import its reporting, investigative, X-thread, newsletter, or fact-verification rules.

## Implement only Slice A

- Audit existing Song DNA source structures, services, APIs, persistence, privacy, draft, prompt, generation, and queue boundaries.
- Propose the smallest customer-safe Song DNA projection that can support selection without exposing raw lyrics or internal analysis.
- Define versioned, validated contracts for:
  - customer-safe Song DNA projection;
  - selected Song DNA elements;
  - Visual Campaign Board;
  - exactly three scene directions;
  - Visual Scene Contract.
- Define contract ownership, transformations, persistence, privacy, validation, fallback, and prompt/template versioning.
- Add deterministic fixtures for intimate loss, triumphant transformation, ambiguous relationship, kinetic adventure, and quiet introspection.
- Add meaningful tests for valid, invalid, missing, ambiguous, and fallback states.
- Update the relevant design-system, component, interaction, implementation-roadmap, and Flutter contract maps where applicable.

## Do not implement yet

- no customer UI against unapproved projection fields;
- no live planning-model call;
- no Quick Generate wiring;
- no Explore Options UI;
- no Fine Tune UI;
- no generation-provider changes;
- no queue execution changes;
- no retry charging or credit changes;
- no broad redesign.

## Preserve

Preserve routes, authentication, authorization, privacy, credits, plans, song lookup, draft behavior, portraits, owner style controls, queue processing, generation providers, storage, gallery, current responsive design, and Flutter documentation contracts.

## Verification and handoff

Run all existing tests plus the new contract/fixture tests.

Then:

1. complete Round 012 in `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`;
2. update `docs/design/CURSOR-HANDOFF.md`;
3. clearly distinguish implemented contracts from future runtime/UI work;
4. commit and push verified work directly to `main`;
5. do not deploy to Hostinger/production;
6. stop for GPT review before Slice B.
