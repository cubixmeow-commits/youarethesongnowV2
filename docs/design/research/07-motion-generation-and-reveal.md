# Research Report 07 — Motion, Generation, and Reveal as a Signature Experience

**Builds on:** Report 06 defined a restrained, image-led premium art direction. Cursor's Explore cleanup is now complete and validated as the current interaction baseline.

## Executive conclusion

Motion should be concentrated where it explains cause, progress, and transition. The premium signature should be the **interpretation → generation → artwork reveal** sequence, not constant ambient animation across the whole product.

Apple's current HIG recommends purposeful motion, warns against gratuitous animation, and emphasizes adapting for Reduce Motion. Flutter's adaptive guidance also reinforces touch-first design and portable interaction logic.

## Canonical motion rules

1. **Motion must explain state or causality.** If it does not show what changed, where content came from, or what action succeeded, remove it.
2. **Selection feedback is immediate.** Song DNA and Explore cards should visibly respond on press and resolve selected state without delay.
3. **Navigation uses one transition language.** Focused creation steps should feel spatially consistent rather than mixing arbitrary fades, zooms, and bounces.
4. **Loading keeps layout stable.** Avoid large jumps when AI options arrive.
5. **Reduced motion is a first-class mode.** Replace travel/scale with opacity or direct state changes where appropriate.

## Explore interaction sequence

Current cleaned-up behavior is a strong base:

- `Generate for me` remains primary
- `Explore options` is secondary
- three cards appear
- first has a Recommended chip
- full-card selection
- selected state is explicit
- `Create this direction` appears after selection
- legacy style grid collapses while an AI direction is active

Next premium pass should add only subtle motion:

- button → loading state transition
- cards appear together or with a very short stagger
- selected card surface/border changes in ~150–220ms
- CTA appears without shifting unrelated content

Do not animate internal compatibility style data because it should remain invisible to customers.

## Generation progress

Avoid fake percentages.

Use two layers:

### A. Truthful system stage
Only display stages the backend can genuinely distinguish, translated into friendly language.

Possible labels:

- Preparing your creation
- Building the image
- Finishing details

### B. Song DNA context
Display the selected DNA concepts as stable context, not as fake per-element progress.

Example:

- Emotional core — grief + longing
- World — monumental night architecture
- Point of view — solitary figure

This reinforces product differentiation during waiting.

## Signature reveal sequence

Recommended direction:

1. generation state completes
2. supporting chrome quiets or recedes
3. artwork resolves as the dominant object
4. a short pause lets the image land
5. primary actions appear progressively
6. metadata and secondary actions follow

Action hierarchy after reveal:

- Save (often automatic/stateful rather than demanding a decision)
- Share
- Variation
- Reimagine

Avoid immediately surrounding the image with controls.

## Haptics and native future mapping

Web can simulate tactile confidence through pressed/selected states. Flutter/iOS later can add subtle haptics for:

- meaningful selection
- successful generation completion
- destructive confirmation

Do not design core meaning around haptics because desktop/web users will not have them.

## Timing philosophy

Suggested design ranges to prototype, not hard rules:

- tap/pressed response: immediate
- micro state transition: ~120–200ms
- panel/card entry: ~180–300ms
- navigation transition: ~220–350ms
- artwork reveal: ~300–500ms, restrained

Long theatrical sequences will feel slow after repeated use.

## Independent reviewer pass

**Risk:** the reveal can become a gimmick.

**Countermeasure:** keep it short, skip/reduce on repeated use if testing shows friction, and ensure the image is interactive immediately.

**Risk:** creative progress labels may imply technical stages that are not true.

**Countermeasure:** map copy to actual job-state boundaries. Song DNA context should never be presented as a percentage or ordered processing claim.

**Risk:** motion can perform well in browser prototypes but port poorly to Flutter.

**Countermeasure:** document every canonical animation in platform-neutral terms: trigger, start state, end state, duration range, easing intent, and reduced-motion alternative.

## Decisions passed to Report 08

Report 08 should define accessibility, performance, and invisible-quality gates so premium craft is measured beyond visual appearance.

## Sources

- Apple HIG Motion: https://developer.apple.com/design/human-interface-guidelines/motion
- Apple HIG Accessibility: https://developer.apple.com/design/human-interface-guidelines/accessibility
- Flutter adaptive best practices: https://docs.flutter.dev/ui/adaptive-responsive/best-practices
