# Research Report 06 — Premium Visual Art Direction Strategy

**Builds on:** Report 05 established semantic tokens and component contracts, but explicitly deferred final visual values until art direction is tested.

## Executive conclusion

The premium identity should be selected by **comparative application to real product screens**, not by choosing a moodboard. The current platinum/blue/black + serif/sans system is a credible foundation, but the final direction should be tested against the same reference screens under 2–3 distinct interpretations.

Premium here should mean editorial confidence, cinematic restraint, exquisite typography/spacing, strong image framing, and quiet but unmistakable product identity.

## What to avoid

- generic AI gradient/glow aesthetics
- dashboards full of bordered cards
- excessive glassmorphism
- neon-on-black cyberpunk defaults
- every control using the brand accent
- decorative animation competing with user artwork
- thin low-contrast typography
- huge hero copy where a direct action is needed

## Candidate direction A — Cinematic Editorial

**Character:** dark gallery/editorial publication meets premium creative tool.

- deep neutral canvas
- platinum hairlines
- serif used sparingly for emotional/product titles
- precise sans UI
- blue accent almost exclusively for interaction/selection
- generous negative space
- artwork framed like photography/album art rather than inside heavy cards
- minimal elevation

**Strength:** likely best bridge between current brand and a high-end native creative app.

## Candidate direction B — Luminous Night Studio

**Character:** black studio environment with subtle light behavior and more atmospheric surfaces.

- near-black canvas with gentle tonal transitions
- selective sapphire/cobalt luminous edges for active states
- subtle material depth
- larger image-led regions
- slightly more immersive motion

**Risk:** easiest direction to overdo into generic AI glow aesthetics. Must remain restrained.

## Candidate direction C — Modern Album Object

**Character:** stronger graphic/editorial system inspired by physical music objects—album jackets, liner notes, sequencing, typographic rhythm—without copying any specific brand.

- bolder typography hierarchy
- structured grids
- occasional large serif display moments
- tactile borders/material cues
- image tiles feel like collectible objects
- Song DNA could be presented like interpreted liner-note fragments

**Strength:** potentially most distinctive and music-specific.

**Risk:** could become overly stylized and less platform-native if every screen is treated like a poster.

## Comparison protocol

Apply all three to the exact same states:

1. Create Home
2. Song DNA selector
3. Explore Options with one recommended/selected card
4. Generation state
5. Reveal/result
6. Gallery with portrait shelf

Evaluate each on:

- 320–375px mobile usability
- current iPhone-class mobile
- 1280–1440 desktop
- typography clarity
- image dominance
- distinctiveness
- accessibility
- ability to express loading/error/disabled states
- Flutter feasibility
- ability to remain attractive after novelty wears off

## Typography strategy

Current Instrument Serif + DM Sans may remain viable.

Recommended roles:

- display/emotional title: serif
- section/title: serif or strong sans depending hierarchy
- controls/navigation: sans
- body/explanation: sans
- metadata/status: sans

The premium feeling should come more from scale, line length, spacing, weight, and contrast than from adding more typefaces.

## Color strategy

Keep brand accent scarce.

A useful hierarchy:

- canvas: nearly black
- primary elevated surface: quiet graphite
- interactive surface: slightly differentiated neutral
- selected state: tonal shift + subtle blue edge/fill
- platinum: borders/text highlights, not large fills
- sapphire/cobalt: action, selection, focus, progress only
- artwork supplies most chromatic richness

This follows a critical principle: **the generated art should be the most colorful thing on most screens.**

## Surface strategy

Avoid wrapping every semantic section in a card. Use cards when selection/grouping requires a boundary. Otherwise rely on spacing, alignment, typography, and hairlines.

The Explore first build currently has a large enclosing panel plus three internal cards; later visual review should test whether the outer panel is unnecessary.

## Icon strategy

Use a small coherent icon set with consistent stroke/weight/optical sizing. Avoid mixing decorative logos, CSS masks, emoji-like icons, and unrelated icon families.

Brand mark remains distinct from utility icons.

## Image treatment

- honor original aspect ratios until a specific crop is intentional
- use predictable corner/radius philosophy
- avoid heavy shadows on every tile
- selected/generated art can bleed closer to edges in immersive states
- gallery thumbnails should prioritize visual scanning over metadata
- metadata can appear after/under selection rather than permanently overlaying images

## Independent reviewer pass

**Risk:** evaluating static screens may underweight motion and real interaction.

**Countermeasure:** shortlist using static reference states, then prototype only the top 1–2 directions through Explore → Generation → Reveal before final lock.

**Risk:** preserving current brand too strongly may prevent a better identity.

**Countermeasure:** candidate C deliberately tests a more music-specific visual language. The comparison should be evidence-driven.

**Risk:** "premium" may drift toward luxury/editorial and lose warmth/playfulness.

**Countermeasure:** user artwork and Song DNA copy should provide emotion. The system should feel sophisticated but not intimidating.

## Decisions passed to Report 07

Report 07 should define the interaction and motion system that supports the chosen art direction, especially Explore loading, Song DNA selection, generation, and the signature reveal.

## Sources

- Apple HIG visual hierarchy/layout: https://developer.apple.com/design/human-interface-guidelines/layout
- Apple HIG principles: https://developer.apple.com/design/human-interface-guidelines
- Existing repo brand/token evidence under `docs/design/foundations/`, `design/DESIGN_SYSTEM.md`, and `development-vault/05 Product Design/`.
