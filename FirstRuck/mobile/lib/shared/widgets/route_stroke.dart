import 'package:flutter/material.dart';

import '../../app/first_ruck_colors.dart';
import '../../app/first_ruck_theme.dart';
import 'welcome_map_geometry.dart';

class RouteStroke extends StatelessWidget {
  const RouteStroke({super.key, required this.progress});

  /// 0 draws nothing; 1 draws the complete route.
  final double progress;

  @override
  Widget build(BuildContext context) {
    return ExcludeSemantics(
      child: CustomPaint(
        key: const ValueKey('welcome-route'),
        painter: RouteStrokePainter(progress: progress.clamp(0.0, 1.0)),
        child: const SizedBox.expand(),
      ),
    );
  }
}

class RouteStrokePainter extends CustomPainter {
  const RouteStrokePainter({required this.progress});

  final double progress;

  @override
  void paint(Canvas canvas, Size size) {
    final route = WelcomeMapGeometry.transform(
      WelcomeMapGeometry.route(),
      size,
    );
    final start = WelcomeMapGeometry.transformOffset(
      WelcomeMapGeometry.start,
      size,
    );
    final end = WelcomeMapGeometry.transformOffset(
      WelcomeMapGeometry.end,
      size,
    );
    final visible = _visiblePath(route, progress);

    final routePaint = Paint()
      ..color = FirstRuckColors.orange
      ..style = PaintingStyle.stroke
      ..strokeWidth = 3.5
      ..strokeCap = StrokeCap.round
      ..strokeJoin = StrokeJoin.round
      ..isAntiAlias = true;

    canvas.drawPath(visible, routePaint);

    _drawPoint(
      canvas,
      start,
      radius: 7,
      fill: FirstRuckColors.forest,
      ring: FirstRuckColors.paper,
    );
    if (progress >= 0.98) {
      _drawPoint(
        canvas,
        end,
        radius: 9,
        fill: FirstRuckColors.orange,
        ring: FirstRuckColors.paper,
      );
    }
  }

  Path _visiblePath(Path source, double amount) {
    if (amount <= 0) return Path();
    if (amount >= 1) return source;

    final visible = Path();
    for (final metric in source.computeMetrics()) {
      visible.addPath(
        metric.extractPath(0, metric.length * amount),
        Offset.zero,
      );
    }
    return visible;
  }

  void _drawPoint(
    Canvas canvas,
    Offset center, {
    required double radius,
    required Color fill,
    required Color ring,
  }) {
    canvas.drawCircle(center, radius, Paint()..color = fill);
    canvas.drawCircle(
      center,
      radius,
      Paint()
        ..color = ring
        ..style = PaintingStyle.stroke
        ..strokeWidth = 3,
    );
  }

  @override
  bool shouldRepaint(covariant RouteStrokePainter oldDelegate) {
    return oldDelegate.progress != progress;
  }
}

class AnimatedRouteStroke extends StatefulWidget {
  const AnimatedRouteStroke({super.key});

  @override
  State<AnimatedRouteStroke> createState() => _AnimatedRouteStrokeState();
}

class _AnimatedRouteStrokeState extends State<AnimatedRouteStroke>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _progress;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: FirstRuckTheme.routeDrawDelay + FirstRuckTheme.routeDraw,
    );
    _progress = CurvedAnimation(
      parent: _controller,
      curve: Interval(
        FirstRuckTheme.routeDrawDelay.inMilliseconds /
            (FirstRuckTheme.routeDrawDelay + FirstRuckTheme.routeDraw)
                .inMilliseconds,
        1,
        curve: Curves.easeOut,
      ),
    );
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (FirstRuckTheme.reduceMotion(context)) {
      _controller.value = 1;
      return;
    }
    if (_controller.value == 0 && !_controller.isAnimating) {
      _controller.forward();
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _progress,
      builder: (context, _) => RouteStroke(progress: _progress.value),
    );
  }
}
