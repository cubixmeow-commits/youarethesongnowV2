import 'package:flutter/material.dart';

import '../../../app/first_ruck_colors.dart';
import '../../../app/first_ruck_theme.dart';
import '../../../shared/widgets/first_ruck_scaffold.dart';
import '../../../shared/widgets/primary_action_button.dart';
import '../data/onboarding_fixture_repository.dart';
import '../domain/onboarding_answer.dart';
import '../domain/onboarding_question.dart';
import 'widgets/answer_card.dart';
import 'widgets/flow_header.dart';
import 'widgets/inline_message.dart';
import 'widgets/progress_line.dart';

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key, required this.repository});

  final OnboardingFixtureRepository repository;

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  late final OnboardingQuestion _question;
  String? _selectedValue;
  String _error = '';

  @override
  void initState() {
    super.initState();
    _question = widget.repository.firstQuestion();
  }

  void _select(OnboardingAnswer answer) {
    setState(() {
      _selectedValue = answer.value;
      _error = '';
    });
  }

  void _continue() {
    if (_selectedValue == null) {
      setState(() => _error = 'Choose one answer to continue.');
      return;
    }
  }

  @override
  Widget build(BuildContext context) {
    final progress = _question.number / _question.total;

    return FirstRuckScaffold(
      surface: FirstRuckSurface.paper,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 20, 0),
            child: FlowHeader(
              stepLabel: '${_question.number} of ${_question.total}',
              onBack: () => Navigator.of(context).pop(),
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: ProgressLine(
              value: progress,
              semanticsLabel: 'Onboarding progress',
            ),
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(
                FirstRuckTheme.pagePadding,
                32,
                FirstRuckTheme.pagePadding,
                0,
              ),
              child: Column(
                children: [
                  Expanded(
                    child: ListView(
                      padding: EdgeInsets.zero,
                      children: [
                        _QuestionCopy(question: _question),
                        const SizedBox(height: 28),
                        for (final answer in _question.options) ...[
                          AnswerCard(
                            answer: answer,
                            selected: _selectedValue == answer.value,
                            onSelected: _select,
                          ),
                          const SizedBox(height: 12),
                        ],
                      ],
                    ),
                  ),
                  InlineMessage(message: _error),
                  Padding(
                    padding: const EdgeInsets.only(top: 8, bottom: 8),
                    child: PrimaryActionButton(
                      key: const Key('onboarding-continue'),
                      label: 'Continue',
                      onPressed: _continue,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _QuestionCopy extends StatelessWidget {
  const _QuestionCopy({required this.question});

  final OnboardingQuestion question;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          question.kicker.toUpperCase(),
          style: FirstRuckTheme.eyebrow(FirstRuckColors.orangeDeep),
        ),
        const SizedBox(height: 10),
        Semantics(
          header: true,
          child: Text(
            question.title,
            style: FirstRuckTheme.screenTitle(context),
          ),
        ),
        const SizedBox(height: 16),
        ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 340),
          child: Text(
            question.help,
            style: FirstRuckTheme.body(FirstRuckColors.inkSoft),
          ),
        ),
      ],
    );
  }
}
