import 'package:flutter/material.dart';
import '../services/akun_service.dart';
import 'auth_provider.dart';

class AkunProvider with ChangeNotifier {
  final AkunService _service;

  AkunProvider(this._service);

  bool _isLoading = false;
  String _errorMessage = '';

  bool get isLoading => _isLoading;
  String get errorMessage => _errorMessage;

  Future<bool> updateProfile(AuthProvider authProvider, String rolePrefix, {String? nama, String? email, String? password, String? passwordConfirmation}) async {
    _isLoading = true;
    notifyListeners();

    Map<String, dynamic> payload = {};
    if (nama != null && nama.isNotEmpty) payload['nama'] = nama;
    if (email != null && email.isNotEmpty) payload['email'] = email;
    if (password != null && password.isNotEmpty) {
      payload['password'] = password;
      payload['password_confirmation'] = passwordConfirmation;
    }

    final result = await _service.updateProfile(rolePrefix, payload);

    _isLoading = false;
    notifyListeners();

    if (result['success']) {
      // Re-fetch user profile from /me (handled by auth provider)
      await authProvider.loadSession();
      return true;
    } else {
      _errorMessage = result['message'];
      return false;
    }
  }
}
