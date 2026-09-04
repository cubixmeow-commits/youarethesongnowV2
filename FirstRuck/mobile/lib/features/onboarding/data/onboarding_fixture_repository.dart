import '../domain/onboarding_answer.dart';
import '../domain/onboarding_question.dart';

/// Local fixture source for Milestone 1. Widgets depend on this type rather
/// than on copy or PHP endpoints so a later API repository can replace it.
final class OnboardingFixtureRepository {
  const OnboardingFixtureRepository();

  OnboardingQuestion firstQuestion() {
    return const OnboardingQuestion(
      id: 'goal',
      kicker: 'Your goal',
      title: 'What would make rucking worthwhile?',
      help: 'Choose the outcome you want to notice first.',
      number: 1,
      total: 12,
      options: [
        OnboardingAnswer(
          value: 'general-fitness',
          label: 'Build everyday fitness',
        ),
        OnboardingAnswer(
          value: 'outdoor-time',
          label: 'Spend more time outside',
        ),
        OnboardingAnswer(value: 'stress', label: 'Clear my head'),
        OnboardingAnswer(value: 'event', label: 'Prepare for a challenge'),
      ],
    );
  }
}
