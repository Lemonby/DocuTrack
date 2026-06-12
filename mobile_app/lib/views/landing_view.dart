import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'dart:math' as math;
import 'login_view.dart';

class LandingView extends StatefulWidget {
  const LandingView({super.key});

  @override
  State<LandingView> createState() => _LandingViewState();
}

class _LandingViewState extends State<LandingView> with TickerProviderStateMixin {
  late AnimationController _floatController;
  late ScrollController _scrollController;
  bool _isScrolled = false;

  @override
  void initState() {
    super.initState();
    _scrollController = ScrollController()
      ..addListener(() {
        if (_scrollController.offset > 50 && !_isScrolled) {
          setState(() => _isScrolled = true);
        } else if (_scrollController.offset <= 50 && _isScrolled) {
          setState(() => _isScrolled = false);
        }
      });

    _floatController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 3),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _floatController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      body: Stack(
        children: [
          SingleChildScrollView(
            controller: _scrollController,
            child: Column(
              children: [
                _buildHeroSection(context),
                _buildAboutSection(),
                _buildFeatureSection(),
                _buildProcessSection(),
                _buildFooter(),
              ],
            ),
          ),
          _buildNavbar(context),
        ],
      ),
    );
  }

  Widget _buildNavbar(BuildContext context) {
    return Positioned(
      top: 0,
      left: 0,
      right: 0,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 300),
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
        decoration: BoxDecoration(
          color: _isScrolled ? Colors.white : Colors.transparent,
          boxShadow: _isScrolled ? [BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10)] : [],
        ),
        child: SafeArea(
          bottom: false,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              // SVG Navbar Logo from web
              SvgPicture.asset(
                'assets/images/logo/docutrack-about.svg',
                height: 32,
                colorFilter: _isScrolled ? const ColorFilter.mode(Color(0xFF014565), BlendMode.srcIn) : const ColorFilter.mode(Colors.white, BlendMode.srcIn),
              ),
              if (!_isScrolled)
                ElevatedButton(
                  onPressed: () {
                    Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginView()));
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.white,
                    foregroundColor: const Color(0xFF014565),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    elevation: 0,
                  ),
                  child: const Text('Log In', style: TextStyle(fontWeight: FontWeight.bold)),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeroSection(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final isSmallScreen = size.width < 600;
    final heroHeight = isSmallScreen ? 650.0 : 800.0;

    return Container(
      width: double.infinity,
      constraints: BoxConstraints(
        minHeight: heroHeight,
      ),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topRight,
          end: Alignment.bottomLeft,
          colors: [Color(0xFF014565), Color(0xFF014565), Color(0xFF00FFBC)],
          stops: [0.0, 0.35, 1.0],
        ),
      ),
      child: Stack(
        children: [
          // Background Decor
          Positioned.fill(
            child: SvgPicture.asset(
              'assets/images/background/hero-sec.svg', 
              fit: BoxFit.cover, 
              colorFilter: ColorFilter.mode(Colors.white.withOpacity(0.1), BlendMode.srcIn)
            ),
          ),
          
          // PNJ Logo (Watermark style for mobile)
          Positioned(
            left: -size.width * 0.1,
            top: 50,
            child: Image.asset(
              'assets/images/logo/logoPnj.jpeg', 
              width: isSmallScreen ? size.width * 0.8 : 400, 
              color: Colors.white.withOpacity(0.05), 
              colorBlendMode: BlendMode.srcIn,
              errorBuilder: (context, error, stackTrace) => const SizedBox.shrink(),
            ),
          ),
          
          SafeArea(
            bottom: false,
            child: SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(24, 90, 24, 40),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const SizedBox(height: 20),
                    Text(
                      'Sistem\nPengajuan TOR\n& Kegiatan PNJ',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: isSmallScreen ? 28 : 42, 
                        fontWeight: FontWeight.w900, 
                        color: Colors.white, 
                        height: 1.2
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'Ajukan, Pantau dan kelola dokumen TOR Anda secara online, cepat, transparant, dan efisien.',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: isSmallScreen ? 14 : 16, 
                        color: Colors.white.withOpacity(0.9)
                      ),
                    ),
                    const SizedBox(height: 32),
                    GestureDetector(
                      onTap: () {
                        Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginView()));
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(colors: [Color(0xFF3B82F6), Color(0xFF22D3EE)]),
                          borderRadius: BorderRadius.circular(30),
                          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.2), blurRadius: 10, offset: const Offset(0, 5))],
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text('Log In', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                            SizedBox(width: 8),
                            Icon(Icons.arrow_forward, color: Colors.white, size: 20),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 48),
                    
                    // Responsive Hero Image with Floating Icons
                    LayoutBuilder(
                      builder: (context, constraints) {
                        final imageWidth = constraints.maxWidth * 0.8;
                        
                        return Center(
                          child: SizedBox(
                            width: constraints.maxWidth,
                            height: imageWidth * 0.8 + (isSmallScreen ? 40 : 80),
                            child: Stack(
                              alignment: Alignment.center,
                              clipBehavior: Clip.none,
                              children: [
                                // Main Image
                                Image.asset(
                                  'assets/images/icon/orang-main-laptop.png', 
                                  width: imageWidth,
                                  fit: BoxFit.contain,
                                ),
                              ],
                            ),
                          ),
                        );
                      }
                    ),
                    const SizedBox(height: 60), // Space for wave
                  ],
                ),
              ),
            ),
          ),
          
          // Wave bottom
          Positioned(
            bottom: -2,
            left: 0,
            right: 0,
            child: SvgPicture.string(
              '<svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="#f9fafb" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,197.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>',
              fit: BoxFit.cover,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFloatingIcon(String asset, double x, double y, double delay, double size) {
    return AnimatedBuilder(
      animation: _floatController,
      builder: (context, child) {
        final val = 10 * math.sin((_floatController.value * 2 * 3.1415) + (delay * 2 * 3.1415));
        return Positioned(
          left: x,
          top: y + val,
          child: child!,
        );
      },
      child: asset.endsWith('.svg') 
          ? SvgPicture.asset(asset, width: size) 
          : Image.asset(asset, width: size),
    );
  }

  Widget _buildAboutSection() {
    return Container(
      color: const Color(0xFFFAFAFA),
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 100),
      child: Column(
        children: [
          SvgPicture.asset(
            'assets/images/icon/laptop-about.svg', 
            width: double.infinity,
            height: 250,
            fit: BoxFit.contain,
          ),
          const SizedBox(height: 60),
          SvgPicture.asset('assets/images/logo/docutrack-about.svg', height: 40),
          const SizedBox(height: 16),
          const Text(
            'DocuTrack adalah platform digital yang mempermudah pengajuan dan pelacakan ToR (Term of Reference) untuk proyek kegiatan di lingkungan kampus yang terintegrasi. Semua proses pengajuan, verifikasi, hingga persetujuan dilakukan secara online, efisien, dan transparan.',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 14, color: Color(0xFF274B8F), height: 1.5, fontWeight: FontWeight.w600),
          ),
        ],
      ),
    );
  }

  Widget _buildFeatureSection() {
    return Container(
      color: Colors.white,
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 100),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Other features &\nadvantages', style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.black87)),
          const SizedBox(height: 24),
          _buildFeatureCard(
            Icons.send,
            'Pengajuan TOR Online',
            'Ajukan TOR kapan saja tanpa kertas. Cukup isi form dan upload dokumen.',
            true,
          ),
          const SizedBox(height: 16),
          _buildFeatureCard(
            Icons.search,
            'Tracking Status',
            'Pantau proses pengajuan TOR anda secara real-time dan transparant.',
            true,
          ),
          const SizedBox(height: 16),
          _buildFeatureCard(
            Icons.access_time,
            'Hemat Waktu',
            'Dengan adanya sistem digital, pengajuan lebih cepat tanpa tatap muka.',
            false,
          ),
          const SizedBox(height: 16),
          _buildFeatureCard(
            Icons.storage,
            'Data Terstruktur',
            'Semua data pengajuan tersimpan rapi untuk audit dan pelacakan.',
            false,
          ),
        ],
      ),
    );
  }

  Widget _buildFeatureCard(IconData icon, String title, String desc, bool isGradient) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: isGradient ? const LinearGradient(colors: [Color(0xFF274B8F), Color(0xFF22D3EE)]) : null,
        color: isGradient ? null : Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 20)],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: isGradient ? Colors.white.withOpacity(0.2) : Colors.grey.shade100, borderRadius: BorderRadius.circular(12)),
            child: Icon(icon, color: isGradient ? Colors.white : Colors.black87),
          ),
          const SizedBox(height: 16),
          Text(title, style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: isGradient ? Colors.white : Colors.black87)),
          const SizedBox(height: 8),
          Text(desc, style: TextStyle(fontSize: 13, color: isGradient ? Colors.white.withOpacity(0.9) : Colors.grey.shade600)),
        ],
      ),
    );
  }

  Widget _buildProcessSection() {
    return Container(
      color: const Color(0xFFFAFAFA),
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 100),
      child: Column(
        children: [
          const Text('Tahapan Pengajuan', style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Color(0xFF0F766E))),
          const SizedBox(height: 32),
          _buildProcessStep(1, 'Pengajuan Kegiatan', 'Pengusul', 'Pengusul membuat dan mengajukan proposal kegiatan melalui sistem secara online', Icons.send),
          _buildProcessStep(2, 'Verifikasi Pengajuan', 'Verifikator', 'Verifikator memeriksa kelengkapan dan keabsahan dokumen', Icons.fact_check),
          _buildProcessStep(3, 'Pembuatan Komitmen', 'PPK', 'PPK membuat komitmen anggaran untuk mendukung kegiatan', Icons.handshake),
          _buildProcessStep(4, 'Persetujuan Wadir', 'Wadir', 'Wadir menyetujui rancangan komitmen yang telah dibuat', Icons.verified_user),
          _buildProcessStep(5, 'Selesai', 'Sistem', 'Dokumen selesai diproses dan dana dapat dicairkan', Icons.check_circle),
        ],
      ),
    );
  }

  Widget _buildProcessStep(int step, String title, String role, String desc, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 24),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: const BoxDecoration(
              gradient: LinearGradient(colors: [Color(0xFF0D9488), Color(0xFF0891B2)]),
              shape: BoxShape.circle,
            ),
            child: Center(child: Text('$step', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18))),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.teal.shade100),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(child: Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(color: Colors.teal.shade50, borderRadius: BorderRadius.circular(12)),
                        child: Text(role, style: const TextStyle(fontSize: 10, color: Colors.teal)),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(desc, style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFooter() {
    return Container(
      color: const Color(0xFF014565),
      padding: const EdgeInsets.all(24),
      child: const Center(
        child: Text('© 2026 DocuTrack PNJ. All rights reserved.', style: TextStyle(color: Colors.white70, fontSize: 12)),
      ),
    );
  }
}
