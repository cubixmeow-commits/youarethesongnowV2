import 'package:flutter/material.dart';

import '../../../../app/first_ruck_colors.dart';
import '../../../../app/first_ruck_theme.dart';
import '../../domain/onboarding_answer.dart';

class AnswerCard extends StatefulWidget {
  const AnswerCard({
    super.key,
    required this.answer,
    required this.selected,
    required this.onSelected,
  });

  final OnboardingAnswer answer;
  final bool selected;
  final ValueChanged<OnboardingAnswer> onSelected;

  @override
  State<AnswerCard> createState() => _AnswerCardState();
}

class _AnswerCardState extends State<AnswerCard> {
  bool _pressed = false;
  bool _focused = false;

  @override
  Widget build(BuildContext context) {
    final reduceMotion = FirstRuckTheme.reduceMotion(context);

    return Semantics(
      inMutuallyExclusiveGroup: true,
      checked: widget.selected,
      selected: widget.selected,
      label: widget.answer.label,
      child: FocusableActionDetector(
        onShowFocusHighlight: (focused) => setState(() => _focused = focused),
        actions: {
          ActivateIntent: CallbackAction<ActivateIntent>(
            onInvoke: (_) {
              widget.onSelected(widget.answer);
              return null;
            },
          ),
        },
        child: GestureDetector(
          behavior: HitTestBehavior.opaque,
          onTap: () => widget.onSelected(widget.answer),
          onTapDown: (_) => setState(() => _pressed = true),
          onTapUp: (_) => setState(() => _pressed = false),
          onTapCancel: () => setState(() => _pressed = false),
          child: AnimatedScale(
            scale: !reduceMotion && _pressed ? 0.98 : 1,
            duration: FirstRuckTheme.motionOf(
              context,
              FirstRuckTheme.motionFast,
            ),
            child: AnimatedContainer(
              duration: FirstRuckTheme.motionOf(
                context,
                FirstRuckTheme.motionFast,
              ),
              curve: Curves.easeOut,
              constraints: const BoxConstraints(
                minHeight: FirstRuckTheme.minTapTarget + 14,
              ),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              decoration: BoxDecoration(
                color: widget.selected
                    ? FirstRuckColors.selectedFill
                    : FirstRuckColors.answerFill,
                borderRadius: BorderRadius.circular(
                  FirstRuckTheme.controlRadius,
                ),
                border: Border.all(
                  color: _focused
                      ? FirstRuckColors.focus
                      : widget.selected
                      ? FirstRuckColors.forest
                      : FirstRuckColors.line,
                  width: _focused || widget.selected ? 1.5 : 1,
                ),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: ExcludeSemantics(
                      child: Text(
                        widget.answer.label,
                        style: FirstRuckTheme.body(FirstRuckColors.ink)
                            .copyWith(fontWeight: FontWeight.w600),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  ExcludeSemantics(
                    child: _RadioMark(selected: widget.selected),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _RadioMark extends StatelessWidget {
  const _RadioMark({required this.selected});

  final bool selected;

  @override
  Widget build(BuildContext context) {
    return AnimatedContainer(
      duration: FirstRuckTheme.motionOf(context, FirstRuckTheme.motionFast),
      width: 18,
      height: 18,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: selected ? FirstRuckColors.forest : Colors.transparent,
        border: Border.all(
          color: selected ? FirstRuckColors.forest : FirstRuckColors.muted,
          width: selected ? 4 : 1.5,
        ),
      ),
    );
  }
}
