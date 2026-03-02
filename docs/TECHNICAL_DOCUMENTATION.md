# Tequila App - Technical Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [Features](#features)
3. [Architecture](#architecture)
4. [Database Schema](#database-schema)
5. [Installation Guide](#installation-guide)
6. [User Guide](#user-guide)
7. [API Integration](#api-integration)
8. [Future Enhancements](#future-enhancements)

---

## Project Overview

**Tequila App** is a comprehensive mobile application built with Flutter for managing tequila and mezcal collections, discovering products, reading reviews, attending events, and connecting with other enthusiasts.

### Technology Stack
- **Framework**: Flutter 3.x
- **Language**: Dart
- **Database**: SQLite (via sqflite package)
- **State Management**: StatefulWidget
- **HTTP Client**: Dio
- **Barcode Scanning**: mobile_scanner
- **Location Services**: geolocator

### Project Structure
```
lib/
├── db/
│   └── app_database.dart          # Database singleton with schema
├── models/
│   ├── ad.dart                     # Ad model
│   ├── review.dart                 # Review model
│   ├── event.dart                  # Event model
│   └── chat.dart                   # Chat & ChatMessage models
├── pages/
│   ├── login_page.dart             # Authentication
│   ├── home_page.dart              # Main menu with grid layout
│   ├── scanner_page.dart           # Barcode scanner
│   ├── cava_page.dart              # Personal collection
│   ├── reviews_page.dart           # Reviews listing/creation
│   ├── events_page.dart            # Events listing
│   ├── chat_list_page.dart         # Chat conversations
│   ├── chat_page.dart              # Individual chat
│   └── info_page.dart              # Tequila/Mezcal info
├── services/
│   ├── api_service.dart            # HTTP wrapper
│   ├── login_service.dart          # Authentication service
│   ├── localization_service.dart   # i18n support
│   └── location_service.dart       # GPS services
├── widgets/
│   └── ads_banner.dart             # Rotating ads banner
└── main.dart                       # App entry point
```

---

## Features

### 1. **Menu & Navigation** ✅
- **Rotating Ads Banner**: Location-based promotional banners displayed at the top of the home screen
- **Grid Menu Layout**: Easy access to all app sections with icons
- **Multi-language Support**: Spanish (es) and English (en)

### 2. **Scan** ✅
- **Barcode Scanner**: Real-time QR/barcode scanning using camera
- **Local Database Search**: Fast lookup of beverages from local SQLite
- **Scan Logging**: Records all scans with GPS coordinates and timestamp
- **Quick Actions**: Add scanned items to personal Cava instantly

### 3. **Cava (Personal Collection)** ✅
- **Catalog View**: Browse all available beverages from system
- **My Cava**: Personal collection tab
- **Add to Collection**: One-tap add from catalog or scanner
- **Detailed Info**: Name, brand, presentation, alcohol content

### 4. **Reviews** ✅
- **View Reviews**: List of approved user reviews with ratings
- **Write Reviews**: Submit reviews with username, comment, and 1-5 star rating
- **Approval System**: Reviews marked as approved/pending (placeholder for admin moderation)
- **Sorting**: Reviews displayed by date (newest first)

### 5. **Events** ✅
- **Location-Based**: Events filtered by user location (mocked)
- **Event Details**: Title, date, location, description, image
- **Card Layout**: Modern card-based UI with event thumbnails

### 6. **Chat** ✅
- **User-to-User Messaging**: Send text messages between users
- **Chat List**: View all active conversations
- **Message History**: Persistent chat storage
- **New Chat**: Initiate conversations via username/email
- **Image Support**: Placeholder for image sharing (future)

### 7. **Tequila & Mezcal Info** ✅
- **Educational Content**: Dedicated pages for tequila and mezcal
- **Rich Media**: Support for images, text, and video (placeholders)
- **Scrollable Content**: Long-form content support

### 8. **Authentication** ✅
- **Email/Password Login**: Standard authentication
- **Token-Based Auth**: Bearer token support for API calls
- **Session Management**: Persistent login state

---

## Architecture

### Design Patterns

#### 1. **Singleton Pattern**
```dart
class AppDatabase {
  AppDatabase._internal();
  static final AppDatabase instance = AppDatabase._internal();
  // ...
}
```
- Used for database access to ensure single connection pool

#### 2. **Repository Pattern**
- Database methods act as repositories for data access
- Clean separation between UI and data layer

#### 3. **Service Layer**
- `ApiService`, `LoginService`, `LocationService` encapsulate business logic
- Reusable across multiple pages

### State Management
- **StatefulWidget**: Primary state management approach
- **ValueNotifier**: Used for locale changes in `LocalizationService`
- **FutureBuilder**: For async data loading in UI

### Navigation
- **Material PageRoute**: Standard push/pop navigation
- **Named Routes**: Not currently used (can be added for deep linking)

---

## Database Schema

### Database Version: 2

#### Tables

##### 1. **beverages**
```sql
CREATE TABLE beverages (
  barcode TEXT PRIMARY KEY,
  name TEXT,
  brand TEXT,
  presentation TEXT,
  alcohol_degrees REAL,
  price REAL
);
```
**Purpose**: Master catalog of all tequila/mezcal products

##### 2. **scans**
```sql
CREATE TABLE scans (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  barcode TEXT,
  username TEXT,
  latitude REAL,
  longitude REAL,
  timestamp TEXT,
  synced INTEGER DEFAULT 0
);
```
**Purpose**: Log all barcode scans with location and time

##### 3. **ads**
```sql
CREATE TABLE ads (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT,
  image_path TEXT,
  description TEXT,
  location TEXT
);
```
**Purpose**: Store promotional banners (location-filtered)

##### 4. **reviews**
```sql
CREATE TABLE reviews (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT,
  content TEXT,
  rating REAL,
  date TEXT,
  approved INTEGER DEFAULT 0
);
```
**Purpose**: User-generated reviews with approval status

##### 5. **events**
```sql
CREATE TABLE events (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT,
  date TEXT,
  location TEXT,
  description TEXT,
  image_path TEXT
);
```
**Purpose**: Tasting events and community gatherings

##### 6. **chats**
```sql
CREATE TABLE chats (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  counterpart TEXT,
  last_message TEXT,
  last_message_time TEXT
);
```
**Purpose**: Chat conversation metadata

##### 7. **chat_messages**
```sql
CREATE TABLE chat_messages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  chat_id INTEGER,
  sender TEXT,
  content TEXT,
  timestamp TEXT,
  is_image INTEGER DEFAULT 0
);
```
**Purpose**: Individual chat messages

##### 8. **my_cava**
```sql
CREATE TABLE my_cava (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  barcode TEXT,
  notes TEXT
);
```
**Purpose**: User's personal collection

### Database Helper Methods

```dart
// Beverages
Future<Map<String, dynamic>?> getBeverageByBarcode(String barcode)
Future<List<Map<String, dynamic>>> listBeverages()

// Scans
Future<int> insertScan(Map<String, dynamic> scan)

// Ads
Future<List<Map<String, dynamic>>> getAds()

// Reviews
Future<List<Map<String, dynamic>>> getReviews()
Future<int> insertReview(Map<String, dynamic> review)

// Events
Future<List<Map<String, dynamic>>> getEvents()

// Chats
Future<List<Map<String, dynamic>>> getChats()
Future<int> createChat(String counterpart)
Future<List<Map<String, dynamic>>> getChatMessages(int chatId)
Future<int> insertChatMessage(Map<String, dynamic> msg)

// Cava
Future<List<Map<String, dynamic>>> getMyCava()
Future<int> addToCava(String barcode)
```

---

## Installation Guide

### Prerequisites
- Flutter SDK 3.x or higher
- Android Studio / VS Code
- Android SDK (for Android development)
- Xcode (for iOS development, macOS only)

### Steps

1. **Clone the Repository**
```bash
cd c:\Users\erson\Downloads\tequila_app\tequila_app
```

2. **Install Dependencies**
```bash
flutter pub get
```

3. **Configure API Endpoints** (Optional)
- Update base URL in Settings page or `services/api_service.dart`

4. **Run the App**
```bash
flutter run
```

5. **Build for Production**
```bash
# Android APK
flutter build apk --release

# Android App Bundle
flutter build appbundle --release

# iOS
flutter build ios --release
```

---

## User Guide

### Getting Started

#### 1. **Login**
- Enter your email and password
- Or use Advanced Debug mode for quick testing
- Configure API endpoints via Settings if needed

#### 2. **Home Screen**
- View rotating promotional banners at the top
- Access all features via the grid menu

### Using Features

#### **Scanning Products**
1. Tap **SCAN** from the home menu
2. Point camera at barcode
3. View product details in dialog
4. Tap **Add to Cava** to save

#### **Managing Your Cava**
1. Tap **CAVA** from the home menu
2. **Catalog Tab**: Browse all beverages, tap + to add
3. **My Cava Tab**: View your saved collection

#### **Reading & Writing Reviews**
1. Tap **Reviews** from the home menu
2. Browse existing reviews
3. Tap **+** button to add your review
4. Enter username, comment, rating (1-5 stars)
5. Submit (approval pending)

#### **Viewing Events**
1. Tap **Events** from the home menu
2. View upcoming tastings and gatherings
3. Events are filtered by location

#### **Chatting with Users**
1. Tap **Chat** from the home menu
2. Tap **+** to start new chat
3. Enter username/email of recipient
4. Send text messages
5. Messages are saved locally

#### **Learning About Products**
1. Tap **Tequila** or **Mezcal** from home menu
2. Read educational content
3. Watch video tutorials (placeholder)

---

## API Integration

### Current Status
The app is designed to work with a backend API but currently uses local SQLite for all data. The following endpoints are expected:

### Authentication
```
POST /api/login
Body: { email, password }
Response: { token, user }
```

### Beverages
```
GET /api/beverages
GET /api/beverages/{barcode}
```

### Ads
```
GET /api/ads?location={location}
```

### Events
```
GET /api/events?location={location}
```

### Reviews
```
GET /api/reviews
POST /api/reviews
Body: { username, content, rating, beverage_id }
```

### Chats (Future - WebSocket/Real-time)
```
WS /api/chat
POST /api/chat/messages
GET /api/chat/conversations
```

### Syncing Scans
```
POST /api/scans
Body: { barcode, latitude, longitude, timestamp }
```

### Configuration
Update API base URL in `services/prefs.dart` or via Settings page:
- Default: `https://api.example.com`

---

## Future Enhancements

### Phase 1: Backend Integration
- [ ] Connect to real API for beverages catalog
- [ ] Sync scans to server
- [ ] Real-time chat via WebSocket
- [ ] Cloud-based review moderation

### Phase 2: Enhanced Features
- [ ] Push notifications for events
- [ ] Social sharing (reviews, favorites)
- [ ] Advanced search and filters
- [ ] QR code generation for sharing

### Phase 3: Gamification
- [ ] Achievement system
- [ ] Leaderboards
- [ ] Collection badges
- [ ] Tasting challenges

### Phase 4: E-Commerce
- [ ] In-app purchases
- [ ] Product recommendations
- [ ] Affiliate links
- [ ] Event ticket sales

---

## Support & Contact

For technical support or questions:
- **Developer**: Tequila App Team
- **Version**: 1.0.0
- **Last Updated**: January 2026

---

*This documentation is subject to updates as new features are added.*
