# Premium product screen specifications

**Direction:** Luminous Night Studio  
**Status:** canonical design specification; implementation must remain sliced and contract-aware  
**Flow:** Create → Song DNA → Quick Generate or Explore → optional Fine Tune → Generation → Reveal → Gallery

These specifications finalize the intended customer experience while preserving current Build 1 domain contracts. An API gap is a dependency, not authorization to invent or silently change a backend response, database field, billing rule, or creative-engine contract.

## Shared rules

- One real screen heading and one dominant next action.
- Draft state persists before leaving a focused creation step.
- Primary navigation remains on destinations and may be hidden only inside focused Create after safe exit/back behavior exists.
- Every networked screen defines pending, empty/unavailable, error/retry, and connectivity recovery.
- Copy contains no em dashes, raw lyrics, provider/model names, prompts, internal StyleMap names, or AI-console language.
- Paywall remains after meaningful configuration and before generation, preserving the prepared draft.
- State models are platform-neutral and map to Flutter without duplicating server business logic.

---

# 1. Create

## Purpose and composition

Make the product promise and first action obvious: choose a meaningful song, then turn its Song DNA into artwork.

- Heading: `Put yourself inside the song.`
- Primary action: `Choose a song`, or the existing artist/title search when no separate chooser route exists.
- Resume state: one concise `Continue your creation` row.
- Recent creations: a small artwork-led row after the primary action, never a dashboard.
- Active portrait: small `You` thumbnail and `Change` only after a default exists. Management remains in Gallery.

Current lookup remains artist + title through `POST /api/v1/song-lookups`. Do not combine inputs or invent autocomplete unless the API changes explicitly. A confirmed lookup advances to Song DNA. Do not show style, quality, orientation, or prompt controls here.

## Layout

- Compact: poster-like opening plus single search surface; bottom nav on destination, hidden only after focused flow begins.
- Medium: larger recent-art row below search.
- Expanded: centered 640–720px decision area; recent art may sit alongside only when subordinate.

## States

New, resumable draft, typing, lookup pending, matched, ambiguous, no match, context fallback, provider/rate error, offline. Preserve fields on error.

## Accessibility, backend, Flutter

Use a real `<h1>`, visible labels, one status announcement, errors linked to fields, and focus on confirmation/error. Reuse lookup, rate limits, privacy, and draft `songLookupId`. Customer-safe DNA projection is a separate explicit contract. Flutter uses `CreateHomeState` + `SongLookupState` with the same endpoints.

## Acceptance

At 320px the song action is identifiable within two seconds, lookup completes without horizontal scroll, and no generic generator setting appears.

---

# 2. Song DNA

## Purpose and content

Let the user choose emotional/story material without exposing raw analysis. Customer-safe dimensions project from `song-dna-v2.0`:

- Emotional core: essence, mood, themes, emotional arc
- Story: narrative archetype and approved original visual moment projection
- Point of view: subject roles and relationship dynamics
- World: setting, atmosphere, weather, spatial character
- Symbols: sanitized symbols and visual metaphors
- Tension: turning point, intensity, themes

Projection copy is concise, non-reconstructive, and free of lyrics, risk internals, confidence, prompts, or technical visual parameters.

## Composition and actions

- Song identity in compact metadata.
- Heading: `What part of this song should lead the artwork?`
- One recommended card, but do not silently select it unless product logic explicitly authorizes that behavior.
- `Add another layer` progressively reveals more.
- Primary: `Generate for me` after at least one layer.
- Secondary: `Explore options`.

## Layout and states

Compact stacks cards and uses a safe-area CTA; medium may use two columns; expanded adds a quiet 320–360px summary. States: analysis pending, projection ready, recommended, 1–3 selected, conflict-disabled, context fallback, unavailable, retry, stale after song change.

## Accessibility, backend, Flutter

Use checkbox semantics and text conflict reasons. Requires an explicitly designed customer-safe projection and persisted draft selection; current dev analysis cannot be rendered directly. The initial maximum of three and conflict rules require server/product confirmation. Flutter uses `SongDnaProjectionState` + immutable `SongDnaSelection`.

## Acceptance

A first-time user selects one meaningful layer without understanding the schema; an engaged user adds layers without seeing technical image controls.

---

# 3. Quick Generate

## Purpose and composition

Provide the shortest default path. Show song, selected DNA, active portrait, and authoritative price/credits. Primary CTA is `Generate for me`; `Explore options` is secondary; `Fine Tune` is quiet and optional.

