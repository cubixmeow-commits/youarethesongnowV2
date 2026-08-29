---
type: product-technical-contract
status: owner-approved-for-private-development-build-1
updated: 2026-08-28
area: creative-engine
owners:
  - CuBiX Meow
  - Brut
foundation:
  - V1 Song DNA
  - V1 cinematic prompt compiler
  - V1 portrait integration
  - V1 StyleMap
---

# V2 Song DNA and Prompt Pipeline Contract

## Owner direction

Use the most recent V1 prompt system as the foundation. Preserve or improve its very good visual results while correcting its storage, copyright, validation, portrait, style and retry problems.

This contract is approved for Private Development Build 1. It does not authorize external access, live charging or commercial protected-lyrics use.

## Success standard

V2 is not successful merely because it generates an image. It must preserve the qualities that made V1 effective:

- interpret the song before generating pixels;
- place the uploaded person or people at the center of the visual story;
- produce a clear narrative moment rather than a generic portrait;
- create cinematic depth, atmosphere, lighting and motion;
- allow a curated visual style to shape the result strongly;
- keep user instructions useful but subordinate to identity, safety and composition;
- produce original imagery without lyrics, unauthorized branding or copied promotional artwork.

CuBiX Meow and Brut must judge the refined V2 benchmark equal to or better than the selected V1 anchor images before the creative engine is approved.

## V1 foundation to preserve

The final V1 analysis used these twelve fields:

1. summary;
2. narrative;
3. themes;
4. symbols;
5. mood;
6. visual metaphors;
7. palette;
8. lighting;
9. camera;
10. composition;
11. influences;
12. texture.

The final V1 prompt compiler added portrait identity, environment, cinematography, aspect ratio, technical detail, user customization, selected style and safety rules. This staged architecture remains the foundation.

## V2 pipeline

```text
Exact user entry
  -> Song/version match
  -> Eligible source selection
  -> Memory-only source analysis
  -> Draft Song DNA
  -> Independent leakage and originality sanitizer
  -> Approved Song DNA
  -> Original Visual Narrative Plan
  -> Portrait Integration Plan
  -> Selected StyleMap
  -> Versioned Prompt Compiler
  -> Provider and model route
  -> Output quality and safety evaluation
  -> Controlled retry or honest failure
  -> Gallery image and safe provenance
```

Raw lyrics exist only during the memory-only analysis and sanitizer steps. They never enter a database, queue, file, stored prompt history, log, analytics record, error report, backup or final image prompt.

## Separation of responsibilities

### Song matching

Song matching owns the exact user entry, normalized lookup value, likely recording/work, version, confidence and source eligibility. It does not create visual direction.

### Song DNA

Song DNA expresses high-level emotional, thematic and visualizable meaning without retaining lyric expression. It does not contain an art style, artist imitation, portrait instructions or provider syntax.

### Visual Narrative Plan

The narrative planner creates a new scene from approved Song DNA. It does not summarize the lyric storyline. Its job is to invent one clear, emotionally faithful visual moment.

### Portrait Integration Plan

The portrait planner decides how the uploaded person or people become the protagonists while preserving recognizable facial identity.

### StyleMap

The StyleMap controls medium, visual treatment and craft language. A separately selected curated style takes dominance while Song DNA continues to control meaning and emotional influence.

### Prompt compiler

The compiler combines only approved structured artifacts. It never sees raw lyrics and does not improvise song facts.

### Output evaluator

The evaluator decides whether an image can be delivered. Prompt instructions alone are not treated as proof of originality, quality or legal safety.

## V2 Song DNA schema

The V2 schema preserves the useful V1 fields, narrows the risky fields and adds the information needed for couples, visual planning and safety.

