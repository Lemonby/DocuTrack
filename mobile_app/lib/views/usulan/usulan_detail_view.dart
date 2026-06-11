import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import 'usulan_form_view.dart';

class UsulanDetailView extends StatefulWidget {
  final int kegiatanId;

  const UsulanDetailView({super.key, required this.kegiatanId});

  @override
  State<UsulanDetailView> createState() => _UsulanDetailViewState();
}

class _UsulanDetailViewState extends State<UsulanDetailView> {
  String status = 'Proses';
  Color statusColor = Colors.blueGrey;

  @override
  void initState() {
    super.initState();
    _setupMockData();
  }

  void _setupMockData() {
    if (widget.kegiatanId == 9002 || widget.kegiatanId == 2) {
      status = 'Revisi';
      statusColor = Colors.orange;
    } else if (widget.kegiatanId == 9003 || widget.kegiatanId == 3) {
      status = 'Disetujui';
      statusColor = Colors.green;
    } else if (widget.kegiatanId == 9004 || widget.kegiatanId == 4) {
      status = 'Ditolak';
      statusColor = Colors.red;
    } else {
      status = 'Proses';
      statusColor = Colors.blueGrey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Detail Usulan KAK', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: AppTheme.textDark,
        elevation: 1,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (status == 'Revisi')
              _buildAlertBanner(
                'Perlu Revisi',
                'Terdapat beberapa bagian yang perlu diperbaiki sebelum pengajuan dapat dilanjutkan.',
                Colors.orange,
                Icons.warning_amber_rounded,
              )
            else if (status == 'Disetujui')
              _buildAlertBanner(
                'Usulan Disetujui',
                'Kegiatan ini telah melewati tahap verifikasi dan siap untuk dilaksanakan.',
                Colors.green,
                Icons.check_circle_rounded,
              ),

            // Header Container
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, 4))]),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(8), border: Border.all(color: statusColor.withOpacity(0.2))),
                        child: Text(status.toUpperCase(), style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 1)),
                      ),
                      const SizedBox(width: 8),
                      const Text('|', style: TextStyle(color: Colors.black26)),
                      const SizedBox(width: 8),
                      Text('ID USULAN: #USL-${widget.kegiatanId.toString().padLeft(5, '0')}', style: const TextStyle(color: AppTheme.textMuted, fontSize: 10, fontWeight: FontWeight.bold)),
                    ],
                  ),
                  const SizedBox(height: 16),
                  const Text('Detail Usulan Kegiatan', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: AppTheme.textDark, height: 1.2)),
                  const SizedBox(height: 24),
                  
                  // Progress Stepper
                  _buildProgressStepper(),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // KAK Data
            _buildCardWrapper(
              title: 'Informasi Kegiatan',
              icon: Icons.file_present_rounded,
              color: statusColor,
              child: Column(
                children: [
                  _buildDataRow('Nama Pengusul', 'Budi Santoso', 'NIM / NIP', '192001'),
                  const SizedBox(height: 16),
                  _buildDataRow('Jurusan & Prodi', 'Teknik Informatika\nD4 Teknik Informatika', 'Nama Kegiatan', 'Pembuatan Sistem Informasi Kepegawaian'),
                  const SizedBox(height: 16),
                  _buildDataRow('Wadir Tujuan', 'Wadir 1 - Akademik', 'Penerima Manfaat', 'Seluruh Mahasiswa TI'),
                  const SizedBox(height: 16),
                  _buildDataRow('Penanggung Jawab', 'Budi Santoso\nNIP: 192001', '', ''),
                  
                  const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider(height: 1)),
                  
                  _buildLongTextData('Gambaran Umum', 'Pembuatan sistem informasi untuk manajemen data pegawai yang lebih terintegrasi dan efisien.'),
                  const SizedBox(height: 16),
                  _buildLongTextData('Metode Pelaksanaan', 'Menggunakan metode Agile Scrum dengan sprint selama 2 minggu, mencakup perancangan, pengembangan, dan pengujian.'),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // IKU Section
            _buildCardWrapper(
              title: 'Indikator Kinerja Utama (IKU)',
              icon: Icons.track_changes_rounded,
              color: statusColor,
              child: Column(
                children: [
                  _buildIkuItem('IKU 1 - Lulusan Mendapat Pekerjaan yang Layak'),
                  _buildIkuItem('IKU 2 - Mahasiswa Mendapat Pengalaman di Luar Kampus'),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Tahapan Pelaksanaan
            _buildCardWrapper(
              title: 'Pelaksanaan & Keberhasilan',
              icon: Icons.checklist_rounded,
              color: statusColor,
              child: Column(
                children: [
                  _buildTahapanBulan('Januari', 'Pengumpulan kebutuhan sistem dan penyusunan desain UI/UX.', 'Desain disetujui oleh stakeholder', '100'),
                  _buildTahapanBulan('Februari', 'Pengembangan sistem backend dan frontend.', 'Fitur utama berjalan tanpa bug', '80'),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // RAB Total
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: [statusColor, statusColor.withBlue(statusColor.blue + 30).withRed(statusColor.red + 30)]),
                borderRadius: BorderRadius.circular(24),
                boxShadow: [BoxShadow(color: statusColor.withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 8))]
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.account_balance_wallet_rounded, color: Colors.white.withOpacity(0.8), size: 16),
                      const SizedBox(width: 8),
                      Text('TOTAL ANGGARAN (RAB)', style: TextStyle(color: Colors.white.withOpacity(0.8), fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 2)),
                    ],
                  ),
                  const SizedBox(height: 16),
                  const Text('Rp 15.000.000', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w900, color: Colors.white)),
                  const SizedBox(height: 4),
                  Text('Sudah termasuk pajak & biaya operasional', style: TextStyle(fontSize: 10, color: Colors.white.withOpacity(0.7))),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Action Card
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade200)),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('AKSI TERSEDIA', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blueGrey, letterSpacing: 1)),
                  const SizedBox(height: 16),
                  if (status == 'Revisi')
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () {
                          // Navigate to UsulanFormView with mock data
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => const UsulanFormView(
                                usulan: {
                                  'nama_mahasiswa': 'Budi Santoso',
                                  'nama': 'Pembuatan Sistem Informasi Kepegawaian',
                                  'jurusan': 'Teknik Informatika dan Komputer',
                                  'prodi': 'D4 Teknik Informatika',
                                  'id': 9002
                                }
                              )
                            )
                          );
                        },
                        icon: const Icon(Icons.edit_document),
                        label: const Text('REVISI SEKARANG', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.orange,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          elevation: 4,
                          shadowColor: Colors.orange.withOpacity(0.4),
                        ),
                      ),
                    )
                  else if (status == 'Proses')
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(color: Colors.blueGrey.withOpacity(0.05), borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.blueGrey.withOpacity(0.1))),
                      child: const Text('"Status saat ini: Menunggu Verifikasi. Tidak ada aksi yang dapat dilakukan."', textAlign: TextAlign.center, style: TextStyle(fontSize: 11, fontStyle: FontStyle.italic, color: Colors.blueGrey, fontWeight: FontWeight.bold)),
                    )
                  else if (status == 'Disetujui')
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () {},
                        icon: const Icon(Icons.download_rounded),
                        label: const Text('UNDUH BERKAS KAK', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          elevation: 4,
                          shadowColor: Colors.green.withOpacity(0.4),
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAlertBanner(String title, String subtitle, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(16),
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        border: Border(left: BorderSide(color: color, width: 4)),
        borderRadius: const BorderRadius.horizontal(right: Radius.circular(16)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: TextStyle(fontWeight: FontWeight.bold, color: color.withOpacity(0.9), fontSize: 14)),
                const SizedBox(height: 4),
                Text(subtitle, style: TextStyle(fontSize: 12, color: color.withOpacity(0.8), height: 1.4)),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildProgressStepper() {
    int currentStep = status == 'Disetujui' ? 2 : (status == 'Revisi' || status == 'Proses' ? 0 : 1);
    final steps = ['Pengajuan', 'Verifikasi', 'Selesai'];

    return Row(
      children: List.generate(steps.length * 2 - 1, (index) {
        if (index % 2 == 0) {
          int stepIdx = index ~/ 2;
          bool isCompleted = status == 'Disetujui' || stepIdx < currentStep || (stepIdx == 1 && status != 'Revisi' && status != 'Proses');
          bool isActive = (stepIdx == 1 && status == 'Proses') || (stepIdx == 0 && status == 'Revisi');
          
          Color bgColor = isCompleted ? statusColor : Colors.white;
          Color borderColor = isCompleted || isActive ? statusColor : Colors.grey.shade300;
          Color textColor = isCompleted ? Colors.white : (isActive ? statusColor : Colors.grey.shade400);

          return Column(
            children: [
              Container(
                width: 36, height: 36,
                decoration: BoxDecoration(color: bgColor, shape: BoxShape.circle, border: Border.all(color: borderColor, width: 3)),
                child: Center(
                  child: isCompleted 
                    ? const Icon(Icons.check, size: 16, color: Colors.white)
                    : Text('${stepIdx + 1}', style: TextStyle(color: textColor, fontWeight: FontWeight.bold, fontSize: 12))
                ),
              ),
              const SizedBox(height: 8),
              Text(steps[stepIdx], style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: isCompleted || isActive ? AppTheme.textDark : Colors.grey.shade400, letterSpacing: 0.5)),
            ],
          );
        } else {
          int stepIdx = (index - 1) ~/ 2;
          bool isLineActive = status == 'Disetujui' || stepIdx < currentStep;
          return Expanded(
            child: Container(
              height: 3,
              margin: const EdgeInsets.only(bottom: 20),
              decoration: BoxDecoration(color: isLineActive ? statusColor : Colors.grey.shade200),
            ),
          );
        }
      }),
    );
  }

  Widget _buildCardWrapper({required String title, required IconData icon, required Color color, required Widget child}) {
    return Container(
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade100), boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, 4))]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(border: Border(bottom: BorderSide(color: Colors.grey.shade100), left: BorderSide(color: color, width: 4))),
            child: Row(
              children: [
                Icon(icon, color: color, size: 20),
                const SizedBox(width: 12),
                Text(title, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textDark)),
              ],
            ),
          ),
          Padding(padding: const EdgeInsets.all(20), child: child),
        ],
      ),
    );
  }

  Widget _buildDataRow(String lbl1, String val1, String lbl2, String val2) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(child: _buildInfoItem(lbl1, val1)),
        if (lbl2.isNotEmpty) Expanded(child: _buildInfoItem(lbl2, val2)),
      ],
    );
  }

  Widget _buildInfoItem(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label.toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blueGrey, letterSpacing: 1)),
        const SizedBox(height: 4),
        Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textDark, height: 1.3)),
      ],
    );
  }

  Widget _buildLongTextData(String label, String text) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label.toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blueGrey, letterSpacing: 1)),
        const SizedBox(height: 8),
        Container(
          width: double.infinity, padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade100)),
          child: Text(text, style: const TextStyle(fontSize: 13, color: Colors.black87, height: 1.5, fontWeight: FontWeight.w500)),
        ),
      ],
    );
  }

  Widget _buildIkuItem(String text) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(color: statusColor.withOpacity(0.1), shape: BoxShape.circle),
            child: Icon(Icons.check_rounded, size: 12, color: statusColor),
          ),
          const SizedBox(width: 12),
          Expanded(child: Text(text, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.textDark))),
        ],
      ),
    );
  }

  Widget _buildTahapanBulan(String bulan, String tahapan, String indikator, String target) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(6)),
            child: Text(bulan.toUpperCase(), style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 1)),
          ),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(child: _buildInfoItem('Tahapan Pelaksanaan', tahapan)),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('INDIKATOR KEBERHASILAN', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blueGrey, letterSpacing: 1)),
                    const SizedBox(height: 4),
                    Text(indikator, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.textDark, height: 1.3)),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(color: Colors.blue.shade50, border: Border.all(color: Colors.blue.shade100), borderRadius: BorderRadius.circular(6)),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.track_changes_rounded, size: 10, color: Colors.blue.shade700),
                          const SizedBox(width: 4),
                          Text('Target: $target%', style: TextStyle(color: Colors.blue.shade700, fontSize: 10, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    )
                  ],
                ),
              ),
            ],
          )
        ],
      ),
    );
  }
}
