import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../services/app_auth_service.dart';
import '../services/localization_service.dart';
import '../services/prefs.dart';
import '../db/app_database.dart';
import '../models/ad.dart';
import '../services/data_sync_service.dart';
import 'cava_page.dart';
import 'scanner_page.dart';
import 'chat_list_page.dart';
import 'reviews_page.dart';
import 'events_page.dart';
import 'dynamic_info_page.dart';
import 'token_login_page.dart';
import 'settings_page.dart';
import '../widgets/ads_banner.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  List<Ad> _ads = [];
  String _username = 'User';
  String _avatarUrl = '';
  int _points = 0;
  final _scrollController = ScrollController();

  final Color primaryGold = const Color(0xFFC5A059);
  final Color darkGray = const Color(0xFF1A1A1A);

  @override
  void initState() {
    super.initState();
    _loadData();
    DataSyncService.instance.syncAll().then((_) {
      if (mounted) _loadData();
    });
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    _loadData();
  }

  Future<void> _loadData() async {
    final adsData = await AppDatabase.instance.getAds();
    final user = await Prefs.getApiUser();
    final avatar = await Prefs.getAvatarUrl();
    final points = await Prefs.getPoints();

    if (mounted) {
      setState(() {
        _ads = adsData.map((e) => Ad.fromMap(e)).toList();
        if (user.isNotEmpty) _username = user;
        _avatarUrl = avatar;
        _points = points;
      });
    }
  }

  Future<void> _logout(BuildContext context) async {
    await AppAuthService.logout();
    if (!context.mounted) return;
    Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const TokenLoginPage()),
        (r) => false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: CustomScrollView(
        controller: _scrollController,
        slivers: [
          // 1. Premium Header
          SliverAppBar(
            expandedHeight: 180,
            floating: false,
            pinned: true,
            backgroundColor: darkGray,
            elevation: 0,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [darkGray, const Color(0xFF333333)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Padding(
                  padding: const EdgeInsets.only(top: 80, left: 24, right: 24),
                  child: Row(
                    children: [
                      _buildAvatar(),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text(
                              '${LocalizationService.tr('home_welcome')},',
                              style: const TextStyle(
                                  color: Colors.white70, fontSize: 14),
                            ),
                            Text(
                              _username,
                              style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 22,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: 0.5),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                      _buildPointsBadge(),
                    ],
                  ),
                ),
              ),
              title: Text(
                _scrollController.hasClients && _scrollController.offset > 100
                    ? LocalizationService.tr('home_title')
                    : '',
                style: const TextStyle(
                    color: Colors.white, fontWeight: FontWeight.bold),
              ),
              centerTitle: true,
            ),
            actions: [
              IconButton(
                  onPressed: () => Navigator.of(context).push(
                      MaterialPageRoute(builder: (_) => const SettingsPage())),
                  icon: const Icon(Icons.tune, color: Colors.white)),
              IconButton(
                  onPressed: () => _logout(context),
                  icon: const Icon(Icons.power_settings_new,
                      color: Colors.white70)),
            ],
          ),

          // 2. Ads Rotating Banner
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.only(top: 24, bottom: 8),
              child: _ads.isNotEmpty
                  ? AdsBannerWidget(ads: _ads)
                  : const SizedBox.shrink(),
            ),
          ),

          // 3. Section Title
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
              child: Text(
                LocalizationService.tr('home_menu'),
                style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w900,
                    color: Colors.grey.shade400,
                    letterSpacing: 2),
              ),
            ),
          ),

          // 4. Premium Menu Grid
          SliverPadding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            sliver: SliverGrid.count(
              crossAxisCount: 2,
              crossAxisSpacing: 16,
              mainAxisSpacing: 16,
              children: [
                _premiumMenuItem(
                    Icons.qr_code_scanner_rounded,
                    LocalizationService.tr('home_tab_scan'),
                    const Color(0xFF6B4EE6),
                    () => const ScannerPage()),
                _premiumMenuItem(
                    Icons.auto_awesome_motion_rounded,
                    LocalizationService.tr('home_tab_cava'),
                    const Color(0xFFE64E6B),
                    () => const CavaPage()),
                _premiumMenuItem(
                    Icons.reviews_rounded,
                    LocalizationService.tr('menu_reviews'),
                    const Color(0xFF4EBDE6),
                    () => const ReviewsPage()),
                _premiumMenuItem(
                    Icons.explore_rounded,
                    LocalizationService.tr('menu_events'),
                    const Color(0xFF4EE6B1),
                    () => const EventsPage()),
                _premiumMenuItem(
                    Icons.chat_bubble_rounded,
                    LocalizationService.tr('chats_title'),
                    const Color(0xFFE6B14E),
                    () => const ChatListPage()),
                _premiumMenuItem(
                    Icons.liquor_rounded,
                    LocalizationService.tr('menu_tequila'),
                    primaryGold,
                    () => DynamicInfoPage(
                        title: LocalizationService.tr('info_tequila'),
                        defaultDescription: 'Master the art of our heritage...',
                        heroTag: 'tequila')),
              ],
            ),
          ),

          // 5. Exit Button
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(40.0),
              child: Center(
                child: TextButton.icon(
                  onPressed: () => SystemNavigator.pop(),
                  icon: const Icon(Icons.close, size: 18),
                  label: const Text('Exit Experience'),
                  style: TextButton.styleFrom(
                    foregroundColor: Colors.grey.shade400,
                    textStyle: const TextStyle(
                        fontWeight: FontWeight.bold, fontSize: 13),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAvatar() {
    return Container(
      padding: const EdgeInsets.all(3),
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: primaryGold, width: 2),
      ),
      child: _avatarUrl.isNotEmpty
          ? CircleAvatar(
              radius: 28,
              backgroundImage: NetworkImage(_avatarUrl),
              onBackgroundImageError: (_, __) => const Icon(Icons.person),
            )
          : CircleAvatar(
              radius: 28,
              backgroundColor: primaryGold.withValues(alpha: 0.2),
              child: Text(
                _username.isNotEmpty ? _username[0].toUpperCase() : 'U',
                style: const TextStyle(
                    fontSize: 22,
                    color: Colors.white,
                    fontWeight: FontWeight.bold),
              ),
            ),
    );
  }

  Widget _buildPointsBadge() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: BoxDecoration(
        color: primaryGold,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: primaryGold.withValues(alpha: 0.3),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            '$_points',
            style: const TextStyle(
                color: Colors.white, fontSize: 18, fontWeight: FontWeight.w900),
          ),
          const Text(
            'PTS',
            style: TextStyle(
                color: Colors.white70,
                fontSize: 10,
                fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }

  Widget _premiumMenuItem(
      IconData icon, String title, Color color, Widget Function() page) {
    return GestureDetector(
      onTap: () =>
          Navigator.of(context).push(MaterialPageRoute(builder: (_) => page())),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 20,
              offset: const Offset(0, 8),
            )
          ],
          border: Border.all(color: Colors.grey.shade50),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, size: 30, color: color),
            ),
            const SizedBox(height: 14),
            Text(
              title,
              style: const TextStyle(
                  fontWeight: FontWeight.w800,
                  fontSize: 14,
                  color: Color(0xFF2D2D2D),
                  letterSpacing: -0.2),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }
}