```json
{
  "schemaVersion": "song-dna-v2.0",
  "essence": "string",
  "emotionalArc": {
    "openingState": "string",
    "turningPoint": "string",
    "closingState": "string",
    "intensityPattern": ["string"]
  },
  "themes": ["string"],
  "relationshipDynamics": ["string"],
  "narrativeArchetype": "string",
  "originalVisualMoment": "string",
  "symbols": [
    {
      "concept": "string",
      "visualTranslation": "string"
    }
  ],
  "visualMetaphors": ["string"],
  "mood": ["string"],
  "environment": {
    "settingTypes": ["string"],
    "eraAtmosphere": "string",
    "weather": ["string"],
    "spatialCharacter": ["string"]
  },
  "palette": ["string"],
  "lighting": ["string"],
  "camera": ["string"],
  "composition": ["string"],
  "motion": ["string"],
  "texture": ["string"],
  "subjectRoles": ["string"],
  "ambiguities": ["string"],
  "confidence": 0.0,
  "riskFlags": ["string"]
}
```

### Field rules

- `essence`: one or two high-level sentences, not a synopsis.
- `emotionalArc`: broad emotional movement, not the lyric event sequence.
- `themes`: no more than six abstract themes.
- `relationshipDynamics`: no more than four general dynamics useful for solo, couple, family or group interpretation.
- `narrativeArchetype`: a generic pattern such as reunion, departure, transformation, celebration, resistance or remembrance.
- `originalVisualMoment`: a newly invented scene that communicates the emotional truth without recreating a lyric scene.
- `symbols`: familiar or independently generalized concepts with a new visual translation.
- `visualMetaphors`: original visual ideas, not literalized signature lyric lines.
- `environment`: setting categories and atmosphere without distinctive protected places or sequences.
- `palette`, `lighting`, `camera`, `composition`, `motion` and `texture`: production-ready visual direction inherited from V1's strongest behavior.
- `subjectRoles`: protagonist functions such as seeker, partners, celebrants or witnesses, not lyric character names.
- `ambiguities`: uncertainties the later planner must not invent around.
- `confidence`: analysis confidence from 0 through 1.
- `riskFlags`: categorical codes only. Never store the protected phrase or passage that triggered a flag.

Allowed risk codes initially include:

- `possible_quote`;
- `possible_close_paraphrase`;
- `distinctive_story_sequence`;
- `named_character_or_place`;
- `public_figure_reference`;
- `album_or_video_association`;
- `brand_or_logo_reference`;
- `source_or_match_uncertain`.

## Forbidden Song DNA content

Song DNA must never contain:

- a lyric quotation;
- a close paraphrase or synonym-swapped signature phrase;
- the song title, artist, band or album name;
- a reconstructive summary of the lyrics;
- distinctive lyric characters, fictional places or unique event sequences;
- a request to reproduce album art, music-video imagery, stage design or merchandise;
- artist or celebrity likenesses;
- logos, labels or endorsement cues;
- an artist-name style imitation;
- raw-source URLs or lyric-provider response text.

## Analysis behavior

The analyzer receives only the matched song context and the memory-only lyric source needed for that single request.

It must:

- return schema-conforming structured output;
- translate meaning into abstractions and original visual possibilities;
- avoid quoting and close paraphrase;
- distinguish uncertainty from fact;
- avoid album art, music videos and performer identity;
- avoid deciding the final curated style;
- produce no user-facing explanation or lyric text.

The analysis model and temperature are benchmark decisions rather than hardcoded product rules.

## Independent leakage and originality sanitizer

The draft Song DNA cannot reach the prompt compiler until a separate sanitizer approves it.

The sanitizer performs:

1. strict schema, type, length and count validation;
2. normalized phrase-overlap detection against the memory-only source;
3. quotation and close-paraphrase review;
4. detection of distinctive story sequences, characters and places;
5. song, artist, album, public-figure, brand and promotional-art reference detection;
6. confirmation that `originalVisualMoment` is newly composed rather than a lyric synopsis;
7. categorical risk-flag assignment without retaining the triggering lyric text.

