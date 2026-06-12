import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import '../services/totp_service.dart';

enum AuthStatus {
  initial,
  authenticating,
  authenticated,
  totpRequired,
  unauthenticated,
}

class AuthProvider extends ChangeNotifier {
  final AuthService _authService;
  final TotpService _totpService = TotpService();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  AuthStatus _status = AuthStatus.initial;
  User? _currentUser;
  bool _isLoading = false;
  String _errorMessage = '';
  
  // CAPTCHA details
  String _captchaKey = '';
  String _captchaCodeText = ''; 
  bool _isCaptchaLoading = false;
  
  // 2FA TOTP configuration state
  bool _isTotpEnabled = false;
  String _totpSecret = '';
  String _totpQrUrl = '';

  AuthProvider(this._authService) {
    Future.microtask(() => loadSession());
  }

  // Getters
  AuthStatus get status => _status;
  User? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  String get errorMessage => _errorMessage;
  String get captchaKey => _captchaKey;
  String get captchaCodeText => _captchaCodeText;
  bool get isCaptchaLoading => _isCaptchaLoading;
  bool get isTotpEnabled => _isTotpEnabled;
  String get totpSecret => _totpSecret;
  String get totpQrUrl => _totpQrUrl;
  String get currentBaseUrl => ApiService.baseUrl;

  // Set error helper
  void _setError(String msg) {
    _errorMessage = msg;
    notifyListeners();
  }

  // Update dynamic API Base URL
  void updateBaseUrl(String newUrl) {
    ApiService.baseUrl = newUrl;
    notifyListeners();
  }

  // Load user session on startup
  Future<void> loadSession() async {
    _isLoading = true;
    notifyListeners();

    final user = await _authService.checkSession();
    if (user != null) {
      _currentUser = user;
      _status = AuthStatus.authenticated;
    } else {
      _status = AuthStatus.unauthenticated;
      refreshCaptcha();
    }
    _isLoading = false;
    notifyListeners();
  }

  // Load/Refresh CAPTCHA
  Future<void> refreshCaptcha() async {
    _isCaptchaLoading = true;
    notifyListeners();

    try {
      final result = await _authService.fetchCaptcha();
      if (result['success']) {
        _captchaKey = result['key'];
        _captchaCodeText = result['code'];
      } else {
        _captchaKey = 'error'; 
        _captchaCodeText = '- - - -';
      }
    } catch (e) {
      _captchaKey = 'error';
      _captchaCodeText = '- - - -';
    } finally {
      _isCaptchaLoading = false;
      notifyListeners();
    }
  }

  // Login via standard credentials + CAPTCHA
  Future<bool> loginWithEmail(String email, String password, String captchaInput) async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();

    final result = await _authService.login(
      email: email,
      password: password,
      captchaKey: _captchaKey,
      captchaCode: captchaInput,
    );

    _isLoading = false;
    if (result['success']) {
      _currentUser = result['user'];
      _status = AuthStatus.authenticated;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'] ?? 'Login gagal.';
      // Refresh captcha after failure so new captcha is ready
      await refreshCaptcha();
      return false;
    }
  }

  // Login via Biometrics (unlock stored Sanctum session)
  Future<bool> loginWithBiometrics() async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();

    final result = await _authService.loginWithBiometrics();
    _isLoading = false;

    if (result['success']) {
      _currentUser = result['user'];
      _status = AuthStatus.authenticated;
      notifyListeners();
      return true;
    } else {
      _setError(result['message']);
      return false;
    }
  }

  // Login via Google
  Future<bool> loginWithGoogle() async {
    _setError('Login Google saat ini tidak tersedia.');
    return false;
  }

  // Register simulation
  Future<bool> register(String name, String email, String password, String department) async {
    _setError('Registrasi saat ini hanya dapat dilakukan melalui Admin.');
    return false;
  }

  // Forgot Password
  Future<bool> forgotPassword(String email) async {
    _setError('Fitur reset password sedang dalam pemeliharaan.');
    return false;
  }

  // ⚠️ SECURITY NOTE: TOTP saat ini dikelola sepenuhnya di sisi Flutter.
  // Server (Laravel) BELUM memiliki endpoint untuk verifikasi TOTP.
  // TODO: Migrasi ke server-side verification saat backend siap.

  // Initialize/Setup Google Authenticator 2FA Secret
  void initTotpSetup() {
    // ⚠️ SECURITY NOTE: This logic should happen on the server.
    if (_currentUser == null) return;
    _totpSecret = _totpService.generateSecret();
    _totpQrUrl = _totpService.getOtpAuthUrl(_currentUser!.email, _totpSecret);
    notifyListeners();
  }

  // Confirm and Save Google Authenticator 2FA
  Future<bool> confirmTotpSetup(String code) async {
    // ⚠️ SECURITY NOTE: Validation should happen on the server via POST /v1/auth/2fa/verify.
    if (_totpSecret.isEmpty) return false;
    
    final verified = _totpService.verifyCode(_totpSecret, code);
    if (verified) {
      _isTotpEnabled = true;
      await _storage.write(key: 'totp_enabled', value: 'true');
      await _storage.write(key: 'totp_secret', value: _totpSecret);
      notifyListeners();
      return true;
    } else {
      _setError('Kode otentikasi tidak valid.');
      return false;
    }
  }

  // Disable 2FA
  Future<void> disableTotp() async {
    // ⚠️ SECURITY NOTE: Server should revoke the secret.
    _isTotpEnabled = false;
    _totpSecret = '';
    _totpQrUrl = '';
    await _storage.delete(key: 'totp_enabled');
    await _storage.delete(key: 'totp_secret');
    notifyListeners();
  }

  // Logout
  Future<void> logout() async {
    _isLoading = true;
    notifyListeners();

    await _authService.logout();
    _currentUser = null;
    _status = AuthStatus.unauthenticated;
    _isLoading = false;
    refreshCaptcha();
    notifyListeners();
  }
}
