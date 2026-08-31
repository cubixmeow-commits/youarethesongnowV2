---
type: flutter-handoff
status: active
updated: 2026-08-30
area: visual-design
phase: round-006-platinum-blue
target: iOS + Android (Flutter)
---

# Flutter / iOS Visual Design Handoff

The web prototype (`public/assets/css/app.css`, templates) is the visual specification for the future Flutter app. This document maps tokens and components without prescribing implementation code.

---

## Design intent (Round 006)

Premium **private creative venue** on a **platinum + blue on black** palette: calm, hosted, artwork-first. Not gaming RGB, nightclub neon, cyberpunk, casino gold, or fintech blue wash.

**Material hierarchy (do not expand casually):**

1. Black / near-black — scaffold
2. Graphite — structural surfaces
3. Smoked graphite — sheets, controls, trays
4. Platinum hairlines + sapphire/cobalt light — selection, action, micro-detail
5. Artwork — strongest color and emotion

---

## Theme tokens

Map web CSS variables to Dart `ThemeExtension` / `ColorScheme` names. OKLCH values in CSS should be converted to Flutter `Color` at integration time (use consistent sRGB approximations).

| Dart name | Web CSS | Role |
| --- | --- | --- |
| `appBackground` | `--color-bg` | Near-black scaffold |
| `surfaceGraphite` | `--color-surface` | Structural graphite |
| `surfaceStone` | `--color-surface-stone` | Quiet working areas |
| `surfaceLacquer` | `--color-surface-lacquer` | Smoked graphite sheets |
| `surfaceElevated` | `--color-surface-elevated` | Raised graphite |
| `surfaceChrome` | `--color-surface-chrome` | App bar / tab bar (translucent) |
| `textPrimary` | `--color-text` | Cool platinum white |
| `textSecondary` | `--color-text-secondary` | Supporting copy |
| `textTertiary` | `--color-text-tertiary` | Metadata, captions |
| `platinum` | `--color-platinum` | Hairlines, fine borders, micro-labels |
| `silver` | `--color-silver` | Secondary metallic tone |
| `accentSapphire` | `--color-accent-sapphire` | Calm brand light / selection |
| `accentCobalt` | `--color-accent-cobalt` | Strong action emphasis |
| `accentPrimary` | `--color-accent` | Alias → sapphire |
| `accentSoft` | `--color-accent-soft` | Sapphire wash |
| `hazeSapphire` | `--color-haze` | Atmospheric deep blue (decorative) |
| `borderHairline` | `--color-border-hairline` | Platinum hairline |
| `borderDefault` | `--color-border` | Standard outline |
| `borderStrong` | `--color-border-strong` | Interactive outline |
| `destructive` | `--color-destructive` | Delete / danger |
| `success` | `--color-success` | Success states |
| `warning` | `--color-warning` | Semantic warning (only warm brand-adjacent color) |
| `focusRing` | `--focus-ring` | Sapphire focus highlight |

### Spacing scale

| Token | px | Dart |
| --- | --- | --- |
| space1 | 4 | `AppSpacing.xs` |
| space2 | 8 | `AppSpacing.sm` |
| space3 | 12 | `AppSpacing.md` |
| space4 | 16 | `AppSpacing.lg` |
| space5 | 24 | `AppSpacing.xl` |
| space6 | 32 | `AppSpacing.xxl` |
| space7 | 48 | `AppSpacing.xxxl` |
| space8 | 64 | `AppSpacing.huge` |

### Radius

| Token | px | Use |
| --- | --- | --- |
| radiusSmall | 8 | inputs, portrait tiles |
| radiusMedium | 14 | buttons, nav items |
| radiusLarge | 20 | panels, sheets |
| radiusXLarge | 28 | large artwork frames |

### Elevation

| Level | Web | Flutter |
| --- | --- | --- |
| 0 | flat on bg | no shadow |
| 1 | hairline + inset stone | `BoxShadow` 1px highlight |
| 2 | `--shadow-2` | soft ambient shadow |
| 3 | `--shadow-3` | modal / sheet depth |

### Typography

| Role | Web | Flutter `TextTheme` |
| --- | --- | --- |
| Display | Instrument Serif, `--type-display` | `displayLarge` |
| Large title | `h1` / `--type-large-title` | `displaySmall` |
| Title | `h2` / `--type-title` | `headlineMedium` |
| Headline | `h3` / `--type-headline` | `titleLarge` |
| Body | `--type-body` | `bodyLarge` |
| Callout | `--type-callout` | `bodyMedium` |
| Caption | `--type-caption` | `labelMedium` |
| Meta | `--type-meta` | `labelSmall` |

**Fonts:** Instrument Serif (display) + DM Sans (UI). Load via `google_fonts` or bundled assets.

### Motion

| Token | ms | Use |
| --- | --- | --- |
| motionFast | 140 | press, toggle |
| motionBase | 220 | hover, focus |
| motionEnter | 420 | cover reveal |
| easeStandard | cubic(0.22, 1, 0.36, 1) | default |
| easeEmphasized | cubic(0.2, 0, 0, 1) | entrances |

Honor `MediaQuery.disableAnimations` / platform reduced-motion.

---

## Widget map

