import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/kegiatan.dart';

class MonitoringService {
  final ApiService _apiService;

  MonitoringService(this._apiService);

  Future<Map<String, dynamic>> getMonitoringList(String rolePrefix, {int page = 1}) async {
    // ... rest of method ...
  }

  Future<Map<String, dynamic>> getIntegritasRanking() async {
    try {
      final response = await _apiService.client.get('/v1/direktur/integritas');
      if (response.data['success'] == true) {
        return {
          'success': true,
          'data': response.data['data'], // This will be the list of ranked jurusans
        };
      }
      return {'success': false, 'message': 'Gagal memuat ranking integritas.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }
}
