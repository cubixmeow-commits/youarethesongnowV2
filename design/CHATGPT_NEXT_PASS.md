# YouAreTheSongNow — ChatGPT Next Design Pass

**Round:** 007  
**Written by:** ChatGPT  
**Date:** 2026-08-30  
**Status:** Ready for Cursor

## Round 006 assessment

Round 006 successfully converted the premium private-venue system to **near-black + platinum + sapphire/cobalt blue**.

Keep this palette direction. Do not bring brass/gold back into the core UI.

The intended read is now:

**black architectural space + platinum detail + controlled blue light + vivid user artwork**

Blue should remain controlled and structural, not neon. Platinum should read as fine cool metal / light edge, not chrome or sci-fi plating.

`php tests/run.php` reported **173 passed, 0 failed** in Round 006.

Important workflow note: `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` on main still reports Round 005 even though the Round 006 inbox is marked consumed and `design/review/round-006/` exists. In this round, repair the canonical handoff so Round 006 is fully recorded before adding Round 007 results.

---

# ROUND 007 GOAL

Do **not** begin Flutter implementation.

The Flutter handoff remains documentation only. There is substantial product, UX, visual, functional, and launch-readiness work to finish in the existing application first.

Round 007 should continue improving the current app and prepare it for custom asset integration.

---

## 1. Freeze the core palette, refine usage

Keep:

- near-black / graphite scaffold
- platinum text + hairlines
- sapphire selection/focus/atmosphere
- cobalt only for deliberate primary emphasis
- generated artwork as the main source of emotional color

Audit the current app for places where cobalt is too saturated or starts to feel gaming/neon. Reduce glow before reducing contrast.

Avoid:

- broad blue fills
- glowing blue outlines everywhere
- metallic chrome effects
- cyan
- purple-heavy AI aesthetics
- black/blue gaming UI

The target remains a **premium creative experience**, not a tech dashboard.

---

## 2. Prepare for the first real custom assets

Do not invent new temporary artwork.

Verify and, if needed, update these four priority asset hooks/specs for the platinum/blue palette:

1. `app-atmosphere-haze`
2. `launch-mark-tile`
3. `creative-session-backdrop`
4. `empty-collection-still`

The visual concepts should now be:

### app-atmosphere-haze
Near-black architectural darkness, cool platinum edge light, subtle sapphire depth, extremely restrained cobalt presence. No literal room, instruments, UI, people, or text.

### launch-mark-tile
Abstract aperture / threshold / entry mark. Graphite-black field, platinum structure/rim, sapphire inner depth, controlled cobalt core light. Must remain elegant at 28–32px and not resemble a gaming logo.

### creative-session-backdrop
Honed black stone / lacquer / architectural surface with platinum grazing light and very subtle sapphire shadow. Center must stay quiet for controls.

### empty-collection-still
Private gallery alcove / aperture waiting for its first artwork: graphite, platinum rim, small sapphire/cobalt depth light. Quiet and collectible.

Update `design/ASSET_REQUESTS.md` and `design/ASSET_INTEGRATION_MAP.md` only where the new palette requires it.

---

## 3. Current-app product polish — not another redesign

Do a targeted visual/UX audit of the current app rather than changing the overall architecture.

Priority screens:

### Create
- preserve current structure and all behavior
- improve any remaining generic-form visual moments
- ensure portrait selection/deletion remains elegant and obvious
- ensure Overview reads as creative context, not checkout/order summary
- make the final generation action premium and confident without oversized glow

### Gallery / Reveal
- strengthen finished-artwork presentation
- generated art should feel like the centerpiece of a private collection
- metadata/chrome should retreat visually
- keep empty-state asset hook ready
- do not bury useful actions

### Account / Sign-in
- bring these screens into the same platinum/blue material system without overdesigning them
- remove obvious generic SaaS styling where possible
- clarity remains more important than atmosphere

### Paywall / membership
This is now worth a focused presentation audit because it is one of the remaining places that can still make the product feel like a membership/dating funnel.

Do not change pricing, entitlement, subscription, payment, or access logic in this pass.

Presentation goals:
- invitation to continue creating, not a sales wall
- concise benefits
- premium but calm
- no dating/membership visual language
- no casino luxury
- no giant pricing-card SaaS composition if avoidable with current markup

---

## 4. Mobile-first quality audit

Because the eventual primary product is iOS/Android, audit the existing mobile experience at ~390px as the primary canvas.

Check:

- touch targets
- safe-area clearance
- bottom navigation overlap
- keyboard/input behavior where review tooling permits
- long labels wrapping
- portrait tile controls
- modal/dialog fit
- vertical rhythm
- excessive scrolling caused purely by decorative spacing
- generated-image visibility on first meaningful viewport

Do not begin Flutter. Record anything that should later become a native-specific consideration in `FLUTTER_DESIGN_HANDOFF.md`, but keep implementation in the current app.

---

## 5. Repair the handoff history

Before finishing Round 007:

1. Add Round 006 to `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` using the actual Round 006 implementation and screenshot pack.
2. Change the canonical current state from Round 005/brass to the actual platinum/blue Round 006 baseline.
3. Then add Round 007 results normally.

The canonical handoff must again be sufficient for ChatGPT to understand the current project state without reconciling multiple stale files manually.

---

# REVIEW PACK

Create `design/review/round-007/`.

Preferred screenshots:

- `home-mobile-390.png`
- `create-mobile-390-top.png`
- `create-mobile-390-people.png`
- `create-mobile-390-direction.png`
- `gallery-mobile-390.png`
- `account-mobile-390.png` if accessible
- `paywall-mobile-390.png` if accessible
- `reveal-mobile-390.png` if accessible with existing data
- `create-desktop-1440.png`
- `gallery-desktop-1440.png`

If a state cannot be reached safely with available test data, document that rather than inventing it.

README should explicitly call out:

- any cobalt saturation reduced
- remaining generic SaaS/membership cues
- mobile UX problems found
- which screens now need custom assets versus structural work

---

# FUNCTIONAL SCOPE

This is primarily design/UX polish.

Do not change unrelated backend/API/database/auth/generation/payment/subscription logic.

Portrait deletion remains supported and must not regress.

Run the existing full test suite and report results.

---

# REPORT BACK

Update `design/CHATGPT_CURSOR_DESIGN_HANDOFF.md` with:

1. repaired Round 006 history/current state
2. Round 007 work completed
3. files changed
4. screenshot references
5. functional audit
6. test results
7. updated asset readiness
8. mobile UX findings
9. no more than 3 high-value questions

Mark this inbox consumed, commit/push everything, and stop for ChatGPT review.

**Do not start Flutter implementation. Do not begin Round 008 speculatively.**