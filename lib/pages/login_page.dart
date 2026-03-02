import 'package:flutter/material.dart';
import '../services/perfex_auth_service.dart';
import '../services/prefs.dart';
import '../services/localization_service.dart';
import 'home_page.dart';
import 'settings_page.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _email = TextEditingController();
  final _password = TextEditingController();
  bool _loading = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _prefill();
  }

  Future<void> _prefill() async {
    // Development phase autofill
    _email.text = 'admin01@3ware.mx';
    _password.text = 'Admin*246';

    final u = await Prefs.getApiUser();
    if (u.isNotEmpty && _email.text.isEmpty) _email.text = u;

    if (mounted) setState(() {});
  }

  Future<void> _login() async {
    final email = _email.text.trim();
    final pass = _password.text;

    if (email.isEmpty || pass.isEmpty) {
      setState(() => _error = LocalizationService.tr('login_error_empty'));
      return;
    }

    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      // Login REAL via PerfexAuthService (handles cookies, CSRF, and Eventos token)
      await PerfexAuthService.login(username: email, password: pass);

      if (!mounted) return;
      Navigator.of(context)
          .pushReplacement(MaterialPageRoute(builder: (_) => const HomePage()));
    } catch (e) {
      setState(() => _error = 'Login failed: ${e.toString()}');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF1F4E79),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 28),
            child: Column(
              children: [
                Align(
                  alignment: Alignment.topRight,
                  child: _buildLanguageSwitcher(),
                ),
                const SizedBox(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Image.asset('assets/icon/icon.png', width: 44, height: 44),
                      const SizedBox(width: 12),
                      const Text(
                        'CAVA APP',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 34,
                          fontWeight: FontWeight.w800,
                          letterSpacing: 1.1,
                        ),
                      ),
                    ],
                  ),
                const SizedBox(height: 34),
                Text(
                  LocalizationService.tr('login_title'),
                  style: const TextStyle(
                      color: Colors.white,
                      fontSize: 28,
                      fontWeight: FontWeight.w700),
                ),
                const SizedBox(height: 34),
                _input(
                  controller: _email,
                  hint: LocalizationService.tr('login_hint_email'),
                  icon: Icons.email_outlined,
                  keyboardType: TextInputType.emailAddress,
                ),
                const SizedBox(height: 18),
                _input(
                  controller: _password,
                  hint: LocalizationService.tr('login_hint_pass'),
                  icon: Icons.lock_outline,
                  obscure: true,
                ),
                const SizedBox(height: 28),
                SizedBox(
                  width: double.infinity,
                  height: 64,
                  child: ElevatedButton(
                    onPressed: _loading ? null : _login,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF7AD49B),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(36)),
                      elevation: 0,
                    ),
                    child: _loading
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(
                                color: Colors.white, strokeWidth: 2))
                        : Text(
                            LocalizationService.tr('login_btn'),
                            style: const TextStyle(
                                fontSize: 20,
                                color: Colors.white,
                                fontWeight: FontWeight.w700),
                          ),
                  ),
                ),
                const SizedBox(height: 10),
                if (const bool.fromEnvironment('ADVANCED_DEBUG',
                    defaultValue: false))
                  TextButton(
                    onPressed: () => Navigator.of(context).push(
                        MaterialPageRoute(
                            builder: (_) => const SettingsPage())),
                    child: Text(LocalizationService.tr('login_config'),
                        style: const TextStyle(color: Colors.white70)),
                  ),
                if (_error != null) ...[
                  const SizedBox(height: 8),
                  Text(_error!,
                      textAlign: TextAlign.center,
                      style: const TextStyle(color: Colors.redAccent)),
                ],
                const SizedBox(height: 42),
                Text(
                  LocalizationService.tr('login_welcome'),
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                      color: Colors.white70, fontSize: 18, height: 1.25),
                ),
                const SizedBox(height: 16),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildLanguageSwitcher() {
    return ValueListenableBuilder(
      valueListenable: LocalizationService.currentLocale,
      builder: (context, locale, child) {
        return Container(
          decoration: BoxDecoration(
            color: Colors.white24,
            borderRadius: BorderRadius.circular(20),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              _langBtn('Español', 'es', locale.languageCode == 'es'),
              _langBtn('English', 'en', locale.languageCode == 'en'),
            ],
          ),
        );
      },
    );
  }

  Widget _langBtn(String label, String code, bool active) {
    return GestureDetector(
      onTap: () => LocalizationService.changeLocale(code),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(
          color: active ? Colors.white : Colors.transparent,
          borderRadius: BorderRadius.circular(16),
        ),
        child: Text(
          label,
          style: TextStyle(
            color: active ? const Color(0xFF1F4E79) : Colors.white70,
            fontWeight: FontWeight.bold,
            fontSize: 12,
          ),
        ),
      ),
    );
  }

  Widget _input({
    required TextEditingController controller,
    required String hint,
    required IconData icon,
    bool obscure = false,
    TextInputType? keyboardType,
  }) {
    return TextField(
      controller: controller,
      obscureText: obscure,
      keyboardType: keyboardType,
      style: const TextStyle(fontSize: 18),
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: const TextStyle(
            color: Color(0xFFB9B9B9), fontWeight: FontWeight.w600),
        prefixIcon: Icon(icon, color: const Color(0xFFB9B9B9)),
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(36),
          borderSide: BorderSide.none,
        ),
        contentPadding: const EdgeInsets.symmetric(vertical: 20),
      ),
    );
  }
}
