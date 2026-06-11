import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/user.dart';
import '../../models/dashboard_data.dart';
import '../../models/kegiatan.dart';
import '../telaah/telaah_detail_view.dart';

class WadirDashboard extends StatelessWidget {
  final User user;
  final DashboardData? data;

  const WadirDashboard({super.key, required this.user, this.data});

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

          // Statistics Grid (Wadir has 3 cards: Total Usulan, Disetujui, Menunggu Persetujuan)
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

          // Filter Controls
          _buildFilterControls(),
          const SizedBox(height: 16),

          // List Items
          Builder(
            builder: (context) {
              final List<Kegiatan> dummyData = [
                Kegiatan(
                  id: 9301,
                  namaKegiatan: 'Pembelian Buku Perpustakaan Pusat',
                  jurusanPenyelenggara: 'Perpustakaan',
                  pemilikKegiatan: 'Dr. Hendra',
                  nimPelaksana: 'NIP. 19780101',
                  tanggalMulai: '10 Des 2026',
                  statusNama: 'Menunggu',
                ),
                Kegiatan(
                  id: 9302,
                  namaKegiatan: 'Seminar Nasional Teknologi Ramah Lingkungan',
                  jurusanPenyelenggara: 'Teknik Sipil',
                  pemilikKegiatan: 'Budi Santoso',
                  nimPelaksana: '190304001',
                  tanggalMulai: '12 Des 2026',
                  statusNama: 'Disetujui',
                ),
                Kegiatan(
                  id: 9303,
                  namaKegiatan: 'Pelatihan Soft Skill Mahasiswa Baru',
                  jurusanPenyelenggara: 'Semua Jurusan',
                  pemilikKegiatan: 'Kemahasiswaan',
                  nimPelaksana: 'NIP. 19800202',
                  tanggalMulai: '15 Des 2026',
                  statusNama: 'Ditolak',
                ),
              ];
              
              final List<Kegiatan> items = [...(data?.recentItems ?? []), ...dummyData];

              return Column(
                children: items.asMap().entries.map((entry) {
                  int index = entry.key;
                  Kegiatan item = entry.value;
                  String status = item.statusNama ?? 'Menunggu';
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
                          builder: (context) => TelaahDetailView(
                            kegiatanId: item.id,
                            rolePrefix: 'wadir',
                          ),
                        ),
                      );
                    },
                  );
                }).toList(),
              );
            }
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
              // Make the third item take full width on mobile or 1/3rd on large
              width: constraints.maxWidth > 600 ? (constraints.maxWidth - 64) / 3 : constraints.maxWidth - 40,
              child: _buildStatCard(
                Icons.hourglass_bottom_outlined, 
                '${stats?.menunggu ?? 0}', 
                'Menunggu Persetujuan', 
                const LinearGradient(colors: [Color(0xFFFDE047), Color(0xFFFACC15)], begin: Alignment.topLeft, end: Alignment.bottomRight),
                textColor: const Color(0xFF713F12), // Yellow-900 text for yellow background
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
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: 'Semua Status',
                    isExpanded: true,
                    icon: const Icon(Icons.keyboard_arrow_down, size: 18),
                    style: const TextStyle(fontSize: 13, color: AppTheme.textDark),
                    items: <String>['Semua Status', 'Menunggu', 'Disetujui', 'Ditolak']
                        .map<DropdownMenuItem<String>>((String value) {
                      return DropdownMenuItem<String>(
                        value: value,
                        child: Text(value),
                      );
                    }).toList(),
                    onChanged: (String? newValue) {},
                  ),
                ),
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
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: 'Semua Jurusan',
                    isExpanded: true,
                    icon: const Icon(Icons.keyboard_arrow_down, size: 18),
                    style: const TextStyle(fontSize: 13, color: AppTheme.textDark),
                    items: <String>['Semua Jurusan', 'Teknik Informatika', 'Teknik Elektro']
                        .map<DropdownMenuItem<String>>((String value) {
                      return DropdownMenuItem<String>(
                        value: value,
                        child: Text(value, overflow: TextOverflow.ellipsis),
                      );
                    }).toList(),
                    onChanged: (String? newValue) {},
                  ),
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Row(
          children: [
            Expanded(
              child: Container(
                height: 44,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.borderLight),
                ),
                child: const TextField(
                  decoration: InputDecoration(
                    hintText: 'Cari nama kegiatan, pengusul...',
                    hintStyle: TextStyle(fontSize: 13),
                    prefixIcon: Icon(Icons.search, size: 18),
                    border: InputBorder.none,
                    contentPadding: EdgeInsets.symmetric(vertical: 12),
                  ),
                ),
              ),
            ),
            const SizedBox(width: 8),
            Container(
              height: 44,
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(12),
              ),
              child: IconButton(
                onPressed: () {},
                icon: const Icon(Icons.refresh, size: 20, color: AppTheme.textDark),
                tooltip: 'Reset Filter',
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
          // Header Row
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
                    '#$no',
                    style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
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
          
          // Body Content
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Activity Name
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.assignment, size: 14, color: AppTheme.textMuted),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        title,
                        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                
                // Proposer info
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.person, size: 14, color: AppTheme.textMuted),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            '$pengusul ($nim)',
                            style: const TextStyle(fontSize: 12, color: AppTheme.textDark),
                          ),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              const Icon(Icons.school, size: 12, color: AppTheme.textMuted),
                              const SizedBox(width: 4),
                              Text(prodi, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                
                // Date
                Row(
                  children: [
                    const Icon(Icons.calendar_today, size: 14, color: AppTheme.textMuted),
                    const SizedBox(width: 6),
                    Text(date, style: const TextStyle(fontSize: 12, color: AppTheme.textDark)),
                  ],
                ),
                const SizedBox(height: 16),
                
                // Action Button
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: onTap,
                    icon: const Icon(Icons.visibility, size: 14),
                    label: const Text('LIHAT DETAIL', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primaryBlue,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                      elevation: 0,
                    ),
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
