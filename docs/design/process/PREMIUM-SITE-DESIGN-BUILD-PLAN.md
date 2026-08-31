# YouAreTheSongNow — Premium Site Design + Build Program

**Status:** Canonical planning document for the next design phase  
**Scope:** Mobile-first web product, responsive desktop expansion, Flutter portability  
**Working model:** GPT = design director / UX critic / design-system architect. Cursor = implementation engineer / repo analyst / test runner.

## North star

Build YouAreTheSongNow so it feels like a premium native creative product, not a generic AI dashboard or a desktop website squeezed onto a phone.

The web product must establish the interaction model, visual language, component system, motion rules, responsive behavior, accessibility, and information hierarchy that can later transfer cleanly to Flutter on iOS and Android.

### Locked product principles

1. **AI removes decisions by default and offers intelligent choices when the user asks for control.**
2. **Song DNA is the creative control surface.** Avoid generic image-generator controls when Song DNA can express the intent more naturally.
3. **Quick Generate is the default path.** `Song → Song DNA → Generate`.
4. **Explore Options is intelligent progressive disclosure.** AI creates song-specific visual directions; these are not a fixed style preset catalog.
5. **Generated artwork is the visual hero.** Product chrome supports the creation rather than competing with it.
6. **Mobile is canonical.** Desktop expands the same architecture rather than becoming a different product.
7. **Portrait management lives at the top of Gallery for now.** Create ultimately uses the active/default portrait automatically.
8. **No fake progress.** Generation progress must communicate truthful states or meaningful creative stages.
9. **Premium means restraint, clarity, rhythm, polish, and strong interaction design — not more decoration.**

---

# Operating model

Every major UI area moves through the same loop:

`Inspect → Define intent → Wireframe → Critique → Refine → Specify → Implement → Test → Visual review → Iterate → Lock`

Do not run a giant redesign in one pass.

Each screen/component is treated as an individual design problem, while the design OS enforces consistency across the product.

## GPT responsibilities

GPT should:

- define product and UX intent
- challenge unnecessary decisions and controls
- establish hierarchy and interaction architecture
- create/maintain design-system rules
- evaluate screenshots from mobile and desktop
- run focused critique passes on each major interaction
- identify inconsistencies and design debt
- write precise Cursor handoffs
- decide what becomes canonical after review

## Cursor responsibilities

Cursor should:

- inspect the actual repo before proposing implementation
- map designs onto existing routes/components/contracts
- identify reusable code and backend constraints
- implement focused slices on `main` while this remains a private development product
- preserve working functionality unless the task explicitly replaces it
- add automated tests for functional/state behavior
- test responsive behavior and accessibility
- produce screenshots for review when possible
- update `docs/design/CURSOR-HANDOFF.md` after every substantial slice
- never silently invent product decisions when a design question should go back to GPT

---

# Phase 0 — Stabilize the new creation model

Before the full visual redesign, make the new product architecture function cleanly.

## Immediate Explore cleanup

Preserve the working Gemini Explore pipeline.

Improve the first-build interaction:

- remove customer-facing text such as `Uses Gothic Romance internally`
- keep internal StyleMap mapping owner/dev-only
- first option receives a subtle `Recommended` treatment instead of explanatory prose
- make the entire direction card a strong tap target
- selected card gets a clear premium selected state
- after selection, expose one dominant CTA such as `Create this direction`
- hide/collapse the legacy `Choose your world` style grid while an AI Explore direction is active
- provide a deliberate way to return to manual direction selection if desired
- keep `Generate for me` as the dominant default action
- preserve working diagnostics in private/dev mode but do not let diagnostics define the product UI

## Exit criteria

- Quick Generate works end-to-end
- Explore returns three usable Song-DNA-specific directions
- a user can clearly select one direction and continue
- legacy style controls no longer visually compete with Explore
- failure states are understandable and recoverable

---

# Phase 1 — Experience architecture

Create a complete inventory and target flow before visual polishing.

## Canonical product map

Define and document:

