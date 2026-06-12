import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/telaah_provider.dart';
import '../../providers/auth_provider.dart';
import '../../theme/app_theme.dart';
import 'verifikator_detail_view.dart';
import 'ppk_detail_view.dart';
import 'wadir_detail_view.dart';

class TelaahListView extends StatefulWidget {
  final bool isDummy; // flag if we want to show dummy data forcibly
  const TelaahListView({super.key, this.isDummy = false});

  @override
  State<TelaahListView> createState() => _TelaahListViewState();
}

class _TelaahListViewState extends State<TelaahListView> {
  final ScrollController _scrollController = ScrollController();
  String _rolePrefix = '';

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _determineRoleAndFetch();
    });
  }

  void _determineRoleAndFetch() {
    final user = Provider.of<AuthProvider>(context, listen: false).currentUser;
    if (user != null) {
      if (user.isPPK) _rolePrefix = 'ppk';
      else if (user.isVerifikator) _rolePrefix = 'verifikator';
      else if (user.isWadir) _rolePrefix = 'wadir';
      else _rolePrefix = '';

      if (_rolePrefix.isNotEmpty) {
        Provider.of<TelaahProvider>(context, listen: false).fetchTelaahList(_rolePrefix, page: 1, isRefresh: true);
      }
    }
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      final provider = Provider.of<TelaahProvider>(context, listen: false);
      if (!provider.isLoading && provider.currentPage < provider.lastPage) {
        provider.fetchTelaahList(_rolePrefix, page: provider.currentPage + 1);
      }
    }
  }

  Future<void> _refresh() async {
    await Provider.of<TelaahProvider>(context, listen: false).fetchTelaahList(_rolePrefix, page: 1, isRefresh: true);
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<TelaahProvider>(context);

    return Scaffold(
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
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 64, color: Colors.redAccent),
            const SizedBox(height: 16),
            const Text(
              'Gagal Memuat Data',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue),
            ),
            const SizedBox(height: 8),
            Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppTheme.textMuted)),
            const SizedBox(height: 24),
            ElevatedButton(onPressed: _refresh, child: const Text('Coba Lagi')),
          ],
        ),
      ),
    );
  }

  Widget _buildList(TelaahProvider provider) {
    if (provider.isLoading && provider.items.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    final items = provider.items;
    
    if (items.isEmpty && !provider.isLoading) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.inbox, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Belum ada dokumen untuk ditelaah', style: TextStyle(color: AppTheme.textMuted)),
          ],
        ),
      );
    }

    return ListView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.all(20.0),
      itemCount: items.length + (provider.isLoading ? 1 : 0),
      itemBuilder: (context, index) {
        if (index == items.length) {
          return const Padding(
            padding: EdgeInsets.symmetric(vertical: 20),
            child: Center(child: CircularProgressIndicator()),
          );
        }

        final item = items[index];
        final String statusText = item.status?.nama?.toUpperCase() ?? 'MENUNGGU';
        final bool isDone = statusText.contains('TELAH DITELAAH') || statusText.contains('SELESAI');
        final bool isRevisi = statusText.contains('REVISI');

        MaterialColor badgeColor;
        if (isDone) badgeColor = Colors.green;
        else if (isRevisi) badgeColor = Colors.orange;
        else badgeColor = Colors.blue;

        return Container(
          margin: const EdgeInsets.only(bottom: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: badgeColor.withOpacity(0.1),
                blurRadius: 15,
                offset: const Offset(0, 8),
              )
            ],
            border: Border(
              left: BorderSide(
                color: badgeColor.shade600,
                width: 6,
              ),
            ),
          ),
          child: Material(
            color: Colors.transparent,
            child: InkWell(
              borderRadius: BorderRadius.circular(20),
              onTap: () async {
                Widget detailView;
                if (_rolePrefix == 'ppk') {
                  detailView = PpkDetailView(kegiatanId: item.id);
                } else if (_rolePrefix == 'wadir') {
                  detailView = WadirDetailView(kegiatanId: item.id);
                } else {
                  detailView = VerifikatorDetailView(kegiatanId: item.id);
                }
                await Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => detailView,
                  ),
                );
                _refresh();
              },
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: badgeColor.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            statusText,
                            style: TextStyle(
                              color: badgeColor.shade700,
                              fontSize: 10,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 1,
                            ),
                          ),
                        ),
                        Text(
                          item.createdAt != null ? item.createdAt!.split('T')[0] : '-',
                          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    Text(
                      item.namaKegiatan,
                      style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark, height: 1.3),
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Icon(Icons.person_rounded, size: 14, color: Colors.grey.shade400),
                        const SizedBox(width: 4),
                        Text(
                          item.pemilikKegiatan ?? '-',
                          style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.grey.shade600),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Icon(Icons.apartment_rounded, size: 14, color: Colors.grey.shade400),
                        const SizedBox(width: 4),
                        Text(
                          item.jurusanPenyelenggara ?? '-',
                          style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.grey.shade600),
                        ),
                      ],
                    ),
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 16),
                      child: Divider(height: 1),
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.account_balance_wallet_rounded, size: 16, color: Colors.blueGrey),
                            const SizedBox(width: 6),
                            const Text('Lihat Rincian', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.blueGrey, fontSize: 13)),
                          ],
                        ),
                        Row(
                          children: [
                            Text(
                              'DETAIL KAK',
                              style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: badgeColor.shade600),
                            ),
                            const SizedBox(width: 4),
                            Icon(Icons.arrow_forward_rounded, size: 14, color: badgeColor.shade600),
                          ],
                        )
                      ],
                    )
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}
