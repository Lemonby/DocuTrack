import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';

// --- MODELS ---
class IndikatorItem {
  String? bulan;
  TextEditingController indikatorCtrl = TextEditingController();
  TextEditingController targetCtrl = TextEditingController();

  void dispose() {
    indikatorCtrl.dispose();
    targetCtrl.dispose();
  }
}

class RabItem {
  TextEditingController uraianCtrl = TextEditingController();
  TextEditingController rincianCtrl = TextEditingController();
  TextEditingController vol1Ctrl = TextEditingController();
  TextEditingController sat1Ctrl = TextEditingController();
  TextEditingController vol2Ctrl = TextEditingController();
  TextEditingController sat2Ctrl = TextEditingController();
  TextEditingController hargaCtrl = TextEditingController();
  
  int get v1 => int.tryParse(vol1Ctrl.text) ?? 0;
  int get v2 {
    final val = int.tryParse(vol2Ctrl.text);
    if (val != null) return val;
    if (vol2Ctrl.text.isEmpty) return 1; // If empty, assume 1 so it doesn't zero out V1
    return 0;
  }
  int get harga => int.tryParse(hargaCtrl.text.replaceAll('.', '')) ?? 0;
  
  int get total {
    if (v1 == 0) return 0;
    return v1 * v2 * harga;
  }

  void dispose() {
    uraianCtrl.dispose();
    rincianCtrl.dispose();
    vol1Ctrl.dispose();
    sat1Ctrl.dispose();
    vol2Ctrl.dispose();
    sat2Ctrl.dispose();
    hargaCtrl.dispose();
  }
}

class RabCategory {
  String name;
  List<RabItem> items = [];
  
  RabCategory(this.name);
  
  int get total => items.fold(0, (sum, item) => sum + item.total);
}

class UsulanFormView extends StatefulWidget {
  final Map<String, dynamic>? usulan;
  const UsulanFormView({super.key, this.usulan});

  @override
  State<UsulanFormView> createState() => _UsulanFormViewState();
}

class _UsulanFormViewState extends State<UsulanFormView> {
  int _currentStep = 0;
  bool _isLoading = false;

  // Controllers - Tahap 1
  final _namaPengusulController = TextEditingController();
  final _nimController = TextEditingController();
  String? _jurusan;
  String? _prodi;
  final _namaKegiatanController = TextEditingController();
  String? _wadirTujuan;

  // Controllers - Tahap 2
  final _gambaranUmumController = TextEditingController();
  final _penerimaManfaatController = TextEditingController();

  // Controllers - Tahap 3
  final _metodePelaksanaanController = TextEditingController();
  final List<TextEditingController> _tahapanList = [];
  final List<IndikatorItem> _indikatorList = [];
  final List<String> _selectedIkus = [];

  // Controllers - Tahap 4 (RAB)
  final List<RabCategory> _rabCategories = [
    RabCategory('Belanja Barang'),
    RabCategory('Belanja Jasa'),
    RabCategory('Belanja Perjalanan'),
  ];
  final _newCategoryCtrl = TextEditingController();

  // Data Masters
  final List<String> _jurusanList = [
    'Teknik Sipil', 'Teknik Mesin', 'Teknik Elektro', 
    'Teknik Informatika dan Komputer', 'Teknik Grafika dan Penerbitan',
    'Akuntansi', 'Administrasi Niaga', 'Pascasarjana'
  ];

