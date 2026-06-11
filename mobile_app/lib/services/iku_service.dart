import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/iku.dart';

class IkuService {
  final ApiService _apiService;

  IkuService(this._apiService);

  // Get paginated list of IKU
  Future<Map<String, dynamic>> getIkus({int page = 1}) async {
    try {
      final response = await _apiService.client.get('/v1/superadmin/iku', queryParameters: {'page': page});
      
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data']['data'] ?? [];
        final List<Iku> ikus = listData.map((e) => Iku.fromJson(e)).toList();
        
        return {
          'success': true,
          'ikus': ikus,
          'last_page': response.data['data']['last_page'] ?? 1,
          'total': response.data['data']['total'] ?? 0,
        };
      }
      return {'success': false, 'message': 'Gagal memuat data IKU.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      return {'success': false, 'message': errorMsg};
    }
  }

  // Create IKU
  Future<Map<String, dynamic>> createIku({
    String? code,
    required String performanceIndicator,
    String? description,
    String? target,
    int? year,
  }) async {
    try {
      final response = await _apiService.client.post(
        '/v1/superadmin/iku',
        data: {
          'kode_iku': code,
          'indikator_kinerja': performanceIndicator,
          'deskripsi': description,
          'target': target,
          'tahun': year,
        },
      );

      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'IKU berhasil ditambahkan.',
          'iku': Iku.fromJson(response.data['data']),
        };
      }
      return {'success': false, 'message': 'Gagal menambahkan IKU.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      return {'success': false, 'message': errorMsg};
    }
  }

  // Update IKU
  Future<Map<String, dynamic>> updateIku({
    required int id,
    String? code,
    required String performanceIndicator,
    String? description,
    String? target,
    int? year,
  }) async {
    try {
      final response = await _apiService.client.put(
        '/v1/superadmin/iku/$id',
        data: {
          'kode_iku': code,
          'indikator_kinerja': performanceIndicator,
          'deskripsi': description,
          'target': target,
          'tahun': year,
        },
      );

      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'IKU berhasil diperbarui.',
          'iku': Iku.fromJson(response.data['data']),
        };
      }
      return {'success': false, 'message': 'Gagal memperbarui IKU.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      return {'success': false, 'message': errorMsg};
    }
  }

  // Delete IKU
  Future<Map<String, dynamic>> deleteIku(int id) async {
    try {
      final response = await _apiService.client.delete('/v1/superadmin/iku/$id');
      
      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'IKU berhasil dihapus.',
        };
      }
      return {'success': false, 'message': 'Gagal menghapus IKU.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      return {'success': false, 'message': errorMsg};
    }
  }
}
