import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/bendahara_provider.dart';
import '../../theme/app_theme.dart';
import '../../models/kegiatan.dart';
import 'bendahara_pencairan_detail_view.dart';

class PencairanTab extends StatefulWidget {
  const PencairanTab({super.key});

  @override
  State<PencairanTab> createState() => _PencairanTabState();
}

class _PencairanTabState extends State<PencairanTab> {
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
      if (!provider.isLoadingPencairan && provider.pencairanPage < provider.pencairanLastPage) {
        provider.fetchPencairanList(page: provider.pencairanPage + 1);
      }
    }
  }

  Future<void> _refresh() async {
    await Provider.of<BendaharaProvider>(context, listen: false).fetchPencairanList(isRefresh: true);
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<BendaharaProvider>(context);

    return RefreshIndicator(
      onRefresh: _refresh,
      color: AppTheme.primaryBlue,
      child: provider.errorMessagePencairan.isNotEmpty && provider.pencairanItems.isEmpty
          ? _buildErrorView(provider.errorMessagePencairan)
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
    if (provider.isLoadingPencairan && provider.pencairanItems.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    final List<Kegiatan> items = provider.pencairanItems;

    if (items.isEmpty && !provider.isLoadingPencairan) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.inbox, size: 64, color: Colors.grey),
            SizedBox(height: 16),
            Text('Belum ada kegiatan untuk dicairkan', style: TextStyle(color: AppTheme.textMuted)),
          ],
        ),
      );
    }

    return ListView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.all(16.0),
      itemCount: items.length + (provider.pencairanPage < provider.pencairanLastPage ? 1 : 0),
      itemBuilder: (context, index) {
        if (index == items.length) {
          return const Padding(
            padding: EdgeInsets.all(16.0),
            child: Center(child: CircularProgressIndicator()),
          );
        }

        final Kegiatan item = items[index];
        final bool isLunas = item.statusNama?.toLowerCase() == 'dana diberikan' || item.statusNama?.toLowerCase() == 'sudah dicairkan';
        final MaterialColor badgeColor = isLunas ? Colors.teal : Colors.blue;
        
        // Coba hitung total dana dari RAW RAB jika ada
        String totalDana = 'Lihat Detail';
        if (item.rawData != null && item.rawData!['rab'] != null) {
            double total = 0;
            for(var r in item.rawData!['rab']){
               double hrg = r['harga_satuan'] != null ? double.parse(r['harga_satuan'].toString()) : 0;
               double v1 = r['volume_1'] != null ? double.parse(r['volume_1'].toString()) : 1;
               double v2 = r['volume_2'] != null ? double.parse(r['volume_2'].toString()) : 1;
               total += (hrg * v1 * v2);
            }
            if(total > 0) {
              totalDana = 'Rp ${total.toStringAsFixed(0).replaceAll(RegExp(r'\B(?=(\d{3})+(?!\d))'), '.')}';
            }
        } else if (item.rawData != null && item.rawData!['dana_disetujui'] != null) {
            double danaDisetujui = double.tryParse(item.rawData!['dana_disetujui'].toString()) ?? 0;
            if (danaDisetujui > 0) {
                totalDana = 'Rp ${danaDisetujui.toStringAsFixed(0).replaceAll(RegExp(r'\B(?=(\d{3})+(?!\d))'), '.')}';
            }
        }

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
                    builder: (context) => BendaharaPencairanDetailView(id: item.id, status: item.statusNama ?? 'Belum Dicairkan'),
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
                            (item.statusNama ?? 'Belum Dicairkan').toUpperCase(),
                            style: TextStyle(
                              color: badgeColor,
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
                    const SizedBox(height: 12),
                    Text(
                      item.namaKegiatan,
                      style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppTheme.textDark, height: 1.3),
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Icon(Icons.person_outline, size: 14, color: Colors.grey.shade500),
                        const SizedBox(width: 4),
                        Text(item.pemilikKegiatan ?? '-', style: TextStyle(fontSize: 12, color: Colors.grey.shade700)),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(Icons.apartment_rounded, size: 14, color: Colors.grey.shade500),
                        const SizedBox(width: 4),
                        Text(item.jurusanPenyelenggara ?? '-', style: TextStyle(fontSize: 12, color: Colors.grey.shade700)),
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
                            const Icon(Icons.account_balance_wallet_rounded, size: 16, color: Colors.blueGrey),
                            const SizedBox(width: 6),
                            Text(totalDana, style: const TextStyle(fontWeight: FontWeight.w900, color: Colors.blueGrey, fontSize: 13)),
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
