import 'package:flutter/material.dart';
import '../models/iku.dart';
import '../services/iku_service.dart';

class IkuProvider extends ChangeNotifier {
  final IkuService _ikuService;

  List<Iku> _ikus = [];
  bool _isLoading = false;
  String _errorMessage = '';
  int _currentPage = 1;
  int _lastPage = 1;
  int _totalItems = 0;

  IkuProvider(this._ikuService);

  // Getters
  List<Iku> get ikus => _ikus;
  bool get isLoading => _isLoading;
  String get errorMessage => _errorMessage;
  int get currentPage => _currentPage;
  int get lastPage => _lastPage;
  int get totalItems => _totalItems;

  // Clear states helper
  void clearState() {
    _ikus = [];
    _currentPage = 1;
    _lastPage = 1;
    _totalItems = 0;
    _errorMessage = '';
  }

  // Fetch paginated IKU items
  Future<void> fetchIkus({int page = 1, bool isRefresh = false}) async {
    if (_isLoading) return;
    
    _isLoading = true;
    _errorMessage = '';
    if (isRefresh || page == 1) {
      clearState();
    }
    notifyListeners();

    final result = await _ikuService.getIkus(page: page);
    _isLoading = false;

    if (result['success']) {
      final List<Iku> newIkus = result['ikus'];
      _currentPage = page;
      _lastPage = result['last_page'];
      _totalItems = result['total'];
      
      if (page == 1) {
        _ikus = newIkus;
      } else {
        // Append unique items
        for (var item in newIkus) {
          if (!_ikus.any((existing) => existing.id == item.id)) {
            _ikus.add(item);
          }
        }
      }
    } else {
      _errorMessage = result['message'];
    }
    notifyListeners();
  }

  // Create Iku
  Future<bool> createIku({
    String? code,
    required String performanceIndicator,
    String? description,
    String? target,
    int? year,
  }) async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();

    final result = await _ikuService.createIku(
      code: code,
      performanceIndicator: performanceIndicator,
      description: description,
      target: target,
      year: year,
    );

    _isLoading = false;
    if (result['success']) {
      // Insert new item at top
      _ikus.insert(0, result['iku']);
      _totalItems += 1;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      notifyListeners();
      return false;
    }
  }

  // Update Iku
  Future<bool> updateIku({
    required int id,
    String? code,
    required String performanceIndicator,
    String? description,
    String? target,
    int? year,
  }) async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();

    final result = await _ikuService.updateIku(
      id: id,
      code: code,
      performanceIndicator: performanceIndicator,
      description: description,
      target: target,
      year: year,
    );

    _isLoading = false;
    if (result['success']) {
      final updatedItem = result['iku'] as Iku;
      final index = _ikus.indexWhere((item) => item.id == id);
      if (index != -1) {
        _ikus[index] = updatedItem;
      }
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      notifyListeners();
      return false;
    }
  }

  // Delete Iku
  Future<bool> deleteIku(int id) async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();

    final result = await _ikuService.deleteIku(id);
    _isLoading = false;
    if (result['success']) {
      _ikus.removeWhere((item) => item.id == id);
      _totalItems -= 1;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      notifyListeners();
      return false;
    }
  }
}
