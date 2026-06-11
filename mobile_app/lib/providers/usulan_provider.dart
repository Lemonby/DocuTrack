import 'package:flutter/material.dart';
import '../models/kegiatan.dart';
import '../models/master_models.dart';
import '../models/iku.dart';
import '../models/lpj.dart';
import '../services/usulan_service.dart';
import '../services/master_service.dart';

class UsulanProvider with ChangeNotifier {
  final UsulanService _usulanService;
  final MasterService _masterService;

  UsulanProvider(this._usulanService, this._masterService);

  // Pagination & List State
  List<Kegiatan> _usulans = [];
  List<Kegiatan> _kegiatans = [];
  List<Lpj> _lpjs = [];
  bool _isLoading = false;
  String _errorMessage = '';
  int _currentPage = 1;
  int _lastPage = 1;

  // Master Data State
  List<Jurusan> _jurusans = [];
  List<Prodi> _prodis = [];
  List<Wadir> _wadirs = [];
  List<Iku> _ikus = [];
  bool _isLoadingMaster = false;

  List<Kegiatan> get usulans => _usulans;
  List<Kegiatan> get kegiatans => _kegiatans;
  List<Lpj> get lpjs => _lpjs;
  bool get isLoading => _isLoading;
  String get errorMessage => _errorMessage;
  int get currentPage => _currentPage;
  int get lastPage => _lastPage;

  List<Jurusan> get jurusans => _jurusans;
  List<Prodi> get prodis => _prodis;
  List<Wadir> get wadirs => _wadirs;
  List<Iku> get ikus => _ikus;
  bool get isLoadingMaster => _isLoadingMaster;

  Future<void> fetchLpjs({int page = 1, bool isRefresh = false}) async {
    if (isRefresh) {
      _currentPage = 1;
      _lpjs.clear();
      _errorMessage = '';
    }

    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.getLpjs(page: page);

    if (result['success']) {
      if (isRefresh) {
        _lpjs = result['data'];
      } else {
        _lpjs.addAll(result['data'] as List<Lpj>);
      }
      _currentPage = page;
      _lastPage = result['last_page'];
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<Lpj?> getLpjDetail(int id) async {
    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.getLpjDetail(id);
    _isLoading = false;
    notifyListeners();

    if (result['success']) {
      return result['data'];
    } else {
      _errorMessage = result['message'];
      return null;
    }
  }

  Future<void> fetchUsulans({int page = 1, bool isRefresh = false}) async {
    if (isRefresh) {
      _currentPage = 1;
      _usulans.clear();
      _errorMessage = '';
    }

    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.getUsulans(page: page);

    if (result['success']) {
      if (isRefresh) {
        _usulans = result['usulans'];
      } else {
        _usulans.addAll(result['usulans']);
      }
      _currentPage = page;
      _lastPage = result['last_page'];
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchKegiatans({int page = 1, bool isRefresh = false}) async {
    if (isRefresh) {
      _currentPage = 1;
      _kegiatans.clear();
      _errorMessage = '';
    }

    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.getKegiatans(page: page);

    if (result['success']) {
      if (isRefresh) {
        _kegiatans = result['kegiatans'];
      } else {
        _kegiatans.addAll(result['kegiatans']);
      }
      _currentPage = page;
      _lastPage = result['last_page'];
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchMasterData() async {
    if (_jurusans.isNotEmpty && _prodis.isNotEmpty && _wadirs.isNotEmpty) return;

    _isLoadingMaster = true;
    notifyListeners();

    final resJurusan = await _masterService.getJurusan();
    if (resJurusan['success']) {
      _jurusans = resJurusan['data'];
    }

    final resProdi = await _masterService.getProdi();
    if (resProdi['success']) {
      _prodis = resProdi['data'];
    }

    final resWadir = await _masterService.getWadir();
    if (resWadir['success']) {
      _wadirs = resWadir['data'];
    }

    final resIku = await _masterService.getIku();
    if (resIku['success']) {
      _ikus = resIku['data'];
    }

    _isLoadingMaster = false;
    notifyListeners();
  }

  Future<bool> createUsulan(Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.createUsulan(data);

    if (result['success']) {
      await fetchUsulans(page: 1, isRefresh: true);
      return true;
    } else {
      _errorMessage = result['message'];
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateUsulan(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.updateUsulan(id, data);

    if (result['success']) {
      await fetchUsulans(page: 1, isRefresh: true);
      return true;
    } else {
      _errorMessage = result['message'];
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> deleteUsulan(int id) async {
    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.deleteUsulan(id);

    if (result['success']) {
      _usulans.removeWhere((u) => u.id == id);
      _isLoading = false;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> submitRincian(int id, Map<String, dynamic> data, String? filePath) async {
    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.submitRincian(id, data, filePath);

    if (result['success']) {
      await fetchKegiatans(page: 1, isRefresh: true);
      return true;
    } else {
      _errorMessage = result['message'];
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> uploadLpjBukti(int lpjId, int rabItemId, String filePath) async {
    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.uploadLpjBukti(lpjId, rabItemId, filePath);
    _isLoading = false;
    if (!result['success']) {
      _errorMessage = result['message'];
    }
    notifyListeners();
    return result['success'];
  }

  Future<bool> submitLpj(int kegiatanId, List<Map<String, dynamic>> items) async {
    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.submitLpj(kegiatanId, items);
    if (result['success']) {
      await fetchLpjs(page: 1, isRefresh: true); // reload list
    } else {
      _errorMessage = result['message'];
    }
    _isLoading = false;
    notifyListeners();
    return result['success'];
  }

  Future<bool> resubmitUsulan(int id) async {
    _isLoading = true;
    notifyListeners();

    final result = await _usulanService.resubmitUsulan(id);
    if (result['success']) {
      await fetchUsulans(page: 1, isRefresh: true);
    } else {
      _errorMessage = result['message'];
    }
    _isLoading = false;
    notifyListeners();
    return result['success'];
  }
}
