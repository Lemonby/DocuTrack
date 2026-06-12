import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/telaah_provider.dart';
import '../../theme/app_theme.dart';
import '../../models/kegiatan.dart';

class PpkDetailView extends StatefulWidget {
  final int kegiatanId;

  const PpkDetailView({super.key, required this.kegiatanId});

  @override
  State<PpkDetailView> createState() => _PpkDetailViewState();
}

class _PpkDetailViewState extends State<PpkDetailView> {
  Kegiatan? _kegiatan;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadDetail();
    });
  }

  Future<void> _loadDetail() async {
    final provider = Provider.of<TelaahProvider>(context, listen: false);
    final detail = await provider.getTelaahDetail('ppk', widget.kegiatanId);
    if (mounted) {
      setState(() {
        if (detail != null) {
          _kegiatan = detail;
        } else {
          _kegiatan = null;
        }
      });
    }
  }

  void _showActionDialog() {
    final provider = Provider.of<TelaahProvider>(context, listen: false);
    final buttonColor = Colors.green.shade600;
    
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return Container(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(context).viewInsets.bottom + 24,
            top: 24, left: 24, right: 24
          ),
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(color: buttonColor.withOpacity(0.1), shape: BoxShape.circle),
                    child: Icon(Icons.check_circle_outline_rounded, color: buttonColor, size: 28),
                  ),
                  const SizedBox(width: 16),
                  const Expanded(
                    child: Text('Setujui Dokumen', style: TextStyle(color: AppTheme.textDark, fontSize: 18, fontWeight: FontWeight.w900)),
                  ),
                ],
              ),
              const SizedBox(height: 20),
              const Text(
                'Anda akan menyetujui dokumen KAK ini.',
                style: TextStyle(fontSize: 14, color: Colors.blueGrey),
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.pop(context),
                      style: OutlinedButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                      child: const Text('Batal'),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(backgroundColor: buttonColor, foregroundColor: Colors.white, padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), elevation: 4),
                      onPressed: () async {
                        Navigator.pop(context);
                        final success = await provider.processAction(
                          'ppk', 
                          widget.kegiatanId, 
                          'approve',
                        );
                        if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text(success ? 'Tindakan berhasil diproses.' : provider.errorMessage),
                              backgroundColor: success ? Colors.green.shade600 : Colors.redAccent,
                            ),
                          );
                          if (success) {
                            Navigator.pop(context);
                          }
                        }
                      },
                      child: const Text('Konfirmasi', style: TextStyle(fontWeight: FontWeight.bold)),
                    ),
                  ),
                ],
              )
            ],
          ),
        );
      },
    );
  }

  double _calculateTotalRab(List<dynamic> rab) {
    double total = 0;
    for (var r in rab) {
      double hrg = double.tryParse((r['harga'] ?? r['harga_satuan'] ?? 0).toString()) ?? 0;
      double v1 = double.tryParse((r['vol1'] ?? r['volume_1'] ?? 1).toString()) ?? 1;
      double v2 = double.tryParse((r['vol2'] ?? r['volume_2'] ?? 1).toString()) ?? 1;
      total += (hrg * v1 * v2);
    }
    return total;
  }

  bool _canAction() {
    if (_kegiatan == null) return false;
    return _kegiatan!.posisiId == 3;
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<TelaahProvider>(context);

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Review & Telaah Usulan (PPK)', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: AppTheme.textDark)),
        backgroundColor: Colors.white,
        elevation: 1,
        iconTheme: const IconThemeData(color: AppTheme.textDark),
        centerTitle: false,
      ),
      body: provider.isLoading && _kegiatan == null
          ? const Center(child: CircularProgressIndicator())
          : _kegiatan == null
              ? Center(child: Text(provider.errorMessage))
              : _buildDetailContent(_kegiatan!),
      bottomNavigationBar: _canAction() ? _buildActionButtons() : _buildFinishedStatus(),
    );
  }

  Widget _buildFinishedStatus() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: Colors.black12, width: 0.5)),
      ),
      child: SafeArea(
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(vertical: 16),
          decoration: BoxDecoration(
            color: const Color(0xFFA8E6CF),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: const Color(0xFF8FE0C0)),
          ),
          child: const Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.check_circle, color: Color(0xFF1D7A46), size: 20),
              SizedBox(width: 8),
              Text(
                'TELAH DISETUJU',
                style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, color: Color(0xFF1D7A46), letterSpacing: 0.5),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDetailContent(Kegiatan kegiatan) {
    final kak = kegiatan.rawData?['kak'];
    final rab = (kegiatan.rawData?['kak']?['rab'] ?? kegiatan.rawData?['rab'] ?? kegiatan.rawData?['rabs']) as List? ?? [];
    final lampiran = kegiatan.rawData?['lampiran'] as List? ?? [];
    final jadwal = kegiatan.rawData?['jadwal'] as List? ?? [];
    final tahapanKegiatan = kegiatan.rawData?['tahapan_kegiatan']?.toString();
    
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Identitas
          _buildCardWrapper(
            title: 'Identitas Kegiatan',
            icon: Icons.assignment_ind_rounded,
            color: Colors.blue,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Status Saat Ini', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: _canAction() ? Colors.orange.shade50 : Colors.green.shade50,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: _canAction() ? Colors.orange.shade300 : Colors.green.shade300),
                      ),
                      child: Text(
                        (kegiatan.statusNama ?? 'Menunggu').toUpperCase(),
                        style: TextStyle(
                          color: _canAction() ? Colors.orange.shade700 : Colors.green.shade700,
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ],
                ),
                _buildDivider(),
                _buildInfoRow('Nama Kegiatan', kegiatan.namaKegiatan),
                _buildDivider(),
                _buildInfoRow('Pengusul', kegiatan.pemilikKegiatan ?? '-'),
                _buildDivider(),
                _buildInfoRow('NIM/NIP', kegiatan.nimPelaksana ?? '-'),
                _buildDivider(),
                _buildInfoRow('Jurusan', kegiatan.jurusanPenyelenggara ?? '-'),
                if (kegiatan.rawData?['tanggal_mulai'] != null) ...[
                  _buildDivider(),
                  _buildInfoRow('Tanggal Mulai', kegiatan.rawData!['tanggal_mulai'].toString()),
                ],
                if (kegiatan.rawData?['tanggal_selesai'] != null) ...[
                  _buildDivider(),
                  _buildInfoRow('Tanggal Selesai', kegiatan.rawData!['tanggal_selesai'].toString()),
                ],
                if (kegiatan.rawData?['kode_mak'] != null) ...[
                  _buildDivider(),
                  _buildInfoRow('Kode MAK', kegiatan.rawData!['kode_mak'].toString()),
                ],
              ],
            )
          ),
          const SizedBox(height: 24),

          // Data Lampiran & Berkas
          if (lampiran.isNotEmpty) ...[
            _buildCardWrapper(
              title: 'Data Lampiran & Berkas',
              icon: Icons.attach_file,
              color: Colors.orange,
              child: Column(
                children: lampiran.map((l) {
                  return Container(
                    margin: const EdgeInsets.only(bottom: 8),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.grey.shade50,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: Colors.grey.shade200),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.picture_as_pdf, color: Colors.red, size: 24),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Text(
                            l['nama'] ?? '-',
                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                          ),
                        ),
                        const Icon(Icons.download_rounded, color: AppTheme.primaryBlue, size: 20),
                      ],
                    ),
                  );
                }).toList(),
              ),
            ),
            const SizedBox(height: 24),
          ],

          // Detail KAK
          if (kak != null) ...[
            _buildCardWrapper(
              title: 'Informasi KAK',
              icon: Icons.description_rounded,
              color: Colors.purple,
              child: Column(
                children: [
                  _buildInfoRow('Indikator Kinerja Utama', kak['iku'] ?? '-'),
                  _buildDivider(),
                  _buildInfoRow('Gambaran Umum', kak['gambaran_umum'] ?? '-'),
                  _buildDivider(),
                  _buildInfoRow('Penerima Manfaat', kak['penerima_manfaat'] ?? '-'),
                  _buildDivider(),
                  _buildInfoRow('Metode Pelaksanaan', kak['metode_pelaksanaan'] ?? '-'),
                ],
              )
            ),
            const SizedBox(height: 24),
          ],

          // Jadwal Pelaksanaan
          if (jadwal.isNotEmpty) ...[
            _buildCardWrapper(
              title: 'Pelaksanaan & Keberhasilan Per Bulan',
              icon: Icons.calendar_month_rounded,
              color: Colors.blueAccent,
              child: Column(
                children: jadwal.map((j) {
                  return Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.blueAccent.withOpacity(0.03),
                      border: Border.all(color: Colors.blueAccent.withOpacity(0.1)),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.event_note_rounded, size: 16, color: Colors.blueAccent),
                            const SizedBox(width: 8),
                            Text((j['bulan'] ?? '-').toUpperCase(), style: TextStyle(fontWeight: FontWeight.w900, fontSize: 12, color: Colors.blueAccent.shade700, letterSpacing: 0.5)),
                          ],
                        ),
                        const SizedBox(height: 16),
                        _buildInfoRow('Tahapan Pelaksanaan', j['tahapan_pelaksanaan'] ?? '-'),
                        const Padding(
                          padding: EdgeInsets.symmetric(vertical: 12),
                          child: Divider(height: 1, color: Colors.black12),
                        ),
                        _buildInfoRow('Indikator Keberhasilan', j['indikator_keberhasilan'] ?? '-'),
                      ],
                    ),
                  );
                }).toList(),
              ),
            ),
            const SizedBox(height: 24),
          ],

          // Tahapan Kegiatan
          if (tahapanKegiatan != null) ...[
            _buildCardWrapper(
              title: 'Tahapan Kegiatan',
              icon: Icons.format_list_numbered_rounded,
              color: Colors.indigo,
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.indigo.withOpacity(0.03),
                  border: Border.all(color: Colors.indigo.withOpacity(0.1)),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Text(
                  tahapanKegiatan,
                  style: const TextStyle(fontSize: 14, color: AppTheme.textDark, height: 1.6, fontWeight: FontWeight.w500),
                ),
              ),
            ),
            const SizedBox(height: 24),
          ],
          
          // RAB Rendering
          const Row(
            children: [
              Icon(Icons.account_balance_wallet_rounded, color: Colors.teal, size: 20),
              SizedBox(width: 8),
              Text('Rincian Anggaran (RAB)', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
            ],
          ),
          const SizedBox(height: 16),
          if (rab.isEmpty)
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
              child: const Center(child: Text('Tidak ada RAB.', style: TextStyle(color: Colors.grey))),
            )
          else
            ...rab.asMap().entries.map((entry) {
              int i = entry.key;
              var r = entry.value;
              double hrg = double.tryParse((r['harga'] ?? r['harga_satuan'] ?? 0).toString()) ?? 0;
              double v1 = double.tryParse((r['vol1'] ?? r['volume_1'] ?? 1).toString()) ?? 1;
              double v2 = double.tryParse((r['vol2'] ?? r['volume_2'] ?? 1).toString()) ?? 1;
              String sat1 = r['sat1'] ?? r['satuan_1'] ?? '';
              String sat2 = r['sat2'] ?? r['satuan_2'] ?? '';
              double total = hrg * v1 * v2;
              return Container(
                margin: const EdgeInsets.only(bottom: 12),
                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, 4))]),
                child: Column(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      decoration: BoxDecoration(color: Colors.teal.shade50, borderRadius: const BorderRadius.vertical(top: Radius.circular(16))),
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(6),
                            decoration: const BoxDecoration(color: Colors.teal, shape: BoxShape.circle),
                            child: Text('${i + 1}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 10)),
                          ),
                          const SizedBox(width: 12),
                          Expanded(child: Text(r['uraian'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.teal))),
                        ],
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        children: [
                          _buildRabRow('Rincian', r['rincian'] ?? '-'),
                          const SizedBox(height: 8),
                          _buildRabRow('Volume', '${v1.toStringAsFixed(0)} $sat1 x ${v2.toStringAsFixed(0)} $sat2'),
                          const SizedBox(height: 8),
                          _buildRabRow('Harga Satuan', 'Rp ${hrg.toStringAsFixed(0).replaceAll(RegExp(r'\B(?=(\d{3})+(?!\d))'), '.')}'),
                          const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider(height: 1)),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text('Total', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey, fontSize: 12)),
                              Text('Rp ${total.toStringAsFixed(0).replaceAll(RegExp(r'\B(?=(\d{3})+(?!\d))'), '.')}', style: TextStyle(fontWeight: FontWeight.w900, color: Colors.teal.shade700, fontSize: 16)),
                            ],
                          )
                        ],
                      ),
                    )
                  ],
                ),
              );
            }),

          // Grand Total
          if (rab.isNotEmpty)
            Container(
              margin: const EdgeInsets.only(top: 12, bottom: 24),
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: [Colors.teal.shade700, Colors.teal.shade500]),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [BoxShadow(color: Colors.teal.withOpacity(0.4), blurRadius: 12, offset: const Offset(0, 6))]
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('GRAND TOTAL', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, letterSpacing: 1)),
                  Text(
                    'Rp ${_calculateTotalRab(rab).toStringAsFixed(0).replaceAll(RegExp(r'\B(?=(\d{3})+(?!\d))'), '.')}',
                    style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900),
                  )
                ],
              ),
            )
        ],
      ),
    );
  }

  Widget _buildCardWrapper({required String title, required IconData icon, required Color color, required Widget child}) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 5))],
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            decoration: BoxDecoration(
              border: Border(bottom: BorderSide(color: Colors.grey.shade100)),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
                  child: Icon(icon, color: color, size: 20),
                ),
                const SizedBox(width: 12),
                Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(20),
            child: child,
          )
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
        const SizedBox(height: 6),
        Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppTheme.textDark, height: 1.4)),
      ],
    );
  }

  Widget _buildRabRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, color: Colors.blueGrey)),
        Text(value, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
      ],
    );
  }

  Widget _buildDivider() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12.0),
      child: Divider(color: Colors.grey.shade200, height: 1),
    );
  }

  Widget _buildActionButtons() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(
        color: Colors.white,
        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 20, offset: Offset(0, -5))],
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: SafeArea(
        child: Row(
          children: [
            Expanded(
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF187CFC), 
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  elevation: 4,
                  shadowColor: const Color(0xFF187CFC).withOpacity(0.4),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))
                ),
                onPressed: _showActionDialog,
                child: const Text('SETUJUI', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
