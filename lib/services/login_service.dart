import 'package:dio/dio.dart';
import 'api_service.dart';
import 'prefs.dart';

class LoginService {
  /// Login real por email/password.
  /// Endpoint configurable: ep_login (default: /auth/login)
  /// Busca token en: token | access_token | jwt | data.token | data.access_token
  static Future<String> loginWithEmailPassword({
    required String email,
    required String password,
  }) async {
    final ep = await Prefs.getEndpoint(Prefs.epLogin, '/auth/login');

    final payload = {
      'email': email,
      'username': email, // compatibilidad
      'password': password,
    };

    Response r;
    try {
      r = await ApiService.post(
        ep, 
        data: payload, 
        noAuth: true,
        contentType: Headers.formUrlEncodedContentType,
      );
    } catch (e) {
      throw Exception('Login error: $e');
    }

    if (r.statusCode != 200) {
      throw Exception('Login fallido: ${r.statusMessage}');
    }


    final token = _extractToken(r.data);
    if (token == null || token.isEmpty) {
      throw Exception('Login sin token en respuesta');
    }
    return token;
  }

  static String? _extractToken(dynamic data) {
    try {
      if (data is Map) {
        String? pick(Map m, String k) => m[k]?.toString();
        final direct = pick(data, 'token') ?? pick(data, 'access_token') ?? pick(data, 'jwt');
        if (direct != null && direct.isNotEmpty) return direct;

        final inner = data['data'];
        if (inner is Map) {
          final innerTok = pick(inner, 'token') ?? pick(inner, 'access_token') ?? pick(inner, 'jwt');
          if (innerTok != null && innerTok.isNotEmpty) return innerTok;
        }
      }
    } catch (_) {}
    return null;
  }
}
