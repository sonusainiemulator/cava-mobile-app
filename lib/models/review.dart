class Review {
  final int? id;
  final String username;
  final String content;
  final double rating;
  final String date;
  final bool approved;

  Review({this.id, required this.username, required this.content, required this.rating, required this.date, this.approved = false});

  factory Review.fromMap(Map<String, dynamic> map) {
    return Review(
      id: map['id'],
      username: map['username'],
      content: map['content'],
      rating: map['rating'],
      date: map['date'],
      approved: map['approved'] == 1,
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'username': username,
      'content': content,
      'rating': rating,
      'date': date,
      'approved': approved ? 1 : 0,
    };
  }
}
