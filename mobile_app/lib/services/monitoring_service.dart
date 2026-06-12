import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/kegiatan.dart';

class MonitoringService {
  final ApiService _apiService;

  MonitoringService(this._apiService);

  Future<Map<String, dynamic>> getMonitoringList(String rolePrefix, {int page = 1, bool isRiwayat = false}) async {
    try {
      // Endpoint is 'riwayat' for verifikator, or if isRiwayat is true, otherwise 'monitoring'.
      String endpoint = (rolePrefix == 'verifikator' || isRiwayat) ? 'riwayat' : 'monitoring';
      
      final response = await _apiService.client.get('/v1/$rolePrefix/$endpoint', queryParameters: {'page': page});
      
      if (response.data['success'] == true) {
        dynamic dataField = response.data['data'];
        List<dynamic> listData = [];
        int lastPage = 1;
        
        if (dataField is Map<String, dynamic>) {
          listData = dataField['data'] ?? [];
          lastPage = dataField['last_page'] ?? 1;
        } else if (dataField is List) {
          listData = dataField;
          lastPage = response.data['meta']?['last_page'] ?? 1;
        }

        final List<Kegiatan> items = listData.map((e) => Kegiatan.fromJson(e)).toList();
        
        return {
          'success': true,
          'data': items,
          'last_page': lastPage,
        };
      }
      return {'success': false, 'message': 'Gagal memuat riwayat.'};
    } on DioException catch (e) {
      return {'success': false, 'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan tidak terduga.'};
    }
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
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan tidak terduga.'};
    }
  }
}
