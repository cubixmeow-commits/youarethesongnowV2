import 'package:flutter/material.dart';

import '../features/onboarding/data/onboarding_fixture_repository.dart';
import '../features/onboarding/presentation/onboarding_screen.dart';
import '../features/onboarding/presentation/welcome_screen.dart';
import 'first_ruck_theme.dart';

class FirstRuckApp extends StatelessWidget {
  const FirstRuckApp({
    super.key,
    this.repository = const OnboardingFixtureRepository(),
  });

  static const welcomeRoute = '/';
  static const onboardingRoute = '/onboarding';

  final OnboardingFixtureRepository repository;

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'First Ruck',
      debugShowCheckedModeBanner: false,
      theme: FirstRuckTheme.theme(),
      initialRoute: Navigator.defaultRouteName,
      onGenerateRoute: (settings) {
        switch (settings.name) {
          case onboardingRoute:
            return MaterialPageRoute<void>(
              settings: settings,
              builder: (_) => OnboardingScreen(repository: repository),
            );
          case welcomeRoute:
          default:
            return MaterialPageRoute<void>(
              settings: settings,
              builder: (_) => const WelcomeScreen(),
            );
        }
      },
    );
  }
}
