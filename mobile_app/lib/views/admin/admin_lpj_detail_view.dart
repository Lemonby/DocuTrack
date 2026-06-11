import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

class AdminLpjDetailView extends StatelessWidget {
  final String status;
  const AdminLpjDetailView({super.key, this.status = 'Perlu Upload'});

  @override
  Widget build(BuildContext context) {
    final isEditable = (status == 'Revisi' || status == 'Perlu Upload' || status == 'Siap Submit' || status == 'Draft');
    final isDisetujui = (status == 'Disetujui' || status == 'Selesai');

    Color statusColor = Colors.blue;
    if (status == 'Revisi') { statusColor = Colors.orange; }
    else if (status == 'Perlu Upload') { statusColor = Colors.orange; }
    else if (status == 'Disetujui') { statusColor = Colors.green; }
    else if (status == 'Telah Direvisi') { statusColor = Colors.cyan; }

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Pusat Pertanggungjawaban', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            Text('Akses: Admin TI', style: TextStyle(fontSize: 10, color: Colors.indigo.shade400)),
          ],
        ),
        backgroundColor: Colors.white,
        foregroundColor: AppTheme.textDark,
        elevation: 1,
        shadowColor: Colors.black12,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (status == 'Revisi')
              Container(
                padding: const EdgeInsets.all(20),
                margin: const EdgeInsets.only(bottom: 20),
                decoration: BoxDecoration(
                  gradient: LinearGradient(colors: [Colors.orange.shade50, Colors.orange.shade100]),
                  border: Border.all(color: Colors.orange.shade200),
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [BoxShadow(color: Colors.orange.withOpacity(0.1), blurRadius: 10, offset: const Offset(0, 5))],
                ),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), boxShadow: [BoxShadow(color: Colors.orange.withOpacity(0.2), blurRadius: 8)]),
                      child: Icon(Icons.warning_amber_rounded, color: Colors.orange.shade700, size: 28),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('PERLU PERBAIKAN LAPORAN', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.orange.shade900, fontSize: 11, letterSpacing: 1)),
                          const SizedBox(height: 6),
                          Text('"Terdapat bukti yang kurang jelas atau nominal yang tidak sesuai. Mohon periksa kembali."', style: TextStyle(fontSize: 13, color: Colors.orange.shade800, fontStyle: FontStyle.italic, fontWeight: FontWeight.w600, height: 1.4)),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

            // Header Premium
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: [Colors.white, Colors.indigo.shade50.withOpacity(0.5)]),
                borderRadius: BorderRadius.circular(24),
                boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 20, offset: Offset(0, 10))],
                border: Border.all(color: Colors.white, width: 2),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(12), border: Border.all(color: statusColor.withOpacity(0.2))),
                        child: Text(status.toUpperCase(), style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 1)),
                      ),
                      const SizedBox(width: 12),
                      Container(width: 4, height: 4, decoration: const BoxDecoration(color: Colors.grey, shape: BoxShape.circle)),
                      const SizedBox(width: 12),
                      const Text('KODE LPJ: #00123', style: TextStyle(color: AppTheme.textMuted, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 1)),
                    ],
                  ),
                  const SizedBox(height: 16),
                  const Text('Laporan Pertanggungjawaban', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900, height: 1.2, letterSpacing: -0.5)),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Icon(Icons.event_available, size: 16, color: Colors.indigo.shade400),
                      const SizedBox(width: 8),
                      Expanded(
                        child: RichText(
                          text: TextSpan(
                            style: const TextStyle(color: AppTheme.textMuted, fontSize: 12, fontFamily: 'Poppins'),
                            children: [
                              const TextSpan(text: 'Kegiatan: '),
                              TextSpan(text: 'Pelatihan UI/UX Dasar', style: TextStyle(color: Colors.indigo.shade700, fontWeight: FontWeight.bold, decoration: TextDecoration.underline)),
                              const TextSpan(text: ' | Teknik Informatika'),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  if (isDisetujui) ...[
                    const SizedBox(height: 20),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () {},
                        icon: const Icon(Icons.print_rounded, size: 16),
                        label: const Text('CETAK LPJ', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green.shade600,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                      ),
                    ),
                  ]
                ],
              ),
            ),

            const SizedBox(height: 24),

            // Stepper
            Container(
              padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 20),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24), border: Border.all(color: Colors.grey.shade200)),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _buildStepIndicator('Penyusunan', '1', true, true, statusColor),
                  Expanded(child: Container(height: 4, margin: const EdgeInsets.only(bottom: 24), decoration: BoxDecoration(color: statusColor, borderRadius: BorderRadius.circular(2)))),
                  _buildStepIndicator('Verifikasi', '2', status != 'Perlu Upload' && status != 'Siap Submit', isDisetujui, statusColor),
                  Expanded(child: Container(height: 4, margin: const EdgeInsets.only(bottom: 24), decoration: BoxDecoration(color: isDisetujui ? statusColor : Colors.grey.shade200, borderRadius: BorderRadius.circular(2)))),
                  _buildStepIndicator('Selesai', '3', isDisetujui, isDisetujui, statusColor),
                ],
              ),
            ),

            const SizedBox(height: 24),

            if (isEditable)
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.indigo.shade600,
                  borderRadius: BorderRadius.circular(24),
                  boxShadow: [BoxShadow(color: Colors.indigo.withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 8))]
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), borderRadius: BorderRadius.circular(16)),
                      child: const Icon(Icons.lightbulb_outline_rounded, color: Colors.white, size: 28),
                    ),
                    const SizedBox(width: 16),
                    const Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('PANDUAN DIGITAL REALISASI', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 10, letterSpacing: 1)),
                          SizedBox(height: 4),
                          Text('Pastikan nominal sesuai dengan bukti fisik. Sistem akan memvalidasi otomatis.', style: TextStyle(color: Colors.white70, fontSize: 12, height: 1.3)),
                        ],
                      ),
                    )
                  ],
                ),
              ),

            const SizedBox(height: 32),
            const Text('RINCIAN ITEM PENGELUARAN', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Colors.blueGrey, letterSpacing: 1.5)),
            const SizedBox(height: 16),

            // RAB Item list
            _buildRabItemCard('Honorarium Narasumber', 'Honorarium', 300000, 300000, isEditable, statusColor),
            _buildRabItemCard('Konsumsi Peserta', 'Konsumsi', 500000, isEditable && status != 'Revisi' ? 0 : 500000, isEditable, statusColor, hasFeedback: status == 'Revisi'),

            const SizedBox(height: 32),

            // Summary Dashboard
            Container(
              decoration: BoxDecoration(
                color: Colors.grey.shade900,
                borderRadius: BorderRadius.circular(32),
                boxShadow: [BoxShadow(color: Colors.black26, blurRadius: 20, offset: const Offset(0, 10))],
              ),
              child: Column(
                children: [
                  Padding(
                    padding: const EdgeInsets.all(32),
                    child: Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('ANGGARAN DISETUJUI', style: TextStyle(color: Colors.white54, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 1)),
                              const SizedBox(height: 8),
                              const Text('Rp 800.000', style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w900, letterSpacing: -1)),
                              const SizedBox(height: 12),
                              Row(
                                children: [
                                  Container(width: 8, height: 8, decoration: const BoxDecoration(color: Colors.greenAccent, shape: BoxShape.circle)),
                                  const SizedBox(width: 8),
                                  const Text('DANA TERSEDIA', style: TextStyle(color: Colors.white54, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 1)),
                                ],
                              )
                            ],
                          ),
                        ),
                        Container(width: 1, height: 80, color: Colors.white12),
                        const SizedBox(width: 24),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('REALISASI DILAPORKAN', style: TextStyle(color: Colors.white54, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 1)),
                              const SizedBox(height: 8),
                              Text(isEditable && status != 'Revisi' ? 'Rp 300.000' : 'Rp 800.000', style: TextStyle(color: isEditable && status != 'Revisi' ? Colors.blue.shade400 : Colors.greenAccent, fontSize: 24, fontWeight: FontWeight.w900, letterSpacing: -1)),
                              const SizedBox(height: 12),
                              if (isEditable && status != 'Revisi')
                                const Text('Menunggu Input Data...', style: TextStyle(color: Colors.white54, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 1))
                              else
                                const Text('Sesuai Anggaran', style: TextStyle(color: Colors.greenAccent, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 1))
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 20),
                    decoration: const BoxDecoration(
                      border: Border(top: BorderSide(color: Colors.white12)),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(color: Colors.blue.withOpacity(0.2), borderRadius: BorderRadius.circular(8)),
                              child: Icon(Icons.fingerprint, color: Colors.blue.shade400, size: 16),
                            ),
                            const SizedBox(width: 12),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text('KODE MAK AKTIF', style: TextStyle(color: Colors.white54, fontSize: 8, fontWeight: FontWeight.w900, letterSpacing: 1.5)),
                                Text('521211.01.02', style: TextStyle(color: Colors.blue.shade100, fontSize: 12, fontWeight: FontWeight.w900, fontFamily: 'monospace')),
                              ],
                            )
                          ],
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                          decoration: BoxDecoration(color: Colors.greenAccent.withOpacity(0.1), borderRadius: BorderRadius.circular(8), border: Border.all(color: Colors.greenAccent.withOpacity(0.3))),
                          child: const Row(
                            children: [
                              Icon(Icons.check_circle, color: Colors.greenAccent, size: 12),
                              SizedBox(width: 6),
                              Text('TERVERIFIKASI', style: TextStyle(color: Colors.greenAccent, fontSize: 9, fontWeight: FontWeight.w900, letterSpacing: 1)),
                            ],
                          ),
                        )
                      ],
                    ),
                  )
                ],
              ),
            ),

            const SizedBox(height: 24),

            if (isEditable)
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () => Navigator.pop(context),
                  icon: const Icon(Icons.send_rounded),
                  label: const Text('KIRIM LPJ DIGITAL', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 2)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.indigo.shade600,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 24),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
                    elevation: 10,
                    shadowColor: Colors.indigo.withOpacity(0.4),
                  ),
                ),
              )
            else
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.shield_rounded, color: isDisetujui ? Colors.green : Colors.blueGrey, size: 20),
                    const SizedBox(width: 12),
                    Expanded(child: Text(isDisetujui ? 'Dokumen ini telah disetujui dan diverifikasi.' : 'Dokumen ini telah dikunci dan sedang dalam tahap verifikasi final.', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blueGrey, fontStyle: FontStyle.italic, letterSpacing: 0.5))),
                  ],
                ),
              )
          ],
        ),
      ),
    );
  }

  Widget _buildStepIndicator(String title, String num, bool active, bool done, Color activeColor) {
    return Column(
      children: [
        Container(
          width: 44,
          height: 44,
          decoration: BoxDecoration(
            color: done ? activeColor : (active ? Colors.white : Colors.white),
            border: Border.all(color: done || active ? activeColor : Colors.grey.shade300, width: active ? 4 : 2),
            shape: BoxShape.circle,
            boxShadow: active && !done ? [BoxShadow(color: activeColor.withOpacity(0.3), blurRadius: 10)] : null,
          ),
          child: Center(
            child: done
                ? const Icon(Icons.check_rounded, color: Colors.white, size: 20)
                : Text(num, style: TextStyle(color: active ? activeColor : Colors.grey.shade400, fontWeight: FontWeight.w900, fontSize: 16)),
          ),
        ),
        const SizedBox(height: 12),
        Text(title.toUpperCase(), style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: active || done ? AppTheme.textDark : AppTheme.textMuted, letterSpacing: 1)),
      ],
    );
  }

  Widget _buildRabItemCard(String uraian, String kategori, int anggaran, int realisasi, bool isEditable, Color activeColor, {bool hasFeedback = false}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(16)),
                  child: const Icon(Icons.folder_open_rounded, color: Colors.blueGrey, size: 24),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(uraian, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15, color: AppTheme.textDark)),
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                            decoration: BoxDecoration(color: Colors.grey.shade200, borderRadius: BorderRadius.circular(4)),
                            child: Text(kategori, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
                          ),
                          const SizedBox(width: 8),
                          const Text('•  1 Paket', style: TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold)),
                        ],
                      )
                    ],
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1, thickness: 1, color: Colors.black12),
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('ANGGARAN', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 1)),
                    const SizedBox(height: 6),
                    Text('Rp $anggaran', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: Colors.blueGrey)),
                  ],
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    const Text('REALISASI', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.blue, letterSpacing: 1)),
                    const SizedBox(height: 6),
                    if (isEditable)
                      SizedBox(
                        width: 140,
                        child: TextField(
                          textAlign: TextAlign.right,
                          style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16),
                          decoration: InputDecoration(
                            prefixText: 'Rp ',
                            prefixStyle: const TextStyle(fontWeight: FontWeight.bold, color: Colors.grey, fontSize: 12),
                            isDense: true,
                            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                            filled: true,
                            fillColor: Colors.blue.shade50,
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                          ),
                          controller: TextEditingController(text: realisasi > 0 ? realisasi.toString() : ''),
                        ),
                      )
                    else
                      Text('Rp $realisasi', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, color: activeColor)),
                  ],
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: const BorderRadius.vertical(bottom: Radius.circular(24))),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('LAMPIRAN / BUKTI', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blueGrey, letterSpacing: 1)),
                if (isEditable)
                  ElevatedButton.icon(
                    onPressed: () {},
                    icon: const Icon(Icons.camera_alt_rounded, size: 14),
                    label: const Text('UNGGAH', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, letterSpacing: 1)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: activeColor,
                      elevation: 2,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                    ),
                  )
                else
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                    decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.green.shade200)),
                    child: const Row(
                      children: [
                        Icon(Icons.receipt_rounded, color: Colors.green, size: 14),
                        SizedBox(width: 8),
                        Text('TERLAMPIR', style: TextStyle(color: Colors.green, fontWeight: FontWeight.w900, fontSize: 11, letterSpacing: 1)),
                      ],
                    ),
                  )
              ],
            ),
          ),
          if (hasFeedback)
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: Colors.orange.shade50, borderRadius: const BorderRadius.vertical(bottom: Radius.circular(24)), border: Border(top: BorderSide(color: Colors.orange.shade100))),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.comment_rounded, size: 16, color: Colors.orange.shade700),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Catatan Perbaikan dari Bendahara', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.orange, letterSpacing: 1)),
                        SizedBox(height: 4),
                        Text('Nota pembelian alat tulis tidak terbaca dengan jelas. Mohon unggah ulang foto nota yang lebih terang.', style: TextStyle(fontSize: 12, color: Colors.orange, fontWeight: FontWeight.w600, height: 1.4)),
                      ],
                    ),
                  ),
                ],
              ),
            )
        ],
      ),
    );
  }
}
