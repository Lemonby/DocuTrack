class User {
  final int userId;
  final String name;
  final String email;
  final String? departmentName; // nama_jurusan
  final String status;
  final String role; // primary role name e.g. 'Admin', 'SuperAdmin', 'PPK'

  User({
    required this.userId,
    required this.name,
    required this.email,
    this.departmentName,
    required this.status,
    required this.role,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    // Parse roles array from Spatie response structure
    String extractedRole = 'User';
    if (json['roles'] != null && json['roles'] is List && (json['roles'] as List).isNotEmpty) {
      extractedRole = json['roles'][0]['name'] ?? 'User';
    } else if (json['role'] != null) {
      extractedRole = json['role'];
    }

    return User(
      userId: json['user_id'] ?? json['id'] ?? 0,
      name: json['nama'] ?? json['name'] ?? '',
      email: json['email'] ?? '',
      departmentName: json['nama_jurusan'],
      status: json['status'] ?? 'Aktif',
      role: extractedRole,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'user_id': userId,
      'nama': name,
      'email': email,
      'nama_jurusan': departmentName,
      'status': status,
      'role': role,
    };
  }

  // Check role helper
  bool get isAdmin => role == 'Admin';
  bool get isSuperAdmin => role == 'SuperAdmin';
  bool get isBendahara => role == 'Bendahara';
  bool get isVerifikator => role == 'Verifikator';
  bool get isPPK => role == 'PPK';
  bool get isWadir => role == 'Wadir';
  bool get isDirektur => role == 'Direktur';
}
