<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('beranda');
});

Route::get('/captcha', [CaptchaController::class, 'generate']);
Route::post('/login', [AuthController::class, 'login']);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UsulanController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\LpjController;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboardController;
use App\Http\Controllers\Bendahara\PencairanDanaController;
use App\Http\Controllers\Bendahara\LpjController as BendaharaLpjController;
use App\Http\Controllers\Bendahara\RiwayatController;
use App\Http\Controllers\Bendahara\AkunController as BendaharaAkunController;
use App\Http\Controllers\Verifikator\VerifikatorController;
use App\Http\Controllers\Verifikator\TelaahController;
use App\Http\Controllers\Verifikator\RiwayatController as VerifikatorRiwayatController;
use App\Http\Controllers\Verifikator\MonitoringController as VerifikatorMonitoringController;
use App\Http\Controllers\Verifikator\AkunController as VerifikatorAkunController;
use App\Http\Controllers\Ppk\PpkController;
use App\Http\Controllers\Ppk\KegiatanController as PpkKegiatanController;
use App\Http\Controllers\Ppk\RiwayatController as PpkRiwayatController;
use App\Http\Controllers\Ppk\MonitoringController as PpkMonitoringController;
use App\Http\Controllers\Ppk\AkunController as PpkAkunController;
use App\Http\Controllers\Wadir\WadirController;
use App\Http\Controllers\Wadir\KegiatanController as WadirKegiatanController;
use App\Http\Controllers\Wadir\RiwayatController as WadirRiwayatController;
use App\Http\Controllers\Wadir\MonitoringController as WadirMonitoringController;
use App\Http\Controllers\Wadir\AkunController as WadirAkunController;
use App\Http\Controllers\Direktur\DirekturController;
use App\Http\Controllers\Direktur\MonitoringController as DirekturMonitoringController;
use App\Http\Controllers\Direktur\AkunController as DirekturAkunController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\UserManagementController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\IkuController as SuperAdminIkuController;
use App\Http\Controllers\SuperAdmin\AkunController as SuperAdminAkunController;
use App\Http\Middleware\CheckRole;

