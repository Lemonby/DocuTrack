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
                  
                  // Setup initial figures
                  _anggaranDisetujui = double.tryParse((_kegiatan!.rawData?['dana_disetujui'] ?? 0).toString()) ?? 0;
                  
                  final progHist = _kegiatan!.rawData?['progress_history'] as List? ?? [];
                  
                  _riwayatPencairan = progHist.where((p) => p['catatan'] != null && (p['catatan'].toString().contains('Termin') || p['catatan'].toString().contains('Cair'))).toList();
                  
                  // Simple heuristic to sum dicairkan from riwayat records in json
                  for(var _ in _riwayatPencairan) {
                       // Typically bendahara saves the nominal inside JSON.
                       // For safety we rely on sisa dana calculation
                  }
                  
                  // As a simple demo of logic, if it's lunas we set dicairkan = anggaran
                  if (_isLunas) {
                      _jumlahDicairkan = _anggaranDisetujui;
                      _sisaDana = 0;
                  } else {
                      _jumlahDicairkan = 0; // Adjust logic based on your real JSON structure
                      _sisaDana = _anggaranDisetujui - _jumlahDicairkan;
                  }
                  
                  _kodeMak = _kegiatan!.rawData?['kode_mak'] ?? '-';
                  
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
        actions: [
          IconButton(icon: const Icon(Icons.print_rounded, color: Colors.blueGrey), onPressed: () {}),
        ],
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
                        
                        // KAK Data Blocks
                        _buildTahapanBlock(),
                        const SizedBox(height: 24),
                        _buildInformasiKegiatanBlock(),
                        const SizedBox(height: 24),
                        _buildIkuBlock(),
                        const SizedBox(height: 24),
                        
                        // Budget & Action Cards
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
            child: Icon(Icons.check_circle_rounded, color: Colors.teal.shade700),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Dana Lunas', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.teal.shade800)),
                Text('Seluruh anggaran untuk kegiatan ini telah dicairkan sepenuhnya.', style: TextStyle(fontSize: 12, color: Colors.teal.shade700)),
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
              decoration: BoxDecoration(color: statusColor.shade50, borderRadius: BorderRadius.circular(8), border: Border.all(color: statusColor.shade200)),
              child: Text(_localStatus.toUpperCase(), style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: statusColor.shade700, letterSpacing: 1)),
            ),
            const Padding(padding: EdgeInsets.symmetric(horizontal: 8), child: Text('|', style: TextStyle(color: Colors.black26))),
            Text('ID USULAN: #USL-${widget.id.toString().padLeft(5, '0')}', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.black45)),
          ],
        ),
      ],
    );
  }

  Widget _buildStepper() {
    final s = _localStatus.toLowerCase();
    final progress = _isLunas ? 1.0 : (s == 'menunggu' ? 0.5 : 0.66);
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

  Widget _buildStepItem(String title, bool isActive, bool isDone, MaterialColor color) {
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
    final jadwal = _kegiatan!.rawData?['jadwal'] as List? ?? [];
    if(jadwal.isEmpty) return const SizedBox.shrink();

    return _buildCardBase(
      title: 'Pelaksanaan & Keberhasilan',
      icon: Icons.task_alt_rounded,
      iconColor: Colors.blue,
      child: Column(
        children: jadwal.map((entry) {
          String tahap = entry['tahapan_pelaksanaan'] ?? '-';
          String ind = entry['indikator_keberhasilan'] ?? '-';
          String bulan = entry['bulan'] ?? '-';
          
          return Container(
            margin: const EdgeInsets.only(bottom: 16),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.grey.shade200)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(color: Colors.blue.shade100, borderRadius: BorderRadius.circular(8)),
                  child: Text('BULAN ${bulan.toUpperCase()}', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blue.shade800, letterSpacing: 1)),
                ),
                const SizedBox(height: 16),
                _buildInfoCol('Tahapan Pelaksanaan', tahap),
                const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider(height: 1, color: Colors.black12)),
                _buildInfoCol('Indikator Keberhasilan', ind),
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
              Expanded(child: _buildInfoCol('Penanggung Jawab', '${_kegiatan!.rawData?['nama_penanggung_jawab'] ?? '-'}\nNIP: ${_kegiatan!.rawData?['nip_penanggung_jawab'] ?? '-'}')),
            ],
          ),
          const SizedBox(height: 16),
          _buildInfoCol('Waktu Pelaksanaan', '${_fmtDateIndo(_kegiatan!.rawData?['tanggal_mulai'])} - ${_fmtDateIndo(_kegiatan!.rawData?['tanggal_selesai'])}'),
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
                const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider(height: 1, color: Colors.black12)),
                const Text('METODE PELAKSANAAN', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black38, letterSpacing: 1)),
                const SizedBox(height: 8),
                Text(kak['metode_pelaksanaan'] ?? '-', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w500, color: Colors.black87, height: 1.5)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildIkuBlock() {
    final kak = _kegiatan!.rawData?['kak'] ?? {};
    final iku = kak['iku'] ?? '-';
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
                  Expanded(child: Text(iku, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Colors.black87))),
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
        child: Stack(
          children: [
            Positioned(right: -50, top: -50, child: Container(width: 150, height: 150, decoration: BoxDecoration(color: Colors.white.withOpacity(0.05), shape: BoxShape.circle))),
            Column(
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
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('DICAIRKAN', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.tealAccent, letterSpacing: 2)),
                              const SizedBox(height: 4),
                              Text(_formatRupiah(_jumlahDicairkan), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white)),
                            ],
                          ),
                          Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: Colors.tealAccent.withOpacity(0.2), borderRadius: BorderRadius.circular(16)), child: const Icon(Icons.check_circle_outline, color: Colors.tealAccent)),
                        ],
                      ),
                      const SizedBox(height: 20),
                      Row(
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
                          Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: Colors.lightBlueAccent.withOpacity(0.2), borderRadius: BorderRadius.circular(16)), child: const Icon(Icons.account_balance_wallet_outlined, color: Colors.lightBlueAccent)),
                        ],
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                  color: Colors.black26,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          const Icon(Icons.fingerprint_rounded, color: Colors.lightBlueAccent, size: 16),
                          const SizedBox(width: 8),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('KODE MAK', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.white54, letterSpacing: 1)),
                              Text(_kodeMak, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white, fontFamily: 'monospace')),
                            ],
                          ),
                        ],
                      ),
                      Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4), decoration: BoxDecoration(color: Colors.tealAccent.withOpacity(0.1), borderRadius: BorderRadius.circular(8), border: Border.all(color: Colors.tealAccent.withOpacity(0.3))), child: const Row(children: [Icon(Icons.check_circle, size: 10, color: Colors.tealAccent), SizedBox(width: 4), Text('TERVERIFIKASI', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.tealAccent, letterSpacing: 1))])),
                    ],
                  ),
                )
              ],
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
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.blue.shade100)),
            child: Row(
              children: [
                Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: Colors.blue.shade100, borderRadius: BorderRadius.circular(10)), child: Icon(Icons.timer_outlined, color: Colors.blue.shade700)),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('PROGRESS LPJ', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.blue.shade700, letterSpacing: 1)),
                    Text(_lpjStatus, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
                  ],
                ),
              ],
            ),
          ),
          if (_lpjStatus == 'Belum Ada' || _lpjStatus == 'Diajukan') ...[
            const SizedBox(height: 12),
            Row(
              children: [
                Icon(Icons.warning_amber_rounded, color: Colors.orange.shade500, size: 14),
                const SizedBox(width: 4),
                Text('LPJ belum dikonfirmasi.', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.orange.shade700)),
              ],
            )
          ]
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
                    Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2), decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(4)), child: Text(item['status'] ?? '', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blue.shade600))),
                  ],
                ),
                const SizedBox(height: 8),
                if (item['catatan'] != null && item['catatan'].toString().isNotEmpty) ...[
                  const SizedBox(height: 8),
                  Text(item['catatan'].toString(), style: const TextStyle(fontSize: 12, fontStyle: FontStyle.italic, color: Colors.black87)),
                ]
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
          
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('TAHAPAN PENCAIRAN', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
              TextButton.icon(
                onPressed: () {
                  setState(() {
                    _terminForms.add(TerminForm(terminKe: _tahapanCounter + _terminForms.length));
                  });
                },
                icon: const Icon(Icons.add_rounded, size: 16),
                label: const Text('Tambah Termin', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                style: TextButton.styleFrom(foregroundColor: Colors.white, backgroundColor: Colors.blue.shade600, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8)),
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          ..._terminForms.asMap().entries.map((entry) {
            final int idx = entry.key;
            final TerminForm form = entry.value;

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
                              // Recalculate terminKe
                              for (int i = 0; i < _terminForms.length; i++) {
                                _terminForms[i].terminKe = _tahapanCounter + i;
                              }
                            });
                          },
                          child: Icon(Icons.delete_outline_rounded, color: Colors.red.shade300, size: 18),
                        )
                    ],
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text('TGL ESTIMASI', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
                            const SizedBox(height: 8),
                            GestureDetector(
                              onTap: () async {
                                final selected = await showDatePicker(
                                  context: context,
                                  initialDate: form.tglEstimasi ?? DateTime.now(),
                                  firstDate: DateTime(2020),
                                  lastDate: DateTime(2030),
                                );
                                if (selected != null) {
                                  setState(() {
                                    form.tglEstimasi = selected;
                                  });
                                }
                              },
                              child: Container(
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade200)),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(_fmtDateIndo(form.tglEstimasi?.toIso8601String()), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                                    const Icon(Icons.calendar_month, size: 14, color: Colors.black38),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text('KETERANGAN TERMIN', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
                            const SizedBox(height: 8),
                            Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade200)), child: Text('Termin ${form.terminKe}', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold))),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  const Text('NOMINAL PENCAIRAN', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
                  const SizedBox(height: 8),
                  TextField(
                    controller: form.nominalController,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(
                      prefixText: 'Rp ',
                      filled: true,
                      fillColor: Colors.grey.shade50,
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
                      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
                      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.blue.shade200)),
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text('CATATAN TERMIN', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
                  const SizedBox(height: 8),
                  TextField(
                    controller: form.catatanController,
                    maxLines: 2,
                    decoration: InputDecoration(
                      filled: true,
                      fillColor: Colors.grey.shade50,
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
                      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
                      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.blue.shade200)),
                    ),
                  ),
                ],
              ),
            );
          }),

          const SizedBox(height: 32),
          Consumer<BendaharaProvider>(
            builder: (context, provider, child) {
              return ElevatedButton(
                onPressed: provider.isSubmitting ? null : () async {
                  double totalDiajukan = 0;
                  List<Map<String, dynamic>> toAdd = [];
                  String catatans = '';
                  
                  for (var form in _terminForms) {
                    final nom = double.tryParse(form.nominalController.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
                    if (nom <= 0) {
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Masukkan nominal yang valid pada Termin ${form.terminKe}')));
                      return;
                    }
                    totalDiajukan += nom;
                    toAdd.add({
                      'tanggal_pencairan': form.tglEstimasi?.toIso8601String() ?? DateTime.now().toIso8601String(),
                      'termin': 'Termin ${form.terminKe}',
                      'nominal': nom,
                      'catatan': form.catatanController.text,
                    });
                    catatans += 'Termin ${form.terminKe}: $nom - ${form.catatanController.text} | ';
                  }

                  if (totalDiajukan > _sisaDana) {
                    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Total nominal seluruh termin melebihi sisa dana')));
                    return;
                  }
                  
                  final success = await provider.submitPencairan({
                      'kegiatan_id': widget.id,
                      'catatan': catatans
                  });
                  
                  if (!mounted) return;

                  if (success['success']) {
                      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Semua pencairan berhasil dicatat!')));
                      Navigator.pop(context);
                  } else {
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(success['message'] ?? 'Gagal memproses')));
                  }
                },
                style: ElevatedButton.styleFrom(backgroundColor: Colors.blue.shade600, foregroundColor: Colors.white, padding: const EdgeInsets.symmetric(vertical: 20), minimumSize: const Size(double.infinity, 50), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)), elevation: 10, shadowColor: Colors.blue.shade200),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    if(provider.isSubmitting) const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) else const SizedBox.shrink(),
                    const SizedBox(width: 12),
                    const Text('PROSES PENCAIRAN DANA', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, letterSpacing: 1)),
                    const SizedBox(width: 12),
                    const Icon(Icons.send_rounded, size: 16),
                  ],
                ),
              );
            }
          )
        ],
      ),
    );
  }

  Widget _buildLunasCard() {
    return Container(
      padding: const EdgeInsets.all(32),
      width: double.infinity,
      decoration: BoxDecoration(color: Colors.teal.shade50, borderRadius: BorderRadius.circular(32), border: Border.all(color: Colors.teal.shade100)),
      child: Column(
        children: [
          Container(padding: const EdgeInsets.all(20), decoration: BoxDecoration(color: Colors.teal.shade100, shape: BoxShape.circle), child: Icon(Icons.check_circle_rounded, color: Colors.teal.shade600, size: 48)),
          const SizedBox(height: 24),
          Text('ANGGARAN LUNAS', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: Colors.teal.shade800, letterSpacing: 2)),
          const SizedBox(height: 8),
          Text('Seluruh dana telah dicairkan.', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.teal.shade600)),
        ],
      ),
    );
  }

  Widget _buildRabDetailTable() {
    final rab = _kegiatan!.rawData?['rab'] as List? ?? [];
    if (rab.isEmpty) return const SizedBox.shrink();

    // Grouping manually since JSON might not be grouped
    final Map<String, List<dynamic>> grouped = {};
    for (var r in rab) {
        // Just put them in one list for simplicity if we don't have categories from DB.
        grouped.putIfAbsent('Belanja', () => []).add(r);
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Row(
          children: [
            Icon(Icons.table_chart_rounded, color: Colors.blue),
            SizedBox(width: 12),
            Text('Rincian Anggaran Detail', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
          ],
        ),
        const SizedBox(height: 24),
        ...grouped.entries.map((entry) {
          String kategori = entry.key;
          List items = entry.value;
          return Padding(
            padding: const EdgeInsets.only(bottom: 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(width: 12, height: 12, decoration: BoxDecoration(color: Colors.blue, borderRadius: BorderRadius.circular(4))),
                    const SizedBox(width: 8),
                    Text(kategori.toUpperCase(), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.black87, letterSpacing: 1)),
                  ],
                ),
                const SizedBox(height: 16),
                Column(
                  children: items.map((item) {
                    double hrg = item['harga_satuan'] != null ? double.parse(item['harga_satuan'].toString()) : 0;
                    double v1 = item['volume_1'] != null ? double.parse(item['volume_1'].toString()) : 1;
                    double v2 = item['volume_2'] != null ? double.parse(item['volume_2'].toString()) : 1;
                    final total = hrg * v1 * v2;
                    return Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: Colors.grey.shade200),
                        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 5))],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Expanded(
                                flex: 3,
                                child: Text(item['uraian'] ?? '', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                              ),
                              Expanded(
                                flex: 2,
                                child: Text(_formatRupiah(total), textAlign: TextAlign.right, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: Colors.blue)),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Row(
                            children: [
                              Container(padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2), decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(4)), child: Text(item['rincian'] ?? '-', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.black54))),
                              const SizedBox(width: 8),
                              Text('$v1 ${item['satuan_1']}' + (v2 > 1 ? ' x $v2 ${item['satuan_2']}' : ''), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black45)),
                            ],
                          ),
                          const SizedBox(height: 4),
                          Text('Harga Satuan: ${_formatRupiah(hrg)}', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.black45)),
                        ],
                      ),
                    );
                  }).toList(),
                ),
              ],
            ),
          );
        }).toList()
      ],
    );
  }

  Widget _buildKodeMakSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Row(
          children: [
            Icon(Icons.fingerprint_rounded, color: Colors.blue),
            SizedBox(width: 12),
            Text('Kode Mata Anggaran Kegiatan (MAK)', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
          ],
        ),
        const SizedBox(height: 24),
        Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(32), border: Border.all(color: Colors.blue.shade200, width: 2)),
          child: Row(
            children: [
              Container(padding: const EdgeInsets.all(16), decoration: BoxDecoration(color: Colors.blue.shade100, borderRadius: BorderRadius.circular(20)), child: Icon(Icons.key_rounded, color: Colors.blue.shade600, size: 32)),
              const SizedBox(width: 24),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('KODE ANGGARAN TERVERIFIKASI', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blue.shade700, letterSpacing: 2)),
                    const SizedBox(height: 8),
                    Text(_kodeMak, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppTheme.textDark, fontFamily: 'monospace', letterSpacing: 2)),
                  ],
                ),
              ),
              Container(padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8), decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.blue.shade200)), child: Row(children: [Icon(Icons.check_circle, size: 14, color: Colors.blue.shade600), const SizedBox(width: 6), Text('AKTIF', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.blue.shade700, letterSpacing: 1))])),
            ],
          ),
        ),
      ],
    );
  }
}
