# 13 — V1 Prompting Functionality Reference

**Status:** evidence/specification only — no V2 implementation  
**Purpose:** document Arcana V1's prompting behavior as product functionality, including source prompts, runtime-assembled prompts, database-stored style directives, model-generated artifacts, and retry mutations.  
**Primary evidence:** `arcana.queue.processor.cron.parallel.v3.dynamicstyle.php`, `arcana.queue.processor.php`, and `arcana.styles.handler.php` in the V1 repository.  
**Related:** `09-v1-creative-engine-audit.md`, `10-v1-prompt-pipeline.md`, `11-v2-prompt-refinement-plan.md`

Evidence labels: **Observed** · **Inferred** · **Proposed** · **Open Question**

---

## Executive finding

Arcana does not have one prompt. It has a **prompting system** with several different sources of instructions:

| Type | Meaning | V1 examples |
|---|---|---|
| `SOURCE_STATIC` | Prompt/template text hardcoded in PHP | Song DNA system text, cinematic template, portrait directives, retry text, older inline style catalog |
| `RUNTIME_ASSEMBLED` | Prompt composed from templates + live values | final cinematic image prompt |
| `DATABASE_STORED` | Prompt text editable/persisted outside source | `arcana_styles.prompt_text` static visual styles |
| `MODEL_GENERATED` | One AI call creates instructions/artifacts for another | Song DNA JSON, Band Visual DNA, StyleMap |
| `FALLBACK_MUTATION` | Existing prompt is changed after failure/safety events | seven-stage image retry ladder |

This distinction matters for V2 because prompt behavior should be versioned and testable regardless of whether its text lives in PHP, SQLite, or an AI-produced intermediate artifact.

---

# 1. Source-to-pixels map

```text
USER INPUT
artist / song / lyrics / custom instructions / aspect / style / portraits
        |
        v
[P1] SONG DNA ANALYSIS                              MODEL_GENERATED
        |
        +--> 12-field JSON
        |
        v
[P2] BASE CINEMATIC PROMPT COMPILER                RUNTIME_ASSEMBLED
        |
        +--> static mission / scene / camera rules SOURCE_STATIC
        +--> portrait directive                    SOURCE_STATIC + runtime choice
        +--> Song DNA values                       MODEL_GENERATED
        +--> user customization                    runtime user data
        +--> branding/safety                       SOURCE_STATIC
        |
        +-----------------------------+
        |                             |
        |                       style selection
        |                             |
        |             +---------------+----------------+
        |             |                                |
        |             v                                v
        |    [P3] BAND VISUAL DNA               STATIC STYLE LOOKUP
        |        MODEL_GENERATED                DATABASE_STORED
        |             |                         arcana_styles.prompt_text
        |             v                                |
        |    [P4] STYLEMAP SYNTHESIS                    |
        |        MODEL_GENERATED                        |
        |             +---------------+----------------+
        |                             |
        +-----------------------------+
                      v
             [P5] IMAGE MODEL REQUEST
                      |
                      v
             [P6] RETRY MUTATIONS 1–7
                 FALLBACK_MUTATION
```

---

# 2. P1 — Song DNA analysis

## Classification

`SOURCE_STATIC` prompt template → `MODEL_GENERATED` JSON artifact.

## Purpose — Observed

Translate artist/song/lyrics into a structured visual interpretation **before** image generation.

## Model/config — Observed

- text model: `gemini-2.0-flash-exp` in the best-evidence worker;
- temperature `0.8`;
- topK `64`;
- topP `0.95`;
- response MIME `application/json`;
- supplied lyrics are truncated to roughly 16,000 characters.

## Role framing — Observed

The model is framed as an expert:

- musicologist;
- narrative analyst;
- visual director;
- translator of music into cinematic imagery.

## Inputs — Observed

- band/artist;
- song title;
- optional lyrics.

It does **not** receive portraits, aspect ratio, style selection, or custom image instructions at this stage.

## Lyrics rule — Observed

Lyrics may inform the analysis, but the output is instructed not to quote or directly paraphrase them.

