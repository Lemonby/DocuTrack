import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../providers/dashboard_provider.dart';
import '../providers/notifikasi_provider.dart';
import '../theme/app_theme.dart';
import 'iku_crud_view.dart';
import 'usulan/usulan_list_view.dart';
import 'bendahara/bendahara_main_view.dart';
import 'notifikasi_view.dart';
import 'profil_view.dart';
import 'login_view.dart';
import 'telaah/telaah_list_view.dart';
import 'telaah/monitoring_list_view.dart';
import 'dashboards/admin_dashboard.dart';
import 'dashboards/verifikator_dashboard.dart';
import 'dashboards/ppk_dashboard.dart';
import 'dashboards/wadir_dashboard.dart';
import 'dashboards/direktur_dashboard.dart';
import 'dashboards/superadmin_dashboard.dart';
import 'dashboards/bendahara_dashboard.dart';
import 'admin/admin_kegiatan_list_view.dart';
import 'admin/admin_lpj_list_view.dart';
import 'superadmin/superadmin_users_tab.dart';
import 'dashboards/direktur_monitoring_view.dart';

class DashboardView extends StatefulWidget {
  const DashboardView({super.key});

  @override
  State<DashboardView> createState() => _DashboardViewState();
}

class _DashboardViewState extends State<DashboardView> {
  int _currentIndex = 0;
  bool _isSidebarCollapsed = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);
      final dashboardProvider = Provider.of<DashboardProvider>(context, listen: false);
      final notifProvider = Provider.of<NotifikasiProvider>(context, listen: false);
      if (authProvider.currentUser != null) {
        dashboardProvider.fetchDashboardData(authProvider.currentUser!);
        notifProvider.fetchList(isRefresh: true);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.currentUser;

    if (user == null) {
      return const LoginView();
    }

    // Determine layout based on screen width (Responsiveness)
    final double screenWidth = MediaQuery.of(context).size.width;
    final bool isLargeScreen = screenWidth >= 768; // Tablet & Desktop threshold

    return Scaffold(
      body: Row(
        children: [
          // Collapsible Sidebar (Only visible on Large Screens)
          if (isLargeScreen) _buildSidebar(context, authProvider),

          // Main Screen Area
          Expanded(
            child: Scaffold(
              appBar: AppBar(
                title: Text(
                  _currentIndex == 0 ? 'Dashboard' : (_currentIndex == 1 ? (user.isSuperAdmin ? 'Kelola User' : (user.isAdmin ? 'Pengajuan KAK' : (user.isBendahara ? 'Keuangan' : (user.isDirektur ? 'Monitoring' : 'Daftar Telaah')))) : (_currentIndex == 2 && user.isSuperAdmin ? 'IKU Management' : 'Profil Pengguna')),
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
                actions: [
                  Consumer<NotifikasiProvider>(
                    builder: (context, notifProvider, child) {
                      return Stack(
                        children: [
                          IconButton(
                            icon: const Icon(Icons.notifications_none),
                            onPressed: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => const NotifikasiView()),
                              );
                            },
                          ),
                          if (notifProvider.unreadCount > 0)
                            Positioned(
                              right: 8,
                              top: 8,
                              child: Container(
                                padding: const EdgeInsets.all(2),
                                decoration: BoxDecoration(
                                  color: Colors.red,
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                                child: Text(
                                  notifProvider.unreadCount > 99 ? '99+' : '${notifProvider.unreadCount}',
                                  style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                                  textAlign: TextAlign.center,
                                ),
                              ),
                            ),
                        ],
                      );
                    },
                  ),
                  const SizedBox(width: 8),
                ],
              ),
              body: Consumer<DashboardProvider>(
                builder: (context, dashboardProvider, child) {
                  return _buildCurrentTabContent(context, authProvider, dashboardProvider);
                },
              ),
              
              // Bottom Navigation Bar (Only visible on Small Screens)
              bottomNavigationBar: isLargeScreen
                  ? null
                  : BottomNavigationBar(
                      currentIndex: _currentIndex,
                      selectedItemColor: AppTheme.primaryBlue,
                      unselectedItemColor: AppTheme.textMuted,
                      onTap: (index) {
                        setState(() {
                          _currentIndex = index;
                        });
                      },
                      items: [
                        const BottomNavigationBarItem(
                          icon: Icon(Icons.dashboard_outlined),
                          activeIcon: Icon(Icons.dashboard),
                          label: 'Beranda',
                        ),
                        if (user.isSuperAdmin) ...[
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.people_outline),
                            activeIcon: Icon(Icons.people),
                            label: 'Kelola User',
                          ),
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.list_alt_outlined),
                            activeIcon: Icon(Icons.list_alt),
                            label: 'CRUD IKU',
                          ),
                        ]
                        else if (user.isAdmin) ...[
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.file_copy_outlined),
                            activeIcon: Icon(Icons.file_copy),
                            label: 'Pengajuan KAK',
                          ),
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.assignment_outlined),
                            activeIcon: Icon(Icons.assignment),
                            label: 'Kegiatan',
                          ),
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.receipt_long_outlined),
                            activeIcon: Icon(Icons.receipt_long),
                            label: 'LPJ',
                          ),
                        ] else if (user.isPPK || user.isVerifikator || user.isWadir) ...[
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.assignment_turned_in_outlined),
                            activeIcon: Icon(Icons.assignment_turned_in),
                            label: 'Telaah',
                          ),
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.history_outlined),
                            activeIcon: Icon(Icons.history),
                            label: 'Riwayat',
                          ),
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.remove_red_eye_outlined),
                            activeIcon: Icon(Icons.remove_red_eye),
                            label: 'Monitoring',
                          ),
                        ] else if (user.isBendahara)
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.account_balance_wallet_outlined),
                            activeIcon: Icon(Icons.account_balance_wallet),
                            label: 'Keuangan',
                          )
                        else if (user.isDirektur) ...[
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.remove_red_eye_outlined),
                            activeIcon: Icon(Icons.remove_red_eye),
                            label: 'Monitoring',
                          ),
                        ] else
                          const BottomNavigationBarItem(
                            icon: Icon(Icons.inbox_outlined),
                            activeIcon: Icon(Icons.inbox),
                            label: 'Inbox', // Placeholder for other roles
                          ),
                        const BottomNavigationBarItem(
                          icon: Icon(Icons.person_outline),
                          activeIcon: Icon(Icons.person),
                          label: 'Profil',
                        ),
                      ],
                    ),
            ),
          ),
        ],
      ),
    );
  }

  // Sidebar Layout for large screens
  Widget _buildSidebar(BuildContext context, AuthProvider authProvider) {
    return Container(
      width: _isSidebarCollapsed ? 80 : 260,
      decoration: BoxDecoration(
        color: AppTheme.primaryBlue,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 10,
          )
        ],
      ),
      child: Column(
        children: [
          // Sidebar Header
          Container(
            padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
            child: Row(
              mainAxisAlignment: _isSidebarCollapsed ? MainAxisAlignment.center : MainAxisAlignment.spaceBetween,
              children: [
                if (!_isSidebarCollapsed)
                  const Row(
                    children: [
                      Icon(Icons.document_scanner_outlined, color: AppTheme.brightAqua, size: 28),
                      SizedBox(width: 8),
                      Text(
                        'DocuTrack',
                        style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
                      )
                    ],
                  ),
                IconButton(
                  icon: Icon(
                    _isSidebarCollapsed ? Icons.chevron_right : Icons.chevron_left,
                    color: Colors.white70,
                  ),
                  onPressed: () {
                    setState(() {
                      _isSidebarCollapsed = !_isSidebarCollapsed;
                    });
                  },
                ),
              ],
            ),
          ),
          
          const Divider(color: Colors.white10),
          const SizedBox(height: 16),

          // Sidebar Navigation Items
          _buildSidebarItem(0, Icons.dashboard_outlined, 'Dashboard'),
          if (authProvider.currentUser?.isSuperAdmin == true) ...[
            _buildSidebarItem(1, Icons.people_outline, 'Kelola User'),
            _buildSidebarItem(2, Icons.list_alt_outlined, 'IKU Management (CRUD)'),
          ] else if (authProvider.currentUser?.isAdmin == true) ...[
            _buildSidebarItem(1, Icons.file_copy_outlined, 'Pengajuan KAK'),
            _buildSidebarItem(2, Icons.assignment_outlined, 'Pengajuan Kegiatan'),
            _buildSidebarItem(3, Icons.receipt_long_outlined, 'Pengajuan LPJ'),
          ] else if (authProvider.currentUser?.isPPK == true || authProvider.currentUser?.isVerifikator == true || authProvider.currentUser?.isWadir == true) ...[
            _buildSidebarItem(1, Icons.assignment_turned_in_outlined, 'Daftar Telaah'),
            _buildSidebarItem(2, Icons.history_outlined, 'Riwayat'),
            _buildSidebarItem(3, Icons.remove_red_eye_outlined, 'Monitoring'),
          ] else if (authProvider.currentUser?.isBendahara == true)
            _buildSidebarItem(1, Icons.account_balance_wallet_outlined, 'Keuangan')
          else if (authProvider.currentUser?.isDirektur == true)
            _buildSidebarItem(1, Icons.remove_red_eye_outlined, 'Monitoring')
          else
            _buildSidebarItem(1, Icons.inbox_outlined, 'Kotak Masuk'),
            
          _buildSidebarItem(authProvider.currentUser?.isAdmin == true || authProvider.currentUser?.isPPK == true || authProvider.currentUser?.isVerifikator == true || authProvider.currentUser?.isWadir == true ? 4 : (authProvider.currentUser?.isSuperAdmin == true ? 3 : 2), Icons.person_outline, 'Profil Pengguna'),
          
          const Spacer(),

          // Logout Item
          _buildSidebarItem(-1, Icons.logout_outlined, 'Logout', onTap: () async {
            await authProvider.logout();
            if (!mounted) return;
            Navigator.of(context).pushReplacement(
              MaterialPageRoute(builder: (context) => const LoginView()),
            );
          }),
          const SizedBox(height: 16),
        ],
      ),
    );
  }

  Widget _buildSidebarItem(int index, IconData icon, String title, {VoidCallback? onTap}) {
    final isSelected = _currentIndex == index;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
      child: ListTile(
        onTap: onTap ?? () {
          setState(() {
            _currentIndex = index;
          });
        },
        selected: isSelected,
        selectedTileColor: Colors.white.withOpacity(0.1),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        leading: Icon(icon, color: isSelected ? AppTheme.brightAqua : Colors.white70),
        title: _isSidebarCollapsed
            ? null
            : Text(
                title,
                style: TextStyle(
                  color: isSelected ? Colors.white : Colors.white70,
                  fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                ),
              ),
      ),
    );
  }

  // Active Screen Selector
  Widget _buildCurrentTabContent(BuildContext context, AuthProvider authProvider, DashboardProvider dashboardProvider) {
    switch (_currentIndex) {
      case 0:
        return _buildHomeTab(context, authProvider, dashboardProvider);
      case 1:
        if (authProvider.currentUser?.isSuperAdmin == true) {
          return SuperadminUsersTab(user: authProvider.currentUser!);
        } else if (authProvider.currentUser?.isAdmin == true) {
          return const UsulanListView();
        } else if (authProvider.currentUser?.isPPK == true || authProvider.currentUser?.isVerifikator == true || authProvider.currentUser?.isWadir == true) {
          return const TelaahListView(); // Was TelaahMainView
        } else if (authProvider.currentUser?.isBendahara == true) {
          return const BendaharaMainView();
        } else if (authProvider.currentUser?.isDirektur == true) {
          return const DirekturMonitoringView();
        } else {
          return const Center(child: Text('Belum Tersedia'));
        }
      case 2:
        if (authProvider.currentUser?.isSuperAdmin == true) {
          return const IkuCrudView();
        } else if (authProvider.currentUser?.isAdmin == true) {
          return const AdminKegiatanListView();
        } else if (authProvider.currentUser?.isPPK == true || authProvider.currentUser?.isVerifikator == true || authProvider.currentUser?.isWadir == true) {
          return const MonitoringListView(isRiwayat: true); // Riwayat
        }
        return const ProfilView();
      case 3:
        if (authProvider.currentUser?.isSuperAdmin == true) {
          return const ProfilView();
        } else if (authProvider.currentUser?.isAdmin == true) {
          return const AdminLpjListView();
        } else if (authProvider.currentUser?.isPPK == true || authProvider.currentUser?.isVerifikator == true || authProvider.currentUser?.isWadir == true) {
          return const MonitoringListView(isRiwayat: false); // Monitoring
        }
        return const ProfilView();
      case 4:
        return const ProfilView();
      default:
        return _buildHomeTab(context, authProvider, dashboardProvider);
    }
  }

  // Dashboard / Home View Content
  Widget _buildHomeTab(BuildContext context, AuthProvider authProvider, DashboardProvider dashboardProvider) {
    final user = authProvider.currentUser!;
    
    if (dashboardProvider.isLoading && dashboardProvider.dashboardData == null) {
      return const Center(child: CircularProgressIndicator(color: AppTheme.primaryBlue));
    }

    if (dashboardProvider.errorMessage.isNotEmpty && dashboardProvider.dashboardData == null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 48, color: Colors.red),
            const SizedBox(height: 16),
            Text(dashboardProvider.errorMessage, style: const TextStyle(color: AppTheme.textMuted)),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: () => dashboardProvider.fetchDashboardData(user),
              child: const Text('Coba Lagi'),
            ),
          ],
        ),
      );
    }
    
    // We pass the fetched dashboardData to the widgets.
    // Ensure that widgets gracefully handle null data by using default empty states if needed.
    final data = dashboardProvider.dashboardData;

    Widget dashboardContent;
    if (user.isSuperAdmin) {
      dashboardContent = SuperadminDashboard(user: user, data: data);
    } else if (user.isAdmin) {
      dashboardContent = AdminDashboard(user: user, data: data);
    } else if (user.isVerifikator) {
      dashboardContent = VerifikatorDashboard(user: user, data: data);
    } else if (user.isPPK) {
      dashboardContent = PpkDashboard(user: user, data: data);
    } else if (user.isWadir) {
      dashboardContent = WadirDashboard(user: user, data: data);
    } else if (user.isDirektur) {
      dashboardContent = DirekturDashboard(user: user, data: data);
    } else if (user.isBendahara) {
      dashboardContent = BendaharaDashboard(user: user, data: data);
    } else {
      dashboardContent = Center(
        child: Text('Dashboard untuk role ${user.role} belum tersedia.'),
      );
    }

    return RefreshIndicator(
      onRefresh: () => dashboardProvider.fetchDashboardData(user),
      color: AppTheme.primaryBlue,
      child: dashboardContent,
    );
  }





  // The dummy _showNotifications and _buildNotificationItem have been removed.
}