- Create
- Gallery
- Discover (planned; validate whether it belongs in first release)
- Account
- Owner/dev surfaces separate from customer product

## Canonical Create flow

Target:

`Create Home → Choose Song → Song DNA → Quick Generate OR Explore Options → optional Fine Tune → Generation → Reveal → Save / Share / Variation / Reimagine`

For every step define:

- user goal
- primary action
- optional actions
- information needed
- loading state
- empty state
- error/retry state
- success transition
- mobile navigation behavior
- desktop expansion behavior
- Flutter portability notes

## Deliverables

- journey map
- screen map
- state map
- current→target route/component mapping
- removal/deprecation list for legacy interactions

---

# Phase 2 — Visual direction and brand expression

Before styling every screen, define what premium YouAreTheSongNow visually means.

## Explore 2–3 visual directions

Each direction must be evaluated against:

- premium creative-app feel
- visual emphasis on generated imagery
- platinum/blue/black brand foundation
- readability in dark environments
- distinctive identity without visual noise
- native-app feel on iPhone
- desktop elegance
- compatibility with a Flutter implementation

Do not merely make mood boards. Apply each candidate direction to the same small reference set:

1. Create Home
2. Song DNA selector
3. Explore direction card
4. Generation state
5. Result/reveal
6. Gallery

Choose one system after comparative review.

## Deliverables

- visual north-star spec
- typography rules
- color/surface hierarchy
- image treatment rules
- icon language
- border/radius/shadow/elevation philosophy
- density and whitespace philosophy

---

# Phase 3 — Design system foundation

Convert the chosen visual direction into reusable tokens and primitives.

## Token layers

Define semantic tokens for:

- background / canvas
- elevated surface
- interactive surface
- selected surface
- borders / hairlines
- primary text / secondary / tertiary / disabled
- primary accent
- secondary accent
- success / warning / destructive
- focus
- spacing scale
- radii
- typography scale
- motion durations/easing
- responsive breakpoints where genuinely needed

Avoid one-off literal values in feature CSS unless documented as an exception.

## Primitive components

Lock premium versions of:

- primary / secondary / quiet / destructive buttons
- icon buttons
- app bar
- mobile bottom navigation
- desktop navigation rail
- cards
- selection cards
- chips/tags
- text fields / search fields
- dialog / sheet
- toast/status messaging
- empty state
- progress/creation state
- artwork tiles
- portrait tiles
- segmented controls where needed

## Quality rules

- minimum touch target ~44px
- keyboard and screen-reader behavior defined
- no hover-only primary interaction
- strong focus treatment
- motion honors reduced-motion settings
- component states specified: default / pressed / selected / focused / disabled / loading / error

---

# Phase 4 — Screen-by-screen premium design

Work in product-value order rather than route order.

## Priority A — Creation loop

1. Create Home / song entry
2. Song search + selection
3. Song DNA selection
4. Quick Generate decision state
5. Explore Options
6. Fine Tune
7. Generation experience
8. Reveal/result

Each receives its own focused design critique and implementation pass.

## Priority B — Creation library

9. Gallery with portraits at top
10. Image detail
11. Save/share/variation/reimagine interactions
12. Empty and first-use Gallery

## Priority C — Product shell

13. mobile navigation
14. desktop rail/context panel
15. account
16. membership/usage/credits
17. onboarding and first-use education

## Priority D — Secondary/public surfaces

18. Discover/showcase if retained
19. marketing/landing surface
20. owner/dev surfaces — functional and clear, but they do not need consumer-level polish first

---

# Phase 5 — Interaction and motion system

Motion should communicate state and make creation feel alive, not decorate every tap.

Define:

- navigation transitions
- card selection feedback
- Song DNA layer addition/removal
- Explore loading and option arrival
- Generate handoff
- progress-stage transitions
- final artwork reveal
- gallery insertion
- sheets/dialogs

## Signature moment

The generation → result transition should become a recognizable product moment: the UI recedes, artwork takes over, then controls arrive progressively.

Avoid fake image previews or fake percentages unless backed by real provider output/progress.

