# 10 — V1 Prompt Pipeline & Schemas

**Purpose:** forensic record of prompt transformations from user input to image model request.

**Best-evidence source:** `arcana.queue.processor.cron.parallel.v3.dynamicstyle.php`  
**Intact retry text reference:** `arcana.queue.processor.cron.php`  
**Companion audit:** `09-v1-creative-engine-audit.md`  
**Status:** evidence only

Evidence labels: **Observed** · **Inferred** · **Proposed** · **Open Question**

---

## Pipeline diagram (source-proven)

```
artist + song + lyrics? + custom? + aspect + image_style? + portraits?
                          │
                          ▼
                 arcana_queue (pending)
                          │
                          ▼
        ┌─────────────────────────────────────┐
        │ STAGE A — Song DNA (text model)     │
        │ gemini-2.0-flash-exp                │
        │ responseMimeType: application/json  │
        └─────────────────────────────────────┘
                          │
                          ▼
        ┌─────────────────────────────────────┐
        │ STAGE B — Cinematic prompt assembly │
        │ PHP template + portrait directives  │
        └─────────────────────────────────────┘
                          │
          ┌───────────────┼────────────────┐
          ▼               ▼                ▼
   image_style==""   ANALYZE_BAND_STYLE   other style_key
   (skip style)      Stage C+D StyleMap   Stage E DB style
                          │                │
                          └────────┬───────┘
                                   ▼
        ┌─────────────────────────────────────┐
        │ STAGE F — Image request             │
        │ gemini-2.5-flash-image              │
        │ text + optional inlineData portraits│
        │ imageConfig.aspectRatio             │
        └─────────────────────────────────────┘
                          │
                          ▼
                 STAGE G — Retry mutations
```

---

## STAGE A — Song DNA analysis

### Model — Observed

- Endpoint: Google Generative Language `generateContent`
- Model: `gemini-2.0-flash-exp`
- Config: temperature `0.8`, topK `64`, topP `0.95`, `responseMimeType: application/json`
- Transport in v3: parallel `curl_multi` batch of analysis requests

### System / task framing (structure) — Observed

Role: expert musicologist, narrative analyst, and visual director translating music into cinematic imagery.

Task: analyze a song and return a detailed DNA blueprint for image generation.

Lyric rule: if lyrics provided, use them to inform analysis; **never quote or paraphrase them directly in output**.

Output rule: STRICT JSON with exact keys; only valid JSON; no markdown/fences/explanations; no direct lyric quotes; be specific/vivid; tailor to this song and band.

### User message variables — Observed

```
Band/Artist: {band}
Song: {song}

[optional]
Provided lyrics (analyze but do not quote):
{lyrics_for_model}   # mb_substr(lyrics, 0, 16000)

Return the JSON object only.
```

### Expected JSON schema — Observed

| Key | Type | Mandatory in prompt? | Validation in v3 |
|---|---|---|---|
| `summary` | string | yes (listed) | not enforced beyond JSON object |
| `narrative` | string | yes | not enforced |
| `themes` | string[] | yes | not enforced |
| `symbols` | string[] | yes | not enforced |
| `mood` | string[] | yes | not enforced |
| `visual_metaphors` | string[] | yes | not enforced |
| `palette` | string[] | yes | not enforced |
| `lighting` | string[] | yes | not enforced |
| `camera` | string[] | yes | not enforced |
| `composition` | string[] | yes | not enforced |
| `influences` | string[] | yes | not enforced |
| `texture` | string[] | yes | not enforced |

### Parsing — Observed

1. Read `candidates[0].content.parts[0].text`
2. Strip markdown fences / leading tags
3. `json_decode`
4. If not array → item error `"Invalid JSON from song analysis"`
5. Else store as `$itemsData[idx]['analysis']`

### Fallbacks — Observed / variant notes

| Worker | On bad JSON |
|---|---|
| v3.dynamicstyle / gpt / parallel | fail item |
| sequential cron / processor | fence strip + optional search path + repair prompt + typed normalization |

### Retained vs discarded — Observed

