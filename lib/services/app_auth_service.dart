import 'prefs.dart';

class AppAuthService {
  static Future<void> loginWithToken({required String apiUser, required String token}) async {
    await Prefs.setApiUser(apiUser);
    await Prefs.setApiToken(token);
    await Prefs.setLoggedIn(true);
  }

  static Future<void> logout() async {
    await Prefs.setApiToken('');
    await Prefs.setLoggedIn(false);
  }
}
