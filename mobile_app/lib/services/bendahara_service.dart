import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/kegiatan.dart';
import '../models/lpj.dart';

class BendaharaService {
  final ApiService _apiService;

  BendaharaService(this._apiService);

  // === Pencairan Dana ===

  Future<Map<String, dynamic>> getPencairanList({int page = 1}) async {
    try {
      final response = await _apiService.client.get('/v1/bendahara/pencairan', queryParameters: {'page': page});
      
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
      return {'success': false, 'message': 'Gagal memuat daftar pencairan.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> getPencairanDetail(int id) async {
    try {
      final response = await _apiService.client.get('/v1/bendahara/pencairan/$id');
      if (response.data['success'] == true) {
        return {
          'success': true,
          'data': Kegiatan.fromJson(response.data['data']),
        };
      }
      return {'success': false, 'message': 'Gagal memuat detail kegiatan.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> prosesPencairan(Map<String, dynamic> data) async {
    try {
      final response = await _apiService.client.post('/v1/bendahara/pencairan/proses', data: data);
      
      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Dana berhasil dicairkan.',
        };
      }
      return {'success': false, 'message': 'Gagal memproses pencairan.'};
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

  // === Verifikasi LPJ ===

  Future<Map<String, dynamic>> getLpjList({int page = 1}) async {
    try {
      final response = await _apiService.client.get('/v1/bendahara/lpj', queryParameters: {'page': page});
      
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        final List<Lpj> lpjs = listData.map((e) => Lpj.fromJson(e)).toList();
        
        return {
          'success': true,
          'data': lpjs,
          'last_page': response.data['meta']?['last_page'] ?? 1,
          'total': response.data['meta']?['total'] ?? 0,
        };
      }
      return {'success': false, 'message': 'Gagal memuat daftar LPJ.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> getLpjDetail(int id) async {
    try {
      final response = await _apiService.client.get('/v1/bendahara/lpj/$id');
      if (response.data['success'] == true) {
        return {
          'success': true,
          'data': Lpj.fromJson(response.data['data']),
        };
      }
      return {'success': false, 'message': 'Gagal memuat detail LPJ.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  Future<Map<String, dynamic>> prosesLpj(int lpjId, String aksi, {String komentar = ''}) async {
    try {
      final response = await _apiService.client.post('/v1/bendahara/lpj/proses', data: {
        'lpj_id': lpjId,
        'aksi': aksi,
        'komentar': komentar,
      });
      
      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'LPJ berhasil diproses.',
        };
      }
      return {'success': false, 'message': 'Gagal memproses LPJ.'};
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
