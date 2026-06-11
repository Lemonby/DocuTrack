class Kegiatan {
  final int id;
  final String namaKegiatan;
  final String? prodiPenyelenggara;
  final String? pemilikKegiatan;
  final String? nimPelaksana;
  final String? jurusanPenyelenggara;
  final int? statusId;
  final String? statusNama;
  final int? posisiId;
  final int? workflowProgress;
  final String? tanggalMulai;
  final String? tanggalSelesai;
  final String? createdAt;
  final Map<String, dynamic>? rawData;

  Kegiatan({
    required this.id,
    required this.namaKegiatan,
    this.prodiPenyelenggara,
    this.pemilikKegiatan,
    this.nimPelaksana,
    this.jurusanPenyelenggara,
    this.statusId,
    this.statusNama,
    this.posisiId,
    this.workflowProgress,
    this.tanggalMulai,
    this.tanggalSelesai,
    this.createdAt,
    this.rawData,
  });

  factory Kegiatan.fromJson(Map<String, dynamic> json) {
    return Kegiatan(
      id: json['id'] ?? 0,
      namaKegiatan: json['nama_kegiatan'] ?? 'Tanpa Judul',
      prodiPenyelenggara: json['prodi_penyelenggara'],
      pemilikKegiatan: json['pemilik_kegiatan'],
      nimPelaksana: json['nim_pelaksana'],
      jurusanPenyelenggara: json['jurusan_penyelenggara'],
      statusId: json['status']?['id'],
      statusNama: json['status']?['nama'] ?? 'Menunggu',
      posisiId: json['posisi_id'],
      workflowProgress: json['workflow_progress'],
      tanggalMulai: json['tanggal_mulai'],
      tanggalSelesai: json['tanggal_selesai'],
      createdAt: json['created_at'],
      rawData: json,
    );
  }
}