**Retained:** full analysis object in memory; later `json_encode` into `arcana_renders.analysis_json`.

**Discarded before image prompt:** raw lyrics are not copied into cinematic prompt; only DNA fields. Lyrics remain in DB rows separately.

**Array truncation at prompt time:** each array field joined with `", "` after `array_slice(..., 0, 10)`.

### Downstream consumer — Observed

Stage B cinematic assembler. Also attempts 6–7 reuse selected DNA fields.

---

## STAGE B — Cinematic prompt compiler

### Model — Observed

None. Pure PHP string assembly into `$basePrompt`.

### Section map (actual order) — Observed

| # | Section | Contents / slots |
|---|---|---|
| 1 | MISSION | “cinematic, photoreal illustration”; song essence |
| 2 | SONG DNA | `summary`, `narrative`, joined `themes/symbols/visual_metaphors/mood` |
| 3 | CHARACTER & SCENE | portrait directive + environment requirements checklist |
| 4 | CINEMATOGRAPHY | joined `palette/lighting/camera/composition/texture/influences` |
| 5 | TECHNICAL SPECIFICATIONS | `{aspect}` canvas; render-quality bullets; constraints; branding block |
| 6 | COPYRIGHT & CONTENT SAFETY | originality; no text/lyrics/logos; no real people; no album replicas |
| 7 | OUTPUT GOAL | cohesive image priorities; `Band: {band}`; `Song: {song}` |
| 8 | USER CUSTOMIZATION | optional soft `{custom_instructions}` with non-override rules |
| 9 | STYLE DIRECTIVE | optional Stage C–E output |
| 10 | SAFETY (again) | appended if style present |
| 11 | WATERMARK OVERRIDE | if `watermark != 1` |

### Portrait directive templates — Observed

**Two portraits:** CRITICAL two main characters; Character 1 = first attachment; Character 2 = second; exact facial match; waist-up/full-body; interaction; avoid studio/passport poses.

**One portrait:** CRITICAL primary character; exact facial match; protagonist in environment.

**None:** central protagonist inspired by song narrator.

### Custom instructions policy — Observed

Integrate only if they harmonize with Song DNA / scene / lighting / composition / style. Soft guidance. Must not add text/logos/IP. Must not violate safety or t-shirt constraints. Conflict → adapt or omit.

### Branding block — Observed

When watermark enabled:

- bottom-right: “AI Saga Arcana” elegant glowing gold script;
- bottom-left: “YouAreTheSongNow.com” luminous blue script;
- text naturally lit by scene;
- also exclude Gemini logo.

When disabled: regex remove branding block + append explicit no-branding override.

### Critical excerpts (structure preserved, not full dump)

Mission line:

> MISSION: Create a cinematic, photoreal illustration that embodies this song's essence.

DNA interpolation pattern:

> Essence: {summary}  
> Narrative Moment …: {narrative}  
> Thematic Core: {themes joined}

Cinematography pattern:

> Color Palette: {palette}  
> Lighting Design: {lighting}  
> Camera Language: {camera}  
> …

### Conflicting / repeated instructions — Observed

- “NO visible text…” vs branding text requirements.
- Safety “no recognizable portraits of real people” vs portrait exact-match.
- Safety block duplicated when style appended.
- Word “cinematic” appears in mission, safety focus line, style rules, and output goal.

### Downstream — Observed

`$itemsData[idx]['basePrompt']` consumed by Stage F/G.

---

## STAGE C — Dynamic Band Visual DNA (optional)

### Trigger — Observed

`strtoupper(image_style) === 'ANALYZE_BAND_STYLE'` **and** `image_style !== ''`.

### Model — Observed

Same text model helper: temperature `0.6`, topK `40`, topP `0.95`, `maxOutputTokens` ~320.

### Inputs — Observed

`{song}`, `{band}` only. No lyrics. No Song DNA. No portraits.

### Prompt structure — Observed