---

# Phase 6 — Responsive system

Mobile design is canonical.

## Phone

- one dominant task per screen
- bottom navigation on top-level destinations only
- focused creation steps may hide app navigation
- thumb-reachable primary actions
- no horizontal overflow at 320px

## Tablet

- preserve mobile hierarchy
- use extra space for larger artwork, two-column supporting content, or contextual panels
- do not simply stretch phone cards

## Desktop

Translate rather than redesign:

`Navigation rail + main creative workspace + optional contextual panel`

Use width to improve comparison, preview, and context — not to expose more unnecessary controls.

For every major screen, document phone / tablet / desktop behavior together.

---

# Phase 7 — Accessibility, performance, and implementation quality

Premium includes invisible quality.

## Accessibility gate

- semantic headings
- screen-reader names/states
- full keyboard access
- dialogs/sheets manage focus correctly
- contrast passes
- selected state is not color-only
- touch targets meet mobile guidance
- reduced motion supported

## Performance gate

- image sizes and lazy loading reviewed
- critical UI loads without avoidable blocking
- animation avoids layout thrash
- mobile rendering remains smooth
- API loading states never freeze the interface

## Code-quality gate

- components use design tokens
- duplicated feature styles are reduced
- old styling is removed when a canonical component replaces it
- no hidden desktop assumptions in mobile components
- Flutter portability notes stay current

---

# Phase 8 — Review loop and acceptance rubric

No major slice is complete after implementation alone.

## Required review artifacts

For each major slice Cursor should provide, where tooling permits:

- iPhone-width screenshot
- narrow phone screenshot (~320–375px)
- desktop screenshot
- list of states tested
- test results
- known deviations

GPT reviews against five axes:

1. **Hierarchy** — obvious primary task/action
2. **Restraint** — no unnecessary controls/content
3. **Craft** — spacing, type, states, alignment, polish
4. **Product identity** — feels like YouAreTheSongNow, not generic AI SaaS
5. **Portability** — interaction can translate naturally to Flutter

A slice should be iterated until all five are strong before being marked canonical.

---

# Phase 9 — Flutter handoff discipline

The web implementation should continuously produce Flutter-ready specifications rather than waiting until the end.

For every canonical component/screen record:

- semantic purpose
- props/data contract
- state machine
- gesture behavior
- responsive behavior
- animation behavior
- token usage
- accessibility behavior
- platform-specific exceptions

Do not port HTML/CSS literally. Port the component and interaction contract.

---

# Build order

Recommended implementation sequence:

1. Clean up working Explore first build
2. Formalize Create journey/state architecture
3. Run visual-direction comparison
4. Lock foundational tokens/primitives
5. Rebuild Create Home + Song selection
6. Build Song DNA selector
7. Integrate Quick Generate + Explore into the canonical Create flow
8. Build generation experience
9. Build premium reveal/result
10. Rebuild Gallery + portraits
11. Rebuild shell/navigation responsively
12. Account/membership/onboarding
13. Discover/marketing as needed
14. Full accessibility/performance cleanup
15. Flutter handoff consolidation

---

# Working rules while the product has no users

- Cursor may work directly on `main` for focused changes.
- Keep commits small enough to identify regressions.
- Preserve functioning backend contracts unless explicitly changing them.
- Do not preserve weak UI merely because it already exists.
- Delete obsolete legacy UI once its replacement is proven.
- Prefer real working slices over static mockups, but do not skip the design/critique phase.
- Update `CURSOR-HANDOFF.md` after every substantive task.

# Definition of done for the premium redesign

The redesign is complete when:

- a new user can understand and generate without instruction
- Quick Generate feels effortless
- Explore feels intelligent rather than technical
- Song DNA is visibly the product's unique creative language
- mobile feels like a premium native app
- desktop feels like the same premium product expanded intelligently
- generated art always remains the visual focus
- interaction patterns are consistent and accessible
- visual and behavioral systems are documented well enough to reproduce in Flutter
- legacy generic AI-generator patterns are no longer driving the customer experience
