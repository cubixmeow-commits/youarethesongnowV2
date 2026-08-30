---
type: prompt-reference
status: active
updated: 2026-08-30
area: prompt-lab
---

# V1 to V2 Creative Engine Comparison

Authoritative comparison for the portrait pipeline. V1 evidence is from the newest prompt-bearing worker unless noted. Do not copy V1 wholesale; preserve proven creative strengths and exclude unsafe or obsolete behavior.

Primary V1 sources:

- `arcana.queue.processor.cron.parallel.v3.dynamicstyle.php` (canonical)
- `arcana.queue.processor.php` (inline style catalog predecessor; gallery/thumbnail helper)
- `arcana.image.gallery.upscaler.php` (deferred upscale only)

## 1. Song DNA generation and normalization

| | |
|---|---|
| **V1** | ~L553–638 in `...v3.dynamicstyle.php`: Gemini text JSON with 12 fields (`summary`, `narrative`, `themes`, `symbols`, `mood`, `visual_metaphors`, `palette`, `lighting`, `camera`, `composition`, `influences`, `texture`). Lyrics may be sent transiently; output must not quote them. |
| **Why strong** | Structured cinematic blueprint before pixels. |
| **V2** | Preserved as derived Song DNA via Gemini Google Search (`GeminiLyricsResearchService`) with V1-compatible acceptance: request search; accept complete structured DNA; label `lyrics` / `song-context` / `v1-model-analysis`. Never persist raw lyrics, excerpts, search responses or prompts. Saved `derived_analysis_json` is locked into the generation snapshot; worker reuses it (`GeminiCreativeAdapter` `:selected-song-dna`) and does not repeat search. |
| **Missing** | Live fal identity proof still open. |
| **Neutral?** | Provider-neutral artifact; Gemini is the current analysis adapter. |
| **Action** | Keep reuse path; refine DNA fields only from benchmark evidence. |

## 2. One-person portrait instructions

| | |
|---|---|
| **V1** | ~L689–690: attached portrait is PRIMARY CHARACTER; exact face/bone/skin/hair/age; waist-up or full-body protagonist in scene, not studio portrait. |
| **Why strong** | Identity + scene integration, not passport crop. |
| **V2** | Canonical package + compact compiler (`compactPortraitEditPrompt`) require IMAGE 1 identity, foreground/near-middle-ground, visible lit face, reject silhouette/distant figures. |
| **Missing** | Provider proof that Flux Kontext Multi obeys this. |
| **Neutral?** | Creative intent provider-neutral; attachment method is adapter-specific. |
| **Action** | Benchmark before rewriting. |

## 3. Two-person portrait instructions

| | |
|---|---|
| **V1** | ~L676–688: two MAIN CHARACTERS; separate exact identity; co-protagonists; dynamic interaction; both clearly visible and lit. |
| **Why strong** | Equal prominence and interaction rules. |
| **V2** | IMAGE 1 / IMAGE 2 distinct references; equal protagonists; no merge/swap/omit. |
| **Missing** | Live two-person fal test. |
| **Neutral?** | Yes (intent). |
| **Action** | After one-person fal identity passes, run couple fixture. |

## 4. Portrait attachment to image requests

| | |
|---|---|
| **V1** | ~L416–428 load/compress; ~L1190–1197 Gemini `inlineData` mime + base64 after prompt text. |
| **Why strong** | Portraits travel with the same request as the cinematic prompt. |
| **V2** | Gemini image: each portrait is a separate `inlineData` part beside the identity-first prompt (`GeminiImageAdapter`), matching V1's multimodal pattern. fal/Replicate remain experimental after failing identity. |
| **Missing** | None structural; fal live identity pending. |
| **Neutral?** | Gemini-specific vs fal `image_urls` / Replicate inputs — adapter concern. |
| **Action** | Keep data-URI compression; do not log bytes. |

## 5. Scene composition and protagonist placement

| | |
|---|---|
| **V1** | ~L697–724 CHARACTER & SCENE + environment requirements; OUTPUT GOAL prioritizes character presence. |
| **Why strong** | People drive the story; environment supports. |
| **V2** | Compact prompt restages the saved visual moment around referenced people; environment must not overwhelm. |
| **Missing** | Proof after fal identity works. |
| **Neutral?** | Yes. |
| **Action** | Refine only after identity acceptance. |

## 6. Foreground / middle-ground / background

