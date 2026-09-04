import 'package:flutter/material.dart';

import '../../app/first_ruck_colors.dart';
import 'welcome_map_geometry.dart';

class TopoBackground extends StatelessWidget {
  const TopoBackground({super.key});

  @override
  Widget build(BuildContext context) {
    return const ExcludeSemantics(
      child: CustomPaint(painter: _TopoPainter(), child: SizedBox.expand()),
    );
  }
}

class _TopoPainter extends CustomPainter {
  const _TopoPainter();

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = FirstRuckColors.contour
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.1
      ..isAntiAlias = true;

    for (final path in [
      WelcomeMapGeometry.contourA(),
      WelcomeMapGeometry.contourB(),
      WelcomeMapGeometry.contourC(),
    ]) {
      canvas.drawPath(WelcomeMapGeometry.transform(path, size), paint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
