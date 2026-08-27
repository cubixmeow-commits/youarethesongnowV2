# 09 — V1 Creative Engine Forensic Audit

**Purpose:** reconstruct how AISaga Arcana actually produced cinematic images from song context, with emphasis on prompt behavior rather than file inventory.

**Legacy source:** `cubixmeow-commits/youarethesongnow`  
**Best-evidence worker:** `arcana.queue.processor.cron.parallel.v3.dynamicstyle.php`  
**Assessment date:** 2026-08-27  
**Status:** planning/evidence only — no V2 implementation

**Evidence labels:** Observed · Inferred · Proposed · Open Question

---

## Executive verdict

Arcana’s creative engine is a **staged interpretation pipeline**, not a single magical prompt.

**Observed pipeline (best-evidence worker):**

```
User form submission
  → arcana_queue row (pending)
  → Worker claim
  → Song DNA (Gemini text JSON)
  → Cinematic prompt assembly
  → Optional StyleMap OR static style append
  → Gemini image generation (+ portraits as inline data)
  → 7-step safety/retry chain
  → WebP + thumbnail + B2/local storage
  → arcana_renders insert + credit debit + notify
```

The marketed phrase **“Dynamic Band Lore Engine™”** does **not** correspond to a separate lore/story subsystem in the production song path.

**Observed instead:**

1. **Song DNA** — always-on song interpretation into structured visual/narrative fields.
2. **Optional Dynamic Band Style (`ANALYZE_BAND_STYLE`)** — album-cover-derived **visual StyleMap**, not narrative lore.
3. **Static styles** — DB-stored style directives from `arcana_styles`.
4. **Cinematic prompt compiler** — PHP string assembly that fuses DNA, portraits, aspect, branding, safety, and style.

**Critical product/implementation gap — Observed:** the generator UI labels the default option “Arcana Dynamic Band Lore Engine (Default)” but submits `image_style=""`. The worker only runs StyleMap analysis when `image_style === "ANALYZE_BAND_STYLE"`. Empty style skips the entire style block. Default users therefore get Song DNA + base cinematic prompt **without** Dynamic Band Style analysis.

That distinction should drive V2 design: lore/mythology vs visual identity are different product features, and V1 only partially implemented the latter.

---

## PART 1 — Locate the real creative engine

### 1.1 Submission entrypoint — Observed

Primary user path: `arcana.image.generator.php`

- Validates auth, plan, credits, portraits, aspect ratio, style key, lyrics, custom instructions.
- Does **not** call Gemini synchronously.
- Inserts into `arcana_queue` with `status = pending`.
- Returns `queue_id`; browser polls `check_queue`.

Stored queue fields include: `user_id`, `band`, `song`, `lyrics`, `custom_instructions`, `aspect_ratio`, `image_style`, `portrait1_url`, `portrait2_url`, `tshirt_mode`, `watermark`, `status`.

### 1.2 Worker family — Observed

| File | Role |
|---|---|
| `arcana.queue.processor.cron.parallel.v3.dynamicstyle.php` | Best-evidence current: parallel batch, Song DNA, dynamic+static styles, watermark flag, batched 7-attempt retries |
| `arcana.queue.processor.cron.gpt.php` | Near-duplicate of v3; filename misleading (still Gemini) |
| `arcana.queue.processor.cron.parallel.php` | Older parallel; Song DNA + image; **no** styles; **no** retry chain |
| `arcana.queue.processor.cron.php` | Sequential; styles + **intact** 7-step retry; JSON repair + optional search |
| `arcana.queue.processor.cron.backup.php` | Byte-identical backup of sequential cron |
| `arcana.queue.processor.cron.nowatermark.php` | Sequential; filename misleading — still applies Imagick watermark post-render |
| `arcana.queue.processor.php` | Oldest web-triggered worker; inline hardcoded styles; no `ANALYZE_BAND_STYLE` |

**Open Question:** which exact script production cron invoked. Repository evidence favors v3.dynamicstyle as intended current path, but deploy/crontab is not in source.

### 1.3 Unrelated / experimental paths — Observed