| | |
|---|---|
| **V1** | ~L718–724: lived-in 3D space; FG/MG/BG; weather; depth cues; motion. |
| **Why strong** | Prevents flat backdrops and passport framing. |
| **V2** | Present in canonical package and compact compiler. |
| **Missing** | None structural. |
| **Neutral?** | Yes. |
| **Action** | Preserve. |

## 7. Camera, lighting, palette, atmosphere, motion, texture

| | |
|---|---|
| **V1** | ~L726–734 from Song DNA arrays; technical filmic specs ~L739–745. |
| **Why strong** | Concrete craft language, not generic “cinematic.” |
| **V2** | DNA fields + StyleMap craft lines compile into compact visual direction. |
| **Missing** | Style dominance ranking after identity. |
| **Neutral?** | Yes. |
| **Action** | Tune from matrix, one variable at a time. |

## 8. StyleMap creation and dominance

| | |
|---|---|
| **V1** | ~L802–967: DB `arcana_styles.prompt_text` or `ANALYZE_BAND_STYLE` → Band Visual DNA → StyleMap headings; appended as STYLE DIRECTIVE governing aesthetic. UI rarely triggered dynamic path. |
| **Why strong** | Structured craft map dominates look. |
| **V2** | Fifteen launch StyleMaps in catalog; selected style compiles into package/prompt. No album-art copying for general songs. |
| **Missing** | Per-style fal refinement. |
| **Neutral?** | StyleMap artifact yes; V1 dynamic album analysis was Gemini-specific and must stay out of commercial general path. |
| **Action** | Keep static launch styles; defer dynamic band analysis. |

## 9. Custom instructions

| | |
|---|---|
| **V1** | ~L782–799 soft integration at end; cannot override DNA/safety/text. |
| **Why strong** | User steer without wrecking identity/safety. |
| **V2** | Soft special instructions with sanitizer; cannot override identity, style, orientation, originality, text, safety. |
| **Missing** | None structural. |
| **Neutral?** | Yes. |
| **Action** | Preserve. |

## 10. Aspect-ratio handling

| | |
|---|---|
| **V1** | ~L75–93 validate; prompt states target; Gemini `imageConfig.aspectRatio` ~L1210. |
| **Why strong** | Native provider ratio + prompt agreement. |
| **V2** | Orientation → `1:1` / `3:4` / `4:3`; sent as fal `aspect_ratio`; wrong output rejected (`fal_output_aspect_mismatch`) and credits released. |
| **Missing** | None structural. |
| **Neutral?** | Mapping provider-specific. |
| **Action** | Keep reject-and-release. |

## 11. Image retries and fallback prompts

| | |
|---|---|
| **V1** | ~L1032–1141 seven attempts; **attempt 4+ remove portraits**; later abstract/landscape. Corrupted abbreviated strings in newest worker. |
| **Why strong** | Persistence through safety blocks — but identity loss is unacceptable for V2. |
| **V2** | Bounded retry; portraits must stay; no silent portrait removal. Deterministic fallback only when explicitly allowed. |
| **Missing** | Full automatic quality/OCR retry loop after provider accepted. |
| **Neutral?** | Policy yes; mutations adapter-specific. |
| **Action** | Never copy portrait-removing retries. |

## 12. Output validation

| | |
|---|---|
| **V1** | Finish reason / IMAGE_SAFETY; decode inline image; limited structural checks. |
| **Why strong** | Basic failure detection. |
| **V2** | NSFW flags, URL host allowlist or data URI, JPEG normalize, dimension bounds, aspect match; failures release reserved credits. Identity acceptance still human/live. |
| **Missing** | Automated recognizable-identity scorer. |
| **Neutral?** | Checks adapter-specific. |
| **Action** | Add evaluation after first recognizable fal result. |

## 13. Upscaling and gallery (later only)

| | |
|---|---|
| **V1** | `arcana.image.gallery.upscaler.php` — Replicate `philz1337x/crystal-upscaler`; separate upscale credits; gallery action. Thumbnails in older processor ~L238–296. |
| **Why strong** | Working print-path proof. |
| **V2** | Deferred by owner decision after core web build. |
| **Missing** | Entire phase. |
| **Neutral?** | Adapterize later. |
| **Action** | Do not implement now. |

## Deliberately excluded from V2

- Raw lyric persistence
- Arcana / YouAreTheSongNow in-image branding
- Album-art or music-video copying
- Performer/celebrity likeness requirements
- Contradictory “no recognizable people” vs portrait identity
- Retries that strip portraits
- Hardwired obsolete Gemini image models
- Provider-specific wording inside the canonical provider-neutral package (compilers may specialize)
)
