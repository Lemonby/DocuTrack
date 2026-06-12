import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:file_picker/file_picker.dart';
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
        for (var item in detail.items) {
          _controllers[item.id] = TextEditingController(text: item.realisasi?.toStringAsFixed(0) ?? '');
        }
      });
    }
  }

  Future<void> _uploadBukti(int lpjItemId) async {
    final result = await FilePicker.pickFiles(type: FileType.custom, allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png']);
    if (result != null) {
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

  @override
  Widget build(BuildContext context) {
    if (_lpj == null) return const Scaffold(body: Center(child: CircularProgressIndicator()));

    final status = _lpj!.statusNama ?? 'Menunggu';
    final isEditable = (status == 'Revisi' || status == 'Menunggu' || status == 'Draft');
    final isDisetujui = (status == 'Disetujui' || status == 'Selesai');

    Color statusColor = Colors.blue;
    if (status == 'Revisi') statusColor = Colors.orange;
    else if (isDisetujui) statusColor = Colors.green;

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Detail LPJ', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: AppTheme.textDark,
        elevation: 1,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            _buildHeader(statusColor, status),
            const SizedBox(height: 24),
            ..._lpj!.items.map((item) => _buildItemCard(item, isEditable, statusColor)),
            const SizedBox(height: 32),
            if (isEditable)
              _buildSubmitButton()
            else
              _buildLockedInfo(isDisetujui),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader(Color color, String status) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24), boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 20)]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
                child: Text(status.toUpperCase(), style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.bold)),
              ),
              const Spacer(),
              Text('ID: #${_lpj!.id}', style: const TextStyle(color: Colors.grey, fontSize: 10)),
            ],
          ),
          const SizedBox(height: 16),
          Text(_lpj!.kegiatan?.namaKegiatan ?? '-', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildItemCard(LpjItem item, bool isEditable, Color color) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(item.uraian ?? '-', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
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
            ],
          ),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Lampiran Bukti', style: TextStyle(fontSize: 12, color: Colors.grey)),
              if (isEditable)
                TextButton.icon(
                  onPressed: () => _uploadBukti(item.id),
                  icon: Icon(item.lampiranPath != null ? Icons.check_circle : Icons.upload, size: 16),
                  label: Text(item.lampiranPath != null ? 'Ganti File' : 'Upload File'),
                )
              else
                Icon(item.lampiranPath != null ? Icons.check_circle : Icons.cancel, color: item.lampiranPath != null ? Colors.green : Colors.red),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildSubmitButton() {
    return SizedBox(
      width: double.infinity,
      child: ElevatedButton(
        onPressed: _submitLpj,
        style: ElevatedButton.styleFrom(backgroundColor: Colors.indigo, foregroundColor: Colors.white, padding: const EdgeInsets.symmetric(vertical: 20), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16))),
        child: const Text('SUBMIT LPJ KE BENDAHARA', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
    );
  }

  Widget _buildLockedInfo(bool isDisetujui) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(12)),
      child: Row(
        children: [
          Icon(isDisetujui ? Icons.verified : Icons.lock, color: Colors.grey),
          const SizedBox(width: 12),
          Expanded(child: Text(isDisetujui ? 'Laporan ini telah disetujui.' : 'Laporan sedang dalam proses verifikasi.', style: const TextStyle(fontSize: 12))),
        ],
      ),
    );
  }
}

// Extension to access raw fields from JSON if needed
extension LpjItemExt on LpjItem {
  String? get uraian => rawData?['uraian']?.toString();
}
