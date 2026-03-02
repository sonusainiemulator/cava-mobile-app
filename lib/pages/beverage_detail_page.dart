import 'package:flutter/material.dart';
import '../db/app_database.dart';
import '../services/location_service.dart';

class BeverageDetailPage extends StatefulWidget {
  final Map<String, dynamic> beverage;
  final String barcode;
  final String username;

  const BeverageDetailPage({
    super.key,
    required this.beverage,
    required this.barcode,
    required this.username,
  });

  @override
  State<BeverageDetailPage> createState() => _BeverageDetailPageState();
}

class _BeverageDetailPageState extends State<BeverageDetailPage> {
  final _presentationCtl = TextEditingController();
  final _alcoholCtl = TextEditingController();
  final _priceCtl = TextEditingController();

  bool _saving = false;
  String? _locText;

  @override
  void initState() {
    super.initState();
    _presentationCtl.text = (widget.beverage['presentation'] ?? '').toString();
    _alcoholCtl.text = widget.beverage['alcohol_degrees']?.toString() ?? '';
    _priceCtl.text = widget.beverage['price']?.toString() ?? '';
  }

  @override
  void dispose() {
    _presentationCtl.dispose();
    _alcoholCtl.dispose();
    _priceCtl.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    setState(() => _saving = true);
    try {
      final pos = await LocationService.getCurrentPosition();
      final lat = pos.latitude;
      final lng = pos.longitude;

      setState(() => _locText = 'Lat: ${lat.toStringAsFixed(6)}, Lng: ${lng.toStringAsFixed(6)}');

      final alcohol = double.tryParse(_alcoholCtl.text.trim());
      final price = double.tryParse(_priceCtl.text.trim());

      final scan = {
        'beverage_id': widget.beverage['id'],
        'barcode': widget.barcode,
        'username': widget.username,
        'beverage_name': widget.beverage['name'],
        'presentation': _presentationCtl.text.trim().isEmpty ? null : _presentationCtl.text.trim(),
        'alcohol_degrees': alcohol,
        'price': price,
        'latitude': lat,
        'longitude': lng,
        'timestamp': DateTime.now().toIso8601String(),
        'synced': 0,
      };

      await AppDatabase.instance.insertScan(scan);

      if (!mounted) return;
      Navigator.of(context).pop(true);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e')));
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final b = widget.beverage;
    final title = (b['name'] ?? '').toString();
    final brand = (b['brand'] ?? '').toString();

    return Scaffold(
      appBar: AppBar(title: const Text('Detalle de bebida')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
            const SizedBox(height: 4),
            Text('Marca: $brand'),
            const SizedBox(height: 8),
            Text('Usuario: ${widget.username}', style: const TextStyle(fontSize: 12)),
            const SizedBox(height: 12),
            if (_locText != null) Text('Ubicación: $_locText', style: const TextStyle(fontSize: 12)),
            const SizedBox(height: 12),
            Expanded(
              child: ListView(
                children: [
                  TextField(controller: _presentationCtl, decoration: const InputDecoration(labelText: 'Presentación')),
                  const SizedBox(height: 10),
                  TextField(controller: _alcoholCtl, decoration: const InputDecoration(labelText: 'Grados de alcohol (%)')),
                  const SizedBox(height: 10),
                  TextField(controller: _priceCtl, decoration: const InputDecoration(labelText: 'Precio')),
                  const SizedBox(height: 22),
                  FilledButton.icon(
                    onPressed: _saving ? null : _save,
                    icon: _saving
                        ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2))
                        : const Icon(Icons.save),
                    label: const Text('Guardar escaneo local'),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
