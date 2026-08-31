# Research Report 04 — Adaptive Desktop Expansion Without Creating a Second Product

**Builds on:** Report 03 defined mobile as the canonical interaction model: one dominant task, progressive disclosure, artwork-first composition, and familiar platform patterns.

## Executive conclusion

Desktop should not expose more complexity merely because more pixels are available. It should use width to improve **comparison, context, preview size, navigation persistence, and multi-pane awareness** while preserving the exact same mental model as mobile.

The right long-term structure is an adaptive scaffold:

`Navigation rail + primary creative workspace + optional contextual panel`

This maps naturally to Flutter's current adaptive guidance, which explicitly recommends abstracting shared data/behavior and switching navigation/layout widgets such as NavigationBar ↔ NavigationRail based on available space.

## Adaptive, not merely responsive

Responsive design changes size and placement. Adaptive design can also choose a more suitable composition or control for the available space.

For YouAreTheSongNow:

- phone: bottom nav + focused single-column creation steps
- tablet: preserve phone hierarchy, use additional width for larger art, previews, or an optional companion panel
- desktop: persistent navigation rail, centered creative workspace, optional contextual panel

The information architecture does not change.

## Desktop rules by product area

### Create Home

**Phone:** one-column hero/action, recent work below.

**Desktop:** larger creative entry area centered, recent work can become a side or lower grid. Avoid a dashboard of cards.

### Song selection

**Phone:** search dominates, results fill screen.

**Desktop:** search and recent/results can coexist in a restrained split if it speeds selection. Do not introduce filters unless real usage requires them.

### Song DNA

**Phone:** recommended DNA card + progressive "add layer" interaction.

**Desktop:** more DNA dimensions can be visible simultaneously for comparison, but selection mechanics and labels must remain identical.

### Explore Options

**Phone:** vertical stack of three rich selection cards.

**Desktop:** three cards may sit in a horizontal comparison row if descriptions remain readable. Artwork/visual previews could eventually make this a particularly strong use of desktop width.

### Generation

**Phone:** immersive centered state, app chrome reduced.

**Desktop:** larger central generation canvas with Song DNA context to the side only if it clarifies what is being transformed.

### Reveal

**Phone:** near-full-width artwork with controls arriving progressively.

**Desktop:** artwork can become dramatically larger; secondary actions and metadata can occupy a quiet side panel so they do not overlay the work.

### Gallery

**Phone:** portraits strip/section at top, creation grid below.

**Desktop:** portraits can become a compact persistent top shelf or side context; gallery gets richer grid density without masonry dependence.

## Width should be used for context, not control proliferation

A common desktop failure is to reveal all advanced settings because there is room. That violates the product principle.

Instead use width to show:

- larger art
- richer comparison between AI directions
- current Song DNA summary
- creation history/context
- nonmodal details

Keep Fine Tune optional even on desktop.

## Breakpoint philosophy

Avoid treating device names as the primary logic. Flutter's adaptive guidance encourages reasoning from available space and choosing appropriate widgets/layouts. The web implementation should therefore use a small number of meaningful layout bands rather than dozens of device-specific breakpoints.

Suggested design bands to test, not blindly hard-code:

- compact: phone
- medium: large phone / tablet portrait / narrow window
- expanded: tablet landscape / laptop / desktop

The existing ≥900px rail breakpoint is a useful starting point but should be validated against content needs.

## Shared state, different composition

For future Flutter portability, a screen should have one state model and multiple compositions.

Example conceptual contract:

`ExploreState { loading, directions, selectedDirection, error }`

Phone widget tree and desktop widget tree both consume that state. This is better than writing desktop-only behavior into CSS/DOM assumptions now.

## Input adaptation

Desktop requires more than width changes:

- keyboard focus order must be intentional
- hover can enhance, never reveal the only action
- selected cards should respond to Enter/Space
- escape/back semantics should be defined for dialogs/panels
- larger screens can support shortcuts later, but shortcuts cannot become required

## Content width and readability

The current app has several competing max widths. The redesign should define semantic layout widths, for example:

- reading/text measure
- form/decision measure
- creative-workspace max
- gallery/grid max
- full-bleed artwork behavior

These should become tokens/layout primitives rather than arbitrary page values.

## Independent reviewer pass

**Risk:** a three-pane desktop shell can itself feel like productivity software.

**Countermeasure:** the contextual panel must be optional and content-driven. Many screens should remain simple centered compositions. Do not force every screen into three columns.

**Risk:** matching mobile too literally can waste desktop potential.

**Countermeasure:** preserve the mental model, not the geometry. Desktop should improve comparison, context, and artwork scale where those gains are real.

**Risk:** tablet becomes an afterthought between phone and desktop.

**Countermeasure:** every screen spec must show compact/medium/expanded behavior. Flutter later benefits directly because tablets/foldables are already considered.

## Decisions passed to Report 05

Report 05 should define the design-system architecture needed to support one semantic product across compact/medium/expanded layouts and later map the same system into Flutter Theme/Data/components.

## Sources

- Flutter adaptive overview: https://docs.flutter.dev/ui/adaptive-responsive
- Flutter general adaptive approach: https://docs.flutter.dev/ui/adaptive-responsive/general
- Flutter adaptive best practices: https://docs.flutter.dev/ui/adaptive-responsive/best-practices
- Apple HIG Layout/adaptability: https://developer.apple.com/design/human-interface-guidelines/layout
