import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/kegiatan.dart';

class TelaahService {
  final ApiService _apiService;

  TelaahService(this._apiService);

  Future<Map<String, dynamic>> getTelaahList(String rolePrefix, {int page = 1}) async {
    try {
      final response = await _apiService.client.get('/v1/$rolePrefix/telaah', queryParameters: {'page': page});
      
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        final List<Kegiatan> kegiatans = listData.map((e) => Kegiatan.fromJson(e)).toList();
        
        return {
          'success': true,
          'data': kegiatans,
          'last_page': response.data['meta']?['last_page'] ?? 1,
          'total': response.data['meta']?['total'] ?? 0,
        };
      }
      return {'success': false, 'message': 'Gagal memuat daftar telaah.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> getTelaahDetail(String rolePrefix, int id) async {
    try {
      final response = await _apiService.client.get('/v1/$rolePrefix/telaah/$id');
      if (response.data['success'] == true) {
        return {
          'success': true,
          'data': Kegiatan.fromJson(response.data['data']),
        };
      }
      return {'success': false, 'message': 'Gagal memuat detail KAK.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> approve(String rolePrefix, int id, {required String kodeMak, required double danaDisetujui, String? catatan}) async {
    final data = <String, dynamic>{
      'kode_mak': kodeMak,
      'dana_disetujui': danaDisetujui,
    };
    if (catatan != null && catatan.isNotEmpty) data['catatan'] = catatan;
    return _processAction(rolePrefix, id, 'approve', data);
  }

  Future<Map<String, dynamic>> reject(String rolePrefix, int id, String? catatan) async {
    return _processAction(rolePrefix, id, 'reject', {'alasan': catatan ?? ''});
  }

  Future<Map<String, dynamic>> revise(String rolePrefix, int id, String? catatan) async {
    return _processAction(rolePrefix, id, 'revise', {'komentar': catatan ?? ''});
  }

  Future<Map<String, dynamic>> _processAction(String rolePrefix, int id, String action, Map<String, dynamic> data) async {
    try {
      final response = await _apiService.client.post('/v1/$rolePrefix/telaah/$id/$action', data: data);
      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Tindakan berhasil.',
        };
      }
      return {'success': false, 'message': 'Gagal memproses tindakan.'};
    } on DioException catch (e) {
      String errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      if (e.response?.statusCode == 422) {
        final errors = e.response?.data['errors'] as Map<String, dynamic>?;
        if (errors != null && errors.isNotEmpty) {
          errorMsg = errors.values.first[0];
        }
      }
      return {'success': false, 'message': errorMsg};
    }
  }
}
