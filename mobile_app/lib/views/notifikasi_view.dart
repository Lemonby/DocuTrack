import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/notifikasi_provider.dart';
import '../theme/app_theme.dart';
import '../models/notifikasi.dart';
import 'package:timeago/timeago.dart' as timeago;

class NotifikasiView extends StatefulWidget {
  const NotifikasiView({super.key});

  @override
  State<NotifikasiView> createState() => _NotifikasiViewState();
}

class _NotifikasiViewState extends State<NotifikasiView> {
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<NotifikasiProvider>(context, listen: false).fetchList(isRefresh: true);
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      final provider = Provider.of<NotifikasiProvider>(context, listen: false);
      if (!provider.isLoading && provider.currentPage < provider.lastPage) {
        provider.fetchList(page: provider.currentPage + 1);
      }
    }
  }

  Future<void> _refresh() async {
    await Provider.of<NotifikasiProvider>(context, listen: false).fetchList(isRefresh: true);
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<NotifikasiProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifikasi', style: TextStyle(fontWeight: FontWeight.bold)),
        actions: [
          if (provider.unreadCount > 0)
            TextButton(
              onPressed: () => provider.markAllAsRead(),
              child: const Text('Tandai Dibaca', style: TextStyle(color: AppTheme.primaryBlue)),
            ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _refresh,
        color: AppTheme.primaryBlue,
        child: provider.errorMessage.isNotEmpty && provider.items.isEmpty
            ? _buildErrorView(provider.errorMessage)
            : _buildList(provider),
      ),
    );
  }

  Widget _buildErrorView(String message) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.error_outline, size: 64, color: Colors.redAccent),
          const SizedBox(height: 16),
          Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppTheme.textMuted)),
          const SizedBox(height: 24),
          ElevatedButton(onPressed: _refresh, child: const Text('Coba Lagi')),
        ],
      ),
    );
  }

  Widget _buildList(NotifikasiProvider provider) {
    if (provider.isLoading && provider.items.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    if (provider.items.isEmpty) {
      return const Center(
        child: Text('Tidak ada notifikasi saat ini.'),
      );
    }

    return ListView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.all(8.0),
      itemCount: provider.items.length + (provider.currentPage < provider.lastPage ? 1 : 0),
      itemBuilder: (context, index) {
        if (index == provider.items.length) {
          return const Padding(
            padding: EdgeInsets.all(16.0),
            child: Center(child: CircularProgressIndicator()),
          );
        }

        final Notifikasi item = provider.items[index];
        final isUnread = !item.isRead;
        final timeStr = item.createdAt != null ? timeago.format(item.createdAt!, locale: 'id') : '';

        return Card(
          elevation: 0,
          color: isUnread ? AppTheme.primaryBlue.withOpacity(0.05) : Colors.white,
          margin: const EdgeInsets.only(bottom: 8),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
            side: BorderSide(color: isUnread ? AppTheme.primaryBlue.withOpacity(0.3) : AppTheme.borderLight),
          ),
          child: InkWell(
            onTap: () {
              if (isUnread) provider.markAsRead(item.id);
            },
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(
                    item.tipe == 'ERROR' ? Icons.error : (item.tipe == 'SUCCESS' ? Icons.check_circle : Icons.notifications),
                    color: isUnread ? AppTheme.primaryBlue : AppTheme.textMuted,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          item.pesan,
                          style: TextStyle(
                            fontWeight: isUnread ? FontWeight.bold : FontWeight.normal,
                            color: AppTheme.textDark,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(timeStr, style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                      ],
                    ),
                  ),
                  if (isUnread)
                    Container(
                      width: 8,
                      height: 8,
                      decoration: const BoxDecoration(color: Colors.redAccent, shape: BoxShape.circle),
                    )
                ],
              ),
            ),
          ),
        );
      },
    );
  }
}