| Path | Relation to Arcana song engine |
|---|---|
| `animal.lore.engine.php` | Separate “Dynamic Animal Lore Engine” demo; animal names → lore JSON → image. Same Gemini pattern, not wired to `arcana_queue` |
| `widget/widget-frame.php` | Stub; placeholder poster prompt; not real Song DNA pipeline |
| `youtube/index*.php` | “Story Forge” / Sora script tools; not Arcana song posters |
| `prompts/` | Contains only `INTEGRATE_VIBEKB.md`; not production Arcana prompt templates |

### 1.4 Evolution summary — Inferred from source diffs

1. Early synchronous/web worker with hardcoded style map.
2. Sequential cron with Song DNA, DB styles, full retry text.
3. Parallel analysis/image for throughput.
4. v3 adds Dynamic Band Style + watermark flag + batched retries.
5. Retry prompt text in v3/gpt appears **truncated/corrupted** relative to sequential workers (see Part 7).

---

## PART 2 — End-to-end prompt pipeline (summary)

Detailed stage-by-stage schemas live in `10-v1-prompt-pipeline.md`. High-level:

| Stage | Model | Input | Output | Downstream consumer |
|---|---|---|---|---|
| Song DNA | `gemini-2.0-flash-exp` (text, JSON mime) | band, song, lyrics≤16k | 12-field JSON | Prompt assembler |
| Band Visual DNA (dynamic only) | same text model | band + song title | prose paragraph about album-art artist/style | StyleMap synthesis |
| StyleMap (dynamic only) | same text model | Band Visual DNA | headed StyleMap text | Appended as STYLE DIRECTIVE |
| Static style (optional) | none (DB) | `style_key` | `prompt_text` | Appended as STYLE DIRECTIVE |
| Cinematic prompt | PHP assembly | DNA + portraits + aspect + custom + style + branding/safety | long text prompt | Image model |
| Image render | `gemini-2.5-flash-image` | prompt + optional portrait inlineData + aspectRatio | image bytes | Storage / gallery |
| Retry mutations | same image model | mutated prompt ± portraits | image or fail | Same |

---

## PART 3 — Song DNA deep audit

### 3.1 What Song DNA is — Observed

Song DNA is a **structured song-to-visual interpretation artifact** produced by a Gemini text call before image generation. It is not a musicological database record and not a full story document. It is a blueprint for one cinematic image.

### 3.2 Exact schema — Observed

```json
{
  "summary": "string",
  "narrative": "string",
  "themes": ["string"],
  "symbols": ["string"],
  "mood": ["string"],
  "visual_metaphors": ["string"],
  "palette": ["string"],
  "lighting": ["string"],
  "camera": ["string"],
  "composition": ["string"],
  "influences": ["string"],
  "texture": ["string"]
}
```

Same 12 keys appear across all workers that implement analysis. No worker adds separate lore fields.

### 3.3 Field meanings and image influence — Observed / Inferred

| Field | Meaning (from prompt wording) | Used in cinematic prompt? | Influence strength |
|---|---|---|---|
| `summary` | Essence / song soul | Yes — “Essence:” | High |
| `narrative` | One readable narrative beat | Yes — “Narrative Moment” | High |
| `themes` | Thematic core | Yes — joined, max 10 | High |
| `symbols` | Symbolic elements | Yes | Medium–High |
| `visual_metaphors` | Metaphorical imagery | Yes | Medium–High |
| `mood` | Emotional atmosphere | Yes | High |
| `palette` | Color guidance | Yes — Cinematography | High |
| `lighting` | Lighting design | Yes | High |
| `camera` | Camera language | Yes | Medium |
| `composition` | Composition rules | Yes | Medium |
| `texture` | Surface & texture | Yes | Medium |
| `influences` | Visual influences | Yes — “Visual Influences” | Medium / risk of imitation |

**Observed:** array fields are joined with commas and truncated to 10 items each. Empty arrays become empty strings.

**Inferred decorative risk:** if the model fills `influences` with named artists/directors/album aesthetics, those can compete with originality/safety instructions later. There is no validation that strips named IP from DNA fields.

### 3.4 What Song DNA does *not* contain — Observed

Absent from schema:

