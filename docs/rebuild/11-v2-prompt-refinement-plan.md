# 11 — V2 Prompt Refinement Plan

**Status:** PROPOSED — NOT IMPLEMENTED  
**Purpose:** research recommendations for a cleaner Arcana creative engine, based on V1 forensic evidence.  
**Does not authorize building application code.**

Evidence labels below mark proposal confidence relative to V1 findings.

---

## Design thesis — Proposed

V1 asks one giant image prompt to simultaneously:

- understand the song;
- decide what it means;
- choose a scene;
- integrate the user;
- choose artist visual style;
- choose cinematography;
- satisfy safety;
- render branding text.

That overload creates contradictions (portraits vs “no real people”, branding vs “no text”, StyleMap vs Song DNA) and makes retries destructive.

V2 should compile images from **explicit intermediate artifacts** with ranked priorities.

---

## Recommended pipeline — Proposed

```
Source Context
     ↓
Song Interpretation
     ↓
Song DNA V2
     ↓
Visual Narrative Plan
     ↓
Artist / Band Visual Identity
     ↓
User Identity / Portrait Plan
     ↓
Scene Composition Spec
     ↓
Image Prompt Compiler
     ↓
Provider Adapter
     ↓
Quality / Safety Evaluation
     ↓
Controlled Retry (priority-preserving)
```

This improves on the example structure in the audit brief by making **Scene Composition** an explicit artifact after portrait planning, so identity and scene are negotiated before prompt compilation.

---

## Priority contract — Proposed

Every generation should carry a ranked priority list, defaulting to:

1. User identity fidelity (if portrait mode on)
2. Song meaning / emotional truth
3. Scene readability
4. Artist visual identity
5. Cinematography polish
6. Branding (if enabled)
7. Provider quirks / style flourishes

Retries may demote lower priorities before higher ones. V1 demotes (1) at attempt 4 while keeping lower stylistic residue — invert that.

---

## Artifact 1 — Song Interpretation prompt

### Responsibility

Read source context and produce a **meaning brief** without cinematography or casting.

### Inputs

- artist/band
- song title
- allowed lyric source (transient)
- optional user notes (non-scene)

### Output schema (conceptual)

```json
{
  "plain_language_summary": "string",
  "emotional_arc": ["string"],
  "central_conflict_or_desire": "string",
  "key_motifs": ["string"],
  "literal_anchors": ["string"],
  "metaphorical_reading": "string",
  "sensitivity_flags": ["string"]
}
```

### Must NOT do

- invent camera lenses;
- invent color grades;
- cast the user;
- name album designers;
- quote lyrics verbatim into durable storage fields.

### Why separate

V1 Song DNA mixes meaning fields with camera/palette/texture. Separating interpretation prevents decorative cinematography from pretending to be song understanding.

---

## Artifact 2 — Song DNA V2 prompt

### Responsibility

Translate Song Interpretation into a **stable creative genome** for visuals and future modes (image, story, video).

### Inputs

- Song Interpretation artifact
- product mode (poster / scene / storyboard)

### Output schema (conceptual)

```json
{
  "version": "dna.v2",
  "essence": "string",
  "themes": ["string"],
  "symbols": ["string"],
  "mood": ["string"],
  "setting_candidates": [{"place":"string","era":"string","why":"string"}],
  "visual_metaphors": ["string"],
  "motion_ideas": ["string"],
  "palette_direction": ["string"],
  "do_not_depict": ["string"],
  "confidence": 0.0
}
```

### Must NOT do

- finalize one locked camera package;
- bind to a specific provider prompt dialect;
- include user biometrics;
- include branding instructions.

### Why separate

Preserves V1’s best idea (structured DNA) while removing fields that belong to later planning. Enables reuse across image/story products.

### Preserve from V1

themes, symbols, mood, visual metaphors, essence/summary, narrative seed (via setting candidates + metaphors).

### Remove / relocate from V1

`camera`, `composition`, `texture`, `lighting`, `influences` → later artifacts; influences especially should not freely name living artists/album layouts without policy.

---

## Artifact 3 — Dynamic Artist Visual Identity prompt

### Responsibility

Produce an **artist/band visual identity brief** distinct from song meaning.

### Inputs

- artist/band
- song release context (optional metadata)
- policy mode: `inspired_by_visual_language` vs `neutral_original`
- optional Song DNA essence (for compatibility check only)

### Output schema (conceptual)

```json
{
  "identity_mode": "inspired_by_visual_language",
  "medium": "string",
  "palette_behavior": "string",
  "lighting_behavior": "string",
  "materials_surfaces": ["string"],
  "composition_habits": ["string"],
  "motion_energy": "string",
  "mythology_tone": "string",
  "avoid": ["string"],
  "imitation_risks": ["string"]
}
```

### Must NOT do

- copy album layouts;
- request trademarked logos/wordmarks;
- claim false cover-artist attribution as fact without provenance;
- override Song DNA meaning.

### Why separate

V1 evidence shows StyleMap is a **visual-style generator**, while marketing called it lore. V2 should name it honestly. If owners later want true lore, that is a different artifact (characters, myths, continuity), not StyleMap.

### Relation to V1 ANALYZE_BAND_STYLE

Replaces album-cover-knowledge StyleMap with a controlled identity brief. Album-cover imitation should be opt-in and policy-reviewed.

---

## Artifact 4 — Visual Narrative / Scene Planner prompt

### Responsibility

Choose **one depictable moment** and explain how song meaning becomes a readable image.

### Inputs

- Song DNA V2
- Artist Visual Identity
- aspect ratio
- mode (single poster vs sequence)

