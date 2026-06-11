import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import 'dashboard_view.dart';

class LoginView extends StatefulWidget {
  const LoginView({super.key});

  @override
  State<LoginView> createState() => _LoginViewState();
}

class _LoginViewState extends State<LoginView> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _captchaController = TextEditingController();
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    _captchaController.dispose();
    super.dispose();
  }

  void _doLogin() async {
    // Hide keyboard
    FocusScope.of(context).unfocus();

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    
    // Check if fields are empty
    if (_emailController.text.isEmpty || _passwordController.text.isEmpty || _captchaController.text.isEmpty) {
       ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Email, Password dan Captcha tidak boleh kosong')),
       );
       return;
    }

    final success = await authProvider.loginWithEmail(
      _emailController.text,
      _passwordController.text,
      _captchaController.text,
    );

    if (success && mounted) {
      // Clear navigation stack and go to Dashboard
      Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (context) => const DashboardView()),
        (route) => false,
      );
    } else if (mounted) {
      // Refresh captcha on failure
      authProvider.refreshCaptcha();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(authProvider.errorMessage.isNotEmpty 
            ? authProvider.errorMessage 
            : 'Login gagal, periksa email dan password'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black87, // Dimmed background like web popup
      body: Center(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: _buildLoginCard(context),
          ),
        ),
      ),
    );
  }

  Widget _buildLoginCard(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final isDesktop = size.width > 800;

    return Container(
      width: isDesktop ? 893 : double.infinity,
      constraints: const BoxConstraints(minHeight: 400),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(30),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      clipBehavior: Clip.antiAlias,
      child: Stack(
        children: [
          // Background SVG
          Positioned.fill(
            child: SvgPicture.asset('assets/images/background/login-bg.svg', fit: BoxFit.cover),
          ),
          
          // Layout
          Row(
            children: [
              if (isDesktop)
                Expanded(
                  flex: 55,
                  child: Center(
                    child: Padding(
                      padding: const EdgeInsets.all(32.0),
                      child: SvgPicture.asset('assets/images/icon/kiri-login.svg', width: 300),
                    ),
                  ),
                ),
              Expanded(
                flex: 45,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 40),
                  color: isDesktop ? Colors.white : Colors.white.withOpacity(0.95),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Close button for mobile to go back to landing
                      if (!isDesktop)
                         Align(
                           alignment: Alignment.topRight,
                           child: IconButton(
                             icon: const Icon(Icons.close, color: Colors.black54),
                             onPressed: () => Navigator.pop(context),
                           ),
                         ),
                      const Text(
                        'Log In',
                        textAlign: TextAlign.center,
                        style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Color(0xFF0A2540)),
                      ),
                      const SizedBox(height: 32),
                      
                      // Email
                      TextField(
                        controller: _emailController,
                        keyboardType: TextInputType.emailAddress,
                        decoration: InputDecoration(
                          hintText: 'Email',
                          filled: true,
                          fillColor: Colors.white,
                          suffixIcon: const Icon(Icons.email_outlined, color: Colors.grey),
                          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFE2E8F0), width: 2)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFE2E8F0), width: 2)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFF4299E1), width: 2)),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Password
                      TextField(
                        controller: _passwordController,
                        obscureText: _obscurePassword,
                        decoration: InputDecoration(
                          hintText: 'Password',
                          filled: true,
                          fillColor: Colors.white,
                          suffixIcon: IconButton(
                            icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility, color: Colors.grey),
                            onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                          ),
                          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFE2E8F0), width: 2)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFE2E8F0), width: 2)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFF4299E1), width: 2)),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Captcha
                      const Text('Kode Keamanan', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Color(0xFF0A2540))),
                      const SizedBox(height: 8),
                      TextField(
                        controller: _captchaController,
                        textCapitalization: TextCapitalization.characters,
                        decoration: InputDecoration(
                          hintText: 'Masukkan kode',
                          filled: true,
                          fillColor: Colors.white,
                          contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFE2E8F0), width: 2)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFE2E8F0), width: 2)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFF4299E1), width: 2)),
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Captcha Image Row
                      Row(
                        children: [
                          Expanded(
                            child: Consumer<AuthProvider>(
                              builder: (context, auth, _) {
                                if (auth.isCaptchaLoading) {
                                  return Container(
                                    height: 60,
                                    decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(8), border: Border.all(color: const Color(0xFFE2E8F0))),
                                    child: const Center(child: CircularProgressIndicator()),
                                  );
                                }
                                return Container(
                                  height: 60,
                                  decoration: BoxDecoration(
                                    color: Colors.white, 
                                    borderRadius: BorderRadius.circular(8), 
                                    border: Border.all(color: const Color(0xFFE2E8F0)),
                                    image: const DecorationImage(
                                      image: AssetImage('assets/images/background/hero-sec.svg'), // fallback noise
                                      fit: BoxFit.cover,
                                      opacity: 0.1,
                                    ),
                                  ),
                                  child: Center(
                                    child: Text(
                                      auth.captchaCodeText,
                                      style: const TextStyle(
                                        fontSize: 24,
                                        fontWeight: FontWeight.bold,
                                        letterSpacing: 8,
                                        fontFamily: 'Courier',
                                        color: Color(0xFF0A2540),
                                        decoration: TextDecoration.lineThrough,
                                        decorationStyle: TextDecorationStyle.double,
                                      ),
                                    ),
                                  ),
                                );
                              },
                            ),
                          ),
                          const SizedBox(width: 8),
                          Container(
                            height: 60,
                            width: 60,
                            decoration: BoxDecoration(color: const Color(0xFF4299E1), borderRadius: BorderRadius.circular(12)),
                            child: IconButton(
                              icon: const Icon(Icons.refresh, color: Colors.white),
                              onPressed: () {
                                Provider.of<AuthProvider>(context, listen: false).refreshCaptcha();
                              },
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      const Text('Masukkan kode yang terlihat pada gambar', style: TextStyle(fontSize: 12, color: Colors.grey)),
                      const SizedBox(height: 24),

                      // Submit Button
                      Consumer<AuthProvider>(
                        builder: (context, auth, child) {
                          return Container(
                            height: 56,
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(colors: [Color(0xFF22D3EE), Color(0xFF3B82F6)]),
                              borderRadius: BorderRadius.circular(28),
                              boxShadow: [BoxShadow(color: const Color(0xFF3B82F6).withOpacity(0.3), blurRadius: 10, offset: const Offset(0, 4))],
                            ),
                            child: ElevatedButton(
                              onPressed: auth.isLoading ? null : _doLogin,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.transparent,
                                shadowColor: Colors.transparent,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(28)),
                              ),
                              child: auth.isLoading
                                  ? const CircularProgressIndicator(color: Colors.white)
                                  : const Text('Log in', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
                            ),
                          );
                        },
                      ),
                      const SizedBox(height: 24),
                      Center(
                        child: TextButton(
                          onPressed: () {},
                          child: const Text('Lupa Password?', style: TextStyle(color: Color(0xFF0A2540), fontWeight: FontWeight.w600)),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
