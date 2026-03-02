class Chat {
  final int? id;
  final String counterpart;
  final String lastMessage;
  final String lastMessageTime;

  Chat({this.id, required this.counterpart, required this.lastMessage, required this.lastMessageTime});

  factory Chat.fromMap(Map<String, dynamic> map) {
    return Chat(
      id: map['id'],
      counterpart: map['counterpart'],
      lastMessage: map['last_message'],
      lastMessageTime: map['last_message_time'],
    );
  }
}

class ChatMessage {
  final int? id;
  final int chatId;
  final String sender;
  final String content;
  final String timestamp;
  final bool isImage;

  ChatMessage({this.id, required this.chatId, required this.sender, required this.content, required this.timestamp, this.isImage = false});

  factory ChatMessage.fromMap(Map<String, dynamic> map) {
    return ChatMessage(
      id: map['id'],
      chatId: map['chat_id'],
      sender: map['sender'],
      content: map['content'],
      timestamp: map['timestamp'],
      isImage: map['is_image'] == 1,
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'chat_id': chatId,
      'sender': sender,
      'content': content,
      'timestamp': timestamp,
      'is_image': isImage ? 1 : 0,
    };
  }
}
