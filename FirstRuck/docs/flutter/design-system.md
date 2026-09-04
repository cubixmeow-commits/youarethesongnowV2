# Flutter design system

## Visual thesis

A calm field guide brought to life with topographic lines, warm trail-paper surfaces, forest ink, and one safety-orange action color.

The native app should feel composed and practical, not military, gamified, or like a generic fitness dashboard.

## Color tokens

Flutter does not render CSS OKLCH directly. These sRGB values are the initial conversions of the current web tokens and should be compared visually with the live reference.

| Token | Hex | Use |
| --- | --- | --- |
| `ink` | `#131F16` | Primary text on paper |
| `inkSoft` | `#3C493E` | Supporting copy |
| `forest` | `#14331D` | Primary field surface |
| `forestDeep` | `#051609` | App background / deep surface |
| `paper` | `#F9F3E6` | Main light surface |
| `paperDeep` | `#E9E0CF` | Dividers and quiet controls |
| `warmWhite` | `#FFFDFA` | Text on dark surfaces |
| `orange` | `#F45707` | Primary action and route |
| `orangeDeep` | `#C73700` | Pressed/strong accent |
| `muted` | `#637063` | Secondary labels |
| `success` | `#347D48` | Verified positive state |
| `focus` | `#DB551D` | Keyboard focus where applicable |

Do not use orange for decoration everywhere. Reserve it for the route, primary action, progress, and critical emphasis.

## Typography

- Display: a bundled, licensed serif should eventually replace the web system fallback. For Milestone 1 use Georgia on iOS through a safe theme/fallback and document any Simulator difference.
- Interface: use the iOS system sans through Flutter's platform typography.
- Display headlines use normal weight, tight but unclipped leading, and balanced line breaks.
- Body text must remain readable at large accessibility text sizes.
- Avoid hardcoded headline heights. The current web screenshot demonstrates why the native layout must adapt when text grows.

Suggested starting scale:

| Role | Compact size | Notes |
| --- | --- | --- |
| Display | 52 | May scale down to 44 on short phones; never truncate |
| Screen title | 38 | Serif, normal weight |
| Section title | 20 | Sans, bold |
| Body | 17 | At least 1.45 line height |
| Button | 17 | Bold |
| Eyebrow | 12 | Bold, uppercase, tracked |
| Caption | 13 | Never essential information by itself |

## Spacing and shape

- Base spacing unit: 4 points
- Common gaps: 8, 12, 16, 24, 32, 48
- Compact horizontal page padding: 24
- Control radius: 14
- Panel radius: 26
- Minimum interactive target: 44 × 44 points
- Primary button target height: 56 points

Use safe-area padding at the top and bottom. Let the welcome content distribute vertically rather than position it with fixed offsets.

## Core native widgets

- `FirstRuckScaffold`: safe-area-aware paper or forest surface
- `BrandLockup`: circular FR mark plus wordmark
- `TopoBackground`: decorative `CustomPainter`; excluded from semantics
- `RouteStroke`: restrained path drawing; static under Reduce Motion
- `PrimaryActionButton`: full-width orange action with pressed state
- `FlowHeader`: Back, centered brand, step count
- `ProgressLine`: thin semantic progress indicator
- `AnswerCard`: entire row is a single radio target
- `InlineMessage`: error or status with icon and text, never color alone

## Welcome composition

The orange route is background atmosphere. It must not cross body copy or reduce legibility. Place it behind content with low-conflict geometry, or mask it away from the text zone. The headline must fit without clipping on small/short phones and at large text sizes. The primary action and footnote remain visible without overlaying the headline.

## Motion

- Use 140–260 ms for control and screen transitions.
- Route drawing may be slower but should not delay navigation.
- No looping decorative animation.
- Read `MediaQuery.disableAnimations`; show final states immediately when enabled.
- Do not encode meaning only in movement.

## Accessibility baseline

- Logical VoiceOver order follows the visual reading order.
- Decorative contour lines and route art are excluded from semantics.
- Buttons have explicit spoken labels.
- Answer cards expose selected state.
- Error messages are announced and focus moves to the affected control when practical.
- Support 200% text scaling without clipped content or unreachable actions.
- Test the smallest supported iPhone portrait size and landscape before expanding scope.
