# YouAreTheSongNow — ChatGPT Next Design Pass

**Round:** 003  
**Written by:** ChatGPT  
**Date:** 2026-08-30  
**Status:** Ready for Cursor

## Round 002 review

Pass 2 is a clear improvement and the core direction should stay intact.

Keep:

- graphite / midnight foundation
- amber stage-light accent
- indigo atmosphere
- Instrument Serif + DM Sans
- mobile bottom navigation
- compact desktop rail
- square album-style portrait/gallery crops
- guest Home as an app launchpad
- Create framed as a Creative Session
- session board concept
- solo/adventure imagery as the primary brand story
- Flutter-portable design tokens

The user confirmed the existing mobile bottom nav is acceptable for now. **Do not apply the earlier navigation hotfix and do not redesign the nav in this pass.**

The main remaining visual issue is Create: it is improved, but it still reads partly like a polished form because the vertical sequence of labels + rectangular fields is visually dominant. The next pass should improve composition and hierarchy without hiding/removing fields or changing their behavior.

The next functional priority is also now explicit: **users must be able to delete uploaded portraits.** This is authorized functional work and should be implemented as a focused feature in this round.

---

# PASS 3 GOALS

## 1. Push Create one step further toward a creative instrument

Do not convert it into a wizard/accordion and do not remove fields.

Instead, improve the visual rhythm so the controls feel like material being assembled into a creative session.

### Song movement

The current artist + song fields can remain, but reduce the generic-form feeling by making the section read more like a track setup surface.

Preferred treatment:

- session/track identity remains visible at the top
- use stronger typographic hierarchy and tighter grouping between Artist and Song
- allow the fields to feel like one composed "track source" rather than two unrelated form rows
- keep `Find my song` as the clear action, but make it visually feel like a creative retrieval action, not a signup CTA

Do not change song lookup behavior.

### People movement

This is the biggest opportunity to remove dating/profile associations.

Treat uploaded portraits as **source material / cast / visual ingredients** rather than profiles.

- square or slightly rounded artwork-style portrait tiles
- selected state should feel like inclusion in the session, not matching/favoriting
- delete affordance must be clear but visually secondary
- no heart/profile/dating iconography
- portrait grid should feel like a creative tray or contact sheet

### Direction movement

Style/direction choices should feel like visual treatments, moods, worlds, or creative controls.

- reduce generic checkbox/radio styling where existing markup permits presentation-only changes
- use tactile selection states
- use compact labels and visual hierarchy
- avoid turning each choice into a giant card

### Generate action

The final generation action should feel like the culmination of the assembled session.

- keep one strong primary action
- visually tie it to the session board / selected inputs
- do not add extra confirmation steps

---

## 2. Implement portrait deletion

Implement the functionality requested in:

`design/CHATGPT_FEATURE_PORTRAIT_DELETE.md`

This is explicitly authorized functional work.

Requirements:

- a user can delete one of their own uploaded portraits
- deletion must be authorized to that user
- remove the portrait record from persistent storage
- remove/delete the associated uploaded file when safe and applicable
- if the deleted portrait is currently selected in Create, remove it from the current UI/session state cleanly
- provide a confirmation step appropriate to the current app style
- provide clear success/failure feedback
- the delete control must work well on mobile and meet touch-target requirements
- deletion must not affect another user's portrait
- do not alter unrelated upload/generation behavior

Prefer a compact secondary overflow/delete control on the portrait tile rather than a large destructive button that dominates the visual design.

Test at minimum:

1. delete owned portrait succeeds
2. deleted portrait disappears after refresh
3. underlying file is handled correctly
4. attempt to delete another user's portrait is rejected
5. deleting a selected portrait removes it from the active Create selection
6. mobile control is tappable and confirmation is usable

Document exact implementation and tests in the handoff.

---

## 3. Keep Home direction; only polish, do not reinvent

The move away from couple-first branding was correct.

Do not perform another structural Home redesign in this pass.

Only make small consistency fixes if needed while working on shared components.

The brand story should remain:

**song → imagination → visual journey → collection**

---

## 4. Asset readiness

Keep these as the next custom asset priorities:

1. `app-atmosphere-haze`
2. `launch-mark-tile`
3. `creative-session-backdrop`
4. `empty-collection-still`

Do not block Pass 3 on these assets.

Do not create throwaway pseudo-art that will have to be removed later.

If Pass 3 changes any required crop, safe area, or composition, update `design/ASSET_REQUESTS.md` before the next handoff.

---

## 5. Navigation — leave it alone

The user reviewed the mobile bottom nav and said it is fine for now.

Do not implement `design/CHATGPT_HOTFIX_NAV.md` in this pass.

Keep the current navigation model unless a genuine bug is discovered.

---

# REVIEW PROTOCOL FOR ROUND 003

Create:

`design/review/round-003/`

Preferred screenshots:

- `create-mobile-390-top.png`
- `create-mobile-390-people.png`
- `create-mobile-390-direction.png`
- `create-desktop-1440.png`
- `portraits-mobile-delete.png`
- `portraits-desktop-delete.png`

If practical, include a screenshot of the portrait delete confirmation state.

Add a README with:

- commit SHA
- viewport/state
- what changed visually in Create
- portrait deletion implementation summary
- what ChatGPT should judge next

---

# REPORT BACK

Update `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` with:

1. Pass 3 design changes
2. exact portrait deletion implementation
3. files changed
4. tests performed and results
5. screenshot references
6. any updated asset specs
7. no more than 3 high-value questions
8. confirmation that no unrelated backend/API/database/auth/generation logic changed

Commit/push everything and stop for review.

Do not begin Pass 4 speculatively.
