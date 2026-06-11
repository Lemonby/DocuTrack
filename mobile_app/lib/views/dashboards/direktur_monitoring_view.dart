import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import 'package:intl/intl.dart';

class DirekturMonitoringView extends StatefulWidget {
  const DirekturMonitoringView({super.key});

  @override
  State<DirekturMonitoringView> createState() => _DirekturMonitoringViewState();
}

class _DirekturMonitoringViewState extends State<DirekturMonitoringView> {
  final TextEditingController _searchController = TextEditingController();
  String _selectedStatus = 'Semua';
  String _selectedJurusan = 'SEMUA UNIT';
  bool _isLoading = false;

  final List<String> _statusTabs = ['Semua', 'Menunggu', 'Approved', 'Ditolak', 'LPJ'];
  final List<String> _jurusanPills = ['SEMUA UNIT', 'Teknik Informatika dan Komputer', 'Teknik Grafika dan Penerbitan', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil', 'Administrasi Niaga', 'Akuntansi'];

  List<Map<String, dynamic>> _dummyProposals = [];

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _loadData() async {
    setState(() => _isLoading = true);
    // Simulate network delay
    await Future.delayed(const Duration(milliseconds: 600));
    
    setState(() {
      _dummyProposals = [
        {
          'id': 1201,
          'nama': 'Pengembangan Kurikulum Berbasis Industri',
          'pengusul': 'Dr. Ir. Heru Santoso',
          'jurusan': 'Teknik Elektro',
          'tahap_sekarang': 'Dana Cair',
          'status': 'In Process',
          'dana': 45000000,
          'is_termin': true,
        },
        {
          'id': 1202,
          'nama': 'Pembangunan Smart Classroom',
          'pengusul': 'Dra. Maria Ulfa, M.Hum',
          'jurusan': 'Administrasi Niaga',
          'tahap_sekarang': 'LPJ',
          'status': 'Approved',
          'dana': 15000000,
          'is_termin': false,
        },
        {
          'id': 1203,
          'nama': 'Workshop UI/UX Design Modern',
          'pengusul': 'Rizky Pratama',
          'jurusan': 'Teknik Informatika dan Komputer',
          'tahap_sekarang': 'Verifikasi',
          'status': 'In Process',
          'dana': 25000000,
          'is_termin': true,
        },
        {
          'id': 1204,
          'nama': 'Seminar Internasional Blockchain',
          'pengusul': 'Ahmad Fauzi',
          'jurusan': 'Teknik Informatika dan Komputer',
          'tahap_sekarang': 'LPJ',
          'status': 'Approved',
          'dana': 12000000,
          'is_termin': false,
        },
        {
          'id': 1205,
          'nama': 'Lomba Karya Tulis Ilmiah Nasional',
          'pengusul': 'Dewi Lestari',
          'jurusan': 'Akuntansi',
          'tahap_sekarang': 'Usulan',
          'status': 'Menunggu',
          'dana': 5000000,
          'is_termin': false,
        },
      ];
      _isLoading = false;
    });
  }

  Future<void> _refresh() async {
    _loadData();
  }

  List<Map<String, dynamic>> get filteredProposals {
    return _dummyProposals.where((p) {
      final matchesStatus = _selectedStatus == 'Semua' || 
          (_selectedStatus == 'Approved' && p['status'] == 'Approved') ||
          (_selectedStatus == 'In Process' && p['status'] == 'In Process') ||
          (_selectedStatus == 'Menunggu' && p['status'] == 'Menunggu') ||
          (_selectedStatus == 'LPJ' && p['tahap_sekarang'] == 'LPJ');
          
      final matchesJurusan = _selectedJurusan == 'SEMUA UNIT' || p['jurusan'] == _selectedJurusan;
      
      final query = _searchController.text.toLowerCase();
      final matchesSearch = query.isEmpty || 
          p['nama'].toLowerCase().contains(query) || 
          p['pengusul'].toLowerCase().contains(query);
          
      return matchesStatus && matchesJurusan && matchesSearch;
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: RefreshIndicator(
        onRefresh: _refresh,
        color: AppTheme.primaryBlue,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildHeader(),
              const SizedBox(height: 24),
              _buildJurusanFilter(),
              const SizedBox(height: 24),
              _buildSearchAndStatus(),
              const SizedBox(height: 32),
              _buildList(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: AppTheme.primaryBlue,
                borderRadius: BorderRadius.circular(8),
                boxShadow: [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 2))],
              ),
              child: const Text('REAL-TIME OVERSIGHT', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 1)),
            ),
            const SizedBox(width: 8),
            Container(width: 8, height: 8, decoration: BoxDecoration(color: AppTheme.accentTeal, shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 2))),
          ],
        ),
        const SizedBox(height: 12),
        const Text('Pemantauan Progres', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: -0.5)),
        const SizedBox(height: 4),
        const Text('Monitor alur pengajuan & anggaran institusi secara akurat.', style: TextStyle(fontSize: 12, color: AppTheme.textMuted, fontStyle: FontStyle.italic)),
      ],
    );
  }

  Widget _buildJurusanFilter() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text('UNIT KERJA FILTER', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
            Text('Slide to explore', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.blue.shade400, fontStyle: FontStyle.italic)),
          ],
        ),
        const SizedBox(height: 12),
        Wrap(
          spacing: 8,
          runSpacing: 10,
          children: _jurusanPills.map((jurusan) {
            final isActive = _selectedJurusan == jurusan;
            return GestureDetector(
              onTap: () => setState(() => _selectedJurusan = jurusan),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 300),
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                decoration: BoxDecoration(
                  color: isActive ? AppTheme.primaryBlue : Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: isActive ? AppTheme.primaryBlue : AppTheme.borderLight),
                  boxShadow: isActive ? [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.3), blurRadius: 4, offset: const Offset(0, 2))] : [],
                ),
                child: Text(
                  jurusan.toUpperCase(),
                  style: TextStyle(
                    fontSize: 9,
                    fontWeight: FontWeight.bold,
                    color: isActive ? Colors.white : AppTheme.textMuted,
                    letterSpacing: 0.5,
                  ),
                ),
              ),
            );
          }).toList(),
        ),
      ],
    );
  }

  Widget _buildSearchAndStatus() {
    return Column(
      children: [
        // Search
        Container(
          height: 56,
          decoration: BoxDecoration(
            color: Colors.grey.shade50,
            borderRadius: BorderRadius.circular(28),
            border: Border.all(color: AppTheme.borderLight),
          ),
          child: TextField(
            controller: _searchController,
            onChanged: (v) => setState(() {}),
            decoration: const InputDecoration(
              hintText: 'Cari kegiatan atau pengusul...',
              hintStyle: TextStyle(fontSize: 13),
              prefixIcon: Icon(Icons.search, color: Colors.blueAccent),
              border: InputBorder.none,
              contentPadding: EdgeInsets.symmetric(vertical: 18),
            ),
          ),
        ),
        const SizedBox(height: 16),
        // Status Tabs
        Container(
          padding: const EdgeInsets.all(6),
          decoration: BoxDecoration(
            color: Colors.grey.shade50,
            borderRadius: BorderRadius.circular(28),
            border: Border.all(color: AppTheme.borderLight),
          ),
          child: SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: _statusTabs.map((status) {
                final isActive = _selectedStatus == status;
                return GestureDetector(
                  onTap: () => setState(() => _selectedStatus = status),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 300),
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                    decoration: BoxDecoration(
                      color: isActive ? AppTheme.primaryBlue : Colors.transparent,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: isActive ? [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.4), blurRadius: 10, offset: const Offset(0, 2))] : [],
                    ),
                    child: Text(
                      status.toUpperCase(),
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: isActive ? Colors.white : AppTheme.textMuted,
                        letterSpacing: 1,
                      ),
                    ),
                  ),
                );
              }).toList(),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildList() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    final items = filteredProposals;

    if (items.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 60),
          child: Column(
            children: [
              Icon(Icons.search_off, size: 48, color: Colors.blue.shade100),
              const SizedBox(height: 16),
              const Text('Data Tidak Ditemukan', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
              const SizedBox(height: 8),
              const Text('Coba gunakan filter lain.', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
            ],
          ),
        ),
      );
    }

    return Column(
      children: items.map((item) {
        return Container(
          margin: const EdgeInsets.only(bottom: 24),
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: AppTheme.borderLight),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 15, offset: const Offset(0, 8))],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Info
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 50,
                    height: 50,
                    decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(16)),
                    child: const Icon(Icons.file_copy, color: Colors.blueAccent),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(item['nama'], style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: AppTheme.textDark, height: 1.2)),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Text(item['pengusul'], style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
                            const SizedBox(width: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: item['is_termin'] ? Colors.amber.shade50 : AppTheme.accentTeal.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: item['is_termin'] ? Colors.amber.shade200 : AppTheme.accentTeal.withOpacity(0.3)),
                              ),
                              child: Text(
                                item['is_termin'] ? 'TERMIN 1 (50%)' : 'LANGSUNG (100%)',
                                style: TextStyle(
                                  fontSize: 8,
                                  fontWeight: FontWeight.bold,
                                  color: item['is_termin'] ? Colors.amber.shade700 : AppTheme.accentTeal,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),
              
              // Timeline Progress
              _buildTimeline(item['tahap_sekarang']),
              
              const SizedBox(height: 24),
              const Divider(color: AppTheme.borderLight),
              const SizedBox(height: 16),
              
              // Bottom Values
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  _buildStatusBadge(item['status']),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(item['dana']),
                        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: -0.5),
                      ),
                      const Text('VALUASI REALISASI', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1.5, fontStyle: FontStyle.italic)),
                    ],
                  ),
                ],
              ),
            ],
          ),
        );
      }).toList(),
    );
  }

  Widget _buildTimeline(String tahapSekarang) {
    final stages = ['Usulan', 'Verifikasi', 'PPK', 'WD', 'Cair', 'LPJ'];
    
    // Determine exact match or fallback logic
    int currentIndex = stages.indexOf(tahapSekarang);
    if (tahapSekarang == 'Dana Cair') currentIndex = 4;
    
    if (currentIndex == -1) currentIndex = 0;

    return Stack(
      alignment: Alignment.center,
      children: [
        // Background line
        Positioned(
          left: 20,
          right: 20,
          top: 10,
          child: Container(height: 4, decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(2))),
        ),
        // Active line
        Positioned(
          left: 20,
          right: 20,
          top: 10,
          child: FractionallySizedBox(
            alignment: Alignment.centerLeft,
            widthFactor: currentIndex / (stages.length - 1),
            child: Container(
              height: 4,
              decoration: BoxDecoration(
                color: Colors.blueAccent,
                borderRadius: BorderRadius.circular(2),
                boxShadow: [BoxShadow(color: Colors.blueAccent.withOpacity(0.4), blurRadius: 4)],
              ),
            ),
          ),
        ),
        // Dots and Labels
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: List.generate(stages.length, (index) {
            final isActive = index <= currentIndex;
            return Column(
              children: [
                AnimatedContainer(
                  duration: const Duration(milliseconds: 500),
                  width: isActive ? 20 : 12,
                  height: isActive ? 20 : 12,
                  margin: EdgeInsets.only(bottom: isActive ? 4 : 8, top: isActive ? 2 : 6),
                  decoration: BoxDecoration(
                    color: isActive ? Colors.blueAccent : Colors.grey.shade200,
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.white, width: isActive ? 4 : 2),
                    boxShadow: isActive ? [BoxShadow(color: Colors.blueAccent.withOpacity(0.4), blurRadius: 6)] : [],
                  ),
                ),
                Text(
                  stages[index].toUpperCase(),
                  style: TextStyle(
                    fontSize: 8,
                    fontWeight: FontWeight.bold,
                    color: isActive ? Colors.blueAccent : Colors.grey.shade400,
                  ),
                ),
              ],
            );
          }),
        ),
      ],
    );
  }

  Widget _buildStatusBadge(String status) {
    Color bg;
    Color text;
    if (status.toLowerCase() == 'approved') {
      bg = AppTheme.accentTeal.withOpacity(0.1);
      text = AppTheme.accentTeal;
    } else if (status.toLowerCase() == 'ditolak') {
      bg = Colors.red.shade50;
      text = Colors.red;
    } else if (status.toLowerCase() == 'menunggu') {
      bg = Colors.amber.shade50;
      text = Colors.amber.shade700;
    } else {
      bg = Colors.blue.shade50;
      text = Colors.blue;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(16)),
      child: Text(
        status.toUpperCase(),
        style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: text, letterSpacing: 1),
      ),
    );
  }
}
