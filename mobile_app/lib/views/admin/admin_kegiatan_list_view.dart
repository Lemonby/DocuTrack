import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import '../../models/kegiatan.dart';
import '../../providers/usulan_provider.dart';
import 'admin_kegiatan_detail_view.dart';

class AdminKegiatanListView extends StatefulWidget {
  const AdminKegiatanListView({super.key});

  @override
  State<AdminKegiatanListView> createState() => _AdminKegiatanListViewState();
}

class _AdminKegiatanListViewState extends State<AdminKegiatanListView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<UsulanProvider>().fetchKegiatans(isRefresh: true);
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
            const Text('Pengajuan Kegiatan', style: TextStyle(color: AppTheme.textDark, fontWeight: FontWeight.bold, fontSize: 18)),
            Text('Akses: Admin TI', style: TextStyle(color: Colors.blue.shade700, fontSize: 12, fontWeight: FontWeight.w600)),
          ],
        ),
      ),
      body: Consumer<UsulanProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading && provider.kegiatans.isEmpty) {
            return const Center(child: CircularProgressIndicator());
          }

          return RefreshIndicator(
            onRefresh: () => provider.fetchKegiatans(isRefresh: true),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(16),
                    margin: const EdgeInsets.only(bottom: 16),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(colors: [Colors.blue.shade700, Colors.blue.shade500]),
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(color: Colors.blue.shade200, blurRadius: 12, offset: const Offset(0, 6))
                      ]
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.assignment, color: Colors.white, size: 40),
                        SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Daftar Kegiatan', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)),
                              Text('Lengkapi dan kelola pengajuan kegiatan dari program studi Anda.', style: TextStyle(color: Colors.white70, fontSize: 12)),
                            ],
                          ),
                        )
                      ],
                    ),
                  ),
                  
                  if (provider.kegiatans.isEmpty && !provider.isLoading)
                    const Padding(
                      padding: EdgeInsets.only(top: 40),
                      child: Center(child: Text('Belum ada kegiatan', style: TextStyle(color: AppTheme.textMuted))),
                    )
                  else
                    ...provider.kegiatans.map((keg) => _buildKegiatanCard(context, keg)),

                  if (provider.isLoading && provider.kegiatans.isNotEmpty)
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

  Widget _buildKegiatanCard(BuildContext context, Kegiatan kegiatan) {
    final status = kegiatan.statusNama ?? 'Proses';
    Color statusColor = Colors.blueGrey;
    IconData icon = Icons.hourglass_empty;

    if (status.toLowerCase().contains('review') || status.toLowerCase().contains('menunggu')) {
      statusColor = Colors.blue;
      icon = Icons.search;
    } else if (status.toLowerCase().contains('revisi')) {
      statusColor = Colors.orange;
      icon = Icons.warning_amber_rounded;
    } else if (status.toLowerCase().contains('disetujui') || status.toLowerCase().contains('diberikan')) {
      statusColor = Colors.green;
      icon = Icons.check_circle_outline;
    } else if (status.toLowerCase().contains('ditolak')) {
      statusColor = Colors.red;
      icon = Icons.cancel_outlined;
    }

    final isEditable = (status.toLowerCase().contains('proses') || status.toLowerCase().contains('revisi'));
    
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
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(color: statusColor.withOpacity(0.08), blurRadius: 10, offset: const Offset(0, 4))
        ],
        border: Border.all(color: statusColor.withOpacity(0.2), width: 1.5),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.05),
              borderRadius: const BorderRadius.vertical(top: Radius.circular(15)),
              border: Border(bottom: BorderSide(color: statusColor.withOpacity(0.1))),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: statusColor,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text('#${kegiatan.id}', style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
                    ),
                    const SizedBox(width: 8),
                    Text(dateStr, style: TextStyle(fontSize: 12, color: statusColor, fontWeight: FontWeight.w600)),
                  ],
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: statusColor.withOpacity(0.5)),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(icon, size: 12, color: statusColor),
                      const SizedBox(width: 4),
                      Text(
                        status.toUpperCase(),
                        style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(kegiatan.namaKegiatan, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.textDark)),
                const SizedBox(height: 6),
                Row(
                  children: [
                    const Icon(Icons.person, size: 14, color: AppTheme.textMuted),
                    const SizedBox(width: 6),
                    Expanded(child: Text('${kegiatan.pemilikKegiatan ?? '-'} • ${kegiatan.prodiPenyelenggara ?? '-'}', style: const TextStyle(color: AppTheme.textMuted, fontSize: 13))),
                  ],
                ),
                const SizedBox(height: 16),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: () async {
                      await Navigator.push(context, MaterialPageRoute(builder: (_) => AdminKegiatanDetailView(kegiatan: kegiatan)));
                      if (!mounted) return;
                      context.read<UsulanProvider>().fetchKegiatans(isRefresh: true);
                    },
                    icon: Icon(isEditable ? Icons.edit : Icons.visibility, size: 16),
                    label: Text(isEditable ? 'Lengkapi Data' : 'Lihat Detail'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: isEditable ? AppTheme.primaryBlue : Colors.white,
                      foregroundColor: isEditable ? Colors.white : AppTheme.textDark,
                      elevation: isEditable ? 2 : 0,
                      side: isEditable ? BorderSide.none : const BorderSide(color: AppTheme.borderLight),
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                )
              ],
            ),
          ),
        ],
      ),
    );
  }
}
