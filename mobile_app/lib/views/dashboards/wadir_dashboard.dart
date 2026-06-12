import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/user.dart';
import '../../models/dashboard_data.dart';
import '../../models/kegiatan.dart';
import '../telaah/wadir_detail_view.dart';

class WadirDashboard extends StatelessWidget {
  final User user;
  final DashboardData? data;

  const WadirDashboard({super.key, required this.user, this.data});

  @override
  Widget build(BuildContext context) {
    final recentItems = data?.recentItems ?? [];

    return SingleChildScrollView(
      padding: const EdgeInsets.all(20.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Welcome Banner
          _buildWelcomeBanner(),
          const SizedBox(height: 24),

          // Statistics Grid
          _buildStatisticsGrid(context),
          const SizedBox(height: 24),

          // Daftar Usulan Section
          const Row(
            children: [
              Icon(Icons.list_alt, color: AppTheme.primaryBlue, size: 20),
              SizedBox(width: 8),
              Text(
                'Daftar Usulan (Semua Status)',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textDark),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // Filter Controls (UI Only)
          _buildFilterControls(),
          const SizedBox(height: 16),

          // List Items (Dinamis)
          if (recentItems.isEmpty)
             const Padding(
               padding: EdgeInsets.symmetric(vertical: 40),
               child: Center(
                 child: Column(
                   children: [
                     Icon(Icons.folder_open_outlined, size: 48, color: Colors.grey),
                     SizedBox(height: 12),
                     Text('Belum ada data usulan.', style: TextStyle(color: AppTheme.textMuted)),
                   ],
                 ),
               ),
             )
          else
            ...recentItems.asMap().entries.map((entry) {
              int index = entry.key;
              Kegiatan item = entry.value;
              String status = item.status?.nama ?? 'Menunggu';
              Color statusColor = status.toLowerCase() == 'disetujui' ? Colors.green : (status.toLowerCase() == 'ditolak' ? Colors.red : Colors.orange);
              IconData statusIcon = status.toLowerCase() == 'disetujui' ? Icons.check_circle : (status.toLowerCase() == 'ditolak' ? Icons.cancel : Icons.access_time);
              
              return _buildWadirListCard(
                no: '${index + 1}',
                title: item.namaKegiatan,
                prodi: item.jurusanPenyelenggara ?? '-',
                pengusul: item.pemilikKegiatan ?? '-',
                nim: item.nimPelaksana ?? '-',
                date: item.tanggalMulai ?? item.createdAt ?? '-',
                status: status,
                statusIcon: statusIcon,
                statusColor: statusColor,
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => WadirDetailView(
                        kegiatanId: item.id,
                      ),
                    ),
                  );
                },
              );
            }),
        ],
      ),
    );
  }

  Widget _buildWelcomeBanner() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: AppTheme.cardGradient,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: AppTheme.secondaryBlue.withOpacity(0.3),
            blurRadius: 15,
            offset: const Offset(0, 8),
          )
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Dashboard Wakil Direktur',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 22,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Halo, ${user.name}!',
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.85),
                    fontSize: 14,
                  ),
                ),
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: Colors.white24,
                    borderRadius: BorderRadius.circular(30),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.check_circle_outline, color: AppTheme.brightAqua, size: 16),
                      SizedBox(width: 4),
                      Text(
                        'Koneksi API Aktif',
                        style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: const BoxDecoration(
              color: Colors.white12,
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.account_balance_outlined, color: Colors.white, size: 48),
          ),
        ],
      ),
    );
  }

  Widget _buildStatisticsGrid(BuildContext context) {
    final stats = data?.stats;
    return LayoutBuilder(
      builder: (context, constraints) {
        return Wrap(
          spacing: 12,
          runSpacing: 12,
          children: [
            SizedBox(
              width: constraints.maxWidth > 600 ? (constraints.maxWidth - 64) / 3 : (constraints.maxWidth - 52) / 2,
              child: _buildStatCard(
                Icons.layers_outlined, 
                '${stats?.totalUsulan ?? 0}', 
                'Total Usulan', 
                const LinearGradient(colors: [Color(0xFF60A5FA), Color(0xFF3B82F6)], begin: Alignment.topLeft, end: Alignment.bottomRight),
              ),
            ),
            SizedBox(
              width: constraints.maxWidth > 600 ? (constraints.maxWidth - 64) / 3 : (constraints.maxWidth - 52) / 2,
              child: _buildStatCard(
                Icons.check_circle_outline, 
                '${stats?.disetujui ?? 0}', 
                'Disetujui', 
                const LinearGradient(colors: [Color(0xFF34D399), Color(0xFF10B981)], begin: Alignment.topLeft, end: Alignment.bottomRight),
              ),
            ),
            SizedBox(
              width: constraints.maxWidth > 600 ? (constraints.maxWidth - 64) / 3 : constraints.maxWidth - 40,
              child: _buildStatCard(
                Icons.hourglass_bottom_outlined, 
                '${stats?.menunggu ?? 0}', 
                'Menunggu Persetujuan', 
                const LinearGradient(colors: [Color(0xFFFDE047), Color(0xFFFACC15)], begin: Alignment.topLeft, end: Alignment.bottomRight),
                textColor: const Color(0xFF713F12),
              ),
            ),
          ],
        );
      },
    );
  }

  Widget _buildStatCard(IconData icon, String count, String label, LinearGradient gradient, {Color textColor = Colors.white}) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: gradient,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: gradient.colors.last.withOpacity(0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          )
        ]
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      count,
                      style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: textColor, height: 1.1),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      label,
                      style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: textColor.withOpacity(0.9)),
                    ),
                  ],
                ),
              ),
              Icon(icon, color: textColor.withOpacity(0.7), size: 32),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildFilterControls() {
    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: Container(
                height: 44,
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.borderLight),
                ),
                child: const Center(child: Text('Filter Status', style: TextStyle(fontSize: 13, color: AppTheme.textMuted))),
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: Container(
                height: 44,
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.borderLight),
                ),
                child: const Center(child: Text('Filter Jurusan', style: TextStyle(fontSize: 13, color: AppTheme.textMuted))),
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildWadirListCard({
    required String no,
    required String title,
    required String prodi,
    required String pengusul,
    required String nim,
    required String date,
    required String status,
    required IconData statusIcon,
    required Color statusColor,
    required VoidCallback onTap,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.borderLight),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 6, offset: const Offset(0, 2))
        ],
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              border: Border(bottom: BorderSide(color: AppTheme.borderLight)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(colors: [Color(0xFF3B82F6), Color(0xFF2563EB)]),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    'ID: $no',
                    style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold),
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: statusColor.withOpacity(0.2)),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(statusIcon, size: 12, color: statusColor),
                      const SizedBox(width: 4),
                      Text(
                        status.toUpperCase(),
                        style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                ),
                const SizedBox(height: 12),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.person, size: 14, color: AppTheme.textMuted),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        '$pengusul ($nim)',
                        style: const TextStyle(fontSize: 12, color: AppTheme.textDark),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(Icons.school, size: 14, color: AppTheme.textMuted),
                    const SizedBox(width: 6),
                    Text(prodi, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                  ],
                ),
                const SizedBox(height: 16),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: onTap,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primaryBlue,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                      elevation: 0,
                    ),
                    child: const Text('LIHAT DETAIL', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, letterSpacing: 1)),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
