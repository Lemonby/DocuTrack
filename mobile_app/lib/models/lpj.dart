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
        calculatedTotal += item.nominal ?? 0.0;
      }
    }

    return Lpj(
      id: json['lpj_id'] ?? json['id'] ?? 0,
      kegiatan: json['kegiatan'] != null ? Kegiatan.fromJson(json['kegiatan']) : null,
      statusNama: json['status']?['nama'] ?? 'Menunggu',
      submittedAt: json['submitted_at'],
      totalPengeluaran: calculatedTotal,
      items: parsedItems,
      rawData: json,
    );
  }
}

class LpjItem {
  final int id;
  final String? keterangan;
  final double? nominal;
  final String? lampiranPath;
  final String? kategoriNama;

  LpjItem({
    required this.id,
    this.keterangan,
    this.nominal,
    this.lampiranPath,
    this.kategoriNama,
  });

  factory LpjItem.fromJson(Map<String, dynamic> json) {
    return LpjItem(
      id: json['id'] ?? 0,
      keterangan: json['keterangan'],
      nominal: json['nominal'] != null ? double.tryParse(json['nominal'].toString()) : 0.0,
      lampiranPath: json['lampiran'],
      kategoriNama: json['kategori']?['nama_kategori'],
    );
  }
}
