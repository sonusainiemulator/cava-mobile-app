import 'package:dio/dio.dart';
import 'package:html/parser.dart' as html;
import 'api_client.dart';
import 'prefs.dart';

import 'data_sync_service.dart';

import 'eventos_service.dart';

class PerfexAuthService {
  static Future<void> login({
    required String username,
    required String password,
  }) async {
    // Derive base ERP URL from API Base URL
    String apiBase = await Prefs.getApiBaseUrl();
    String baseErpUrl = apiBase;
    if (baseErpUrl.endsWith('/admin/api')) {
      baseErpUrl = baseErpUrl.substring(0, baseErpUrl.length - 10);
    } else if (baseErpUrl.endsWith('/api')) {
      baseErpUrl = baseErpUrl.substring(0, baseErpUrl.length - 4);
    }
    if (baseErpUrl.endsWith('/')) {
      baseErpUrl = baseErpUrl.substring(0, baseErpUrl.length - 1);
    }

    // Force production perfex admin URL if it's the cavaapp domain
    if (baseErpUrl.contains('cavaapp.3ware.com.mx')) {
      baseErpUrl = 'https://3ware.com.mx/tequila/erp';
    }

    final dio = await ApiClient.instance.dio();

    final loginHtml = await _fetchLoginHtml(dio, baseErpUrl);
    final csrf = _extractCsrf(loginHtml);
    if (csrf == null) {
      throw Exception('No se encontró CSRF en el HTML de login.');
    }

    final csrfName = csrf.$1;
    final csrfValue = csrf.$2;

    final resp = await dio.post(
      "$baseErpUrl/admin/authentication",
      data: {
        'email': username, // Compatibility
        'username': username, // Some setups use username
        'password': password,
        'remember': 'on',
        csrfName: csrfValue,
      },
      options: Options(contentType: Headers.formUrlEncodedContentType),
    );

    // Checks
    final code = resp.statusCode ?? 0;

    // Login to API Service (Backend Token)
    // We try this regardless of cookie login outcome to ensure we get a token if credentials are valid
    // But logically, we should only do it if cookie login succeeds OR if status is 200/302.
    // Let's do it on success paths.

    Future<void> doApiLogin() async {
      await EventosService.login(username, password);
    }

    if (code >= 300 && code < 400) {
      final loc = (resp.headers.value('location') ?? '').toLowerCase();
      if (loc.contains('authentication')) {
        throw Exception(
            'Login falló (HTTP $code) redirige a login: ${resp.headers.value('location') ?? ''}');
      }
      final ok = await _verifyLoggedIn(dio, baseErpUrl);
      if (ok) {
        await Prefs.setLoggedIn(true);
        await Prefs.setApiUser(username);
        await doApiLogin();
        DataSyncService.instance.syncAll();
        return;
      }
      await Prefs.setLoggedIn(true);
      await Prefs.setApiUser(username);
      await doApiLogin();
      DataSyncService.instance.syncAll();
      return;
    }

    final ok = await _verifyLoggedIn(dio, baseErpUrl);
    if (ok) {
      await Prefs.setLoggedIn(true);
      await Prefs.setApiUser(username);
      await doApiLogin();
      DataSyncService.instance.syncAll();
      return;
    }

    throw Exception(
        'Login falló (HTTP $code). Verifica username/password o reglas del servidor.');
  }

  static Future<void> logout() async {
    String baseErpUrl = await Prefs.getBaseErpUrl();
    if (baseErpUrl.contains('cavaapp.3ware.com.mx')) {
      baseErpUrl = 'https://3ware.com.mx/tequila/erp';
    }
    final dio = await ApiClient.instance.dio();
    try {
      await dio.get('$baseErpUrl/admin/authentication/logout');
    } catch (_) {}
    await Prefs.logout();
  }

  static Future<bool> _verifyLoggedIn(Dio dio, String baseErpUrl) async {
    final r = await dio.get('$baseErpUrl/admin');
    final code = r.statusCode ?? 0;

    if (code >= 300 && code < 400) {
      final loc = (r.headers.value('location') ?? '').toLowerCase();
      if (loc.contains('authentication')) return false;
      return true; // redirige a dashboard u otra ruta interna
    }

    final body = (r.data?.toString() ?? '').toLowerCase();
    if ((body.contains('name="username"') || body.contains('name="email"')) &&
        body.contains('name="password"')) {
      return false;
    }
    if (body.contains('admin/authentication')) return false;

    return true;
  }

  static Future<String> _fetchLoginHtml(Dio dio, String baseErpUrl) async {
    final candidates = <String>[
      '$baseErpUrl/admin',
      '$baseErpUrl/admin/',
      '$baseErpUrl/admin/authentication',
      '$baseErpUrl/admin/login',
    ];

    for (final url in candidates) {
      final r = await dio.get(url);
      final code = r.statusCode ?? 0;

      if (code >= 300 && code < 400) {
        final loc = r.headers.value('location') ?? '';
        final target = _resolveLocation(baseErpUrl, loc);
        if (target == null) continue;

        final rr = await dio.get(target);
        final body = rr.data?.toString() ?? '';
        if (body.contains('<form')) return body;
        continue;
      }

      final body = r.data?.toString() ?? '';
      if (body.contains('<form')) return body;
    }

    return '';
  }

  static String? _resolveLocation(String baseErpUrl, String location) {
    if (location.isEmpty) return null;
    if (location.startsWith('http')) return location;
    if (location.startsWith('/')) return '$baseErpUrl$location';
    return '$baseErpUrl/$location';
  }

  static (String, String)? _extractCsrf(String htmlStr) {
    if (htmlStr.isEmpty) return null;

    final doc = html.parse(htmlStr);

    // 1) nombre clásico perfex
    final classic = doc.querySelector('input[name="csrf_token_name"]');
    if (classic != null) {
      final v = classic.attributes['value'];
      if (v != null && v.isNotEmpty) return ('csrf_token_name', v);
    }

    // 2) cualquier hidden con csrf
    final inputs = doc.querySelectorAll('input[type="hidden"]');
    for (final i in inputs) {
      final name = (i.attributes['name'] ?? '').trim();
      final value = (i.attributes['value'] ?? '').trim();
      if (name.toLowerCase().contains('csrf') && value.isNotEmpty) {
        return (name, value);
      }
    }

    // 3) regex fallback seguro
    final re = RegExp(
      r"""name=['\"]([^'\"]*csrf[^'\"]*)['\"]\s+value=['\"]([^'\"]+)['\"]""",
      caseSensitive: false,
    );
    final m = re.firstMatch(htmlStr);
    if (m != null) {
      final name = (m.group(1) ?? '').trim();
      final value = (m.group(2) ?? '').trim();
      if (name.isNotEmpty && value.isNotEmpty) return (name, value);
    }

    return null;
  }
}
