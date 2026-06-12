import 'kegiatan.dart';

class Lpj {
  final int id;
  final int? kegiatanId;
  final Kegiatan? kegiatan;
  final String? statusNama;
  final int? statusId;
  final String? submittedAt;
  final String? approvedAt;
  final String? tenggatLpj;
  final double? grandTotalRealisasi;
  final String? komentarRevisi;
  final String? komentarPenolakan;
  final List<LpjItem> items;
  final Map<String, dynamic>? rawData;

  Lpj({
    required this.id,
    this.kegiatanId,
    this.kegiatan,
    this.statusNama,
    this.statusId,
    this.submittedAt,
    this.approvedAt,
    this.tenggatLpj,
    this.grandTotalRealisasi,
    this.komentarRevisi,
    this.komentarPenolakan,
    this.items = const [],
    this.rawData,
  });

  factory Lpj.fromJson(Map<String, dynamic> json) {
    List<LpjItem> parsedItems = [];
    if (json['items'] != null) {
      final itemList = json['items'] as List<dynamic>;
      parsedItems = itemList.map((e) => LpjItem.fromJson(e)).toList();
<<<<<<< HEAD
      for (var item in parsedItems) {
        calculatedTotal += item.realisasi ?? 0.0;
      }
=======
>>>>>>> c0d5a63 (fix masalah field semua yang ga ke show di halaman utama)
    }

    String extractedStatus = 'Menunggu';
    if (json['status'] is Map) {
      extractedStatus = json['status']['nama'] ?? 'Menunggu';
    } else if (json['status'] is String) {
      extractedStatus = json['status'];
    }

    return Lpj(
      id: json['lpj_id'] ?? json['id'] ?? 0,
      kegiatanId: json['kegiatan_id'],
      kegiatan: json['kegiatan'] != null ? Kegiatan.fromJson(json['kegiatan']) : null,
<<<<<<< HEAD
      statusNama: extractedStatus,
      submittedAt: json['submitted_at'],
      totalPengeluaran: json['grand_total_realisasi'] != null 
          ? (json['grand_total_realisasi'] as num).toDouble()
          : calculatedTotal,
=======
      statusNama: json['status'] is Map ? json['status']['nama'] : (json['status']?.toString() ?? 'Menunggu'),
      statusId: json['status'] is Map ? json['status']['id'] : null,
      submittedAt: json['submitted_at'],
      approvedAt: json['approved_at'],
      tenggatLpj: json['tenggat_lpj'],
      grandTotalRealisasi: json['grand_total_realisasi'] != null ? double.tryParse(json['grand_total_realisasi'].toString()) : 0.0,
      komentarRevisi: json['komentar_revisi'],
      komentarPenolakan: json['komentar_penolakan'],
>>>>>>> c0d5a63 (fix masalah field semua yang ga ke show di halaman utama)
      items: parsedItems,
      rawData: json,
    );
  }
}

class LpjItem {
  final int id;
<<<<<<< HEAD
  final String? uraian;
  final String? rincian;
  final double? realisasi;
  final String? lampiranPath;
  final String? kategoriNama;
  final String? komentar;
=======
  final String? kategoriNama;
  final String? uraian;
  final String? rincian;
  final double? totalHarga; // Anggaran disetujui
  final double? realisasi;
  final double? nominal; // Alias for realisasi
  final String? lampiranPath;
  final String? komentar;
  final String? sat1;
  final String? sat2;
  final double? vol1;
  final double? vol2;
  final double? harga;
>>>>>>> c0d5a63 (fix masalah field semua yang ga ke show di halaman utama)
  final Map<String, dynamic>? rawData;

  LpjItem({
    required this.id,
<<<<<<< HEAD
    this.uraian,
    this.rincian,
    this.realisasi,
    this.lampiranPath,
    this.kategoriNama,
    this.komentar,
=======
    this.kategoriNama,
    this.uraian,
    this.rincian,
    this.totalHarga,
    this.realisasi,
    this.nominal,
    this.lampiranPath,
    this.komentar,
    this.sat1,
    this.sat2,
    this.vol1,
    this.vol2,
    this.harga,
>>>>>>> c0d5a63 (fix masalah field semua yang ga ke show di halaman utama)
    this.rawData,
  });

  factory LpjItem.fromJson(Map<String, dynamic> json) {
    String? catName;
    if (json['kategori'] is Map) {
      catName = json['kategori']['nama_kategori'];
    } else if (json['kategori'] is String) {
      catName = json['kategori'];
    }

    return LpjItem(
<<<<<<< HEAD
      id: json['lpj_item_id'] ?? json['id'] ?? 0,
      uraian: json['uraian'],
      rincian: json['rincian'],
      realisasi: (json['realisasi'] as num?)?.toDouble() ?? 0.0,
      lampiranPath: json['file_bukti'],
      kategoriNama: catName,
      komentar: json['komentar'],
=======
      id: json['id'] ?? 0,
      kategoriNama: json['kategori'] is Map ? json['kategori']['nama_kategori'] : (json['jenis_belanja'] ?? 'Lainnya'),
      uraian: json['uraian'],
      rincian: json['rincian'],
      totalHarga: json['total_harga'] != null ? double.tryParse(json['total_harga'].toString()) : 0.0,
      realisasi: json['realisasi'] != null ? double.tryParse(json['realisasi'].toString()) : 0.0,
      nominal: json['nominal'] != null ? double.tryParse(json['nominal'].toString()) : 0.0,
      lampiranPath: json['lampiran'] ?? json['file_bukti'],
      komentar: json['komentar'],
      sat1: json['sat1'],
      sat2: json['sat2'],
      vol1: json['vol1'] != null ? double.tryParse(json['vol1'].toString()) : 0.0,
      vol2: json['vol2'] != null ? double.tryParse(json['vol2'].toString()) : 0.0,
      harga: json['harga'] != null ? double.tryParse(json['harga'].toString()) : 0.0,
>>>>>>> c0d5a63 (fix masalah field semua yang ga ke show di halaman utama)
      rawData: json,
    );
  }
}