| Web selector / area | Flutter widget (proposed) | Notes |
| --- | --- | --- |
| `body.app` + chrome | `AppScaffold` | SafeArea, background stack, grain optional |
| `.app-topbar` + `.brand` | `VenueTopBar` | Mark + wordmark; mark from asset |
| `.app-nav` | `AppBottomNavigation` | 3–4 tabs; 48px min touch |
| Desktop `.app-nav` rail | `DesktopNavigationRail` | 88px width; same destinations |
| `.session-header` | `PrivateSuiteHeader` | Art mark, title, stage chips |
| `.session-progress` | `StageIndicator` | Song / People / Direction |
| `.movement` sections | `VenueSection` | Numbered stage container |
| `.track-source__fields` + inputs | `VenueTextField` ×2 in `SongStageForm` | Hairline grouped layout |
| `.btn--primary` | `VenuePrimaryButton` | Sapphire→cobalt gradient, full-width where needed |
| `.btn--secondary` | `VenueSecondaryButton` | Lacquer outline |
| `.btn--danger` | `VenueDestructiveButton` | Delete confirm |
| `.portrait-tray` + `.portrait-chip` | `PortraitGalleryTray` + `PortraitTile` | Square crop, select + delete |
| `.style-option` | `CuratorOption` | Style tile; quiet, not dashboard card |
| `.choice-row` | `CuratorSegmentedChoice` | Quality / format |
| `.session-board` | `SessionOverview` | Sticky summary panel |
| `.confirm-sheet` | `VenueConfirmationSheet` | Portrait delete; modal bottom sheet on phone |
| `.gallery-grid` + `.gallery-item` | `GalleryGrid` + `ArtworkTile` | Exhibition spacing |
| `.gallery-empty` | `GalleryEmptyState` | Asset + copy |
| `.hero--launchpad` | `LaunchpadHero` | Guest home only |
| `.panel.narrow` | `VenueAuthPanel` | Sign-in / account sheets |
| `.venue-progress` | `VenueProgressLine` | Generation wait indicator |
| `.reveal__figure` | `ArtworkRevealStage` | Full-bleed result |

---

## Responsive model

**Philosophy:** mobile app first; larger canvases expand the same components.

| Breakpoint | Web | Flutter behavior |
| --- | --- | --- |
| Phone | < 600px | Single column; bottom nav; full-bleed create |
| Large phone | 600–899px | Wider grids; same chrome |
| Tablet | 900px+ (web) | Optional side rail; Create split column |
| Desktop web | 1120px max content | Rail + sticky `SessionOverview` |

Flutter should use `LayoutBuilder` / `Breakpoint` constants rather than copying web px exactly:

- `compact` — phone
- `medium` — large phone / small tablet
- `expanded` — tablet / desktop web

Create on expanded: main column + sticky overview (~35% width). Gallery: 2-col phone → auto-fill grid.

---

## iOS considerations

| Topic | Guidance |
| --- | --- |
| **Safe areas** | Respect top notch and home indicator; tab bar padding matches `env(safe-area-inset-*)` behavior |
| **Touch targets** | 44–48pt minimum (`--touch-min: 48px` on web) |
| **Keyboard** | Create forms scroll above keyboard; no obscured primary actions |
| **Bottom navigation** | Fixed; content `padding-bottom` accounts for tab bar + safe area |
| **Dynamic Type** | Support text scaling on body/headlines; clamp display sizes where needed |
| **Dark mode** | Dark-only baseline today; design tokens assume dark scaffold |
| **Reduced motion** | Disable venue-progress animation; reduce hero Ken Burns |
| **Image loading** | Placeholder stone surface; fade-in artwork; square crops for portraits/gallery |
| **Sheets** | Portrait delete + future paywall: `showModalBottomSheet` with lacquer surface — not default Cupertino chrome |
| **Status bar** | Dark content; `theme-color` equivalent via `SystemChrome` |

Do **not** default to Cupertino/Material stock styling for brand surfaces. Use custom `Venue*` components with shared `VenueTheme`.

---

## Asset bundle (Flutter)

Mirror `design/ASSET_INTEGRATION_MAP.md` paths under:

```
assets/images/system/
  app_atmosphere_haze_phone.webp
  app_atmosphere_haze_desktop.webp
  launch_mark_tile.webp
  creative_session_backdrop_phone.webp
  creative_session_backdrop_desktop.webp
  empty_collection_still.webp
```

Declare in `pubspec.yaml`; load via `AssetImage` / `DecorationImage`.

---

## Screen inventory (parity with web Build 1)

| Route | Flutter screen | Priority |
| --- | --- | --- |
| `/` guest | `LaunchpadScreen` | Later (web marketing) |
| `/sign-in` | `SignInScreen` | High |
| `/create` | `CreateFlowScreen` | Critical |
| `/gallery` | `GalleryScreen` | High |
| `/images/:id` | `ArtworkRevealScreen` | High |
| `/account` | `AccountScreen` | Medium |
| `/owner` | `OwnerScreen` | Low (dev) |

---

## Readiness (Round 005)

| Area | Status |
| --- | --- |
| Color / spacing / radius tokens | Ready to port |
| Component boundaries | Documented above |
| Asset hooks | Documented in `ASSET_INTEGRATION_MAP.md` |
| Create flow structure | Stable; matches web stages |
| Navigation IA | Stable (Create, Gallery, Account, Owner) |
| Generated assets | Not yet delivered |

**Not ready without further work:** paywall art, procedural grain parity, full widget library implementation, iOS store icon pipeline.
