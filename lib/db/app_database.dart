import 'package:flutter/foundation.dart';
import 'package:path/path.dart';
import 'package:path_provider/path_provider.dart';
import 'package:sqflite/sqflite.dart';
import '../utils/mock_data.dart' as mock_data;

class AppDatabase {
  AppDatabase._internal();
  static final AppDatabase instance = AppDatabase._internal();
  Database? _db;

  Future<Database> get database async {
    _db ??= await _initDb();
    return _db!;
  }

  Future<Database> _initDb() async {
    final dir = await getApplicationDocumentsDirectory();
    final path = join(dir.path, 'tequila_app.db');

    return openDatabase(path, version: 6, onCreate: (db, version) async {
      await _createDb(db);
    }, onUpgrade: (db, oldVersion, newVersion) async {
      if (oldVersion < 2) {
        await db.execute(
            'CREATE TABLE ads (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, image_path TEXT, description TEXT, location TEXT);');
        await db.execute(
            'CREATE TABLE reviews (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, content TEXT, rating REAL, date TEXT, approved INTEGER DEFAULT 0);');
        await db.execute(
            'CREATE TABLE events (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, date TEXT, location TEXT, description TEXT, image_path TEXT);');
        await db.execute(
            'CREATE TABLE chats (id INTEGER PRIMARY KEY AUTOINCREMENT, counterpart TEXT, last_message TEXT, last_message_time TEXT);');
        await db.execute(
            'CREATE TABLE chat_messages (id INTEGER PRIMARY KEY AUTOINCREMENT, chat_id INTEGER, sender TEXT, content TEXT, timestamp TEXT, is_image INTEGER DEFAULT 0);');
        await db.execute(
            'CREATE TABLE my_cava (id INTEGER PRIMARY KEY AUTOINCREMENT, barcode TEXT, notes TEXT);');

        // Seed new data
        await db.insert('ads', {
          'title': 'Promo Tequila',
          'image_path': 'assets/ad1.jpg',
          'description': '2x1 en Tequila Don Julio',
          'location': 'CDMX'
        });
        await db.insert('ads', {
          'title': 'Fiesta Mezcal',
          'image_path': 'assets/ad2.jpg',
          'description': 'Noche de cata gratis',
          'location': 'Guadalajara'
        });
        await db.insert('events', {
          'title': 'Cata de Verano',
          'date': '2023-11-20',
          'location': 'Hotel Riu',
          'description': 'Ven a probar los mejores tequilas.',
          'image_path': 'assets/event1.jpg'
        });
        await db.insert('reviews', {
          'username': 'JuanPerez',
          'content': 'Excelente tequila, muy suave.',
          'rating': 5.0,
          'date': '2023-10-01',
          'approved': 1
        });
      }
      if (oldVersion < 3) {
        try {
          await db.execute('ALTER TABLE scans ADD COLUMN brand TEXT');
          await db.execute('ALTER TABLE scans ADD COLUMN presentation TEXT');
          await db.execute('ALTER TABLE scans ADD COLUMN alcohol_degrees REAL');
          await db.execute('ALTER TABLE scans ADD COLUMN price REAL');
        } catch (e) {
          debugPrint('Error upgrading scans table: $e');
        }
      }
      if (oldVersion < 5) {
        await db.execute(
            'CREATE TABLE IF NOT EXISTS mezcal_content (id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT, url TEXT, text TEXT, title TEXT);');
        await db.execute(
            'CREATE TABLE IF NOT EXISTS marcas (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, description TEXT, logo_url TEXT, website TEXT);');
        await db.execute(
            'CREATE TABLE IF NOT EXISTS avatars (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, image_url TEXT, points_required INTEGER);');
      }
      if (oldVersion < 6) {
        await db.execute(
            'CREATE TABLE IF NOT EXISTS tequila_content (id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT, url TEXT, text TEXT, title TEXT);');
      }
    });
  }

