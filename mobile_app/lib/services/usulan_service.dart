import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'api_service.dart';
import '../models/kegiatan.dart';
import '../models/lpj.dart';

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

  // Get USULAN Detail (List usulan context)
  Future<Map<String, dynamic>> getUsulanDetail(int id) async {
    try {
      final response = await _apiService.client.get('/v1/admin/usulan/$id');
      if (response.data['success'] == true) {
        return {
          'success': true,
          'data': Kegiatan.fromJson(response.data['data']),
        };
      }
      return {'success': false, 'message': 'Gagal memuat detail usulan.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  // Get KEGIATAN Detail (Phase 2 context)
  Future<Map<String, dynamic>> getKegiatanDetail(int id) async {
    try {
      final response = await _apiService.client.get('/v1/admin/kegiatan/$id');
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

  // Submit Rincian (Add PJ, dates, and letter)
  Future<Map<String, dynamic>> submitRincian(int id, Map<String, dynamic> data, String? filePath) async {
    try {
      final formDataMap = {
        'kegiatan_id': id,
        ...data,
      };

      if (filePath != null) {
        formDataMap['surat_pengantar'] = await MultipartFile.fromFile(
          filePath,
          filename: filePath.split('/').last,
        );
      }

      final formData = FormData.fromMap(formDataMap);
      final response = await _apiService.client.post(
        '/v1/admin/kegiatan/submit-rincian',
        data: formData,
      );

      if (response.data['success'] == true) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Rincian berhasil disubmit.',
        };
      }
      return {'success': false, 'message': 'Gagal mensubmit rincian.'};
    } on DioException catch (e) {
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

  // Get paginated list of LPJ
  Future<Map<String, dynamic>> getLpjs({int page = 1}) async {
    try {
      final response = await _apiService.client.get('/v1/admin/lpj', queryParameters: {'page': page});
      if (response.data['success'] == true) {
        final List<dynamic> listData = response.data['data'] ?? [];
        return {
          'success': true,
          'data': listData.map((e) => Lpj.fromJson(e)).toList(),
          'last_page': response.data['meta']?['last_page'] ?? 1,
        };
      }
      return {'success': false, 'message': 'Gagal memuat data LPJ.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    }
  }

  // Get LPJ Detail
  Future<Map<String, dynamic>> getLpjDetail(int id) async {
    try {
      final response = await _apiService.client.get('/v1/admin/lpj/$id');
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

  // Submit LPJ Bukti
  Future<Map<String, dynamic>> uploadLpjBukti(int lpjId, int rabItemId, String filePath) async {
    try {
      final formData = FormData.fromMap({
        'lpj_id': lpjId,
        'rab_item_id': rabItemId,
        'file': await MultipartFile.fromFile(filePath, filename: filePath.split('/').last),
      });

      final response = await _apiService.client.post('/v1/admin/lpj/upload-bukti', data: formData);
      if (response.data['success'] == true) {
        return {'success': true, 'message': 'Bukti berhasil diunggah.'};
      }
      return {'success': false, 'message': 'Gagal mengunggah bukti.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Gagal mengunggah bukti.'};
    }
  }

  // Final LPJ Submit
  Future<Map<String, dynamic>> submitLpj(int kegiatanId, List<Map<String, dynamic>> items) async {
    try {
      final response = await _apiService.client.post('/v1/admin/lpj/submit', data: {
        'kegiatan_id': kegiatanId,
        'items': items,
      });
      if (response.data['success'] == true) {
        return {'success': true, 'message': 'LPJ berhasil disubmit.'};
      }
      return {'success': false, 'message': 'Gagal mensubmit LPJ.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Gagal mensubmit LPJ.'};
    }
  }

  // Resubmit KAK (after revision)
  Future<Map<String, dynamic>> resubmitUsulan(int id) async {
    try {
      final response = await _apiService.client.post('/v1/admin/kak/$id/resubmit');
      if (response.data['success'] == true) {
        return {'success': true, 'message': 'KAK berhasil diajukan kembali.'};
      }
      return {'success': false, 'message': 'Gagal mengajukan kembali KAK.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Gagal mengajukan kembali KAK.'};
    }
  }
}
