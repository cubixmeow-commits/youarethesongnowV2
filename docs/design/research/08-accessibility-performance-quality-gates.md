# Research Report 08 — Accessibility, Performance, and Invisible Premium Quality

**Builds on:** Report 07 made motion purposeful and state-driven. This report defines the nonvisual gates required before any slice can be called premium.

## Executive conclusion

A premium interface must remain clear under keyboard navigation, assistive technology, reduced motion, slow networks, error states, and narrow screens. Accessibility and performance should therefore be **acceptance criteria for every component and screen**, not a final cleanup phase.

Apple's current accessibility guidance explicitly recommends familiar interactions, sufficiently sized controls, reduced motion support, and breaking complex workflows into focused interactions. Flutter's adaptive guidance similarly emphasizes touch-first design, keyboard support, shared smaller widgets, and preserving state across layout changes.

## Accessibility gates

### Semantics and structure

Every screen:

- one clear page/screen heading
- logical heading order
- meaningful landmark/region structure
- icon-only buttons have accessible names
- selection state is programmatically exposed
- loading/error/success changes are announced selectively, not by putting entire changing panels in `aria-live`

### Touch and pointer

Target premium default:

- ~44px minimum interactive box
- primary actions often 48–56px high
- sufficient spacing between adjacent destructive/secondary controls
- whole selection cards tappable

### Keyboard

Every customer workflow must work without pointer input:

- logical tab order
- visible focus
- Enter/Space activates selection cards
- dialogs trap and restore focus correctly
- Escape closes dismissible contextual layers
- hover never reveals the only action

### Contrast and state

- selected state not color-only
- disabled state remains legible
- tertiary text still meets required contrast at its size
- focus ring has a semantic token and passes against all surfaces
- imagery overlays use tested scrims when text must sit on art

### Motion preferences

When reduced motion is requested:

- suppress nonessential travel/scale
- shorten or replace reveals with fades/direct state
- do not repeatedly animate peripheral elements
- preserve all semantic feedback

## Known repo accessibility debt to eliminate through canonical rebuilds

- missing real Create `<h1>`
- Gallery empty state hidden from AT
- broken Showcase focus outline
- Style/listbox keyboard incompleteness
- inconsistent focus management
- inconsistent confirm patterns
- some undersized progress/touch targets
- overly broad live-region behavior

These should be retired as corresponding components are rebuilt rather than retained as permanent exceptions.

## Performance gates

### Images

Because artwork is the product hero:

- serve correctly sized thumbnails in grids
- lazy-load offscreen gallery images
- preserve aspect ratio to avoid layout shift
- preload only the next truly important image/state
- avoid decoding dozens of full-resolution artworks at once

### Interaction latency

- pressed state must not wait on network
- API calls show immediate honest pending state
- disable or idempotently protect repeated submissions
- Explore results should eventually be cached/persisted so repeated taps do not repeatedly pay latency/cost

### JavaScript / DOM

The current mega `app.js` is a portability and maintenance risk. Do not rewrite it globally first. As canonical features are rebuilt:

- extract feature modules
- keep state transitions explicit
- reduce DOM query coupling
- create reusable rendering/component helpers only where they correspond to real semantic components

### CSS

- migrate feature literals to semantic tokens during each rebuilt slice
- delete superseded legacy rules
- avoid deep selectors tied to specific page DOM
- gate hover effects behind fine-pointer capability

### Animation

- animate transform/opacity where practical
- avoid layout-heavy continuous animation
- test generation/reveal on real phone hardware
- keep decorative effects off large image grids

## Failure-state quality

Every networked interaction requires:

- pending
- timeout
- provider failure
- retry
- no-data/empty
- stale/recovery behavior where relevant

The Gemini Explore debugging process demonstrated why sanitized diagnostics are valuable in private development. Production UI later should translate these into useful user recovery actions while keeping detailed codes owner/log-only.

## Quality checklist for every slice

A slice is not complete until:

1. primary task is obvious
2. phone at ~320px does not clip or horizontally scroll
3. touch targets are comfortable
4. keyboard workflow works
5. screen reader semantics make sense
6. selected/focus/disabled/loading/error states exist
7. reduced motion has a defined alternative
8. API retry behavior is intentional
9. no avoidable layout shift
10. desktop does not expose extra complexity without reason
11. screenshot evidence exists
12. tests pass

## Independent reviewer pass

**Risk:** a strict checklist can lead to box-ticking rather than good design.

**Countermeasure:** keep the five qualitative axes from the premium plan—hierarchy, restraint, craft, identity, portability—alongside the objective gates.

**Risk:** performance optimization too early can slow experimentation.

**Countermeasure:** distinguish architecture constraints from micro-optimization. Prevent known bad patterns now; profile and optimize expensive paths after the interaction is approved.

## Decisions passed to Report 09

Report 09 should compare the tools and prototyping surfaces we should actually use: repo-native prototypes, Figma, screenshots, image generation/reference work, component labs, Cursor, ChatGPT/Work, and optional Codex—assigning each a specific role rather than adding tools for their own sake.

## Sources

- Apple HIG Accessibility: https://developer.apple.com/design/human-interface-guidelines/accessibility
- WCAG 2.2: https://www.w3.org/TR/WCAG22/
- Flutter adaptive best practices: https://docs.flutter.dev/ui/adaptive-responsive/best-practices
- Flutter performance guidance: https://docs.flutter.dev/perf/best-practices
