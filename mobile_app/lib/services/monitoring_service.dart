import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/kegiatan.dart';

class MonitoringService {
  final ApiService _apiService;

  MonitoringService(this._apiService);

  Future<Map<String, dynamic>> getMonitoringList(String rolePrefix, {int page = 1}) async {
    try {
      // Endpoint is 'riwayat' for verifikator, 'monitoring' for others.
      String endpoint = rolePrefix == 'verifikator' ? 'riwayat' : 'monitoring';
      
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
    }
  }
}
