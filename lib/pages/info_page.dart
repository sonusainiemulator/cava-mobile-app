import 'package:flutter/material.dart';
import '../services/localization_service.dart';

class InfoPage extends StatelessWidget {
  final String title;
  final String description;
  final String heroTag;

  const InfoPage({super.key, required this.title, required this.description, this.heroTag = 'info'});

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: Text(LocalizationService.tr('info_tequila')), // Or similar
          bottom: const TabBar(
            tabs: [
              Tab(text: 'Tequila'),
              Tab(text: 'Mezcal'),
            ],
          ),
        ),
        body: const TabBarView(
          children: [
            Center(child: Text('Tequila Content')),
            Center(child: Text('Mezcal Content')),
          ],
        ),
      ),
    );
  }
}
