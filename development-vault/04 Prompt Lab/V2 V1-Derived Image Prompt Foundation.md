---
type: prompt-implementation-note
status: implemented-private-development
updated: 2026-08-29
area: prompt-lab
---

# V2 V1-Derived Image Prompt Foundation

## Decision

V2 now begins image generation from the strongest recoverable V1 creative behavior. The implementation preserves V1's visual ambition while removing provider-specific contradictions and unsafe fallbacks.

## V1 evidence reviewed

The canonical evidence is `arcana.queue.processor.cron.parallel.v3.dynamicstyle.php`, supported by the earlier inline style catalog in `arcana.queue.processor.php`.

V1's strongest image behavior came from:

1. a single clear narrative beat;
2. explicit one- or two-person identity roles;
3. foreground, middle ground and background staging;
4. atmosphere, motion and environmental storytelling;
5. concrete palette, lighting, camera, composition, surface and texture direction;
6. selected-style dominance through a structured StyleMap;
7. soft user instructions;
8. poster-scale output priorities.

## Implemented V2 foundation

The provider-neutral compiler now assembles:

1. mission and output goal;
2. approved Song DNA and emotional arc;
3. one original narrative moment;
4. one- or two-person portrait integration;
5. dimensional environment and motion;
6. cinematography and materials;
7. the selected launch StyleMap;
8. orientation and quality-tier direction;
9. subordinate user direction;
10. text, originality, likeness and content rules;
11. ranked output priorities.

All fifteen active launch styles now have concrete V1-derived craft direction across `STYLE`, `MEDIUM`, `COLOR`, `LIGHTING`, `SURFACE`, `MOTION`, `COMPOSITION`, `MOOD`, `ATMOSPHERE`, `DETAIL`, `INFLUENCE`, `TYPOGRAPHY`, `SPECIAL` and `AVOID`.

## V1 behavior deliberately removed

- generated Arcana or website branding inside the artwork;
- the contradiction between preserving uploaded identities and forbidding all recognizable people;
- general-song imitation of official album art;
- song title and band name in the final image prompt;
- retries that silently remove required portraits;
- copied logos, promotional layouts, music-video imagery or merchandise;
- one universal photoreal requirement that weakens painterly and collage styles.

## Current provider adaptation

The Replicate P-Image-Edit adapter now treats uploaded images as identity references only, explicitly replaces their original crop, pose, background, lighting and clothing, and forbids returning the source photos as visible collage or inset elements.

## Next refinement

Run the controlled song/portrait/style matrix and compare results against selected V1 anchor images. Refine the canonical prompt and individual StyleMaps from observed identity fidelity, narrative clarity, style strength, composition, anatomy and provider behavior.
