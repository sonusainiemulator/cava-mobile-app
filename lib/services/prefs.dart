import 'package:shared_preferences/shared_preferences.dart';

class Prefs {
  static const _kApiBaseUrl = 'api_base_url';
  static const _kApiToken = 'api_token';
  static const _kApiUser = 'api_user';
  static const _kIsLoggedIn = 'is_logged_in';

  static const epLogin = 'ep_login';
  static const epMe = 'ep_me';
  static const epBanners = 'ep_banners';
  static const epEvents = 'ep_events';
  static const epCavaItems = 'ep_cava_items';
  static const epTequila = 'ep_tequila';
  static const epMezcal = 'ep_mezcal';
  static const epUsers = 'ep_users';
  static const epChatThread = 'ep_chat_thread';
  static const epChatMessages = 'ep_chat_messages';
  static const epScanUpload = 'ep_scan_upload';

  static const _kAvatarUrl = 'avatar_url';
  static const _kPoints = 'points';

  static Future<SharedPreferences> _p() => SharedPreferences.getInstance();

  static Future<void> ensureDefaults() async {
    final p = await _p();
    p.setString(
        _kApiBaseUrl,
        p.getString(_kApiBaseUrl) ??
            'https://3ware.com.mx/tequila/erp/admin/api');
    p.setString(_kApiUser, p.getString(_kApiUser) ?? 'tequila');

    String currentToken = p.getString(_kApiToken) ?? '';
    p.setString(_kApiToken, currentToken);

    p.setBool(_kIsLoggedIn, p.getBool(_kIsLoggedIn) ?? false);

    p.setString(epLogin,
        p.getString(epLogin) ?? '/eventos_actividades/eventos_api/login');
    p.setString(
        epMe, p.getString(epMe) ?? '/eventos_actividades/eventos_api/me');
    p.setString(epBanners, p.getString(epBanners) ?? '/banners');
    p.setString(epEvents, p.getString(epEvents) ?? '/events');
    p.setString(
        epCavaItems,
        p.getString(epCavaItems) ??
            '/eventos_actividades/eventos_api/products');
    p.setString(epTequila, p.getString(epTequila) ?? '/content/tequila');
    p.setString(epMezcal,
        p.getString(epMezcal) ?? '/eventos_actividades/eventos_api/mezcal');
    p.setString(epUsers, p.getString(epUsers) ?? '/users');
    p.setString(epChatThread, p.getString(epChatThread) ?? '/chat/thread');
    p.setString(epChatMessages,
        p.getString(epChatMessages) ?? '/chat/thread/{id}/messages');
    p.setString(epScanUpload,
        p.getString(epScanUpload) ?? '/eventos_actividades/eventos_api/scan');
  }

  static Future<String> getApiBaseUrl() async =>
      (await _p()).getString(_kApiBaseUrl) ?? '';
  static Future<void> setApiBaseUrl(String v) async =>
      (await _p()).setString(_kApiBaseUrl, v.replaceAll(RegExp(r'/$'), ''));

  static Future<String> getApiUser() async =>
      (await _p()).getString(_kApiUser) ?? '';
  static Future<void> setApiUser(String v) async =>
      (await _p()).setString(_kApiUser, v);

  static const _kLanguage = 'language';
  static Future<String> getLanguage() async =>
      (await _p()).getString(_kLanguage) ?? 'es';
  static Future<void> setLanguage(String v) async =>
      (await _p()).setString(_kLanguage, v);

  static Future<String> getAvatarUrl() async =>
      (await _p()).getString(_kAvatarUrl) ?? '';
  static Future<void> setAvatarUrl(String v) async =>
      (await _p()).setString(_kAvatarUrl, v);

  static Future<int> getPoints() async => (await _p()).getInt(_kPoints) ?? 0;
  static Future<void> setPoints(int v) async =>
      (await _p()).setInt(_kPoints, v);

  static Future<String> getApiToken() async =>
      (await _p()).getString(_kApiToken) ?? '';
  static Future<void> setApiToken(String v) async =>
      (await _p()).setString(_kApiToken, v.trim());

  static Future<bool> isLoggedIn() async =>
      (await _p()).getBool(_kIsLoggedIn) ?? false;
  static Future<void> setLoggedIn(bool v) async =>
      (await _p()).setBool(_kIsLoggedIn, v);

  static Future<String> getBaseErpUrl() async {
    String apiBase = await getApiBaseUrl();
    if (apiBase.endsWith('/admin/api')) {
      return apiBase.substring(0, apiBase.length - 10);
    } else if (apiBase.endsWith('/api')) {
      return apiBase.substring(0, apiBase.length - 4);
    }
    return apiBase.endsWith('/')
        ? apiBase.substring(0, apiBase.length - 1)
        : apiBase;
  }

  static Future<void> logout() async {
    await setApiToken('');
    await setLoggedIn(false);
    await setAvatarUrl('');
    await setPoints(0);
  }

  static Future<String> getEndpoint(String key, String fallback) async =>
      (await _p()).getString(key) ?? fallback;
  static Future<void> setEndpoint(String key, String value) async =>
      (await _p()).setString(key, value.trim());
}
