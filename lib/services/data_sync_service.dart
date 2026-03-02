import 'package:flutter/foundation.dart';
import '../db/app_database.dart';
import 'api_service.dart';
import 'prefs.dart';
import 'eventos_service.dart';

class DataSyncService {
  static final DataSyncService instance = DataSyncService._();
  DataSyncService._();

  Future<void> syncAll() async {
    debugPrint('Starting Data Sync...');
    try {
      if (!(await Prefs.isLoggedIn())) {
        debugPrint('Sync skipped: Not logged in.');
        return;
      }

      await Future.wait([
        _pullUser(),
        _pullAds(),
        _pullBeverages(),
        _pullMezcal(),
        _pullTequila(),
        _pullMarcas(),
        _pullAvatars(),
        _pullReviews(),
      ]);


      // Upload pending data
      await _pushScans();
      await _pushReviews();
      debugPrint('Data Sync Completed Successfully.');
    } catch (e) {
      debugPrint('Sync error: $e');
    }
  }

  Future<void> _pullUser() async {
    try {
      // Refresh points and profile on sync
      await EventosService.getProfile();
    } catch (e) {
      debugPrint('Error pulling user: $e');
    }
  }


  Future<void> _pullMezcal() async {
    try {
      debugPrint('Syncing Mezcal content...');
      final data = await EventosService.getMezcalContent();
      if (data != null) {
        await AppDatabase.instance.clearTable('mezcal_content');

        final types = ['image', 'video', 'text'];
        for (final t in types) {
          final list = data['${t}s'] ?? [];
          for (final item in list) {
            await AppDatabase.instance.upsertMezcalContent({
              'type': t,
              'url': item['file'] ?? item['url'] ?? '',
              'text': item['text'] ?? '',
              'title': item['title'] ?? '',
            });
          }
        }
      }
    } catch (e) {
      debugPrint('Error pulling mezcal: $e');
    }
  }

  Future<void> _pullTequila() async {
    try {
      debugPrint('Syncing Tequila content...');
      final data = await EventosService.getTequilaContent();
      if (data != null) {
        await AppDatabase.instance.clearTable('tequila_content');

        final types = ['image', 'video', 'text'];
        for (final t in types) {
          final list = data['${t}s'] ?? [];
          for (final item in list) {
            await AppDatabase.instance.upsertTequilaContent({
              'type': t,
              'url': item['file'] ?? item['url'] ?? '',
              'text': item['text'] ?? '',
              'title': item['title'] ?? '',
            });
          }
        }
      }
    } catch (e) {
      debugPrint('Error pulling tequila: $e');
    }
  }

  Future<void> _pullMarcas() async {
    try {
      debugPrint('Syncing Marcas...');
      final list = await EventosService.getMarcas();
      if (list.isNotEmpty) {
        await AppDatabase.instance.clearTable('marcas');
        for (final item in list) {
          await AppDatabase.instance.upsertMarca({
            'name': item['nombre'] ?? item['name'] ?? '',
            'description': item['descripcion'] ?? item['description'] ?? '',
            'logo_url': item['imagen_url'] ?? item['logo_url'] ?? '',
            'website': item['website'] ?? '',
          });
        }
      }
    } catch (e) {
      debugPrint('Error pulling marcas: $e');
    }
  }

  Future<void> _pullAvatars() async {
    try {
      debugPrint('Syncing Avatars...');
      final list = await EventosService.getAvatars();
      if (list.isNotEmpty) {
        await AppDatabase.instance.clearTable('avatars');
        for (final item in list) {
          await AppDatabase.instance.upsertAvatar({
            'name': item['name'] ?? '',
            'image_url': item['image_url'] ?? '',
            'points_required':
                int.tryParse(item['puntos']?.toString() ?? '0') ?? 0,
          });
        }
      }
    } catch (e) {
      debugPrint('Error pulling avatars: $e');
    }
  }

  Future<void> _pullReviews() async {
    try {
      debugPrint('Syncing Reviews...');
      final list = await EventosService.getReviews();
      if (list.isNotEmpty) {
        // We only pull approved reviews
        // No need to clear local table maybe? Or just clear and sync.
        // Usually, we want to clear and re-sync for freshness if it's a small list.
        await AppDatabase.instance.clearTable('reviews');
        for (final item in list) {
          await AppDatabase.instance.insertReview({
            'username': item['user_name'] ?? '',
            'content': item['review_text'] ?? '',
            'rating': double.tryParse(item['rating']?.toString() ?? '5') ?? 5.0,
            'date': item['created_at'] ?? '',
            'approved': 1,
            'synced': 1, // It came from server
          });
        }
      }
    } catch (e) {
      debugPrint('Error pulling reviews: $e');
    }
  }