Phrase-overlap thresholds are engineering warning signals, not legal safe-word counts.

On failure:

1. generalize and sanitize the affected fields once;
2. regenerate the complete Song DNA once under stricter instructions if needed;
3. use the approved context-only fallback if the full-source path remains unsafe;
4. otherwise stop without image generation or credit capture.

The raw source is discarded immediately after the sanitizer finishes or the request fails.

## Original Visual Narrative Plan

The planner receives approved Song DNA, portrait count, orientation, user instructions and product configuration. It does not receive lyrics, artist/song metadata or an album-art reference.

It produces:

- one instantly readable dramatic moment;
- protagonist roles and interaction;
- foreground, middle ground and background assignments;
- environment and atmospheric storytelling;
- motivated action or stillness;
- emotional focal point;
- safe symbolic props;
- enough negative space for the selected orientation when useful.

The scene may be adventurous, dramatic and artistic. It must remain original rather than illustrating a distinctive lyric sequence.

## Portrait Integration Plan

### One portrait

- The uploaded person is the primary protagonist.
- Preserve recognizable facial geometry, skin tone, hair, age presentation and other stable identity cues.
- Clothing and environment may change freely unless the user asks otherwise.
- Prefer waist-up or full-body environmental storytelling over passport or studio framing.

### Two portraits

- Both uploaded people are equal primary characters.
- Preserve each identity separately without blending their features.
- Keep both clearly visible and appropriately lit.
- Create meaningful interaction through pose, gaze, shared action or environmental relationship.
- Avoid static side-by-side placement unless the visual concept specifically benefits from it.

### Portrait safety language

The prompt distinguishes authorized uploaded people from prohibited third-party likenesses. It must never use V1's contradictory instruction to preserve the uploaded identity while also forbidding all recognizable real people.

Retries may not silently remove, replace, merge or minimize required portraits.

## V2 StyleMap

Preserve the V1 structured style architecture:

```text
STYLE
MEDIUM
COLOR
LIGHTING
SURFACE
MOTION
COMPOSITION
MOOD
ATMOSPHERE
DETAIL
INFLUENCE
TYPOGRAPHY
SPECIAL
AVOID
```

V2 rules:

- The user's curated style is the dominant aesthetic.
- Song DNA may influence emotion, palette and symbolism without overriding the selected style.
- `INFLUENCE` uses movements, periods, craft traditions, technical methods and general schools, not requests to imitate a living artist.
- Static style records are versioned and keep revision history.
- The job snapshots the selected style revision so later admin edits do not alter submitted or completed work.
- Dynamic analysis of a band's official album art is disabled for the general-song path.
- Band-specific visual identity is available only through a direct partner agreement that authorizes the relevant assets and usage.
- All 52 recovered styles remain admin-accessible; only the approved 15 are active for the first build.

## User instructions

Preserve V1's soft-integration principle.

Special instructions may refine:

- weather;
- location category;
- mood;
- palette;
- camera distance;
- clothing direction;
- props;
- relationship or activity;
- original permitted text when the no-text option is not selected.

They may not override portrait identity, source rights, prohibited content, selected orientation, style safety, text policy or account permissions. Conflicting instructions are safely adapted when possible and rejected when adaptation would materially change the request.

## Versioned prompt compiler

The compiler assembles the image request in this order:

1. outcome and scene mission;
2. approved Song DNA essence and emotional direction;
3. original Visual Narrative Plan;
4. Portrait Integration Plan;
5. selected StyleMap;
6. orientation and framing;
7. user instructions;
8. text policy;
9. originality, likeness, branding and content constraints;
10. provider-specific syntax added only inside the provider adapter.

The final image prompt must not contain raw lyrics, song title, artist/band name, album title, source URL, lyric-provider text or unauthorized promotional-art references.

Do not embed V1 branding text inside the generated artwork. Product branding may be applied later through a controlled post-processing layer if the owners choose it.

## Text policy

