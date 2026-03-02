import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '../db/app_database.dart';
import '../services/location_service.dart';
import '../services/prefs.dart';
import '../services/localization_service.dart';
import '../services/eventos_service.dart';
import '../services/api_service.dart';

class ScannerPage extends StatefulWidget {
  const ScannerPage({super.key});
  @override
  State<ScannerPage> createState() => _ScannerPageState();
}

class _ScannerPageState extends State<ScannerPage> {
  final MobileScannerController _controller = MobileScannerController();
  bool _processing = false;
  String _username = 'tequila';

  @override
  void initState() {
    super.initState();
    _loadUser();
  }

  Future<void> _loadUser() async {
    _username = await Prefs.getApiUser();
    if (mounted) setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(LocalizationService.tr('scan_title')),
        actions: [
          IconButton(
            icon: const Icon(Icons.sync),
            tooltip: 'Sync Scans',
            onPressed: _syncScans,
          )
        ],
      ),
      body: Column(
        children: [
          const SizedBox(height: 12),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: MobileScanner(
                  controller: _controller,
                  onDetect: (cap) {
                    if (cap.barcodes.isEmpty) return;
                    final raw = cap.barcodes.first.rawValue;
                    if (raw == null || raw.isEmpty) return;
                    _handleCode(raw);
                  },
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _syncScans() async {
    final pending = await AppDatabase.instance.getPendingScans();
    if (pending.isEmpty) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('No pending scans to sync.')));
      }
      return;
    }

    if (mounted) {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Syncing...')));
    }

    // Use ApiService (mock for now)
    final success = await ApiService.syncScans(pending);

    if (!mounted) return;

    if (success) {
      final ids = pending.map((e) => e['id'] as int).toList();
      await AppDatabase.instance.markScansAsSynced(ids);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Synced ${ids.length} scans!')));
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Sync failed. Try again later.')));
    }
  }

  Future<void> _handleCode(String code) async {
    if (_processing) return;
    setState(() => _processing = true);
    try {
      var bev = await AppDatabase.instance.getBeverageByBarcode(code);

      if (bev == null) {
        try {
          final products = await EventosService.getProducts();
          final matches =
              products.where((p) => p['barcode'].toString() == code).toList();
          if (matches.isNotEmpty) {
            final p = matches.first;
            await AppDatabase.instance.upsertBeverage({
              'barcode': code,
              'name': p['name'] ?? 'Unknown',
              'brand': p['brand'] ?? '',
              'presentation': p['presentation'] ?? '',
              'alcohol_degrees':
                  (p['alcohol_degrees'] is num) ? p['alcohol_degrees'] : 0.0,
              'price': (p['price'] is num) ? p['price'] : 0.0,
            });
            bev = await AppDatabase.instance.getBeverageByBarcode(code);
          }
        } catch (_) {}
      }

      if (bev == null) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(
              content: Text(LocalizationService.tr('scan_not_found'))));
        }
      } else {
        // Log scan
        final pos = await LocationService.getCurrentPosition();
        final timestamp = DateTime.now().toIso8601String();
        await AppDatabase.instance.insertScan({
          'barcode': code,
          'username': _username,
          'latitude': pos.latitude,
          'longitude': pos.longitude,
          'timestamp': timestamp,
          'synced': 0,
          'brand': bev['brand'],
          'presentation': bev['presentation'],
          'alcohol_degrees': bev['alcohol_degrees'],
          'price': bev['price'],
        });

        // Log scan to API and get points
        int pointsAwarded = 10; // Default local
        try {
          final res = await EventosService.scan(code,
              lat: pos.latitude,
              lng: pos.longitude,
              username: _username,
              date: timestamp);
          if (res != null && res['points_awarded'] != null) {
            pointsAwarded = int.tryParse(res['points_awarded'].toString()) ?? 0;
          }
        } catch (_) {}

        // Award Points
        await AppDatabase.instance
            .addPoints(pointsAwarded, 'Scan: ${bev['name']}');

        // Update Prefs for UI
        final currentPoints = await Prefs.getPoints();
        await Prefs.setPoints(currentPoints + pointsAwarded);

        if (!mounted) return;

        // Show success dialog with actions
        final beverage = bev;
        showDialog(
          context: context,
          builder: (ctx) => AlertDialog(
            title: Row(children: [
              const Icon(Icons.check_circle, color: Colors.green),
              const SizedBox(width: 8),
              Expanded(child: Text(beverage['name']))
            ]),
            content: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('${beverage['brand']} - ${beverage['presentation']}'),
                const SizedBox(height: 8),
                Text('Alcohol: ${beverage['alcohol_degrees']}°'),
                const SizedBox(height: 8),
                Text('Price: \$${beverage['price']}',
                    style: const TextStyle(
                        fontWeight: FontWeight.bold, fontSize: 16)),
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                      color: Colors.amber.shade100,
                      borderRadius: BorderRadius.circular(8)),
                  child: Row(children: [
                    const Icon(Icons.stars, color: Colors.amber),
                    const SizedBox(width: 8),
                    Text('+$pointsAwarded Points!')
                  ]),
                ),
                const SizedBox(height: 16),
                const Text('What would you like to do?',
                    style: TextStyle(fontWeight: FontWeight.bold)),
              ],
            ),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  _controller.start(); // Resume scanning
                },
                child: const Text('Cancel'),
              ),
              FilledButton.icon(
                icon: const Icon(Icons.wine_bar),
                label: const Text('Add to Cava'),
                onPressed: () async {
                  await AppDatabase.instance.addToCava(code);
                  await EventosService.addToCava(code);
                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Added to My Cava!')));
                  }
                  if (ctx.mounted) Navigator.pop(ctx);
                },
              ),
            ],
          ),
        ).then((_) => _processing = false); // Reset flag after dialog closes

        // Don't auto-reset processing in finally block if we showed dialog
        return;
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text('Error: $e')));
      }
    } finally {
      // Only reset if we didn't return early (e.g. not found or error)
      if (mounted && _processing) {
        await Future.delayed(const Duration(seconds: 2));
        setState(() => _processing = false);
      }
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }
}
