# Core component specifications

**Status:** production-ready behavioral and visual contract  
**Direction:** Luminous Night Studio  
**Scope:** customer product; owner/admin may reuse primitives but prioritizes density and clarity

Component names are platform-neutral. Web may implement them as PHP partials plus focused JavaScript modules; Flutter maps them to widgets with the same states and semantics.

## Shared rules

- Components use canonical semantic tokens from `assets/design/tokens/semantic-tokens.json`.
- Minimum target is 44×44px; standard control height is 48px; primary action is 52px.
- Every async action protects against repeated submission and preserves an idempotency key where the API requires one.
- Focus is visible on every interactive surface. Hover never reveals the only action.
- Loading state keeps the control label or an accessible equivalent so intent remains clear.
- Disabled is used only when the reason is evident nearby. Validation errors are preferred when a user can fix the state.
- Status is never communicated by color, glow, motion, or icon alone.

## State matrix

| State | Visual | Behavior | Semantics |
| --- | --- | --- | --- |
| Default | neutral surface/content | available | natural role/name |
| Hover | slight tonal lift/edge | pointer enhancement only | unchanged |
| Pressed | immediate tonal darken; optional 0.98 scale | action begins on release | unchanged |
| Focused | 3px focus ring with offset/contrast | keyboard target | `:focus-visible` / Flutter focus |
| Selected | selected surface + edge + persistent marker | value is active | `aria-pressed`, radio/checkbox, or `aria-selected` as appropriate |
| Recommended | quiet text badge or ordering cue | no automatic selection | description includes “Recommended” once |
| Disabled | reduced contrast but readable | cannot activate | native disabled; reason available |
| Loading | stable width, progress indicator, action locked | request in flight | `aria-busy=true`; concise live status elsewhere |
| Error | semantic error message and optional edge | retry/fix available | error linked with `aria-describedby` |
| Success | brief confirmation, then stable end state | next action enabled | polite announcement only when useful |

## 1. Button

Variants: `primary` (one per decision view), `secondary`, `quiet`, and `destructive`. Primary uses cobalt with high-contrast text; destructive is outlined until final confirmation.

Anatomy: optional leading icon, label, optional trailing icon. States: default, hover, pressed, focused, disabled, loading, success. Primary loading retains the action in its accessible name, for example “Generate for me, loading.”

Compact primary actions are full-width when they end a focused step. Expanded actions fit content or use an intentional 240–360px width. Flutter maps to a themed `YatsnButton` wrapper around filled, outlined, and text buttons.

## 2. Icon button

- 44px square minimum; 24px icon; optical adjustment allowed inside the box.
- Always has an accessible label and tooltip on hover-capable platforms.
- Use a single outline icon family with 1.75–2px apparent stroke.
- Destructive icon buttons require a named action and confirmation when irreversible.

Flutter: shared `IconButton` wrapper with constraints and tooltip.

## 3. App chrome

The destination top bar contains the brand. Focused Create replaces it with Back/Close, a concise stage title, and optional saved-state text. Do not show both brand lockup and a competing large title.

Approved destinations are Create, Gallery, and Account; Owner appears only for owners. Discover remains unapproved. Compact uses a bottom navigation bar; expanded uses the same ordered model in an 88px rail. Active state uses icon, label, and persistent indicator, not color alone.

Flutter: `NavigationBar` and `NavigationRail` driven from one destination model.

## 4. Search field and song result

The search input has visible labels. Keep separate artist/title inputs until an explicit API change authorizes a combined query. The clear action is 44px; submit copy is `Find this song`.

States: empty, typing, submitting, match, no match, ambiguous match, network/provider error, and rate limited.

The song-result row is one full activation target with song title, artist, optional safe art placeholder, required match qualifier, and trailing affordance. Selection advances only after the current lookup contract confirms the intended match.

Flutter: labeled fields plus a custom result row.

## 5. Song DNA card

### Purpose

Expose a customer-safe, short projection of one meaningful DNA dimension. Never render raw JSON, lyrics, risk flags, internal confidence, prompts, or palette/camera internals.

### Anatomy and behavior

Dimension label, emotionally legible value (1–3 short lines), optional supporting phrase, and selected marker. The whole card toggles the layer. At least one layer is selected before generation unless owners later approve `Choose for me`. The initial UI maximum is three layers; backend conflict resolution must be specified before implementation. `Add another layer` reveals eligible dimensions instead of showing the whole schema.

States: skeleton/loading, available, recommended, selected, selected+recommended, disabled due to conflict, unavailable, projection error.

Accessibility: checkbox semantics for independent multi-selection; dimension and value precede state in the accessible name. Flutter uses a custom `Semantics(checked:)` card backed by immutable selection state.

## 6. Creative direction card

Presents one of exactly three song-specific Explore directions. It is not a public StyleMap tile.

Anatomy: direction name, 1–3 line world description, optional later rights-cleared preview, recommended label on the first server-ranked result, and selected marker.

