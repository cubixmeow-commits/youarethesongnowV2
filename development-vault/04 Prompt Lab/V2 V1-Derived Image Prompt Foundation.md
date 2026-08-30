---
type: prompt-implementation-note
status: implemented-private-development
updated: 2026-08-30
area: prompt-lab
---

# V2 V1-Derived Image Prompt Foundation

## Decision

V2 image generation follows V1's proven multimodal pattern: cinematic identity-first text instructions plus attached portrait inline images in one provider request. The selected production path is native Gemini `gemini-3.1-flash-image`.

## V1 evidence

Canonical worker `arcana.queue.processor.cron.parallel.v3.dynamicstyle.php` attached portraits as Gemini `inlineData` after the compiled prompt and required exact facial identity with waist-up/full-body protagonists. That is the foundation ported into `GeminiImageAdapter`.

## Current path

1. Song DNA from selection (no re-search in the image adapter).
2. V1-style identity-first image prompt + canonical creative package.
3. Each selected portrait as its own `inlineData` part.
4. Curated StyleMap, aspect ratio (`1:1` / `3:4` / `4:3`), special instructions, no-text.
5. `responseModalities: ["IMAGE"]`, `imageSize: 1K` for development.
6. Decode inline output → validate → normalize JPEG → private storage.

## Failed experiments (not deleted)

| Provider | Result |
|---|---|
| Replicate P-Image-Edit | Strong scenes; uploaded people omitted or minimized |
| fal Kontext Multi | Strong scenes; people omitted entirely |

Neither meets the defining requirement that uploaded people are recognizable central subjects. Both remain optional experimental providers for later benchmarks.

## Observed two-person staging bias

Multiple live two-person Gemini outputs repeated a left/middle-ground + larger right-foreground arrangement. Repeated waist-up, close/three-quarter, FG/MG, interaction, and “visual center” wording in both the Gemini adapter and the appended compiled package over-weighted that safe default.

**Correction:** consolidate identity into one section; keep every person recognizable and present; clarify “central” as narrative/emotional; let Song DNA moment, roles, relationships, camera, composition, motion, environment, and style choose staging; grant explicit placement freedom; do not append a second near-identical portrait-placement block via `compiledPromptSafe`.

## Boundary

Lyrics may be searched/analyzed transiently during private Song DNA development. Raw lyrics are never saved or committed. Print/poster/T-shirt/upscaling remain post-Build-1.