- `No text in image` selected: no readable text, lettering, signatures or typographic marks.
- Setting not selected: intentional text is allowed only when it is original, user-owned, licensed or verified public-domain material.
- Never include copyrighted lyrics, song titles used as artwork branding, artist/band names, album titles, logos or implied endorsements.
- Accidental signs, gibberish, watermarks and provider marks are delivery failures.

## Provider routing

Song interpretation, sanitization and prompt compilation are provider-neutral contracts. Image models are selected by benchmarked portrait fidelity, couples performance, style strength, quality, cost, latency, privacy and reliability.

The user selects low, medium or high quality. The system may route different styles to different models while keeping that routing hidden and replaceable.

## Provider-specific prompt compilation

V1 used one Gemini-centered multimodal prompt. Its long hierarchy, inline portrait handling, temperature changes, safety wording and fallback mutations were responses to Gemini's particular behavior. V2 preserves the creative intent but does not treat that prompt string as universal.

Provider neutrality means **equivalent intent**, not identical wording.

The canonical creative package contains:

- approved Song DNA;
- Visual Narrative Plan;
- Portrait Integration Plan;
- StyleMap revision;
- orientation;
- quality tier;
- lawful user instructions;
- text policy;
- originality and content constraints.

Each provider adapter compiles that package into the format proven most effective for its selected model.

Every provider/model capability profile records:

- supported reference-image count and ordering;
- identity or character-reference controls;
- prompt-length and instruction-following behavior;
- whether negative prompts are supported;
- native aspect-ratio, resolution and quality controls;
- seed or reproducibility controls;
- image-edit versus generation mode;
- text-rendering strengths and weaknesses;
- safety-block behavior and error categories;
- response format, latency and retry guidance;
- provider-side storage, privacy and training settings.

Adapter rules:

- use native API controls for aspect ratio, resolution, quality, seed and reference strength instead of repeating them as prose when possible;
- keep prompts as short as the model allows without losing required meaning;
- use the model's preferred ordering and syntax for portraits, scene, style and exclusions;
- avoid Gemini-specific camera, safety or temperature language unless benchmarks prove it helps that model;
- never send unsupported fields and hope the model ignores them;
- never expose provider syntax or model names as customer-facing product behavior;
- version the adapter and model-specific prompt template independently from Song DNA and styles;
- preserve all product rules even when their technical expression differs by provider.

The same model may use different optimized templates for one portrait, two portraits, text-allowed, no-text, realistic and illustrative routes when benchmarks justify the separation.

### Provider comparison method

Use two complementary comparisons:

1. **Canonical baseline:** equivalent minimal instructions across providers to reveal native strengths and weaknesses.
2. **Optimized route:** the best validated provider-specific compiler for the same canonical creative package.

Product routing decisions use the optimized-route results. The canonical baseline is diagnostic only. Record both template versions, model versions, costs, latency and owner scores.

## Controlled retry policy

Retries preserve priorities in this order:

1. uploaded identity and required portrait count;
2. song's emotional truth;
3. original narrative readability;
4. selected visual style;
5. orientation and composition;
6. lawful user instructions;
7. provider-specific flourishes.

Retry sequence:

1. classify the provider response as transport, capacity, format, safety, reference, quality or unknown failure;
2. apply the selected model's validated retry action rather than a universal fallback chain;
3. retry the same model with a semantically equivalent prompt only when its capability profile supports that response;
4. route to a comparable-quality model and compile a fresh provider-specific request from the unchanged canonical package;
5. make a restrained composition or safety adjustment while preserving portraits and meaning when the original request itself caused the failure;
6. fail honestly and return the reserved credits if no usable image can be delivered.

Never:

- remove portraits to obtain a technically successful image;
- switch to abstract or environment-only art when portraits were required;
- lower the purchased quality tier without disclosure and owner-approved policy;
- remove the selected style silently;
- strip the no-text requirement;
- generate repeated paid attempts without the configured provider-call and spending ceiling.

