import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/master_models.dart';
import '../models/iku.dart';

class MasterService {
  final ApiService _apiService;

  MasterService(this._apiService);

  Future<Map<String, dynamic>> getJurusan() async {
    try {
      final response = await _apiService.client.get('/v1/jurusan');
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        return {
          'success': true,
          'data': listData.map((e) => Jurusan.fromJson(e)).toList(),
        };
      }
      return {'success': false, 'message': 'Gagal memuat Jurusan.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Kesalahan jaringan'};
    }
  }

  Future<Map<String, dynamic>> getProdi() async {
    try {
      final response = await _apiService.client.get('/v1/prodi');
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        return {
          'success': true,
          'data': listData.map((e) => Prodi.fromJson(e)).toList(),
        };
      }
      return {'success': false, 'message': 'Gagal memuat Prodi.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Kesalahan jaringan'};
    }
  }

  Future<Map<String, dynamic>> getWadir() async {
    try {
      final response = await _apiService.client.get('/v1/wadir');
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        return {
          'success': true,
          'data': listData.map((e) => Wadir.fromJson(e)).toList(),
        };
      }
      return {'success': false, 'message': 'Gagal memuat Wadir.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Kesalahan jaringan'};
    }
  }

  Future<Map<String, dynamic>> getIku() async {
    try {
      final response = await _apiService.client.get('/v1/iku');
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        return {
          'success': true,
          'data': listData.map((e) => Iku.fromJson(e)).toList(),
        };
      }
      return {'success': false, 'message': 'Gagal memuat IKU.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Kesalahan jaringan'};
    }
  }
}
