import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/notifikasi.dart';

class NotifikasiService {
  final ApiService _apiService;

  NotifikasiService(this._apiService);

  Future<Map<String, dynamic>> getNotifikasiList({int page = 1}) async {
    try {
      final response = await _apiService.client.get('/v1/notifikasi', queryParameters: {'page': page});
      
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        final List<Notifikasi> items = listData.map((e) => Notifikasi.fromJson(e)).toList();
        
        return {
          'success': true,
          'data': items,
          'unread': response.data['meta']?['unread'] ?? 0,
          'last_page': response.data['meta']?['last_page'] ?? 1,
        };
      }
      return {'success': false, 'message': 'Gagal memuat notifikasi.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<bool> markAsRead(int id) async {
    try {
      final response = await _apiService.client.post('/v1/notifikasi/$id/baca');
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }

  Future<bool> markAllAsRead() async {
    try {
      final response = await _apiService.client.post('/v1/notifikasi/baca-semua');
      return response.data['success'] == true;
    } catch (_) {
      return false;
    }
  }
}