- era / decade;
- genre / subgenre as first-class fields;
- character roster / casting;
- explicit setting/location object;
- emotional trajectory over time (only a single narrative beat);
- lyric-line references (explicitly forbidden);
- artist visual identity / costume language;
- user portrait plan;
- text/title treatment plan;
- safety risk flags;
- confidence / uncertainty;
- scene alternatives.

### 3.5 Literal vs metaphorical — Observed

Analysis instructions:

- use lyrics to inform analysis;
- never quote or paraphrase lyrics in output;
- be specific/vivid;
- tailor to this song and band.

Cinematic prompt then asks for one clear readable narrative beat plus visual metaphors/symbols.

**Inferred:** the system intends **metaphorical cinematic interpretation**, not lyric illustration panels. However, nothing prevents DNA from being highly literal if the model chooses concrete objects from lyrics.

### 3.6 Persistence — Observed

`analysis_json` is stored on `arcana_renders` (and schema helpers also prepare analysis columns on queue-related tables). Full lyrics are also stored on queue and render rows.

### 3.7 Mandatory fields / validation — Observed

v3:

- requires JSON decode to array;
- does **not** coerce missing keys;
- does **not** schema-validate types;
- on failure, marks item error and skips prompt/image.

Older sequential workers:

- JSON fence strip;
- optional `googleSearchRetrieval` then retry without;
- repair-pass for malformed JSON;
- explicit normalization into typed arrays/strings.

**Inferred:** sequential workers are more resilient on DNA parse; v3 prefers throughput and fails faster.

---

## PART 4 — Dynamic Band Style / Lore deep audit

### 4.1 The central distinction

| Concept | Marketing claim | Actual implementation |
|---|---|---|
| Dynamic Band Lore Engine™ | Analyzes tone, emotion, symbolism; builds Song DNA; orchestrates visual saga | Umbrella brand for the whole creative pipeline |
| Song DNA | (folded into lore marketing) | Real always-on structured analysis |
| Dynamic Band Style | Often conflated with lore | Real **optional** StyleMap path keyed by `ANALYZE_BAND_STYLE` |
| Separate lore/story engine | Implied by “lore” | **Not implemented** for songs in queue workers |

**Observed conclusion:** V1’s “Dynamic Band Lore Engine” is primarily a **marketing name** for Song DNA + cinematic image generation (+ optional visual StyleMap). It is **not** a true lore system that produces durable band mythology, character bibles, or multi-scene narrative arcs.

### 4.2 What `ANALYZE_BAND_STYLE` does — Observed

When `image_style` uppercases to `ANALYZE_BAND_STYLE`:

**Phase A — Band Visual DNA (text call)**

- Asks Gemini to identify the album-cover artist/designer for `{song}` by `{band}`.
- If unknown, infer artistic school/influence from **official album cover artwork**.
- Explicitly: base on album cover, **not** videos/stage/general aesthetic.
- Output: one prose paragraph titled conceptually as album-art artist & style analysis.
- Fallback if empty: generic mythic-stage paragraph.

**Phase B — StyleMap synthesis (text call)**

Exact headings requested:

```
STYLE:
MEDIUM:
COLOR:
LIGHTING:
SURFACE:
MOTION:
COMPOSITION:
MOOD:
ATMOSPHERE:
DETAIL:
INFLUENCE:
TYPOGRAPHY:
SPECIAL:
AVOID:
```

Rules emphasize craft terms, no logos/text/album replicas, cinematic coherence.

Fallback if missing/`STYLE:` absent: canned “Mythic Cinematic Realism” StyleMap.

**Phase C — Append**

StyleMap (+ application directive) appended to base cinematic prompt as `STYLE DIRECTIVE:`, then a duplicated copyright/safety block.

### 4.3 Inputs to style analysis — Observed

Receives:

- band name;
- song title.

Does **not** receive:

- lyrics;
- Song DNA JSON;
- user portraits;
- custom instructions.

**Inferred:** StyleMap is artist/album-cover visual identity, not lyric-driven. It can therefore diverge from Song DNA if album aesthetics and lyric meaning conflict.

### 4.4 Does it rely on model knowledge of the artist? — Observed / Inferred

