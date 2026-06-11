import 'kegiatan.dart';

class DashboardStats {
  final int totalUsulan;
  final int disetujui;
  final int ditolak;
  final int menunggu;

  DashboardStats({
    this.totalUsulan = 0,
    this.disetujui = 0,
    this.ditolak = 0,
    this.menunggu = 0,
  });

  factory DashboardStats.fromJson(Map<String, dynamic>? json) {
    if (json == null) return DashboardStats();
    
    // Attempt to map from various possible keys depending on role
    return DashboardStats(
      totalUsulan: json['total_usulan'] ?? json['total'] ?? 0,
      disetujui: json['disetujui'] ?? json['total_disetujui'] ?? 0,
      ditolak: json['ditolak'] ?? json['total_ditolak'] ?? 0,
      menunggu: json['menunggu'] ?? json['total_menunggu'] ?? 0,
    );
  }
}

class DashboardData {
  final DashboardStats stats;
  final List<Kegiatan> recentItems;
  final Map<String, dynamic>? serverLoad;
  final List<dynamic>? recentLogs;
  final List<dynamic>? activeUsers;
  final Map<String, dynamic>? budget;
  final List<dynamic>? ikuAchievements;
  final List<String>? listJurusan;
  final Map<String, dynamic>? monthlyTrend;

  DashboardData({
    required this.stats,
    this.recentItems = const [],
    this.serverLoad,
    this.recentLogs,
    this.activeUsers,
    this.budget,
    this.ikuAchievements,
    this.listJurusan,
    this.monthlyTrend,
  });

  factory DashboardData.fromJson(Map<String, dynamic> json) {
    // Determine which key contains the list (recent_kegiatan for admin, pending for ppk/wadir)
    List<dynamic> rawItems = [];
    if (json['recent_kegiatan'] != null) {
      rawItems = json['recent_kegiatan'];
    } else if (json['pending'] != null) {
      rawItems = json['pending'];
    }

    return DashboardData(
      stats: DashboardStats.fromJson(json['stats']),
      recentItems: rawItems.map((e) => Kegiatan.fromJson(e)).toList(),
      serverLoad: json['server_load'],
      recentLogs: json['recent_logs'],
      activeUsers: json['active_users'],
      budget: json['budget'],
      ikuAchievements: json['iku_achievements'],
      listJurusan: json['list_jurusan'] != null ? List<String>.from(json['list_jurusan']) : null,
      monthlyTrend: json['monthly_trend'],
    );
  }
}
