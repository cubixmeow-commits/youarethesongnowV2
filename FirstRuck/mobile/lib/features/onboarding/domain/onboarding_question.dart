import 'onboarding_answer.dart';

final class OnboardingQuestion {
  const OnboardingQuestion({
    required this.id,
    required this.kicker,
    required this.title,
    required this.help,
    required this.number,
    required this.total,
    required this.options,
  });

  final String id;
  final String kicker;
  final String title;
  final String help;
  final int number;
  final int total;
  final List<OnboardingAnswer> options;
}