## Output evaluation

Before delivery, evaluate:

- successful decoding and required dimensions;
- selected orientation;
- correct number of required people;
- recognizable identity for each uploaded person;
- no unintended feature blending;
- scene readability and emotional relevance;
- selected-style compliance;
- text-setting compliance using OCR and visual inspection;
- absence of lyrics, song/artist/album names and provider watermarks;
- absence of unauthorized logos, public figures and obvious promotional-art imitation;
- absence of severe image defects or unsafe content.

During development, CuBiX Meow and Brut perform human review for lyric-scene resemblance, album/video similarity, visual quality and emotional effectiveness. Automated checks support that decision but do not replace it.

## Stored creative artifacts

The application may retain:

- matched song and artist metadata, subject to the approved display policy;
- source eligibility and match metadata;
- approved Song DNA;
- Visual Narrative Plan;
- Portrait Integration Plan;
- StyleMap identifier and immutable revision;
- prompt-compiler version;
- provider/model route, attempts, cost and duration;
- output-evaluation results;
- final image and approved gallery metadata.

It must never retain raw lyrics, lyric excerpts, reconstructive summaries or the raw analysis-provider request/response containing lyrics.

## Prompt and style administration

- Prompts, schemas, sanitizers and styles have immutable versions.
- Admin edits create a new revision rather than rewriting historical behavior.
- A submitted job snapshots every component version.
- Owners can activate, deactivate, compare and roll back revisions.
- Secrets and raw lyrics are never visible in admin.
- Test results are traceable to the exact component and model versions used.

## Quality-preservation benchmark

Before creative-engine approval:

1. Select a small set of the strongest V1 output images as visual-quality anchors.
2. Run the fixed 12-song core for controlled analysis comparisons.
3. Use the broad rotating private song corpus to expose genre, mood, era, language and narrative weaknesses.
4. Compare V1-style DNA against V2 sanitized DNA for emotional specificity and visual richness.
5. Compare image models using identical portraits, Song DNA, style and orientation.
6. Conduct owner review without provider/model labels when practical.
7. Reject any safety revision that materially flattens or genericizes the results.
8. Approve only when V2 equals or improves V1's strongest qualities while passing the new storage, leakage, portrait and originality controls.

The final engine must also pass the 90-generation Acceptance Test Contract, including the 90 percent usable identity-preserving threshold.

## V1-to-V2 correction map

| V1 behavior | V2 decision |
| --- | --- |
| Strong 12-field Song DNA | Preserve, refine and expand |
| Free-form lyric-informed narrative | Replace with generic archetype plus newly invented visual moment |
| Prompt-only no-quote instruction | Add independent overlap, paraphrase and narrative leakage sanitizer |
| Lyrics in queue/render storage | Prohibit all raw-lyric persistence |
| Song and band names in image prompt | Keep outside final image prompt |
| Default cinematic photoreal mission | Let selected StyleMap dominate |
| Strong one/two-person portrait directives | Preserve and strengthen |
| `No recognizable real people` contradiction | Permit authorized portraits; prohibit unauthorized public figures and third parties |
| No-text plus generated branding conflict | Separate text policy and optional post-processing branding |
| Album-art-derived dynamic style | Restrict to explicitly authorized artist partnerships |
| Mutable database style prompts | Add immutable revisions and job snapshots |
| Parallel worker lost JSON repair | Require strict schema validation and controlled regeneration |
| Retry silently removed portraits | Preserve portraits on every valid retry or fail honestly |
| Corrupted or model-specific fallback strings | Use tested, versioned provider adapters and retry templates |
| Prompt claims output is commercially safe | Use factual constraints, source eligibility, evaluation and legal gates |

## Approval status

CuBiX Meow and Brut approved this contract for Private Development Build 1 on 2026-08-28. Provider routing, benchmark outcomes and launch eligibility remain provisional until the approved testing is complete.
