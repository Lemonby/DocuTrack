import 'package:flutter/material.dart';
import '../models/dashboard_data.dart';
import '../models/user.dart';
import '../services/dashboard_service.dart';

class DashboardProvider with ChangeNotifier {
  final DashboardService _dashboardService;

  DashboardData? _dashboardData;
  bool _isLoading = false;
  String _errorMessage = '';

  DashboardProvider(this._dashboardService);

  DashboardData? get dashboardData => _dashboardData;
  bool get isLoading => _isLoading;
  String get errorMessage => _errorMessage;

  Future<void> fetchDashboardData(User user) async {
    _isLoading = true;
    _errorMessage = '';
    // Use Future.microtask to notify listeners safely before the async call
    Future.microtask(() => notifyListeners());

    final result = await _dashboardService.fetchDashboardData(user);

    if (result['success']) {
      _dashboardData = result['data'];
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }
  
  void clearData() {
    _dashboardData = null;
    _errorMessage = '';
    notifyListeners();
  }
}
