import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  // Brand Colors
  static const Color primaryBlue = Color(0xFF014565); // Deep Navy Blue
  static const Color secondaryBlue = Color(0xFF274B8F); // Indigo Blue
  static const Color accentTeal = Color(0xFF33ABA0); // Teal
  static const Color brightAqua = Color(0xFF00FFBC); // Bright Green/Teal
  static const Color cyanBg = Color(0xFF22D3EE); // Cyan
  
  static const Color bgLight = Color(0xFFF9FAFB); // Gray 50
  static const Color cardLight = Colors.white;
  static const Color textDark = Color(0xFF1F2937); // Gray 800
  static const Color textMuted = Color(0xFF6B7280); // Gray 500
  static const Color borderLight = Color(0xFFE5E7EB); // Gray 200

  // Brand Gradients
  static const LinearGradient heroGradient = LinearGradient(
    colors: [primaryBlue, Color(0xFF014B6F), Color(0xFF08808F), accentTeal],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient buttonGradient = LinearGradient(
    colors: [Color(0xFF3B82F6), Color(0xFF22D3EE)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient cardGradient = LinearGradient(
    colors: [secondaryBlue, Color(0xFF22D3EE)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  // Web Dashboard Gradients
  static const LinearGradient totalUsulanGradient = LinearGradient(
    colors: [Color(0xFF3B82F6), Color(0xFF2563EB)], // blue-500 to blue-600
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient disetujuiGradient = LinearGradient(
    colors: [Color(0xFF10B981), Color(0xFF059669)], // emerald-500 to emerald-600
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient ditolakGradient = LinearGradient(
    colors: [Color(0xFFF43F5E), Color(0xFFE11D48)], // rose-500 to rose-600
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient menungguGradient = LinearGradient(
    colors: [Color(0xFFFCD34D), Color(0xFFF59E0B)], // amber-300 to amber-500
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  // Light Theme Configuration
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      primaryColor: primaryBlue,
      scaffoldBackgroundColor: bgLight,
      colorScheme: const ColorScheme.light(
        primary: primaryBlue,
        secondary: accentTeal,
        tertiary: secondaryBlue,
        surface: bgLight,
      ),
      textTheme: GoogleFonts.poppinsTextTheme(
        ThemeData.light().textTheme.copyWith(
          titleLarge: TextStyle(color: textDark, fontWeight: FontWeight.bold),
          bodyLarge: TextStyle(color: textDark),
          bodyMedium: TextStyle(color: textMuted),
        ),
      ),
      appBarTheme: AppBarTheme(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: primaryBlue),
        titleTextStyle: GoogleFonts.poppins(
          color: primaryBlue,
          fontSize: 20,
          fontWeight: FontWeight.bold,
        ),
      ),
      cardTheme: CardThemeData(
        color: cardLight,
        elevation: 2,
        shadowColor: Colors.black.withOpacity(0.05),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: borderLight, width: 1),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: borderLight),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: borderLight),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: accentTeal, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Colors.redAccent, width: 1),
        ),
        labelStyle: const TextStyle(color: textMuted),
        hintStyle: const TextStyle(color: textMuted),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryBlue,
          foregroundColor: Colors.white,
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          textStyle: GoogleFonts.poppins(
            fontWeight: FontWeight.bold,
            fontSize: 16,
          ),
        ),
      ),
    );
  }
}
