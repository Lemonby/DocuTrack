import 'package:flutter_test/flutter_test.dart';
import 'package:docutrack_mobile/models/user.dart';

void main() {
  group('User Model Tests', () {
    test('User.fromJson should create a valid User object', () {
      final json = {
        'user_id': 1,
        'nama': 'Test User',
        'email': 'test@example.com',
        'role': 'Admin',
        'nama_jurusan': 'Teknik Informatika',
        'status': 'Aktif',
      };

      final user = User.fromJson(json);

      expect(user.userId, 1);
      expect(user.name, 'Test User');
      expect(user.isAdmin, true);
      expect(user.isSuperAdmin, false);
    });

    test('User role getters should work correctly', () {
      final superAdmin = User(userId: 1, name: 'SA', email: 'sa@a.com', role: 'SuperAdmin', status: 'Aktif');
      final admin = User(userId: 2, name: 'A', email: 'a@a.com', role: 'Admin', status: 'Aktif');
      final bendahara = User(userId: 3, name: 'B', email: 'b@a.com', role: 'Bendahara', status: 'Aktif');

      expect(superAdmin.isSuperAdmin, true);
      expect(admin.isAdmin, true);
      expect(bendahara.isBendahara, true);
      expect(admin.isSuperAdmin, false);
    });
  });
}
