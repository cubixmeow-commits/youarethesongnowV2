import 'package:flutter/material.dart';

import '../../../../app/first_ruck_colors.dart';
import '../../../../app/first_ruck_theme.dart';

class ProgressLine extends StatelessWidget {
  const ProgressLine({
    super.key,
    required this.value,
    required this.semanticsLabel,
  });

  final double value;
  final String semanticsLabel;

  @override
  Widget build(BuildContext context) {
    return Semantics(
      label: semanticsLabel,
      value: '${(value * 100).round()} percent',
      child: ExcludeSemantics(
        child: ClipRRect(
          borderRadius: BorderRadius.circular(999),
          child: SizedBox(
            height: 3,
            child: Stack(
              fit: StackFit.expand,
              children: [
                const ColoredBox(color: FirstRuckColors.paperDeep),
                Align(
                  alignment: Alignment.centerLeft,
                  child: AnimatedFractionallySizedBox(
                    duration: FirstRuckTheme.motionOf(
                      context,
                      FirstRuckTheme.motionScreen,
                    ),
                    curve: Curves.easeOut,
                    widthFactor: value.clamp(0.0, 1.0),
                    child: const ColoredBox(color: FirstRuckColors.orange),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
