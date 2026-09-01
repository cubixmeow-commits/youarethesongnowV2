# Flutter component and token map

**Boundary:** portability contract only. Flutter implementation remains separately authorized after web/API/quality approval.

## Theme architecture

| Design OS layer | Flutter target |
| --- | --- |
| Color semantics | `ColorScheme` for standard roles + `YatsnColors extends ThemeExtension` for surface/selection/status detail |
| Typography | `TextTheme` using bundled Instrument Serif + DM Sans assets; no dependency on a runtime font network |
| Spacing/radius/control sizes | immutable `YatsnDimensions extends ThemeExtension` |
| Elevation/material | `YatsnElevation` constants; use shadows only for sheet/dialog/real elevation |
| Motion | `YatsnMotion` duration/curve constants; gate through `MediaQuery.disableAnimations` |
| Breakpoints | `LayoutBuilder`/available width: medium 600, expanded 900, wide 1200 as starting design bands |

`assets/design/tokens/semantic-tokens.json` is the semantic baseline. Convert reviewed OKLCH values to tested sRGB/Display-P3-safe `Color` values; do not paste CSS `color-mix()` expressions into Dart.

## Component mapping

| Product component | Web direction | Flutter direction |
| --- | --- | --- |
| Button | `.yatsn-btn` + `--primary/--secondary/--quiet/--destructive`; states via `:hover/:focus-visible/:active/:disabled` and `.is-loading` | `YatsnButton` wrappers around Filled/Outlined/Text buttons |
| Icon button | `.yatsn-icon-btn`; named SVG/`currentColor` | `YatsnIconButton` using bundled vector assets |
| App chrome | top bar + bottom nav/rail | one destination model feeding `NavigationBar` / `NavigationRail` |
| Focused Create shell | compact header, optional hidden global nav | nested navigator/adaptive scaffold without bottom bar |
| Song search | `.yatsn-song-search`, `.yatsn-song-result`, `.yatsn-song-selected`; `dataset.yatsnSongState` (`idle|typing|loading|result|selected|empty|error`) | `SongLookupState` + labeled `TextField`s and a custom result row; confirm before advancing |
| Song DNA card | `.yatsn-dna-card` checkbox-like selection; `.is-selected` / `.is-recommended` / `.is-conflict` / `.is-loading` | custom `Semantics(checked:)` widget |
| Direction card | `.yatsn-direction-card` radiogroup; Explore also keeps `.ai-direction-card` | custom radio-card group with selected/recommended states |
| Fine Tune | sheet/panel | `showModalBottomSheet` compact; constrained side panel expanded |
| Portrait shelf | horizontal shelf in Gallery | sliver/list section with private thumbnail loader |
| Status banner | `.yatsn-status` + tone modifiers; Explore error uses the same class | shared inline widget + selective live-region semantics |
| Generation stage | full-screen job status | route backed by lifecycle-safe polling state |
| Artwork figure/tile | `.yatsn-artwork` loading/ready/unavailable; later private image/content endpoints | `AspectRatio`, cached thumbnail, authenticated network loader, error builder |
| Sheet/dialog | `.yatsn-sheet` / `.yatsn-dialog` on `<dialog>` | modal bottom sheet / `AlertDialog` with shared content contracts |
| Confirmation | canonical confirm sheet | dialog/sheet; haptic only as enhancement |
| Gallery | CSS grid, no required masonry | slivers/grid; preserve original ratio and private authorization |

## State architecture

Use one immutable state model per feature and different compact/expanded compositions. Suggested conceptual states:

- `CreateHomeState`
- `SongLookupState`
- `SongDnaProjectionState`
- `SongDnaSelection`
- `QuickGenerateState`
- `ExploreState`
- `FineTuneDraft`
- `GenerationJobState`
- `RevealState`
- `GalleryState`
- `AccountState`

Exact state-management library is not chosen here. Domain rules remain on PHP; Flutter repositories call versioned `/api/v1` endpoints and translate transport errors into these UI states.

Phase 1 web class names (`.yatsn-btn`, `.yatsn-direction-card`, `.yatsn-status`, `.yatsn-dna-card`, `.yatsn-sheet`, `.yatsn-dialog`, `.yatsn-artwork`) are the current visual/state source. Flutter should recreate the same roles, compact-first composition, and states — not CSS. Explore `dataset.yatsnExploreState` is a platform-neutral hook (`idle|loading|ready|selected|error|manual`) that a later Flutter Explore repository can mirror without depending on DOM. The Explore card grid sizes columns with `repeat(auto-fit, minmax(min(100%, 16.5rem), 1fr))` inside a `yatsn-explore` size container. Flutter should use `LayoutBuilder` / available width so three columns appear only when names and descriptions stay readable.

## Platform behavior

- Touch behavior is canonical. Pointer hover and keyboard shortcuts are enhancements on desktop/tablet.
- Use platform share UI only after server privacy/share rules are satisfied.
- Add restrained haptics for meaningful selection, completion, and destructive confirmation only; never encode sole meaning in haptics.
- Refresh job/account/gallery state after app lifecycle resume.
- Persist opaque sessions securely; never place provider credentials, prompts, private media paths, or raw lyrics in the app.

## Parity acceptance

Flutter parity means matching user goal, state transitions, semantics, token intent, and adaptive composition. It does not mean reproducing CSS, backdrop filters, hover effects, Masonry.js, DOM data attributes, or web route geometry.
