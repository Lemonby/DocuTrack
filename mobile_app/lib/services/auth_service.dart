import 'package:dio/dio.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'api_service.dart';
import 'biometric_service.dart';
import '../models/user.dart';

class AuthService {
  final ApiService _apiService;
  final BiometricService _biometricService = BiometricService();
  final GoogleSignIn _googleSignIn = GoogleSignIn(
    scopes: ['email', 'profile'],
    clientId: 'demo-client-id.apps.googleusercontent.com', // Required for Flutter Web
  );

  AuthService(this._apiService);

  // Fetch CAPTCHA from API
  Future<Map<String, dynamic>> fetchCaptcha() async {
    try {
      final response = await _apiService.client.post('/v1/captcha');
      if (response.data['success'] == true) {
        return {
          'success': true,
          'key': response.data['data']['captcha_key'],
          'code': response.data['data']['captcha_code'], // Returns the text to display if no image renderer is needed
        };
      }
      return {'success': false, 'message': 'Gagal memuat CAPTCHA.'};
    } on DioException catch (e) {
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Terjadi kesalahan jaringan saat memuat CAPTCHA.'
      };
    }
  }

  // Login with standard credentials & CAPTCHA
  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
    required String captchaKey,
    required String captchaCode,
  }) async {
    try {
      final response = await _apiService.client.post(
        '/v1/login',
        data: {
          'email': email,
          'password': password,
          'captcha_key': captchaKey,
          'captcha_code': captchaCode,
        },
      );

      if (response.data['success'] == true) {
        final token = response.data['data']['token'];
        final userData = response.data['data']['user'];
        
        // Save token to secure storage
        await _apiService.saveToken(token);
        
        final user = User.fromJson(userData);
        return {
          'success': true,
          'user': user,
          'token': token,
        };
      }
      return {'success': false, 'message': response.data['message'] ?? 'Login gagal.'};
    } on DioException catch (e) {
      final errorMsg = e.response?.data['message'] ?? 'Email, password, atau CAPTCHA salah.';
      return {'success': false, 'message': errorMsg};
    }
  }

  // Check if session token is valid and retrieve user data (auto-login check)
  Future<User?> checkSession() async {
    final token = await _apiService.getToken();
    if (token == null) return null;

    try {
      final response = await _apiService.client.get('/v1/me');
      if (response.data['success'] == true) {
        return User.fromJson(response.data['data']);
      }
    } on DioException {
      // Token is invalid/expired
      await _apiService.deleteToken();
    }
    return null;
  }

  // Logout
  Future<bool> logout() async {
    try {
      await _apiService.client.post('/v1/logout');
    } catch (_) {
      // Ignore network errors on logout to allow offline state cleanup
    }
    await _apiService.deleteToken();
    return true;
  }

  // Biometric login execution (unlock stored token)
  Future<Map<String, dynamic>> loginWithBiometrics() async {
    final hasToken = await _apiService.getToken() != null;
    if (!hasToken) {
      return {'success': false, 'message': 'Belum ada akun yang masuk di perangkat ini.'};
    }

    final isSupported = await _biometricService.isSupported();
    if (!isSupported) {
      return {'success': false, 'message': 'Perangkat tidak mendukung biometrik.'};
    }

    final authenticated = await _biometricService.authenticate();
    if (!authenticated) {
      return {'success': false, 'message': 'Otentikasi biometrik gagal.'};
    }

    // Attempt to load current user with stored token
    final user = await checkSession();
    if (user != null) {
      return {'success': true, 'user': user};
    }
    
    return {'success': false, 'message': 'Sesi telah kedaluwarsa. Silakan masuk manual.'};
  }

  // OPSI B: Features not yet implemented in backend
  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    String? department,
  }) async {
    throw UnimplementedError('Fitur pendaftaran belum tersedia di backend.');
  }

  Future<Map<String, dynamic>> loginWithGoogle() async {
    throw UnimplementedError('Fitur login Google belum tersedia di backend.');
  }

  Future<Map<String, dynamic>> forgotPassword(String email) async {
    throw UnimplementedError('Fitur lupa password belum tersedia di backend.');
  }
}
