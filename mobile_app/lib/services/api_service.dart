import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiService {
  final Dio _dio = Dio();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  // Dynamic Base URL support (default accommodates XAMPP and artisan serve)
  static String _baseUrl = kIsWeb
      ? 'http://127.0.0.1:8000/api'
      : (Platform.isAndroid
          ? 'http://10.0.2.2:8000/api'
          : 'http://127.0.0.1:8000/api');

  static String get baseUrl => _baseUrl;
  
  static set baseUrl(String url) {
    _baseUrl = url;
  }

  ApiService() {
    _dio.options.connectTimeout = const Duration(seconds: 15);
    _dio.options.receiveTimeout = const Duration(seconds: 15);
    
    // Add Interceptors
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Update URL to current baseUrl
          options.baseUrl = _baseUrl;
          options.headers['Accept'] = 'application/json';
          
          // Get saved token
          final token = await _storage.read(key: 'auth_token');
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException e, handler) {
          if (kDebugMode) {
            print('API Error [${e.response?.statusCode}]: ${e.message}');
          }
          return handler.next(e);
        },
      ),
    );
  }

  Dio get client => _dio;

  // Save token helper
  Future<void> saveToken(String token) async {
    await _storage.write(key: 'auth_token', value: token);
  }

  // Delete token helper
  Future<void> deleteToken() async {
    await _storage.delete(key: 'auth_token');
  }

  // Get token helper
  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }
}
