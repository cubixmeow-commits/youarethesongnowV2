# Design OS phases

| Phase | Status | Gate to exit |
| --- | --- | --- |
| **1 — Audit & structure** | **Done + Create-flow structural spec (2026-08-31)** | GPT / design-director review of `CURSOR-HANDOFF.md` |
| **2 — Foundations** | Not started | Owner + GPT approval of token adoption + critical a11y/state fixes |
| **3 — Components** | Not started | Review pack for shared partials/widgets |
| **4 — Screens** | Not started (Create structural docs only) | Screen-by-screen review packs; Create UX after contract amendment |
| **5 — Flutter parity docs freeze** | Not started | Separate Flutter start authorization |

## Create-flow documentation (Phase 1 extension)

Approved structural architecture is recorded in `docs/design/screens/create-flow.md`. **No Create production refactor** in this extension. Implementation requires First Build / Onboarding contract amendment plus a focused design pass.

## Phase 2 candidate scope (proposal only)

Small, reviewable slices — not a redesign:

1. Adopt breakpoint CSS variables (behavior-neutral)
2. Fix showcase focus ring + gallery empty a11y
3. Unify confirm dialogs
4. Add button disabled/loading during auth/create submits
5. Replace hardcoded OKLCH with tokens where values already match
6. Document retry + offline patterns; implement minimal offline banner

Stop after each slice for visual review if it changes pixels.

## Highest-leverage next design task (recommendation)

After this Create-flow architecture review: **Song DNA selection interaction design** (dimension labels mapped to `song-dna-v2.0`, progressive multi-select, loading/unavailable states, and Quick Generate CTA) — still documentation/wireframe level until contracts are amended. Resolve portrait placement in the same pass.
