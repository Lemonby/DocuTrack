import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/bendahara_provider.dart';
import '../../theme/app_theme.dart';
import '../../models/lpj.dart';
import 'bendahara_lpj_detail_view.dart';

class LpjTab extends StatefulWidget {
  const LpjTab({super.key});

  @override
  State<LpjTab> createState() => _LpjTabState();
}

class _LpjTabState extends State<LpjTab> {
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      final provider = Provider.of<BendaharaProvider>(context, listen: false);
      if (!provider.isLoadingLpj && provider.lpjPage < provider.lpjLastPage) {
        provider.fetchLpjList(page: provider.lpjPage + 1);
      }
    }
  }

  Future<void> _refresh() async {
    await Provider.of<BendaharaProvider>(context, listen: false).fetchLpjList(isRefresh: true);
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<BendaharaProvider>(context);

    return RefreshIndicator(
      onRefresh: _refresh,
      color: AppTheme.primaryBlue,
      child: provider.errorMessageLpj.isNotEmpty && provider.lpjItems.isEmpty
          ? _buildErrorView(provider.errorMessageLpj)
          : _buildList(provider),
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

  Widget _buildList(BendaharaProvider provider) {
    if (provider.isLoadingLpj && provider.lpjItems.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    final List<Lpj> items = provider.lpjItems;

    if (items.isEmpty && !provider.isLoadingLpj) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.receipt_long, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Belum ada LPJ untuk diverifikasi', style: TextStyle(color: AppTheme.textMuted)),
          ],
        ),
      );
    }

    return ListView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.all(16.0),
      itemCount: items.length + (provider.lpjPage < provider.lpjLastPage ? 1 : 0),
      itemBuilder: (context, index) {
        if (index == items.length) {
          return const Padding(
            padding: EdgeInsets.all(16.0),
            child: Center(child: CircularProgressIndicator()),
          );
        }

        final Lpj item = items[index];
        final String s = item.statusNama?.toLowerCase() ?? 'menunggu verifikasi';
        MaterialColor badgeColor = Colors.blue;
        if (s == 'disetujui' || s == 'selesai') badgeColor = Colors.teal;
        else if (s == 'revisi') badgeColor = Colors.orange;

        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppTheme.borderLight),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.02),
                blurRadius: 8,
                offset: const Offset(0, 4),
              )
            ],
          ),
          child: Material(
            color: Colors.transparent,
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
              onTap: () async {
                await Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => BendaharaLpjDetailView(id: item.id, status: item.statusNama ?? 'Menunggu Verifikasi'),
                  ),
                );
                _refresh();
              },
              child: Padding(
                padding: const EdgeInsets.all(16.0),
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
                            (item.statusNama ?? 'Menunggu Verifikasi').toUpperCase(),
                            style: TextStyle(
                              color: badgeColor,
                              fontSize: 10,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 1,
                            ),
                          ),
                        ),
                        Text(
                          item.submittedAt != null ? item.submittedAt!.split('T')[0] : '-',
                          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Text(
                      item.kegiatan?.namaKegiatan ?? 'Tanpa Judul',
                      style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppTheme.textDark, height: 1.3),
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Icon(Icons.person_outline, size: 14, color: Colors.grey.shade500),
                        const SizedBox(width: 4),
                        Text(item.kegiatan?.pemilikKegiatan ?? '-', style: TextStyle(fontSize: 12, color: Colors.grey.shade700)),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(Icons.apartment_rounded, size: 14, color: Colors.grey.shade500),
                        const SizedBox(width: 4),
                        Text(item.kegiatan?.jurusanPenyelenggara ?? '-', style: TextStyle(fontSize: 12, color: Colors.grey.shade700)),
                      ],
                    ),
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 12),
                      child: Divider(height: 1),
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.receipt_long_rounded, size: 16, color: Colors.blueGrey),
                            const SizedBox(width: 6),
                            const Text('Dokumen LPJ', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.blueGrey, fontSize: 13)),
                          ],
                        ),
                        Row(
                          children: [
                            Text(
                              'DETAIL',
                              style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: AppTheme.primaryBlue),
                            ),
                            const SizedBox(width: 4),
                            const Icon(Icons.arrow_forward_rounded, size: 14, color: AppTheme.primaryBlue),
                          ],
                        )
                      ],
                    ),
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