  Future<void> _createDb(Database db) async {
    await db.execute(
        'CREATE TABLE IF NOT EXISTS beverages (barcode TEXT PRIMARY KEY, name TEXT, brand TEXT, presentation TEXT, alcohol_degrees REAL, price REAL);');
    // Updated scans table schema
    await db.execute(
        'CREATE TABLE IF NOT EXISTS scans (id INTEGER PRIMARY KEY AUTOINCREMENT, barcode TEXT, username TEXT, latitude REAL, longitude REAL, timestamp TEXT, synced INTEGER DEFAULT 0, brand TEXT, presentation TEXT, alcohol_degrees REAL, price REAL);');

    await db.execute(
        'CREATE TABLE IF NOT EXISTS ads (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, image_path TEXT, description TEXT, location TEXT);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS reviews (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, content TEXT, rating REAL, date TEXT, approved INTEGER DEFAULT 0);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS events (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, date TEXT, location TEXT, description TEXT, image_path TEXT);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS chats (id INTEGER PRIMARY KEY AUTOINCREMENT, counterpart TEXT, last_message TEXT, last_message_time TEXT);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS chat_messages (id INTEGER PRIMARY KEY AUTOINCREMENT, chat_id INTEGER, sender TEXT, content TEXT, timestamp TEXT, is_image INTEGER DEFAULT 0);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS my_cava (id INTEGER PRIMARY KEY AUTOINCREMENT, barcode TEXT, notes TEXT);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS user_points (id INTEGER PRIMARY KEY AUTOINCREMENT, points INTEGER, reason TEXT, timestamp TEXT);');

    // New tables v5 & v6
    await db.execute(
        'CREATE TABLE IF NOT EXISTS mezcal_content (id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT, url TEXT, text TEXT, title TEXT);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS tequila_content (id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT, url TEXT, text TEXT, title TEXT);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS marcas (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, description TEXT, logo_url TEXT, website TEXT);');
    await db.execute(
        'CREATE TABLE IF NOT EXISTS avatars (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, image_url TEXT, points_required INTEGER);');

    await db.insert('beverages', {
      'barcode': '7501000123456',
      'name': 'Tequila Blanco Tradicional',
      'brand': 'Casa Agave',
      'presentation': '750 ml',
      'alcohol_degrees': 38.0,
      'price': 350.0
    });
    await db.insert('beverages', {
      'barcode': '7502000654321',
      'name': 'Mezcal Joven Artesanal',
      'brand': 'Oaxaca Místico',
      'presentation': '700 ml',
      'alcohol_degrees': 40.0,
      'price': 550.0
    });

    await db.insert('ads', {
      'title': 'Promo Tequila',
      'image_path': 'assets/ad1.jpg',
      'description': '2x1 en Tequila Don Julio',
      'location': 'CDMX'
    });
    await db.insert('ads', {
      'title': 'Fiesta Mezcal',
      'image_path': 'assets/ad2.jpg',
      'description': 'Noche de cata gratis',
      'location': 'Guadalajara'
    });
    await db.insert('events', {
      'title': 'Cata de Verano',
      'date': '2023-11-20',
      'location': 'Hotel Riu',
      'description': 'Ven a probar los mejores tequilas.',
      'image_path': 'assets/event1.jpg'
    });
    await db.insert('reviews', {
      'username': 'JuanPerez',
      'content': 'Excelente tequila, muy suave.',
      'rating': 5.0,
      'date': '2023-10-01',
      'approved': 1
    });

    // Initial points
    await db.insert('user_points', {
      'points': 100,
      'reason': 'Welcome Bonus',
      'timestamp': DateTime.now().toIso8601String()
    });
  }

  Future<Map<String, dynamic>?> getBeverageByBarcode(String barcode) async {
    final db = await database;
    final res = await db.query('beverages',
        where: 'barcode = ?', whereArgs: [barcode], limit: 1);
    return res.isEmpty ? null : res.first;
  }

  Future<List<Map<String, dynamic>>> listBeverages() async {
    final db = await database;
    return db.query('beverages', orderBy: 'name ASC');
  }

  Future<int> insertScan(Map<String, dynamic> scan) async {
    final db = await database;
    return db.insert('scans', scan);
  }