  Future<void> _pullAds() async {
    try {
      debugPrint('Syncing Ads/Banners...');
      final list = await EventosService.getBanners();
      if (list.isNotEmpty) {
        for (final item in list) {
          if (item is Map<String, dynamic>) {
            await AppDatabase.instance.upsertAd({
              'title': item['title'] ?? '',
              'image_path': item['image'] ?? item['image_url'] ?? '',
              'description': item['description'] ?? '',
              'location': item['location'] ?? '',
            });
          }
        }
      }
    } catch (e) {
      debugPrint('Error pulling ads: $e');
    }
  }

  Future<void> _pullBeverages() async {
    try {
      debugPrint('Syncing Beverages (Cava)...');
      final list = await EventosService.getProducts();
      if (list.isNotEmpty) {
        for (final item in list) {
          if (item is Map<String, dynamic>) {
            await AppDatabase.instance.upsertBeverage({
              'barcode': item['barcode'] ?? item['codigo_barras'] ?? '',
              'name': item['name'] ?? item['nombre_tequila'] ?? '',
              'brand': item['brand'] ?? item['marca'] ?? '',
              'presentation':
                  item['presentation'] ?? item['presentacion'] ?? '',
              'alcohol_degrees': double.tryParse(
                      item['alcohol_degrees']?.toString() ??
                          item['grados_alcohol']?.toString() ??
                          '0') ??
                  0.0,
              'price': double.tryParse(item['price']?.toString() ??
                      item['precio']?.toString() ??
                      '0') ??
                  0.0,
            });
          }
        }
      }
    } catch (e) {
      debugPrint('Error pulling beverages: $e');
    }
  }

  Future<void> _pushScans() async {
    try {
      final scans = await AppDatabase.instance.getPendingScans();
      if (scans.isEmpty) return;

      final endpoint = await Prefs.getEndpoint(Prefs.epScanUpload, '/scan');
      final uploadedIds = <int>[];

      for (final scan in scans) {
        try {
          // Prepare data for API
          final data = {
            'barcode': scan['barcode'],
            'latitude': scan['latitude'],
            'longitude': scan['longitude'],
            'timestamp': scan['timestamp'],
            'username': scan['username'],
            'date': scan['timestamp'],
            // Add other fields if API expects them
          };

          final response = await ApiService.post(endpoint, data: data);
          if (response.statusCode == 200 || response.statusCode == 201) {
            uploadedIds.add(scan['id'] as int);
          }
        } catch (e) {
          debugPrint('Error pushing scan ${scan['id']}: $e');
        }
      }

      if (uploadedIds.isNotEmpty) {
        await AppDatabase.instance.markScansAsSynced(uploadedIds);
      }
    } catch (e) {
      debugPrint('Error pushScans: $e');
    }
  }

  Future<void> _pushReviews() async {
    try {
      final db = await AppDatabase.instance.database;
      // Get reviews where synced = 0
      final reviews = await db.query('reviews', where: 'synced = 0');
      if (reviews.isEmpty) return;

      // Assuming endpoint /reviews exists, though not explicitly in Prefs defaults yet.
      // Let's use a generic /reviews endpoint or add it to Prefs if needed.
      // For now, I'll use '/reviews'
      final endpoint = await Prefs.getEndpoint('ep_reviews', '/eventos_actividades/eventos_api/reviews');

      final uploadedIds = <int>[];

      for (final r in reviews) {
        try {
          final success = await EventosService.submitReview(
            r['content'] as String,
            (r['rating'] as num).toDouble(),
          );
          if (success) {
            uploadedIds.add(r['id'] as int);
          }
        } catch (e) {
          debugPrint('Error pushing review ${r['id']}: $e');
        }
      }


      if (uploadedIds.isNotEmpty) {
        final batch = db.batch();
        for (final id in uploadedIds) {
          batch.update('reviews', {'synced': 1},
              where: 'id = ?', whereArgs: [id]);
        }
        await batch.commit(noResult: true);
      }
    } catch (e) {
      debugPrint('Error pushReviews: $e');
    }
  }
}
