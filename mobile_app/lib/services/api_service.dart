import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../main.dart'; // To access navigatorKey
import '../views/login_view.dart';

class ApiService {
  final Dio _dio = Dio();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  // Hardened Base URL: priority to environment variable, then platform default
  static const String _defaultUrl = kIsWeb
      ? 'http://127.0.0.1:8000/api'
      : 'http://10.0.2.2:8000/api'; // Standard Android Emulator loopback

  static String _baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: _defaultUrl,
  );

  static String get baseUrl => _baseUrl;
  
  static set baseUrl(String url) {
    _baseUrl = url;
  }

  ApiService() {
    _dio.options.baseUrl = _baseUrl;
    _dio.options.connectTimeout = const Duration(seconds: 15);
    _dio.options.receiveTimeout = const Duration(seconds: 15);
    _dio.options.headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    };
    
    // Add Interceptors
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Sync with dynamic baseUrl changes
          options.baseUrl = _baseUrl;
          
          // Inject Sanctum Token
          final token = await _storage.read(key: 'auth_token');
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException e, handler) async {
          if (kDebugMode) {
            print('API Error [${e.response?.statusCode}]: ${e.requestOptions.path}');
          }

          // 401 Unauthorized: Session expired or invalid
          if (e.response?.statusCode == 401) {
            await deleteToken();
            
            // Trigger Global Redirect to Login
            if (navigatorKey.currentState != null) {
              navigatorKey.currentState!.pushAndRemoveUntil(
                MaterialPageRoute(builder: (_) => const LoginView()),
                (route) => false,
              );
            }
          }

          return handler.next(e);
        },
      ),
    );
  }

  Dio get client => _dio;

  // Token Management Helpers
  Future<void> saveToken(String token) async {
    await _storage.write(key: 'auth_token', value: token);
  }

  Future<void> deleteToken() async {
    await _storage.delete(key: 'auth_token');
  }

  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }
}
