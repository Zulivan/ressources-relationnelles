// import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
// import 'package:mockito/mockito.dart';

void main() {
  testWidgets('La naviggation est fonctionnelle', (WidgetTester tester) async {
    // Mock the API module
    // MockApiModule mockApiModule = MockApiModule();
    // List<Ressource> mockedResources = [
    //   // Create some mock resource data here
    // ];

    // // Set up the mockApiModule and the homeScreen widget
    // when(mockApiModule.request(String, String?, dynamic, Map<String, dynamic>?)).thenAnswer((_) async {
    //   return SampleResponse(200, {
    //     'hydra:member': mockedResources,
    //   });
    // });

    // // Pump the widget to build it.
    // await tester.pumpWidget(HomeScreen());

    // // Wait for the fetchResources function to complete
    // await tester.idle();
    // await tester.pump();

    // // Verify that the CircularProgressIndicator is not present
    // expect(find.byType(CircularProgressIndicator), findsNothing);

    // // Verify that the ListView contains the fetched resources
    // expect(find.byType(ListTile), findsNWidgets(mockedResources.length));
  });

  testWidgets('Les ressources sont affichées', (WidgetTester tester) async {

  });

  testWidgets('Les ressources sont cliquables', (WidgetTester tester) async {

  });

  testWidgets('On peut ajouter aux favorites', (WidgetTester tester) async {

  });

  testWidgets('On peut commenter en connecté', (WidgetTester tester) async {

  });
}