1. Identify visual artist/designer of album cover for song/band.
2. If known: name + characteristic style/techniques on that cover.
3. If unknown: infer school/influence examples (Frazetta-like, Bauhaus, vaporwave, 80s airbrush, punk zine, minimalist photo, etc.).
4. Return one concise paragraph summarizing artist/influence, medium, composition/palette/lighting/symbolism, emotional/narrative tone relative to music.
5. Base strictly on **official album cover artwork**, not videos/stage/general aesthetic.
6. Do not include/replicate logos/trademarks/text.
7. Output only the paragraph.

### Expected output — Observed

Freeform prose string (`$bandDNA`).

### Fallback — Observed

If empty:

> Band Visual DNA: Cohesive mythic stage and narrative costuming; saturated signature palette tempered by metal/stone neutrals; painterly or analog film mood; symmetrical, icon-like compositions with controlled, ritual energy.

### Downstream — Observed

Fed into Stage D StyleMap synthesis. In sequential workers, often re-included in final style block; in v3, StyleMap application directive uses StyleMap primarily (DNA used as synthesis input).

---

## STAGE D — StyleMap synthesis (optional)

### Model — Observed

Same text helper; `maxOutputTokens` ~650.

### Prompt structure — Observed

Using Band Visual DNA, synthesize production-ready Arcana StyleMap with exact headings, 1–2 vivid sentences per line; no bullets/extra commentary.

Required headings:

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

Rules:

- translate motifs/palette/geometry from Band Visual DNA;
- concrete craft terms;
- no logos/visible text/album replicas/trademarked layouts;
- coherent cinematic; avoid generic fantasy filler.

### Expected output — Observed

Headed plaintext StyleMap (`$styleMap`).

### Validation — Observed

If empty **or** `stripos($styleMap, 'STYLE:') === false` → use canned fallback StyleMap (“Mythic Cinematic Realism” with full heading set).

### Application wrapper (v3) — Observed

```
{styleMap}

### STYLE APPLICATION DIRECTIVE ###
Use the StyleMap above as the governing visual aesthetic for the cinematic prompt that follows.
Fuse the band's atmosphere, mythology, and tone into composition, color behavior, materials, and environment.
Do not render any text, album titles, or logos. Output must be fully original and commercially safe.
```

Then appended as:

```
STYLE DIRECTIVE:
{stylePrompt}
```

plus second copyright/safety block.

### What StyleMap is / is not — Observed

**Is:** a visual production brief derived from album-cover aesthetics.

**Is not:** a lore bible, character roster, plot outline, or lyric interpretation layer.

---

## STAGE E — Static style injection (optional)

### Trigger — Observed

Non-empty `image_style` that is **not** `ANALYZE_BAND_STYLE`.

### Mechanism — Observed

```sql
SELECT prompt_text FROM arcana_styles
WHERE style_key = ? AND is_active = 1
LIMIT 1
```

If missing/inactive: warn and proceed without style directive.

### Schema of stored styles — Observed (from admin handler)

`style_key`, `name`, `prompt_text`, `category`, `sample_image_url`, `is_active`, `sort_order`.

Admin UI encourages StyleMap-like `prompt_text` format (STYLE/MEDIUM/COLOR/…).

### Legacy alternate — Observed

`arcana.queue.processor.php` uses a large inline PHP `$styleMap` array instead of DB. Not the v3 path.

---

## STAGE F — Image model request

### Model — Observed

- `gemini-2.5-flash-image`
- `responseModalities: ["IMAGE"]`
- `imageConfig.aspectRatio: {aspect}`
- temperature from attempt (default 0.8)
- topK 40, topP 0.95

### Parts assembly — Observed

1. text part = attempt prompt
2. each portrait (if any) as `inlineData { mimeType, data }`

### Success extraction — Observed

Parse candidates for inline image data; convert/save WebP; thumbnail; optional B2; insert `arcana_renders` with `prompt` + `analysis_json` + lyrics + portraits_json metadata.

---

## STAGE G — Retry / safety mutations

### Gate function — Observed (v3)

- skip if image already obtained or hard error set;
- attempts 1–5 always while unresolved;
- attempts 6–7 only if `lastFinishReason === 'IMAGE_SAFETY'`.

