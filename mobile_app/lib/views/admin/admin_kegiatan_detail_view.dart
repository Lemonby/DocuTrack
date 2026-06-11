import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

class AdminKegiatanDetailView extends StatelessWidget {
  final String status;
  const AdminKegiatanDetailView({super.key, this.status = 'Proses'});

  @override
  Widget build(BuildContext context) {
    final isReadonly = (status == 'Disetujui' || status == 'Review' || status == 'Ditolak');

    Color statusColor = Colors.blueGrey;
    IconData statusIcon = Icons.hourglass_empty;
    if (status == 'Revisi') { statusColor = Colors.orange; statusIcon = Icons.warning_amber_rounded; }
    if (status == 'Review') { statusColor = Colors.blue; statusIcon = Icons.search; }
    if (status == 'Disetujui') { statusColor = Colors.green; statusIcon = Icons.check_circle_outline; }
    if (status == 'Ditolak') { statusColor = Colors.red; statusIcon = Icons.cancel_outlined; }

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(!isReadonly ? 'Lengkapi Kegiatan' : 'Detail Kegiatan', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            const Text('Akses: Admin TI', style: TextStyle(fontSize: 10, color: Colors.blue)),
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
                padding: const EdgeInsets.all(16),
                margin: const EdgeInsets.only(bottom: 20),
                decoration: BoxDecoration(
                  gradient: LinearGradient(colors: [Colors.orange.shade50, Colors.orange.shade100]),
                  border: Border.all(color: Colors.orange.shade200),
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [BoxShadow(color: Colors.orange.withOpacity(0.1), blurRadius: 10, offset: const Offset(0, 4))],
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle, boxShadow: [BoxShadow(color: Colors.orange.withOpacity(0.2), blurRadius: 5)]),
                      child: Icon(Icons.warning_amber_rounded, color: Colors.orange.shade700, size: 28),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Perlu Perbaikan Data', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.orange.shade900, fontSize: 14)),
                          const SizedBox(height: 4),
                          Text('Dokumen pendukung atau data penanggung jawab tidak valid. Mohon periksa kembali dan kirim ulang.', style: TextStyle(fontSize: 12, color: Colors.orange.shade800, height: 1.4)),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 5))],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: statusColor.withOpacity(0.05),
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                      border: Border(bottom: BorderSide(color: statusColor.withOpacity(0.1))),
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: statusColor.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: statusColor.withOpacity(0.3)),
                          ),
                          child: Row(
                            children: [
                              Icon(statusIcon, size: 14, color: statusColor),
                              const SizedBox(width: 6),
                              Text(status.toUpperCase(), style: TextStyle(color: statusColor, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1)),
                            ],
                          ),
                        ),
                        const Spacer(),
                        const Text('ID: #KGT-00123', style: TextStyle(color: AppTheme.textMuted, fontSize: 12, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ),
                  
                  Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildSectionHeader('Penanggung Jawab Kegiatan', Icons.person_pin),
                        const SizedBox(height: 16),
                        if (!isReadonly) ...[
                          _buildTextField('Nama Lengkap *', 'Masukkan nama lengkap'),
                          const SizedBox(height: 16),
                          _buildTextField('NIM / NIP *', 'Masukkan NIM atau NIP'),
                        ] else ...[
                          _buildReadOnlyField('Nama Lengkap', 'Budi Santoso'),
                          const SizedBox(height: 12),
                          _buildReadOnlyField('NIM / NIP', '192001'),
                        ],

                        const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider(height: 1)),

                        _buildSectionHeader('Waktu Pelaksanaan', Icons.calendar_month),
                        const SizedBox(height: 16),
                        if (!isReadonly) ...[
                          Row(
                            children: [
                              Expanded(child: _buildTextField('Tanggal Mulai *', '01 Nov', icon: Icons.calendar_today)),
                              const SizedBox(width: 12),
                              Expanded(child: _buildTextField('Tanggal Selesai *', '05 Nov', icon: Icons.calendar_today)),
                            ],
                          )
                        ] else ...[
                          Row(
                            children: [
                              Expanded(child: _buildReadOnlyField('Tanggal Mulai', '01 Nov 2026', icon: Icons.event)),
                              const Padding(padding: EdgeInsets.symmetric(horizontal: 12), child: Icon(Icons.arrow_forward_rounded, color: Colors.grey, size: 16)),
                              Expanded(child: _buildReadOnlyField('Tanggal Selesai', '05 Nov 2026', icon: Icons.event)),
                            ],
                          ),
                        ],

                        const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider(height: 1)),

                        _buildSectionHeader('Dokumen Pendukung', Icons.file_present_rounded),
                        const SizedBox(height: 16),
                        if (!isReadonly) ...[
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.all(32),
                            decoration: BoxDecoration(
                              border: Border.all(color: Colors.blue.shade300, width: 2, style: BorderStyle.none),
                              borderRadius: BorderRadius.circular(16),
                              color: Colors.blue.shade50,
                            ),
                            child: Column(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(16),
                                  decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle),
                                  child: Icon(Icons.cloud_upload_outlined, size: 36, color: Colors.blue.shade600),
                                ),
                                const SizedBox(height: 16),
                                const Text('Unggah Surat Pengantar / Undangan', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.blue), textAlign: TextAlign.center),
                                const SizedBox(height: 4),
                                const Text('Format: PDF (Maks. 5MB)', style: TextStyle(fontSize: 11, color: Colors.blueGrey)),
                              ],
                            ),
                          ),
                        ] else ...[
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.grey.shade50,
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: Colors.grey.shade200),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(12)),
                                  child: const Icon(Icons.picture_as_pdf_rounded, size: 30, color: Colors.red),
                                ),
                                const SizedBox(width: 16),
                                const Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text('Surat Pengantar', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                                      Text('DOKUMEN_PENDUKUNG.PDF', style: TextStyle(fontSize: 10, color: AppTheme.textMuted)),
                                    ],
                                  ),
                                ),
                                IconButton(
                                  onPressed: () {},
                                  icon: const Icon(Icons.visibility),
                                  color: Colors.blue.shade700,
                                  style: IconButton.styleFrom(backgroundColor: Colors.blue.shade50),
                                )
                              ],
                            ),
                          )
                        ],
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),
            if (!isReadonly)
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () => Navigator.pop(context),
                  icon: const Icon(Icons.send_rounded),
                  label: const Text('SIMPAN & KIRIM', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.blue.shade700,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 20),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    elevation: 5,
                    shadowColor: Colors.blue.withOpacity(0.5),
                  ),
                ),
              )
            else
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('KEMBALI KE LIST', style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.grey.shade800,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 20),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                ),
              )
          ],
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: Colors.blueGrey),
        const SizedBox(width: 8),
        Text(title.toUpperCase(), style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 11, color: Colors.blueGrey, letterSpacing: 1)),
      ],
    );
  }

  Widget _buildReadOnlyField(String label, String value, {IconData? icon}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
        const SizedBox(height: 4),
        Row(
          children: [
            if (icon != null) ...[Icon(icon, size: 14, color: AppTheme.textDark), const SizedBox(width: 6)],
            Text(value, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: AppTheme.textDark)),
          ],
        ),
      ],
    );
  }

  Widget _buildTextField(String label, String hint, {IconData? icon}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
        const SizedBox(height: 8),
        TextField(
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: TextStyle(fontSize: 13, color: Colors.grey.shade400),
            prefixIcon: icon != null ? Icon(icon, size: 18, color: Colors.grey.shade500) : null,
            filled: true,
            fillColor: Colors.grey.shade50,
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade300)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.blue.shade400, width: 2)),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          ),
        ),
      ],
    );
  }
}