  final Map<String, List<String>> _prodiMap = {
    'Teknik Sipil': ['D3 Konstruksi Sipil', 'D4 Teknik Perancangan Jalan dan Jembatan'],
    'Teknik Mesin': ['D3 Teknik Mesin', 'D4 Teknik Mesin Produksi dan Perawatan', 'D4 Teknologi Rekayasa Pembangkit Energi'],
    'Teknik Elektro': ['D3 Teknik Listrik', 'D3 Teknik Telekomunikasi', 'D4 Teknik Otomasi Listrik Industri'],
    'Teknik Informatika dan Komputer': ['D3 Teknik Informatika', 'D4 Teknik Informatika', 'D4 Teknik Multimedia Digital', 'D4 Keamanan Sistem Informasi'],
    'Teknik Grafika dan Penerbitan': ['D3 Desain Grafis', 'D3 Penerbitan'],
    'Akuntansi': ['D3 Akuntansi', 'D4 Akuntansi Keuangan', 'D4 Keuangan Perbankan'],
    'Administrasi Niaga': ['D3 Administrasi Bisnis', 'D4 MICE', 'D4 Administrasi Bisnis Terapan'],
    'Pascasarjana': ['S2 Rekayasa Teknologi', 'S2 Terapan Keuangan dan Perbankan'],
  };

  final List<String> _wadirList = [
    'Wadir 1 - Akademik', 'Wadir 2 - Umum & Keuangan',
    'Wadir 3 - Kemahasiswaan', 'Wadir 4 - Kerjasama & Hubungan Luar'
  ];

