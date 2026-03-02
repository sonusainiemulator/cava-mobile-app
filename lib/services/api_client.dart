import 'package:dio/dio.dart';
import 'prefs.dart';

class ApiClient {
  ApiClient._();
  static final ApiClient instance = ApiClient._();
  Dio? _dio;
  bool _mockEnabled = false;

  void setMockEnabled(bool enabled) {
    _mockEnabled = enabled;
    _dio = null; // Reset dio to apply changes
  }

  Future<Dio> dio() async {
    if (_dio != null) return _dio!;
    final d = Dio(BaseOptions(
      connectTimeout: const Duration(seconds: 20),
      receiveTimeout: const Duration(seconds: 30),
      validateStatus: (s) => s != null && s < 500,
      headers: {'User-Agent': 'TequilaApp/1.3.2'},
    ));
    d.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        if (_mockEnabled) {
          await Future.delayed(
              const Duration(milliseconds: 500)); // Simulate latency
          final path = options.path;

          if (path.contains('/products')) {
            return handler.resolve(
                Response(requestOptions: options, statusCode: 200, data: [
              {
                'barcode': '123456',
                'name': 'Mock Tequila',
                'brand': 'Mock Brand',
                'presentation': '750ml',
                'price': 500.0,
                'alcohol_degrees': 38.0
              }
            ]));
          }
          if (path.contains('/banners')) {
            return handler.resolve(
                Response(requestOptions: options, statusCode: 200, data: [
              {
                'title': 'Mock Banner',
                'image': 'https://via.placeholder.com/300x150',
                'description': 'Mock Description'
              }
            ]));
          }
          if (path.contains('/mezcal')) {
            return handler.resolve(
                Response(requestOptions: options, statusCode: 200, data: {
              'images': [],
              'videos': [],
              'texts': [
                {'title': 'Mock Mezcal', 'text': 'Content'}
              ]
            }));
          }
          if (path.contains('/tequila')) {
            return handler.resolve(
                Response(requestOptions: options, statusCode: 200, data: {
              'images': [],
              'videos': [],
              'texts': [
                {'title': 'Mock Tequila', 'text': 'Content'}
              ]
            }));
          }
          if (path.contains('/marcas')) {
            return handler.resolve(
                Response(requestOptions: options, statusCode: 200, data: [
              {'name': 'Mock Brand', 'logo_url': ''}
            ]));
          }
          if (path.contains('/avatars')) {
            return handler.resolve(
                Response(requestOptions: options, statusCode: 200, data: [
              {'name': 'Mock Avatar', 'image_url': ''}
            ]));
          }
          if (path.contains('/add_to_cava')) {
            return handler.resolve(Response(
                requestOptions: options,
                statusCode: 200,
                data: {'success': true}));
          }
        }

        final noAuth = options.extra['noAuth'] == true;
        if (!noAuth) {
          final token = await Prefs.getApiToken();
          if (token.isNotEmpty)
            options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
    ));
    // Advanced Debug Mode: Log everything
    const advancedDebug =
        bool.fromEnvironment('ADVANCED_DEBUG', defaultValue: false);
    if (advancedDebug) {
      d.interceptors.add(LogInterceptor(
        request: true,
        requestHeader: true,
        requestBody: true,
        responseHeader: true,
        responseBody: true,
        error: true,
      ));
    }
    _dio = d;
    return d;
  }
}