## Exact output shape — Observed

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

## Functional field map

| Field | Job in the system | Final-prompt effect |
|---|---|---|
| `summary` | compress the essence | high-level scene identity |
| `narrative` | choose/read a story beat | character/action moment |
| `themes` | thematic anchors | semantic constraints |
| `symbols` | symbolic vocabulary | objects/environment |
| `mood` | emotional state | atmosphere/grading |
| `visual_metaphors` | transform meaning into imagery | visual storytelling |
| `palette` | color direction | cinematography |
| `lighting` | illumination direction | cinematography |
| `camera` | lens/shot language | framing |
| `composition` | spatial arrangement | image structure |
| `influences` | aesthetic reference language | style pressure |
| `texture` | material/surface behavior | rendering detail |

## Validation — Observed

The v3 worker mostly requires successful JSON decoding. Older sequential workers contain more elaborate fence stripping, repair, and normalization. There is no strong schema/version contract across all variants.

## Strong functionality worth preserving

- interpretation happens before pixels;
- structured information is produced rather than unstructured prose only;
- raw lyrics are not simply copied into the image prompt;
- song-specific symbolism and visual metaphors can drive imagery.

## Problems to refine

- semantic interpretation and cinematography are mixed in one artifact;
- `influences` can inject named artistic/IP references;
- no confidence, safety flags, alternative readings, explicit setting candidates, or literal/metaphor control;
- weak structural validation in the later worker.

---

# 3. P2 — Cinematic prompt compiler

## Classification

`RUNTIME_ASSEMBLED`, built by PHP from `SOURCE_STATIC` templates + Song DNA + runtime inputs.

## Important distinction

V1 does **not** ask a second text model to write the final cinematic prompt. PHP itself compiles the final text.

## Section order — Observed

The best-evidence worker organizes the image prompt approximately as:

1. `MISSION`
2. `SONG DNA`
3. `CHARACTER & SCENE`
4. `CINEMATOGRAPHY`
5. `TECHNICAL SPECIFICATIONS`
6. `COPYRIGHT & CONTENT SAFETY DIRECTIVE`
7. `OUTPUT GOAL`
8. optional `USER CUSTOMIZATION`
9. optional `STYLE DIRECTIVE`
10. another safety block if a style is appended
11. optional watermark/branding override

## What “cinematic” means functionally — Observed

The legacy prompt does much more than use the adjective *cinematic*. Across worker generations it contains instructions for:

- foreground / midground / background separation;
- readable focal hierarchy;
- environmental storytelling;
- motivated lighting including key/fill/rim behavior;
- atmospheric perspective and depth cues;
- camera/shot language;
- implied movement in characters, fabric, particles or weather;
- material/surface behavior;
- film grain, bloom/halation and other post-processing language;
- scene readability at the chosen aspect ratio.

The older worker contains particularly explicit cinematography instructions and is useful as evidence of the intended prompt vocabulary even where later versions simplified it.

---

# 4. Portrait prompt functionality

## Classification

`SOURCE_STATIC` directive selected and populated at runtime.

## Zero portraits — Observed

The prompt asks for a central protagonist inferred from the song narrator/meaning.

## One portrait — Observed

The uploaded reference is promoted to **primary protagonist**. The prompt emphasizes:

- facial identity fidelity;
- recognizable match to the attachment;
- integration into the scene;
- action/environment rather than a studio/passport pose.

## Two portraits — Observed

- first attachment becomes Character 1;
- second becomes Character 2;
- both are central;
- interaction is encouraged;
- both identities should remain recognizable.

## Product meaning

This directive is a major part of the product promise. The portrait is not merely passed as image data; the prompting system assigns the uploaded person a **story role**.

## Contradiction — Observed

Later generic safety text says to avoid recognizable portraits of real people, directly competing with the exact-match portrait instruction.

---

# 5. User custom instructions

## Classification

Runtime user content inserted into a `SOURCE_STATIC` containment/precedence wrapper.

## Precedence behavior — Observed

