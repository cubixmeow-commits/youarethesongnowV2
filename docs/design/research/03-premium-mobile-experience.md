# Research Report 03 — What Premium Mobile Should Mean for YouAreTheSongNow

**Builds on:** Report 02 established a repo-native, screenshot-reviewed design loop with mobile as the canonical product expression.

## Executive conclusion

Premium mobile quality is primarily **cognitive clarity + tactile confidence + visual restraint + platform familiarity + emotionally strong content presentation**. For YouAreTheSongNow, that means the user should feel like they are stepping into a creative experience, not operating an image-generation dashboard.

The app should minimize repeated decisions, keep one dominant task per focused creation step, make artwork and Song DNA emotionally legible, and use familiar mobile patterns for navigation, sheets, dialogs, search, selection, and back behavior.

## Core premium-mobile principles

### 1. One dominant task per focused screen

Apple's accessibility and interface guidance strongly favors reducing cognitive load and breaking multistep workflows into focused interactions. This aligns directly with the product rule that AI removes decisions by default.

For Create, avoid screens that simultaneously ask for song, portrait, style, aspect ratio, quality, instructions, and generation settings.

Prefer:

- choose song
- understand/select Song DNA
- generate OR explore
- optionally fine-tune
- experience generation
- reveal result

Each step should have one primary CTA and a small number of secondary actions.

### 2. Top-level navigation should disappear during immersive creation steps

Bottom navigation is useful for Create / Gallery / Account / future Discover at the product level. But during a focused creation sequence, persistent tabs compete with the task and increase accidental exits.

Recommendation:

- keep bottom nav on top-level destinations
- hide it during focused creation stages when appropriate
- provide a clear back/close affordance
- preserve draft state automatically

This creates a more native creative-tool feel.

### 3. Touch targets should feel generous, not merely compliant

Apple recommends default iOS controls around 44×44 pt and emphasizes spacing as well as control size. WCAG's stricter enhanced target guidance also uses 44×44 CSS px.

For this product, aim for:

- primary buttons: 48–56px visual/touch height
- icon controls: ≥44px interactive box
- selection cards: whole card tappable
- comfortable vertical spacing between adjacent actions

Do not rely on tiny text links for critical decisions.

### 4. Artwork should dominate chrome

The strongest premium cue is not decorative UI. It is allowing the user's generated imagery to command space.

Use:

- large edge-conscious artwork presentation
- restrained UI chrome
- controls that appear after or below the artwork hierarchy
- progressive reveal of metadata/actions
- dark surfaces that support rather than compete with imagery

The current product principle "artwork is the hero" should become a measurable layout rule.

### 5. Song DNA should be emotionally readable, not analytically dense

Do not expose the raw analysis schema. Translate it into small, meaningful creative concepts.

Good mobile interaction:

- AI highlights a recommended DNA starting point
- user can tap one meaningful card
- "Add another layer" reveals compatible dimensions
- selected DNA becomes a compact summary before generation

Bad mobile interaction:

- long technical analysis dump
- checkboxes for every schema field
- permanent list of camera/palette/lighting controls

### 6. Progressive disclosure should be a signature behavior

Apple's layout guidance explicitly recommends progressive disclosure where it helps users discover additional content without overwhelming the initial view.

The product hierarchy should be:

**default:** Generate

**secondary:** Explore Options

**tertiary:** Fine Tune

This pattern should repeat elsewhere: simple first, depth on demand.

### 7. Motion should reinforce causality and state

Selection, opening a sheet, moving into generation, and revealing an image can use motion to make the system feel tactile and coherent. But motion must be purposeful and reduced when users prefer reduced motion.

Do not animate every card continuously. Premium interfaces often feel better because they are calm.

### 8. The product should feel platform-aware without becoming an Apple clone

Use familiar interaction conventions:

- obvious back behavior
- bottom sheets for contextual controls
- native-feeling search field behavior
- scroll views that respect safe areas
- large readable controls
- direct-manipulation feedback
- reduced-motion support

Brand expression should come from typography, imagery, surfaces, rhythm, icon language, and signature transitions—not from inventing unfamiliar gestures.

## Recommended mobile creation shell

### Top-level

`Create | Gallery | Account | Discover?`

### Focused Create flow

`Choose Song → Song DNA → Generate/Explore → Generation → Reveal`

During focused flow:

- compact top bar/back control
- content-first body
- sticky or bottom-safe primary CTA when useful
- no persistent global navigation unless user needs to leave intentionally

## Mobile state requirements

Every canonical screen must define:

- initial
- loading
- content ready
- selected
- error
- retry
- disabled
- success transition
- offline/network interruption where meaningful
- reduced-motion behavior

Premium quality is often most visible when something is waiting, unavailable, or recoverable.

## Typography and density direction

The current serif/sans pairing can support a premium editorial/creative identity, but mobile typography must prioritize legibility over mood.

Recommended hierarchy:

- serif for emotionally important titles and selected creative concepts
- sans for controls, body text, status, metadata
- avoid overly thin serif weights at small sizes
- keep body text generous enough for dark-mode readability
- limit all-caps microcopy

## Independent reviewer pass

**Risk:** hiding navigation during Create could make users feel trapped.

**Countermeasure:** always provide an obvious back/close path and preserve draft progress. Focused does not mean modal prison.

**Risk:** "one task per screen" could create too many steps.

**Countermeasure:** screens should correspond to genuine decisions, not arbitrary wizard pages. Quick Generate should remain extremely short. Optional complexity should branch, not lengthen the default path.

**Risk:** a dark premium interface can become low-contrast and overly cinematic.

**Countermeasure:** accessibility contrast and readable text sizes are part of the visual north star, not post-processing.

## Decisions passed to Report 04

Report 04 should determine how this mobile-first interaction system expands to tablet and desktop without becoming a different product. The goal is adaptive composition, not duplicated UX.

## Sources

- Apple HIG Accessibility: https://developer.apple.com/design/human-interface-guidelines/accessibility
- Apple HIG Layout: https://developer.apple.com/design/human-interface-guidelines/layout
- Apple HIG Motion: https://developer.apple.com/design/human-interface-guidelines/motion
- WCAG 2.2 target-size guidance: https://www.w3.org/TR/WCAG22/
