class Event {
  final int? id;
  final String title;
  final String date;
  final String location;
  final String description;
  final String imagePath;

  Event({this.id, required this.title, required this.date, required this.location, required this.description, required this.imagePath});

  factory Event.fromMap(Map<String, dynamic> map) {
    return Event(
      id: map['id'],
      title: map['title'],
      date: map['date'],
      location: map['location'],
      description: map['description'],
      imagePath: map['image_path'],
    );
  }
}