Yes. Phase A asks the model to name cover artists and describe album artwork from knowledge. There is no image download of the actual cover in this path.

**Open Question:** accuracy for obscure artists; hallucination risk for cover-artist attribution.

### 4.5 UI exposure — Observed

- Default style option label: “Arcana Dynamic Band Lore Engine (Default)” with `data-style-key=""`.
- Hidden input default `image_style=""`.
- Worker comment: “Trigger key your UI should send for the dynamic mode”.
- No generator UI option found that sets `ANALYZE_BAND_STYLE`.
- Style picker gated to plans with `has_styles` (CREATOR in plan tiers); lower tiers see locked UI.
- Admin Style Editor manages **static** DB styles, not dynamic analysis.

**Inferred:** Dynamic Band Style was built in workers ahead of (or instead of) completing UI wiring. Marketing overstates what the default path does.

### 4.6 Dynamic Band Lore vs Dynamic Visual Style

| | Dynamic Band Lore (marketing) | Dynamic Visual Style (code) | Song DNA (code) |
|---|---|---|---|
| Narrative mythology | Claimed | No | Partial via `narrative`/`themes`/`symbols` |
| Visual aesthetic identity | Claimed | Yes (StyleMap) | Partial via palette/camera/etc. |
| Separate durable artifact | Claimed | Ephemeral text appended to prompt | Stored as `analysis_json` |
| User-visible | Branding copy | Mostly not | Not shown to user in traced UI |

### 4.7 Unfinished appearance — Observed / Inferred

- UI key mismatch.
- Tooltip says default auto-selects perfect style; code skips style when empty.
- StyleMap not persisted as its own column (only embedded inside final prompt / indirectly via `image_style` key).
- No user preview of StyleMap before render.
- No reconciliation step between Song DNA and StyleMap conflicts.
- animal.lore.engine.php shows a richer multi-step lore pattern that was never adapted into the song product.

---

## PART 5 — Cinematic prompt anatomy

### 5.1 Actual section order — Observed (v3 base prompt)

1. **MISSION** — role framing: cinematic photoreal illustration of song essence.
2. **SONG DNA** — summary, narrative moment, themes, symbols, metaphors, mood.
3. **CHARACTER & SCENE** — portrait directive + environment requirements.
4. **CINEMATOGRAPHY** — palette, lighting, camera, composition, texture, influences.
5. **TECHNICAL SPECIFICATIONS** — aspect ratio, render quality, constraints, branding.
6. **COPYRIGHT & CONTENT SAFETY DIRECTIVE** — originality / no text / no real people / no album replicas.
7. **OUTPUT GOAL** — cohesive readable image; band + song names as footer context.
8. **USER CUSTOMIZATION** (optional, soft guidance at end).
9. **STYLE DIRECTIVE** (optional static or dynamic; appended after base).
10. **Second COPYRIGHT & SAFETY block** (when style present).
11. **WATERMARK/BRANDING OVERRIDE** (when watermark disabled).

### 5.2 What “cinematic” means in the actual prompt — Observed

Not merely the word “cinematic.” Concrete instructions include:

- lived-in 3D space with FG/MG/BG;
- weather/atmosphere/environmental storytelling;
- motivated lighting (key/fill/rim);
- depth cues (atmospheric perspective, parallax, scale);
- implied motion (pose, fabric, particles, weather);
- filmic grading, materials, bokeh, chromatic aberration, vignette, fresnel;
- avoid flat backdrops, passport poses, empty voids.

Vague/noise adjectives also appear: “cinematic,” “photoreal,” “soul of this song,” “instantly readable.” These are less actionable than the concrete cinematography checklist.

### 5.3 Instruction precedence conflicts — Observed

| Conflict | Clauses |
|---|---|
| Branding vs no-text | Constraints: “NO visible text…” **and** Branding: render “AI Saga Arcana” / “YouAreTheSongNow.com” |
| Real people vs portraits | Safety: avoid recognizable portraits of real people **and** portrait directive: match exact facial features from attached photos |
| Photoreal vs illustrative fallback | Mission says photoreal; retries push matte-painting / illustrative |
| Album-artist StyleMap vs no album replicas | Dynamic path names cover artists/influences while safety forbids album-layout replication |
| Soft user customization vs CRITICAL portrait | Custom is soft; portraits are CRITICAL — good hierarchy, but custom can still inject conflicting scene demands |
| Band name in prompt footer vs “no band names” in image | Band/song provided as context while image must not show band names |

