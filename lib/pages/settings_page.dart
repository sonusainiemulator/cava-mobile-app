import 'package:flutter/material.dart';
import '../services/prefs.dart';
import '../services/localization_service.dart';

class SettingsPage extends StatefulWidget {
  const SettingsPage({super.key});
  @override
  State<SettingsPage> createState() => _SettingsPageState();
}

class _SettingsPageState extends State<SettingsPage> {
  final _base = TextEditingController();
  final _user = TextEditingController();
  final _token = TextEditingController();
  final Map<String, TextEditingController> _eps = {};
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    await Prefs.ensureDefaults();
    _base.text = await Prefs.getApiBaseUrl();
    _user.text = await Prefs.getApiUser();
    _token.text = await Prefs.getApiToken();

    final keys = [
      Prefs.epLogin,
      Prefs.epMe, Prefs.epBanners, Prefs.epEvents, Prefs.epCavaItems, Prefs.epTequila, Prefs.epMezcal,
      Prefs.epUsers, Prefs.epChatThread, Prefs.epChatMessages, Prefs.epScanUpload
    ];
    final labels = {
      Prefs.epLogin: '/auth/login',
      Prefs.epMe: '/me',
      Prefs.epBanners: '/banners',
      Prefs.epEvents: '/events',
      Prefs.epCavaItems: '/cava/items',
      Prefs.epTequila: '/content/tequila',
      Prefs.epMezcal: '/content/mezcal',
      Prefs.epUsers: '/users (search)',
      Prefs.epChatThread: '/chat/thread',
      Prefs.epChatMessages: '/chat/thread/{id}/messages',
      Prefs.epScanUpload: '/scan',
    };
    for (final k in keys) {
      _eps[k] = TextEditingController(text: await Prefs.getEndpoint(k, labels[k] ?? ''));
    }
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _save() async {
    await Prefs.setApiBaseUrl(_base.text.trim());
    await Prefs.setApiUser(_user.text.trim());
    await Prefs.setApiToken(_token.text.trim());
    for (final e in _eps.entries) { await Prefs.setEndpoint(e.key, e.value.text.trim()); }
    if (!mounted) return;
    Navigator.of(context).pop();
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    return Scaffold(
      appBar: AppBar(title: Text(LocalizationService.tr('settings_title'))),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _buildLanguageSwitcher(),
          const SizedBox(height: 16),
          TextField(controller: _base, decoration: const InputDecoration(labelText: 'API Base URL')),
          const SizedBox(height: 10),
          TextField(controller: _user, decoration: InputDecoration(labelText: LocalizationService.tr('login_hint_email'))),
          const SizedBox(height: 10),
          TextField(controller: _token, decoration: const InputDecoration(labelText: 'Token'), maxLines: 3),
          const SizedBox(height: 16),
          const Text('Endpoints', style: TextStyle(fontWeight: FontWeight.bold)),
          const SizedBox(height: 10),
          ..._eps.entries.map((e) => Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: TextField(controller: e.value, decoration: InputDecoration(labelText: e.key)),
          )),
          const SizedBox(height: 16),
          FilledButton(onPressed: _save, child: Text(LocalizationService.tr('login_btn'))), // Reusing login/save
        ],
      ),
    );
  }

  Widget _buildLanguageSwitcher() {
    return ValueListenableBuilder(
      valueListenable: LocalizationService.currentLocale,
      builder: (context, locale, child) {
        return Card(
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                Text(LocalizationService.tr('settings_lang'), style: const TextStyle(fontWeight: FontWeight.bold)),
                const Spacer(),
                SegmentedButton<String>(
                  segments: const [
                    ButtonSegment(value: 'es', label: Text('Español')),
                    ButtonSegment(value: 'en', label: Text('English')),
                  ],
                  selected: {locale.languageCode},
                  onSelectionChanged: (Set<String> newSelection) {
                    LocalizationService.changeLocale(newSelection.first);
                  },
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
