import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/bendahara_provider.dart';
import '../../theme/app_theme.dart';
import 'package:intl/intl.dart';
import '../../models/lpj.dart';

class BendaharaLpjDetailView extends StatefulWidget {
  final int id;
  final String status;

  const BendaharaLpjDetailView({super.key, required this.id, required this.status});

  @override
  State<BendaharaLpjDetailView> createState() => _BendaharaLpjDetailViewState();
}

class _BendaharaLpjDetailViewState extends State<BendaharaLpjDetailView> with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late Animation<double> _fadeAnim;
  late Animation<Offset> _slideAnim;

  // Track expanded feedback cards
  final Set<String> _expandedFeedbacks = {};
  
  Lpj? _lpj;
  bool _isLoading = true;
  String _localStatus = '';

  final TextEditingController _globalCatatanController = TextEditingController();

  bool get _isActionable => _localStatus.toLowerCase() == 'menunggu verifikasi' || _localStatus.toLowerCase() == 'telah direvisi' || widget.status.toLowerCase() == 'menunggu verifikasi' || widget.status.toLowerCase() == 'telah direvisi';

  @override
  void initState() {
    super.initState();
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
    final data = await provider.getLpjDetail(widget.id);
    
    if (mounted) {
      setState(() {
         _isLoading = false;
         if (data != null) {
            _lpj = data;
            _localStatus = _lpj!.statusNama ?? widget.status;
         }
      });
    }
  }

  @override
  void dispose() {
    _animController.dispose();
    _globalCatatanController.dispose();
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

  MaterialColor _getStatusColor() {
    final s = _localStatus.toLowerCase();
    if (s == 'disetujui' || s == 'selesai') return Colors.teal;
    if (s == 'revisi') return Colors.amber;
    if (s == 'telah direvisi') return Colors.indigo;
    return Colors.blue;
  }

  @override
  Widget build(BuildContext context) {
    final statusColor = _getStatusColor();
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Validasi Pertanggungjawaban', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: AppTheme.textDark),
      ),
      body: _isLoading 
         ? const Center(child: CircularProgressIndicator())
         : _lpj == null
           ? const Center(child: Text("Gagal memuat detail LPJ."))
           : FadeTransition(
        opacity: _fadeAnim,
        child: SlideTransition(
          position: _slideAnim,
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildStatusAlert(),
                _buildHeader(statusColor),
                const SizedBox(height: 32),
                _buildStepper(statusColor),
                const SizedBox(height: 32),
                _buildBasicInfo(),
                const SizedBox(height: 40),
                _buildTableLpj(),
                const SizedBox(height: 40),
                _buildSummaryDashboard(),
                const SizedBox(height: 40),
                
                if (_isActionable) _buildPanelVerifikasi() else _buildLockedPanel(),
                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildStatusAlert() {
    final s = _localStatus.toLowerCase();
    if (s == 'revisi') {
      return Container(
        margin: const EdgeInsets.only(bottom: 24),
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(color: Colors.amber.shade50, borderRadius: const BorderRadius.horizontal(right: Radius.circular(24), left: Radius.circular(8)), border: Border(left: BorderSide(color: Colors.amber.shade500, width: 4))),
        child: Row(
          children: [
            Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: Colors.amber.shade100, borderRadius: BorderRadius.circular(16)), child: Icon(Icons.history_rounded, color: Colors.amber.shade700)),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('MENUNGGU HASIL REVISI', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.amber.shade800, letterSpacing: 1)),
                  const SizedBox(height: 4),
                  Text('LPJ ini sedang dalam proses perbaikan oleh Admin berdasarkan catatan Anda.', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.amber.shade700)),
                ],
              ),
            ),
          ],
        ),
      );
    } else if (s == 'telah direvisi') {
      return Container(
        margin: const EdgeInsets.only(bottom: 24),
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(color: Colors.indigo.shade50, borderRadius: const BorderRadius.horizontal(right: Radius.circular(24), left: Radius.circular(8)), border: Border(left: BorderSide(color: Colors.indigo.shade500, width: 4))),
        child: Row(
          children: [
            Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: Colors.indigo.shade100, borderRadius: BorderRadius.circular(16)), child: Icon(Icons.done_all, color: Colors.indigo.shade700)),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('LPJ TELAH DIREVISI', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.indigo.shade800, letterSpacing: 1)),
                  const SizedBox(height: 4),
                  Text('Laporan telah diperbarui. Silakan tinjau kembali rincian bukti sebelum memberikan keputusan final.', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.indigo.shade700)),
                ],
              ),
            ),
          ],
        ),
      );
    }
    return const SizedBox.shrink();
  }

  Widget _buildHeader(MaterialColor statusColor) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Container(padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6), decoration: BoxDecoration(color: statusColor.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: statusColor.shade200)), child: Text(_localStatus.toUpperCase(), style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: statusColor.shade700, letterSpacing: 1))),
            const Padding(padding: EdgeInsets.symmetric(horizontal: 12), child: Text('|', style: TextStyle(color: Colors.black26))),
            Text('KODE LPJ: #${widget.id.toString().padLeft(5, '0')}', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.black38, letterSpacing: 1)),
          ],
        ),
        const SizedBox(height: 16),
        Text('Kegiatan: ${_lpj!.kegiatan?.namaKegiatan ?? '-'}', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.blue, decoration: TextDecoration.underline)),
      ],
    );
  }

  Widget _buildStepper(MaterialColor statusColor) {
    final s = _localStatus.toLowerCase();
    double progress = 0.5; // default for menunggu
    if (s == 'disetujui' || s == 'selesai') progress = 1.0;
    
    return Stack(
      alignment: Alignment.center,
      children: [
        Container(height: 6, width: double.infinity, decoration: BoxDecoration(color: Colors.grey.shade200, borderRadius: BorderRadius.circular(3))),
        Align(
          alignment: Alignment.centerLeft,
          child: AnimatedContainer(duration: const Duration(seconds: 1), height: 6, width: MediaQuery.of(context).size.width * 0.8 * progress, decoration: BoxDecoration(color: statusColor, borderRadius: BorderRadius.circular(3))),
        ),
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            _buildStepItem('Penyusunan', true, true, statusColor),
            _buildStepItem('Verifikasi', progress <= 0.5, progress > 0.5, statusColor),
            _buildStepItem('Selesai', progress == 1.0, progress == 1.0, statusColor),
          ],
        )
      ],
    );
  }

  Widget _buildStepItem(String title, bool isActive, bool isDone, MaterialColor color) {
    return Column(
      children: [
        Container(
          width: 36, height: 36,
          decoration: BoxDecoration(color: isDone ? color : Colors.white, shape: BoxShape.circle, border: Border.all(color: isDone ? color : (isActive ? color : Colors.grey.shade300), width: 3), boxShadow: isActive || isDone ? [BoxShadow(color: color.withOpacity(0.3), blurRadius: 8)] : []),
          child: Icon(isDone ? Icons.check : Icons.circle, size: 16, color: isDone ? Colors.white : (isActive ? color : Colors.transparent)),
        ),
        const SizedBox(height: 8),
        Text(title, style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: isActive || isDone ? AppTheme.textDark : Colors.black38)),
      ],
    );
  }

  Widget _buildBasicInfo() {
    final keg = _lpj!.kegiatan;
    return Column(
      children: [
        Row(
          children: [
            Expanded(child: _buildInfoCard('Pelaksana', '${keg?.prodiPenyelenggara ?? '-'}\n${keg?.pemilikKegiatan ?? '-'}')),
            const SizedBox(width: 16),
            Expanded(child: _buildInfoCard('Penanggung Jawab', '${keg?.rawData?['nama_penanggung_jawab'] ?? '-'}\nNIP. ${keg?.rawData?['nip_penanggung_jawab'] ?? '-'}')),
          ],
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(
              child: Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.blue.shade100)),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('KODE MAK AKTIF', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.blue, letterSpacing: 1)),
                    const SizedBox(height: 4),
                    Text(keg?.rawData?['kode_mak'] ?? '-', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: Colors.blue, fontFamily: 'monospace')),
                  ],
                ),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(child: _buildInfoCard('Waktu Pelaksanaan', '${_fmtDateIndo(keg?.rawData?['tanggal_mulai'])} -\n${_fmtDateIndo(keg?.rawData?['tanggal_selesai'])}')),
          ],
        ),
      ],
    );
  }

  Widget _buildInfoCard(String title, String value) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade200)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title.toUpperCase(), style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
          const SizedBox(height: 4),
          Text(value, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textDark, height: 1.4)),
        ],
      ),
    );
  }

  Widget _buildTableLpj() {
    final rabItems = _lpj!.rawData?['items'] as List? ?? [];
    if(rabItems.isEmpty) return const SizedBox.shrink();
    
    // Grouping simple by index since we might not have 'kategori' inside LPJ items directly
    final Map<String, List<dynamic>> grouped = {'Belanja LPJ': rabItems};
    
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: grouped.entries.map((entry) {
        String kategori = entry.key;
        List items = entry.value;

        return Padding(
          padding: const EdgeInsets.only(bottom: 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.blue.shade100)), child: const Icon(Icons.folder_open_rounded, color: Colors.blue, size: 20)),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(kategori.toUpperCase(), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: 1)),
                        const Text('KATEGORI BELANJA', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.black45, letterSpacing: 1)),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              ...items.map((item) => _buildLpjItemCard(item)).toList(),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildLpjItemCard(dynamic item) {
    bool isExpanded = _expandedFeedbacks.contains(item['id'].toString());
    bool hasFeedback = item['catatan'] != null && item['catatan'].toString().isNotEmpty;

    double anggaran = item['nominal_anggaran'] != null ? double.parse(item['nominal_anggaran'].toString()) : 0;
    double realisasi = item['nominal_realisasi'] != null ? double.parse(item['nominal_realisasi'].toString()) : 0;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade200), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 5))]),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  flex: 3,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(item['uraian'] ?? 'Item Rincian', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Container(padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2), decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(4), border: Border.all(color: Colors.grey.shade200)), child: Text(item['rincian'] ?? '-', style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.black54))),
                        ],
                      )
                    ],
                  ),
                ),
                Expanded(
                  flex: 2,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      const Text('ANGGARAN', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
                      Text(_formatRupiah(anggaran), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.black54)),
                      const SizedBox(height: 8),
                      const Text('REALISASI', style: TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.blue, letterSpacing: 1)),
                      Text(_formatRupiah(realisasi), style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: Colors.blue)),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    ElevatedButton.icon(
                      onPressed: () {},
                      icon: const Icon(Icons.receipt_rounded, size: 14),
                      label: const Text('Bukti', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                      style: ElevatedButton.styleFrom(backgroundColor: Colors.blue.shade50, foregroundColor: Colors.blue.shade700, elevation: 0, padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8)),
                    ),
                  ],
                ),
                InkWell(
                  onTap: () {
                    setState(() {
                      if (isExpanded) _expandedFeedbacks.remove(item['id'].toString());
                      else _expandedFeedbacks.add(item['id'].toString());
                    });
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(color: hasFeedback ? Colors.amber.shade100 : Colors.grey.shade100, borderRadius: BorderRadius.circular(12)),
                    child: Row(
                      children: [
                        Icon(Icons.comment_rounded, size: 14, color: hasFeedback ? Colors.amber.shade700 : Colors.black45),
                        const SizedBox(width: 6),
                        Text('Catatan', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: hasFeedback ? Colors.amber.shade800 : Colors.black54)),
                        if (hasFeedback) ...[
                          const SizedBox(width: 4),
                          Container(width: 6, height: 6, decoration: const BoxDecoration(color: Colors.red, shape: BoxShape.circle))
                        ]
                      ],
                    ),
                  ),
                )
              ],
            ),
          ),
          if (isExpanded)
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: Colors.amber.shade50, borderRadius: const BorderRadius.vertical(bottom: Radius.circular(20))),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.edit_note_rounded, size: 14, color: Colors.blue),
                      SizedBox(width: 6),
                      Text('CATATAN PERBAIKAN', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: TextEditingController(text: item['catatan']),
                    readOnly: !_isActionable,
                    maxLines: 2,
                    decoration: InputDecoration(
                      hintText: 'Tuliskan alasan jika perlu perbaikan...',
                      hintStyle: const TextStyle(fontSize: 12, color: Colors.black38),
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.amber.shade200)),
                      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.amber.shade200)),
                      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Colors.blue)),
                      contentPadding: const EdgeInsets.all(12),
                    ),
                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                  ),
                ],
              ),
            )
        ],
      ),
    );
  }

  Widget _buildSummaryDashboard() {
    double totalAnggaran = 0;
    double totalRealisasi = 0;
    final items = _lpj!.rawData?['items'] as List? ?? [];
    
    for (var item in items) {
       totalAnggaran += item['nominal_anggaran'] != null ? double.parse(item['nominal_anggaran'].toString()) : 0;
       totalRealisasi += item['nominal_realisasi'] != null ? double.parse(item['nominal_realisasi'].toString()) : 0;
    }
    double diff = totalRealisasi - totalAnggaran;

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.blueGrey.shade900, borderRadius: BorderRadius.circular(32), boxShadow: [BoxShadow(color: Colors.blue.shade900.withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 10))]),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('ANGGARAN DISETUJUI', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.white.withOpacity(0.6), letterSpacing: 1)),
                    const SizedBox(height: 4),
                    Text(_formatRupiah(totalAnggaran), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white)),
                  ],
                ),
              ),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('TOTAL REALISASI', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.white.withOpacity(0.6), letterSpacing: 1)),
                    const SizedBox(height: 4),
                    Text(_formatRupiah(totalRealisasi), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.lightBlueAccent)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.white.withOpacity(0.1), borderRadius: BorderRadius.circular(16)),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                if (diff.abs() < 1) ...[
                  const Icon(Icons.check_circle_rounded, color: Colors.tealAccent, size: 16),
                  const SizedBox(width: 8),
                  const Text('BALANCE: SESUAI ANGGARAN', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.tealAccent, letterSpacing: 1)),
                ] else if (diff > 0) ...[
                  const Icon(Icons.warning_amber_rounded, color: Colors.redAccent, size: 16),
                  const SizedBox(width: 8),
                  Text('OVER BUDGET: ${_formatRupiah(diff)}', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.redAccent, letterSpacing: 1)),
                ] else ...[
                  const Icon(Icons.info_outline_rounded, color: Colors.lightBlueAccent, size: 16),
                  const SizedBox(width: 8),
                  Text('UNDER BUDGET: ${_formatRupiah(diff.abs())}', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.lightBlueAccent, letterSpacing: 1)),
                ]
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildPanelVerifikasi() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(32), boxShadow: [BoxShadow(color: Colors.blue.shade100.withOpacity(0.5), blurRadius: 20, offset: const Offset(0, 10))], border: Border.all(color: Colors.blue.shade50)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.admin_panel_settings_rounded, color: Colors.blue.shade600),
              const SizedBox(width: 8),
              const Text('PANEL VERIFIKASI BENDAHARA', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: 1)),
            ],
          ),
          const SizedBox(height: 24),
          const Text('CATATAN PENUTUP / INSTRUKSI GLOBAL', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
          const SizedBox(height: 8),
          TextField(
            controller: _globalCatatanController,
            maxLines: 3,
            decoration: InputDecoration(
              hintText: 'Tuliskan feedback akhir untuk seluruh laporan ini...',
              hintStyle: const TextStyle(fontSize: 12, color: Colors.black38),
              filled: true,
              fillColor: Colors.grey.shade50,
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Colors.grey.shade200)),
              enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Colors.grey.shade200)),
              focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: const BorderSide(color: Colors.blue)),
            ),
          ),
          const SizedBox(height: 24),
          Consumer<BendaharaProvider>(
            builder: (context, provider, child) {
              return Column(
                children: [
                  ElevatedButton(
                    onPressed: provider.isSubmitting ? null : () async {
                       final success = await provider.prosesLpj(_lpj!.id, 'approve', komentar: _globalCatatanController.text);
                       
                       if (!mounted) return;

                       if(success['success']) {
                           ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('LPJ berhasil disetujui')));
                           Navigator.pop(context);
                       } else {
                           ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(success['message'])));
                       }
                    },
                    style: ElevatedButton.styleFrom(backgroundColor: Colors.teal.shade600, foregroundColor: Colors.white, padding: const EdgeInsets.symmetric(vertical: 16), minimumSize: const Size(double.infinity, 50), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)), elevation: 4),
                    child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [if(provider.isSubmitting) const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)), const SizedBox(width: 8), const Icon(Icons.check_circle_rounded, size: 18), const SizedBox(width: 8), const Text('SETUJUI LPJ LUNAS', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, letterSpacing: 1))]),
                  ),
                  const SizedBox(height: 12),
                  OutlinedButton(
                    onPressed: provider.isSubmitting ? null : () async {
                       if (_globalCatatanController.text.isEmpty) {
                           ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Catatan wajib diisi untuk permintaan revisi')));
                           return;
                       }
                       final success = await provider.prosesLpj(_lpj!.id, 'revisi', komentar: _globalCatatanController.text);
                       
                       if (!mounted) return;

                       if(success['success']) {
                           ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Permintaan revisi LPJ berhasil dikirim')));
                           Navigator.pop(context);
                       } else {
                           ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(success['message'])));
                       }
                    },
                    style: OutlinedButton.styleFrom(foregroundColor: Colors.amber.shade700, side: BorderSide(color: Colors.amber.shade300, width: 2), padding: const EdgeInsets.symmetric(vertical: 16), minimumSize: const Size(double.infinity, 50), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16))),
                    child: const Row(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.edit_rounded, size: 18), SizedBox(width: 8), Text('KIRIM PERMINTAAN REVISI', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, letterSpacing: 1))]),
                  ),
                ],
              );
            }
          )
        ],
      ),
    );
  }

  Widget _buildLockedPanel() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(32),
      decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(32), border: Border.all(color: Colors.grey.shade200, style: BorderStyle.solid)),
      child: Column(
        children: [
          Icon(Icons.lock_rounded, size: 48, color: Colors.grey.shade400),
          const SizedBox(height: 16),
          const Text('HALAMAN TERKUNCI (READ-ONLY)', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.black45, letterSpacing: 1)),
          const SizedBox(height: 8),
          Text(_localStatus.toLowerCase() == 'revisi' ? 'Sedang diperbaiki oleh Admin. Pantau status hingga berubah menjadi "Telah Direvisi".' : 'Laporan ini telah selesai divalidasi.', textAlign: TextAlign.center, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.black54)),
        ],
      ),
    );
  }
}
