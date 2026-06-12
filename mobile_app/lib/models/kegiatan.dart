class KegiatanStatus {
  final int id;
  final String nama;

  KegiatanStatus({required this.id, required this.nama});

  factory KegiatanStatus.fromJson(Map<String, dynamic> json) {
    return KegiatanStatus(
      id: json['id'] as int,
      nama: json['nama'] as String,
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'nama': nama,
  };
}

class TahapanPencairan {
  final int id;
  final int kegiatanId;
  final String namaTahapan;
  final double jumlahCair;
  final String? tanggalCair;
  final String? fileBukti;

  TahapanPencairan({
    required this.id,
    required this.kegiatanId,
    required this.namaTahapan,
    required this.jumlahCair,
    this.tanggalCair,
    this.fileBukti,
  });

  factory TahapanPencairan.fromJson(Map<String, dynamic> json) {
    return TahapanPencairan(
      id: json['id'] ?? 0,
      kegiatanId: json['kegiatan_id'] ?? 0,
      namaTahapan: json['nama_tahapan'] ?? '',
      jumlahCair: (json['jumlah_cair'] as num?)?.toDouble() ?? 0.0,
      tanggalCair: json['tanggal_cair'],
      fileBukti: json['file_bukti'],
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'kegiatan_id': kegiatanId,
    'nama_tahapan': namaTahapan,
    'jumlah_cair': jumlahCair,
    'tanggal_cair': tanggalCair,
    'file_bukti': fileBukti,
  };
}

class Kegiatan {
  final int id;
  final String namaKegiatan;
  final String? prodiPenyelenggara;
  final String? pemilikKegiatan;
  final String? nimPelaksana;
  final String? nip;
  final String? namaPj;
  final String? buktiMak;
  final String? jurusanPenyelenggara;
  final String? suratPengantar;
  final double? danaDiSetujui;
  final double? jumlahDicairkan;
  final String? metodePencairan;
  final String? tanggalPencairan;
  final String? catatanBendahara;
  final String? umpanBaikVerifikator;
  final KegiatanStatus? status;
  final List<TahapanPencairan>? tahapanPencairan;
  final int? posisiId;
  final int? workflowProgress;
  final String? tanggalMulai;
  final String? tanggalSelesai;
  final String? createdAt;
  final Map<String, dynamic>? rawData;

  // Backwards compatibility getters
  int? get statusId => status?.id;
  String? get statusNama => status?.nama;

  Kegiatan({
    required this.id,
    required this.namaKegiatan,
    this.prodiPenyelenggara,
    this.pemilikKegiatan,
    this.nimPelaksana,
    this.nip,
    this.namaPj,
    this.buktiMak,
    this.jurusanPenyelenggara,
    this.suratPengantar,
    this.danaDiSetujui,
    this.jumlahDicairkan,
    this.metodePencairan,
    this.tanggalPencairan,
    this.catatanBendahara,
    this.umpanBaikVerifikator,
    this.status,
    this.tahapanPencairan,
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
      nip: json['nip'],
      namaPj: json['nama_pj'],
      buktiMak: json['bukti_mak'],
      jurusanPenyelenggara: json['jurusan_penyelenggara'],
      suratPengantar: json['surat_pengantar'],
      danaDiSetujui: (json['dana_di_setujui'] as num?)?.toDouble(),
      jumlahDicairkan: (json['jumlah_dicairkan'] as num?)?.toDouble(),
      metodePencairan: json['metode_pencairan'],
      tanggalPencairan: json['tanggal_pencairan'],
      catatanBendahara: json['catatan_bendahara'],
      umpanBaikVerifikator: json['umpan_balik_verifikator'],
      status: json['status'] != null ? KegiatanStatus.fromJson(json['status']) : null,
      tahapanPencairan: json['tahapan_pencairan'] != null
          ? (json['tahapan_pencairan'] as List)
              .map((e) => TahapanPencairan.fromJson(e))
              .toList()
          : null,
      posisiId: json['posisi_id'],
      workflowProgress: json['workflow_progress'] as int?,
      tanggalMulai: json['tanggal_mulai'],
      tanggalSelesai: json['tanggal_selesai'],
      createdAt: json['created_at'],
      rawData: json,
    );
  }
}
