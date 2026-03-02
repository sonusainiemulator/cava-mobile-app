import 'package:flutter/material.dart';
import '../services/app_auth_service.dart';
import '../services/prefs.dart';
import 'home_page.dart';
import 'settings_page.dart';

class TokenLoginPage extends StatefulWidget {
  const TokenLoginPage({super.key});
  @override
  State<TokenLoginPage> createState() => _TokenLoginPageState();
}

class _TokenLoginPageState extends State<TokenLoginPage> {
  final _userCtl = TextEditingController(text: 'tequila');
  final _tokenCtl = TextEditingController(text: '');
  bool _loading = false;

  @override
  void initState() { super.initState(); _load(); }
  Future<void> _load() async {
    _userCtl.text = await Prefs.getApiUser();
    _tokenCtl.text = await Prefs.getApiToken();
    if (mounted) setState(() {});
  }

  Future<void> _login() async {
    setState(() => _loading = true);
    await AppAuthService.loginWithToken(apiUser: _userCtl.text.trim(), token: _tokenCtl.text.trim());
    if (!mounted) return;
    Navigator.of(context).pushReplacement(MaterialPageRoute(builder: (_) => const HomePage()));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Login API')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          TextField(controller: _userCtl, decoration: const InputDecoration(labelText: 'Usuario API')),
          const SizedBox(height: 10),
          TextField(controller: _tokenCtl, decoration: const InputDecoration(labelText: 'Token (Bearer)'), maxLines: 3),
          const SizedBox(height: 16),
          FilledButton(onPressed: _loading ? null : _login, child: _loading ? const SizedBox(width:18,height:18,child:CircularProgressIndicator(strokeWidth:2)) : const Text('Entrar')),
          TextButton(onPressed: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const SettingsPage())), child: const Text('Configurar URL/Endpoints')),
        ],
      ),
    );
  }
}
