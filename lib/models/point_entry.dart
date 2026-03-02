class PointEntry {
  final int id;
  final int points;
  final String reason;
  final DateTime timestamp;

  PointEntry({
    required this.id,
    required this.points,
    required this.reason,
    required this.timestamp,
  });

  factory PointEntry.fromMap(Map<String, dynamic> map) {
    return PointEntry(
      id: map['id'],
      points: map['points'],
      reason: map['reason'],
      timestamp: DateTime.parse(map['timestamp']),
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'points': points,
      'reason': reason,
      'timestamp': timestamp.toIso8601String(),
    };
  }
}
