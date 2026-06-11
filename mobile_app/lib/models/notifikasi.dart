class Notifikasi {
  final int id;
  final String tipe;
  final String status;
  final Map<String, dynamic>? konten;
  final int? idReferensi;
  final String? createdAt;

  Notifikasi({
    required this.id,
    required this.tipe,
    required this.status,
    this.konten,
    this.idReferensi,
    this.createdAt,
  });

  bool get isRead => status == 'DIBACA';

  factory Notifikasi.fromJson(Map<String, dynamic> json) {
    return Notifikasi(
      id: json['id'] ?? 0,
      tipe: json['tipe'] ?? 'INFO',
      status: json['status'] ?? 'BELUM DIBACA',
      konten: json['konten'],
      idReferensi: json['id_referensi'],
      createdAt: json['created_at'],
    );
  }
}
