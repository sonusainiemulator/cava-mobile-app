import 'package:flutter/material.dart';
import '../db/app_database.dart';
import '../models/chat.dart';
import 'chat_page.dart';
import '../services/localization_service.dart';

class ChatListPage extends StatefulWidget {
  const ChatListPage({super.key});

  @override
  State<ChatListPage> createState() => _ChatListPageState();
}

class _ChatListPageState extends State<ChatListPage> {
  List<Chat> _chats = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadChats();
  }

  Future<void> _loadChats() async {
    setState(() => _loading = true);
    final data = await AppDatabase.instance.getChats();
    _chats = data.map((e) => Chat.fromMap(e)).toList();
    setState(() => _loading = false);
  }

  Future<void> _newChat() async {
    final nameCtrl = TextEditingController();
    await showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('New Chat'),
        content: TextField(
          controller: nameCtrl,
          decoration: const InputDecoration(labelText: 'Enter username/email'),
        ),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () async {
              if (nameCtrl.text.isEmpty) return;
              await AppDatabase.instance.createChat(nameCtrl.text);
              if (ctx.mounted) Navigator.pop(ctx);
              _loadChats();
            },
            child: const Text('Start Chat'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(LocalizationService.tr('chats_title'))),
      floatingActionButton: FloatingActionButton(
        onPressed: _newChat,
        tooltip: LocalizationService.tr('chats_new'),
        child: const Icon(Icons.chat_bubble),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _chats.isEmpty
              ? Center(child: Text(LocalizationService.tr('chats_empty')))
              : ListView.builder(
                  itemCount: _chats.length,
                  itemBuilder: (context, index) {
                    final chat = _chats[index];
                    return ListTile(
                      leading: CircleAvatar(
                          child: Text(chat.counterpart[0].toUpperCase())),
                      title: Text(chat.counterpart),
                      subtitle: Text(
                        chat.lastMessage.isEmpty
                            ? 'Start a conversation'
                            : chat.lastMessage,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      trailing: Text(
                        chat.lastMessageTime.isEmpty
                            ? ''
                            : chat.lastMessageTime.split('T')[0],
                        style:
                            const TextStyle(fontSize: 10, color: Colors.grey),
                      ),
                      onTap: () async {
                        await Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => ChatPage(chat: chat),
                          ),
                        );
                        _loadChats();
                      },
                    );
                  },
                ),
    );
  }
}