### Intact attempt table (from sequential cron.php) — Observed

| Attempt | Prompt mutation | Portraits | Temp |
|---|---|---|---|
| 1 | full `basePrompt` | yes | 0.8 |
| 2 | same | yes | 0.75 |
| 3 | regex-remove BRANDING section; remove “NO visible text…” constraint line | yes | 0.65 |
| 4 | branding strip + SAFETY GUARANTEES (fictional characters, no celebrity likeness, clothed, non-violent, no weapons-in-use, avoid close-up faces) + STYLE ADJUSTMENT (painterly/matte) | **no** | 0.55 |
| 5 | attempt-4 prompt + FRAMING wide/long + illustrative/concept-art emphasis | no | 0.45 |
| 6 | new abstract prompt: no people/faces/bodies/text; geometry/nature; selected DNA fields | no | 0.5 |
| 7 | new landscape-only prompt; selected DNA mood/palette/lighting/composition | no | 0.45 |

### Attempt 6 DNA fields retained — Observed

`summary`, `themes`, `symbols`, `mood`, `palette`, `lighting`, `composition`

### Attempt 7 DNA fields retained — Observed

`summary`, `mood`, `palette`, `lighting`, `composition`

### Discarded by late retries — Observed / Inferred

Portraits; much of CHARACTER & SCENE; branding; often StyleMap richness; camera/texture/influences/visual_metaphors depending on attempt.

### v3 corruption note — Observed

v3/gpt `arcana_build_prompt_for_attempt` contains truncated `...` placeholders in regexes and prompt strings. Treat sequential cron.php as the behavioral specification of intended mutations; treat v3 as intended control-flow with degraded mutation text.

### State diagram

```
[start attempt 1]
   | success -> COMPLETE
   | fail
[attempt 2 temp↓]
   | success -> COMPLETE
   | fail
[attempt 3 strip branding/text-rule]
   | success -> COMPLETE
   | fail
[attempt 4 DROP PORTRAITS + soften]
   | success -> COMPLETE (identity lost; user may not know)
   | fail
[attempt 5 widen + illustrative]
   | success -> COMPLETE
   | fail
   | if finishReason != IMAGE_SAFETY -> FAIL
[attempt 6 abstract no people]
   | success -> COMPLETE
   | fail
[attempt 7 landscape only]
   | success -> COMPLETE
   | fail -> FAIL
```

---

## Variable retention matrix

| Data | Sent to DNA model | In base image prompt | In StyleMap path | Stored in DB |
|---|---|---|---|---|
| band | yes | yes (footer + style phase) | yes | yes |
| song | yes | yes | yes | yes |
| lyrics | yes (≤16k) | no (directly) | no | yes (full) |
| custom instructions | no | yes (soft block) | no | yes |
| portraits bytes | no | via inlineData + text directive | no | paths/meta |
| Song DNA | n/a | yes | no (dynamic style ignores it) | `analysis_json` |
| StyleMap | n/a | appended if triggered | n/a | only inside final prompt text |
| aspect | no | yes + imageConfig | no | yes |

---

## Worker variance cheat-sheet

| Concern | Best evidence | Important variant |
|---|---|---|
| DNA schema | identical 12 fields | sequential adds repair/normalization |
| Dynamic StyleMap | v3 / gpt / sequential cron | absent in parallel.php & old processor.php |
| Retry text integrity | sequential cron.php | v3/gpt truncated |
| Static styles | DB in v3/sequential | inline map in old processor |
| Default UI style | empty string | does not trigger StyleMap |

---

## Source citations (primary)

- Song DNA prompt: `arcana.queue.processor.cron.parallel.v3.dynamicstyle.php` ~553–587
- Cinematic assembly: same file ~671–800
- Dynamic StyleMap: same file ~811–932
- Static style lookup: same file ~934–962
- Retry builders (corrupted): same file ~1009–1142
- Intact retries: `arcana.queue.processor.cron.php` ~962–1045
- UI default style gap: `arcana.image.generator.php` ~1956–1978
- Queue insert: `arcana.image.generator.php` ~508–522
