import 'package:first_ruck/app/first_ruck_app.dart';
import 'package:first_ruck/features/onboarding/presentation/welcome_screen.dart';
import 'package:first_ruck/features/onboarding/presentation/widgets/answer_card.dart';
import 'package:first_ruck/shared/widgets/primary_action_button.dart';
import 'package:first_ruck/shared/widgets/route_stroke.dart';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  Future<void> pumpApp(
    WidgetTester tester, {
    bool reduceMotion = true,
    Size size = const Size(390, 844),
    double textScale = 1,
  }) async {
    tester.view.physicalSize = size;
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.resetPhysicalSize);
    addTearDown(tester.view.resetDevicePixelRatio);

    tester.platformDispatcher.textScaleFactorTestValue = textScale;
    addTearDown(tester.platformDispatcher.clearTextScaleFactorTestValue);

    tester.platformDispatcher.accessibilityFeaturesTestValue =
        FakeAccessibilityFeatures(disableAnimations: reduceMotion);
    addTearDown(tester.platformDispatcher.clearAccessibilityFeaturesTestValue);

    await tester.pumpWidget(const FirstRuckApp());
    await tester.pumpAndSettle();
  }

  testWidgets('welcome screen shows the First Ruck starting copy', (
    tester,
  ) async {
    await pumpApp(tester);

    expect(find.text('First Ruck'), findsWidgets);
    expect(find.text(WelcomeCopy.primaryAction), findsOneWidget);
    expect(find.textContaining('Carry forward.'), findsOneWidget);
    expect(find.text(WelcomeCopy.expectation), findsOneWidget);

    final button = tester.getSize(find.byType(PrimaryActionButton));
    expect(button.height, greaterThanOrEqualTo(44));
  });

  testWidgets('Build my plan opens the first question and Back returns', (
    tester,
  ) async {
    await pumpApp(tester);

    await tester.ensureVisible(find.byKey(const Key('welcome-start')));
    await tester.tap(find.byKey(const Key('welcome-start')));
    await tester.pumpAndSettle();

    expect(find.text('What would make rucking worthwhile?'), findsOneWidget);
    expect(find.text('1 of 12'), findsOneWidget);
    expect(find.text('Build everyday fitness'), findsOneWidget);
    expect(find.text('Spend more time outside'), findsOneWidget);
    expect(find.text('Clear my head'), findsOneWidget);
    expect(find.text('Prepare for a challenge'), findsOneWidget);

    await tester.tap(find.byKey(const Key('onboarding-back')));
    await tester.pumpAndSettle();

    expect(find.text(WelcomeCopy.primaryAction), findsOneWidget);
    expect(find.text('What would make rucking worthwhile?'), findsNothing);
  });

  testWidgets('Reduce Motion draws the welcome route immediately', (
    tester,
  ) async {
    await pumpApp(tester);

    final painters = tester
        .widgetList<CustomPaint>(find.byType(CustomPaint))
        .map((paint) => paint.painter)
        .whereType<RouteStrokePainter>();

    expect(painters, isNotEmpty);
    expect(painters.every((painter) => painter.progress == 1), isTrue);
  });

  testWidgets('first question validates before continuing', (tester) async {
    await pumpApp(tester);

    await tester.ensureVisible(find.byKey(const Key('welcome-start')));
    await tester.tap(find.byKey(const Key('welcome-start')));
    await tester.pumpAndSettle();

    await tester.tap(find.byKey(const Key('onboarding-continue')));
    await tester.pump();

    expect(find.text('Choose one answer to continue.'), findsOneWidget);

    final card = find.byType(AnswerCard).first;
    expect(tester.getSize(card).height, greaterThanOrEqualTo(44));

    await tester.tap(find.text('Build everyday fitness'));
    await tester.pump();

    expect(find.text('Choose one answer to continue.'), findsNothing);
    expect(find.text('What would make rucking worthwhile?'), findsOneWidget);
  });

  testWidgets('welcome remains usable at a large text scale', (tester) async {
    await pumpApp(tester, textScale: 2, size: const Size(320, 680));

    expect(find.text(WelcomeCopy.primaryAction), findsOneWidget);
    expect(find.textContaining('Carry forward.'), findsOneWidget);

    await tester.ensureVisible(find.byKey(const Key('welcome-start')));
    await tester.tap(find.byKey(const Key('welcome-start')));
    await tester.pumpAndSettle();

    expect(find.text('What would make rucking worthwhile?'), findsOneWidget);
    await tester.ensureVisible(find.byKey(const Key('onboarding-back')));
  });
}
