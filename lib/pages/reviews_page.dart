import 'package:flutter/material.dart';
import '../db/app_database.dart';
import '../models/review.dart';
import '../services/localization_service.dart';

class ReviewsPage extends StatefulWidget {
  const ReviewsPage({super.key});

  @override
  State<ReviewsPage> createState() => _ReviewsPageState();
}

class _ReviewsPageState extends State<ReviewsPage> {
  List<Review> _reviews = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadreviews();
  }

  Future<void> _loadreviews() async {
    setState(() => _loading = true);
    final data = await AppDatabase.instance.getReviews();
    _reviews = data.map((e) => Review.fromMap(e)).toList();
    setState(() => _loading = false);
  }

  Future<void> _addReview() async {
    final userCtrl = TextEditingController();
    final contentCtrl = TextEditingController();
    double rating = 5.0;

    await showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(LocalizationService.tr('reviews_add')),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
                controller: userCtrl,
                decoration: const InputDecoration(labelText: 'Username')),
            TextField(
                controller: contentCtrl,
                decoration: const InputDecoration(labelText: 'Comment'),
                maxLines: 3),
            const SizedBox(height: 10),
            Row(
              children: [
                const Text('Rating: '),
                Expanded(
                  child: Slider(
                    value: rating,
                    min: 1,
                    max: 5,
                    divisions: 4,
                    label: rating.toString(),
                    onChanged: (v) => (ctx as Element)
                        .markNeedsBuild(), // Hacky refresh for dialog
                  ),
                ),
              ],
            )
          ],
        ),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () async {
              if (userCtrl.text.isEmpty || contentCtrl.text.isEmpty) return;

              final newReview = Review(
                  username: userCtrl.text,
                  content: contentCtrl.text,
                  rating: rating,
                  date: DateTime.now().toIso8601String(),
                  approved: true // Auto approve for demo
                  );

              await AppDatabase.instance.insertReview(newReview.toMap());
              if (ctx.mounted) Navigator.pop(ctx);
              _loadreviews();
            },
            child: const Text('Submit'),
          )
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(LocalizationService.tr('reviews_title'))),
      floatingActionButton: FloatingActionButton(
        onPressed: _addReview,
        tooltip: LocalizationService.tr('reviews_add'),
        child: const Icon(Icons.add),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _reviews.isEmpty
              ? const Center(
                  child: Text(
                      'No reviews yet.')) // Could localize this too if added

              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _reviews.length,
                  itemBuilder: (context, index) {
                    final r = _reviews[index];
                    return Card(
                      margin: const EdgeInsets.only(bottom: 12),
                      child: ListTile(
                        leading: CircleAvatar(
                            child: Text(r.username[0].toUpperCase())),
                        title: Row(
                          children: [
                            Text(r.username,
                                style: const TextStyle(
                                    fontWeight: FontWeight.bold)),
                            const Spacer(),
                            const Icon(Icons.star,
                                size: 16, color: Colors.amber),
                            Text(r.rating.toString()),
                          ],
                        ),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(r.content),
                            const SizedBox(height: 4),
                            Text(r.date.split('T')[0],
                                style: const TextStyle(
                                    fontSize: 10, color: Colors.grey)),
                          ],
                        ),
                      ),
                    );
                  },
                ),
    );
  }
}
