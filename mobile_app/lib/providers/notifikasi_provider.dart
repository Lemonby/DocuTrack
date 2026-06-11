import 'package:flutter/material.dart';
import '../models/notifikasi.dart';
import '../services/notifikasi_service.dart';

class NotifikasiProvider with ChangeNotifier {
  final NotifikasiService _service;

  NotifikasiProvider(this._service);

  List<Notifikasi> _items = [];
  bool _isLoading = false;
  String _errorMessage = '';
  int _currentPage = 1;
  int _lastPage = 1;
  int _unreadCount = 0;

  List<Notifikasi> get items => _items;
  bool get isLoading => _isLoading;
  String get errorMessage => _errorMessage;
  int get currentPage => _currentPage;
  int get lastPage => _lastPage;
  int get unreadCount => _unreadCount;

  Future<void> fetchList({int page = 1, bool isRefresh = false}) async {
    if (isRefresh) {
      _currentPage = 1;
      _items.clear();
      _errorMessage = '';
    }

    _isLoading = true;
    notifyListeners();

    final result = await _service.getNotifikasiList(page: page);

    if (result['success']) {
      if (isRefresh) {
        _items = result['data'];
      } else {
        _items.addAll(result['data']);
      }
      _unreadCount = result['unread'] ?? 0;
      _currentPage = page;
      _lastPage = result['last_page'] ?? 1;
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> markAsRead(int id) async {
    final idx = _items.indexWhere((e) => e.id == id);
    if (idx != -1 && !_items[idx].isRead) {
      final success = await _service.markAsRead(id);
      if (success) {
        // Create new item with DIBACA status
        final oldItem = _items[idx];
        _items[idx] = Notifikasi(
          id: oldItem.id,
          tipe: oldItem.tipe,
          status: 'DIBACA',
          konten: oldItem.konten,
          idReferensi: oldItem.idReferensi,
          createdAt: oldItem.createdAt,
        );
        if (_unreadCount > 0) _unreadCount--;
        notifyListeners();
      }
    }
  }

  Future<void> markAllAsRead() async {
    if (_unreadCount > 0) {
      final success = await _service.markAllAsRead();
      if (success) {
        _items = _items.map((item) {
          if (!item.isRead) {
            return Notifikasi(
              id: item.id,
              tipe: item.tipe,
              status: 'DIBACA',
              konten: item.konten,
              idReferensi: item.idReferensi,
              createdAt: item.createdAt,
            );
          }
          return item;
        }).toList();
        _unreadCount = 0;
        notifyListeners();
      }
    }
  }
}
