import 'package:flutter/material.dart';
import 'db/app_database.dart';
import 'pages/login_page.dart';
import 'services/prefs.dart';
import 'services/localization_service.dart';

Future<void> main() async {
  try {
    WidgetsFlutterBinding.ensureInitialized();
    await Prefs.ensureDefaults();
    await LocalizationService.init();
    await AppDatabase.instance.database;
    await AppDatabase.instance.insertMockData();
  } catch (e) {
    debugPrint('Initialization Error: $e');
  }
  runApp(const TequilaApp());
}

class TequilaApp extends StatelessWidget {
  const TequilaApp({super.key});
  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder(
      valueListenable: LocalizationService.currentLocale,
      builder: (context, locale, child) {
        return MaterialApp(
          title: 'Tequila App',
          locale: locale,
          debugShowCheckedModeBanner:
              const bool.fromEnvironment('ADVANCED_DEBUG', defaultValue: false),
          theme: ThemeData(
              colorScheme: ColorScheme.fromSeed(seedColor: Colors.teal),
              useMaterial3: true),
          home: const LoginPage(),
        );
      },
    );
  }
}
