class Notifikasi {
  final int id;
  final String tipe;
  final String status;
  final Map<String, dynamic>? konten;
  final int? idReferensi;
  final DateTime? createdAt;

  Notifikasi({
    required this.id,
    required this.tipe,
    required this.status,
    this.konten,
    this.idReferensi,
    this.createdAt,
  });

  bool get isRead => status == 'DIBACA' || status == 'READ';

  String get pesan {
    if (konten == null) return 'Informasi sistem baru.';
    return konten!['pesan'] ?? konten!['message'] ?? konten!['deskripsi'] ?? 'Pembaruan status kegiatan.';
  }

  factory Notifikasi.fromJson(Map<String, dynamic> json) {
    DateTime? parsedDate;
    if (json['created_at'] != null) {
      try {
        parsedDate = DateTime.parse(json['created_at']);
      } catch (_) {
        parsedDate = null;
      }
    }

    return Notifikasi(
      id: json['id'] ?? 0,
      tipe: json['tipe'] ?? 'INFO',
      status: json['status'] ?? 'BELUM DIBACA',
      konten: json['konten'],
      idReferensi: json['id_referensi'],
      createdAt: parsedDate,
    );
  }
}