This may be a Create state rather than a standalone route. On activation, refresh the authoritative quote, enter the existing paywall if required, then submit one idempotent job. Do not imply generation began before membership and job creation succeed.

## States

Ready, missing portrait/default, quote pending, membership/credits gate, submitting, validation changed, duplicate protected, error with preserved draft.

## Accessibility, backend, Flutter

Keep CTA naming stable, announce quote changes politely, and focus the paywall as a new state. Reuse summary, quote/credit, membership, snapshot, and job submission. Auto-resolving visual treatment from DNA is a separate server creative-engine change. Flutter `QuickGenerateState` coordinates contracts without duplicating credit logic.

## Acceptance

From ready DNA, the default path has one dominant action and no required StyleMap, quality, orientation, or instruction decision.

---

# 4. Explore

## Purpose and composition

Offer three song-specific worlds while keeping StyleMap compatibility invisible.

- Heading: `Explore directions`.
- Supporting line: `Choose the world that feels right for this Song DNA.`
- Exactly three direction cards.
- First server-ranked result has a quiet `Recommended` label.
- Selection reveals `Use this direction` when Fine Tune/confirmation follows.
- `Generate for me instead` exits to the default path.

Compact stacks cards; expanded may compare three columns if names/descriptions remain readable. Remove an unnecessary outer container if spacing alone groups the set.

## States

Three stable loading placeholders, ready, recommended, selected, stale after DNA change, sanitized provider error, retry, Quick Generate fallback.

## Accessibility, backend, Flutter

Use radio semantics and full-card activation. Reuse `POST /api/v1/explore-directions`, derived-DNA-only privacy, the exact three-direction schema, diagnostics gating, and internal compatibility bridge. Do not expose internal style data or redesign the endpoint in the visual slice. Flutter uses `ExploreState { idle, loading, directions, selectedId, error }`.

## Acceptance

Users compare, select, keyboard-operate, retry, or exit. No card reads like a permanent generic preset.

---

# 5. Fine Tune

## Purpose and controls

Give bounded advanced control without becoming an image console.

- Orientation: square, portrait, landscape.
- No text in image: off by default.
- Special instructions: optional, short, server-limited.
- Quality only while visible credit economics require it; medium remains recommended.

StyleMap, provider/model, seed, prompt strength, negative prompt, camera, palette, lighting, and era are not customer controls.

Compact uses a full-height sheet or route; expanded uses a 320–360px panel. Closing without Apply preserves applied state; dirty dismissal warns only if data would be lost. Reset restores system defaults.

## States

Collapsed, open, clean, dirty, invalid, quote recalculating, applied, unavailable.

## Accessibility, backend, Flutter

Focus enters and returns correctly. Orientation is a radio group; no-text is a checkbox/switch; instructions have count/errors. Reuse current draft fields and product options. Moving controls is UI work; removing required submission fields needs verified server defaults. Flutter uses shared `FineTuneDraft` in a sheet/panel.

## Acceptance

Fine Tune never blocks Quick Generate and quote-affecting changes are authoritative before submission.

---

# 6. Generation

## Purpose and composition

Turn waiting into a truthful transformation without fake progress.

- Atmospheric art stage, never a fake preview.
- One truthful status line.
- Thin playhead/state line with no percentage.
- Selected DNA as stable context, not checkmarked fake sub-progress.
- A leave-safely note when background processing is durable.

| Backend state | Customer copy direction |
| --- | --- |
| submit pending | Preparing your creation |
| queued | Finding the heart of your song |
| claimed/generating | Building your cinematic world |
| creative package | Bringing you into the scene |
| provider/finishing | Adding the final details |
| completed | Your artwork is ready |
| failed | We could not finish this image. Your generation credits were returned. |

Use only actual server distinctions. Compact is immersive with global nav hidden; expanded may add a stable DNA context panel, never an operational dashboard.

## States

Submitting, queued, generating, connectivity lost/poll retry, completed transition, failed/refunded. Submission remains non-cancellable per contract.

## Accessibility, backend, Flutter

Announce only stage changes; never a timer. Reduced motion removes shimmer, blur travel, and scale. Reuse async job, polling, credit reservation/release, and durable Gallery behavior. Flutter uses lifecycle-safe `GenerationJobState` and refresh-on-resume.

## Acceptance

The user understands status, can safely leave, and gets an honest failure/refund message without provider detail.

---

