import 'kegiatan.dart';

class Lpj {
  final int id;
  final Kegiatan? kegiatan;
  final String? statusNama;
  final String? submittedAt;
  final double? totalPengeluaran;
  final List<LpjItem> items;
  final Map<String, dynamic>? rawData;

  Lpj({
    required this.id,
    this.kegiatan,
    this.statusNama,
    this.submittedAt,
    this.totalPengeluaran,
    this.items = const [],
    this.rawData,
  });

  factory Lpj.fromJson(Map<String, dynamic> json) {
    List<LpjItem> parsedItems = [];
    double calculatedTotal = 0.0;
    
    if (json['items'] != null) {
      final itemList = json['items'] as List<dynamic>;
      parsedItems = itemList.map((e) => LpjItem.fromJson(e)).toList();
      for (var item in parsedItems) {
        calculatedTotal += item.realisasi ?? 0.0;
      }
    }

    String extractedStatus = 'Menunggu';
    if (json['status'] is Map) {
      extractedStatus = json['status']['nama'] ?? 'Menunggu';
    } else if (json['status'] is String) {
      extractedStatus = json['status'];
    }

    return Lpj(
      id: json['lpj_id'] ?? json['id'] ?? 0,
      kegiatan: json['kegiatan'] != null ? Kegiatan.fromJson(json['kegiatan']) : null,
      statusNama: extractedStatus,
      submittedAt: json['submitted_at'],
      totalPengeluaran: json['grand_total_realisasi'] != null 
          ? (json['grand_total_realisasi'] as num).toDouble()
          : calculatedTotal,
      items: parsedItems,
      rawData: json,
    );
  }
}

class LpjItem {
  final int id;
  final String? uraian;
  final String? rincian;
  final double? realisasi;
  final String? lampiranPath;
  final String? kategoriNama;
  final String? komentar;
  final Map<String, dynamic>? rawData;

  LpjItem({
    required this.id,
    this.uraian,
    this.rincian,
    this.realisasi,
    this.lampiranPath,
    this.kategoriNama,
    this.komentar,
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
      id: json['lpj_item_id'] ?? json['id'] ?? 0,
      uraian: json['uraian'],
      rincian: json['rincian'],
      realisasi: (json['realisasi'] as num?)?.toDouble() ?? 0.0,
      lampiranPath: json['file_bukti'],
      kategoriNama: catName,
      komentar: json['komentar'],
      rawData: json,
    );
  }
}
