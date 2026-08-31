# Research Report 10 — Final Premium Design Operating Model

**Builds on:** Reports 01–09. This is the synthesis and recommended final structure for planning, designing, implementing, reviewing, and later porting YouAreTheSongNow to Flutter.

## Executive conclusion

The project should proceed as a **mobile-canonical, adaptive, repo-native product-design program** with one design authority, one persistent design OS, bounded implementation-agent tasks, and mandatory visual/state review before each slice becomes canonical.

Do not begin by redesigning every page. First lock the complete Create journey/state architecture, then compare 2–3 visual systems on the same reference screens. Once one visual direction is chosen, formalize only the design-system primitives required by the next screen and rebuild the product in value order.

The website becomes the working prototype and behavioral specification for the future Flutter app.

---

# Final product principles

1. **AI removes decisions by default and offers intelligent choices when the user asks for control.**
2. **Song DNA is the primary creative control surface.**
3. **Quick Generate is the default creation path.**
4. **Explore Options is generated from the selected Song DNA, not a fixed style catalog.**
5. **Fine Tune is optional progressive disclosure.**
6. **Artwork is the visual hero.**
7. **Mobile/touch interaction is canonical.**
8. **Desktop is an adaptive expansion, not another product.**
9. **Portrait management lives in Gallery; Create eventually uses the active/default portrait automatically.**
10. **No fake progress.**
11. **Premium means clarity, restraint, craft, reliability, accessibility, and emotional specificity—not decoration.**
12. **Every canonical web component must have a platform-neutral contract suitable for Flutter.**

---

# Final team/tool model

## GPT — design director / product architect / critic

Owns:

- research synthesis
- product/UX intent
- hierarchy
- interaction architecture
- visual-direction briefs
- component semantics
- screenshot critique
- acceptance decisions
- Cursor task briefs
- final design-system governance

## Cursor — primary implementation engineer

Owns:

- repo inspection
- implementation
- migrations
- automated tests
- responsive verification
- screenshots/logs/proof
- documenting technical constraints
- handoff updates

## Figma — optional visual workbench

Use specifically for:

- Candidate A/B/C comparison
- typography/surface exploration
- component variants and modes
- compact/expanded side-by-side layouts

Repo remains canonical.

## Optional Codex — independent engineering/review agent

Use selectively for:

- second code-review pass
- accessibility/test audits
- low-overlap parallel engineering
- later Flutter prototypes/migrations

---

# Final persistent repo structure

Recommended `/docs/design/` responsibilities:

```text
/docs/design/
  DESIGN-OPERATING-SYSTEM.md
  CURSOR-HANDOFF.md

  research/
    01-current-state-and-design-debt.md
    ...
    10-final-premium-design-operating-model.md

  foundations/
    principles.md
    color.md
    typography.md
    spacing.md
    motion.md
    tokens.md

  components/
    inventory.md
    <canonical component specs>

  screens/
    inventory.md
    create-flow.md
    <canonical screen specs>

  process/
    PREMIUM-SITE-DESIGN-BUILD-PLAN.md
    review-rubric.md
    screenshot-protocol.md

  audits/
    accessibility/
    performance/
    visual-consistency/

  flutter/
    portability.md
    component-map.md
    screen-map.md
```

Detailed historical design rounds can remain in `/design/`, but new canonical decisions belong under `/docs/design/`.

---

# Final design/build sequence

## Phase 0 — functional stabilization

**Status:** substantially complete for Explore proof-of-concept.

Cursor has now cleaned up the Explore interaction:

- no customer-facing StyleMap names
- Recommended chip
- full-card selection
- clear selected state
- `Create this direction` CTA
- legacy style grid collapses during AI direction mode
- manual-style escape path remains
- 981 tests pass in the current handoff

Remaining Phase 0 decision: decide how much of quality/format/no-text belongs behind Fine Tune in the final architecture.

## Phase 1 — canonical Create journey/state architecture

This should be the **next major desktop session task**.

Define the final state machine for:

`Create Home → Choose Song → Song DNA → Quick Generate / Explore → optional Fine Tune → Generation → Reveal`

For every state define:

- user goal
- input/data requirements
- primary action
- secondary actions
- navigation/back behavior
- loading
- empty/unavailable
- error/retry
- success transition
- compact/medium/expanded composition
- accessibility requirements
- Flutter contract

Resolve legacy-removal questions at this stage, including:

- removal of People stage from repeat Create
- how active portrait is chosen/changed
- deprecation of the primary manual StyleMap picker
- placement of quality/orientation/no-text/special instructions
- whether Review remains a separate screen/state

**Do not visually redesign the whole product before this state architecture is locked.**

## Phase 2 — comparative premium visual direction

Build Candidate A/B/C on the same six reference screens:

1. Create Home
2. Song DNA selector
3. Explore Options
4. Generation
5. Reveal
6. Gallery + portraits

Candidate families from Report 06:

- Cinematic Editorial
- Luminous Night Studio
- Modern Album Object

Review each at compact and expanded widths.

Choose one system using these axes:

- hierarchy
- restraint
- craft
- product identity
- artwork dominance
- accessibility
- mobile-native feel
- desktop elegance
- Flutter portability

## Phase 3 — foundational system lock

Once art direction is selected:

