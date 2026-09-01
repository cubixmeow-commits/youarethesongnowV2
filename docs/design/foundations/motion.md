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

Luminous Night Studio concentrates motion in three places:

1. **Track advance:** 220–300ms spatial transition between focused Create steps, interruptible and state-preserving.
2. **Luminous selection:** 140–220ms tonal/edge change for Song DNA and Direction cards; no continuous glow.
3. **Cover reveal:** 300–420ms opacity/blur/12px settle for artwork, followed by quiet action entry. The artwork remains interactive immediately.

Do not use `transition: all`, infinite ambient animation, decorative parallax in routine product screens, or stagger more than three peer items. Exit is shorter/quieter than entry.

## Reduced motion

`prefers-reduced-motion: reduce` disables hero Ken Burns, example/gallery zooms, venue shimmer, button/nav transitions, and showcase tile motion.

**Gap:** portrait-chip, style-option, choice-row, and movement transitions are not fully cleared.

Phase 1 acceptance requires those gaps to be fixed for every touched canonical component. Reduced motion replaces travel/scale/shimmer with direct state or a short opacity change and never suppresses semantic feedback.

## Flutter note

Map to durations + curves; honor `MediaQuery.disableAnimations`. Prefer press states over hover-only polish.
