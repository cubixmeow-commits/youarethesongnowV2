import 'package:first_ruck/app/first_ruck_app.dart';
import 'package:first_ruck/features/onboarding/presentation/welcome_screen.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('app launches onto the welcome screen', (tester) async {
    await tester.pumpWidget(const FirstRuckApp());
    await tester.pumpAndSettle();

    expect(find.byType(WelcomeScreen), findsOneWidget);
    expect(find.text(WelcomeCopy.primaryAction), findsOneWidget);
  });
}