- freeze semantic token names and values
- build canonical button/icon-button
- navigation bar / rail
- selection card
- Song DNA components
- field/search
- sheet/dialog
- artwork/portrait tile
- status/error/retry
- progress/generation component

Build only what the next screens need.

## Phase 4 — core creation rebuild

Implement in product-value order:

1. Create Home
2. Song search/selection
3. Song DNA selection
4. Quick Generate state
5. Explore Options
6. Fine Tune
7. Generation
8. Reveal/result

Each slice runs the full loop:

`Intent → wireframe → critique → spec → implementation → tests → screenshots → GPT review → revision → lock`

## Phase 5 — library and identity

- Gallery
- portrait shelf/management
- image detail
- share
- variation
- reimagine
- empty/first-use states

## Phase 6 — shell/account/onboarding

- final top-level mobile navigation
- desktop navigation rail
- account
- membership/credits
- onboarding
- first-use portrait flow

## Phase 7 — secondary/public surfaces

- Discover/showcase if retained
- public/marketing site
- owner/dev cleanup

Do not prioritize admin polish over the core creation loop.

## Phase 8 — system-wide quality pass

- accessibility audit
- keyboard/focus audit
- reduced-motion audit
- performance/image audit
- narrow-width audit
- token drift cleanup
- obsolete CSS/JS removal
- screenshot consistency review

## Phase 9 — Flutter implementation

Flutter should consume the already-defined product/component contracts.

Port in the same product order rather than page order.

### Flutter architecture principle

**One state model, adaptive compositions.**

Examples:

- compact navigation → `NavigationBar`
- expanded navigation → `NavigationRail`
- contextual mobile control → `showModalBottomSheet` / sheet pattern
- desktop context → side panel when space allows

Use `MediaQuery.sizeOf` / `LayoutBuilder` and available-space logic instead of hardcoding device types, consistent with current Flutter guidance.

Build touch behavior first; add keyboard/mouse accelerators afterward.

---

# Screen acceptance rubric

A screen is not canonical until it scores strongly on all of these:

## 1. Hierarchy
Can the user identify the primary task/action immediately?

## 2. Restraint
Are unnecessary decisions, labels, containers, and controls removed?

## 3. Craft
Are typography, spacing, alignment, states, transitions, and image treatment polished?

## 4. Identity
Does it feel specifically like YouAreTheSongNow rather than generic AI SaaS?

## 5. Accessibility
Touch, keyboard, screen-reader semantics, contrast, and reduced motion are intentional.

## 6. Reliability
Loading/error/retry/disabled states work and preserve user progress.

## 7. Adaptive quality
Compact/medium/expanded compositions all feel designed rather than stretched.

## 8. Portability
Behavior can be described without reference to HTML/CSS implementation details.

---

# Mandatory evidence per major implementation slice

Cursor should return:

- narrow mobile screenshot (~320–375px)
- current iPhone-class screenshot
- desktop screenshot
- selected/loading/error state screenshots where relevant
- test results
- accessibility notes
- implementation deviations
- Flutter notes
- final commit hash
- updated `CURSOR-HANDOFF.md`

---

# Recommended immediate decisions after the research program

### Explore CTA wording

For the temporary/current flow, `Create this direction` is clear and action-oriented. In the final state architecture, test whether `Use this direction` is better if another review/Fine Tune step follows. CTA wording should describe the *next actual action*, not overpromise image generation if a decision remains.

### Quality / format / no-text controls

They should **not remain permanently visible in the primary Explore path**. Final recommendation: move these into Fine Tune unless user testing demonstrates that one of them is a frequent first-order choice. This best matches the locked principle of AI removing decisions by default.

### Recommended chip

Keep it for now. It helps users understand why option one is first. Visual treatment should be quiet and secondary to the creative direction name.

### Review screen

Do not preserve a separate Review step solely because the old flow has one. During Phase 1 state architecture, decide whether Review has a genuine job (cost confirmation, portrait, final summary) or whether it can collapse into the Generate/Fine Tune state.

---

# Final next desktop-session agenda

When the desktop session begins:

1. Read Reports 01–10.
2. Read latest `CURSOR-HANDOFF.md` and current Create implementation.
3. Build the complete canonical Create state map.
4. Resolve controls/portrait/review/manual-style legacy questions.
5. Update `create-flow.md` and produce final wireframes for compact/expanded.
6. Then begin Candidate A/B/C visual-direction prototypes on the six reference screens.
7. Compare and select the strongest system before broad production styling.

This is the highest-leverage sequence because it prevents us from polishing UI whose structure is still temporary.

---

# Final reviewer verdict

The project is now ready to move from **functional experimentation** into **systematic premium product design**.

The critical discipline is to resist two temptations:

1. polishing the legacy flow because it exists;
2. trying to redesign the entire site in one agent run.

The strongest route is a sequence of small canonical decisions, each verified in real code and then documented for the eventual Flutter build.

## Key external guidance used across the research series

- Apple Human Interface Guidelines: design principles, accessibility, motion
- Flutter adaptive/responsive best practices (current docs reflect Flutter 3.44.x)
- Cursor Cloud Agent workflow/proof-of-work model
- OpenAI Codex multi-agent/skills model
- Figma variables/modes/component-property design-system workflow
- WCAG 2.2 accessibility requirements
