import 'package:flutter/material.dart';

import '../../app/first_ruck_colors.dart';

class BrandLockup extends StatelessWidget {
  const BrandLockup({super.key, this.light = false, this.compact = false});

  final bool light;
  final bool compact;

  @override
  Widget build(BuildContext context) {
    final color = light ? FirstRuckColors.warmWhite : FirstRuckColors.ink;
    final markSize = compact ? 28.0 : 38.0;

    return Row(
      children: [
        ExcludeSemantics(
          child: Container(
            width: markSize,
            height: markSize,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: color),
            ),
            child: Text(
              'FR',
              style: TextStyle(
                color: color,
                fontSize: compact ? 10 : 11.5,
                fontWeight: FontWeight.w800,
                height: 1,
                letterSpacing: -0.4,
              ),
            ),
          ),
        ),
        const SizedBox(width: 12),
        Flexible(
          child: Text(
            'First Ruck',
            style: TextStyle(
              color: color,
              fontSize: 15,
              fontWeight: FontWeight.w800,
              letterSpacing: 0.3,
              height: 1.1,
            ),
          ),
        ),
      ],
    );
  }
}
