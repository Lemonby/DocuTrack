import 'package:flutter/material.dart';
import '../models/kegiatan.dart';
import '../services/telaah_service.dart';

class TelaahProvider with ChangeNotifier {
  final TelaahService _telaahService;

  TelaahProvider(this._telaahService);

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

  Future<void> fetchTelaahList(String rolePrefix, {int page = 1, bool isRefresh = false}) async {
    if (isRefresh) {
      _currentPage = 1;
      _items.clear();
      _errorMessage = '';
    }

    _isLoading = true;
    notifyListeners();

    final result = await _telaahService.getTelaahList(rolePrefix, page: page);

    if (result['success']) {
      if (isRefresh) {
        _items = result['data'];
      } else {
        _items.addAll(result['data']);
      }
      _currentPage = page;
      _lastPage = result['last_page'];
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<Kegiatan?> getTelaahDetail(String rolePrefix, int id) async {
    _isLoading = true;
    notifyListeners();

    final result = await _telaahService.getTelaahDetail(rolePrefix, id);

    _isLoading = false;
    notifyListeners();

    if (result['success']) {
      return result['data'];
    } else {
      _errorMessage = result['message'];
      return null;
    }
  }

  Future<bool> processAction(String rolePrefix, int id, String action, {String? catatan, String? kodeMak, double? danaDisetujui}) async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();

    Map<String, dynamic> result;
    try {
      if (action == 'approve') {
        if (kodeMak == null || danaDisetujui == null) {
          _errorMessage = 'Kode MAK dan Dana Disetujui wajib diisi.';
          _isLoading = false;
          notifyListeners();
          return false;
        }
        result = await _telaahService.approve(rolePrefix, id, kodeMak: kodeMak, danaDisetujui: danaDisetujui, catatan: catatan);
      } else if (action == 'reject') {
        result = await _telaahService.reject(rolePrefix, id, catatan);
      } else if (action == 'revise') {
        result = await _telaahService.revise(rolePrefix, id, catatan);
      } else {
        result = {'success': false, 'message': 'Unknown action'};
      }

      if (result['success'] == true) {
        // Remove the item from list locally
        _items.removeWhere((item) => item.id == id);
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = result['message'] ?? 'Tindakan gagal diproses.';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Terjadi kesalahan sistem.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
