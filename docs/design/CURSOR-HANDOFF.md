# CURSOR-HANDOFF — Design Operating System

**Date:** 2026-08-31  
**Branch:** `cursor/design-system-audit-95fa`  
**Primary delivery commit (this turn):** `1b43cd38747047601202567aa3b1f11bc9c2f72d`  
**Phase:** 1 — Audit & structure + **Create-flow architecture documentation**  
**Do not begin Phase 2 token migration or DNA-first Create UX implementation** until this handoff is reviewed and First Build / Onboarding contracts are amended where required.

---

## This turn (Create-flow architecture)

Incorporated newly approved GPT/design-review product decisions into the permanent design OS. **No production Create refactor.** Structural documentation only.

### Locked principle

AI should remove decisions by default and offer intelligent choices when the user asks for control. Prefer Song DNA over generic image-generator controls.

### Canonical target flow

```text
Create → Choose Song → Choose Song DNA
  → Quick Generate (default) OR Explore Options
  → AI visual directions (Explore) → Fine Tune (optional, advanced)
  → Generate → Generation experience → Reveal
  → Save / Share / Variation / Reimagine
```

### Primary deliverable

`docs/design/screens/create-flow.md` — full current→target map, Song DNA field inventory, API/backend gaps, UI states, Flutter notes, open questions.

### Also updated

| Path | Change |
| --- | --- |
| `docs/design/DESIGN-OPERATING-SYSTEM.md` | §§2a creation principle, shell, premium rules, phases, pointer |
| `docs/design/README.md`, `screens/README.md`, `screens/inventory.md` | Indexes + as-built vs target |
| `docs/design/flutter/portability.md` | DNA / directions / reveal / immersive Create |
| `docs/design/process/phases.md` | Create-flow extension + next-task recommendation |
| `docs/design/components/inventory.md` | StyleTile demotion note |
| `development-vault/05 Product Design/Create Flow Architecture Contract.md` | Vault contract |
| `development-vault/02 Decisions/ADR-20260831-create-flow-dna-first.md` | ADR |
| `development-vault/02 Decisions/Decision Index.md` + `Decision Inbox.md` | Index + open questions |
| `development-vault/01 Current Project/Current Priorities.md` | Newly documented + Next |
| `development-vault/01 Current Project/Dashboard Snapshot.md` | Design status |
| `docs/dashboard-data.js` | Meow Control brand line |

---

## Inspection summary (Create + Song DNA)

### As-built Create (`templates/pages/create.php` + `app.js`)

1. Song (artist/title) → `POST /api/v1/song-lookups`
2. People (1–2 portraits)
3. Direction: StyleMap grid + quality + orientation + no-text + special instructions
4. Review → paywall if needed → `POST /generation-jobs`
5. Progress stages (backend): Finding the heart… → Building… → Bringing you… → Adding the final details
6. Reveal on `/images/{id}`: Download, Share link, Stop sharing, Create another, Delete, email share

### Song DNA fields available (`song-dna-v2.0`)

`essence`, `emotionalArc` (opening/turning/closing + intensityPattern), `themes`, `relationshipDynamics`, `narrativeArchetype`, `originalVisualMoment`, `symbols[{concept,visualTranslation}]`, `visualMetaphors`, `mood`, `environment` (settingTypes, eraAtmosphere, weather, spatialCharacter), `palette`, `lighting`, `camera`, `composition`, `motion`, `texture`, `subjectRoles`, `ambiguities`, `confidence`, `riskFlags`.

Customer API does **not** currently expose selectable DNA; only private-dev `developmentAnalysis` on lookup create. Derived analysis may persist on `song_lookups.derived_analysis_json`.

### AI visual directions

Full approved intent needs **new backend behavior** (directions generation step). Heuristic packaging of existing DNA visual fields can bootstrap a limited MVP; **relabeling the StyleMap grid is not acceptable**.

---

## Prior Phase 1 audit (still valid)

See earlier sections of branch history / prior handoff commits for full UI audit (routes, components, tokens, a11y, mobile). Key permanent tree remains under `docs/design/`; artifacts under `assets/design/`.

**Missing original OS file:** `YouAreTheSongNow_Design_Operating_System.md` was never found; reconstituted OS remains unless owners supply the original.

---

## Questions requiring GPT / owner review

### Create-flow (new)

1. Amend First Build + Onboarding contracts for DNA-first Create? (Required before implementation.)
2. Where do **portraits** sit in the new flow?
3. Final DNA dimension labels vs provisional Emotional Core / World / etc.?
4. Is `originalVisualMoment` selectable or system-owned?
5. **Variation** vs **Reimagine** vs regenerate — definitions?
6. **Save** = download, gallery pin, or both?
7. Explore Options entry pattern (link vs gate vs segment)?
8. **Discover** destination before chrome change?
9. Visual directions: new model call now, or heuristic MVP first?
10. Which Fine Tune knobs survive (quality, orientation, no-text, special, internal style)?

### Carry-over from Phase 1 audit

1. Confirm reconstituted `DESIGN-OPERATING-SYSTEM.md` vs any missing original file.
2. `/docs/design/` vs `/design/` authority split (current proposal: permanent OS vs round-ops).
3. Showcase in guest primary nav?
4. Home/Showcase in Flutter v1?
5. Owner admin web-only forever?
6. Approve proposed semantic token JSON before CSS migration?
7. Phase 2 priority: a11y vs token hygiene?

---

## Recommended next design task (single highest leverage)

**Song DNA selection interaction design** (GPT + Cursor documentation/wireframe pass — still no production Create refactor):

1. Map provisional dimension labels → exact `song-dna-v2.0` fields and customer-safe copy.
2. Specify progressive multi-select (“add another layer”) states and empty/loading/unavailable.
3. Resolve **portrait placement** relative to DNA.
4. Define Quick Generate CTA + credit/paywall handoff on the DNA-ready screen.
5. Produce a review pack under `design/review/` when owners want visuals — structural first.

This unblocks Explore Options / Fine Tune / generation checklist design without boiling the ocean.

---

## Git

- **Branch:** `cursor/design-system-audit-95fa`
- **Primary delivery commit (this turn):** `1b43cd38747047601202567aa3b1f11bc9c2f72d`
- **Scope this turn:** documentation / vault / dashboard only — no Create template or CSS behavior changes