  final List<String> _bulanList = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];

  final List<String> _ikuOptions = [
    'IKU 1 - Lulusan Mendapat Pekerjaan yang Layak',
    'IKU 2 - Mahasiswa Mendapat Pengalaman di Luar Kampus',
    'IKU 3 - Dosen Berkegiatan di Luar Kampus',
    'IKU 4 - Praktisi Mengajar di Dalam Kampus',
    'IKU 5 - Hasil Kerja Dosen Digunakan oleh Masyarakat',
    'IKU 6 - Program Studi Bekerjasama dengan Mitra Kelas Dunia',
    'IKU 7 - Kelas yang Kolaboratif dan Partisipatif',
    'IKU 8 - Program Studi Berstandar Internasional',
  ];

  @override
  void initState() {
    super.initState();
    if (widget.usulan != null) {
      _namaPengusulController.text = widget.usulan!['nama_mahasiswa'] ?? '';
      _namaKegiatanController.text = widget.usulan!['nama'] ?? '';
      _jurusan = 'Teknik Informatika dan Komputer';
    }
  }

  @override
  void dispose() {
    for (var ctrl in _tahapanList) { ctrl.dispose(); }
    for (var ind in _indikatorList) { ind.dispose(); }
    for (var cat in _rabCategories) {
      for (var item in cat.items) { item.dispose(); }
    }
    _newCategoryCtrl.dispose();
    super.dispose();
  }

  void _nextStep() {
    if (_currentStep < 3) setState(() => _currentStep++);
    else _submitForm();
  }

  void _prevStep() {
    if (_currentStep > 0) setState(() => _currentStep--);
  }

  Future<void> _submitForm() async {
    setState(() => _isLoading = true);
    await Future.delayed(const Duration(seconds: 2));
    setState(() => _isLoading = false);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Berhasil menyimpan usulan!')));
      Navigator.pop(context, true);
    }
  }

  int get grandTotal => _rabCategories.fold(0, (sum, cat) => sum + cat.total);

  void _addTahapan() => setState(() => _tahapanList.add(TextEditingController()));
  void _removeTahapan(int index) => setState(() { _tahapanList[index].dispose(); _tahapanList.removeAt(index); });

  void _addIndikator() => setState(() => _indikatorList.add(IndikatorItem()));
  void _removeIndikator(int index) => setState(() { _indikatorList[index].dispose(); _indikatorList.removeAt(index); });

  void _showIkuDialog() {
    showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setDialogState) {
            return AlertDialog(
              title: const Text('Pilih IKU', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              content: SizedBox(
                width: double.maxFinite,
                child: ListView.builder(
                  shrinkWrap: true,
                  itemCount: _ikuOptions.length,
                  itemBuilder: (context, index) {
                    final iku = _ikuOptions[index];
                    final isSelected = _selectedIkus.contains(iku);
                    return CheckboxListTile(
                      title: Text(iku, style: const TextStyle(fontSize: 13)),
                      value: isSelected,
                      activeColor: Colors.blue.shade600,
                      onChanged: (val) {
                        setDialogState(() {
                          if (val == true) _selectedIkus.add(iku);
                          else _selectedIkus.remove(iku);
                        });
                        setState(() {});
                      },
                    );
                  },
                ),
              ),
              actions: [
                TextButton(onPressed: () => Navigator.pop(context), child: const Text('Tutup', style: TextStyle(fontWeight: FontWeight.bold))),
              ],
            );
          }
        );
      }
    );
  }

  void _showAddCategoryDialog() {
    _newCategoryCtrl.clear();
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Tambah Kategori RAB', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
          content: TextField(
            controller: _newCategoryCtrl,
            decoration: const InputDecoration(hintText: 'Contoh: Konsumsi, Transportasi...'),
            autofocus: true,
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
            ElevatedButton(
              onPressed: () {
                if (_newCategoryCtrl.text.isNotEmpty) {
                  setState(() => _rabCategories.add(RabCategory(_newCategoryCtrl.text)));
                  Navigator.pop(context);
                }
              },
              style: ElevatedButton.styleFrom(backgroundColor: Colors.blue.shade600, foregroundColor: Colors.white),
              child: const Text('Simpan'),
            )
          ],
        );
      }
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: Text(widget.usulan == null ? 'Tambah Usulan KAK' : 'Edit Usulan KAK', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: AppTheme.textDark,
        elevation: 1,
      ),
      body: _isLoading 
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                if (widget.usulan != null)
                  Container(
                    margin: const EdgeInsets.all(16).copyWith(bottom: 0),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.orange.withOpacity(0.1),
                      border: Border(left: const BorderSide(color: Colors.orange, width: 4)),
                      borderRadius: const BorderRadius.horizontal(right: Radius.circular(16)),
                    ),
                    child: const Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Icon(Icons.warning_amber_rounded, color: Colors.orange, size: 24),
                        SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Catatan Revisi', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.orange, fontSize: 14)),
                              SizedBox(height: 4),
                              Text('Terdapat beberapa bagian yang perlu diperbaiki sebelum pengajuan dapat dilanjutkan.', style: TextStyle(fontSize: 12, color: Colors.black87, height: 1.4)),
                            ],
                          ),
                        )
                      ],
                    ),
                  ),
                _buildStepperHeader(),
                Expanded(
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(20),
                    child: _buildCurrentStepContent(),
                  ),
                ),
                _buildBottomNav(),
              ],
            ),
    );
  }

  Widget _buildStepperHeader() {
    final steps = ['Data Pengusul', 'Strategi', 'IKU & Renstra', 'RAB'];
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
      decoration: const BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, 4))]),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: List.generate(steps.length, (index) {
          final isActive = index <= _currentStep;
          final isCurrent = index == _currentStep;
          return Expanded(
            child: Column(
              children: [
                Container(
                  width: 36, height: 36,
                  decoration: BoxDecoration(
                    color: isActive ? Colors.blue.shade600 : Colors.grey.shade200,
                    shape: BoxShape.circle,
                    boxShadow: isCurrent ? [BoxShadow(color: Colors.blue.withOpacity(0.4), blurRadius: 8)] : null,
                  ),
                  child: Center(child: Text('${index + 1}', style: TextStyle(color: isActive ? Colors.white : Colors.grey.shade500, fontWeight: FontWeight.bold))),
                ),
                const SizedBox(height: 8),
                Text(steps[index], textAlign: TextAlign.center, style: TextStyle(fontSize: 10, fontWeight: isCurrent ? FontWeight.w900 : FontWeight.bold, color: isActive ? Colors.blue.shade700 : Colors.grey.shade500))
              ],
            ),
          );
        }),
      ),
    );
  }

  Widget _buildCurrentStepContent() {
    switch (_currentStep) {
      case 0: return _buildStep1();
      case 1: return _buildStep2();
      case 2: return _buildStep3();
      case 3: return _buildStep4();
      default: return const SizedBox();
    }
  }

  Widget _buildStep1() {
    return _buildCardWrapper(
      title: 'Input Data Pengusul / Pelaksana',
      child: Column(
        children: [
          _buildTextField('Nama Pengusul', 'Masukkan nama pengusul', _namaPengusulController, Icons.person_rounded),
          const SizedBox(height: 16),
          _buildTextField('NIM/NIP', 'Masukkan NIM atau NIP', _nimController, Icons.badge_rounded),
          const SizedBox(height: 16),
          _buildDropdown(
            'Jurusan', 'Pilih Jurusan', _jurusanList, _jurusan, 
            (val) => setState(() {
              _jurusan = val;
              _prodi = null; // Reset prodi
            })
          ),
          const SizedBox(height: 16),
          _buildDropdown(
            'Prodi', 
            _jurusan == null ? 'Pilih Jurusan Terlebih Dahulu' : 'Pilih Program Studi', 
            _jurusan == null ? [] : _prodiMap[_jurusan!] ?? [], 
            _prodi, 
            (val) => setState(() => _prodi = val), 
            disabled: _jurusan == null
          ),
          const SizedBox(height: 16),
          _buildTextField('Nama Kegiatan', 'Masukkan nama kegiatan', _namaKegiatanController, Icons.event_rounded),
          const SizedBox(height: 16),
          _buildDropdown('Wadir Tujuan', 'Pilih Wadir Tujuan', _wadirList, _wadirTujuan, (val) => setState(() => _wadirTujuan = val)),
        ],
      ),
    );
  }

  Widget _buildStep2() {
    return _buildCardWrapper(
      title: 'Informasi Dasar Kegiatan',
      child: Column(
        children: [
          _buildTextField('Nama Pengusul', '', TextEditingController(text: _namaPengusulController.text), Icons.person_rounded, disabled: true),
          const SizedBox(height: 16),
          _buildTextField('Nama Kegiatan', '', TextEditingController(text: _namaKegiatanController.text), Icons.event_rounded, disabled: true),
          const SizedBox(height: 16),
          _buildTextField('Gambaran Umum', 'Jelaskan gambaran umum kegiatan ini...', _gambaranUmumController, Icons.description_rounded, maxLines: 4),
          const SizedBox(height: 16),
          _buildTextField('Penerima Manfaat', 'Siapa yang mendapat manfaat dari kegiatan ini?', _penerimaManfaatController, Icons.group_rounded, maxLines: 3),
        ],
      ),
    );
  }

  Widget _buildStep3() {
    return _buildCardWrapper(
      title: 'Indikator Kinerja Utama & Renstra',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildTextField('Metode Pelaksanaan', 'Jelaskan metode pelaksanaan kegiatan...', _metodePelaksanaanController, Icons.build_circle_rounded, maxLines: 3),
          const SizedBox(height: 24),
          
          const Text('Tahapan Pelaksanaan', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
          const SizedBox(height: 8),
          ...List.generate(_tahapanList.length, (index) => Padding(
            padding: const EdgeInsets.only(bottom: 8.0),
            child: Row(
              children: [
                Expanded(child: _buildTextField('', 'Tahapan ${index + 1}', _tahapanList[index], null)),
                IconButton(icon: const Icon(Icons.remove_circle, color: Colors.red), onPressed: () => _removeTahapan(index)),
              ],
            ),
          )),
          OutlinedButton.icon(
            onPressed: _addTahapan, icon: const Icon(Icons.add_rounded, size: 16), label: const Text('Tambah Tahapan'),
            style: OutlinedButton.styleFrom(foregroundColor: Colors.blue.shade700, side: BorderSide(color: Colors.blue.shade200))
          ),
          
          const SizedBox(height: 32),
          
          const Text('Indikator Keberhasilan per Bulan', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
          const SizedBox(height: 8),
          ...List.generate(_indikatorList.length, (index) {
            final item = _indikatorList[index];
            return Container(
              margin: const EdgeInsets.only(bottom: 12),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(border: Border.all(color: Colors.grey.shade300), borderRadius: BorderRadius.circular(12), color: Colors.grey.shade50),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Indikator ${index + 1}', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                      IconButton(icon: const Icon(Icons.delete_outline, color: Colors.red, size: 18), padding: EdgeInsets.zero, constraints: const BoxConstraints(), onPressed: () => _removeIndikator(index)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        flex: 2,
                        child: DropdownButtonFormField<String>(
                          value: item.bulan,
                          decoration: InputDecoration(
                            hintText: 'Bulan',
                            isDense: true, contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                          ),
                          style: const TextStyle(fontSize: 12, color: Colors.black),
                          items: _bulanList.map((String val) => DropdownMenuItem(value: val, child: Text(val))).toList(),
                          onChanged: (val) => setState(() => item.bulan = val),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(flex: 3, child: _buildCompactTextField('Indikator', item.indikatorCtrl)),
                      const SizedBox(width: 8),
                      Expanded(
                        flex: 2, 
                        child: TextField(
                          controller: item.targetCtrl,
                          keyboardType: TextInputType.number,
                          style: const TextStyle(fontSize: 12),
                          decoration: InputDecoration(
                            hintText: 'Target', isDense: true, suffixText: '%',
                            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                          ),
                        )
                      ),
                    ],
                  )
                ],
              ),
            );
          }),
          OutlinedButton.icon(
            onPressed: _addIndikator, icon: const Icon(Icons.add_rounded, size: 16), label: const Text('Tambah Indikator'),
            style: OutlinedButton.styleFrom(foregroundColor: Colors.blue.shade700, side: BorderSide(color: Colors.blue.shade200))
          ),

          const SizedBox(height: 32),
          
          const Text('IKU (Indikator Kinerja Utama) yang Dipilih:', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blueGrey)),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(border: Border.all(color: Colors.blue.shade200), borderRadius: BorderRadius.circular(12), color: Colors.blue.shade50),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (_selectedIkus.isEmpty) const Text('Belum ada IKU dipilih', style: TextStyle(color: Colors.blueGrey, fontStyle: FontStyle.italic))
                else Wrap(
                  spacing: 8, runSpacing: 8,
                  children: _selectedIkus.map((iku) => Chip(
                    label: Text(iku, style: const TextStyle(fontSize: 11, color: Colors.blue)),
                    backgroundColor: Colors.white, deleteIconColor: Colors.red.shade300,
                    onDeleted: () => setState(() => _selectedIkus.remove(iku)),
                    side: BorderSide(color: Colors.blue.shade200),
                  )).toList()
                ),
                const SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: _showIkuDialog, icon: const Icon(Icons.edit_rounded, size: 16), label: const Text('Pilih IKU'),
                    style: ElevatedButton.styleFrom(backgroundColor: Colors.white, foregroundColor: Colors.blue.shade700, elevation: 0, side: BorderSide(color: Colors.blue.shade200)),
                  ),
                )
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildStep4() {
    return _buildCardWrapper(
      title: 'Rincian Anggaran Biaya (RAB)',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Kategori RAB', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
              ElevatedButton.icon(
                onPressed: _showAddCategoryDialog, icon: const Icon(Icons.add_rounded, size: 14), label: const Text('Kategori'),
                style: ElevatedButton.styleFrom(backgroundColor: Colors.blue.shade600, foregroundColor: Colors.white, padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6)),
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          if (_rabCategories.isEmpty)
            Container(
              padding: const EdgeInsets.all(32),
              decoration: BoxDecoration(borderRadius: BorderRadius.circular(16), color: Colors.grey.shade100),
              child: const Center(child: Text('Belum ada data RAB.', style: TextStyle(color: Colors.grey))),
            )
          else
            ...List.generate(_rabCategories.length, (catIdx) {
              final category = _rabCategories[catIdx];
              return Container(
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(border: Border.all(color: Colors.grey.shade300), borderRadius: BorderRadius.circular(12)),
                child: Column(
                  children: [
                    // Category Header
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      decoration: BoxDecoration(color: Colors.blue.shade50.withOpacity(0.5), borderRadius: const BorderRadius.vertical(top: Radius.circular(12))),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Text(category.name, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.blue)),
                              const SizedBox(width: 4),
                              IconButton(icon: const Icon(Icons.delete, color: Colors.red, size: 18), padding: EdgeInsets.zero, constraints: const BoxConstraints(), onPressed: () => setState(() => _rabCategories.removeAt(catIdx))),
                            ],
                          ),
                          Text('Rp ${category.total.toString().replaceAll(RegExp(r'\B(?=(\d{3})+(?!\d))'), '.')}', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.blue.shade700)),
                        ],
                      ),
                    ),
                    
                    // Items List
                    Padding(
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          ...List.generate(category.items.length, (iIdx) {
                            final item = category.items[iIdx];
                            return Container(
                              margin: const EdgeInsets.only(bottom: 12),
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(border: Border.all(color: Colors.grey.shade200), borderRadius: BorderRadius.circular(8), color: Colors.white),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text('Item ${iIdx + 1}', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Colors.grey)),
                                      IconButton(icon: const Icon(Icons.close, color: Colors.red, size: 16), padding: EdgeInsets.zero, constraints: const BoxConstraints(), onPressed: () => setState(() { item.dispose(); category.items.removeAt(iIdx); })),
                                    ],
                                  ),
                                  const SizedBox(height: 8),
                                  _buildCompactTextField('Uraian Item', item.uraianCtrl),
                                  const SizedBox(height: 8),
                                  _buildCompactTextField('Rincian', item.rincianCtrl),
                                  const SizedBox(height: 8),
                                  Row(
                                    children: [
                                      Expanded(child: _buildCompactTextField('Vol 1', item.vol1Ctrl, isNumber: true)),
                                      const SizedBox(width: 8),
                                      Expanded(child: _buildCompactTextField('Sat 1', item.sat1Ctrl)),
                                      const SizedBox(width: 8),
                                      Expanded(child: _buildCompactTextField('Vol 2', item.vol2Ctrl, isNumber: true)),
                                      const SizedBox(width: 8),
                                      Expanded(child: _buildCompactTextField('Sat 2', item.sat2Ctrl)),
                                    ],
                                  ),
                                  const SizedBox(height: 8),
                                  Row(
                                    children: [
                                      Expanded(child: _buildCompactTextField('Harga Satuan (Rp)', item.hargaCtrl, isNumber: true)),
                                      const SizedBox(width: 12),
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.end,
                                        children: [
                                          const Text('Sub-total', style: TextStyle(fontSize: 10, color: Colors.grey)),
                                          Text('Rp ${item.total.toString().replaceAll(RegExp(r'\B(?=(\d{3})+(?!\d))'), '.')}', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black)),
                                        ],
                                      )
                                    ],
                                  )
                                ],
                              ),
                            );
                          }),
                          OutlinedButton.icon(
                            onPressed: () => setState(() => category.items.add(RabItem())), 
                            icon: const Icon(Icons.add, size: 14), 
                            label: const Text('Tambah Item RAB', style: TextStyle(fontSize: 12)),
                            style: OutlinedButton.styleFrom(foregroundColor: Colors.blue, side: BorderSide(color: Colors.blue.shade100)),
                          )
                        ],
                      ),
                    )
                  ],
                ),
              );
            }),

          const SizedBox(height: 24),
          
          // Grand Total
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(color: Colors.blue.shade50, border: Border.all(color: Colors.blue.shade200), borderRadius: BorderRadius.circular(16)),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Icon(Icons.calculate_rounded, color: Colors.blue.shade700, size: 28),
                    const SizedBox(width: 12),
                    const Text('Grand Total', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
                  ],
                ),
                Text('Rp ${grandTotal.toString().replaceAll(RegExp(r'\B(?=(\d{3})+(?!\d))'), '.')}', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.blue.shade700)),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildCompactTextField(String hint, TextEditingController controller, {bool isNumber = false}) {
    return TextField(
      controller: controller,
      keyboardType: isNumber ? TextInputType.number : TextInputType.text,
      onChanged: (_) => setState(() {}),
      style: const TextStyle(fontSize: 12),
      decoration: InputDecoration(
        hintText: hint, hintStyle: const TextStyle(fontSize: 12),
        isDense: true, contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(6)),
        filled: true, fillColor: Colors.grey.shade50,
      ),
    );
  }

  Widget _buildCardWrapper({required String title, required Widget child}) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24), boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 15, offset: Offset(0, 5))]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
          const Padding(padding: EdgeInsets.symmetric(vertical: 16), child: Divider()),
          child,
        ],
      ),
    );
  }

  Widget _buildTextField(String label, String hint, TextEditingController controller, IconData? icon, {int maxLines = 1, bool disabled = false}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (label.isNotEmpty) ...[
          Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey, letterSpacing: 0.5)),
          const SizedBox(height: 8),
        ],
        TextField(
          controller: controller, maxLines: maxLines, readOnly: disabled,
          style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: disabled ? Colors.grey.shade600 : AppTheme.textDark),
          decoration: InputDecoration(
            hintText: hint, hintStyle: TextStyle(fontSize: 13, color: Colors.grey.shade400, fontWeight: FontWeight.normal),
            prefixIcon: maxLines == 1 && icon != null ? Icon(icon, size: 18, color: disabled ? Colors.grey.shade400 : Colors.grey.shade500) : null,
            filled: true, fillColor: disabled ? Colors.grey.shade100 : Colors.grey.shade50,
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade300)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.blue.shade400, width: 2)),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          ),
        ),
      ],
    );
  }

  Widget _buildDropdown(String label, String hint, List<String> items, String? value, Function(String?) onChanged, {bool disabled = false}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey, letterSpacing: 0.5)),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: value,
          icon: Icon(Icons.expand_more_rounded, color: disabled ? Colors.grey.shade400 : Colors.grey.shade600),
          decoration: InputDecoration(
            hintText: hint, hintStyle: TextStyle(fontSize: 13, color: Colors.grey.shade400, fontWeight: FontWeight.normal),
            filled: true, fillColor: disabled ? Colors.grey.shade100 : Colors.grey.shade50,
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade300)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.blue.shade400, width: 2)),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          ),
          style: TextStyle(fontSize: 13, color: disabled ? Colors.grey.shade600 : AppTheme.textDark, fontWeight: FontWeight.w600),
          items: items.map((String val) => DropdownMenuItem(value: val, child: Text(val))).toList(),
          onChanged: disabled ? null : onChanged,
        ),
      ],
    );
  }

  Widget _buildBottomNav() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, -4))]),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          if (_currentStep > 0)
            ElevatedButton.icon(
              onPressed: _prevStep, icon: const Icon(Icons.arrow_back_rounded, size: 16), label: const Text('Kembali'),
              style: ElevatedButton.styleFrom(backgroundColor: Colors.grey.shade100, foregroundColor: Colors.grey.shade800, elevation: 0, padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
            )
          else const SizedBox(),
          
          ElevatedButton.icon(
            onPressed: _nextStep,
            icon: Icon(_currentStep == 3 ? Icons.check_circle_rounded : Icons.arrow_forward_rounded, size: 16),
            label: Text(_currentStep == 3 ? 'SIMPAN & AJUKAN' : 'Lanjut', style: const TextStyle(fontWeight: FontWeight.w900)),
            style: ElevatedButton.styleFrom(backgroundColor: _currentStep == 3 ? Colors.green.shade600 : Colors.blue.shade600, foregroundColor: Colors.white, elevation: 4, padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
          )
        ],
      ),
    );
  }
}
