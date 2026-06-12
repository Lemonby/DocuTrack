import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:flutter/foundation.dart';
import 'package:file_picker/file_picker.dart';
import '../../theme/app_theme.dart';
import '../../models/kegiatan.dart';
import '../../providers/usulan_provider.dart';

class AdminKegiatanDetailView extends StatefulWidget {
  final Kegiatan kegiatan;
  const AdminKegiatanDetailView({super.key, required this.kegiatan});

  @override
  State<AdminKegiatanDetailView> createState() => _AdminKegiatanDetailViewState();
}

class _AdminKegiatanDetailViewState extends State<AdminKegiatanDetailView> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _pjController;
  late TextEditingController _nimPjController;
  late TextEditingController _tglMulaiController;
  late TextEditingController _tglSelesaiController;
  
  PlatformFile? _selectedFile;
  DateTime? _startDate;
  DateTime? _endDate;

  bool _isLoadingDetail = true;
  Kegiatan? _kegiatan;

  @override
  void initState() {
    super.initState();
    _pjController = TextEditingController();
    _nimPjController = TextEditingController();
    _tglMulaiController = TextEditingController();
    _tglSelesaiController = TextEditingController();
    
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadDetail());
  }

  Future<void> _loadDetail() async {
    final provider = context.read<UsulanProvider>();
    final data = await provider.getKegiatanDetail(widget.kegiatan.id);
    
    if (mounted) {
      setState(() {
        _kegiatan = data ?? widget.kegiatan;
        _isLoadingDetail = false;
        
        _pjController.text = _kegiatan!.rawData?['nama_pj'] ?? _kegiatan!.rawData?['penanggung_jawab'] ?? '';
        _nimPjController.text = _kegiatan!.rawData?['nip'] ?? _kegiatan!.rawData?['nim_nip_pj'] ?? '';
        _tglMulaiController.text = _kegiatan!.tanggalMulai ?? '';
        _tglSelesaiController.text = _kegiatan!.tanggalSelesai ?? '';

        if (_kegiatan!.tanggalMulai != null) {
          _startDate = DateTime.tryParse(_kegiatan!.tanggalMulai!);
        }
        if (_kegiatan!.tanggalSelesai != null) {
          _endDate = DateTime.tryParse(_kegiatan!.tanggalSelesai!);
        }
      });
    }
  }

  @override
  void dispose() {
    _pjController.dispose();
    _nimPjController.dispose();
    _tglMulaiController.dispose();
    _tglSelesaiController.dispose();
    super.dispose();
  }

  Future<void> _pickFile() async {
    final result = await FilePicker.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf', 'doc', 'docx'],
      withData: kIsWeb,
    );

    if (result != null) {
      setState(() {
        _selectedFile = result.files.single;
      });
    }
  }

  Future<void> _selectDate(BuildContext context, bool isStart) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: (isStart ? _startDate : _endDate) ?? DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime(2030),
    );

    if (picked != null) {
      setState(() {
        if (isStart) {
          _startDate = picked;
          _tglMulaiController.text = DateFormat('yyyy-MM-dd').format(picked);
        } else {
          _endDate = picked;
          _tglSelesaiController.text = DateFormat('yyyy-MM-dd').format(picked);
        }
      });
    }
  }

  void _submit() async {
    if (!_formKey.currentState!.validate()) return;
    
    final provider = context.read<UsulanProvider>();
    
    final data = {
      'penanggung_jawab': _pjController.text,
      'nim_nip_pj': _nimPjController.text,
      'tanggal_mulai': _tglMulaiController.text,
      'tanggal_selesai': _tglSelesaiController.text,
    };

    final success = await provider.submitRincian(widget.kegiatan.id, data, _selectedFile);

    if (mounted) {
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Rincian kegiatan berhasil disubmit'), backgroundColor: Colors.green),
        );
        Navigator.pop(context);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(provider.errorMessage), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoadingDetail) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    
    final curKegiatan = _kegiatan ?? widget.kegiatan;
    final status = curKegiatan.statusNama ?? curKegiatan.status?.nama ?? 'Proses';
    final statusLower = status.toLowerCase();
    final isEditable = (statusLower == 'telah diverifikasi' || statusLower == 'disetujui' || statusLower == 'revisi');
    final isReadonly = !isEditable;

    Color statusColor = Colors.blueGrey;
    IconData statusIcon = Icons.hourglass_empty;
    if (statusLower == 'revisi') { statusColor = Colors.orange; statusIcon = Icons.warning_amber_rounded; }
    if (statusLower == 'review' || statusLower == 'telah diverifikasi') { statusColor = Colors.blue; statusIcon = Icons.search; }
    if (statusLower == 'disetujui' || statusLower == 'selesai') { statusColor = Colors.green; statusIcon = Icons.check_circle_outline; }
    if (statusLower == 'ditolak') { statusColor = Colors.red; statusIcon = Icons.cancel_outlined; }

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(!isReadonly ? 'Lengkapi Kegiatan' : 'Detail Kegiatan', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            Text('ID: #${curKegiatan.id}', style: const TextStyle(fontSize: 10, color: Colors.blue)),
          ],
        ),
        backgroundColor: Colors.white,
        foregroundColor: AppTheme.textDark,
        elevation: 1,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (status == 'Revisi')
                _buildRevisiAlert(),

              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 5))],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildStatusHeader(statusColor, statusIcon, status, curKegiatan),
                    
                    Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildSectionHeader('Info Dasar', Icons.info_outline),
                          const SizedBox(height: 12),
                          _buildReadOnlyField('Nama Kegiatan', curKegiatan.namaKegiatan),
                          const SizedBox(height: 12),
                          _buildReadOnlyField('Pengusul', curKegiatan.pemilikKegiatan ?? '-'),

                          const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider(height: 1)),

                          _buildSectionHeader('Penanggung Jawab Kegiatan', Icons.person_pin),
                          const SizedBox(height: 16),
                          if (!isReadonly) ...[
                            _buildInputField('Nama Lengkap PJ *', _pjController, 'Masukkan nama lengkap', (v) => v!.isEmpty ? 'Wajib diisi' : null),
                            const SizedBox(height: 16),
                            _buildInputField('NIP PJ *', _nimPjController, 'Masukkan NIP', (v) => v!.isEmpty ? 'Wajib diisi' : null),
                          ] else ...[
                            _buildReadOnlyField('Nama Lengkap PJ', curKegiatan.rawData?['nama_pj'] ?? curKegiatan.rawData?['penanggung_jawab'] ?? '-'),
                            const SizedBox(height: 12),
                            _buildReadOnlyField('NIP PJ', curKegiatan.rawData?['nip'] ?? curKegiatan.rawData?['nim_nip_pj'] ?? '-'),
                          ],

                          const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider(height: 1)),

                          _buildSectionHeader('Waktu Pelaksanaan', Icons.calendar_month),
                          const SizedBox(height: 16),
                          if (!isReadonly) ...[
                            Row(
                              children: [
                                Expanded(child: _buildDateField('Mulai *', _tglMulaiController, () => _selectDate(context, true))),
                                const SizedBox(width: 12),
                                Expanded(child: _buildDateField('Selesai *', _tglSelesaiController, () => _selectDate(context, false))),
                              ],
                            )
                          ] else ...[
                            Row(
                              children: [
                                Expanded(child: _buildReadOnlyField('Mulai', curKegiatan.tanggalMulai ?? '-', icon: Icons.event)),
                                const Padding(padding: EdgeInsets.symmetric(horizontal: 12), child: Icon(Icons.arrow_forward_rounded, color: Colors.grey, size: 16)),
                                Expanded(child: _buildReadOnlyField('Selesai', curKegiatan.tanggalSelesai ?? '-', icon: Icons.event)),
                              ],
                            ),
                          ],

                          const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider(height: 1)),

                          _buildSectionHeader('Dokumen Pendukung', Icons.file_present_rounded),
                          const SizedBox(height: 16),
                          if (!isReadonly) ...[
                            if (_selectedFile != null)
                              ListTile(
                                leading: const Icon(Icons.file_present),
                                title: Text(_selectedFile!.name),
                                trailing: IconButton(
                                  icon: const Icon(Icons.close, color: Colors.red),
                                  onPressed: () => setState(() => _selectedFile = null),
                                ),
                              )
                            else
                              _buildFilePicker(),
                          ] else ...[
                            _buildFileDisplay(curKegiatan),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 24),
              _buildActionButton(isReadonly),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildRevisiAlert() {
    return Container(
      padding: const EdgeInsets.all(16),
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: [Colors.orange.shade50, Colors.orange.shade100]),
        border: Border.all(color: Colors.orange.shade200),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Row(
        children: [
          Icon(Icons.warning_amber_rounded, color: Colors.orange.shade700, size: 28),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Perlu Perbaikan Data', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.orange.shade900)),
                const SizedBox(height: 4),
                Text(_kegiatan?.rawData?['revisi_comment'] ?? 'Mohon periksa kembali data penanggung jawab atau dokumen pendukung.', style: TextStyle(fontSize: 12, color: Colors.orange.shade800)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusHeader(Color statusColor, IconData statusIcon, String status, Kegiatan curKegiatan) {
    return Container(
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
          Text(curKegiatan.jurusanPenyelenggara ?? '-', style: const TextStyle(color: AppTheme.textMuted, fontSize: 10, fontWeight: FontWeight.bold)),
        ],
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
            Expanded(child: Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppTheme.textDark))),
          ],
        ),
      ],
    );
  }

  Widget _buildInputField(String label, TextEditingController controller, String hint, String? Function(String?)? validator) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
        const SizedBox(height: 8),
        TextFormField(
          controller: controller,
          validator: validator,
          decoration: InputDecoration(
            hintText: hint,
            filled: true,
            fillColor: Colors.grey.shade50,
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade300)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Colors.blue, width: 2)),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          ),
        ),
      ],
    );
  }

  Widget _buildDateField(String label, TextEditingController controller, VoidCallback onTap) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
        const SizedBox(height: 8),
        TextFormField(
          controller: controller,
          readOnly: true,
          onTap: onTap,
          validator: (v) => (v == null || v.isEmpty) ? 'Wajib diisi' : null,
          decoration: InputDecoration(
            prefixIcon: const Icon(Icons.calendar_today, size: 18),
            filled: true,
            fillColor: Colors.grey.shade50,
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade300)),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          ),
        ),
      ],
    );
  }

  Widget _buildFilePicker() {
    return Column(
      children: [
        InkWell(
          onTap: _pickFile,
          child: Container(
            width: double.infinity,
            padding: const EdgeInsets.all(32),
            decoration: BoxDecoration(
              border: Border.all(color: Colors.blue.shade200, width: 2, style: BorderStyle.none),
              borderRadius: BorderRadius.circular(16),
              color: Colors.blue.shade50,
            ),
            child: Column(
              children: [
                Icon(Icons.cloud_upload_outlined, size: 36, color: Colors.blue.shade600),
                const SizedBox(height: 12),
                Text(_selectedFile?.name ?? 'Unggah Surat Pengantar / Undangan', 
                    style: TextStyle(fontWeight: FontWeight.bold, color: Colors.blue.shade700), textAlign: TextAlign.center),
                const SizedBox(height: 4),
                const Text('Format: PDF, DOC (Maks. 10MB)', style: TextStyle(fontSize: 11, color: Colors.blueGrey)),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildFileDisplay(Kegiatan curKegiatan) {
    final fileName = curKegiatan.rawData?['surat_pengantar']?.toString().split('/').last ?? 'TIDAK_ADA_FILE.PDF';
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey.shade50,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Icon(Icons.picture_as_pdf_rounded, size: 30, color: Colors.red.shade400),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Surat Pengantar', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                Text(fileName, style: const TextStyle(fontSize: 10, color: AppTheme.textMuted)),
              ],
            ),
          ),
          IconButton(
            onPressed: () {},
            icon: const Icon(Icons.download),
            color: Colors.blue.shade700,
          )
        ],
      ),
    );
  }

  Widget _buildActionButton(bool isReadonly) {
    final isLoading = context.watch<UsulanProvider>().isLoading;
    
    return SizedBox(
      width: double.infinity,
      child: ElevatedButton.icon(
        onPressed: (isReadonly || isLoading) ? () => Navigator.pop(context) : _submit,
        icon: Icon(isReadonly ? Icons.arrow_back : Icons.send_rounded),
        label: isLoading 
            ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
            : Text(isReadonly ? 'KEMBALI KE LIST' : 'SIMPAN & KIRIM', style: const TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1)),
        style: ElevatedButton.styleFrom(
          backgroundColor: isReadonly ? Colors.grey.shade800 : Colors.blue.shade700,
          foregroundColor: Colors.white,
          padding: const EdgeInsets.symmetric(vertical: 20),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        ),
      ),
    );
  }
}
