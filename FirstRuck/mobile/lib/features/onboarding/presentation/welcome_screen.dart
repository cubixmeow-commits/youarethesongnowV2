import 'package:flutter/material.dart';

import '../../../app/first_ruck_app.dart';
import '../../../app/first_ruck_colors.dart';
import '../../../app/first_ruck_theme.dart';
import '../../../shared/widgets/brand_lockup.dart';
import '../../../shared/widgets/delayed_reveal.dart';
import '../../../shared/widgets/first_ruck_scaffold.dart';
import '../../../shared/widgets/primary_action_button.dart';
import '../../../shared/widgets/route_stroke.dart';
import '../../../shared/widgets/topo_background.dart';

abstract final class WelcomeCopy {
  static const eyebrow = 'A beginner plan built around you';
  static const headline = 'Start where you are.\nCarry forward.';
  static const summary =
      'Tell us what feels comfortable. First Ruck will shape your first weeks and find nearby routes that fit.';
  static const primaryAction = 'Build my plan';
  static const expectation = 'About 4 minutes · You control location access';
}

class WelcomeScreen extends StatelessWidget {
  const WelcomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return FirstRuckScaffold(
      surface: FirstRuckSurface.forest,
      background: const _WelcomeAtmosphere(),
      minimumPadding: const EdgeInsets.symmetric(
        horizontal: FirstRuckTheme.pagePadding,
        vertical: 8,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const DelayedReveal(
            delay: Duration(milliseconds: 80),
            child: BrandLockup(light: true),
          ),
          Expanded(
            child: LayoutBuilder(
              builder: (context, constraints) {
                return SingleChildScrollView(
                  child: ConstrainedBox(
                    constraints: BoxConstraints(
                      minHeight: constraints.maxHeight,
                    ),
                    child: const Align(
                      alignment: Alignment.bottomLeft,
                      child: _WelcomeCopy(),
                    ),
                  ),
                );
              },
            ),
          ),
          const SizedBox(height: 24),
          DelayedReveal(
            delay: const Duration(milliseconds: 320),
            child: _WelcomeActions(
              onStart: () {
                Navigator.of(context).pushNamed(FirstRuckApp.onboardingRoute);
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _WelcomeAtmosphere extends StatelessWidget {
  const _WelcomeAtmosphere();

  @override
  Widget build(BuildContext context) {
    final height = MediaQuery.sizeOf(context).height;

    return Stack(
      fit: StackFit.expand,
      children: [
        const ColoredBox(color: FirstRuckColors.forest),
        const DecoratedBox(
          decoration: BoxDecoration(
            gradient: RadialGradient(
              center: Alignment(0.55, -0.42),
              radius: 0.85,
              colors: [FirstRuckColors.glow, Color(0x0014331D)],
            ),
          ),
        ),
        const DecoratedBox(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [Color(0x14101612), Color(0xA608140C)],
            ),
          ),
        ),
        Positioned(
          top: height * 0.08,
          left: -28,
          right: -64,
          height: height * 0.46,
          child: IgnorePointer(
            child: ShaderMask(
              blendMode: BlendMode.dstIn,
              shaderCallback: (rect) {
                return const LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [Colors.white, Colors.white, Color(0x00FFFFFF)],
                  stops: [0, 0.62, 1],
                ).createShader(rect);
              },
              child: const Stack(
                fit: StackFit.expand,
                children: [TopoBackground(), AnimatedRouteStroke()],
              ),
            ),
          ),
        ),
      ],
    );
  }
}

class _WelcomeCopy extends StatelessWidget {
  const _WelcomeCopy();

  @override
  Widget build(BuildContext context) {
    return ConstrainedBox(
      constraints: const BoxConstraints(maxWidth: 360),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          DelayedReveal(
            delay: const Duration(milliseconds: 140),
            child: Text(
              WelcomeCopy.eyebrow.toUpperCase(),
              style: FirstRuckTheme.eyebrow(FirstRuckColors.welcomeEyebrow),
            ),
          ),
          const SizedBox(height: 12),
          DelayedReveal(
            delay: const Duration(milliseconds: 200),
            child: Semantics(
              header: true,
              child: Text(
                WelcomeCopy.headline,
                style: FirstRuckTheme.display(
                  context,
                  size: FirstRuckTheme.welcomeDisplaySize(context),
                ),
              ),
            ),
          ),
          const SizedBox(height: 20),
          DelayedReveal(
            delay: const Duration(milliseconds: 260),
            child: ConstrainedBox(
              constraints: BoxConstraints(maxWidth: 280),
              child: Text(
                WelcomeCopy.summary,
                style: TextStyle(
                  fontSize: 17,
                  height: 1.45,
                  color: FirstRuckColors.welcomeSummary,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _WelcomeActions extends StatelessWidget {
  const _WelcomeActions({required this.onStart});

  final VoidCallback onStart;

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        PrimaryActionButton(
          key: const Key('welcome-start'),
          label: WelcomeCopy.primaryAction,
          onPressed: onStart,
        ),
        const SizedBox(height: 12),
        const Text(
          WelcomeCopy.expectation,
          textAlign: TextAlign.center,
          style: TextStyle(
            fontSize: 13,
            height: 1.35,
            color: FirstRuckColors.welcomeFootnote,
          ),
        ),
      ],
    );
  }
}
