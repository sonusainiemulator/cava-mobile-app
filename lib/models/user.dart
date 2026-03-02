class User {
  final int? id;
  final String username;
  final String? email;
  final String? avatarUrl;
  final int points;

  User({
    this.id,
    required this.username,
    this.email,
    this.avatarUrl,
    this.points = 0,
  });

  factory User.fromMap(Map<String, dynamic> map) {
    return User(
      id: map['id'],
      username: map['username'] ?? '',
      email: map['email'],
      avatarUrl: map['avatar_url'],
      points: map['points'] ?? 0,
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'username': username,
      'email': email,
      'avatar_url': avatarUrl,
      'points': points,
    };
  }
}
