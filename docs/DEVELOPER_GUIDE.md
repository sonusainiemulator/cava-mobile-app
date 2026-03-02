# Tequila App - Developer Guide

## Table of Contents
1. [Development Environment Setup](#development-environment-setup)
2. [Project Architecture](#project-architecture)
3. [Code Standards](#code-standards)
4. [Database Management](#database-management)
5. [Adding New Features](#adding-new-features)
6. [Testing](#testing)
7. [Building & Deployment](#building--deployment)
8. [API Integration Guide](#api-integration-guide)

---

## Development Environment Setup

### Required Tools
```bash
# Flutter SDK
flutter --version  # Should be 3.x or higher

# IDE (Choose one)
- VS Code with Flutter extension
- Android Studio with Flutter plugin
- IntelliJ IDEA with Flutter plugin
```

### Clone & Setup
```bash
cd c:\Users\erson\Downloads\tequila_app\tequila_app
flutter pub get
flutter doctor  # Check for any issues
```

### Run Development Build
```bash
flutter run --dart-define=ADVANCED_DEBUG=true
```

---

## Project Architecture

### Folder Structure
```
lib/
├── db/                  # Database layer
├── models/              # Data models
├── pages/               # UI screens
├── services/            # Business logic & API
├── widgets/             # Reusable components
└── main.dart           # Entry point
```

### Key Design Principles

#### 1. **Single Responsibility**
Each class/file has one clear purpose.

#### 2. **Separation of Concerns**
- **UI (Pages)**: Flutter widgets, user interaction
- **Logic (Services)**: Business rules, API calls
- **Data (Database)**: Storage, queries
- **Models**: Data structures

#### 3. **DRY (Don't Repeat Yourself)**
Reusable widgets and helper methods.

---

## Code Standards

### Dart Style Guide

```dart
// ✅ Good: Descriptive names
class BeverageDetailPage extends StatefulWidget { }

// ❌ Bad: Vague names
class Page2 extends StatefulWidget { }

// ✅ Good: Constants in UPPER_CASE
const String API_BASE_URL = 'https://api.example.com';

// ✅ Good: Private members start with _
String _privateField;

// ✅ Good: Async/await for readability
Future<void> loadData() async {
  final data = await database.query('table');
}

// ❌ Bad: Nested .then() chains
database.query('table').then((data) => { ... });
```

### Widget Building Best Practices

```dart
// ✅ Good: Extract to methods
Widget build(BuildContext context) {
  return Scaffold(
    appBar: _buildAppBar(),
    body: _buildBody(),
  );
}

Widget _buildAppBar() => AppBar(title: Text('Title'));

// ✅ Good: Use const when possible
const SizedBox(height: 16);

// ✅ Good: Named parameters for clarity
_menuItem(
  icon: Icons.scan,
  title: 'Scan',
  onTap: () => _handleScan(),
);
```

### Error Handling

```dart
Future<void> fetchData() async {
  try {
    final response = await ApiService.get('/endpoint');
    // Process response
  } on DioException catch (e) {
    // Network error
    _showError('Network error: ${e.message}');
  } catch (e) {
    // General error
    _showError('An error occurred');
  }
}
```

---

## Database Management

### Schema Migrations

When updating the database schema:

1. **Increment Version Number**
```dart
// In app_database.dart
return openDatabase(path, version: 3, onCreate: ...
```

2. **Add Migration Logic**
```dart
onUpgrade: (db, oldVersion, newVersion) async {
  if (oldVersion < 3) {
    await db.execute('ALTER TABLE beverages ADD COLUMN region TEXT');
  }
}
```

3. **Test Migration**
- Install old version on device
- Upgrade to new version
- Verify data integrity

### Adding a New Table

```dart
// 1. Create table in _createDb method
await db.execute('''
  CREATE TABLE new_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    created_at TEXT
  )
''');

// 2. Add to onUpgrade for existing users
if (oldVersion < 4) {
  await db.execute('CREATE TABLE new_table ...');
}

// 3. Add helper methods
Future<List<Map<String, dynamic>>> getNewTableData() async {
  final db = await database;
  return db.query('new_table');
}

Future<int> insertIntoNewTable(Map<String, dynamic> data) async {
  final db = await database;
  return db.insert('new_table', data);
}
```

---

## Adding New Features

### Checklist for New Feature

#### 1. **Database Changes** (if needed)
- [ ] Add/modify table schema
- [ ] Increment database version
- [ ] Add migration logic
- [ ] Add helper methods in `AppDatabase`

#### 2. **Create Model** (if needed)
```dart
// models/my_feature.dart
class MyFeature {
  final int? id;
  final String name;
  
  MyFeature({this.id, required this.name});
  
  factory MyFeature.fromMap(Map<String, dynamic> map) {
    return MyFeature(
      id: map['id'],
      name: map['name'],
    );
  }
  
  Map<String, dynamic> toMap() {
    return {
      'name': name,
    };
  }
}
```

#### 3. **Create Page**
```dart
// pages/my_feature_page.dart
class MyFeaturePage extends StatefulWidget {
  const MyFeaturePage({super.key});

  @override
  State<MyFeaturePage> createState() => _MyFeaturePageState();
}

class _MyFeaturePageState extends State<MyFeaturePage> {
  List<MyFeature> _items = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _loading = true);
    final data = await AppDatabase.instance.getMyFeatureData();
    _items = data.map((e) => MyFeature.fromMap(e)).toList();
    setState(() => _loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('My Feature')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : ListView.builder(
              itemCount: _items.length,
              itemBuilder: (context, index) {
                final item = _items[index];
                return ListTile(
                  title: Text(item.name),
                );
              },
            ),
    );
  }
}
```

#### 4. **Add to Navigation**
```dart
// In home_page.dart
_menuItem(
  Icons.new_feature,
  'My Feature',
  () => const MyFeaturePage()
),
```

#### 5. **Add Localization**
```dart
// In localization_service.dart
'es': {
  'my_feature_title': 'Mi Función',
  // ...
},
'en': {
  'my_feature_title': 'My Feature',
  // ...
},
```

#### 6. **Test**
- Manual testing on device/emulator
- Edge cases (empty data, errors, etc.)
- Different screen sizes

---

## Testing

### Manual Testing Checklist

- [ ] Login flow
- [ ] Each menu item navigates correctly
- [ ] Scanner works with sample barcodes
- [ ] Add to Cava functionality
- [ ] Review submission
- [ ] Chat message sending
- [ ] Language switching
- [ ] Logout

### Test Data

#### Sample Barcodes
```
7501000123456 - Tequila Blanco Tradicional
7502000654321 - Mezcal Joven Artesanal
```

#### Test Accounts
```
Email: admin01@3ware.mx
Password: Admin*246
```

### Device Testing Matrix

| Device | Android Version | Status |
|--------|----------------|--------|
| Pixel 6 | 13 | ✅ |
| Samsung Galaxy S21 | 12 | ✅ |
| Emulator | 11 | ✅ |

---

## Building & Deployment

### Android APK

```bash
# Debug build
flutter build apk --debug

# Release build
flutter build apk --release

# Output: build/app/outputs/flutter-apk/app-release.apk
```

### Android App Bundle (for Play Store)

```bash
flutter build appbundle --release

# Output: build/app/outputs/bundle/release/app-release.aab
```

### iOS (macOS required)

```bash
flutter build ios --release

# Then open in Xcode for signing and upload
open ios/Runner.xcworkspace
```

### Version Management

Update version in `pubspec.yaml`:
```yaml
version: 1.0.0+1  # version+build
```

---

## API Integration Guide

### Setting Up API Service

#### 1. **Configure Base URL**
```dart
// In services/prefs.dart or settings
static const String DEFAULT_API_URL = 'https://api.yourserver.com';
```

#### 2. **Create API Endpoint Methods**
```dart
// In services/api_service.dart
class ApiService {
  static Future<List<Beverage>> fetchBeverages() async {
    final response = await get('/beverages');
    final List data = response.data;
    return data.map((json) => Beverage.fromJson(json)).toList();
  }
  
  static Future<void> syncScans() async {
    final scans = await AppDatabase.instance.getUnsynced();
    await post('/scans/batch', data: scans);
    // Mark as synced
  }
}
```

#### 3. **Authentication**
```dart
// services/api_client.dart already handles Bearer token
dio.interceptors.add(InterceptorsWrapper(
  onRequest: (options, handler) async {
    if (!options.extra['noAuth']) {
      final token = await Prefs.getApiToken();
      options.headers['Authorization'] = 'Bearer $token';
    }
    return handler.next(options);
  },
));
```

### Real-time Features (WebSocket)

For chat and live updates:

```dart
// services/websocket_service.dart
import 'package:web_socket_channel/web_socket_channel.dart';

class WebSocketService {
  late WebSocketChannel _channel;
  
  void connect() {
    _channel = WebSocketChannel.connect(
      Uri.parse('wss://api.yourserver.com/ws'),
    );
    
    _channel.stream.listen((message) {
      // Handle incoming messages
    });
  }
  
  void sendMessage(String message) {
    _channel.sink.add(message);
  }
  
  void close() {
    _channel.sink.close();
  }
}
```

### Data Sync Strategy

```dart
Future<void> syncData() async {
  // 1. Check connectivity
  final hasConnection = await _checkConnection();
  if (!hasConnection) return;
  
  // 2. Upload local changes
  await _uploadScans();
  await _uploadReviews();
  
  // 3. Download updates
  await _downloadBeverages();
  await _downloadEvents();
  await _downloadAds();
  
  // 4. Mark sync time
  await Prefs.setLastSync(DateTime.now());
}
```

---

## Performance Optimization

### Database Queries

```dart
// ✅ Good: Use indexes
CREATE INDEX idx_beverages_barcode ON beverages(barcode);

// ✅ Good: Limit results
db.query('table', limit: 50);

// ✅ Good: Use transactions for multiple writes
await db.transaction((txn) async {
  await txn.insert('table1', data1);
  await txn.insert('table2', data2);
});
```

### UI Performance

```dart
// ✅ Good: Use ListView.builder for long lists
ListView.builder(
  itemCount: items.length,
  itemBuilder: (context, index) => ItemWidget(items[index]),
);

// ❌ Bad: Creating all widgets at once
ListView(
  children: items.map((item) => ItemWidget(item)).toList(),
);

// ✅ Good: Cache complex calculations
final expensiveValue = useMemoized(() => calculateValue());

// ✅ Good: Debounce search
Timer? _debounce;
_onSearchChanged(String query) {
  _debounce?.cancel();
  _debounce = Timer(Duration(milliseconds: 500), () => _search(query));
}
```

---

## Debugging Tips

### Enable Logging

```dart
// In services/api_client.dart
dio.interceptors.add(LogInterceptor(
  request: true,
  requestBody: true,
  responseBody: true,
));
```

### Database Inspection

```dart
// Print all tables
final tables = await db.rawQuery(
  "SELECT name FROM sqlite_master WHERE type='table'"
);
print(tables);

// View table contents
final rows = await db.query('beverages');
print(rows);
```

### Common Issues

**Issue**: Hot reload not working
**Fix**: Use hot restart (flutter run -d <device>)

**Issue**: Database not updating
**Fix**: Uninstall app and reinstall to recreate database

**Issue**: Build errors after adding package
**Fix**: `flutter clean && flutter pub get`

---

## Git Workflow

### Branch Strategy
```bash
main          # Production-ready code
develop       # Development branch
feature/*     # New features
bugfix/*      # Bug fixes
```

### Commit Messages
```bash
# Good examples
git commit -m "feat: Add reviews page with rating system"
git commit -m "fix: Scanner dialog not closing properly"
git commit -m "refactor: Extract database helpers to separate methods"
git commit -m "docs: Update API integration guide"
```

---

## Resources

### Official Documentation
- [Flutter Docs](https://flutter.dev/docs)
- [Dart Language Tour](https://dart.dev/guides/language/language-tour)
- [sqflite Package](https://pub.dev/packages/sqflite)

### Useful Packages
- `dio` - HTTP client
- `mobile_scanner` - Barcode scanning
- `geolocator` - Location services
- `shared_preferences` - Key-value storage

---

*Happy Coding! 🚀*
