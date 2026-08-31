---
type: decision-inbox
status: active
updated: 2026-08-27
area: decisions
---

# Decision Inbox

Use this note for important questions that are not settled yet. When a choice becomes accepted and materially affects the product or architecture, create an ADR.

## Creative engine

- What should “Dynamic Band Lore” mean in V2: visual identity, true lore, or retire the term?
- Should dynamic artist visual identity run by default?
- Should artist visual identity use model knowledge, user references, licensed metadata/artwork, or a hybrid?
- ~~Should users be able to choose literal vs metaphorical interpretation?~~ → Partially superseded by DNA-first Create (ADR-20260831); exact literal/metaphorical control still open inside Fine Tune / DNA emphasis.
- ~~Should users see or edit Song DNA / Visual Narrative Plan before generation?~~ → Users **select** meaningful Song DNA dimensions (not free-edit DNA / not lyric selection). Remaining opens listed in `docs/design/screens/create-flow.md` §12.
- Should there be a real story/lore mode later, or remain image-first?
- Where do portraits sit in the DNA-first Create diagram?
- Variation vs Reimagine vs regenerate — product definitions?
- Discover destination definition before chrome change?

## Portraits

- Never drop portraits, ask before dropping, or allow a clearly labeled degraded result?
- Keep 1–2 portraits or eventually support role-based ensembles?
- Portrait step order relative to Song DNA selection (before DNA, after DNA, or parallel)?

## Lyrics

- Process lyrics transiently and persist only derived artifacts?
- User-supplied lyrics, licensed source, or hybrid?
- What retention/deletion policy should apply?

## Output and branding

- Add branding post-process, through a dedicated text layer, or generatively?
- Should clean and branded exports both exist?

## Commercial/product

- Credits, subscription, or hybrid?
- Which V1 plan concepts are worth retaining?
