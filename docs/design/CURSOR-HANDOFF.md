# CURSOR-HANDOFF — Explore cleanup + premium redesign program

**Date:** 2026-08-31  
**Working branch:** `main`  
**Phase:** Working Gemini Explore → product-quality integration, followed by systematic premium redesign

## Live result confirmed

Gemini Explore now works on the private Hostinger site and returns exactly three Song-DNA-specific visual directions.

Observed successful options included distinct treatments such as:

- `Cathedral of Shadows` — gothic, monumental, grief/longing
- `Threshold of Eternity` — grounded cinematic realism, mourning/spiritual isolation
- `Elegiac Reverie` — painterly, timeless, muted warmth, love/loss

This validates the product concept: AI can translate Song DNA into meaningful creative directions and privately bridge those into the current generation system.

## Locked product principles

1. **AI should remove decisions by default and offer intelligent choices when the user asks for control.**
2. **Song DNA is the creative control surface.**
3. Default path: `Song → Song DNA → Generate`.
4. Optional path: `Song → Song DNA → Explore Options → AI-generated visual direction → optional Fine Tune → Generate`.
5. AI Explore directions are not generic presets.
6. Portrait management belongs at the top of Gallery for now. Create should eventually use the active/default portrait automatically.
7. Mobile is canonical; desktop expands the same product architecture.

## Immediate task — clean up the working Explore experience

Preserve the now-working Gemini pipeline. Do not rewrite the provider logic unless necessary for a regression.

Improve only the product interaction around the successful result:

1. Remove customer-facing implementation text such as:
   - `Uses Gothic Romance internally`
   - `Uses Cinematic Realism internally`
   - `Uses Heirloom Oil Portrait internally`
2. Keep StyleMap mapping available only in owner/dev diagnostics if useful.
3. Replace the explanatory sentence `The first option is Gemini’s strongest fit` with a subtle visual `Recommended` treatment on the first card.
4. Make each direction card a large, clear, accessible tap target.
5. Add a strong selected state.
6. After a direction is selected, expose one dominant continuation CTA such as `Create this direction` (choose wording that fits the current Review/generation contract without introducing misleading behavior).
7. When an Explore direction is active, hide or collapse the legacy `Choose your world` grid so the new intelligent-control system and the old style picker do not compete visually.
8. Provide a deliberate secondary path back to manual direction/style selection for development/fallback use.
9. Keep `Generate for me` visually primary before Explore is opened.
10. Keep safe private-build diagnostics available for failures, but diagnostics should not dominate successful customer UI.
11. Do not break Quick Generate, current draft/job contracts, credits/paywall, portraits, or generation.
12. Test mobile touch behavior, keyboard behavior, loading, failure, retry, direction selection, and transition to Review/generation.
13. Run the full test suite.
14. Commit directly to `main`.

## Premium redesign program

A canonical program now exists at:

`docs/design/process/PREMIUM-SITE-DESIGN-BUILD-PLAN.md`

Read it in full before beginning broad redesign work.

This is the governing implementation sequence:

1. Clean up working Explore first build
2. Formalize Create journey/state architecture
3. Run visual-direction comparison
4. Lock foundational tokens/primitives
5. Rebuild Create Home + Song selection
6. Build Song DNA selector
7. Integrate Quick Generate + Explore into canonical Create flow
8. Build generation experience
9. Build premium reveal/result
10. Rebuild Gallery + portraits
11. Rebuild shell/navigation responsively
12. Account/membership/onboarding
13. Discover/marketing as needed
14. Full accessibility/performance cleanup
15. Flutter handoff consolidation

Do **not** execute the entire redesign as one giant task. Work in focused slices using the loop:

`Inspect → Define intent → Wireframe → Critique → Refine → Specify → Implement → Test → Visual review → Iterate → Lock`

GPT is design director / UX critic / design-system architect. Cursor is implementation engineer / repo analyst / test runner.

## Current task stopping point

For this Cursor run, implement **only the Explore cleanup** above.

Then update this handoff with:

- files changed
- interaction changes
- tests/results
- final commit hash
- iPhone retest steps
- desktop implications discovered
- any design questions that should go back to GPT
- recommended next slice from the premium build plan

Do not begin the full visual redesign until GPT reviews the cleaned-up Explore implementation.