### Output schema (conceptual)

```json
{
  "chosen_moment": "string",
  "why_this_moment": "string",
  "literal_vs_metaphor_balance": "metaphor_led|balanced|literal_led",
  "setting": "string",
  "action": "string",
  "emotional_read": "string",
  "focal_hierarchy": ["primary","secondary","environment"],
  "props_symbols": ["string"],
  "rejected_alternatives": ["string"]
}
```

### Must NOT do

- write final provider prompt prose;
- decide portrait crop/identity matching details;
- add safety legalese walls of text.

### Why separate

This is the missing V1 stage. V1 stuffed “Narrative Moment” inside DNA and hoped the image model would stage it while also doing identity/style/safety.

---

## Artifact 5 — Portrait Integration planner

### Responsibility

Plan how the user enters the song **without** silently abandoning them.

### Inputs

- portrait count/refs
- Visual Narrative Plan
- Song DNA essence
- user consent flags

### Output schema (conceptual)

```json
{
  "mode": "solo_protagonist|duo|none",
  "role_in_story": "string",
  "wardrobe_direction": "string",
  "interaction": "string",
  "camera_distance": "medium|waist_up|full|wide",
  "identity_constraints": ["exact_face","keep_age","keep_hair", "..."],
  "conflict_resolutions": ["string"],
  "fallback_policy": "ask_user|preserve_silhouette|symbolize_absence"
}
```

### Must NOT do

- invent a different person’s face;
- require studio headshot framing;
- authorize dropping identity without recording a user-visible reason.

### Why separate

V1’s CRITICAL portrait text is strong, but retries discard portraits at attempt 4 while still returning success. V2 needs an explicit identity contract.

---

## Artifact 6 — Final image prompt compiler

### Responsibility

Compile prior artifacts into a **provider-facing prompt** with explicit hierarchy.

### Inputs

- all artifacts above
- branding policy
- safety policy pack
- provider capabilities

### Output

- `prompt_text` (ordered sections)
- `reference_images` ordered
- `negative_or_avoid` structured list
- `compiler_version`
- `priority_snapshot`

### Suggested section order — Proposed

1. Priority header (what must win)
2. Scene action + setting (from Narrative Plan)
3. Subject / portrait integration
4. Song meaning constraints (DNA)
5. Artist visual identity
6. Cinematography (camera/light/palette/texture)
7. Technical (aspect, quality targets)
8. Avoid list
9. Branding (only if enabled; never contradict avoid-text without a dedicated text layer)
10. Soft user customization

### Must NOT do

- re-analyze lyrics from scratch;
- invent a new story not present in Narrative Plan;
- include raw full lyrics;
- hide priority inversions.

### Why separate

Turns V1’s brittle PHP mega-string into a testable compiler. Enables unit tests: given artifacts A, expect section ordering/constraints B.

---

## Artifact 7 — Retry / safety transformation instructions

### Responsibility

Mutate generation **while preserving higher priorities as long as possible**.

### Proposed retry ladder

| Step | Change | Preserve |
|---|---|---|
| R1 | resubmit same compile | all |
| R2 | soften contested props/symbols from sensitivity flags | identity, meaning, style |
| R3 | change camera distance / occlusions; keep face identity | identity, meaning |
| R4 | simplify wardrobe/environment complexity | identity, core meaning |
| R5 | switch metaphor balance toward safer symbols | identity if possible |
| R6 | **ask user / mark degraded** before identity drop | honesty |
| R7 | identity-free symbolic scene (only with policy + UI notice) | meaning/mood |
| R8 | abstract/landscape last resort | mood/palette |

### Must NOT do

- silently succeed without portraits when portrait mode was requested;
- destroy meaning before attempting scene/prop changes;
- use truncated/placeholder safety prompts (V1 v3 bug).

### Evaluation hooks — Proposed

After each image:

- safety status;
- optional face-presence check when portrait mode on;
- optional text/logo detection;
- optional “scene matches narrative plan” judge.

---

## Branding & text policy — Proposed

V1 contradicts itself by forbidding all text while requiring branded captions inside the same image prompt.

Options for owners (see open decisions):

1. **No in-image text**; apply branding in post-process compositor (closest to trustworthy).
2. **Dedicated text layer** via image editor, not generative prompt.
3. Generative text only in modes that explicitly allow typography, with no global “NO TEXT” contradiction.

---

## What V2 should preserve from V1 — Proposed

- staged interpretation before pixels;
- structured DNA idea;
- portrait-as-protagonist-in-world;
- soft custom instruction sandboxing;
- async job architecture;
- StyleMap-like visual brief headings (as Artist Visual Identity);
- progressive fallback philosophy (reordered).

## What V2 should remove — Proposed

- marketing/implementation mismatch for “lore”;
- single mega-prompt doing all jobs;
- unvalidated influence name-dropping;
- full lyric persistence by default;
- silent portrait dropping;
- duplicate conflicting safety/branding clauses;
- multiple divergent worker prompt sources of truth;
- animal/youtube/widget experimental paths as product assumptions.

---

## True lore system (optional future) — Open / Proposed

If owners want an actual **Dynamic Band Lore Engine**, implement it as a separate product artifact family:

- canon characters;
- motifs & myths;
- continuity bible;
- relationship graph;
- allowed visual emblems;

…produced and stored independently, then *referenced* by Scene Planner. Do not overload StyleMap to pretend it is lore.

V1 does not implement this for songs.

---

## Implementation note

These prompts are **research specifications only**. Do not wire them into application code until the build freeze is lifted and owners accept the open decisions in `12-open-creative-decisions.md`.
