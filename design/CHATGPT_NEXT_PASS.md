# YouAreTheSongNow — ChatGPT Next Design Pass

**Round:** 002 preparation  
**Written by:** ChatGPT  
**Date:** 2026-08-30  
**Status:** Ready for Cursor

## Overall assessment

Pass 1 is moving in the right direction. Keep the mobile-app-first architecture, bottom navigation, compact desktop rail, graphite/midnight foundation, amber stage-light accent, indigo atmosphere, square artwork/portrait treatment, and Flutter-portable tokens.

The biggest remaining risk is not the palette; it is the **visual story**. Couple-focused hero photography, conventional form composition, and sparse dark desktop space can still make the product read as membership/dating or generic SaaS even when the chrome is improved.

The product should read immediately as:

**song → imagination → visual journey → collection**

The UI should feel like a premium creative instrument wrapped around artwork, not a membership funnel.

---

## Answers to Cursor's questions

### 1. Palette

**Keep amber-on-graphite with indigo haze.** This is a strong direction and is distinct from the old dating-site feel.

Do not solve the remaining dating association by changing the palette again. The larger source of that association is couple-centric hero imagery and membership-style composition.

Use amber as emitted light / selection / primary action, not as a large flat brand fill. Indigo should remain atmospheric. Let generated artwork provide most of the vivid color.

### 2. Guest home

Move the guest experience toward an **app-home / launchpad**, not a conventional marketing landing page.

It can still explain the product, but the first screen should feel like opening an app and being invited to begin a journey.

Preferred hierarchy:

1. compact app chrome / brand
2. one strong emotional statement
3. a prominent **Start with a song** creation entry point
4. visual examples of song-inspired adventures
5. concise explanation / trust / membership information later

Avoid making an intimate couple photograph the primary identity of the product. If people appear, favor adventure, imagination, movement, exploration, performance, travel, surreal/cinematic environments, or a single protagonist entering a world.

The site should make sense even if the hero image contained **no romantic pair at all**.

### 3. Create flow

Yes, based on the description it is probably still somewhat form-like. Do **not** remove fields or alter their behavior during this pass.

Make the existing fields feel like controls in a creative instrument by changing presentation:

- Introduce a persistent **session header** at the top of Create: song/artist or “Choose your song” state, small artwork/placeholder, and a subtle session/progress indicator.
- Treat `01 The song`, `02 The people`, and `03 The direction` as **movements in one creative session**, not three form sections.
- Give the current/primary movement more visual emphasis while keeping all fields available and preserving behavior.
- Reduce boxed-panel appearance. Prefer open composition, hairlines, tonal separation, artwork, and spacing.
- Make selected people/styles/directions resemble creative material being assembled into a piece rather than account/profile data.
- Keep the final generation action visually unmistakable and emotionally rewarding.
- On desktop, let the right-side summary feel like an **album/session sleeve or creative board**, not a checkout/order summary.

Do not add new application logic solely to create an accordion or wizard in this pass. We can consider real progressive disclosure later when we are ready to touch interaction behavior.

### 4. Desktop rail

**Keep the compact ~88px rail for now.** Do not widen it into a dashboard sidebar.

The narrow rail reinforces that desktop is an expanded version of the app rather than a separate admin-style website. Labels can remain compact with icons. We can revisit after screenshots.

### 5. First custom assets

Priority for maximum global impact:

1. **`app-atmosphere-haze`** — affects the entire product and should remove the “flat dark website” feeling.
2. **`launch-mark-tile`** — establishes a recognizable premium app identity in chrome and future Flutter/splash work.

Next priority after those:

3. `creative-session-backdrop`
4. `empty-collection-still`
5. `paywall-world-preview`

Do not spend design time polishing the paywall before the Create/home identity is right.

---

# PASS 2 IMPLEMENTATION INSTRUCTIONS

## A. Preserve the successful foundation

Do not regress:

- mobile bottom tabs
- compact desktop rail
- Flutter-portable tokens
- Instrument Serif + DM Sans pairing unless a concrete rendering problem appears
- graphite/midnight + amber + indigo system
- square album-style artwork/portrait crops
- artwork-first reveal/gallery philosophy
- 48px-ish mobile targets and safe-area handling
- existing application functionality

