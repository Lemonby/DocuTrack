import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'api_service.dart';
import '../models/kegiatan.dart';

class UsulanService {
  final ApiService _apiService;

  UsulanService(this._apiService);

  // Get paginated list of Usulan
  Future<Map<String, dynamic>> getUsulans({int page = 1}) async {
    try {
      final response = await _apiService.client.get('/v1/admin/usulan', queryParameters: {'page': page});
      
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        final List<Kegiatan> kegiatans = listData.map((e) => Kegiatan.fromJson(e)).toList();
        
        return {
          'success': true,
          'usulans': kegiatans,
          'last_page': response.data['meta']?['last_page'] ?? 1,
          'total': response.data['meta']?['total'] ?? 0,
        };
      }
      return {'success': false, 'message': 'Gagal memuat data Usulan.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      return {'success': false, 'message': errorMsg};
    }
  }

  // Get paginated list of Kegiatan (Rincian)
  Future<Map<String, dynamic>> getKegiatans({int page = 1}) async {
    try {
      final response = await _apiService.client.get('/v1/admin/kegiatan', queryParameters: {'page': page});
      
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        final List<Kegiatan> kegiatans = listData.map((e) => Kegiatan.fromJson(e)).toList();
        
        return {
          'success': true,
          'kegiatans': kegiatans,
          'last_page': response.data['meta']?['last_page'] ?? 1,
          'total': response.data['meta']?['total'] ?? 0,
        };
      }
      return {'success': false, 'message': 'Gagal memuat data Kegiatan.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      return {'success': false, 'message': errorMsg};
    }
  }

  // Create new Usulan (KAK)
  Future<Map<String, dynamic>> createUsulan(Map<String, dynamic> data) async {
    try {
      final response = await _apiService.client.post(
        '/v1/admin/usulan',
        data: data,
      );

      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Usulan berhasil ditambahkan.',
        };
      }
      return {'success': false, 'message': 'Gagal membuat usulan.'};
    } on DioException catch (e) {
      // Handle validation errors from Laravel
      String errorMsg = 'Terjadi kesalahan jaringan.';
      if (e.response?.statusCode == 422) {
        final errors = e.response?.data['errors'] as Map<String, dynamic>?;
        if (errors != null && errors.isNotEmpty) {
          errorMsg = errors.values.first[0]; // Get first validation error message
        } else {
          errorMsg = e.response?.data['message'] ?? errorMsg;
        }
      } else {
        errorMsg = e.response?.data['message'] ?? errorMsg;
      }
      return {'success': false, 'message': errorMsg};
    }
  }

  // Update Usulan (Revision)
  Future<Map<String, dynamic>> updateUsulan(int id, Map<String, dynamic> data) async {
    try {
      final response = await _apiService.client.put(
        '/v1/admin/usulan/$id',
        data: data,
      );

      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Usulan berhasil direvisi.',
        };
      }
      return {'success': false, 'message': 'Gagal merevisi usulan.'};
    } on DioException catch (e) {
      if (kDebugMode) {
        print('DIO ERROR IN UPDATE USULAN: ${e.response?.statusCode}');
        print('DIO RESPONSE DATA: ${e.response?.data}');
        print('DIO ERROR MESSAGE: ${e.message}');
      }
      String errorMsg = 'Terjadi kesalahan jaringan.';
      if (e.response?.statusCode == 422) {
        final errors = e.response?.data['errors'] as Map<String, dynamic>?;
        if (errors != null && errors.isNotEmpty) {
          errorMsg = errors.values.first[0];
        } else {
          errorMsg = e.response?.data['message'] ?? errorMsg;
        }
      } else {
        errorMsg = e.response?.data['message'] ?? errorMsg;
      }
      return {'success': false, 'message': errorMsg};
    }
  }

  // Get Usulan Detail
  Future<Map<String, dynamic>> getUsulanDetail(int id) async {
    try {
      final response = await _apiService.client.get('/v1/admin/usulan/$id');
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

  // Delete Usulan
  Future<Map<String, dynamic>> deleteUsulan(int id) async {
    try {
      final response = await _apiService.client.delete('/v1/admin/usulan/$id');
      
      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Usulan berhasil dihapus.',
        };
      }
      return {'success': false, 'message': 'Gagal menghapus Usulan.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      return {'success': false, 'message': errorMsg};
    }
  }
}
