import 'package:flutter/material.dart';
import '../models/kegiatan.dart';
import '../models/lpj.dart';
import '../services/bendahara_service.dart';

class BendaharaProvider with ChangeNotifier {
  final BendaharaService _service;

  BendaharaProvider(this._service);

  // States for Pencairan
  List<Kegiatan> _pencairanItems = [];
  bool _isLoadingPencairan = false;
  String _errorMessagePencairan = '';
  int _pencairanPage = 1;
  int _pencairanLastPage = 1;

  List<Kegiatan> get pencairanItems => _pencairanItems;
  bool get isLoadingPencairan => _isLoadingPencairan;
  String get errorMessagePencairan => _errorMessagePencairan;
  int get pencairanPage => _pencairanPage;
  int get pencairanLastPage => _pencairanLastPage;

  // States for LPJ
  List<Lpj> _lpjItems = [];
  bool _isLoadingLpj = false;
  String _errorMessageLpj = '';
  int _lpjPage = 1;
  int _lpjLastPage = 1;

  List<Lpj> get lpjItems => _lpjItems;
  bool get isLoadingLpj => _isLoadingLpj;
  String get errorMessageLpj => _errorMessageLpj;
  int get lpjPage => _lpjPage;
  int get lpjLastPage => _lpjLastPage;

  // Generic loading for form submissions
  bool _isSubmitting = false;
  bool get isSubmitting => _isSubmitting;

  Future<void> fetchPencairanList({int page = 1, bool isRefresh = false}) async {
    if (isRefresh) {
      _pencairanPage = 1;
      _pencairanItems.clear();
      _errorMessagePencairan = '';
    }

    _isLoadingPencairan = true;
    notifyListeners();

    final result = await _service.getPencairanList(page: page);

    if (result['success']) {
      if (isRefresh) {
        _pencairanItems = result['data'];
      } else {
        _pencairanItems.addAll(result['data']);
      }
      _pencairanPage = page;
      _pencairanLastPage = result['last_page'];
    } else {
      _errorMessagePencairan = result['message'];
    }

    _isLoadingPencairan = false;
    notifyListeners();
  }

  Future<void> fetchLpjList({int page = 1, bool isRefresh = false}) async {
    if (isRefresh) {
      _lpjPage = 1;
      _lpjItems.clear();
      _errorMessageLpj = '';
    }

    _isLoadingLpj = true;
    notifyListeners();

    final result = await _service.getLpjList(page: page);

    if (result['success']) {
      if (isRefresh) {
        _lpjItems = result['data'];
      } else {
        _lpjItems.addAll(result['data']);
      }
      _lpjPage = page;
      _lpjLastPage = result['last_page'];
    } else {
      _errorMessageLpj = result['message'];
    }

    _isLoadingLpj = false;
    notifyListeners();
  }

  Future<Kegiatan?> getPencairanDetail(int id) async {
    _isLoadingPencairan = true;
    notifyListeners();
    final result = await _service.getPencairanDetail(id);
    _isLoadingPencairan = false;
    notifyListeners();

    if (result['success']) {
      return result['data'];
    } else {
      _errorMessagePencairan = result['message'];
      return null;
    }
  }

  Future<Lpj?> getLpjDetail(int id) async {
    _isLoadingLpj = true;
    notifyListeners();
    final result = await _service.getLpjDetail(id);
    _isLoadingLpj = false;
    notifyListeners();

    if (result['success']) {
      return result['data'];
    } else {
      _errorMessageLpj = result['message'];
      return null;
    }
  }

  Future<Map<String, dynamic>> submitPencairan(Map<String, dynamic> data) async {
    _isSubmitting = true;
    notifyListeners();

    final result = await _service.prosesPencairan(data);

    if (result['success']) {
      // Remove from list if successful
      final id = data['kegiatan_id'];
      _pencairanItems.removeWhere((element) => element.id == id);
    }

    _isSubmitting = false;
    notifyListeners();
    return result;
  }

  Future<Map<String, dynamic>> prosesLpj(int lpjId, String aksi, {String komentar = ''}) async {
    _isSubmitting = true;
    notifyListeners();

    final result = await _service.prosesLpj(lpjId, aksi, komentar: komentar);

    if (result['success']) {
      // Remove from list if successful
      _lpjItems.removeWhere((element) => element.id == lpjId);
    }

    _isSubmitting = false;
    notifyListeners();
    return result;
  }
}