## B. Make Home feel like opening an app

Refine guest Home so the top viewport resembles a premium creative app launch experience rather than a marketing/dating landing page.

Keep the existing routes/actions, but visually prioritize **starting a song adventure**.

Reduce romance-coded imagery as the defining visual motif. Existing couple imagery may remain lower in examples if it demonstrates a valid output, but it should not define the product brand.

The strongest visual story should be personal imagination and adventure.

## C. Refine Create into a creative instrument

Without changing field behavior, redesign composition around the concept of a **Creative Session**.

Add/polish a visual session header and make the three movements feel like one composition building toward generation.

On phone:

- strong song/session identity near top
- calm vertical rhythm
- large tap-friendly creative choices
- minimal framing
- final create action should feel like the culmination of the session

On desktop:

- main creative workspace + compact session board
- use negative space intentionally
- avoid large dead dark areas
- do not turn it into a dashboard

## D. Remove remaining dating/membership cues

Audit especially:

- hero imagery
- auth/signup surfaces
- people selectors
- paywall presentation
- CTA language styling
- circular or profile-like imagery
- centered membership-funnel compositions

Do not remove legitimate user/account functionality. Change only visual presentation in this round.

## E. Owner navigation

For the private current build, functionality can remain.

Visually de-emphasize `Owner` so it does not appear to be a primary consumer destination. On mobile, if existing structure allows presentation-only differentiation safely, treat it as an operational/private item rather than equal product identity. Do not change permissions/routes/logic.

## F. Asset integration readiness

Keep the existing CSS hooks ready for the first assets. Do not invent substitute decorative graphics that will have to be removed once the generated assets arrive.

---

# VISUAL REVIEW PROTOCOL — ADD THIS TO THE HANDOFF WORKFLOW

This will materially improve ChatGPT ↔ Cursor iteration.

For every significant design round, Cursor should create or capture review screenshots when its environment allows it.

Create a folder such as:

`design/review/round-002/`

Preferred captures:

- `home-mobile-390.png`
- `create-mobile-390.png`
- `gallery-mobile-390.png`
- `home-desktop-1440.png`
- `create-desktop-1440.png`
- `gallery-desktop-1440.png`

If authentication/data prevents a screen from being captured, document that instead of fabricating a state.

Also add `design/review/round-002/README.md` containing:

- commit SHA / branch
- viewport dimensions
- logged-in/logged-out state
- what changed
- what Cursor wants ChatGPT to judge visually

These screenshots should be committed with the round when practical. This lets ChatGPT review the actual visual result instead of relying only on prose.

If automated screenshots are not possible, ask the human for only the smallest necessary set of screenshots.

---

# HANDOFF WORKFLOW IMPROVEMENT

To avoid merge conflicts and the need for ChatGPT to rewrite the entire growing handoff file, use two roles:

- `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` = **canonical history**, maintained/consolidated by Cursor after each round.
- `design/CHATGPT_NEXT_PASS.md` = **ChatGPT inbox**, written by ChatGPT with the next instructions.

Cursor should read `CHATGPT_NEXT_PASS.md`, implement it, then copy/summarize the relevant instructions and outcome into the canonical handoff Round History. Cursor may leave `CHATGPT_NEXT_PASS.md` in place and mark it consumed, or replace its contents during the next cycle if asked.

This prevents both agents from editing the same large file simultaneously and makes the mobile workflow much more reliable.

Add this protocol to the canonical handoff under Operating Rules.

---

# WHAT CURSOR SHOULD REPORT BACK

After Pass 2:

1. Update `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md`.
2. Record exactly what was changed and what was intentionally left untouched.
3. Add/refresh screenshot references.
4. Update screen status/backlog.
5. Note whether each remaining dating/SaaS cue was reduced.
6. List any asset specs that changed after the new composition.
7. Ask no more than 3 high-value design questions for the next round.
8. Confirm no backend/API/database/auth/generation logic was intentionally changed.
9. Commit/push the completed round so ChatGPT can read it through GitHub.

Do not start Pass 3 speculatively. Stop for visual review.
