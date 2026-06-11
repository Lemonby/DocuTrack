import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import '../../providers/usulan_provider.dart';
import '../../models/kegiatan.dart';
import 'usulan_form_view.dart';
import 'usulan_detail_view.dart';

class UsulanListView extends StatefulWidget {
  const UsulanListView({super.key});

  @override
  State<UsulanListView> createState() => _UsulanListViewState();
}

class _UsulanListViewState extends State<UsulanListView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<UsulanProvider>().fetchUsulans(isRefresh: true);
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
            const Text('Pengajuan KAK', style: TextStyle(color: AppTheme.textDark, fontWeight: FontWeight.w900, fontSize: 18)),
            Text('Manajemen Usulan Kegiatan', style: TextStyle(color: Colors.blue.shade700, fontSize: 11, fontWeight: FontWeight.w600)),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.search_rounded, color: AppTheme.textDark),
            onPressed: () {},
          )
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          await Navigator.push(context, MaterialPageRoute(builder: (_) => const UsulanFormView()));
          if (mounted) context.read<UsulanProvider>().fetchUsulans(isRefresh: true);
        },
        backgroundColor: Colors.blue.shade600,
        elevation: 4,
        icon: const Icon(Icons.add_rounded, color: Colors.white),
        label: const Text('Buat Usulan', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
      ),
      body: Consumer<UsulanProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading && provider.usulans.isEmpty) {
            return const Center(child: CircularProgressIndicator());
          }

          return RefreshIndicator(
            onRefresh: () => provider.fetchUsulans(isRefresh: true),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(colors: [Colors.blue.shade800, Colors.blue.shade500], begin: Alignment.topLeft, end: Alignment.bottomRight),
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [BoxShadow(color: Colors.blue.shade200, blurRadius: 15, offset: const Offset(0, 8))]
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.file_copy_rounded, color: Colors.white, size: 48),
                        SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Daftar Usulan KAK', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 16, letterSpacing: 0.5)),
                              SizedBox(height: 4),
                              Text('Pantau status persetujuan dari usulan Kerangka Acuan Kerja Anda.', style: TextStyle(color: Colors.white70, fontSize: 12, height: 1.3)),
                            ],
                          ),
                        )
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  
                  if (provider.usulans.isEmpty && !provider.isLoading)
                    const Padding(
                      padding: EdgeInsets.only(top: 40),
                      child: Center(child: Text('Belum ada usulan KAK', style: TextStyle(color: AppTheme.textMuted))),
                    )
                  else
                    ...provider.usulans.map((usulan) => _buildUsulanCard(context, usulan)),

                  if (provider.isLoading && provider.usulans.isNotEmpty)
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 20),
                      child: Center(child: CircularProgressIndicator()),
                    ),
                    
                  const SizedBox(height: 80), // Padding for FAB
                ],
              ),
            ),
          );
        }
      ),
    );
  }

  Widget _buildUsulanCard(BuildContext context, Kegiatan kegiatan) {
    final status = kegiatan.statusNama ?? 'Proses';
    Color statusColor = Colors.blueGrey;
    String btnText = 'Lihat Progress';
    IconData btnIcon = Icons.hourglass_top_rounded;

    if (status.toLowerCase().contains('revisi')) {
      statusColor = Colors.orange;
      btnText = 'Perbaiki Usulan';
      btnIcon = Icons.warning_rounded;
    } else if (status.toLowerCase().contains('disetujui')) {
      statusColor = Colors.green;
      btnText = 'Lihat Detail';
      btnIcon = Icons.check_circle_rounded;
    } else if (status.toLowerCase().contains('ditolak')) {
      statusColor = Colors.red;
      btnText = 'Lihat Alasan';
      btnIcon = Icons.cancel_rounded;
    } else if (status.toLowerCase().contains('menunggu')) {
      statusColor = Colors.blue;
    }

    final isActionable = (status.toLowerCase().contains('revisi') || status.toLowerCase().contains('ditolak'));
    
    // Formatting date safely
    String dateStr = '-';
    if (kegiatan.createdAt != null) {
      try {
        final dt = DateTime.parse(kegiatan.createdAt!);
        dateStr = DateFormat('dd MMM yyyy', 'id_ID').format(dt);
      } catch (_) {
        dateStr = kegiatan.createdAt!.split('T')[0];
      }
    }

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
                      child: Text('#${kegiatan.id}', style: const TextStyle(color: Colors.black87, fontSize: 11, fontWeight: FontWeight.w900)),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: statusColor.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: statusColor.withOpacity(0.2)),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(btnIcon, size: 12, color: statusColor),
                          const SizedBox(width: 6),
                          Text(status.toUpperCase(), style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Text(kegiatan.namaKegiatan, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textDark, height: 1.2)),
                const SizedBox(height: 8),
                Text('${kegiatan.pemilikKegiatan ?? 'User'} • ${kegiatan.prodiPenyelenggara ?? '-'}', style: const TextStyle(color: AppTheme.textMuted, fontSize: 12, fontWeight: FontWeight.w500)),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Icon(Icons.calendar_month_rounded, size: 14, color: Colors.grey.shade400),
                    const SizedBox(width: 6),
                    Text(dateStr, style: const TextStyle(fontSize: 12, color: AppTheme.textMuted, fontWeight: FontWeight.bold)),
                  ],
                ),
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
                await Navigator.push(context, MaterialPageRoute(builder: (_) => UsulanDetailView(kegiatanId: kegiatan.id)));
                if (!mounted) return;
                context.read<UsulanProvider>().fetchUsulans(isRefresh: true);
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