User directions are explicitly described as **soft guidance**, not an override. They may refine things such as:

- mood;
- palette;
- weather;
- camera;
- set dressing.

If they conflict with the song interpretation, style, scene, safety, or other constraints, the model is told to adapt or omit them.

## Strong idea

This is useful functionality worth retaining: users can steer the result while the application keeps higher-level creative and safety rules authoritative.

## V2 refinement

Represent precedence as a formal contract/policy, not only prose order inside a giant prompt.

---

# 6. Dynamic visual-identity prompting

## Trigger — Observed

Runs only when `image_style` resolves to `ANALYZE_BAND_STYLE`.

The earlier audit found a UI mismatch: the default option was marketed as the Dynamic Band Lore Engine while submitting an empty style key, so this dynamic StyleMap path was not actually the normal default generation path.

## P3 — Band Visual DNA

### Classification

`SOURCE_STATIC` analysis prompt → `MODEL_GENERATED` prose artifact.

### Inputs

- artist/band;
- song title.

No lyrics, Song DNA, portraits, or custom instructions are supplied.

### Function

The model is asked to identify the official album-cover artist/designer when known, or infer a likely visual school/influence otherwise. It describes:

- medium;
- stylistic approach;
- composition;
- palette;
- lighting;
- symbolism;
- emotional/narrative tone.

It is instructed to focus on official album artwork rather than stage/videos/general band aesthetic, while avoiding logos/text/trademark copying.

### Output

A concise prose visual-analysis paragraph. A generic visual-DNA paragraph is used if the call fails/returns empty.

## P4 — StyleMap synthesis

### Classification

`SOURCE_STATIC` synthesis prompt → `MODEL_GENERATED` headed visual brief.

### Required headings — Observed

