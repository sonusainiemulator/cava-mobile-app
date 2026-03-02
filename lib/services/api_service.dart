import 'package:dio/dio.dart';
import 'api_client.dart';
import 'prefs.dart';

class ApiService {
  static Future<Response> get(String endpoint,
      {Map<String, dynamic>? query, bool noAuth = false}) async {
    final base = await Prefs.getApiBaseUrl();
    final url = _join(base, endpoint);
    final dio = await ApiClient.instance.dio();
    return dio.get(url,
        queryParameters: query, options: Options(extra: {'noAuth': noAuth}));
  }

  static Future<Response> post(String endpoint,
      {Object? data,
      Map<String, dynamic>? query,
      bool noAuth = false,
      String? contentType}) async {
    final base = await Prefs.getApiBaseUrl();
    final url = _join(base, endpoint);
    final dio = await ApiClient.instance.dio();
    return dio.post(url,
        data: data,
        queryParameters: query,
        options: Options(
          extra: {'noAuth': noAuth},
          contentType: contentType,
        ));
  }

  static String _join(String base, String ep) {
    if (ep.startsWith('http')) return ep;
    if (ep.startsWith('/')) return '$base$ep';
    return '$base/$ep';
  }

  static Future<bool> syncScans(List<Map<String, dynamic>> scans) async {
    // try {
    //   await post('/api/scans/sync', data: {'scans': scans});
    //   return true;
    // } catch (e) {
    //   return false;
    // }
    // Mock implementation for now
    await Future.delayed(const Duration(seconds: 1));
    return true;
  }
}
