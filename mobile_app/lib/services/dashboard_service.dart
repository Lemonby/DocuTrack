import 'package:dio/dio.dart';
import 'api_service.dart';
import '../models/dashboard_data.dart';
import '../models/user.dart';

class DashboardService {
  final ApiService _apiService;

  DashboardService(this._apiService);

  Future<Map<String, dynamic>> fetchDashboardData(User user) async {
    try {
      String endpoint = '';
      
      if (user.isSuperAdmin) {
        endpoint = '/v1/superadmin/dashboard';
      } else if (user.isAdmin) {
        endpoint = '/v1/admin/dashboard';
      } else if (user.isPPK) {
        endpoint = '/v1/ppk/dashboard';
      } else if (user.isWadir) {
        endpoint = '/v1/wadir/dashboard';
      } else if (user.isDirektur) {
        endpoint = '/v1/direktur/dashboard';
      } else if (user.isVerifikator) {
        endpoint = '/v1/verifikator/dashboard';
      } else if (user.isBendahara) {
        endpoint = '/v1/bendahara/dashboard';
      } else {
        return {'success': false, 'message': 'Role tidak dikenali.'};
      }

      final response = await _apiService.client.get(endpoint);
      
      if (response.data['success'] == true) {
        final data = DashboardData.fromJson(response.data['data']);
        return {
          'success': true,
          'data': data,
        };
      }
      return {'success': false, 'message': 'Gagal memuat data dashboard.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Terjadi kesalahan jaringan.';
      return {'success': false, 'message': errorMsg};
    }
  }
}
