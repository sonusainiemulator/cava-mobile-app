import 'package:flutter/material.dart';
import '../db/app_database.dart';

class CavaPage extends StatefulWidget {
  const CavaPage({super.key});
  @override
  State<CavaPage> createState() => _CavaPageState();
}

class _CavaPageState extends State<CavaPage> {
  bool _loading = true;
  List<Map<String, dynamic>> _items = [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    _items = await AppDatabase.instance.listBeverages();
    if (mounted) setState(() => _loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Cava'),
          bottom: const TabBar(
            tabs: [
              Tab(text: 'Catálogo / Catalog'),
              Tab(text: 'Mi Cava / My Cava'),
            ],
          ),
        ),
        body: TabBarView(
          children: [
            _buildCatalog(),
            _buildMyCava(),
          ],
        ),
      ),
    );
  }

  Widget _buildCatalog() {
    if (_loading) return const Center(child: CircularProgressIndicator());
    return ListView.builder(
      itemCount: _items.length,
      itemBuilder: (_, i) {
        final item = _items[i];
        return ListTile(
          leading: const Icon(Icons.liquor),
          title: Text(item['name']?.toString() ?? ''),
          subtitle: Text('${item['brand']} • ${item['price']}'),
          trailing: IconButton(
            icon: const Icon(Icons.add_circle_outline),
            onPressed: () async {
              await AppDatabase.instance.addToCava(item['barcode']);
              setState(
                  () {}); // Refresh? well we need to refresh my cava tab ideally
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Added to My Cava')));
              }
            },
          ),
        );
      },
    );
  }

  Widget _buildMyCava() {
    return FutureBuilder<List<Map<String, dynamic>>>(
      future: AppDatabase.instance.getMyCava(),
      builder: (ctx, snapshot) {
        if (!snapshot.hasData) {
          return const Center(child: CircularProgressIndicator());
        }
        final myItems = snapshot.data!;
        if (myItems.isEmpty) return const Center(child: Text('Empty / Vacía'));

        return ListView.builder(
          itemCount: myItems.length,
          itemBuilder: (_, i) {
            final item = myItems[i];
            return ListTile(
              leading: const Icon(Icons.bookmark, color: Colors.teal),
              title: Text(item['name']?.toString() ?? 'Unknown'),
              subtitle: Text(item['brand']?.toString() ?? ''),
              trailing: IconButton(
                icon: const Icon(Icons.delete, color: Colors.red),
                onPressed: () async {
                  final confirm = await showDialog<bool>(
                      context: context,
                      builder: (ctx) => AlertDialog(
                            title: const Text('Remove from Cava?'),
                            actions: [
                              TextButton(
                                  onPressed: () => Navigator.pop(ctx, false),
                                  child: const Text('Cancel')),
                              TextButton(
                                  onPressed: () => Navigator.pop(ctx, true),
                                  child: const Text('Remove')),
                            ],
                          ));
                  if (confirm == true) {
                    await AppDatabase.instance.removeFromCava(item['barcode']);
                    setState(() {});
                  }
                },
              ),
            );
          },
        );
      },
    );
  }
}
