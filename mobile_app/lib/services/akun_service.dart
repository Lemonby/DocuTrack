import 'package:dio/dio.dart';
import 'api_service.dart';

class AkunService {
  final ApiService _apiService;

  AkunService(this._apiService);

  Future<Map<String, dynamic>> getProfile(String rolePrefix) async {
    try {
      final response = await _apiService.client.get('/v1/$rolePrefix/akun');
      if (response.data['success'] == true) {
        return {
          'success': true,
          'data': response.data['data'],
        };
      }
      return {'success': false, 'message': 'Gagal memuat profil.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> updateProfile(String rolePrefix, Map<String, dynamic> data) async {
    try {
      final response = await _apiService.client.put('/v1/$rolePrefix/akun', data: data);
      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Profil berhasil diperbarui.',
          'data': response.data['data'],
        };
      }
      return {'success': false, 'message': 'Gagal memperbarui profil.'};
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
