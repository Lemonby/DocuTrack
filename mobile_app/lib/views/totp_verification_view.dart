import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';
import 'dashboard_view.dart';
import 'login_view.dart';

class TotpVerificationView extends StatefulWidget {
  const TotpVerificationView({super.key});

  @override
  State<TotpVerificationView> createState() => _TotpVerificationViewState();
}

class _TotpVerificationViewState extends State<TotpVerificationView> {
  final TextEditingController _codeController = TextEditingController();
  final _formKey = GlobalKey<FormState>();

  @override
  void dispose() {
    _codeController.dispose();
    super.dispose();
  }

  void _verify(BuildContext context) {
    if (!_formKey.currentState!.validate()) return;
    
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final success = authProvider.verifyTotpCode(_codeController.text);
    
    if (success) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (context) => const DashboardView()),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(authProvider.errorMessage.isNotEmpty 
              ? authProvider.errorMessage 
              : 'Kode salah.'),
          backgroundColor: Colors.redAccent,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: AppTheme.heroGradient,
        ),
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: Card(
                elevation: 8,
                shadowColor: Colors.black.withOpacity(0.2),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
                child: Padding(
                  padding: const EdgeInsets.all(32.0),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(
                          Icons.security_outlined,
                          size: 64,
                          color: AppTheme.primaryBlue,
                        ),
                        const SizedBox(height: 16),
                        Text(
                          'Verifikasi 2FA',
                          style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                            fontWeight: FontWeight.bold,
                            color: AppTheme.primaryBlue,
                          ),
                        ),
                        const SizedBox(height: 12),
                        const Text(
                          'Masukkan 6 digit kode dari aplikasi Google Authenticator Anda.',
                          textAlign: TextAlign.center,
                          style: TextStyle(color: AppTheme.textMuted),
                        ),
                        const SizedBox(height: 24),
                        
                        // Digit Input Field
                        TextFormField(
                          controller: _codeController,
                          keyboardType: TextInputType.number,
                          maxLength: 6,
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontSize: 24, 
                            fontWeight: FontWeight.bold, 
                            letterSpacing: 8,
                            color: AppTheme.primaryBlue,
                          ),
                          decoration: InputDecoration(
                            hintText: '000000',
                            counterText: '',
                            hintStyle: TextStyle(
                              color: AppTheme.textMuted.withOpacity(0.3),
                              letterSpacing: 8,
                            ),
                          ),
                          validator: (value) {
                            if (value == null || value.trim().length != 6) {
                              return 'Harap masukkan 6 digit kode';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 24),
                        
                        // Error message
                        if (authProvider.errorMessage.isNotEmpty)
                          Padding(
                            padding: const EdgeInsets.only(bottom: 16),
                            child: Text(
                              authProvider.errorMessage,
                              style: const TextStyle(color: Colors.redAccent, fontSize: 13),
                              textAlign: TextAlign.center,
                            ),
                          ),

                        // Action Buttons
                        Row(
                          children: [
                            Expanded(
                              child: OutlinedButton(
                                onPressed: () {
                                  authProvider.cancelTotpVerification();
                                  Navigator.of(context).pushReplacement(
                                    MaterialPageRoute(builder: (context) => const LoginView()),
                                  );
                                },
                                style: OutlinedButton.styleFrom(
                                  padding: const EdgeInsets.symmetric(vertical: 16),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  side: const BorderSide(color: AppTheme.borderLight),
                                ),
                                child: const Text(
                                  'Batal',
                                  style: TextStyle(color: AppTheme.primaryBlue, fontWeight: FontWeight.bold),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: ElevatedButton(
                                onPressed: () => _verify(context),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: AppTheme.primaryBlue,
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(vertical: 16),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                ),
                                child: const Text('Verifikasi'),
                              ),
                            ),
                          ],
                        ),
                        
                        const SizedBox(height: 16),
                        // Testing tip
                        Text(
                          'Tips: Gunakan kode "123456" untuk mempermudah testing.',
                          style: TextStyle(
                            fontSize: 11,
                            color: Colors.teal.shade700,
                            fontStyle: FontStyle.italic,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
