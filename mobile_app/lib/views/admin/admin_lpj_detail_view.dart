import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:file_picker/file_picker.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import '../../models/lpj.dart';
import '../../providers/usulan_provider.dart';

class AdminLpjDetailView extends StatefulWidget {
  final int lpjId;
  const AdminLpjDetailView({super.key, required this.lpjId});

  @override
  State<AdminLpjDetailView> createState() => _AdminLpjDetailViewState();
}

class _AdminLpjDetailViewState extends State<AdminLpjDetailView> {
  Lpj? _lpj;
  final Map<int, TextEditingController> _controllers = {};
  double _totalRealisasi = 0;
  double _totalAnggaran = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadDetail();
    });
  }

  Future<void> _loadDetail() async {
    final provider = context.read<UsulanProvider>();
    final detail = await provider.getLpjDetail(widget.lpjId);
    if (mounted && detail != null) {
      setState(() {
        _lpj = detail;
        _totalAnggaran = 0;
        _totalRealisasi = 0;
        for (var item in detail.items) {
<<<<<<< HEAD
          _controllers[item.id] = TextEditingController(text: item.realisasi?.toStringAsFixed(0) ?? '');
=======
          final realisasi = item.realisasi ?? 0;
          _controllers[item.id] = TextEditingController(text: realisasi.toStringAsFixed(0));
          _totalAnggaran += item.totalHarga ?? 0;
          _totalRealisasi += realisasi;
          
          _controllers[item.id]!.addListener(_updateTotals);
>>>>>>> c0d5a63 (fix masalah field semua yang ga ke show di halaman utama)
        }
      });
    }
  }

  void _updateTotals() {
    double total = 0;
    _controllers.forEach((key, controller) {
      total += double.tryParse(controller.text) ?? 0;
    });
    setState(() {
      _totalRealisasi = total;
    });
  }

  @override
  void dispose() {
    for (var controller in _controllers.values) {
      controller.dispose();
    }
    super.dispose();
  }

  Future<void> _uploadBukti(int lpjItemId) async {
    final result = await FilePicker.pickFiles(type: FileType.custom, allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png']);
    if (result != null && result.files.single.path != null) {
      final success = await context.read<UsulanProvider>().uploadLpjBukti(widget.lpjId, lpjItemId, result.files.single.path!);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(success ? 'Bukti berhasil diunggah' : 'Gagal mengunggah bukti'), backgroundColor: success ? Colors.green : Colors.red),
        );
        if (success) _loadDetail();
      }
    }
  }

  Future<void> _submitLpj() async {
    if (_lpj == null) return;
    
    final items = _lpj!.items.map((item) {
      return {
        'id': item.id,
        'realisasi': double.tryParse(_controllers[item.id]?.text ?? '0') ?? 0.0,
      };
    }).toList();

    final success = await context.read<UsulanProvider>().submitLpj(_lpj!.kegiatan!.id, items);
    if (mounted) {
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('LPJ berhasil disubmit'), backgroundColor: Colors.green));
        Navigator.pop(context);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(context.read<UsulanProvider>().errorMessage), backgroundColor: Colors.red));
      }
    }
  }

  String _formatCurrency(double amount) {
    return NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(amount);
  }

  @override
  Widget build(BuildContext context) {
    if (_lpj == null) return const Scaffold(body: Center(child: CircularProgressIndicator()));

    final status = (_lpj!.statusNama ?? 'Menunggu').toLowerCase();
    final isEditable = status.contains('upload') || status.contains('revisi') || status.contains('draft') || status.contains('menunggu');
    final isSelesai = status.contains('disetujui') || status.contains('selesai');

    Color statusColor = Colors.blue;
    if (status.contains('revisi')) statusColor = Colors.orange;
    else if (isSelesai) statusColor = Colors.green;
    else if (status.contains('upload')) statusColor = Colors.blue;

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Pusat Pertanggungjawaban', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: AppTheme.textDark,
        elevation: 1,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildActivityHeader(statusColor, status.toUpperCase()),
            const SizedBox(height: 20),
            if (status.contains('revisi') && _lpj!.komentarRevisi != null)
              _buildRevisionNote(),
            _buildSummaryDashboard(isSelesai),
            const SizedBox(height: 24),
            _buildSectionHeader('REALISASI KEGIATAN', Icons.receipt_long),
            const SizedBox(height: 12),
            ..._lpj!.items.map((item) => _buildItemCard(item, isEditable, statusColor)),
            const SizedBox(height: 32),
            if (isEditable)
              _buildSubmitButton()
            else
              _buildLockedInfo(isSelesai),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _buildActivityHeader(Color color, String status) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(24), 
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 20, offset: const Offset(0, 4))],
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(10), border: Border.all(color: color.withOpacity(0.2))),
                child: Text(status, style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
              ),
              const Spacer(),
              Text('ID LPJ: #${_lpj!.id.toString().padLeft(5, '0')}', style: const TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.bold)),
            ],
          ),
          const SizedBox(height: 16),
          Text(_lpj!.kegiatan?.namaKegiatan ?? '-', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: AppTheme.textDark, height: 1.2)),
          const SizedBox(height: 8),
          Row(
            children: [
              const Icon(Icons.apartment_rounded, size: 14, color: Colors.grey),
              const SizedBox(width: 4),
              Text(_lpj!.kegiatan?.prodiPenyelenggara ?? '-', style: const TextStyle(color: Colors.grey, fontSize: 12, fontWeight: FontWeight.w600)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildRevisionNote() {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.amber.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.amber.shade200)),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(Icons.warning_amber_rounded, color: Colors.amber, size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('PERLU PERBAIKAN LAPORAN', style: TextStyle(color: Colors.amber, fontWeight: FontWeight.w900, fontSize: 10)),
                const SizedBox(height: 4),
                Text(_lpj!.komentarRevisi!, style: TextStyle(color: Colors.amber.shade900, fontSize: 12, fontWeight: FontWeight.w500, fontStyle: FontStyle.italic)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryDashboard(bool isSelesai) {
    final diff = _totalRealisasi - _totalAnggaran;
    final bool isOver = diff > 0.1;
    final bool isExact = diff.abs() < 0.1;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: const Color(0xFF1E293B),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: Colors.indigo.withOpacity(0.2), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        children: [
          Row(
            children: [
<<<<<<< HEAD
              const Text('Realisasi (Rp)', style: TextStyle(fontSize: 12, color: Colors.grey)),
              if (isEditable)
                SizedBox(
                  width: 140,
                  child: TextField(
                    controller: _controllers[item.id],
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(isDense: true, filled: true, fillColor: Colors.grey.shade50, border: OutlineInputBorder(borderRadius: BorderRadius.circular(8))),
                  ),
                )
              else
                Text('Rp ${item.realisasi?.toStringAsFixed(0) ?? '0'}', style: const TextStyle(fontWeight: FontWeight.bold)),
=======
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('ANGGARAN DISETUJUI', style: TextStyle(color: Colors.white60, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 0.5)),
                    const SizedBox(height: 8),
                    Text(_formatCurrency(_totalAnggaran), style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w900)),
                  ],
                ),
              ),
              Container(width: 1, height: 40, color: Colors.white10),
              const SizedBox(width: 20),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('REALISASI LAPORAN', style: TextStyle(color: Colors.white60, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 0.5)),
                    const SizedBox(height: 8),
                    Text(_formatCurrency(_totalRealisasi), style: TextStyle(color: isOver ? Colors.redAccent : Colors.blueAccent, fontSize: 18, fontWeight: FontWeight.w900)),
                  ],
                ),
              ),
>>>>>>> c0d5a63 (fix masalah field semua yang ga ke show di halaman utama)
            ],
          ),
          const SizedBox(height: 20),
          Container(height: 1, color: Colors.white10),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                isExact ? 'NOMINAL SESUAI PAGU' : (isOver ? 'MELEBIHI ANGGARAN' : 'SISA ANGGARAN (HEMAT)'),
                style: TextStyle(color: isExact ? Colors.green : (isOver ? Colors.redAccent : Colors.blueAccent), fontSize: 10, fontWeight: FontWeight.w900),
              ),
              Text(
                _formatCurrency(diff.abs()),
                style: TextStyle(color: isExact ? Colors.green : (isOver ? Colors.redAccent : Colors.blueAccent), fontSize: 10, fontWeight: FontWeight.w900),
              ),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 16, color: Colors.indigo),
        const SizedBox(width: 8),
        Text(title, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 12, color: AppTheme.textDark, letterSpacing: 0.5)),
      ],
    );
  }

  Widget _buildItemCard(LpjItem item, bool isEditable, Color statusColor) {
    final anggaran = item.totalHarga ?? 0;
    final realisasi = double.tryParse(_controllers[item.id]?.text ?? '0') ?? 0;
    final diff = realisasi - anggaran;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(20), 
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 4))],
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
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(6)),
                      child: Text(item.kategoriNama ?? 'Lainnya', style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey)),
                    ),
                    const Spacer(),
                    if (diff.abs() > 0.1)
                      Text(
                        diff > 0 ? '+${_formatCurrency(diff)}' : '-${_formatCurrency(diff.abs())}',
                        style: TextStyle(color: diff > 0 ? Colors.red : Colors.green, fontWeight: FontWeight.bold, fontSize: 10),
                      )
                    else
                      const Icon(Icons.check_circle, color: Colors.green, size: 14),
                  ],
                ),
                const SizedBox(height: 12),
                Text(item.uraian ?? '-', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: AppTheme.textDark)),
                const SizedBox(height: 4),
                Text(item.rincian ?? '-', style: const TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.w500)),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('ANGGARAN', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey)),
                          const SizedBox(height: 4),
                          Text(_formatCurrency(anggaran), style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.blueGrey)),
                        ],
                      ),
                    ),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const Text('REALISASI', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey)),
                          const SizedBox(height: 4),
                          if (isEditable)
                            TextField(
                              controller: _controllers[item.id],
                              keyboardType: TextInputType.number,
                              textAlign: TextAlign.end,
                              style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 13, color: Colors.indigo),
                              decoration: const InputDecoration(
                                isDense: true,
                                contentPadding: EdgeInsets.zero,
                                border: InputBorder.none,
                                hintText: '0',
                              ),
                            )
                          else
                            Text(_formatCurrency(realisasi), style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 13, color: AppTheme.textDark)),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: const BorderRadius.vertical(bottom: Radius.circular(20))),
            child: Row(
              children: [
                const Icon(Icons.receipt_long, size: 14, color: Colors.grey),
                const SizedBox(width: 8),
                Text(item.lampiranPath != null ? 'Bukti Terunggah' : 'Belum Ada Bukti', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: item.lampiranPath != null ? Colors.green : Colors.grey)),
                const Spacer(),
                if (isEditable)
                  TextButton(
                    onPressed: () => _uploadBukti(item.id),
                    style: TextButton.styleFrom(padding: EdgeInsets.zero, minimumSize: const Size(0, 0), tapTargetSize: MaterialTapTargetSize.shrinkWrap),
                    child: Text(item.lampiranPath != null ? 'GANTI BUKTI' : 'UNGGAH BUKTI', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Colors.indigo)),
                  )
                else if (item.lampiranPath != null)
                  const Text('LIHAT', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Colors.indigo)),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildSubmitButton() {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        boxShadow: [BoxShadow(color: Colors.indigo.withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: ElevatedButton(
        onPressed: _submitLpj,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.indigo, 
          foregroundColor: Colors.white, 
          padding: const EdgeInsets.symmetric(vertical: 20), 
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          elevation: 0,
        ),
        child: const Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text('KIRIM LPJ DIGITAL', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1.5, fontSize: 14)),
            SizedBox(width: 12),
            Icon(Icons.send_rounded, size: 18),
          ],
        ),
      ),
    );
  }

  Widget _buildLockedInfo(bool isSelesai) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade100)),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(color: isSelesai ? Colors.green.withOpacity(0.1) : Colors.amber.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
            child: Icon(isSelesai ? Icons.verified_user_rounded : Icons.lock_clock_rounded, color: isSelesai ? Colors.green : Colors.amber),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(isSelesai ? 'DOKUMEN FINAL' : 'TAHAP VERIFIKASI', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 10, color: isSelesai ? Colors.green : Colors.amber)),
                const SizedBox(height: 4),
                Text(isSelesai ? 'Laporan ini telah disetujui lunas.' : 'Dokumen sedang ditinjau oleh Bendahara.', style: const TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.w500)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
