# Flutter portability

## What maps cleanly

| Web concept | Flutter direction |
| --- | --- |
| Semantic color/space/type/motion tokens | `ThemeData` / `ColorScheme` / `ThemeExtension` / `TextTheme` |
| App chrome destinations (Create / Gallery / Account) | `NavigationBar` (phone) + `NavigationRail` (large) |
| Create stages Song → People → Direction | Multi-step flow / wizard with shared draft state |
| PortraitChip / StyleTile / ChoiceRow | Custom widgets with selected + semantic states |
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

1. Exact sRGB hex table derived from OKLCH tokens.
2. Whether marketing Home/Showcase ship in v1 Flutter or remain web-only.
3. Owner admin — web-only forever vs limited mobile ops.
4. Icon asset pipeline ownership (`assets/design/` → Flutter `assets/`).
5. Navigation pattern: Flutter adaptive scaffold vs always bottom bar on phone and rail on tablet+.

Do not start Flutter UI until Phase 2+ web foundations stabilize and owners authorize mobile work.
