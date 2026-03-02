import 'dart:async';
import 'package:flutter/material.dart';
import '../models/ad.dart';

class AdsBannerWidget extends StatefulWidget {
  final List<Ad> ads;

  const AdsBannerWidget({super.key, required this.ads});

  @override
  State<AdsBannerWidget> createState() => _AdsBannerWidgetState();
}

class _AdsBannerWidgetState extends State<AdsBannerWidget> {
  final PageController _pageController = PageController();
  Timer? _timer;
  int _currentPage = 0;

  @override
  void initState() {
    super.initState();
    _startAutoScroll();
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    super.dispose();
  }

  void _startAutoScroll() {
    _timer = Timer.periodic(const Duration(seconds: 5), (Timer timer) {
      if (widget.ads.isEmpty) return;

      if (_currentPage < widget.ads.length - 1) {
        _currentPage++;
      } else {
        _currentPage = 0;
      }

      if (_pageController.hasClients) {
        _pageController.animateToPage(
          _currentPage,
          duration: const Duration(milliseconds: 800),
          curve: Curves.fastOutSlowIn,
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if (widget.ads.isEmpty) return const SizedBox.shrink();

    return SizedBox(
      height: 180, // Slightly taller for better layout
      child: PageView.builder(
        controller: _pageController,
        itemCount: widget.ads.length,
        onPageChanged: (int page) {
          setState(() {
            _currentPage = page;
          });
        },
        itemBuilder: (context, index) {
          final ad = widget.ads[index];
          // Cycle through some nice gradients
          final List<List<Color>> gradients = [
            [Colors.teal.shade700, Colors.teal.shade900],
            [Colors.indigo.shade700, Colors.indigo.shade900],
            [Colors.orange.shade700, Colors.deepOrange.shade900],
            [Colors.purple.shade700, Colors.purple.shade900],
          ];
          final gradient = gradients[index % gradients.length];

          bool isNetworkImage = ad.imagePath.startsWith('http');

          return Container(
            margin: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(20),
              gradient: LinearGradient(
                colors: gradient,
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              boxShadow: [
                BoxShadow(
                  color: gradient.last.withValues(alpha: 0.4),
                  blurRadius: 8,
                  offset: const Offset(0, 4),
                )
              ],
            ),
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Expanded(
                  flex: 3,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: Colors.white24,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          'FEATURED',
                          style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.9),
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 1),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        ad.title,
                        style: const TextStyle(
                            color: Colors.white,
                            fontSize: 18,
                            fontWeight: FontWeight.bold),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 4),
                      Text(
                        ad.description,
                        style: const TextStyle(
                            color: Colors.white70, fontSize: 13),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          const Icon(Icons.location_on,
                              color: Colors.white60, size: 14),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              ad.location,
                              style: const TextStyle(
                                  color: Colors.white60, fontSize: 12),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 12),
                // Display remote image with fallback
                Container(
                  width: 100,
                  height: 100,
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: Colors.white24),
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(16),
                    child: ad.imagePath.isEmpty
                        ? Center(
                            child: Icon(Icons.local_offer_outlined,
                                color: Colors.white.withValues(alpha: 0.8), size: 40))
                        : isNetworkImage
                            ? Image.network(
                                ad.imagePath,
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => Center(
                                    child: Icon(Icons.broken_image,
                                        color: Colors.white.withValues(alpha: 0.5))),
                              )
                            : Image.asset(
                                ad.imagePath,
                                fit: BoxFit.cover,
                                errorBuilder: (_, __, ___) => Center(
                                    child: Icon(Icons.image_not_supported,
                                        color: Colors.white.withValues(alpha: 0.5))),
                              ),
                  ),
                )
              ],
            ),
          );
        },
      ),
    );
  }
}