# 7. Reveal

## Purpose and sequence

Let the artwork land before another decision.

1. Generation completes and chrome recedes.
2. Artwork appears with a restrained 300–420ms cover reveal.
3. Actions enter after the image is usable; reduced motion renders directly/fades.
4. Metadata follows below or in a quiet expanded side panel.

## Actions

- Use `Download` until `Save` has a distinct product meaning; the image already exists in Gallery.
- `Share` reuses link/email contracts.
- `Create a variation` reuses regeneration/recreate-draft only if labeled honestly.
- `Reimagine` remains future until a distinct behavior/endpoint is approved.
- Delete remains secondary and outside the primary reveal cluster.

Compact uses a near-full-width image and actions below; expanded uses a much larger image plus 320–360px action/metadata panel.

## States

Image loading, ready, download pending, share off/on/error, variation draft pending, unavailable, delete confirmation.

## Accessibility, backend, Flutter

Use safe-metadata alt text and logical action order; do not steal focus during decorative reveal. Reuse image, content/download, sharing, regeneration, and deletion. Flutter `RevealState` can use the platform share sheet without bypassing server privacy.

## Acceptance

Artwork occupies the visual majority and every action label matches real behavior.

---

# 8. Gallery

## Purpose and composition

Manage identity and creations as a private collection:

1. `Gallery` heading and collection status.
2. `Your portraits` shelf with active/default first and `Add portrait`.
3. `Your creations` grid, newest first unless product logic says otherwise.
4. Queued/generating/failed placeholders when exposed by the gallery contract.

Compact uses a horizontal portrait shelf and 1–2 artwork columns based on fit; medium 2–3; expanded 3–4 with aspect-ratio-aware rows. Private Gallery does not require masonry.

## States and actions

First-use empty, portraits empty, creations empty, loading, partial image error, queued/generating, failed/refunded, deletion pending. Portrait actions are add/select/delete. Artwork opens detail; avoid overlay toolbars. Empty CTA: `Create an image`.

## Accessibility, backend, Flutter

Empty states remain visible to assistive tech, sections have headings, and all actions work without hover. Reuse portrait/image APIs, private media, deletion, thumbnails, and job states. Active/default portrait persistence is an explicit dependency if absent. Flutter uses one custom scroll view with shelf + grid.

## Acceptance

Portrait management is obvious without interrupting repeat Create, and the collection reads as artwork rather than metadata cards.

---

# 9. Account

## Purpose and composition

Let users understand membership/credits and safely manage identity, security, billing, sessions, and deletion.

1. Membership and generation credits
2. Profile: display name and verified email/change
3. Sign-in and sessions: optional password and sign out all
4. Billing management/customer portal
5. Privacy and permanent deletion

No marketing hero, KPI dashboard, or cards around every field. Compact uses grouped vertical sections; expanded uses a 640–760px settings column with optional section index.

## States

Loading, active, cancelled-through-period, grace period, inactive/read-only, complimentary, profile saving/error/success, email verification pending, password set/remove, session revoke, portal unavailable, deletion preview/confirm/error.

## Accessibility, backend, Flutter

Use real heading structure, persistent labels, section-specific status, and keyboard-complete confirmation. Deletion is separated, immediately irreversible, identity-confirmed, and explains the payment-record exception. Reuse all current `/me`, credits, membership, account/security, billing portal, and deletion contracts. Flutter uses sectioned `AccountState` with server authority.

## Acceptance

Users identify status and complete each operation without guessing; sensitive behavior stays explicit and contract-accurate.

---

# Cross-screen acceptance evidence

| Screen | Dominant action | Required states/evidence |
| --- | --- | --- |
| Create | Find/choose song | 320/390/900/1440; loading/no-match/error |
| Song DNA | Generate for me | loading, selected, recommended, conflict |
| Quick Generate | Generate for me | quote, paywall, submit error, duplicate protection |
| Explore | Use this direction | loading, three results, selected, retry |
| Fine Tune | Apply | collapsed/open/dirty/error/quote update |
| Generation | status-led | queued/generating/offline/failure/reduced motion |
| Reveal | Download/Share after art | loading/ready/share/variation/delete confirm |
| Gallery | Create image when empty | empty/portraits/jobs/mixed ratios |
| Account | contextual per section | membership lifecycle, save/error, deletion |

No screen is canonical until tests, keyboard review, screen-reader semantics, 200% zoom, reduced motion, and required screenshots pass.
