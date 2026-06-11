import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/bendahara_provider.dart';
import '../../models/kegiatan.dart';
import '../../theme/app_theme.dart';
import 'package:intl/intl.dart';

class TerminForm {
  int terminKe;
  DateTime? tglEstimasi = DateTime.now();
  final TextEditingController nominalController = TextEditingController();
  final TextEditingController catatanController = TextEditingController();

  TerminForm({required this.terminKe});

  void dispose() {
    nominalController.dispose();
    catatanController.dispose();
  }
}

class BendaharaPencairanDetailView extends StatefulWidget {
  final int id;
  final String status;

  const BendaharaPencairanDetailView({super.key, required this.id, required this.status});

  @override
  State<BendaharaPencairanDetailView> createState() => _BendaharaPencairanDetailViewState();
}

class _BendaharaPencairanDetailViewState extends State<BendaharaPencairanDetailView> with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late Animation<double> _fadeAnim;
  late Animation<Offset> _slideAnim;
  
  Kegiatan? _kegiatan;
  bool _isLoading = true;

  bool get _isLunas => _localStatus.toLowerCase() == 'dana diberikan' || _localStatus.toLowerCase() == 'sudah dicairkan' || widget.status.toLowerCase() == 'dana diberikan' || widget.status.toLowerCase() == 'sudah dicairkan';

  double _anggaranDisetujui = 0;
  double _jumlahDicairkan = 0;
  double _sisaDana = 0;
  String _kodeMak = '-';
  String _lpjStatus = 'Belum Ada';

  List<dynamic> _riwayatPencairan = [];
  String _localStatus = '';

  int _tahapanCounter = 1;
  List<TerminForm> _terminForms = [];
  bool _isFullDisbursement = true;

  @override
  void initState() {
    super.initState();
    _terminForms.add(TerminForm(terminKe: _tahapanCounter));
    _localStatus = widget.status;
    _animController = AnimationController(vsync: this, duration: const Duration(milliseconds: 600));
    _fadeAnim = Tween<double>(begin: 0, end: 1).animate(CurvedAnimation(parent: _animController, curve: Curves.easeOut));
    _slideAnim = Tween<Offset>(begin: const Offset(0, 0.1), end: Offset.zero).animate(CurvedAnimation(parent: _animController, curve: Curves.easeOut));
    _animController.forward();
    
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
    });
  }
  
  Future<void> _loadData() async {
      final provider = Provider.of<BendaharaProvider>(context, listen: false);
      final data = await provider.getPencairanDetail(widget.id);
      
      if(mounted) {
          setState((){
              _isLoading = false;
              if (data != null) {
                  _kegiatan = data;
                  _localStatus = _kegiatan!.statusNama ?? widget.status;
                  
                  _anggaranDisetujui = double.tryParse((_kegiatan!.rawData?['dana_di_setujui'] ?? 0).toString()) ?? 0;
                  
                  final progHist = _kegiatan!.rawData?['progress_history'] as List? ?? [];
                  _riwayatPencairan = progHist.where((p) => p['catatan'] != null && (p['catatan'].toString().contains('Termin') || p['catatan'].toString().contains('Cair'))).toList();
                  
                  if (_isLunas) {
                      _jumlahDicairkan = _anggaranDisetujui;
                      _sisaDana = 0;
                  } else {
                      _jumlahDicairkan = 0; 
                      _sisaDana = _anggaranDisetujui - _jumlahDicairkan;
                  }
                  
                  _kodeMak = _kegiatan!.rawData?['bukti_mak'] ?? _kegiatan!.rawData?['kode_mak'] ?? '-';
                  
                  final lpjList = _kegiatan!.rawData?['lpj'] as List? ?? [];
                  if (lpjList.isNotEmpty) {
                      _lpjStatus = lpjList.last['status'] ?? 'Diajukan';
                  }
              }
          });
      }
  }

  @override
  void dispose() {
    for (var form in _terminForms) {
      form.dispose();
    }
    _animController.dispose();
    super.dispose();
  }

  String _formatRupiah(num value) {
    return NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(value);
  }

  String _fmtDateIndo(String? date) {
    if (date == null) return '-';
    try {
        final DateTime d = DateTime.parse(date);
        return DateFormat('dd MMM yyyy', 'id_ID').format(d);
    } catch(_) {
        return date.split('T')[0];
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Detail Usulan & Pencairan', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: AppTheme.textDark),
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : _kegiatan == null 
           ? const Center(child: Text("Gagal memuat data pencairan"))
           : FadeTransition(
                opacity: _fadeAnim,
                child: SlideTransition(
                  position: _slideAnim,
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (_isLunas) _buildLunasAlert(),
                        _buildHeader(),
                        const SizedBox(height: 32),
                        _buildStepper(),
                        const SizedBox(height: 32),
                        
                        _buildTahapanBlock(),
                        const SizedBox(height: 24),
                        _buildInformasiKegiatanBlock(),
                        const SizedBox(height: 24),
                        _buildIkuBlock(),
                        const SizedBox(height: 24),
                        
                        _buildBudgetSummaryCard(),
                        const SizedBox(height: 24),
                        _buildLpjStatusCard(),
                        const SizedBox(height: 24),
                        
                        if (_riwayatPencairan.isNotEmpty) ...[
                          _buildRiwayatPencairan(),
                          const SizedBox(height: 24),
                        ],
                        
                        if (!_isLunas) _buildPanelPencairan() else _buildLunasCard(),
                        const SizedBox(height: 32),
                        
                        _buildRabDetailTable(),
                        const SizedBox(height: 32),
                        _buildKodeMakSection(),
                        const SizedBox(height: 40),
                      ],
                    ),
                  ),
                ),
              ),
    );
  }

  Widget _buildLunasAlert() {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.teal.shade50, borderRadius: BorderRadius.circular(16), border: Border(left: BorderSide(color: Colors.teal.shade500, width: 4))),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.teal.shade100, shape: BoxShape.circle),
            child: const Icon(Icons.check_circle_rounded, color: Colors.teal),
          ),
          const SizedBox(width: 12),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Dana Lunas', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.teal)),
                Text('Seluruh anggaran untuk kegiatan ini telah dicairkan sepenuhnya.', style: TextStyle(fontSize: 12, color: Colors.teal)),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildHeader() {
    final statusColor = _isLunas ? Colors.teal : Colors.blue;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(8), border: Border.all(color: statusColor.withOpacity(0.2))),
              child: Text(_localStatus.toUpperCase(), style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: statusColor, letterSpacing: 1)),
            ),
            const Padding(padding: EdgeInsets.symmetric(horizontal: 8), child: Text('|', style: TextStyle(color: Colors.black26))),
            Text('ID USULAN: #USL-${widget.id.toString().padLeft(5, '0')}', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.black45)),
          ],
        ),
      ],
    );
  }

  Widget _buildStepper() {
    final progress = _isLunas ? 1.0 : 0.5;
    final statusColor = _isLunas ? Colors.teal : Colors.blue;
    
    return Column(
      children: [
        Stack(
          alignment: Alignment.center,
          children: [
            Container(height: 6, width: double.infinity, decoration: BoxDecoration(color: Colors.grey.shade200, borderRadius: BorderRadius.circular(3))),
            Align(
              alignment: Alignment.centerLeft,
              child: AnimatedContainer(
                duration: const Duration(seconds: 1),
                curve: Curves.easeInOut,
                height: 6,
                width: MediaQuery.of(context).size.width * 0.8 * progress,
                decoration: BoxDecoration(color: statusColor, borderRadius: BorderRadius.circular(3)),
              ),
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                _buildStepItem('Pengajuan', true, true, statusColor),
                _buildStepItem('Verifikasi', !_isLunas, true, statusColor),
                _buildStepItem('Selesai', _isLunas, _isLunas, statusColor),
              ],
            )
          ],
        ),
      ],
    );
  }

  Widget _buildStepItem(String title, bool isActive, bool isDone, Color color) {
    return Column(
      children: [
        Container(
          width: 36, height: 36,
          decoration: BoxDecoration(
            color: isDone ? color : Colors.white,
            shape: BoxShape.circle,
            border: Border.all(color: isDone ? color : (isActive ? color : Colors.grey.shade300), width: 3),
            boxShadow: isActive || isDone ? [BoxShadow(color: color.withOpacity(0.3), blurRadius: 8)] : [],
          ),
          child: Icon(isDone ? Icons.check : Icons.circle, size: 16, color: isDone ? Colors.white : (isActive ? color : Colors.transparent)),
        ),
        const SizedBox(height: 8),
        Text(title, style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: isActive || isDone ? AppTheme.textDark : Colors.black38)),
      ],
    );
  }

  Widget _buildCardBase({required String title, required IconData icon, required Color iconColor, required Widget child}) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 5))],
      ),
      child: Stack(
        children: [
          Positioned(left: 0, top: 0, bottom: 0, child: Container(width: 6, decoration: BoxDecoration(color: iconColor, borderRadius: const BorderRadius.only(topLeft: Radius.circular(24), bottomLeft: Radius.circular(24))))),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Icon(icon, color: iconColor, size: 24),
                    const SizedBox(width: 12),
                    Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
                  ],
                ),
                const SizedBox(height: 24),
                child,
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTahapanBlock() {
    final jadwal = _kegiatan!.rawData?['kak']?['tahapans'] as List? ?? _kegiatan!.rawData?['jadwal'] as List? ?? [];
    if(jadwal.isEmpty) return const SizedBox.shrink();

    return _buildCardBase(
      title: 'Pelaksanaan & Keberhasilan',
      icon: Icons.task_alt_rounded,
      iconColor: Colors.blue,
      child: Column(
        children: jadwal.map((entry) {
          String tahap = entry['nama_tahapan'] ?? entry['tahapan_pelaksanaan'] ?? '-';
          return Container(
            margin: const EdgeInsets.only(bottom: 16),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildInfoCol('Tahapan Pelaksanaan', tahap),
              ],
            ),
          );
        }).toList(),
      ),
    );
  }

  Widget _buildInfoCol(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label.toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black38, letterSpacing: 1)),
        const SizedBox(height: 4),
        Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.black87, height: 1.4)),
      ],
    );
  }

  Widget _buildInformasiKegiatanBlock() {
    final kak = _kegiatan!.rawData?['kak'] ?? {};
    return _buildCardBase(
      title: 'Informasi Kegiatan',
      icon: Icons.description_rounded,
      iconColor: Colors.blue,
      child: Column(
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(child: _buildInfoCol('Nama Pengusul', _kegiatan!.pemilikKegiatan ?? '-')),
              Expanded(child: _buildInfoCol('NIM / NIP', _kegiatan!.nimPelaksana ?? '-')),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(child: _buildInfoCol('Jurusan & Prodi', '${_kegiatan!.jurusanPenyelenggara ?? '-'}\n${_kegiatan!.prodiPenyelenggara ?? '-'}')),
              Expanded(child: _buildInfoCol('Penanggung Jawab', '${_kegiatan!.rawData?['nama_pj'] ?? '-'}\nNIP: ${_kegiatan!.rawData?['nip'] ?? '-'}')),
            ],
          ),
          const SizedBox(height: 16),
          _buildInfoCol('Nama Kegiatan', _kegiatan!.namaKegiatan),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('GAMBARAN UMUM', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black38, letterSpacing: 1)),
                const SizedBox(height: 8),
                Text(kak['gambaran_umum'] ?? '-', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w500, color: Colors.black87, height: 1.5)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildIkuBlock() {
    return _buildCardBase(
      title: 'Indikator Kinerja Utama',
      icon: Icons.api_rounded,
      iconColor: Colors.blue,
      child: Column(
        children: [
            Container(
              margin: const EdgeInsets.only(bottom: 12),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
              child: Row(
                children: [
                  Icon(Icons.check_circle_rounded, color: Colors.blue.shade600, size: 20),
                  const SizedBox(width: 12),
                  Expanded(child: Text(_kegiatan!.rawData?['indikator_kinerja'] ?? '-', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Colors.black87))),
                ],
              ),
            )
        ],
      ),
    );
  }

  Widget _buildBudgetSummaryCard() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.blueGrey.shade900,
        borderRadius: BorderRadius.circular(32),
        boxShadow: [BoxShadow(color: Colors.blueGrey.shade900.withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(32),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('ANGGARAN DISETUJUI', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.white.withOpacity(0.6), letterSpacing: 2)),
                  const SizedBox(height: 8),
                  Text(_formatRupiah(_anggaranDisetujui), style: const TextStyle(fontSize: 32, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: -1)),
                ],
              ),
            ),
            Container(
              padding: const EdgeInsets.all(24),
              color: Colors.white.withOpacity(0.05),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('SISA DANA', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.lightBlueAccent, letterSpacing: 2)),
                      const SizedBox(height: 4),
                      Text(_formatRupiah(_sisaDana), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
                    ],
                  ),
                  const Icon(Icons.account_balance_wallet_outlined, color: Colors.lightBlueAccent),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLpjStatusCard() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.description_outlined, color: Colors.blue),
              SizedBox(width: 8),
              Text('STATUS LAPORAN (LPJ)', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
            ],
          ),
          const SizedBox(height: 16),
          Text(_lpjStatus, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
        ],
      ),
    );
  }

  Widget _buildRiwayatPencairan() {
    return _buildCardBase(
      title: 'Riwayat Aksi',
      icon: Icons.history_rounded,
      iconColor: Colors.blue,
      child: Column(
        children: _riwayatPencairan.map((item) {
          return Container(
            margin: const EdgeInsets.only(bottom: 12),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(_fmtDateIndo(item['created_at']), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
                    Text(item['status']?.toString() ?? '', style: const TextStyle(fontSize: 10, color: Colors.blue)),
                  ],
                ),
                const SizedBox(height: 8),
                Text(item['catatan']?.toString() ?? '', style: const TextStyle(fontSize: 12, fontStyle: FontStyle.italic)),
              ],
            ),
          );
        }).toList(),
      ),
    );
  }

  Widget _buildPanelPencairan() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(32), boxShadow: [BoxShadow(color: Colors.blue.shade100.withOpacity(0.5), blurRadius: 20, offset: const Offset(0, 10))], border: Border.all(color: Colors.blue.shade50)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.payments_rounded, color: Colors.blue.shade600),
              const SizedBox(width: 8),
              const Text('PANEL PENCAIRAN', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: 1)),
            ],
          ),
          const SizedBox(height: 24),
          Container(
            padding: const EdgeInsets.all(4),
            decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(16)),
            child: Row(
              children: [
                Expanded(
                  child: InkWell(
                    onTap: () => setState(() => _isFullDisbursement = true),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      decoration: BoxDecoration(
                        color: _isFullDisbursement ? Colors.white : Colors.transparent,
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: _isFullDisbursement ? [const BoxShadow(color: Colors.black12, blurRadius: 4)] : null,
                      ),
                      child: Center(child: Text('Penuh', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: _isFullDisbursement ? Colors.blue : Colors.grey))),
                    ),
                  ),
                ),
                Expanded(
                  child: InkWell(
                    onTap: () => setState(() => _isFullDisbursement = false),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      decoration: BoxDecoration(
                        color: !_isFullDisbursement ? Colors.white : Colors.transparent,
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: !_isFullDisbursement ? [const BoxShadow(color: Colors.black12, blurRadius: 4)] : null,
                      ),
                      child: Center(child: Text('Bertahap', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: !_isFullDisbursement ? Colors.blue : Colors.grey))),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),
          if (_isFullDisbursement) _buildFullDisbursementForm() else ...[
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('TAHAPAN PENCAIRAN', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
                TextButton.icon(
                  onPressed: () => setState(() => _terminForms.add(TerminForm(terminKe: _tahapanCounter + _terminForms.length))),
                  icon: const Icon(Icons.add_rounded, size: 16),
                  label: const Text('Tambah Termin', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                  style: TextButton.styleFrom(foregroundColor: Colors.white, backgroundColor: Colors.blue.shade600, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8)),
                ),
              ],
            ),
            const SizedBox(height: 16),
            ..._terminForms.asMap().entries.map((entry) => _buildTerminForm(entry.key, entry.value)),
          ],
          const SizedBox(height: 32),
          _buildProcessButton(),
        ],
      ),
    );
  }

  Widget _buildFullDisbursementForm() {
     return Column(
       crossAxisAlignment: CrossAxisAlignment.start,
       children: [
         const Text('NOMINAL TOTAL PENCAIRAN', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
         const SizedBox(height: 8),
         TextField(
           controller: TextEditingController(text: _sisaDana.toStringAsFixed(0)),
           readOnly: true,
           decoration: InputDecoration(prefixText: 'Rp ', filled: true, fillColor: Colors.grey.shade50, border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200))),
         ),
       ],
     );
  }

  Widget _buildTerminForm(int idx, TerminForm form) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.blue.shade100)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4), decoration: BoxDecoration(color: Colors.blueGrey.shade900, borderRadius: BorderRadius.circular(8)), child: Text('TAHAPAN ${form.terminKe}', style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.white, letterSpacing: 1))),
              if (_terminForms.length > 1)
                InkWell(
                  onTap: () {
                    setState(() {
                      form.dispose();
                      _terminForms.removeAt(idx);
                      for (int i = 0; i < _terminForms.length; i++) { _terminForms[i].terminKe = _tahapanCounter + i; }
                    });
                  },
                  child: const Icon(Icons.delete_outline_rounded, color: Colors.red, size: 18),
                )
            ],
          ),
          const SizedBox(height: 16),
          TextField(
            controller: form.nominalController,
            keyboardType: TextInputType.number,
            decoration: InputDecoration(prefixText: 'Rp ', labelText: 'Nominal', filled: true, fillColor: Colors.grey.shade50, border: OutlineInputBorder(borderRadius: BorderRadius.circular(12))),
          ),
        ],
      ),
    );
  }

  Widget _buildProcessButton() {
    return Consumer<BendaharaProvider>(
      builder: (context, provider, child) {
        return ElevatedButton(
          onPressed: provider.isSubmitting ? null : () async {
            Map<String, dynamic> payload = {'kegiatan_id': widget.id, 'metode': _isFullDisbursement ? 'penuh' : 'bertahap', 'catatan': 'Pencairan via Mobile'};
            if (_isFullDisbursement) {
              payload['jumlah'] = _sisaDana;
              payload['tanggal'] = DateFormat('yyyy-MM-dd').format(DateTime.now());
            } else {
              List<Map<String, dynamic>> tahapan = [];
              for (var form in _terminForms) {
                final nom = double.tryParse(form.nominalController.text) ?? 0;
                if (nom <= 0) return;
                tahapan.add({'tanggal': DateFormat('yyyy-MM-dd').format(form.tglEstimasi ?? DateTime.now()), 'termin': 'Termin ${form.terminKe}', 'nominal': nom});
              }
              payload['tahapan'] = tahapan;
            }
            final success = await provider.submitPencairan(payload);
            if (mounted && success['success']) { Navigator.pop(context); }
          },
          style: ElevatedButton.styleFrom(backgroundColor: Colors.blue.shade600, foregroundColor: Colors.white, padding: const EdgeInsets.symmetric(vertical: 20), minimumSize: const Size(double.infinity, 50), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20))),
          child: const Text('KONFIRMASI PENCAIRAN'),
        );
      }
    );
  }

  Widget _buildLunasCard() {
    return Container(
      padding: const EdgeInsets.all(32),
      width: double.infinity,
      decoration: BoxDecoration(color: Colors.teal.shade50, borderRadius: BorderRadius.circular(32), border: Border.all(color: Colors.teal.shade100)),
      child: const Column(
        children: [
          Icon(Icons.check_circle_rounded, color: Colors.teal, size: 48),
          SizedBox(height: 16),
          Text('ANGGARAN LUNAS', style: TextStyle(fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildRabDetailTable() {
    final rab = _kegiatan!.rawData?['kak']?['rabs'] as List? ?? [];
    if (rab.isEmpty) return const SizedBox.shrink();
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Rincian Anggaran Detail', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        const SizedBox(height: 16),
        ...rab.map((item) => ListTile(title: Text(item['uraian'] ?? ''), subtitle: Text(_formatRupiah(double.tryParse(item['harga']?.toString() ?? '0') ?? 0)))),
      ],
    );
  }

  Widget _buildKodeMakSection() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(32)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('KODE MAK', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
          Text(_kodeMak, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }
}