### 5.4 Overly prescriptive / repetitive risks — Inferred

- Long technical material/fresnel laundry list may homogenize outputs.
- Duplicate safety blocks waste tokens and amplify “no faces/no text” pressure against portrait identity.
- Style appended after user customization means style can overshadow user soft guidance depending on model attention.
- `influences` + StyleMap INFLUENCE + album-artist naming stack imitation risk.

---

## PART 6 — Portrait integration

### 6.1 How portraits enter the request — Observed

1. Paid users upload 1–2 portraits on generator form.
2. Images resized/compressed; stored under refs path; URLs put on queue row.
3. Worker loads bytes + mime via `load_portrait_from_path`.
4. Image API request parts: `[text prompt, inlineData portrait1?, inlineData portrait2?]`.
5. Aspect ratio set via `imageConfig.aspectRatio`.

### 6.2 Prompt identity instructions — Observed

**One portrait:**

- CRITICAL: use attached portrait as PRIMARY CHARACTER.
- Match exact facial features, bone structure, skin tone, hair, age.
- Protagonist in scene, not studio portrait; waist-up or full-body; engaged with environment.

**Two portraits:**

- Both are MAIN CHARACTERS.
- Character 1 / Character 2 ordered by attachment order.
- Match exact features for each.
- Dynamic interaction; avoid passport poses.

**Zero portraits:**

- “Depict a central protagonist inspired by the song's narrator…”

**Observed:** prompt does **not** name the user (“this is Alice”). Identity is image-reference + facial matching instructions only.

### 6.3 Competition with song imagery — Inferred

Portrait block is CRITICAL and early (CHARACTER & SCENE). Song DNA narrative may invent a different protagonist archetype. Safety later says avoid recognizable real people. Retries may drop portraits entirely.

So identity fidelity competes with:

- song narrator depiction;
- safety policy;
- StyleMap/costume aesthetics;
- abstract fallbacks.

### 6.4 When portraits are removed — Observed (intended chain)

| Attempt | Portraits |
|---|---|
| 1–3 | Kept |
| 4–7 | Removed |

**Observed:** users are **not** informed in the traced success path that a completed image may have been generated without their portrait after fallback. Gallery stores portrait metadata from original refs, which can imply portraits were used even if later attempts dropped them.

**Open Question:** whether any UI/log surfaces attempt number or portrait-dropped status to the user. Worker logs exist for CLI; user-facing generator polling shows completed/failed primarily.

### 6.5 Gender/appearance assumptions — Observed / Inferred

No explicit gender injection in portrait directives. Without portraits, “song's narrator” may cause the text model/image model to invent gendered appearance from lyrics/artist stereotypes.

---

## PART 7 — Retry and safety prompt mutation

### 7.1 Intended 7-attempt chain — Observed from sequential `cron.php` (intact text)

```
Attempt 1: full prompt + portraits, temp 0.8
    ↓ fail
Attempt 2: same prompt + portraits, temp 0.75
    ↓ fail
Attempt 3: strip branding / soften text rule + portraits, temp 0.65
    ↓ fail
Attempt 4: DROP portraits + safety guarantees + slightly illustrative, temp 0.55
    ↓ fail
Attempt 5: wider framing + matte-painting emphasis, temp 0.45
    ↓ fail AND lastFinishReason == IMAGE_SAFETY
Attempt 6: abstract / no people; keep selected DNA fields, temp 0.5
    ↓ fail AND IMAGE_SAFETY
Attempt 7: landscape-only environment tableau, temp 0.45
    ↓ fail
Job failed
```

### 7.2 Meaning survival across fallbacks — Inferred