```text
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

### Function

Turn the Band Visual DNA into concrete production language such as pigments, film stocks, brushwork, camera/lens behavior, materials, atmosphere, and things to avoid.

### Validation/fallback

If the output is empty or lacks `STYLE:`, a built-in `Mythic Cinematic Realism` StyleMap is substituted.

## Core naming conclusion

This is **dynamic visual identity**, not a persistent lore engine. V1's “Dynamic Band Lore Engine” marketing combines multiple systems under one name.

---

# 7. Database-stored prompt functionality

## Classification

`DATABASE_STORED`.

This is the part of V1 that was moved out of PHP near the later design.

## Verified table behavior — Observed

The newer worker retrieves a selected static style using the equivalent of:

```sql
SELECT prompt_text
FROM arcana_styles
WHERE style_key = ? AND is_active = 1
LIMIT 1
```

The admin handler proves that `prompt_text` is a first-class editable database field alongside:

- `style_key`;
- `name`;
- `category`;
- `sample_image_url`;
- `is_active`;
- `sort_order`;
- `created_at`.

Admins can list, read, add, update, activate/deactivate and delete these style records.

## Important implication

The final production style catalog cannot be reconstructed **perfectly** from PHP alone if the database rows themselves are not present in the repository. A production DB export would be needed to prove the exact final set and final edited text.

However, the repository preserves unusually strong evidence of the catalog's lineage.

---

# 8. Older inline style catalog: likely predecessor to the DB catalog

## Classification

`SOURCE_STATIC` legacy style directives.

The older `arcana.queue.processor.php` contains a large `$styleMap` associative array where each style key maps directly to a complete prompt directive. Later workers replace this behavior with `arcana_styles.prompt_text` lookup.

**Inferred:** this is the clearest recoverable predecessor/source material for at least much of the later database-managed catalog.

## Examples observed in the legacy catalog

The source contains dozens of style prompt families, including examples such as:

### General / medium / visual approaches

- cyberpunk neon noir;
- vaporwave/retrowave;
- 1990s grunge/xerox;
- hip-hop graffiti;
- minimal techno/Bauhaus;
- jazz expressionism;
- gothic darkwave;
- indie pastel;
- expressionism;
- dreamcore/liminal;
- cinematic photorealism;
- impressionist painting;
- oil-painting realism;
- charcoal/ink/Sumi-e;
- surrealist collage.

### Era / genre hybrids

The catalog also includes a large sequence of era/genre visual languages, for example:

- 1920s Jazz Deco;
- 1930s WPA Swing;
- 1940s studio portrait;
- 1950s Rockabilly Americana;
- 1960s Motown glamour;
- 1970s Krautrock minimalism;
- 1970s prog-rock surrealism;
- disco airbrush;
- reggae poster;
- 1980s synthpop neon;
- New Wave Memphis;
- hardcore-punk Xerox;
- 1990s shoegaze blur;
- trip-hop noir;
- black-metal photocopy;
- 2000s indie Swiss minimalism;
- Y2K chrome pop;
- 2010s EDM festival neon;
- K-pop hypergloss;
- 2020s hyperpop glitch-candy.

### Cinematic / fantasy / sci-fi families

Additional catalog entries include cyberpunk, retrofuturism, holographic sci-fi, pulp space opera, epic fantasy and other cinematic variants.

## Common mini-schema

Many of these legacy style directives use a compact structured vocabulary such as:

```text
STYLE:
MEDIUM:
COLOR:
LIGHTING:
SURFACE:
MOTION:
COMPOSITION:
AVOID:
```

Some richer/special variants extend this toward the later full StyleMap vocabulary.

## Why this matters for V2

We should preserve the **functional idea of curated style presets**, but we do not need to copy every legacy phrase. The catalog can be reconstructed, evaluated, deduplicated and rewritten for modern image models.

---

# 9. Special legacy analysis-style prompts

The older inline catalog also contains unusually elaborate style modes that do more than inject a fixed aesthetic.

Observed examples include prompts conceptually equivalent to:

- inferred style from official album-cover artwork;
- dynamic style from historical-performance analysis.

These prompts ask the model to reason about source aesthetics/performance eras and then convert that reasoning into detailed visual instructions.

**Inferred:** these are experiments/precursors to the later `ANALYZE_BAND_STYLE` workflow, and they help explain the evolution from giant embedded style prompts toward a two-stage Band Visual DNA → StyleMap system.

They should be documented as historical functionality, not automatically restored as V2 requirements.

---

# 10. Branding functionality

## Classification

`SOURCE_STATIC` instructions plus runtime removal/mutation.

V1 asks the image model to create Arcana/YouAreTheSongNow branding in some modes while also carrying general instructions against visible text.

When branding/watermark is disabled, PHP tries to remove the branding block and appends an explicit no-branding instruction.

## Contradiction

The generated-image prompt can simultaneously require and prohibit text. This is a strong candidate for removal in V2.

## Proposed modern behavior

Use post-generation compositing/dedicated typography rather than requiring the generative image model to render deterministic branding.

---

# 11. P5 — Image-model request

## Classification

Compiled prompt + optional image-reference parts sent to provider.

## Observed best-evidence model

- `gemini-2.5-flash-image`;
- image response modality;
- selected aspect ratio passed as image configuration;
- text prompt plus zero/one/two inline portrait images.

The prompt therefore works together with multimodal reference data; prompt wording alone is not the complete portrait mechanism.

---

# 12. P6 — Seven-stage fallback/retry prompting

## Classification

`FALLBACK_MUTATION`.

The retry system is part of prompting functionality because later attempts materially rewrite what is being requested.

| Attempt | Prompt behavior | Portraits | Approx. temperature |
|---|---|---:|---:|
| 1 | full compiled prompt | yes | 0.80 |
| 2 | same prompt, lower randomness | yes | 0.75 |
| 3 | simplify/remove branding/text-related constraints | yes | 0.65 |
| 4 | stronger safety rewrite, painterly/softened direction | **removed** | 0.55 |
| 5 | wider/illustrative framing, simplified safe scene | no | 0.45 |
| 6 | new abstract/no-people prompt retaining selected Song DNA | no | 0.50 |
| 7 | landscape-only last resort with reduced DNA | no | 0.45 |

Attempts 6–7 are gated on image-safety failure in the newer control flow.

## Important source-quality note

The v3 worker contains visibly truncated/corrupted `...` text in portions of its retry-building code. The older sequential `arcana.queue.processor.cron.php` contains intact text and should be treated as the stronger specification for the intended mutation language, while v3 remains useful evidence of the intended control flow.

## Product problem

By attempt 4, a user who explicitly selected portrait mode may receive a successful result with the identity reference removed. V1 does not clearly surface this degradation.

---

# 13. Prompt precedence in V1

The effective precedence is only partially explicit.

A useful approximation from source is:

```text
hard safety / provider behavior
        ^
