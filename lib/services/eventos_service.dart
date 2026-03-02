import 'package:flutter/foundation.dart';
import '../../services/api_service.dart';
import '../../services/prefs.dart';

class EventosService {
  static const _modulePath = '/api';

  static Future<String> _baseUrl() async {
    String base = await Prefs.getApiBaseUrl();
    
    // Normalize base: Extract actual domain part
    // If it is https://.../admin/api, we want https://.../
    if (base.contains('/admin/api')) {
      base = base.substring(0, base.indexOf('/admin/api'));
    } else if (base.endsWith('/api')) {
      base = base.substring(0, base.length - 4);
    }

    if (base.endsWith('/')) {
      base = base.substring(0, base.length - 1);
    }
    return '$base$_modulePath';
  }


  static Future<bool> login(String email, String password) async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.post('$base/login',
          data: {
            'email': email,
            'password': password,
          },
          noAuth: true); // Login endpoint is public

      if (response.statusCode == 200) {
        final data = response.data;
        if (data['token'] != null) {
          await Prefs.setApiToken(data['token']);
          await Prefs.setLoggedIn(true);

          // Save user data
          if (data['user'] != null) {
            final user = data['user'];
            if (user['avatar_url'] != null) {
              await Prefs.setAvatarUrl(user['avatar_url']);
            }
            if (user['points'] != null) {
              await Prefs.setPoints(
                  int.tryParse(user['points'].toString()) ?? 0);
            }
            // Also update username if available
            if (user['firstname'] != null) {
              await Prefs.setApiUser(
                  '${user['firstname']} ${user['lastname'] ?? ''}');
            }
          }
          return true;
        }
      }
    } catch (e) {
      debugPrint('EventosService Login Error: $e');
    }
    return false;
  }

  static Future<Map<String, dynamic>?> getProfile() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/me');

      if (response.statusCode == 200) {
        final raw = response.data;
        final data = (raw is Map && raw.containsKey('data')) ? raw['data'] : raw;
        if (data != null) {
          if (data['avatar_url'] != null) await Prefs.setAvatarUrl(data['avatar_url']);
          if (data['points'] != null) await Prefs.setPoints(int.tryParse(data['points'].toString()) ?? 0);
        }
        return data;
      }
    } catch (e) {
      debugPrint('EventosService getProfile Error: $e');
    }
    return null;
  }

  static Future<Map<String, dynamic>?> scan(String barcode,
      {double? lat, double? lng, String? username, String? date}) async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.post('$base/scan', data: {
        'barcode': barcode,
        'latitude': lat,
        'longitude': lng,
        'username': username,
        'date': date,
      });

      if (response.statusCode == 200 || response.statusCode == 201) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'];
        return raw;
      }
    } catch (e) {
      debugPrint('EventosService scan Error: $e');
    }
    return null;
  }

  static Future<bool> addToCava(String barcode) async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.post('$base/add_to_cava', data: {
        'barcode': barcode,
      });

      if (response.statusCode == 200 || response.statusCode == 201) {
        return true;
      }
    } catch (e) {
      debugPrint('EventosService addToCava Error: $e');
    }
    return false;
  }


  // --- New Methods for Full Connectivity ---

  static Future<Map<String, dynamic>?> getMezcalContent() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/mezcal');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'];
        return raw;
      }
    } catch (e) {
      debugPrint('EventosService getMezcalContent Error: $e');
    }
    return null;
  }

  static Future<Map<String, dynamic>?> getTequilaContent() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/tequila');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'];
        return raw;
      }
    } catch (e) {
      debugPrint('EventosService getTequilaContent Error: $e');
    }
    return null;
  }

  static Future<List<dynamic>> getProducts() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/products');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'] as List;
        if (raw is List) return raw;
      }
    } catch (e) {
      debugPrint('EventosService getProducts Error: $e');
    }
    return [];
  }

  static Future<List<dynamic>> getBanners() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/banners');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'] as List;
        if (raw is List) return raw;
      }
    } catch (e) {
      debugPrint('EventosService getBanners Error: $e');
    }
    return [];
  }

  static Future<List<dynamic>> getMarcas() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/marcas');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'] as List;
        if (raw is List) return raw;
      }
    } catch (e) {
      debugPrint('EventosService getMarcas Error: $e');
    }
    return [];
  }

  static Future<List<dynamic>> getAvatars() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/avatars');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'] as List;
        if (raw is List) return raw;
      }
    } catch (e) {
      debugPrint('EventosService getAvatars Error: $e');
    }
    return [];
  }

  static Future<List<dynamic>> getReviews() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/reviews');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'] as List;
        if (raw is List) return raw;
      }
    } catch (e) {
      debugPrint('EventosService getReviews Error: $e');
    }
    return [];
  }


  static Future<bool> submitReview(String content, double rating) async {

    try {
      final base = await _baseUrl();
      final response = await ApiService.post('$base/reviews', data: {
        'content': content,
        'rating': rating,
      });

      if (response.statusCode == 200 || response.statusCode == 201) {
        return true;
      }
    } catch (e) {
      debugPrint('EventosService submitReview Error: $e');
    }
    return false;
  }

  // --- Chat Methods ---

  static Future<List<dynamic>> getChatHistory() async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/chat/history');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'] as List;
        if (raw is List) return raw;
      }
    } catch (e) {
      debugPrint('EventosService getChatHistory Error: $e');
    }
    return [];
  }

  static Future<List<dynamic>> getChatMessages(int partnerId) async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.get('$base/chat/messages?partner_id=$partnerId');

      if (response.statusCode == 200) {
        final raw = response.data;
        if (raw is Map && raw.containsKey('data')) return raw['data'] as List;
        if (raw is List) return raw;
      }
    } catch (e) {
      debugPrint('EventosService getChatMessages Error: $e');
    }
    return [];
  }

  static Future<bool> sendMessage(String toIdentity, String message) async {
    try {
      final base = await _baseUrl();
      final response = await ApiService.post('$base/chat/send', data: {
        'to': toIdentity,
        'message': message,
      });

      if (response.statusCode == 200 || response.statusCode == 201) {
        return true;
      }
    } catch (e) {
      debugPrint('EventosService sendMessage Error: $e');
    }
    return false;
  }
}
