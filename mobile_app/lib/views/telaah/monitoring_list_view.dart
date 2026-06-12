import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/monitoring_provider.dart';
import '../../providers/auth_provider.dart';
import '../../theme/app_theme.dart';
import '../../models/kegiatan.dart';
import 'verifikator_detail_view.dart';
import 'ppk_detail_view.dart';
import 'wadir_detail_view.dart';

class MonitoringListView extends StatefulWidget {
  final bool isRiwayat;
  
  const MonitoringListView({super.key, this.isRiwayat = true});

  @override
  State<MonitoringListView> createState() => _MonitoringListViewState();
}

class _MonitoringListViewState extends State<MonitoringListView> {
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
      else if (user.isDirektur) _rolePrefix = 'direktur';
      else if (user.isSuperAdmin) _rolePrefix = 'superadmin';
      else _rolePrefix = '';

      if (_rolePrefix.isNotEmpty) {
        Provider.of<MonitoringProvider>(context, listen: false).fetchList(_rolePrefix, page: 1, isRefresh: true, isRiwayat: widget.isRiwayat);
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
      final provider = Provider.of<MonitoringProvider>(context, listen: false);
      if (!provider.isLoading && provider.currentPage < provider.lastPage) {
        provider.fetchList(_rolePrefix, page: provider.currentPage + 1, isRiwayat: widget.isRiwayat);
      }
    }
  }

  Future<void> _refresh() async {
    await Provider.of<MonitoringProvider>(context, listen: false).fetchList(_rolePrefix, page: 1, isRefresh: true, isRiwayat: widget.isRiwayat);
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<MonitoringProvider>(context);

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
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

  Widget _buildList(MonitoringProvider provider) {
    if (provider.isLoading && provider.items.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    final items = provider.items.isEmpty ? _getDummyData() : provider.items;

    return ListView.builder(
      controller: _scrollController,
      padding: const EdgeInsets.all(20.0),
      itemCount: items.length,
      itemBuilder: (context, index) {
        final item = items[index];
        final String statusText = item.status?.nama?.toUpperCase() ?? (index % 2 == 0 ? 'TELAH DITELAAH' : 'PROSES TAHAP SELANJUTNYA');
        final bool isApproved = statusText.contains('SELESAI') || statusText.contains('TELAH DITELAAH');

        return Container(
          margin: const EdgeInsets.only(bottom: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: isApproved ? Colors.green.withOpacity(0.05) : Colors.orange.withOpacity(0.05),
                blurRadius: 15,
                offset: const Offset(0, 8),
              )
            ],
            border: Border(
              left: BorderSide(
                color: isApproved ? Colors.green.shade600 : Colors.orange.shade600,
                width: 6,
              ),
            ),
          ),
          child: Material(
            color: Colors.transparent,
            child: InkWell(
              borderRadius: BorderRadius.circular(20),
              onTap: () {
                Widget detailView;
                if (_rolePrefix == 'ppk') {
                  detailView = PpkDetailView(kegiatanId: item.id);
                } else if (_rolePrefix == 'wadir') {
                  detailView = WadirDetailView(kegiatanId: item.id);
                } else {
                  detailView = VerifikatorDetailView(kegiatanId: item.id);
                }
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => detailView,
                  ),
                );
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
                            color: isApproved ? Colors.green.shade50 : Colors.orange.shade50,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            statusText,
                            style: TextStyle(
                              color: isApproved ? Colors.green.shade700 : Colors.orange.shade700,
                              fontSize: 10,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 1,
                            ),
                          ),
                        ),
                        Text(
                          item.createdAt ?? '12 Nov 2026',
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
                        Icon(widget.isRiwayat ? Icons.history_rounded : Icons.remove_red_eye_rounded, size: 14, color: Colors.grey.shade400),
                        const SizedBox(width: 4),
                        Text(
                          widget.isRiwayat ? 'Riwayat Pemeriksaan' : 'Monitoring Tracking',
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
                          item.jurusanPenyelenggara ?? 'Teknik Informatika',
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
                            const Text('Rp 15.000.000', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.blueGrey, fontSize: 13)),
                          ],
                        ),
                        Row(
                          children: [
                            Text(
                              'DETAIL KAK',
                              style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: isApproved ? Colors.green.shade600 : Colors.orange.shade600),
                            ),
                            const SizedBox(width: 4),
                            Icon(Icons.arrow_forward_rounded, size: 14, color: isApproved ? Colors.green.shade600 : Colors.orange.shade600),
                          ],
                        )
                      ],
                    ),
                    if (!widget.isRiwayat) ...[
                      const SizedBox(height: 16),
                      _buildMonitoringStepper(item),
                    ],
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildMonitoringStepper(Kegiatan item) {
    // Dummy stages based on web docutrack
    final List<String> stages = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
    int currentStageIndex = 0;

    // Dummy logic to set progress based on item ID for demonstration
    if (item.id % 6 == 0) {
      currentStageIndex = 5; // LPJ
    } else if (item.id % 5 == 0) {
      currentStageIndex = 4; // Dana Cair
    } else if (item.id % 4 == 0) {
      currentStageIndex = 3; // ACC PPK
    } else if (item.id % 3 == 0) {
      currentStageIndex = 2; // ACC WD
    } else if (item.id % 2 == 0) {
      currentStageIndex = 1; // Verifikasi
    } else {
      currentStageIndex = 0; // Pengajuan
    }

    return Container(
      padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 8),
      decoration: BoxDecoration(
        color: Colors.blue.shade50.withOpacity(0.5),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.blue.shade100),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Progres Tahapan', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue)),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: List.generate(stages.length, (index) {
              final isCompleted = index <= currentStageIndex;
              final isActive = index == currentStageIndex;
              
              return Expanded(
                child: Column(
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Container(
                            height: 3,
                            color: index == 0 ? Colors.transparent : (isCompleted ? AppTheme.primaryBlue : Colors.grey.shade300),
                          ),
                        ),
                        Container(
                          width: 20,
                          height: 20,
                          decoration: BoxDecoration(
                            color: isCompleted ? AppTheme.primaryBlue : Colors.white,
                            shape: BoxShape.circle,
                            border: Border.all(
                              color: isCompleted ? AppTheme.primaryBlue : Colors.grey.shade300,
                              width: 2,
                            ),
                            boxShadow: isActive ? [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.4), blurRadius: 6, spreadRadius: 2)] : null,
                          ),
                          child: isCompleted
                              ? const Icon(Icons.check, size: 12, color: Colors.white)
                              : null,
                        ),
                        Expanded(
                          child: Container(
                            height: 3,
                            color: index == stages.length - 1 ? Colors.transparent : (index < currentStageIndex ? AppTheme.primaryBlue : Colors.grey.shade300),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Text(
                      stages[index],
                      style: TextStyle(
                        fontSize: 8, // Smaller font for 6 items
                        fontWeight: isActive ? FontWeight.bold : FontWeight.normal,
                        color: isActive ? AppTheme.primaryBlue : Colors.grey.shade600,
                      ),
                      textAlign: TextAlign.center,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              );
            }),
          ),
        ],
      ),
    );
  }

  List<Kegiatan> _getDummyData() {
    if (widget.isRiwayat) {
      if (_rolePrefix == 'wadir' || _rolePrefix == 'ppk') {
        return [
          Kegiatan(
            id: 9101,
            namaKegiatan: 'Pembelian Alat Lab Komputer Terapan',
            pemilikKegiatan: 'Budi Santoso',
            createdAt: '10 Nov 2026',
            jurusanPenyelenggara: 'Teknik Informatika dan Komputer',
            status: KegiatanStatus(id: 3, nama: 'Telah Ditelaah'),
          ),
          Kegiatan(
            id: 9103,
            namaKegiatan: 'Penyusunan Kurikulum Berbasis Industri',
            pemilikKegiatan: 'Ahmad Dahlan',
            createdAt: '15 Nov 2026',
            jurusanPenyelenggara: 'Akuntansi',
            status: KegiatanStatus(id: 4, nama: 'Ditolak'),
          ),
        ];
      }
      return [
        Kegiatan(
          id: 9101,
          namaKegiatan: 'Pembelian Alat Lab Komputer Terapan',
          pemilikKegiatan: 'Budi Santoso',
          createdAt: '10 Nov 2026',
          jurusanPenyelenggara: 'Teknik Informatika dan Komputer',
          status: KegiatanStatus(id: 3, nama: 'Telah Ditelaah'),
        ),
        Kegiatan(
          id: 9102,
          namaKegiatan: 'Pemeliharaan Jaringan Internet Kampus',
          pemilikKegiatan: 'Siti Aminah',
          createdAt: '12 Nov 2026',
          jurusanPenyelenggara: 'Teknik Elektro',
          status: KegiatanStatus(id: 2, nama: 'Menunggu Direvisi Admin TI'),
        ),
        Kegiatan(
          id: 9103,
          namaKegiatan: 'Penyusunan Kurikulum Berbasis Industri',
          pemilikKegiatan: 'Ahmad Dahlan',
          createdAt: '15 Nov 2026',
          jurusanPenyelenggara: 'Akuntansi',
          status: KegiatanStatus(id: 2, nama: 'Revisi'),
        ),
      ];
    } else {
      if (_rolePrefix == 'wadir' || _rolePrefix == 'ppk') {
        return [
          Kegiatan(
            id: 9201,
            namaKegiatan: 'Renovasi Gedung Kuliah Bersama',
            pemilikKegiatan: 'Ahmad Dahlan',
            createdAt: '01 Des 2026',
            jurusanPenyelenggara: 'Teknik Sipil',
            status: KegiatanStatus(id: 3, nama: 'Telah Ditelaah'),
          ),
        ];
      }
      return [
        Kegiatan(
          id: 9201,
          namaKegiatan: 'Renovasi Gedung Kuliah Bersama',
          pemilikKegiatan: 'Ahmad Dahlan',
          createdAt: '01 Des 2026',
          jurusanPenyelenggara: 'Teknik Sipil',
          status: KegiatanStatus(id: 3, nama: 'Telah Ditelaah'),
        ),
        Kegiatan(
          id: 9202,
          namaKegiatan: 'Sertifikasi ISO Manajemen Perguruan Tinggi',
          pemilikKegiatan: 'Retno Widiastuti',
          createdAt: '05 Des 2026',
          jurusanPenyelenggara: 'Administrasi Niaga',
          status: KegiatanStatus(id: 2, nama: 'Menunggu Direvisi Admin TI'),
        ),
      ];
    }
  }
}
