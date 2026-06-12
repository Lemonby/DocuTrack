class Jurusan {
  final dynamic id;
  final String namaJurusan;
  final List<Prodi> prodis;

  Jurusan({required this.id, required this.namaJurusan, this.prodis = const []});

  factory Jurusan.fromJson(Map<String, dynamic> json) {
    return Jurusan(
      id: json['nama_jurusan'] ?? json['jurusan_id'] ?? json['id'] ?? 0,
      namaJurusan: json['nama_jurusan'] ?? '',
      prodis: (json['prodis'] as List<dynamic>?)?.map((e) => Prodi.fromJson(e)).toList() ?? [],
    );
  }
}

class Prodi {
  final dynamic id;
  final String namaProdi;
  final dynamic jurusanId;

  Prodi({required this.id, required this.namaProdi, this.jurusanId});

  factory Prodi.fromJson(Map<String, dynamic> json) {
    return Prodi(
      id: json['nama_prodi'] ?? json['prodi_id'] ?? json['id'] ?? 0,
      namaProdi: json['nama_prodi'] ?? '',
      jurusanId: json['nama_jurusan'] ?? json['jurusan_id'],
    );
  }
}

class Wadir {
  final int id;
  final String namaWadir;
  final int wadirKe;

  Wadir({required this.id, required this.namaWadir, required this.wadirKe});

  factory Wadir.fromJson(Map<String, dynamic> json) {
    return Wadir(
      id: json['wadir_id'] ?? json['id'] ?? 0,
      namaWadir: json['nama_wadir'] ?? '',
      wadirKe: json['wadir_ke'] ?? 1,
    );
  }
}
