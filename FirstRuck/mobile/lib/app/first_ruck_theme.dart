import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';

import 'first_ruck_colors.dart';

/// Visual tokens for the native shell.
///
/// Display type uses Georgia on iOS, matching the web system fallback until a
/// bundled licensed serif is chosen. Simulator Georgia metrics can sit a little
/// heavier than a later custom face; layout must not depend on exact glyph
/// widths.
abstract final class FirstRuckTheme {
  static const displayFamily = 'Georgia';
  static const pagePadding = 24.0;
  static const controlRadius = 14.0;
  static const minTapTarget = 44.0;
  static const primaryButtonHeight = 56.0;
  static const motionFast = Duration(milliseconds: 140);
  static const motionScreen = Duration(milliseconds: 220);
  static const motionEnter = Duration(milliseconds: 260);
  static const routeDraw = Duration(milliseconds: 1400);
  static const routeDrawDelay = Duration(milliseconds: 500);

  static ThemeData theme() {
    final textTheme = Typography.blackCupertino.apply(
      bodyColor: FirstRuckColors.ink,
      displayColor: FirstRuckColors.ink,
    );

    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      platform: TargetPlatform.iOS,
      colorScheme: const ColorScheme.light(
        primary: FirstRuckColors.orange,
        onPrimary: FirstRuckColors.forestDeep,
        secondary: FirstRuckColors.forest,
        onSecondary: FirstRuckColors.warmWhite,
        surface: FirstRuckColors.paper,
        onSurface: FirstRuckColors.ink,
        error: FirstRuckColors.error,
        onError: FirstRuckColors.warmWhite,
      ),
      scaffoldBackgroundColor: FirstRuckColors.forestDeep,
      canvasColor: FirstRuckColors.paper,
      textTheme: textTheme,
      splashFactory: NoSplash.splashFactory,
      highlightColor: Colors.transparent,
      focusColor: FirstRuckColors.focus.withValues(alpha: 0.18),
      pageTransitionsTheme: const PageTransitionsTheme(
        builders: {TargetPlatform.iOS: CupertinoPageTransitionsBuilder()},
      ),
    );
  }

  static TextStyle display(BuildContext context, {required double size}) {
    return TextStyle(
      fontFamily: displayFamily,
      fontFamilyFallback: const ['Times New Roman', 'serif'],
      fontSize: size,
      fontWeight: FontWeight.w400,
      height: 1.05,
      letterSpacing: size * -0.045,
      color: FirstRuckColors.warmWhite,
    );
  }

  static TextStyle screenTitle(BuildContext context) {
    return display(
      context,
      size: 38,
    ).copyWith(color: FirstRuckColors.ink, height: 1.08);
  }

  static TextStyle body(Color color) {
    return TextStyle(
      fontSize: 17,
      height: 1.45,
      fontWeight: FontWeight.w400,
      color: color,
    );
  }

  static TextStyle eyebrow(Color color) {
    return TextStyle(
      fontSize: 12,
      height: 1.2,
      fontWeight: FontWeight.w700,
      letterSpacing: 1.32,
      color: color,
    );
  }

  static TextStyle buttonLabel(Color color) {
    return TextStyle(
      fontSize: 17,
      height: 1.2,
      fontWeight: FontWeight.w700,
      color: color,
    );
  }

  static TextStyle caption(Color color) {
    return TextStyle(
      fontSize: 13,
      height: 1.35,
      fontWeight: FontWeight.w500,
      color: color,
    );
  }

  static double welcomeDisplaySize(BuildContext context) {
    final size = MediaQuery.sizeOf(context);
    return size.height < 700 || size.width < 360 ? 44.0 : 52.0;
  }

  static bool reduceMotion(BuildContext context) {
    return MediaQuery.disableAnimationsOf(context);
  }

  static Duration motionOf(BuildContext context, Duration duration) {
    return reduceMotion(context) ? Duration.zero : duration;
  }
}
