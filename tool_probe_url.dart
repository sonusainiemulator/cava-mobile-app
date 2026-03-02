import 'package:dio/dio.dart';

void main() async {
  final dio = Dio();
  // Valid token from Prefs (Debug Token)
  final token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoidGVxdWlsYSIsIm5hbWUiOiJ0ZXF1aWxhIHVzZXIiLCJBUElfVElNRSI6MTc2ODUwMDI2Mn0.d07qDBVbZDIucoLNEi6QtVm3v3ZtLAPFAjzJgyRQFz0';
  
  dio.options.headers['Authorization'] = 'Bearer $token';
  dio.options.validateStatus = (status) => true;

  final bases = [
    'https://3ware.com.mx/tequila/erp/index.php/admin/eventos_actividades/eventos_api',
    'https://3ware.com.mx/tequila/erp/index.php/eventos_actividades/eventos_api',
    'https://3ware.com.mx/tequila/erp/admin/modules/eventos_actividades/eventos_api',
  ];

  final endpoints = ['/products', '/banners', '/mezcal'];

  for (final base in bases) {
    print('\nTesting Base: $base');
    for (final ep in endpoints) {
      final url = '$base$ep';
      try {
        final r = await dio.get(url);
        print('  $ep -> ${r.statusCode}');
        if (r.statusCode == 200) {
          print('    SUCCESS! Found data.');
        }
      } catch (e) {
        print('  $ep -> Error: $e');
      }
    }
  }
}
