import 'package:flutter/material.dart';
import '../services/superadmin_service.dart';

class SuperadminProvider with ChangeNotifier {
  final SuperadminService _service;

  SuperadminProvider(this._service);

  bool _aiAgentsActive = false;
  bool _isLoadingAiSettings = false;
  bool _isSendingNotification = false;
  String _errorMessage = '';

  bool get aiAgentsActive => _aiAgentsActive;
  bool get isLoadingAiSettings => _isLoadingAiSettings;
  bool get isSendingNotification => _isSendingNotification;
  String get errorMessage => _errorMessage;

  Future<void> fetchAiSettings() async {
    _isLoadingAiSettings = true;
    notifyListeners();

    final result = await _service.getAiSettings();
    if (result['success']) {
      _aiAgentsActive = result['ai_agents_active'];
      _errorMessage = '';
    } else {
      _errorMessage = result['message'] ?? 'Error fetching AI settings';
    }

    _isLoadingAiSettings = false;
    notifyListeners();
  }

  Future<bool> updateAiSettings(bool isActive) async {
    _isLoadingAiSettings = true;
    notifyListeners();

    final result = await _service.updateAiSettings(isActive);
    if (result['success']) {
      _aiAgentsActive = isActive;
      _errorMessage = '';
      _isLoadingAiSettings = false;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'] ?? 'Error updating AI settings';
      _isLoadingAiSettings = false;
      notifyListeners();
      return false;
    }
  }

  Future<Map<String, dynamic>> sendNotification(String title, String message, {int? userId, bool sendEmail = true}) async {
    _isSendingNotification = true;
    notifyListeners();

    final result = await _service.sendNotification(title, message, userId: userId, sendEmail: sendEmail);

    _isSendingNotification = false;
    notifyListeners();

    return result;
  }
}
