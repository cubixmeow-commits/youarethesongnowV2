# Luminous Night Studio principles

**Status:** approved production baseline  
**Approved:** 2026-08-31  
**Applies to:** authenticated web product, compact through expanded layouts, and the later Flutter client

## Visual thesis

**A quiet midnight studio where Song DNA becomes luminous artwork: matte near-black and smoked graphite provide the room, platinum typography provides editorial structure, a single sapphire/cobalt light identifies action, and the artwork provides the emotion.**

The product must feel intimate, music-led, image-first, and precise. It must not feel like an AI dashboard, a futuristic console, or a generic neon generator.

## Content plan

Every product surface follows the same hierarchy:

1. **Orient:** identify the song, current creation stage, or collection.
2. **Act:** expose one dominant next action.
3. **Deepen:** reveal Song DNA, creative directions, metadata, or advanced controls only when useful.
4. **Resolve:** end in an artwork-first reveal or a clear recoverable state.

## Interaction thesis

1. **Track advance:** focused Create steps move with one consistent, interruptible transition and preserve draft state.
2. **Luminous selection:** selected Song DNA and creative directions use a tonal lift, platinum edge, cobalt marker, and explicit selected semantics. Glow is never the only state cue.
3. **Cover reveal:** generation resolves into the finished artwork before actions and metadata enter.

## Locked rules

1. Mobile is canonical. Tablet and desktop adapt the same state and information architecture.
2. AI removes decisions by default. Quick Generate is primary; Explore is secondary; Fine Tune is optional.
3. Song DNA is the creative control surface. Do not lead with generic style, model, camera, or prompt controls.
4. Artwork is the most colorful and visually dominant object on result, gallery, and creation-summary surfaces.
5. Use no more than two type families: Instrument Serif and DM Sans.
6. Use one customer-facing accent family: sapphire/cobalt. Semantic status colors are functional only.
7. Prefer cardless composition. Use a bounded surface only when the boundary is the interaction: selection, input, sheet, dialog, or grouped status.
8. Do not use em dashes in product copy.
9. Do not expose provider names, prompts, internal StyleMap names, raw lyrics, confidence/risk fields, or technical generation language.
10. Do not add motion that lacks a reduced-motion equivalent.

## Premium quality test

A screen is on-direction when all answers are yes:

- Is the next action obvious within two seconds?
- Does the artwork or emotionally important content outrank the chrome?
- Can at least one container, label, or choice be removed without losing meaning? If yes, remove it.
- Are selected, focus, disabled, loading, empty, error, and retry states intentional?
- Does compact width feel designed rather than compressed?
- Does expanded width improve comparison, context, or art scale without exposing extra complexity?
- Can the behavior be described without CSS or DOM terminology for Flutter?

## Reference

The canonical direction board is `assets/design/references/luminous-night-studio-style-board.png`. It is art-direction evidence, not a pixel specification. Exact component behavior and accessible token values in this design OS override any ambiguous detail in the board.