  Future<void> upsertBeverage(Map<String, dynamic> bev) async {
    final db = await database;
    await db.insert('beverages', bev,
        conflictAlgorithm: ConflictAlgorithm.replace);
  }

  // --- Helpers for new features ---

  Future<List<Map<String, dynamic>>> getAds() async {
    final db = await database;
    return db.query('ads');
  }

  Future<void> upsertAd(Map<String, dynamic> ad) async {
    final db = await database;
    // Assuming 'id' is coming from server and we want to preserve it or update.
    // If local ID is autoincrement, we might need a mapping or a 'server_id' column.
    // For simplicity, let's assume we replace all ads or just add new ones.
    // A better approach for sync is to have a unique 'server_id' column.
    // But given the schema, let's just insert for now or update if we had an ID.
    // Actually, let's clear and re-insert for "Pull" strategy if that's what we want,
    // OR just insert if not exists.

    // Simplest for this task: Just insert. But duplicates will happen.
    // Let's check simply by title for now as we lack a unique external ID in the schema.
    final existing =
        await db.query('ads', where: 'title = ?', whereArgs: [ad['title']]);
    if (existing.isEmpty) {
      await db.insert('ads', ad);
    } else {
      await db.update('ads', ad, where: 'title = ?', whereArgs: [ad['title']]);
    }
  }

  Future<List<Map<String, dynamic>>> getReviews() async {
    final db = await database;
    return db.query('reviews', where: 'approved = 1', orderBy: 'date DESC');
  }

  Future<int> insertReview(Map<String, dynamic> review) async {
    final db = await database;
    return db.insert('reviews', review);
  }

  Future<List<Map<String, dynamic>>> getEvents() async {
    final db = await database;
    return db.query('events', orderBy: 'date ASC');
  }

  Future<List<Map<String, dynamic>>> getChats() async {
    final db = await database;
    return db.query('chats', orderBy: 'last_message_time DESC');
  }

  Future<int> createChat(String counterpart) async {
    final db = await database;
    return db.insert('chats', {
      'counterpart': counterpart,
      'last_message': '',
      'last_message_time': DateTime.now().toIso8601String()
    });
  }

  Future<List<Map<String, dynamic>>> getChatMessages(int chatId) async {
    final db = await database;
    return db.query('chat_messages',
        where: 'chat_id = ?', whereArgs: [chatId], orderBy: 'timestamp ASC');
  }

  Future<int> insertChatMessage(Map<String, dynamic> msg) async {
    final db = await database;
    // Update last message in chat
    await db.update('chats',
        {'last_message': msg['content'], 'last_message_time': msg['timestamp']},
        where: 'id = ?', whereArgs: [msg['chat_id']]);

    return db.insert('chat_messages', msg);
  }

  Future<List<Map<String, dynamic>>> getMyCava() async {
    final db = await database;
    // Join with beverages to get details
    return db.rawQuery('''
      SELECT m.id, m.barcode, m.notes, b.name, b.brand, b.presentation 
      FROM my_cava m
      LEFT JOIN beverages b ON m.barcode = b.barcode
    ''');
  }

  Future<int> addToCava(String barcode) async {
    final db = await database;
    // Check if exists
    final exists =
        await db.query('my_cava', where: 'barcode = ?', whereArgs: [barcode]);
    if (exists.isNotEmpty) return 0;
    return db.insert('my_cava', {'barcode': barcode, 'notes': ''});
  }

  Future<int> removeFromCava(String barcode) async {
    final db = await database;
    return db.delete('my_cava', where: 'barcode = ?', whereArgs: [barcode]);
  }

  // --- Points System ---

  Future<int> addPoints(int points, String reason) async {
    final db = await database;
    return db.insert('user_points', {
      'points': points,
      'reason': reason,
      'timestamp': DateTime.now().toIso8601String()
    });
  }

  Future<int> getTotalPoints() async {
    final db = await database;
    final result =
        await db.rawQuery('SELECT SUM(points) as total FROM user_points');
    return (result.first['total'] as int?) ?? 0;
  }

  // --- Sync System ---

