import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';

class TotpSetupView extends StatefulWidget {
  const TotpSetupView({super.key});

  @override
  State<TotpSetupView> createState() => _TotpSetupViewState();
}

class _TotpSetupViewState extends State<TotpSetupView> {
  final TextEditingController _verifyController = TextEditingController();
  final _formKey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AuthProvider>(context, listen: false).initTotpSetup();
    });
  }

  @override
  void dispose() {
    _verifyController.dispose();
    super.dispose();
  }

  void _verifyAndConfirm() async {
    if (!_formKey.currentState!.validate()) return;
    
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final success = await authProvider.confirmTotpSetup(_verifyController.text);
    
    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Google Authenticator 2FA Berhasil Diaktifkan!'),
          backgroundColor: AppTheme.accentTeal,
        ),
      );
      Navigator.pop(context);
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(authProvider.errorMessage.isNotEmpty 
              ? authProvider.errorMessage 
              : 'Verifikasi Gagal.'),
          backgroundColor: Colors.redAccent,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Keamanan Dua Faktor (2FA)'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Current 2FA Status Card
              Card(
                color: authProvider.isTotpEnabled 
                    ? Colors.teal.shade50 
                    : Colors.orange.shade50,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Row(
                    children: [
                      Icon(
                        authProvider.isTotpEnabled ? Icons.verified_user : Icons.warning_amber_rounded,
                        color: authProvider.isTotpEnabled ? Colors.teal : Colors.orange,
                        size: 32,
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              authProvider.isTotpEnabled ? '2FA Aktif' : '2FA Belum Aktif',
                              style: TextStyle(
                                fontWeight: FontWeight.bold,
                                color: authProvider.isTotpEnabled ? Colors.teal.shade900 : Colors.orange.shade900,
                              ),
                            ),
                            Text(
                              authProvider.isTotpEnabled
                                  ? 'Akun Anda dilindungi oleh Google Authenticator.'
                                  : 'Aktifkan Google Authenticator untuk keamanan ekstra.',
                              style: TextStyle(
                                fontSize: 12,
                                color: authProvider.isTotpEnabled ? Colors.teal.shade700 : Colors.orange.shade700,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),

              if (authProvider.isTotpEnabled) ...[
                // Option to Disable 2FA
                ElevatedButton(
                  onPressed: () {
                    authProvider.disableTotp();
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Google Authenticator 2FA dinonaktifkan.')),
                    );
                  },
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.redAccent),
                  child: const Text('Nonaktifkan 2FA'),
                ),
              ] else ...[
                // Setup Guide Header
                const Text(
                  'Langkah Aktivasi Google Authenticator',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.primaryBlue),
                ),
                const SizedBox(height: 16),

                // Step 1
                _buildStep(
                  '1. Pasang Aplikasi',
                  'Unduh dan buka aplikasi "Google Authenticator" dari Play Store atau App Store.',
                ),
                const SizedBox(height: 12),

                // Step 2
                _buildStep(
                  '2. Pindai QR Code atau Masukkan Kunci Manual',
                  'Di Google Authenticator, ketuk ikon "+" lalu pilih "Pindai Kode QR" atau masukkan Kunci Rahasia manual.',
                ),
                const SizedBox(height: 16),

                // QR Code Render
                if (authProvider.totpQrUrl.isNotEmpty)
                  Center(
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: AppTheme.borderLight),
                      ),
                      child: QrImageView(
                        data: authProvider.totpQrUrl,
                        version: QrVersions.auto,
                        size: 200.0,
                      ),
                    ),
                  ),
                const SizedBox(height: 12),

                // Manual Secret Key Display
                Center(
                  child: Text(
                    'Kunci Rahasia: ${authProvider.totpSecret}',
                    style: const TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1.2, fontSize: 13),
                  ),
                ),
                const SizedBox(height: 24),

                // Step 3
                _buildStep(
                  '3. Verifikasi Kode 6-Digit',
                  'Masukkan 6-digit angka terbaru yang tertera pada aplikasi Google Authenticator Anda untuk mengonfirmasi.',
                ),
                const SizedBox(height: 12),

                // Code Input Field
                TextFormField(
                  controller: _verifyController,
                  keyboardType: TextInputType.number,
                  maxLength: 6,
                  textAlign: TextAlign.center,
                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, letterSpacing: 4),
                  decoration: const InputDecoration(
                    hintText: '000 000',
                    counterText: '',
                  ),
                  validator: (value) {
                    if (value == null || value.trim().length != 6) {
                      return 'Masukkan 6 digit kode';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 24),

                // Confirm Button
                ElevatedButton(
                  onPressed: _verifyAndConfirm,
                  child: const Text('Aktifkan 2FA'),
                ),
                const SizedBox(height: 16),
                
                Center(
                  child: Text(
                    'Kunci Tes: 123456',
                    style: TextStyle(color: Colors.teal.shade800, fontSize: 12, fontStyle: FontStyle.italic),
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStep(String title, String desc) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
        const SizedBox(height: 2),
        Text(desc, style: const TextStyle(color: AppTheme.textMuted, fontSize: 12)),
      ],
    );
  }
}
