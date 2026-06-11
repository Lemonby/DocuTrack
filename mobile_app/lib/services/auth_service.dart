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

  // Register Simulation
  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    String? department,
  }) async {
    await Future.delayed(const Duration(seconds: 1)); // Simulate server delay
    
    // Check local database/simulation
    if (email.contains('taken')) {
      return {'success': false, 'message': 'Email sudah terdaftar.'};
    }

    // Mock create a SuperAdmin user for the sake of demo
    final mockUser = User(
      userId: 99,
      name: name,
      email: email,
      departmentName: department ?? 'Teknik Informatika',
      status: 'Aktif',
      role: 'SuperAdmin', // Grant superadmin for CRUD access in mobile demo
    );

    // Save a mock token
    await _apiService.saveToken('mock_sanctum_token_for_$email');

    return {
      'success': true,
      'message': 'Registrasi berhasil. Selamat datang!',
      'user': mockUser,
    };
  }

  // Forgot Password Simulation
  Future<Map<String, dynamic>> forgotPassword(String email) async {
    await Future.delayed(const Duration(seconds: 1));
    return {
      'success': true,
      'message': 'Instruksi reset password telah dikirim ke email Anda.',
    };
  }

  // Google Login (with simulation fallback)
  Future<Map<String, dynamic>> loginWithGoogle() async {
    try {
      // Attempt actual google sign in
      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn().timeout(
        const Duration(seconds: 5),
        onTimeout: () => throw Exception('Timeout Google Login'),
      );
      
      if (googleUser != null) {
        // Mock token issuing for Google account
        final token = 'mock_google_token_${googleUser.id}';
        await _apiService.saveToken(token);
        
        final user = User(
          userId: 100 + int.parse(googleUser.id.substring(0, 4)),
          name: googleUser.displayName ?? 'Google User',
          email: googleUser.email,
          status: 'Aktif',
          role: 'SuperAdmin', // Grant SuperAdmin for IKU CRUD testing convenience
        );
        
        return {'success': true, 'user': user};
      }
      return {'success': false, 'message': 'Login Google dibatalkan.'};
    } catch (e) {
      // Catch platform configuration errors (like missing debug SHA1 or services json)
      // and provide an elegant Simulation dialog alternative
      print('Google Sign In failed or unconfigured, running mock selection: $e');
      
      // Simulate Google selection
      await Future.delayed(const Duration(milliseconds: 800));
      final token = 'mock_google_token_12345';
      await _apiService.saveToken(token);
      
      final user = User(
        userId: 999,
        name: 'Demo Google User',
        email: 'demouser@gmail.com',
        status: 'Aktif',
        role: 'SuperAdmin', // Default to SuperAdmin for Demo
      );
      return {
        'success': true,
        'user': user,
        'simulated': true,
      };
    }
  }
}
