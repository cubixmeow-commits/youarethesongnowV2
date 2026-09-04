import 'package:flutter/material.dart';

import '../../app/first_ruck_colors.dart';
import '../../app/first_ruck_theme.dart';

class PrimaryActionButton extends StatefulWidget {
  const PrimaryActionButton({
    super.key,
    required this.label,
    required this.onPressed,
  });

  final String label;
  final VoidCallback onPressed;

  @override
  State<PrimaryActionButton> createState() => _PrimaryActionButtonState();
}

class _PrimaryActionButtonState extends State<PrimaryActionButton> {
  bool _pressed = false;

  @override
  Widget build(BuildContext context) {
    final reduceMotion = FirstRuckTheme.reduceMotion(context);

    return Semantics(
      button: true,
      label: widget.label,
      excludeSemantics: true,
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: widget.onPressed,
          onHighlightChanged: (pressed) => setState(() => _pressed = pressed),
          overlayColor: const WidgetStatePropertyAll(Colors.transparent),
          borderRadius: BorderRadius.circular(FirstRuckTheme.controlRadius),
          child: AnimatedScale(
            scale: !reduceMotion && _pressed ? 0.96 : 1,
            duration: FirstRuckTheme.motionOf(
              context,
              FirstRuckTheme.motionFast,
            ),
            curve: Curves.easeOut,
            child: AnimatedContainer(
              duration: FirstRuckTheme.motionOf(
                context,
                FirstRuckTheme.motionFast,
              ),
              curve: Curves.easeOut,
              constraints: const BoxConstraints(
                minHeight: FirstRuckTheme.primaryButtonHeight,
                minWidth: FirstRuckTheme.minTapTarget,
              ),
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
              decoration: BoxDecoration(
                color: _pressed
                    ? FirstRuckColors.orangeDeep
                    : FirstRuckColors.orange,
                borderRadius: BorderRadius.circular(
                  FirstRuckTheme.controlRadius,
                ),
                boxShadow: const [
                  BoxShadow(
                    color: Color(0x38C73700),
                    blurRadius: 16,
                    offset: Offset(0, 7),
                  ),
                ],
              ),
              alignment: Alignment.center,
              child: Text(
                widget.label,
                textAlign: TextAlign.center,
                style: FirstRuckTheme.buttonLabel(FirstRuckColors.forestDeep),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
