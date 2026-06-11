class Iku {
  final int id;
  final String? code; // kode_iku
  final String performanceIndicator; // indikator_kinerja
  final String? description; // deskripsi
  final String? target;
  final int? year; // tahun

  Iku({
    required this.id,
    this.code,
    required this.performanceIndicator,
    this.description,
    this.target,
    this.year,
  });

  factory Iku.fromJson(Map<String, dynamic> json) {
    return Iku(
      id: json['id'] ?? 0,
      code: json['kode_iku'],
      performanceIndicator: json['indikator_kinerja'] ?? '',
      description: json['deskripsi'],
      target: json['target'],
      year: json['tahun'] is String ? int.tryParse(json['tahun']) : json['tahun'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'kode_iku': code,
      'indikator_kinerja': performanceIndicator,
      'deskripsi': description,
      'target': target,
      'tahun': year,
    };
  }
}
