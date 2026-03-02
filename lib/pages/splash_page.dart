import 'package:flutter/material.dart';
import '../services/prefs.dart';
import 'home_page.dart';
import 'token_login_page.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});
  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() { super.initState(); _go(); }

  Future<void> _go() async {
    final logged = await Prefs.isLoggedIn();
    if (!mounted) return;
    Navigator.of(context).pushReplacement(MaterialPageRoute(builder: (_) => logged ? const HomePage() : const TokenLoginPage()));
  }

  @override
  Widget build(BuildContext context) => const Scaffold(body: Center(child: CircularProgressIndicator()));
}
