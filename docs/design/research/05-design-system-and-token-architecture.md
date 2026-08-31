# Research Report 05 — Cross-Platform Design System and Token Architecture

**Builds on:** Report 04 established that phone/tablet/desktop should share one semantic product model and adapt composition rather than duplicate behavior.

## Executive conclusion

The premium redesign needs a **semantic, code-owned design system** that can be expressed in web CSS today and Flutter Theme/components later. The most important architectural distinction is between **raw primitives**, **semantic tokens**, and **component contracts**.

A strong system prevents Cursor from solving each screen independently and prevents the future Flutter port from reverse-engineering arbitrary CSS values.

## Token layers

### Layer 1 — primitives

Raw values that rarely appear directly in feature code:

- color ramps
- spacing scale
- radius scale
- type sizes/weights/line heights
- duration/easing values
- opacity values
- elevation/shadow primitives

Example conceptual values:

`blue.500`, `graphite.900`, `space.4`, `radius.lg`, `duration.fast`

### Layer 2 — semantic tokens

Describe intent rather than appearance:

- `surface.canvas`
- `surface.elevated`
- `surface.interactive`
- `surface.selected`
- `content.primary`
- `content.secondary`
- `content.tertiary`
- `border.subtle`
- `border.focus`
- `action.primary`
- `action.primaryPressed`
- `status.error`
- `status.success`

Feature code should prefer semantic names.

### Layer 3 — component tokens / contracts

A canonical button or selection card can define:

- padding
- min height
- radius
- text role
- icon size
- selected/focus/disabled behavior
- animation

These can be represented differently on web and Flutter while preserving the same contract.

## Why semantic tokens matter for Flutter

If web CSS says `background: oklch(...)` everywhere, Flutter must rediscover meaning later. If web says `--surface-selected`, Flutter can map the same semantic token to a `ColorScheme` extension or custom theme extension.

The future port should translate intent, not CSS syntax.

## Modes and contexts

Figma's current variables system explicitly supports modes for themes and device/context variations, and can import/export DTCG-format design tokens. That is useful as a design tool, but the canonical source for this project should remain version-controlled JSON/docs in the repo.

Potential modes:

- dark default
- high contrast (future)
- compact / medium / expanded spacing only where needed
- reduced-motion behavior as a system mode/setting concept

Avoid creating device modes for every token. Most typography/colors should remain stable; layout composition should do the adaptive work.

## Component system priorities

Build primitives in the order the core experience needs them:

1. buttons / icon buttons
2. app bar / navigation bar / navigation rail
3. selection card
4. Song DNA card/chip/layer control
5. search field
6. artwork tile
7. portrait tile
8. status/error/retry pattern
9. sheet/dialog
10. progress/creation-stage component
11. gallery grid/list primitives
12. segmented/advanced controls only if Fine Tune needs them

## Required component-state contract

Every interactive component should specify:

- default
- hover (desktop enhancement only)
- pressed
- focused
- selected
- disabled
- loading
- error where relevant

For a selection card, also specify:

- recommended
- selected + recommended
- keyboard activation
- screen-reader selected state

This is more valuable than dozens of visual variants.

## Component documentation format

Each component doc should include:

- purpose
- when to use
- when not to use
- anatomy
- props/data inputs
- states
- interaction behavior
- accessibility
- responsive/adaptive behavior
- token usage
- Flutter mapping
- screenshot/examples

Storybook is a strong general model for component documentation and visual states, but this PHP/vanilla-JS repo does not need to adopt Storybook immediately. The principle to borrow is **examples/stories as executable documentation**. A lightweight internal component-lab page may be more appropriate initially.

## Figma's role

Figma is useful for:

- visual-system comparison
- variable/mode exploration
- component variants/properties
- quick compact/expanded comparisons
- prototyping motion/flow intent

But Figma must not become a detached second design system. Any locked token/component decision should be copied/exported into the repo immediately.

## DTCG-compatible token recommendation

Use Design Tokens Community Group-compatible JSON where practical, because tooling increasingly understands that structure and Figma supports importing DTCG JSON modes. The repo can then generate or manually map:

- CSS custom properties
- Flutter theme constants/extensions
- documentation tables

Do not over-engineer a token build pipeline before the visual direction is locked.

## Current system migration strategy

The repo already has useful token scales. Migrate incrementally:

1. inventory existing token use
2. freeze semantic names
3. split ambiguous tokens (e.g. focus color vs focus shadow)
4. move hardcoded values into semantic tokens as screens are rebuilt
5. remove legacy aliases only after usage reaches zero

Avoid a giant token migration with no visible product benefit.

## Independent reviewer pass

**Risk:** design-system work can become an abstraction project that delays the product.

**Countermeasure:** only build primitives required by the next canonical screen. Expand system coverage through real product slices.

**Risk:** DTCG/Figma/token tooling could create unnecessary complexity for a small project.

**Countermeasure:** keep the source files simple. The value is semantic naming and portability, not enterprise tooling.

**Risk:** too many variants can make components harder for agents to use correctly.

**Countermeasure:** component APIs should expose intentional properties, similar to Figma component properties: a small set of clear booleans/variants/slots rather than freeform overrides.

## Decisions passed to Report 06

Report 06 should research the actual visual-art-direction strategy: how to turn the existing platinum/blue/black and serif/sans foundation into a distinctive premium creative product, how to compare candidate systems, and how to avoid generic AI-SaaS aesthetics.

## Sources

- Figma design systems: https://www.figma.com/design-systems/
- Figma variables/modes: https://help.figma.com/hc/en-us/articles/14506821864087-Overview-of-variables-collections-and-modes
- Figma component properties: https://help.figma.com/hc/en-us/articles/5579474826519-Explore-component-properties
- Figma modes + DTCG import/export: https://help.figma.com/hc/en-us/articles/15343816063383-Modes-for-variables
- Storybook component documentation model: https://storybook.js.org/docs/writing-docs/index
