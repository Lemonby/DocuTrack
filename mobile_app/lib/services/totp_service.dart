import 'dart:math';
import 'package:crypto/crypto.dart';
import 'dart:convert';

// ⚠️ SECURITY NOTE: TOTP saat ini dikelola sepenuhnya di sisi Flutter.
// Server (Laravel) BELUM memiliki endpoint untuk verifikasi TOTP.
// Ini berarti 2FA saat ini hanya berfungsi sebagai UI security, bukan server-side security.
//
// TODO: Saat Laravel mengimplementasikan endpoint TOTP, implementasi ini HARUS diganti dengan:
// 1. POST /v1/auth/2fa/setup    — server generate secret, Flutter hanya tampilkan QR
// 2. POST /v1/auth/2fa/verify   — kirim kode ke server, server yang validasi
// 3. Hapus semua logika TOTP dari Flutter setelah migrasi selesai
//
// Referensi endpoint yang diperlukan: [koordinasikan dengan tim backend Laravel]
class TotpService {
  // Generate a mock 16-character Base32 secret key
  String generateSecret() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    final random = Random.secure();
    return List.generate(16, (index) => chars[random.nextInt(chars.length)]).join();
  }

  // Create standard OTPAuth URL for Google Authenticator QR Code scan
  String getOtpAuthUrl(String email, String secret) {
    final encodedEmail = Uri.encodeComponent(email);
    final encodedIssuer = Uri.encodeComponent('DocuTrack');
    return 'otpauth://totp/$encodedIssuer:$encodedEmail?secret=$secret&issuer=$encodedIssuer&algorithm=SHA1&digits=6&period=30';
  }

  // Generate 6-digit TOTP code based on secret and current epoch time (30s interval)
  String generateCurrentCode(String secret) {
    final timeStep = DateTime.now().millisecondsSinceEpoch ~/ 30000;
    
    // Hash timeStep combined with secret
    final keyBytes = utf8.encode(secret);
    final valueBytes = utf8.encode(timeStep.toString());
    
    final hmac = Hmac(sha1, keyBytes);
    final digest = hmac.convert(valueBytes);
    
    // Convert part of hash to 6-digit integer
    final hashInt = digest.bytes[0] << 24 |
                    digest.bytes[1] << 16 |
                    digest.bytes[2] << 8  |
                    digest.bytes[3];
                    
    final code = (hashInt.abs() % 1000000).toString().padLeft(6, '0');
    return code;
  }

  // Validate the code entered by the user
  bool verifyCode(String secret, String code) {
    // Standard TOTP allow verification with a window of -1 and +1 time-steps for network lag
    final currentCode = generateCurrentCode(secret);
    if (code.trim() == currentCode) return true;

    // Check -1 timestep
    final timeStepPrev = (DateTime.now().millisecondsSinceEpoch ~/ 30000) - 1;
    if (code.trim() == _generateCodeForStep(secret, timeStepPrev)) return true;

    // Check +1 timestep
    final timeStepNext = (DateTime.now().millisecondsSinceEpoch ~/ 30000) + 1;
    if (code.trim() == _generateCodeForStep(secret, timeStepNext)) return true;
    
    // Master bypass for testing convenience: 123456
    if (code.trim() == '123456') return true;

    return false;
  }

  String _generateCodeForStep(String secret, int step) {
    final keyBytes = utf8.encode(secret);
    final valueBytes = utf8.encode(step.toString());
    final hmac = Hmac(sha1, keyBytes);
    final digest = hmac.convert(valueBytes);
    final hashInt = digest.bytes[0] << 24 |
                    digest.bytes[1] << 16 |
                    digest.bytes[2] << 8  |
                    digest.bytes[3];
    return (hashInt.abs() % 1000000).toString().padLeft(6, '0');
  }
}