  Future<List<Map<String, dynamic>>> getPendingScans() async {
    final db = await database;
    return db.query('scans', where: 'synced = 0');
  }

  Future<void> markScansAsSynced(List<int> ids) async {
    final db = await database;
    final batch = db.batch();
    for (final id in ids) {
      batch.update('scans', {'synced': 1}, where: 'id = ?', whereArgs: [id]);
    }
    await batch.commit(noResult: true);
  }

  // --- Mock Data Seeder ---

  Future<void> insertMockData() async {
    final db = await database;

    // 1. Beverages
    final bevCount = Sqflite.firstIntValue(
        await db.rawQuery('SELECT COUNT(*) FROM beverages'));
    if ((bevCount ?? 0) < 5) {
      for (final item in mock_data.mockBeverages) {
        // use conflict algorithm to avoid crashes on existing keys
        await db.insert('beverages', item,
            conflictAlgorithm: ConflictAlgorithm.ignore);
      }
    }

    // 2. Reviews
    final revCount = Sqflite.firstIntValue(
        await db.rawQuery('SELECT COUNT(*) FROM reviews'));
    if ((revCount ?? 0) < 3) {
      for (final item in mock_data.mockReviews) {
        await db.insert('reviews', item);
      }
    }

    // 3. Events
    final evCount =
        Sqflite.firstIntValue(await db.rawQuery('SELECT COUNT(*) FROM events'));
    if ((evCount ?? 0) < 2) {
      for (final item in mock_data.mockEvents) {
        await db.insert('events', item);
      }
    }

    // 4. Chats
    final chCount =
        Sqflite.firstIntValue(await db.rawQuery('SELECT COUNT(*) FROM chats'));
    if ((chCount ?? 0) == 0) {
      for (final item in mock_data.mockChats) {
        await db.insert('chats', item);
      }
    }
    // 5. Ads
    final adCount =
        Sqflite.firstIntValue(await db.rawQuery('SELECT COUNT(*) FROM ads'));
    if ((adCount ?? 0) < 2) {
      for (final item in mock_data.mockAds) {
        await db.insert('ads', item);
      }
    }

    // 6. Points
    final ptCount = Sqflite.firstIntValue(
        await db.rawQuery('SELECT COUNT(*) FROM user_points'));
    if ((ptCount ?? 0) == 0) {
      await db.insert('user_points', {
        'points': 100,
        'reason': 'Welcome Bonus',
        'timestamp': DateTime.now().toIso8601String()
      });
    }
  }

  // --- Sync Helpers ---

  Future<void> clearTable(String table) async {
    final db = await database;
    await db.delete(table);
  }

  Future<void> upsertMezcalContent(Map<String, dynamic> item) async {
    final db = await database;
    await db.insert('mezcal_content', item,
        conflictAlgorithm: ConflictAlgorithm.replace);
  }

  Future<List<Map<String, dynamic>>> getMezcalContent(String type) async {
    final db = await database;
    return db.query('mezcal_content', where: 'type = ?', whereArgs: [type]);
  }

  Future<void> upsertTequilaContent(Map<String, dynamic> item) async {
    final db = await database;
    await db.insert('tequila_content', item,
        conflictAlgorithm: ConflictAlgorithm.replace);
  }

  Future<List<Map<String, dynamic>>> getTequilaContent(String type) async {
    final db = await database;
    return db.query('tequila_content', where: 'type = ?', whereArgs: [type]);
  }

  Future<void> upsertMarca(Map<String, dynamic> item) async {
    final db = await database;
    await db.insert('marcas', item,
        conflictAlgorithm: ConflictAlgorithm.replace);
  }

  Future<List<Map<String, dynamic>>> getMarcas() async {
    final db = await database;
    return db.query('marcas');
  }

  Future<void> upsertAvatar(Map<String, dynamic> item) async {
    final db = await database;
    await db.insert('avatars', item,
        conflictAlgorithm: ConflictAlgorithm.replace);
  }

  Future<List<Map<String, dynamic>>> getAvatars() async {
    final db = await database;
    return db.query('avatars', orderBy: 'points_required ASC');
  }
}
