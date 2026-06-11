import 'package:dio/dio.dart';
import 'api_service.dart';

class SuperadminService {
  final ApiService _apiService;

  SuperadminService(this._apiService);

  Future<Map<String, dynamic>> getAiSettings() async {
    try {
      final response = await _apiService.client.get('/v1/superadmin/ai-monitoring/settings');
      if (response.data['success'] == true) {
        return {
          'success': true,
          'ai_agents_active': response.data['data']['ai_agents_active'] ?? false,
        };
      }
      return {'success': false, 'message': 'Gagal mengambil pengaturan AI.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> updateAiSettings(bool isActive) async {
    try {
      final response = await _apiService.client.put(
        '/v1/superadmin/ai-monitoring/settings',
        data: {'ai_agents_active': isActive},
      );
      if (response.data['success'] == true) {
        return {'success': true, 'message': response.data['message']};
      }
      return {'success': false, 'message': 'Gagal memperbarui pengaturan AI.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> sendNotification(String title, String message, {int? userId, bool sendEmail = true}) async {
    try {
      final response = await _apiService.client.post(
        '/v1/superadmin/send-notification',
        data: {
          'title': title,
          'message': message,
          if (userId != null) 'user_id': userId,
          'send_email': sendEmail,
          'type_log': 'INFO'
        },
      );
      if (response.data['success'] == true) {
        return {'success': true, 'message': response.data['message']};
      }
      return {'success': false, 'message': 'Gagal mengirim notifikasi.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }
}