| Attempt | Identity | Song meaning | Composition | Style |
|---|---|---|---|---|
| 1–2 | Strong | Strong | Strong | Strong |
| 3 | Strong | Strong | Strong | Strong (branding noise reduced) |
| 4 | **Destroyed** | Mostly kept | Softened | Partially altered |
| 5 | Destroyed | Mood/environment prioritized | Wide/env | Illustrative shift |
| 6 | Destroyed | Themes→abstract metaphors | Abstract | DNA palette/light only |
| 7 | Destroyed | Mood via landscape | Landscape | Painterly env |

**Observed product issue:** creative meaning can survive longer than user identity. The product promise “starring you” is abandoned at attempt 4 without explicit user consent/notification.

### 7.3 v3/gpt regression — Observed

In `parallel.v3.dynamicstyle.php` and `cron.gpt.php`, attempt 3–7 strings contain literal `...` truncations inside regexes and prompt bodies (comments claim parity with v2). Sequential workers retain full text.

**Inferred:** if production runs v3, fallbacks 3–7 may not strip branding as intended and may send nonsense/truncated safety prompts. Attempts 1–2 remain intact.

### 7.4 Termination — Observed

Success when image bytes extracted. Failure after max attempts with finishReason noted; optional safety category logging. Credit not consumed on failure (success path deducts after render insert).

---

## PART 8 — Prompt-quality critique

### Clever / preserve — Observed / Proposed

1. **Staged Song DNA before image** — separates interpretation from rendering.
2. **Structured visual fields** (palette/lighting/camera/composition/texture).
3. **Portrait as scene protagonist**, not sticker face-swap language alone.
4. **Soft custom instructions** with explicit non-override rules.
5. **Aspect ratio as first-class model config**, not only prompt prose.
6. **Progressive safety policy** rather than immediate hard fail.
7. **StyleMap headings** that look like a production design brief.
8. **Async queue** so creative work is durable.

### Brittle / weak — Observed / Inferred

1. Marketing “lore” ≠ implemented lore.
2. Dynamic style UI unwired.
3. Branding vs no-text contradiction.
4. Portrait vs “no real people” contradiction.
5. Duplicate safety blocks.
6. Influences/album-artist naming invite imitation.
7. No schema validation on DNA.
8. No intermediate Visual Narrative Plan — one giant prompt does too many jobs.
9. Lyrics persisted in full despite “don’t quote” analysis rules.
10. Retry chain destroys identity early.
11. v3 retry text corruption.
12. User not told when portrait dropped.
13. Vague filler adjectives mixed with precise craft language.
14. StyleMap ignores lyrics/Song DNA → possible aesthetic/meaning split.
15. Prompt injection surface: lyrics + custom instructions enter model context (mitigated partly by soft-custom rules, not by hard sandboxing).

### Older-model hacks that may be unnecessary now — Inferred

- Extreme “NO TEXT” repetition and Gemini-logo bans.
- Multiple temperature nudges before changing concept.
- Illustrative downgrade as safety workaround.
- googleSearchRetrieval on older analysis path.
- Very long fresnel/micro-detail laundry lists.

Modern multimodal models may respond better to shorter hierarchical contracts with explicit priority ranks and separate planner artifacts.

---

## PART 9 — Answers to the mandatory audit questions

1. **What exactly is Song DNA?**  
   **Observed:** a Gemini-produced JSON blueprint (12 fields) translating band/song/lyrics into visual-narrative guidance for one image.

2. **What exact information goes into it?**  
   **Observed:** band/artist name, song title, optional lyrics truncated to 16,000 chars, plus system analysis instructions.

3. **What does its JSON output look like?**  
   **Observed:** see schema in §3.2.

4. **Which Song DNA fields actually affect the image?**  
   **Observed:** all 12 are interpolated into the cinematic prompt; arrays joined/truncated to 10. No field is computed then discarded in v3.

5. **What is Dynamic Band Style?**  
   **Observed:** optional path that derives Band Visual DNA from album-cover knowledge, synthesizes a StyleMap, and appends it as STYLE DIRECTIVE.

6. **Is Dynamic Band Lore actually implemented separately?**  
   **Observed:** No separate lore subsystem for songs. “Lore Engine” is marketing; implementation is Song DNA + image pipeline (+ optional StyleMap). `animal.lore.engine.php` is a different experiment.

