import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/date_symbol_data_local.dart';

// Services
import 'services/api_service.dart';
import 'services/auth_service.dart';
import 'services/iku_service.dart';
import 'services/dashboard_service.dart';
import 'services/master_service.dart';
import 'services/usulan_service.dart';
import 'services/telaah_service.dart';
import 'services/bendahara_service.dart';
import 'services/notifikasi_service.dart';
import 'services/akun_service.dart';
import 'services/monitoring_service.dart';
import 'services/superadmin_service.dart';

// State Providers
import 'providers/auth_provider.dart';
import 'providers/iku_provider.dart';
import 'providers/dashboard_provider.dart';
import 'providers/usulan_provider.dart';
import 'providers/telaah_provider.dart';
import 'providers/bendahara_provider.dart';
import 'providers/notifikasi_provider.dart';
import 'providers/akun_provider.dart';
import 'providers/monitoring_provider.dart';
import 'providers/superadmin_provider.dart';

// Themes & Views
import 'theme/app_theme.dart';
import 'views/splash_view.dart';
// import 'views/login_view.dart';

final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id_ID', null);

  // Instantiate Singletons
  final apiService = ApiService();
  final authService = AuthService(apiService);
  final ikuService = IkuService(apiService);
  final dashboardService = DashboardService(apiService);
  final masterService = MasterService(apiService);
  final usulanService = UsulanService(apiService);
  final telaahService = TelaahService(apiService);
  final bendaharaService = BendaharaService(apiService);
  final notifikasiService = NotifikasiService(apiService);
  final akunService = AkunService(apiService);
  final monitoringService = MonitoringService(apiService);
  final superadminService = SuperadminService(apiService);

  runApp(
    MultiProvider(
      providers: [
        // Services injection
        Provider<ApiService>.value(value: apiService),
        Provider<AuthService>.value(value: authService),
        Provider<IkuService>.value(value: ikuService),
        Provider<DashboardService>.value(value: dashboardService),
        Provider<MasterService>.value(value: masterService),
        Provider<UsulanService>.value(value: usulanService),
        Provider<TelaahService>.value(value: telaahService),
        Provider<BendaharaService>.value(value: bendaharaService),
        Provider<NotifikasiService>.value(value: notifikasiService),
        Provider<AkunService>.value(value: akunService),
        Provider<MonitoringService>.value(value: monitoringService),
        Provider<SuperadminService>.value(value: superadminService),
        
        // State Providers injection
        ChangeNotifierProvider<AuthProvider>(
          create: (context) => AuthProvider(authService),
        ),
        ChangeNotifierProvider<IkuProvider>(
          create: (context) => IkuProvider(ikuService),
        ),
        ChangeNotifierProvider<DashboardProvider>(
          create: (context) => DashboardProvider(dashboardService),
        ),
        ChangeNotifierProvider<UsulanProvider>(
          create: (context) => UsulanProvider(usulanService, masterService),
        ),
        ChangeNotifierProvider<TelaahProvider>(
          create: (context) => TelaahProvider(telaahService),
        ),
        ChangeNotifierProvider<BendaharaProvider>(
          create: (context) => BendaharaProvider(bendaharaService),
        ),
        ChangeNotifierProvider<NotifikasiProvider>(
          create: (context) => NotifikasiProvider(notifikasiService),
        ),
        ChangeNotifierProvider<AkunProvider>(
          create: (context) => AkunProvider(akunService),
        ),
        ChangeNotifierProvider<MonitoringProvider>(
          create: (context) => MonitoringProvider(monitoringService),
        ),
        ChangeNotifierProvider<SuperadminProvider>(
          create: (context) => SuperadminProvider(superadminService),
        ),
      ],
      child: const MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      navigatorKey: navigatorKey,
      title: 'DocuTrack Mobile',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: const SplashView(),
    );
  }
}
