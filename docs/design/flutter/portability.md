# Flutter portability

## What maps cleanly

| Web concept | Flutter direction |
| --- | --- |
| Semantic color/space/type/motion tokens | `ThemeData` / `ColorScheme` / `ThemeExtension` / `TextTheme` |
| App chrome destinations (Create / Gallery / Account) | `NavigationBar` (phone) + `NavigationRail` (large) |
| Create stages Song → People → Direction | **As-built.** Target: Song → Song DNA → Quick Generate / Explore Options / Fine Tune — see `screens/create-flow.md` |
| DNA selection progressive layers | Custom chips/cards + shared draft state (new API contract) |
| AI visual direction cards | Selectable recommendation cards — not StyleMap grid |
| Generation DNA checklist + honest stages | Full-screen progress route; poll job; no fake % |
| Reveal Save / Share / Variation / Reimagine | Hero image route; delayed secondary actions |
| Immersive Create (optional hide nav) | Nested navigator / shell without bottom bar on focused steps |
| PortraitChip / StyleTile / ChoiceRow | Custom widgets; StyleTile demoted to Fine Tune / admin over time |
| Gallery grid + image detail | `GridView` + detail route |
| Status / empty / error / paywall panels | Shared widgets fed by API state |
| `/api/v1` contracts | Same backend; opaque sessions already designed for mobile |

## What does not port literally

| Web pattern | Flutter approach |
| --- | --- |
| Masonry.js showcase wall | Custom sliver / staggered grid or curated rows |
| CSS sticky Create split | `LayoutBuilder` / adaptive split widgets |
| CSS mask nav icons | Bundled SVG/PNG icon set |
| `backdrop-filter` topbar | Material/Cupertino blur equivalents — verify |
| Google Fonts `<link>` | `google_fonts` or bundled font assets |
| DOM `data-*` mega-script | Feature modules calling API + local state |
| `<dialog>` / `window.confirm` | `showModalBottomSheet` / `showDialog` |
| Showcase static JSON + IntersectionObserver | Asset bundle or CDN + scroll pagination |

## Mobile-first mandate

Design and validate phone compositions first. Tablet/desktop web and Flutter large-layout are expansions of the same product, not alternate brands.

## Open decisions before Flutter start

1. Final contrast-reviewed sRGB table derived from canonical OKLCH tokens.
2. Whether marketing Home/Showcase ship in v1 Flutter or remain web-only.
3. Owner admin — web-only forever vs limited mobile ops.
4. Approved utility icon export from `assets/design/` → Flutter `assets/` after the web component slice.
5. Navigation pattern: Flutter adaptive scaffold vs always bottom bar on phone and rail on tablet+.

Do not start Flutter UI until Phase 2+ web foundations stabilize and owners authorize mobile work.
