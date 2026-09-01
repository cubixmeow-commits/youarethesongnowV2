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
| Button | PHP/HTML + canonical class/state | `YatsnButton` wrappers around Filled/Outlined/Text buttons |
| Icon button | named SVG/currentColor | `YatsnIconButton` using bundled vector assets |
| App chrome | top bar + bottom nav/rail | one destination model feeding `NavigationBar` / `NavigationRail` |
| Focused Create shell | compact header, optional hidden global nav | nested navigator/adaptive scaffold without bottom bar |
| Song search | labeled inputs + async result | `TextField`/custom result list with repository state |
| Song DNA card | checkbox-like selection card | custom `Semantics(checked:)` widget |
| Direction card | radio-card group | custom radio-card group with selected/recommended states |
| Fine Tune | sheet/panel | `showModalBottomSheet` compact; constrained side panel expanded |
| Portrait shelf | horizontal shelf in Gallery | sliver/list section with private thumbnail loader |
| Status banner | inline live status | shared inline widget + selective live-region semantics |
| Generation stage | full-screen job status | route backed by lifecycle-safe polling state |
| Artwork figure/tile | responsive image/content endpoint | `AspectRatio`, cached thumbnail, authenticated network loader, error builder |
| Sheet/dialog | `<dialog>`/sheet pattern | modal bottom sheet / `AlertDialog` with shared content contracts |
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

## Platform behavior

- Touch behavior is canonical. Pointer hover and keyboard shortcuts are enhancements on desktop/tablet.
- Use platform share UI only after server privacy/share rules are satisfied.
- Add restrained haptics for meaningful selection, completion, and destructive confirmation only; never encode sole meaning in haptics.
- Refresh job/account/gallery state after app lifecycle resume.
- Persist opaque sessions securely; never place provider credentials, prompts, private media paths, or raw lyrics in the app.

## Parity acceptance

Flutter parity means matching user goal, state transitions, semantics, token intent, and adaptive composition. It does not mean reproducing CSS, backdrop filters, hover effects, Masonry.js, DOM data attributes, or web route geometry.
