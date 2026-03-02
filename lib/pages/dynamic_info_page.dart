import 'package:flutter/material.dart';
import '../db/app_database.dart';

class DynamicInfoPage extends StatefulWidget {
  final String title;
  final String defaultDescription;
  final String heroTag; // 'tequila' or 'mezcal'

  const DynamicInfoPage({
    super.key,
    required this.title,
    required this.defaultDescription,
    required this.heroTag,
  });

  @override
  State<DynamicInfoPage> createState() => _DynamicInfoPageState();
}

class _DynamicInfoPageState extends State<DynamicInfoPage> {
  List<Map<String, dynamic>> _texts = [];
  List<Map<String, dynamic>> _images = [];
  List<Map<String, dynamic>> _videos = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadContent();
  }

  Future<void> _loadContent() async {
    try {
      if (widget.heroTag == 'mezcal') {
        _texts = await AppDatabase.instance.getMezcalContent('text');
        _images = await AppDatabase.instance.getMezcalContent('image');
        _videos = await AppDatabase.instance.getMezcalContent('video');
      } else if (widget.heroTag == 'tequila') {
        _texts = await AppDatabase.instance.getTequilaContent('text');
        _images = await AppDatabase.instance.getTequilaContent('image');
        _videos = await AppDatabase.instance.getTequilaContent('video');
      }
    } catch (e) {
      debugPrint('Error loading dynamic info: $e');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    String description = widget.defaultDescription;
    if (_texts.isNotEmpty) {
      // Use the first text entry as description if available from server
      description =
          _texts.first['text'] ?? _texts.first['title'] ?? description;
    }

    return Scaffold(
      appBar: AppBar(title: Text(widget.title)),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              child: Column(
                children: [
                  // 1. Hero Image
                  _buildHeroImage(),

                  Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(widget.title,
                            style: const TextStyle(
                                fontSize: 32, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 16),
                        Text(
                          description,
                          style: const TextStyle(
                              fontSize: 16, height: 1.6, color: Colors.black87),
                        ),
                        const SizedBox(height: 32),

                        // 2. Images Gallery
                        if (_images.length > 1) _buildGallery(),

                        // 3. Videos Section
                        if (_videos.isNotEmpty) _buildVideos(),

                        const SizedBox(height: 40),
                      ],
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildHeroImage() {
    String? url;
    if (_images.isNotEmpty) {
      url = _images.first['url'];
    }

    return Container(
      height: 300,
      width: double.infinity,
      decoration: BoxDecoration(
        color: Colors.grey.shade200,
      ),
      child: url != null && url.startsWith('http')
          ? Image.network(url, fit: BoxFit.cover)
          : Stack(
              children: [
                Center(
                  child: Icon(Icons.liquor,
                      size: 100, color: Colors.grey.shade400),
                ),
                Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [
                        Colors.black.withValues(alpha: 0.4),
                        Colors.transparent
                      ],
                      begin: Alignment.bottomCenter,
                      end: Alignment.topCenter,
                    ),
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildGallery() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Gallery',
            style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
        const SizedBox(height: 12),
        SizedBox(
          height: 150,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: _images.length - 1,
            itemBuilder: (ctx, i) {
              final img = _images[i + 1];
              final imgUrl = img['url'] ?? '';
              return Container(
                width: 200,
                margin: const EdgeInsets.only(right: 12),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(16),
                  color: Colors.grey.shade100,
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(16),
                  child: imgUrl.startsWith('http')
                      ? Image.network(imgUrl, fit: BoxFit.cover)
                      : const Icon(Icons.image, color: Colors.grey),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 32),
      ],
    );
  }

  Widget _buildVideos() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Videos',
            style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
        const SizedBox(height: 12),
        ..._videos.map((v) => Container(
              margin: const EdgeInsets.only(bottom: 16),
              height: 200,
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.black87,
                borderRadius: BorderRadius.circular(16),
                image: v['url'] != null && v['url'].toString().contains('img')
                    ? DecorationImage(
                        image: NetworkImage(v['url']),
                        fit: BoxFit.cover,
                        opacity: 0.5)
                    : null,
              ),
              child: Stack(
                children: [
                  Center(
                    child: Icon(Icons.play_circle_fill,
                        color: Colors.white.withValues(alpha: 0.9), size: 64),
                  ),
                  Positioned(
                    bottom: 16,
                    left: 16,
                    child: Text(
                      v['title'] ?? 'Watch Experience',
                      style: const TextStyle(
                          color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  )
                ],
              ),
            )),
      ],
    );
  }
}
