import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/user.dart';
import '../../models/dashboard_data.dart';
import '../bendahara/bendahara_pencairan_detail_view.dart';
import '../bendahara/bendahara_lpj_detail_view.dart';

class BendaharaDashboard extends StatelessWidget {
  final User user;
  final DashboardData? data;

  const BendaharaDashboard({super.key, required this.user, this.data});

  @override
  Widget build(BuildContext context) {
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

          // Antrian Pencairan
          const Row(
            children: [
              Icon(Icons.request_quote_rounded, color: AppTheme.primaryBlue, size: 20),
              SizedBox(width: 8),
              Text(
                'Antrian Pencairan Dana',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textDark),
              ),
            ],
          ),
          const SizedBox(height: 4),
          const Text(
            'Kegiatan yang menunggu pencairan dana',
            style: TextStyle(fontSize: 11, color: AppTheme.textMuted, fontStyle: FontStyle.italic),
          ),
          const SizedBox(height: 16),
          _buildApprovalCard(
            context: context,
            title: 'Pelatihan Digitalisasi Desa',
            prodi: 'Teknik Informatika',
            pengusul: 'Budi Santoso',
            date: '02 Jun 2026',
            status: 'Belum Dicairkan',
            statusColor: Colors.blue,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BendaharaPencairanDetailView(id: 101, status: 'Belum Dicairkan')))
          ),
          _buildApprovalCard(
            context: context,
            title: 'Workshop AI for Students',
            prodi: 'Sistem Informasi',
            pengusul: 'Andi Rahman',
            date: '15 Jun 2026',
            status: 'Sudah Dicairkan',
            statusColor: Colors.teal,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BendaharaPencairanDetailView(id: 102, status: 'Sudah Dicairkan')))
          ),
          
          const SizedBox(height: 24),

          // Antrian LPJ
          const Row(
            children: [
              Icon(Icons.receipt_long_rounded, color: AppTheme.primaryBlue, size: 20),
              SizedBox(width: 8),
              Text(
                'Antrian Verifikasi LPJ',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textDark),
              ),
            ],
          ),
          const SizedBox(height: 4),
          const Text(
            'Laporan Pertanggungjawaban yang menunggu verifikasi',
            style: TextStyle(fontSize: 11, color: AppTheme.textMuted, fontStyle: FontStyle.italic),
          ),
          const SizedBox(height: 16),
          _buildApprovalCard(
            context: context,
            title: 'Pengabdian Masyarakat Terpadu',
            prodi: 'Teknik Sipil',
            pengusul: 'Siti Aminah',
            date: '20 Jun 2026',
            status: 'Menunggu Verifikasi',
            statusColor: Colors.orange,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BendaharaLpjDetailView(id: 201, status: 'Menunggu Verifikasi')))
          ),
          _buildApprovalCard(
            context: context,
            title: 'Lomba Inovasi Mahasiswa',
            prodi: 'Akuntansi',
            pengusul: 'Dedi Kurniawan',
            date: '25 Jun 2026',
            status: 'Revisi',
            statusColor: Colors.red,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BendaharaLpjDetailView(id: 202, status: 'Revisi')))
          ),
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
                  'Dashboard Bendahara',
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
            child: const Icon(Icons.account_balance_wallet_rounded, color: Colors.white, size: 48),
          ),
        ],
      ),
    );
  }

  Widget _buildStatisticsGrid(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final bool isMobile = constraints.maxWidth < 600;
        return GridView.count(
          crossAxisCount: isMobile ? 2 : 4,
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          childAspectRatio: isMobile ? 1.3 : 1.1,
          children: [
            _buildStatCard(Icons.layers_outlined, '42', 'TOTAL\nKAK', AppTheme.totalUsulanGradient),
            _buildStatCard(Icons.check_circle_outline, '18', 'DICAIRKAN', AppTheme.disetujuiGradient),
            _buildStatCard(Icons.hourglass_bottom_outlined, '15', 'MENUNGGU', AppTheme.menungguGradient),
            _buildStatCard(Icons.cancel_outlined, '9', 'DITOLAK', AppTheme.ditolakGradient),
          ],
        );
      },
    );
  }

  Widget _buildStatCard(IconData icon, String count, String label, LinearGradient gradient) {
    return Container(
      decoration: BoxDecoration(
        gradient: gradient,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: gradient.colors.last.withOpacity(0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(12.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.2),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: Colors.white, size: 18),
            ),
            const Spacer(),
            Text(
              count,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 24,
                fontWeight: FontWeight.bold,
                height: 1.0,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                color: Colors.white.withOpacity(0.9),
                fontSize: 10,
                fontWeight: FontWeight.w600,
                letterSpacing: 0.5,
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildApprovalCard({
    required BuildContext context,
    required String title,
    required String prodi,
    required String pengusul,
    required String date,
    required String status,
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
          onTap: onTap,
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: statusColor.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        status.toUpperCase(),
                        style: TextStyle(
                          color: statusColor,
                          fontSize: 10,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 1,
                        ),
                      ),
                    ),
                    Text(
                      date,
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Text(
                  title,
                  style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppTheme.textDark, height: 1.3),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Icon(Icons.person_outline, size: 14, color: Colors.grey.shade500),
                    const SizedBox(width: 4),
                    Text(pengusul, style: TextStyle(fontSize: 12, color: Colors.grey.shade700)),
                  ],
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    Icon(Icons.apartment_rounded, size: 14, color: Colors.grey.shade500),
                    const SizedBox(width: 4),
                    Text(prodi, style: TextStyle(fontSize: 12, color: Colors.grey.shade700)),
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
                        const Text('Rp 15.000.000', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.blueGrey, fontSize: 13)),
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
  }
}
