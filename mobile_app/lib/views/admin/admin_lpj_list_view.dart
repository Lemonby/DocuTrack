import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import '../../models/lpj.dart';
import '../../providers/usulan_provider.dart';
import 'admin_lpj_detail_view.dart';

class AdminLpjListView extends StatefulWidget {
  const AdminLpjListView({super.key});

  @override
  State<AdminLpjListView> createState() => _AdminLpjListViewState();
}

class _AdminLpjListViewState extends State<AdminLpjListView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<UsulanProvider>().fetchLpjs(isRefresh: true);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Antrian LPJ', style: TextStyle(color: AppTheme.textDark, fontWeight: FontWeight.bold, fontSize: 18)),
            Text('Akses: Admin TI', style: TextStyle(color: Colors.indigo.shade700, fontSize: 12, fontWeight: FontWeight.w600)),
          ],
        ),
      ),
      body: Consumer<UsulanProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading && provider.lpjs.isEmpty) {
            return const Center(child: CircularProgressIndicator());
          }

          return RefreshIndicator(
            onRefresh: () => provider.fetchLpjs(isRefresh: true),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(20),
                    margin: const EdgeInsets.only(bottom: 20),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(colors: [Colors.indigo.shade800, Colors.indigo.shade500], begin: Alignment.topLeft, end: Alignment.bottomRight),
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [BoxShadow(color: Colors.indigo.shade200, blurRadius: 15, offset: const Offset(0, 8))]
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.receipt_long_rounded, color: Colors.white, size: 48),
                        SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Laporan Pertanggungjawaban', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 16, letterSpacing: 0.5)),
                              SizedBox(height: 4),
                              Text('Unggah bukti realisasi dan kelola pengajuan LPJ.', style: TextStyle(color: Colors.white70, fontSize: 12, height: 1.3)),
                            ],
                          ),
                        )
                      ],
                    ),
                  ),
                  
                  if (provider.lpjs.isEmpty && !provider.isLoading)
                    const Padding(
                      padding: EdgeInsets.only(top: 40),
                      child: Center(child: Text('Belum ada data LPJ', style: TextStyle(color: AppTheme.textMuted))),
                    )
                  else
                    ...provider.lpjs.map((lpj) => _buildLpjCard(context, lpj)),

                  if (provider.isLoading && provider.lpjs.isNotEmpty)
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 20),
                      child: Center(child: CircularProgressIndicator()),
                    ),
                ],
              ),
            ),
          );
        }
      ),
    );
  }

  Widget _buildLpjCard(BuildContext context, Lpj lpj) {
    final status = lpj.statusNama ?? 'Menunggu';
    Color statusColor = Colors.blue;
    IconData btnIcon = Icons.visibility;
    String btnText = 'Lihat Detail';

    if (status.toLowerCase().contains('upload') || status.toLowerCase().contains('proses')) {
      statusColor = Colors.orange;
      btnIcon = Icons.upload_file;
      btnText = 'Upload Bukti';
    } else if (status.toLowerCase().contains('revisi')) {
      statusColor = Colors.amber.shade700;
      btnIcon = Icons.warning_rounded;
      btnText = 'Revisi LPJ';
    } else if (status.toLowerCase().contains('disetujui') || status.toLowerCase().contains('selesai')) {
      statusColor = Colors.green;
      btnIcon = Icons.check_circle_rounded;
      btnText = 'Lihat Laporan';
    }

    final isActionable = (status.toLowerCase().contains('upload') || status.toLowerCase().contains('revisi') || status.toLowerCase().contains('draft'));

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: statusColor.withOpacity(0.08), blurRadius: 15, offset: const Offset(0, 5))],
        border: Border(left: BorderSide(color: statusColor, width: 6)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(8)),
                      child: Text('#${lpj.id}', style: const TextStyle(color: Colors.black87, fontSize: 11, fontWeight: FontWeight.w900)),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: statusColor.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: statusColor.withOpacity(0.2)),
                      ),
                      child: Text(status.toUpperCase(), style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Text(lpj.kegiatan?.namaKegiatan ?? 'Tanpa Nama', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textDark, height: 1.2)),
                const SizedBox(height: 8),
                Text('${lpj.kegiatan?.pemilikKegiatan ?? '-'} • ${lpj.kegiatan?.prodiPenyelenggara ?? '-'}', style: const TextStyle(color: AppTheme.textMuted, fontSize: 12, fontWeight: FontWeight.w500)),
              ],
            ),
          ),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            decoration: BoxDecoration(
              color: isActionable ? statusColor.withOpacity(0.05) : Colors.grey.shade50,
              borderRadius: const BorderRadius.vertical(bottom: Radius.circular(20)),
              border: Border(top: BorderSide(color: Colors.grey.shade100)),
            ),
            child: ElevatedButton.icon(
              onPressed: () async {
                await Navigator.push(context, MaterialPageRoute(builder: (_) => AdminLpjDetailView(lpjId: lpj.id)));
                if (!mounted) return;
                context.read<UsulanProvider>().fetchLpjs(isRefresh: true);
              },
              icon: Icon(btnIcon, size: 16),
              label: Text(btnText, style: const TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1)),
              style: ElevatedButton.styleFrom(
                backgroundColor: isActionable ? statusColor : Colors.white,
                foregroundColor: isActionable ? Colors.white : AppTheme.textDark,
                elevation: isActionable ? 4 : 0,
                shadowColor: statusColor.withOpacity(0.5),
                side: isActionable ? BorderSide.none : BorderSide(color: Colors.grey.shade300),
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
          )
        ],
      ),
    );
  }
}
