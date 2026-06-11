import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/user.dart';
import '../../models/dashboard_data.dart';
import '../admin/admin_lpj_detail_view.dart';
import '../usulan/usulan_detail_view.dart';

class AdminDashboard extends StatelessWidget {
  final User user;
  final DashboardData? data;

  const AdminDashboard({super.key, required this.user, this.data});

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
          const SizedBox(height: 32),

          // Progress Workflow Sections
          const Text('ALUR STATUS BERKAS', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.blueGrey, letterSpacing: 1.5)),
          const SizedBox(height: 16),
          _buildWorkflowCard(
            title: 'Alur KAK Saat Ini',
            accentColor: Colors.blue.shade600,
            steps: ['Penyusunan', 'Review', 'Revisi', 'Disetujui'],
            currentStepIndex: 2,
            icons: [Icons.edit_document, Icons.search_rounded, Icons.warning_amber_rounded, Icons.check_circle_rounded],
          ),
          const SizedBox(height: 16),
          _buildWorkflowCard(
            title: 'Alur LPJ Saat Ini',
            accentColor: Colors.green.shade600,
            steps: ['Penyusunan', 'Review', 'Revisi', 'Disetujui'],
            currentStepIndex: 1,
            icons: [Icons.post_add_rounded, Icons.search_rounded, Icons.warning_amber_rounded, Icons.check_circle_rounded],
          ),
          const SizedBox(height: 32),
          
          // List Pengajuan KAK
          Row(
            children: [
              Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(8)), child: Icon(Icons.file_copy_rounded, color: Colors.blue.shade600, size: 20)),
              const SizedBox(width: 12),
              const Text('Antrian KAK Terbaru', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
            ],
          ),
          const SizedBox(height: 16),
          _buildListCard(
            context,
            'Pembuatan Sistem Informasi Kepegawaian', 'Budi Santoso • Teknik Informatika', '01 Nov 2026', null, 'Proses', Colors.blueGrey,
            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const UsulanDetailView(kegiatanId: 9001)))
          ),
          _buildListCard(
            context,
            'Studi Banding Universitas', 'Dewi Lestari • Teknik Informatika', '12 Nov 2026', null, 'Revisi', Colors.orange,
            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const UsulanDetailView(kegiatanId: 9002)))
          ),
          const SizedBox(height: 32),

          // List Pengajuan LPJ
          Row(
            children: [
              Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(8)), child: Icon(Icons.receipt_long_rounded, color: Colors.green.shade600, size: 20)),
              const SizedBox(width: 12),
              const Text('Antrian LPJ Terbaru', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
            ],
          ),
          const SizedBox(height: 16),
          _buildListCard(
            context,
            'Pengadaan Komputer Lab A', 'Siti Aminah • Teknik Informatika', '05 Nov 2026', '2026-11-04', 'Perlu Upload', Colors.orange,
            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AdminLpjDetailView(status: 'Perlu Upload')))
          ),
          _buildListCard(
            context,
            'Pelatihan UI/UX Dasar', 'Agus Pratama • Teknik Informatika', '10 Nov 2026', null, 'Siap Submit', Colors.blue,
            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AdminLpjDetailView(status: 'Siap Submit')))
          ),
          const SizedBox(height: 12),
        ],
      ),
    );
  }

  Widget _buildWelcomeBanner() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: [Colors.indigo.shade800, Colors.indigo.shade500], begin: Alignment.topLeft, end: Alignment.bottomRight),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: Colors.indigo.withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Selamat Datang,', style: TextStyle(color: Colors.indigo.shade100, fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1)),
                    const SizedBox(height: 4),
                    Text(user.name, style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w900, letterSpacing: -0.5)),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), shape: BoxShape.circle),
                child: const Icon(Icons.admin_panel_settings_rounded, color: Colors.white, size: 36),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(color: Colors.white.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(width: 8, height: 8, decoration: const BoxDecoration(color: Colors.greenAccent, shape: BoxShape.circle)),
                const SizedBox(width: 8),
                Text('Role: Admin TI • ${user.departmentName ?? ''}', style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatisticsGrid(BuildContext context) {
    final stats = data?.stats;
    return LayoutBuilder(
      builder: (context, constraints) {
        int crossAxisCount = constraints.maxWidth > 600 ? 4 : 2;
        return GridView.count(
          crossAxisCount: crossAxisCount,
          crossAxisSpacing: 16,
          mainAxisSpacing: 16,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          childAspectRatio: 1.1,
          children: [
            _buildStatCard(Icons.layers_rounded, '${stats?.totalUsulan ?? 0}', 'TOTAL USULAN', [Colors.blue.shade400, Colors.blue.shade600]),
            _buildStatCard(Icons.check_circle_rounded, '${stats?.disetujui ?? 0}', 'DISETUJUI', [Colors.green.shade400, Colors.green.shade600]),
            _buildStatCard(Icons.cancel_rounded, '${stats?.ditolak ?? 0}', 'DITOLAK', [Colors.red.shade400, Colors.red.shade600]),
            _buildStatCard(Icons.hourglass_bottom_rounded, '${stats?.menunggu ?? 0}', 'MENUNGGU', [Colors.amber.shade400, Colors.amber.shade600]),
          ],
        );
      },
    );
  }

  Widget _buildStatCard(IconData icon, String count, String label, List<Color> gradientColors) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: gradientColors, begin: Alignment.topLeft, end: Alignment.bottomRight),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: gradientColors.last.withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 8))],
      ),
      child: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(14)),
              child: Icon(icon, color: Colors.white, size: 24),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(count, style: const TextStyle(fontSize: 32, fontWeight: FontWeight.w900, color: Colors.white, height: 1.1)),
                Text(label, style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.white.withOpacity(0.9), letterSpacing: 1), maxLines: 1, overflow: TextOverflow.ellipsis),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildWorkflowCard({
    required String title,
    required Color accentColor,
    required List<String> steps,
    required int currentStepIndex,
    required List<IconData> icons,
  }) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 20, offset: Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(width: 4, height: 16, decoration: BoxDecoration(color: accentColor, borderRadius: BorderRadius.circular(2))),
                  const SizedBox(width: 8),
                  Text(title, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14)),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(color: accentColor.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
                child: Text('Live Status', style: TextStyle(color: accentColor, fontSize: 10, fontWeight: FontWeight.bold)),
              )
            ],
          ),
          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: List.generate(
              steps.length * 2 - 1,
              (index) {
                if (index % 2 == 0) {
                  int stepIdx = index ~/ 2;
                  bool isCompleted = stepIdx < currentStepIndex;
                  bool isActive = stepIdx == currentStepIndex;
                  return _buildStepIndicator(
                    icons[stepIdx],
                    steps[stepIdx],
                    isActive,
                    isCompleted,
                    accentColor,
                  );
                } else {
                  int stepIdx = (index - 1) ~/ 2;
                  bool isLineActive = stepIdx < currentStepIndex;
                  return Expanded(
                    child: Container(
                      height: 4,
                      margin: const EdgeInsets.only(bottom: 24),
                      decoration: BoxDecoration(
                        color: isLineActive ? accentColor : Colors.grey.shade200,
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  );
                }
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStepIndicator(IconData icon, String label, bool isActive, bool isCompleted, Color accentColor) {
    Color bgColor = Colors.white;
    Color iconColor = Colors.grey.shade400;
    
    if (isActive) {
      bgColor = accentColor;
      iconColor = Colors.white;
    } else if (isCompleted) {
      bgColor = accentColor;
      iconColor = Colors.white;
    }

    return Column(
      children: [
        Container(
          width: 48,
          height: 48,
          decoration: BoxDecoration(
            color: bgColor,
            shape: BoxShape.circle,
            border: Border.all(color: isCompleted || isActive ? accentColor : Colors.grey.shade300, width: isActive ? 4 : 2),
            boxShadow: isActive ? [BoxShadow(color: accentColor.withOpacity(0.3), blurRadius: 10)] : null,
          ),
          child: Icon(isCompleted && !isActive ? Icons.check_rounded : icon, color: iconColor, size: 20),
        ),
        const SizedBox(height: 12),
        Text(
          label.toUpperCase(),
          style: TextStyle(
            fontSize: 9,
            fontWeight: FontWeight.w900,
            letterSpacing: 1,
            color: isActive || isCompleted ? AppTheme.textDark : AppTheme.textMuted,
          ),
        ),
      ],
    );
  }

  Widget _buildListCard(BuildContext context, String title, String subtitle, String date, String? deadline, String status, Color statusColor, VoidCallback onTap) {
    Widget deadlineWidget = const SizedBox();
    if (deadline != null) {
      final diff = DateTime.parse(deadline).difference(DateTime.now()).inDays;
      Color dColor = diff < 0 ? Colors.red : (diff <= 3 ? Colors.orange : Colors.blue);
      String dText = diff < 0 ? 'Terlewat ${diff.abs()} hari' : (diff == 0 ? 'Hari Ini!' : 'Sisa $diff hari');
      
      deadlineWidget = Container(
        margin: const EdgeInsets.only(top: 8),
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
        decoration: BoxDecoration(color: dColor.withOpacity(0.1), borderRadius: BorderRadius.circular(20)),
        child: Text(dText, style: TextStyle(color: dColor, fontSize: 10, fontWeight: FontWeight.bold)),
      );
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 5))],
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
                      decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
                      child: Text(status.toUpperCase(), style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
                    ),
                    Icon(Icons.arrow_forward_ios_rounded, size: 12, color: Colors.grey.shade400)
                  ],
                ),
                const SizedBox(height: 16),
                Text(title, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: AppTheme.textDark, height: 1.2)),
                const SizedBox(height: 8),
                Text(subtitle, style: const TextStyle(color: AppTheme.textMuted, fontSize: 11, fontWeight: FontWeight.w500)),
                const SizedBox(height: 12),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(Icons.calendar_month_rounded, size: 14, color: Colors.grey.shade400),
                            const SizedBox(width: 6),
                            Text(date, style: const TextStyle(fontSize: 12, color: AppTheme.textMuted, fontWeight: FontWeight.bold)),
                          ],
                        ),
                        if (deadline != null) deadlineWidget
                      ],
                    ),
                  ],
                ),
              ],
            ),
          ),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            decoration: BoxDecoration(
              color: Colors.grey.shade50,
              borderRadius: const BorderRadius.vertical(bottom: Radius.circular(20)),
              border: Border(top: BorderSide(color: Colors.grey.shade100)),
            ),
            child: ElevatedButton(
              onPressed: onTap,
              style: ElevatedButton.styleFrom(
                backgroundColor: statusColor,
                foregroundColor: Colors.white,
                elevation: 0,
                padding: const EdgeInsets.symmetric(vertical: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('Lihat Detail', style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1)),
            ),
          )
        ],
      ),
    );
  }
}
