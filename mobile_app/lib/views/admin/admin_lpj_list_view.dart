import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import 'admin_lpj_detail_view.dart';

class AdminLpjListView extends StatelessWidget {
  const AdminLpjListView({super.key});

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
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
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
            
            _buildLpjCard(
              context,
              no: '1',
              title: 'Pembuatan Sistem Informasi Kepegawaian',
              subtitle: 'Budi Santoso (192001) • Teknik Informatika',
              date: '01 Nov 2026',
              deadline: '2026-11-03',
              status: 'Perlu Upload',
              statusColor: Colors.orange,
              btnText: 'Upload Bukti',
              btnIcon: Icons.upload_file,
            ),
            _buildLpjCard(
              context,
              no: '2',
              title: 'Pengadaan Komputer Lab A',
              subtitle: 'Siti Aminah (192002) • Teknik Informatika',
              date: '05 Nov 2026',
              deadline: '2026-11-04',
              status: 'Perlu Upload',
              statusColor: Colors.orange,
              btnText: 'Upload Bukti',
              btnIcon: Icons.upload_file,
            ),
            _buildLpjCard(
              context,
              no: '3',
              title: 'Pelatihan UI/UX Dasar',
              subtitle: 'Agus Pratama (192003) • Teknik Informatika',
              date: '10 Nov 2026',
              deadline: null,
              status: 'Siap Submit',
              statusColor: Colors.blue,
              btnText: 'Submit LPJ',
              btnIcon: Icons.send_rounded,
            ),
            _buildLpjCard(
              context,
              no: '4',
              title: 'Studi Banding Universitas',
              subtitle: 'Dewi Lestari (192004) • Teknik Informatika',
              date: '12 Nov 2026',
              deadline: null,
              status: 'Revisi',
              statusColor: Colors.amber.shade700,
              btnText: 'Lihat Revisi',
              btnIcon: Icons.warning_rounded,
            ),
            _buildLpjCard(
              context,
              no: '5',
              title: 'Lomba Pemrograman Nasional',
              subtitle: 'Reza Rahadian (192005) • Teknik Informatika',
              date: '15 Nov 2026',
              deadline: null,
              status: 'Telah Direvisi',
              statusColor: Colors.cyan,
              btnText: 'Cek Revisi',
              btnIcon: Icons.history_rounded,
            ),
            _buildLpjCard(
              context,
              no: '6',
              title: 'Seminar Nasional AI',
              subtitle: 'Rina Nose (192006) • Teknik Informatika',
              date: '20 Nov 2026',
              deadline: null,
              status: 'Disetujui',
              statusColor: Colors.green,
              btnText: 'Lihat Detail',
              btnIcon: Icons.check_circle_rounded,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLpjCard(
    BuildContext context, {
    required String no,
    required String title,
    required String subtitle,
    required String date,
    required String? deadline,
    required String status,
    required Color statusColor,
    required String btnText,
    required IconData btnIcon,
  }) {
    final isActionable = (status == 'Perlu Upload' || status == 'Siap Submit' || status == 'Revisi');
    
    Widget deadlineWidget = const SizedBox();
    if (status == 'Perlu Upload' && deadline != null) {
      // Dummy logic for deadline
      final diff = DateTime.parse(deadline).difference(DateTime.now()).inDays;
      Color dColor;
      String dText;
      IconData dIcon;
      if (diff < 0) {
        dColor = Colors.red;
        dText = 'Terlewat ${diff.abs()} hari';
        dIcon = Icons.warning_amber_rounded;
      } else if (diff == 0) {
        dColor = Colors.red;
        dText = 'Hari Ini!';
        dIcon = Icons.error_outline;
      } else if (diff <= 3) {
        dColor = Colors.orange;
        dText = 'Sisa $diff hari';
        dIcon = Icons.hourglass_bottom_rounded;
      } else {
        dColor = Colors.blue;
        dText = 'Sisa $diff hari';
        dIcon = Icons.calendar_today_rounded;
      }
      
      deadlineWidget = Container(
        margin: const EdgeInsets.only(top: 8),
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
        decoration: BoxDecoration(color: dColor.withOpacity(0.1), borderRadius: BorderRadius.circular(20), border: Border.all(color: dColor.withOpacity(0.3))),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(dIcon, size: 10, color: dColor),
            const SizedBox(width: 4),
            Text(dText, style: TextStyle(color: dColor, fontSize: 10, fontWeight: FontWeight.bold)),
          ],
        ),
      );
    } else if (status == 'Perlu Upload' && deadline == null) {
      deadlineWidget = Container(
        margin: const EdgeInsets.only(top: 8),
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
        decoration: BoxDecoration(color: Colors.orange.withOpacity(0.1), borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.orange.withOpacity(0.3))),
        child: const Text('Belum Ditetapkan', style: TextStyle(color: Colors.orange, fontSize: 10, fontWeight: FontWeight.bold)),
      );
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
                      child: Text('#$no', style: const TextStyle(color: Colors.black87, fontSize: 11, fontWeight: FontWeight.w900)),
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
                Text(title, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textDark, height: 1.2)),
                const SizedBox(height: 8),
                Text(subtitle, style: const TextStyle(color: AppTheme.textMuted, fontSize: 12, fontWeight: FontWeight.w500)),
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
                        deadlineWidget
                      ],
                    ),
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
              onPressed: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => AdminLpjDetailView(status: status)));
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
