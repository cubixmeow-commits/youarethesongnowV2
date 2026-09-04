import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../app/first_ruck_colors.dart';

enum FirstRuckSurface { forest, paper }

class FirstRuckScaffold extends StatelessWidget {
  const FirstRuckScaffold({
    super.key,
    required this.surface,
    required this.child,
    this.background,
    this.minimumPadding = EdgeInsets.zero,
  });

  final FirstRuckSurface surface;
  final Widget child;
  final Widget? background;
  final EdgeInsets minimumPadding;

  @override
  Widget build(BuildContext context) {
    final isForest = surface == FirstRuckSurface.forest;
    final overlay = isForest
        ? SystemUiOverlayStyle.light.copyWith(
            statusBarColor: Colors.transparent,
            systemNavigationBarColor: FirstRuckColors.forest,
          )
        : SystemUiOverlayStyle.dark.copyWith(
            statusBarColor: Colors.transparent,
            systemNavigationBarColor: FirstRuckColors.paper,
          );

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: overlay,
      child: Scaffold(
        backgroundColor: isForest
            ? FirstRuckColors.forest
            : FirstRuckColors.paper,
        body: Stack(
          fit: StackFit.expand,
          children: [
            ?background,
            SafeArea(minimum: minimumPadding, child: child),
          ],
        ),
      ),
    );
  }
}