7. **How is artist identity represented without simply copying album art?**  
   **Observed:** StyleMap instructions forbid logos/text/album replicas and ask for original stylistic interpretation inspired by cover-artist craft. Song DNA uses band/song context without requiring album pixels. Residual imitation risk remains via named influences.

8. **How are lyrics transformed into imagery?**  
   **Observed:** lyrics inform Song DNA; DNA fields feed cinematic prompt; prompts forbid quoting lyrics and forbid visible lyric text in image.

9. **How much is literal vs metaphorical?**  
   **Inferred:** design intent is metaphorical cinematic beat; literal objects can still appear via DNA content. No explicit literalism dial.

10. **How does the user portrait become part of the scene?**  
    **Observed:** inline image parts + CRITICAL facial-match / protagonist-in-environment instructions.

11. **What makes Arcana images “cinematic” according to the prompt?**  
    **Observed:** explicit cinematography/environment/lighting/depth/motion/technical film language — not the adjective alone.

12. **What happens when image generation gets blocked?**  
    **Observed:** retry chain softens prompt, drops portraits, widens framing, then abstract/landscape under IMAGE_SAFETY; else fail.

13. **At what retry point is the user’s portrait removed?**  
    **Observed:** Attempt 4.

14. **How much creative meaning survives the fallback chain?**  
    **Inferred:** themes/mood/palette can survive through attempt 5; attempts 6–7 keep only selected DNA fragments as abstract/landscape. Character narrative and identity mostly gone after 4–5.

15. **What prompt instructions are redundant or contradictory?**  
    **Observed:** branding vs no-text; portraits vs no real people; duplicated safety; photoreal vs later illustrative; StyleMap influence vs no replicas.

16. **What parts appear to be hacks for older Gemini models?**  
    **Inferred:** logo bans, repeated no-text, search-retrieval repair path, temperature-only retries, illustrative safety downgrade, fresnel laundry list.

17. **What parts remain genuinely strong ideas?**  
    **Observed/Proposed:** staged DNA, structured visual fields, portrait-as-protagonist, soft custom instructions, async jobs, StyleMap-as-design-brief, progressive fallback philosophy (if repaired and reordered).

18. **What intermediate artifacts should V2 add?**  
    **Proposed:** Visual Narrative Plan, Artist Visual Identity separate from Song DNA, Portrait Integration Plan, Prompt Compiler IR, Evaluation/Retry Decision record. See `11-v2-prompt-refinement-plan.md`.

19. **What should V2 remove?**  
    **Proposed:** marketing/implementation mismatch; giant undifferentiated prompt; early identity drop without consent; full lyric persistence by default; contradictory branding/no-text coupling; unvalidated influence name-dropping; multiple conflicting workers as sources of truth.

20. **How can V2 make “put me inside the song” more reliable?**  
    **Proposed:** treat portrait identity as a first-class plan with priority rank; never silently drop identity; retry by changing scene/props/safety before removing the person; show users when identity could not be preserved; evaluate face/scene fit; optionally regenerate with explicit consent to identity-free fallback. See docs 11–12.

---

## PART 10 — Copyright / lyrics architecture (non-legal)

**Observed V1 behavior:**

- Full lyrics accepted from user textarea.
- Lyrics stored on `arcana_queue` and `arcana_renders`.
- Lyrics sent to Gemini text analysis (truncated 16k).
- Analysis instructed not to quote/paraphrase lyrics in JSON.
- Image prompts forbid visible lyrics/text.
- Final prompts can still contain DNA-derived imagery inspired by lyric content.
- Band/song names stored and included in prompt context.

**Proposed V2 architecture options (product/legal review required):**

- transient lyric processing (process → DNA → discard);
- persist derived DNA only;
- store lyric hash/provenance, not full text;
- licensed lyric providers vs user-pasted only;
- never render lyric lines as image text;
- retention/deletion policy for creative intermediates.

These are **not** legal conclusions.

---

## Cross-links

- Prompt transformations & schemas: `10-v1-prompt-pipeline.md`
- V2 prompt refinement proposals: `11-v2-prompt-refinement-plan.md`
- Open product questions: `12-open-creative-decisions.md`
- Prior feature map: `06-v1-feature-map.md`
