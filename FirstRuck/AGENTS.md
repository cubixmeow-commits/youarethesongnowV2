# FirstRuck agent instructions

These instructions apply to everything inside `FirstRuck/`.

## Required reading

Before planning or changing FirstRuck, read these files in order:

1. `development-vault/START HERE.md`
2. `development-vault/01 Current Project/Current Status.md`
3. `development-vault/01 Current Project/Active Roadmap.md`
4. `development-vault/01 Current Project/Current Architecture.md`
5. `development-vault/04 Decisions/Decision Index.md`

Then read only the task-specific notes linked from `START HERE.md`. Do not scan the entire FirstRuck source tree before reading the vault.

## Authority

When information conflicts, use this order:

1. The owner's latest direct instruction
2. Accepted decisions in `development-vault/04 Decisions/Decision Index.md`
3. `development-vault/01 Current Project/`
4. Verified current implementation and passing tests
5. Task-specific vault notes
6. Older documents, experiments, and research

The original onboarding lab and older Flutter documents are useful evidence, but they do not override the current mobile web experience or the vault.

## Working rules

- Keep provider credentials server-side and out of Git, browser code, screenshots, logs, and prompts.
- Treat route geometry as a candidate, not proof of safety, access, surface, hills, closures, or current conditions.
- Run deterministic safety and eligibility checks before any LLM ranking or explanation.
- Do not let an LLM invent routes, coordinates, conditions, health facts, or progressions.
- Do not persist the onboarding safety answer in browser storage.
- Do not share precise routes, start points, GPS traces, or photo metadata by default.
- Keep example and simulated data visibly labelled.
- Preserve reduced-motion, keyboard, screen-reader, and small-screen behavior.
- Do not add prices, trials, testimonials, ratings, health claims, or community activity unless they are real and approved.
- Update the vault in the same change whenever current status, architecture, an accepted decision, a provider, a contract, or the roadmap changes.

## Completion

Run the checks listed in `development-vault/03 Engineering/Testing and Deployment.md`. Record meaningful changes in `development-vault/05 Operations/Change Log.md`.
