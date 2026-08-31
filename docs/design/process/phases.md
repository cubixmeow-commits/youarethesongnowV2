# Design OS phases

| Phase | Status | Gate to exit |
| --- | --- | --- |
| **1 — Audit & structure** | **Done (this delivery)** | GPT / design-director review of `CURSOR-HANDOFF.md` |
| **2 — Foundations** | Not started | Owner + GPT approval of token adoption + critical a11y/state fixes |
| **3 — Components** | Not started | Review pack for shared partials/widgets |
| **4 — Screens** | Not started | Screen-by-screen review packs |
| **5 — Flutter parity docs freeze** | Not started | Separate Flutter start authorization |

## Phase 2 candidate scope (proposal only)

Small, reviewable slices — not a redesign:

1. Adopt breakpoint CSS variables (behavior-neutral)
2. Fix showcase focus ring + gallery empty a11y
3. Unify confirm dialogs
4. Add button disabled/loading during auth/create submits
5. Replace hardcoded OKLCH with tokens where values already match
6. Document retry + offline patterns; implement minimal offline banner

Stop after each slice for visual review if it changes pixels.
