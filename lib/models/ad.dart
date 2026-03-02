class Ad {
  final int? id;
  final String title;
  final String imagePath;
  final String description;
  final String location;

  Ad({this.id, required this.title, required this.imagePath, required this.description, required this.location});

  factory Ad.fromMap(Map<String, dynamic> map) {
    return Ad(
      id: map['id'],
      title: map['title'],
      imagePath: map['image_path'],
      description: map['description'],
      location: map['location'],
    );
  }
}