// Authentication required routes
Route::middleware([CheckRole::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Web Notification API routes
    Route::get('/api/notifikasi', [\App\Http\Controllers\WebNotifikasiController::class, 'index']);
    Route::post('/api/notifikasi/baca/{id}', [\App\Http\Controllers\WebNotifikasiController::class, 'markAsRead']);
    Route::post('/api/notifikasi/baca-semua', [\App\Http\Controllers\WebNotifikasiController::class, 'markAllAsRead']);

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        // Usulan (Pengajuan KAK)
        Route::get('/pengajuan-usulan', [UsulanController::class, 'index'])->name('admin.usulan.index');
        Route::get('/pengajuan-usulan/show/{id}', [UsulanController::class, 'show'])->name('admin.usulan.show');
        Route::get('/pengajuan-usulan/edit/{id}', [UsulanController::class, 'edit'])->name('admin.usulan.edit');
        Route::post('/pengajuan-usulan/store', [UsulanController::class, 'store'])->name('admin.usulan.store');
        Route::put('/pengajuan-usulan/update/{id}', [UsulanController::class, 'update'])->name('admin.usulan.update');
        Route::post('/pengajuan-usulan/selesai/{id}', [UsulanController::class, 'selesai'])->name('admin.usulan.selesai');

        // Kegiatan (KAK List)
        Route::get('/pengajuan-kegiatan', [KegiatanController::class, 'index'])->name('admin.kegiatan.index');
        Route::get('/pengajuan-kegiatan/show/{id}', [KegiatanController::class, 'detail'])->name('admin.kegiatan.detail');
        Route::post('/pengajuan-kegiatan/submitrincian', [KegiatanController::class, 'storeRincian'])->name('admin.kegiatan.store-rincian');

        // LPJ
        Route::get('/pengajuan-lpj', [LpjController::class, 'index'])->name('admin.lpj.index');
        Route::get('/pengajuan-lpj/show/{id}', [LpjController::class, 'detail'])->name('admin.lpj.detail');
        Route::post('/pengajuan-lpj/store', [LpjController::class, 'store'])->name('admin.lpj.store');

        // Akun
        Route::get('/akun', [AkunController::class, 'index'])->name('admin.akun.index');
        Route::patch('/akun/update', [AkunController::class, 'update'])->name('admin.akun.update');
        Route::patch('/akun/password', [AkunController::class, 'changePassword'])->name('admin.akun.password');
    });

    // ─── BENDAHARA ROUTES ────────────────────────────────────────────
    Route::prefix('bendahara')->group(function () {
        Route::get('/dashboard', [BendaharaDashboardController::class, 'index'])->name('bendahara.dashboard');

        // Pencairan Dana
        Route::get('/pencairan-dana', [PencairanDanaController::class, 'index'])->name('bendahara.pencairan.index');
        Route::get('/pencairan-dana/show/{id}', [PencairanDanaController::class, 'detail'])->name('bendahara.pencairan.detail');
        Route::post('/pencairan-dana/proses', [PencairanDanaController::class, 'proses'])->name('bendahara.pencairan.proses');

        // LPJ
        Route::get('/lpj', [BendaharaLpjController::class, 'index'])->name('bendahara.lpj.index');
        Route::get('/lpj/show/{id}', [BendaharaLpjController::class, 'detail'])->name('bendahara.lpj.detail');
        Route::post('/lpj/proses/{id}', [BendaharaLpjController::class, 'proses'])->name('bendahara.lpj.proses');

        // Riwayat Verifikasi
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('bendahara.riwayat.index');
        Route::get('/riwayat/show/{id}', [RiwayatController::class, 'detail'])->name('bendahara.riwayat.detail');

        // Akun
        Route::get('/akun', [BendaharaAkunController::class, 'index'])->name('bendahara.akun.index');
        Route::patch('/akun/update', [BendaharaAkunController::class, 'update'])->name('bendahara.akun.update');
        Route::patch('/akun/password', [BendaharaAkunController::class, 'changePassword'])->name('bendahara.akun.password');
    });

    // ─── VERIFIKATOR ROUTES ──────────────────────────────────────────
    Route::prefix('verifikator')->group(function () {
        Route::get('/dashboard', [VerifikatorController::class, 'dashboard'])->name('verifikator.dashboard');

        // Telaah
        Route::get('/telaah', [TelaahController::class, 'index'])->name('verifikator.telaah.index');
        Route::get('/telaah/show/{id}', [TelaahController::class, 'show'])->name('verifikator.telaah.show');
        Route::post('/telaah/store/{id}', [TelaahController::class, 'store'])->name('verifikator.telaah.store');

        // Riwayat
        Route::get('/riwayat', [VerifikatorRiwayatController::class, 'index'])->name('verifikator.riwayat.index');

        // Monitoring
        Route::get('/monitoring', [VerifikatorMonitoringController::class, 'index'])->name('verifikator.monitoring.index');

        // Akun
        Route::get('/akun', [VerifikatorAkunController::class, 'index'])->name('verifikator.akun.index');
        Route::patch('/akun/update', [VerifikatorAkunController::class, 'update'])->name('verifikator.akun.update');
        Route::patch('/akun/password', [VerifikatorAkunController::class, 'changePassword'])->name('verifikator.akun.password');
    });

    // ─── PPK ROUTES ────────────────────────────────────────────────
    Route::prefix('ppk')->group(function () {
        Route::get('/dashboard', [PpkController::class, 'dashboard'])->name('ppk.dashboard');

        // Kegiatan
        Route::get('/kegiatan', [PpkKegiatanController::class, 'index'])->name('ppk.kegiatan.index');
        Route::get('/kegiatan/show/{id}', [PpkKegiatanController::class, 'show'])->name('ppk.kegiatan.show');
        Route::post('/kegiatan/store/{id}', [PpkKegiatanController::class, 'store'])->name('ppk.kegiatan.store');

        // Riwayat
        Route::get('/riwayat', [PpkRiwayatController::class, 'index'])->name('ppk.riwayat.index');

        // Monitoring
        Route::get('/monitoring', [PpkMonitoringController::class, 'index'])->name('ppk.monitoring.index');

        // Akun
        Route::get('/akun', [PpkAkunController::class, 'index'])->name('ppk.akun.index');
        Route::patch('/akun/update', [PpkAkunController::class, 'update'])->name('ppk.akun.update');
        Route::patch('/akun/password', [PpkAkunController::class, 'changePassword'])->name('ppk.akun.password');
    });

    // ─── WADIR ROUTES ──────────────────────────────────────────────
    Route::prefix('wadir')->group(function () {
        Route::get('/dashboard', [WadirController::class, 'dashboard'])->name('wadir.dashboard');
        Route::get('/kegiatan', [WadirKegiatanController::class, 'index'])->name('wadir.kegiatan.index');
        Route::get('/kegiatan/show/{id}', [WadirKegiatanController::class, 'show'])->name('wadir.kegiatan.show');
        Route::post('/kegiatan/store/{id}', [WadirKegiatanController::class, 'store'])->name('wadir.kegiatan.store');
        Route::get('/riwayat', [WadirRiwayatController::class, 'index'])->name('wadir.riwayat.index');
        Route::get('/monitoring', [WadirMonitoringController::class, 'index'])->name('wadir.monitoring.index');
        Route::get('/monitoring/data', [WadirMonitoringController::class, 'getData'])->name('wadir.monitoring.data');
        Route::get('/akun', [WadirAkunController::class, 'index'])->name('wadir.akun.index');
        Route::patch('/akun/update', [WadirAkunController::class, 'update'])->name('wadir.akun.update');
        Route::patch('/akun/password', [WadirAkunController::class, 'changePassword'])->name('wadir.akun.password');
    });

    // ─── DIREKTUR ROUTES ───────────────────────────────────────────
    Route::prefix('direktur')->group(function () {
        Route::get('/dashboard', [DirekturController::class, 'dashboard'])->name('direktur.dashboard');
        Route::get('/dashboard/api/dana-per-jurusan', [DirekturController::class, 'getDanaPerJurusan'])->name('direktur.dashboard.api.dana');
        Route::get('/monitoring', [DirekturMonitoringController::class, 'index'])->name('direktur.monitoring.index');
        Route::get('/monitoring/data', [DirekturMonitoringController::class, 'getData'])->name('direktur.monitoring.data');
        Route::get('/akun', [DirekturAkunController::class, 'index'])->name('direktur.akun.index');
        Route::patch('/akun/update', [DirekturAkunController::class, 'update'])->name('direktur.akun.update');
        Route::patch('/akun/password', [DirekturAkunController::class, 'changePassword'])->name('direktur.akun.password');
    });

    // ─── SUPERADMIN ROUTES ─────────────────────────────────────────
    Route::prefix('superadmin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
        Route::get('/get-ai-analysis', [SuperAdminController::class, 'getAiAnalysis'])->name('superadmin.ai.analysis');
        Route::get('/kelola-akun', [SuperAdminUserController::class, 'index'])->name('superadmin.users.index');
        Route::get('/buat-iku', [SuperAdminIkuController::class, 'index'])->name('superadmin.iku.index');
        Route::get('/monitoring', [SuperAdminController::class, 'monitoring'])->name('superadmin.monitoring');
        Route::get('/akun', [SuperAdminAkunController::class, 'index'])->name('superadmin.akun.index');
        Route::patch('/akun/update', [SuperAdminAkunController::class, 'update'])->name('superadmin.akun.update');
        Route::patch('/akun/password', [SuperAdminAkunController::class, 'changePassword'])->name('superadmin.akun.password');
    });
});
