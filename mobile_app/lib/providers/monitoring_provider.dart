import 'package:flutter/material.dart';
import '../models/kegiatan.dart';
import '../services/monitoring_service.dart';

class MonitoringProvider with ChangeNotifier {
  final MonitoringService _service;

  MonitoringProvider(this._service);

  List<Kegiatan> _items = [];
  bool _isLoading = false;
  String _errorMessage = '';
  int _currentPage = 1;
  int _lastPage = 1;

  List<Kegiatan> get items => _items;
  bool get isLoading => _isLoading;
  String get errorMessage => _errorMessage;
  int get currentPage => _currentPage;
  int get lastPage => _lastPage;

  Future<void> fetchList(String rolePrefix, {int page = 1, bool isRefresh = false, bool isRiwayat = false}) async {
    if (isRefresh) {
      _currentPage = 1;
      _items.clear();
      _errorMessage = '';
    }

    _isLoading = true;
    notifyListeners();

    final result = await _service.getMonitoringList(rolePrefix, page: page, isRiwayat: isRiwayat);

    if (result['success']) {
      if (isRefresh) {
        _items = result['data'];
      } else {
        _items.addAll(result['data']);
      }
      _currentPage = page;
      // We don't have exact meta parsing sometimes from Laravel's default pagination without explicit Resource wrapping,
      // But Assuming it returns data.data and meta.last_page or similar.
      _lastPage = result['last_page'] ?? 1;
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<Map<String, dynamic>> fetchIntegritasRanking() async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();

    final result = await _service.getIntegritasRanking();

    _isLoading = false;
    notifyListeners();
    return result;
  }
}
