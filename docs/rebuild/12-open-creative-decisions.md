# 12 — Open Creative Decisions

**Purpose:** unresolved product questions revealed by the creative-engine audit.  
**Status:** awaiting owner decisions. No implementation implied.

Each item is an **Open Question** unless marked otherwise.

---

## How to use this register

For each question:

1. read the V1 evidence summary;
2. choose a V2 direction;
3. record the decision in this file (or a future ADR) with date/owner;
4. only then allow implementation work that depends on it.

---

## D1 — What does “Dynamic Band Lore” mean in V2?

**V1 evidence:** Marketing uses “Dynamic Band Lore Engine™”. Implementation is Song DNA + cinematic image generation + optional visual StyleMap. No separate song lore bible/story engine exists in the queue path.

**Options:**

- A. Keep the brand name; redefine it as Song DNA + visuals (honest rename in docs only).
- B. Build a real lore subsystem (characters, myths, continuity) separate from visual style.
- C. Retire “lore” language; market Song DNA + Artist Visual Identity separately.

**Why it matters:** changes information architecture, prompts, storage, and user-facing explanations.

---

## D2 — Should Dynamic Artist Style run by default?

**V1 evidence:** UI default label implies Dynamic Lore/Style, but submits empty `image_style`, so StyleMap never runs. Worker requires `ANALYZE_BAND_STYLE`.

**Options:**

- A. Default on for all generations.
- B. Default on only for paid tiers.
- C. Opt-in advanced control.
- D. Replace with a non-album-cover identity system.

---

## D3 — Artist identity source of truth

**V1 evidence:** Dynamic style asks the model to recall/infer album-cover artists and aesthetics from knowledge, without fetching the cover image.

**Options:**

- A. Model knowledge only (V1-like).
- B. Optional user-uploaded reference moodboard.
- C. Licensed/metadata provider artwork analysis.
- D. Fully original style, ignore artist catalog aesthetics.
- E. Hybrid with explicit user choice.

**Risk notes:** imitation, hallucination of cover artists, trademark adjacency.

---

## D4 — Song DNA scope

**V1 evidence:** DNA mixes meaning + cinematography fields; all fields dump into one image prompt.

**Options:**

- A. Preserve V1 12-field schema.
- B. Adopt Song DNA V2 (meaning-focused) + later cinematography plan (`11-...`).
- C. Minimal DNA (essence/themes/mood only) for faster cheap path.

---

## D5 — Literal vs metaphorical imagery

**V1 evidence:** design intent is metaphorical cinematic beat; no user control for literalism.

**Options:**

- A. Always metaphor-led.
- B. User dial: literal ↔ metaphor.
- C. Automatic based on sensitivity/genre heuristics.

---

## D6 — Portrait promise & fallback honesty

**V1 evidence:** portraits removed at retry attempt 4; success can return without telling the user identity was dropped.

**Options:**

- A. Never drop portraits; fail instead.
- B. Drop only after user confirmation.
- C. Allow drop but label result “identity not preserved”.
- D. Offer dual outputs: identity attempt + safe alternate.

**Related:** how “put me in the song” is guaranteed emotionally, not just technically.

---

## D7 — One portrait vs ensemble casting

**V1 evidence:** one or two portraits; ordered as Character 1/2; no named roles beyond protagonist/co-protagonist.

**Options:**

- A. Keep 1–2 portraits.
- B. Support more cast slots with roles.
- C. Separate “self as extra” vs “self as hero” modes.

---

## D8 — Branding in-image vs post-process

**V1 evidence:** prompt both forbids visible text and requires Arcana/YouAreTheSongNow captions.

**Options:**

- A. Post-process compositor watermark only.
- B. Generative branding with no global no-text ban.
- C. No branding in free preview; branding on export variants.
- D. User-selectable clean / branded outputs.

---

## D9 — Lyrics handling architecture

**V1 evidence:** full lyrics stored on queue/renders; sent to Gemini (16k truncate); prompts forbid quoting/rendering lyric text.

**Options (product/legal review required):**

- A. Transient process → store DNA only.
- B. Store lyrics with retention limits + user delete.
- C. Licensed lyric providers only.
- D. User-pasted lyrics only with explicit terms acknowledgment.
- E. Hybrid.

**Not a legal conclusion.**

---

## D10 — Should V2 show intermediate artifacts to users?

**V1 evidence:** users do not see Song DNA or StyleMap; only final image (and logs server-side).

**Options:**

- A. Hidden intermediates (V1-like).
- B. Show editable Song DNA before render.
- C. Show Narrative Plan for approval.
- D. Advanced mode reveals all artifacts.

---

## D11 — Story / lore text product

**V1 evidence:** story credits and FAQ mentions exist; no wired song story generator consuming those credits. `narrative` in DNA is for images only. Separate youtube/animal tools are not Arcana poster lore.

**Options:**

- A. Image-only product.
- B. Optional short lore paragraph beside image.
- C. Full multi-scene story mode using story credits.
- D. Defer until after image vertical slice.

---

## D12 — Retry philosophy

**V1 evidence:** temperature nudges → strip branding → drop portraits → widen → abstract → landscape.

**Options:**

- A. Preserve identity longest (`11` ladder).
- B. Preserve safety/completion rate over identity (V1-like).
- C. Configurable per plan.
- D. Human-in-the-loop on degraded attempts.

---

## D13 — Static style library

**V1 evidence:** DB-managed `arcana_styles` with admin CRUD; legacy hardcoded catalogs also exist; plan-gated in main generator.

**Options:**

- A. Keep curated static styles + dynamic identity.
- B. Dynamic only.
- C. Static only.
- D. User-defined style presets.

---

## D14 — Provider & model strategy

**V1 evidence:** hardwired Gemini text/image model IDs; older workers include search/repair hacks.

**Options:**

- A. Single provider with adapters ready.
- B. Multi-provider from day one.
- C. Text planner on one model family; image on another.

---

## D15 — Evaluation / quality gates

**V1 evidence:** success = bytes returned; no face-match or prompt-adherence judge.

**Options:**

- A. No evaluators initially.
- B. Safety + face-presence checks for portrait mode.
- C. Full judge stack (text detection, identity, narrative fit).

---

## Decision log

| ID | Decision | Owner | Date | Notes |
|---|---|---|---|---|
| — | — | — | — | none yet |

---

## Recommended next design workshop

Order for owner discussion:

1. D1 lore meaning  
2. D6 portrait promise  
3. D9 lyrics architecture  
4. D2/D3 artist style defaults  
5. D8 branding  
6. D10 user-visible intermediates  
7. D11 story product scope  

After those, refine `11-v2-prompt-refinement-plan.md` into accepted contracts and only then lift the relevant slice of the build freeze.