application safety/copyright instructions
        ^
portrait + scene directives
        ^
Song DNA / cinematic structure
        ^
style directive
        ^
custom user instructions (explicitly soft)
```

But the actual concatenated prompt contains contradictions, and later text can sometimes compete with earlier text. There is no formal compiler-level conflict-resolution system.

---

# 14. Prompt persistence and provenance

Observed V1 persistence includes:

- Song DNA analysis JSON stored with renders;
- final compiled prompt stored with renders;
- raw lyrics stored in legacy queue/render paths;
- selected style key stored on queue/render paths;
- static style prompt text lives in `arcana_styles` DB records;
- generated StyleMap does not appear to have a dedicated durable first-class record of its own.

This is insufficient for serious prompt versioning because a future admin edit to `arcana_styles.prompt_text` can change behavior without an immutable revision trail.

---

# 15. Recommended V2 prompt-artifact metadata — Proposed

For every generation, V2 should eventually be able to answer **exactly which prompting behavior produced this result**.

Useful metadata:

```text
prompt_system_version
song_interpretation_version
song_dna_schema_version
scene_planner_version
artist_identity_version
portrait_policy_version
prompt_compiler_version
retry_policy_version
provider
text_model
image_model
static_style_id
static_style_revision
compiled_prompt_hash
experiment_id (optional)
```

If curated styles remain database-backed, style edits should create revisions rather than silently overwriting historical prompt behavior.

---

# 16. What appears genuinely valuable from V1

1. **Interpret before generating.**
2. **Use structured Song DNA.**
3. **Give portraits a narrative role rather than merely attaching an image.**
4. **Keep custom instructions soft relative to product constraints.**
5. **Support both curated static styles and dynamically derived visual identity.**
6. **Use concrete cinematography language rather than generic “epic cinematic” adjectives alone.**
7. **Progressively adapt on provider failure instead of immediately abandoning the job.**

---

# 17. What should be redesigned rather than copied

1. One oversized final prompt with competing responsibilities.
2. Song meaning mixed with camera/style fields too early.
3. Portrait exact-match requirements fighting “no recognizable real people.”
4. Branding requirements fighting “no visible text.”
5. Silent identity removal during retries.
6. Named-style/influence hallucination/imitation risk.
7. Mutable DB prompt text without revisions/provenance.
8. Multiple worker variants as competing sources of prompt truth.
9. Model-specific workarounds preserved after the model landscape changes.

---

# 18. Database evidence still worth recovering

**Open Question:** the repository proves the schema/CRUD and lookup of database styles, but not necessarily the exact contents of the final production `arcana_styles` table at shutdown.

If a final database export is available later, perform a schema/data-only extraction of:

- `style_key`;
- `name`;
- `prompt_text`;
- `category`;
- `is_active`;
- `sort_order`;
- timestamps if available.

Then compare those rows to the older inline `$styleMap` catalog to determine exactly what was migrated, rewritten, added, removed or disabled.

Until then, the older inline catalog plus the later database CRUD/lookup code is the strongest recoverable documentation of static prompt functionality.

---

## Bottom line

V1's prompting should be preserved as a **behavioral specification**, not copied as strings. The important functionality is the orchestration:

**interpret song → create structured meaning → choose/derive visual identity → place user into the scene → compile cinematic instructions → generate → adapt intelligently on failure.**

V2 can keep that product intelligence while rewriting each prompt stage for modern models and making every stage versioned, observable, and testable.