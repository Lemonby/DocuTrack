import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'landing_view.dart';
import 'dashboard_view.dart';
import 'totp_verification_view.dart';

class SplashView extends StatefulWidget {
  const SplashView({super.key});

  @override
  State<SplashView> createState() => _SplashViewState();
}

class _SplashViewState extends State<SplashView> with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    );
    _fadeAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _animationController, curve: Curves.easeIn),
    );
    
    _animationController.forward();
    _checkAuth();
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  Future<void> _checkAuth() async {
    // Wait a brief moment for the animation and loading of session
    await Future.delayed(const Duration(milliseconds: 2000));
    if (!mounted) return;

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    _navigateBasedOnStatus(authProvider.status);
  }

  void _navigateBasedOnStatus(AuthStatus status) {
    if (!mounted) return;

    Widget targetView;
    switch (status) {
      case AuthStatus.authenticated:
        targetView = const DashboardView();
        break;
      case AuthStatus.totpRequired:
        targetView = const TotpVerificationView();
        break;
      default:
        targetView = const LandingView();
    }

    Navigator.of(context).pushReplacement(
      PageRouteBuilder(
        pageBuilder: (context, animation, secondaryAnimation) => targetView,
        transitionsBuilder: (context, animation, secondaryAnimation, child) {
          return FadeTransition(opacity: animation, child: child);
        },
        transitionDuration: const Duration(milliseconds: 600),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: AppTheme.heroGradient,
        ),
        child: FadeTransition(
          opacity: _fadeAnimation,
          child: Stack(
            children: [
              // Decorative background patterns
              Positioned(
                top: -50,
                left: -50,
                child: Container(
                  width: 200,
                  height: 200,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.05),
                    shape: BoxShape.circle,
                  ),
                ),
              ),
              Positioned(
                bottom: -80,
                right: -80,
                child: Container(
                  width: 300,
                  height: 300,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.03),
                    shape: BoxShape.circle,
                  ),
                ),
              ),
              
                    // Center Logo Content
                    Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          // Official DocuTrack SVG Logo
                          SvgPicture.asset(
                            'assets/images/logo/docutrack-about.svg',
                            height: 64,
                            // Ensure the logo looks good on dark gradient background
                            colorFilter: const ColorFilter.mode(Colors.white, BlendMode.srcIn),
                          ),
                          const SizedBox(height: 16),
                    
                    // Brand Subtitle
                    Text(
                      'Sistem Pengajuan TOR & Kegiatan PNJ',
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                        color: Colors.white.withOpacity(0.7),
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 48),
                    
                    // Spinner / Loading Indicator
                    if (authProvider.isLoading)
                      const CircularProgressIndicator(
                        valueColor: AlwaysStoppedAnimation<Color>(AppTheme.brightAqua),
                      )
                  ],
                ),
              ),
              
              // Footer text
              Positioned(
                bottom: 24,
                left: 0,
                right: 0,
                child: Text(
                  'Politeknik Negeri Jakarta',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.5),
                    fontSize: 12,
                    letterSpacing: 1,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
