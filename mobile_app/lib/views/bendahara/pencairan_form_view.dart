import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/bendahara_provider.dart';
import '../../theme/app_theme.dart';
import '../../models/kegiatan.dart';
import 'package:intl/intl.dart';

class PencairanFormView extends StatefulWidget {
  final int kegiatanId;

  const PencairanFormView({super.key, required this.kegiatanId});

  @override
  State<PencairanFormView> createState() => _PencairanFormViewState();
}

class _PencairanFormViewState extends State<PencairanFormView> {
  Kegiatan? _kegiatan;
  String _metode = 'penuh'; // 'penuh' or 'bertahap'
  
  // Penuh
  final _jumlahPenuhCtrl = TextEditingController();
  DateTime? _tanggalPenuh;

  // Bertahap
  List<Map<String, dynamic>> _tahapan = [];
  
  // General
  final _catatanCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadDetail();
    });
  }

  Future<void> _loadDetail() async {
    final provider = Provider.of<BendaharaProvider>(context, listen: false);
    final detail = await provider.getPencairanDetail(widget.kegiatanId);
    if (mounted) {
      setState(() {
        _kegiatan = detail;
      });
    }
  }

  void _addTahapan() {
    setState(() {
      _tahapan.add({
        'tanggal': null,
        'termin': TextEditingController(),
        'nominal': TextEditingController(),
      });
    });
  }

  void _removeTahapan(int index) {
    setState(() {
      _tahapan.removeAt(index);
    });
  }

  Future<void> _selectDate(BuildContext context, {bool isPenuh = true, int index = 0}) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2000),
      lastDate: DateTime(2101),
    );
    if (picked != null) {
      setState(() {
        if (isPenuh) {
          _tanggalPenuh = picked;
        } else {
          _tahapan[index]['tanggal'] = picked;
        }
      });
    }
  }

  Future<void> _submit() async {
    // Basic validation
    if (_metode == 'penuh') {
      if (_jumlahPenuhCtrl.text.isEmpty || _tanggalPenuh == null) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Harap lengkapi jumlah dan tanggal.')));
        return;
      }
    } else {
      if (_tahapan.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Harap tambahkan minimal 1 termin pencairan.')));
        return;
      }
      for (var t in _tahapan) {
        if (t['tanggal'] == null || t['nominal'].text.isEmpty) {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Lengkapi tanggal dan nominal untuk setiap termin.')));
          return;
        }
      }
    }

    final provider = Provider.of<BendaharaProvider>(context, listen: false);

    Map<String, dynamic> payload = {
      'kegiatan_id': widget.kegiatanId,
      'metode': _metode,
      'catatan': _catatanCtrl.text,
    };

    if (_metode == 'penuh') {
      payload['jumlah'] = double.tryParse(_jumlahPenuhCtrl.text);
      payload['tanggal'] = DateFormat('yyyy-MM-dd').format(_tanggalPenuh!);
    } else {
      List<Map<String, dynamic>> tahapanPayload = [];
      for (var t in _tahapan) {
        tahapanPayload.add({
          'tanggal': DateFormat('yyyy-MM-dd').format(t['tanggal']),
          'termin': t['termin'].text,
          'nominal': double.tryParse(t['nominal'].text),
        });
      }
      payload['tahapan'] = tahapanPayload;
    }

    final result = await provider.submitPencairan(payload);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? (result['success'] ? 'Sukses' : 'Gagal')),
          backgroundColor: result['success'] ? AppTheme.accentTeal : Colors.redAccent,
        ),
      );
      if (result['success']) {
        Navigator.pop(context);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<BendaharaProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Proses Pencairan Dana', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
      body: provider.isLoadingPencairan && _kegiatan == null
          ? const Center(child: CircularProgressIndicator())
          : _kegiatan == null
              ? Center(child: Text(provider.errorMessagePencairan))
              : _buildForm(provider),
    );
  }

  Widget _buildForm(BendaharaProvider provider) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Identitas
          Card(
            elevation: 0,
            color: AppTheme.bgLight,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Detail KAK', style: TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryBlue)),
                  const SizedBox(height: 8),
                  Text('Kegiatan: ${_kegiatan!.namaKegiatan}', style: const TextStyle(fontWeight: FontWeight.w500)),
                  Text('Pengusul: ${_kegiatan!.pemilikKegiatan ?? '-'}'),
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),

          const Text('Metode Pencairan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
          Row(
            children: [
              Expanded(
                child: RadioListTile<String>(
                  title: const Text('Penuh'),
                  value: 'penuh',
                  groupValue: _metode,
                  onChanged: (val) => setState(() => _metode = val!),
                  contentPadding: EdgeInsets.zero,
                ),
              ),
              Expanded(
                child: RadioListTile<String>(
                  title: const Text('Bertahap'),
                  value: 'bertahap',
                  groupValue: _metode,
                  onChanged: (val) {
                    setState(() {
                      _metode = val!;
                      if (_tahapan.isEmpty) _addTahapan();
                    });
                  },
                  contentPadding: EdgeInsets.zero,
                ),
              ),
            ],
          ),
          const Divider(),
          const SizedBox(height: 16),

          if (_metode == 'penuh') _buildPenuhForm() else _buildBertahapForm(),

          const SizedBox(height: 24),
          const Text('Catatan (Opsional)', style: TextStyle(fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          TextField(
            controller: _catatanCtrl,
            maxLines: 3,
            decoration: const InputDecoration(
              hintText: 'Tambahkan catatan jika perlu...',
              border: OutlineInputBorder(),
            ),
          ),
          
          const SizedBox(height: 32),
          SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton(
              onPressed: provider.isSubmitting ? null : _submit,
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primaryBlue, foregroundColor: Colors.white),
              child: provider.isSubmitting
                  ? const CircularProgressIndicator(color: Colors.white)
                  : const Text('Simpan Pencairan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            ),
          ),
          const SizedBox(height: 24),
        ],
      ),
    );
  }

  Widget _buildPenuhForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Jumlah Pencairan (Rp)', style: TextStyle(fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),
        TextField(
          controller: _jumlahPenuhCtrl,
          keyboardType: TextInputType.number,
          decoration: const InputDecoration(border: OutlineInputBorder(), prefixText: 'Rp '),
        ),
        const SizedBox(height: 16),
        const Text('Tanggal Pencairan', style: TextStyle(fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),
        InkWell(
          onTap: () => _selectDate(context, isPenuh: true),
          child: InputDecorator(
            decoration: const InputDecoration(border: OutlineInputBorder()),
            child: Text(
              _tanggalPenuh == null ? 'Pilih Tanggal' : DateFormat('dd MMM yyyy').format(_tanggalPenuh!),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildBertahapForm() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text('Tahapan Pencairan', style: TextStyle(fontWeight: FontWeight.bold)),
            TextButton.icon(
              onPressed: _addTahapan,
              icon: const Icon(Icons.add),
              label: const Text('Tambah Termin'),
            ),
          ],
        ),
        const SizedBox(height: 8),
        ..._tahapan.asMap().entries.map((entry) {
          int idx = entry.key;
          Map<String, dynamic> t = entry.value;
          return Card(
            elevation: 0,
            margin: const EdgeInsets.only(bottom: 12),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8), side: const BorderSide(color: AppTheme.borderLight)),
            child: Padding(
              padding: const EdgeInsets.all(12.0),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Termin ${idx + 1}', style: const TextStyle(fontWeight: FontWeight.bold)),
                      if (_tahapan.length > 1)
                        IconButton(icon: const Icon(Icons.delete, color: Colors.red), onPressed: () => _removeTahapan(idx)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: t['termin'],
                    decoration: const InputDecoration(labelText: 'Nama Termin (Misal: Tahap I)', border: OutlineInputBorder()),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: t['nominal'],
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(labelText: 'Nominal (Rp)', border: OutlineInputBorder(), prefixText: 'Rp '),
                  ),
                  const SizedBox(height: 12),
                  InkWell(
                    onTap: () => _selectDate(context, isPenuh: false, index: idx),
                    child: InputDecorator(
                      decoration: const InputDecoration(labelText: 'Tanggal Cair', border: OutlineInputBorder()),
                      child: Text(
                        t['tanggal'] == null ? 'Pilih Tanggal' : DateFormat('dd MMM yyyy').format(t['tanggal']),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }
}