Behavior: radio selection. Selection reveals `Use this direction` when Fine Tune/review follows, or `Create this direction` only when the next action truly submits generation. Internal `styleId`/`styleName` remain private compatibility data.

States: three stable loading placeholders, recommended, default, hover, focused, selected, selected+recommended, stale after DNA changes, and generation error with one retry action.

Accessibility: radiogroup with arrow keys when implemented as a composite, otherwise native radios with whole-label activation. Flutter maps to a custom radio-card group driven by `ExploreState`.

## 7. Progressive disclosure trigger

Used for `Explore options`, `Fine Tune`, `Add another layer`, and advanced account detail.

- Copy states what opens; never vague `More`.
- Expanded state is persistent and programmatically exposed.
- Collapsing does not discard unsaved input without warning.
- Fine Tune is subordinate to generation and never pre-expanded for a new user.

Flutter chooses a route, expansion section, or modal sheet based on available space while keeping the same behavior.

## 8. Fine Tune control group

Initial allowlist:

- Orientation: square, portrait, landscape.
- No text in image: off by default.
- Special instructions: optional short field with safe examples and server limits.
- Quality: only while credits/provider economics require a customer choice; medium/default remains recommended.

Internal StyleMap, provider, model, seed, camera, lighting, palette, prompt strength, and negative prompt are not customer controls.

States: collapsed, expanded, dirty, valid, invalid, price recalculating, price ready, unavailable. Cost-affecting changes update the quote before submit. Compact uses a full-height bottom sheet or route; expanded uses a constrained panel/dialog.

## 9. Portrait shelf and portrait tile

Portrait management lives first in Gallery. The active/default portrait is used by Create; Create may show `You [thumbnail] Change` without reopening the full library.

Tile anatomy: private thumbnail, label, active marker, and actions. States: loading, active, default, upload processing, upload error, delete pending, delete blocked because the portrait is in an active draft.

Deletion uses the canonical confirmation sheet, explains scope, and restores focus. Do not expose storage paths or metadata. Flutter uses a horizontal shelf plus modal actions.

## 10. Status banner

Variants: info, success, warning, error. Anatomy: concise title or sentence, optional recovery detail, primary recovery action, and dismiss only when safe.

Use polite status semantics for ordinary changes and alerts only for urgent blocking errors. Private provider/build codes do not lead production copy. Errors do not auto-dismiss.

Flutter: shared inline status with selective live-region semantics.

## 11. Generation stage

Anatomy: artwork/atmosphere stage, current truthful status, thin state line, stable selected Song DNA summary, and a leave-safely note when background processing is durable.

States: submitting, queued, creative-package generation, image generation, finishing, completed transition, final failure with credits returned, and connectivity lost while the job may continue.

Never show a percentage without backend progress. DNA items are context, not fake completed processing steps. Reduced motion uses a static tonal stage and immediate copy changes without shimmer or travel.

Flutter: full-screen route polling the same job contract; lifecycle resume refreshes status.

## 12. Artwork figure and artwork tile

The figure preserves aspect ratio, reserves layout while loading, uses a 1px neutral inner edge, and never overlays essential controls on a busy focal region. Alternative text identifies the creation without inventing unavailable scene details.

The tile is image-dominant. Metadata appears below or through deliberate focus/selection treatment; actions remain reachable on touch. States: loading, ready, selected, job queued, job failed, image unavailable, deletion pending.

Flutter: constraint-aware image with cached thumbnail and explicit error builder.

## 13. Sheet and dialog

Use a sheet for contextual, reversible choices such as Fine Tune, portrait actions, and sharing. Use a dialog for blocking confirmation or a narrow decision.

Trap and restore focus on web, close with Escape when dismissible, and label title/description. Compact sheets respect safe area and keyboard. Destructive action is last and separated. Flutter maps to modal bottom sheet/dialog.

## 14. Empty state

Anatomy: optional purposeful still, specific title, one sentence, one primary action. Never hide an empty state from assistive technology and avoid inspirational filler.

- Gallery: `Your first world starts with a song.` / `Create an image`
- Portrait shelf: `Add a portrait to step into the artwork.` / `Add portrait`
- Explore failure is an error, not an empty state; offer retry and Quick Generate.

## 15. Confirmation sheet

Used for image, portrait, share-revocation, and account deletion. It states the object, consequence, recoverability, and exact action. Account deletion retains stronger identity confirmation. Replace `window.confirm()` as each slice is rebuilt.

## Component acceptance

Before a component becomes canonical:

1. all applicable states render in a lab/fixture;
2. keyboard and screen-reader semantics are verified;
3. compact and expanded layouts consume the same data contract;
4. reduced-motion behavior is verified;
5. feature code uses semantic tokens except documented fit exceptions;
6. Web and Flutter names/mappings are recorded;
7. tests cover state changes and repeated async submission where applicable.
