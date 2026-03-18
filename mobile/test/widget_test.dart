// This is a basic Flutter widget test.
//
// To perform an interaction with a widget in your test, use the WidgetTester
// utility in the flutter_test package. For example, you can send tap and scroll
// gestures. You can also use WidgetTester to find child widgets in the widget
// tree, read text, and verify that the values of widget properties are correct.

import 'package:flutter_test/flutter_test.dart';

import 'package:mobile/main.dart';

void main() {
  testWidgets('starter page renders', (WidgetTester tester) async {
    await tester.pumpWidget(const PdmuomsApp());

    expect(find.text('PDMUOMS'), findsOneWidget);
    expect(find.text('Start building from here.'), findsOneWidget);
    expect(find.text('Next step'), findsOneWidget);
  });
}
