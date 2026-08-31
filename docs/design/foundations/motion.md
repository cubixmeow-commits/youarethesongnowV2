# Motion

## Tokens

| Token | Value | Use |
| --- | --- | --- |
| `--motion-fast` | 140ms | press, toggle |
| `--motion-base` | 220ms | hover, focus |
| `--motion-enter` | 420ms | cover reveal |
| `--ease-standard` | cubic-bezier(0.22, 1, 0.36, 1) | default |
| `--ease-emphasized` | cubic-bezier(0.2, 0, 0, 1) | entrances |

## Keyframes in app.css

| Name | Behavior |
| --- | --- |
| `cover-reveal` | opacity + translateY + blur settle on `.hero__copy` |
| `venue-glow` | shimmer on generation progress bar |

## Interaction thesis (from responsive plan)

1. Quiet **cover reveal** on entry
2. Clear **track-advance** between Song → People → Direction
3. Restrained focus/state motion — never ornamental

## Reduced motion

`prefers-reduced-motion: reduce` disables hero Ken Burns, example/gallery zooms, venue shimmer, button/nav transitions, and showcase tile motion.

**Gap:** portrait-chip, style-option, choice-row, and movement transitions are not fully cleared.

## Flutter note

Map to durations + curves; honor `MediaQuery.disableAnimations`. Prefer press states over hover-only polish.
