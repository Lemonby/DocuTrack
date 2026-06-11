import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/bendahara_provider.dart';
import '../../theme/app_theme.dart';
import '../../models/lpj.dart';

class LpjDetailView extends StatefulWidget {
  final int lpjId;

  const LpjDetailView({super.key, required this.lpjId});

  @override
  State<LpjDetailView> createState() => _LpjDetailViewState();
}

class _LpjDetailViewState extends State<LpjDetailView> {
  Lpj? _lpj;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
    });
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    
    // DUMMY UI BYPASS FOR ADMIN TI DASHBOARD
    if (widget.lpjId >= 9000) {
      await Future.delayed(const Duration(milliseconds: 500));
      setState(() {
        _isLoading = false;
        String status = '';
        if (widget.lpjId == 9001) status = 'Menunggu';
        else if (widget.lpjId == 9002) status = 'Revisi';
        else if (widget.lpjId == 9003) status = 'Disetujui';

        _lpj = Lpj.fromJson({
          'id': widget.lpjId,
          'kegiatan_id': 9999,
          'total_pengeluaran': 300000,
          'submitted_at': '2026-10-25 10:00:00',
          'kegiatan': {
            'nama_kegiatan': 'Dummy Kegiatan LPJ $status',
            'pemilik_kegiatan': 'Budi Santoso',
            'jurusan_penyelenggara': 'Teknik Informatika',
          },
          'status': {'nama': status},
          'latar_belakang': 'Dummy latar belakang LPJ',
          'tujuan_kegiatan': 'Tujuan dummy LPJ',
          'catatan_review': status == 'Revisi' ? 'Harap perbaiki kuitansi' : null,
          'items': [
            {
              'keterangan': 'Honor Narasumber',
              'kategori_nama': 'Honorarium',
              'nominal': 300000,
            }
          ]
        });
      });
      return;
    }

    final provider = Provider.of<BendaharaProvider>(context, listen: false);
    final detail = await provider.getLpjDetail(widget.lpjId);
    if (mounted) {
      setState(() {
        _lpj = detail;
        _isLoading = false;
      });
    }
  }

  void _showActionDialog(String action) {
    final provider = Provider.of<BendaharaProvider>(context, listen: false);
    final isVerify = action == 'verify';
    final title = isVerify ? 'Verifikasi LPJ' : (action == 'reject' ? 'Tolak LPJ' : 'Revisi LPJ');
    final buttonColor = isVerify ? AppTheme.accentTeal : (action == 'reject' ? Colors.redAccent : Colors.orangeAccent);
    
    final _catatanCtrl = TextEditingController();

    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text(title, style: TextStyle(color: buttonColor, fontWeight: FontWeight.bold)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(isVerify ? 'Apakah Anda yakin ingin memverifikasi laporan ini?' : 'Silakan masukkan komentar:'),
              if (!isVerify) ...[
                const SizedBox(height: 12),
                TextField(
                  controller: _catatanCtrl,
                  maxLines: 3,
                  decoration: const InputDecoration(
                    hintText: 'Komentar tambahan...',
                    border: OutlineInputBorder(),
                  ),
                )
              ]
            ],
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: buttonColor, foregroundColor: Colors.white),
              onPressed: () async {
                Navigator.pop(context);
                final success = await provider.prosesLpj(widget.lpjId, action, komentar: _catatanCtrl.text);
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(success['message'] ?? (success['success'] ? 'Sukses' : 'Gagal')),
                      backgroundColor: success['success'] ? AppTheme.accentTeal : Colors.redAccent,
                    ),
                  );
                  if (success['success']) {
                    Navigator.pop(context); // Go back to list
                  }
                }
              },
              child: const Text('Proses'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<BendaharaProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Verifikasi Detail LPJ', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
      body: _isLoading || (provider.isLoadingLpj && _lpj == null)
          ? const Center(child: CircularProgressIndicator())
          : _lpj == null
              ? Center(child: Text(provider.errorMessageLpj))
              : _buildDetailContent(_lpj!),
      bottomNavigationBar: _lpj != null ? _buildActionButtons() : null,
    );
  }

  Widget _buildDetailContent(Lpj lpj) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Identitas
          _buildSectionTitle('Informasi Kegiatan'),
          Card(
            elevation: 0,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12), side: const BorderSide(color: AppTheme.borderLight)),
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                children: [
                  _buildDetailRow('Kegiatan', lpj.kegiatan?.namaKegiatan ?? '-'),
                  const Divider(),
                  _buildDetailRow('Tanggal Pengajuan', lpj.submittedAt ?? '-'),
                  const Divider(),
                  _buildDetailRow('Total Pengeluaran', 'Rp ${lpj.totalPengeluaran?.toStringAsFixed(0) ?? 0}'),
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),

          _buildSectionTitle('Rincian Item Pengeluaran'),
          if (lpj.items.isEmpty)
            const Text('Tidak ada item pengeluaran.', style: TextStyle(color: AppTheme.textMuted))
          else
            ...lpj.items.map((item) => Card(
              elevation: 0,
              margin: const EdgeInsets.only(bottom: 8),
              color: AppTheme.bgLight,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
              child: ListTile(
                leading: const Icon(Icons.receipt, color: AppTheme.primaryBlue),
                title: Text(item.keterangan ?? 'Tanpa keterangan', style: const TextStyle(fontWeight: FontWeight.bold)),
                subtitle: Text(item.kategoriNama ?? 'Kategori tidak tersedia'),
                trailing: Text('Rp ${item.nominal?.toStringAsFixed(0) ?? 0}', style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.accentTeal)),
              ),
            )),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0, left: 4.0),
      child: Text(
        title,
        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 2,
            child: Text(label, style: const TextStyle(color: AppTheme.textMuted, fontSize: 13)),
          ),
          Expanded(
            flex: 3,
            child: Text(value, style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 14)),
          ),
        ],
      ),
    );
  }

  Widget _buildActionButtons() {
    final provider = Provider.of<BendaharaProvider>(context, listen: false);
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: AppTheme.borderLight)),
      ),
      child: SafeArea(
        child: Row(
          children: [
            Expanded(
              child: OutlinedButton(
                style: OutlinedButton.styleFrom(foregroundColor: Colors.redAccent, side: const BorderSide(color: Colors.redAccent)),
                onPressed: provider.isSubmitting ? null : () => _showActionDialog('reject'),
                child: const Text('Tolak'),
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: OutlinedButton(
                style: OutlinedButton.styleFrom(foregroundColor: Colors.orangeAccent, side: const BorderSide(color: Colors.orangeAccent)),
                onPressed: provider.isSubmitting ? null : () => _showActionDialog('revise'),
                child: const Text('Revisi'),
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: AppTheme.accentTeal, foregroundColor: Colors.white),
                onPressed: provider.isSubmitting ? null : () => _showActionDialog('verify'),
                child: const Text('Verifikasi'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
