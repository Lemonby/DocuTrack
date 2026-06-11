# DocuTrack Suite

Repositori ini adalah pusat operasional terpadu untuk ekosistem **DocuTrack**, yang terdiri dari backend API berbasis Laravel dan aplikasi mobile berbasis Flutter.

## Struktur Repositori

- **Backend (Root):** Sistem core berbasis Laravel 13.x untuk manajemen dokumen, alur telaah, dan database.
- **Mobile App (`/mobile_app`):** Aplikasi klien berbasis Flutter 3.x untuk akses lintas platform (Android/iOS).

---

## Panduan Memulai (Getting Started)

### 1. Backend (Laravel)
Pastikan Anda memiliki PHP 8.3+ dan Composer terinstal.

```bash
# Install dependensi
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Jalankan migrasi database
php artisan migrate --seed

# Jalankan server
php artisan serve
```

### 2. Mobile App (Flutter)
Masuk ke direktori mobile sebelum menjalankan perintah Flutter.

```bash
cd mobile_app

# Ambil paket dependensi
flutter pub get

# Jalankan aplikasi (Pastikan emulator/perangkat terhubung)
flutter run
```

---

## Fitur Utama
- **Multi-Level Review:** Verifikasi dokumen dari level Verifikator hingga Direktur.
- **Finance Integration:** Pencairan dana termin-based dan validasi LPJ oleh Bendahara.
- **AI Analytics:** Monitoring sistem dan keamanan bertenaga AI.
- **Real-time Notifications:** In-app dan Email notification untuk setiap perubahan status.

## Kontribusi
Silakan lihat dokumentasi masing-masing folder untuk panduan pengembangan lebih lanjut.
