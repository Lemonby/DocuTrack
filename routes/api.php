<?php

use Illuminate\Support\Facades\Route;

// --- Controller Imports ---
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\NotifikasiController;

// Admin
use App\Http\Controllers\API\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\API\Admin\AkunController as AdminAkun;
use App\Http\Controllers\API\Admin\PengajuanUsulanController;
use App\Http\Controllers\API\Admin\PengajuanKegiatanController as AdminKegiatan;
use App\Http\Controllers\API\Admin\DetailKakController;
use App\Http\Controllers\API\Admin\PengajuanLpjController as AdminLpj;

// Verifikator
use App\Http\Controllers\API\Verifikator\DashboardController as VerifikatorDashboard;
use App\Http\Controllers\API\Verifikator\AkunController as VerifikatorAkun;
use App\Http\Controllers\API\Verifikator\TelaahController as VerifikatorTelaah;
use App\Http\Controllers\API\Verifikator\RiwayatController as VerifikatorRiwayat;

// PPK
use App\Http\Controllers\API\PPK\DashboardController as PPKDashboard;
use App\Http\Controllers\API\PPK\AkunController as PPKAkun;
use App\Http\Controllers\API\PPK\TelaahController as PPKTelaah;
use App\Http\Controllers\API\PPK\MonitoringController as PPKMonitoring;
use App\Http\Controllers\API\PPK\RiwayatController as PPKRiwayat;

// Wadir
use App\Http\Controllers\API\Wadir\DashboardController as WadirDashboard;
use App\Http\Controllers\API\Wadir\AkunController as WadirAkun;
use App\Http\Controllers\API\Wadir\TelaahController as WadirTelaah;
use App\Http\Controllers\API\Wadir\MonitoringController as WadirMonitoring;
use App\Http\Controllers\API\Wadir\RiwayatController as WadirRiwayat;

// Bendahara
use App\Http\Controllers\API\Bendahara\DashboardController as BendaharaDashboard;
use App\Http\Controllers\API\Bendahara\AkunController as BendaharaAkun;
use App\Http\Controllers\API\Bendahara\PencairanDanaController;
use App\Http\Controllers\API\Bendahara\PengajuanLpjController as BendaharaLpj;
use App\Http\Controllers\API\Bendahara\RiwayatController as BendaharaRiwayat;

// SuperAdmin
use App\Http\Controllers\API\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\API\SuperAdmin\AkunController as SuperAdminAkun;
use App\Http\Controllers\API\SuperAdmin\KelolaAkunController;
use App\Http\Controllers\API\SuperAdmin\BuatIkuController;
use App\Http\Controllers\API\SuperAdmin\MonitoringController as SuperAdminMonitoring;
use App\Http\Controllers\API\SuperAdmin\AiMonitoringController;

// Direktur
use App\Http\Controllers\API\Direktur\DashboardController as DirekturDashboard;
use App\Http\Controllers\API\Direktur\AkunController as DirekturAkun;
use App\Http\Controllers\API\Direktur\MonitoringController as DirekturMonitoring;
use App\Http\Controllers\API\Direktur\IntegritasController as DirekturIntegritas;

/*
|--------------------------------------------------------------------------
| API Routes — DocuTrack REST API v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // === Public (unauthenticated) ===
    Route::get('/', fn () => response()->json([
        'success' => true,
        'message' => 'Welcome to DocuTrack API v1',
        'status' => 'online'
    ]));

    Route::post('/captcha', [AuthController::class, 'captcha']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // === Authenticated ===
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Notifikasi (all roles)
        Route::prefix('notifikasi')->group(function () {
            Route::get('/', [NotifikasiController::class, 'index']);
            Route::post('/{id}/baca', [NotifikasiController::class, 'markAsRead']);
            Route::post('/baca-semua', [NotifikasiController::class, 'markAllAsRead']);
        });

        // Master data (shared)
        Route::get('/jurusan', fn () => response()->json([
            'success' => true,
            'data' => \App\Models\Jurusan::with('prodis')->get(),
        ]));
        Route::get('/prodi', fn () => response()->json([
            'success' => true,
            'data' => \App\Models\Prodi::all(),
        ]));
        Route::get('/wadir', fn () => response()->json([
            'success' => true,
            'data' => \App\Models\Wadir::all(),
        ]));
        Route::get('/iku', fn () => response()->json([
            'success' => true,
            'data' => \App\Models\Iku::all(),
        ]));

        // ===============================
        // ADMIN
        // ===============================
        Route::middleware('role:Admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminDashboard::class, 'index']);
            Route::get('/akun', [AdminAkun::class, 'show']);
            Route::put('/akun', [AdminAkun::class, 'update']);

            // Pengajuan Usulan
            Route::prefix('usulan')->group(function () {
                Route::get('/', [PengajuanUsulanController::class, 'index']);
                Route::post('/', [PengajuanUsulanController::class, 'store']);
                Route::get('/{id}', [PengajuanUsulanController::class, 'show']);
                Route::put('/{id}', [PengajuanUsulanController::class, 'update']);
                Route::delete('/{id}', [PengajuanUsulanController::class, 'destroy']);
                Route::post('/{id}/selesai', [PengajuanUsulanController::class, 'selesai']);
            });

            // Pengajuan Kegiatan (rincian)
            Route::prefix('kegiatan')->group(function () {
                Route::get('/', [AdminKegiatan::class, 'index']);
                Route::get('/{id}', [AdminKegiatan::class, 'show']);
                Route::post('/submit-rincian', [AdminKegiatan::class, 'submitRincian']);
            });

            // KAK Detail
            Route::prefix('kak')->group(function () {
                Route::get('/{id}', [DetailKakController::class, 'show']);
                Route::post('/{id}/resubmit', [DetailKakController::class, 'resubmit']);
            });

            // LPJ
            Route::prefix('lpj')->group(function () {
                Route::get('/', [AdminLpj::class, 'index']);
                Route::get('/{id}', [AdminLpj::class, 'show']);
                Route::post('/upload-bukti', [AdminLpj::class, 'uploadBukti']);
                Route::post('/submit', [AdminLpj::class, 'submit']);
            });
        });

        // ===============================
        // VERIFIKATOR
        // ===============================
        Route::middleware('role:Verifikator')->prefix('verifikator')->group(function () {
            Route::get('/dashboard', [VerifikatorDashboard::class, 'index']);
            Route::get('/akun', [VerifikatorAkun::class, 'show']);
            Route::put('/akun', [VerifikatorAkun::class, 'update']);

            Route::prefix('telaah')->group(function () {
                Route::get('/', [VerifikatorTelaah::class, 'index']);
                Route::get('/{id}', [VerifikatorTelaah::class, 'show']);
                Route::post('/{id}/approve', [VerifikatorTelaah::class, 'approve']);
                Route::post('/{id}/reject', [VerifikatorTelaah::class, 'reject']);
                Route::post('/{id}/revise', [VerifikatorTelaah::class, 'revise']);
            });

            Route::get('/riwayat', [VerifikatorRiwayat::class, 'index']);
        });

        // ===============================
        // PPK
        // ===============================
        Route::middleware('role:PPK')->prefix('ppk')->group(function () {
            Route::get('/dashboard', [PPKDashboard::class, 'index']);
            Route::get('/akun', [PPKAkun::class, 'show']);
            Route::put('/akun', [PPKAkun::class, 'update']);

            Route::prefix('telaah')->group(function () {
                Route::get('/', [PPKTelaah::class, 'index']);
                Route::get('/{id}', [PPKTelaah::class, 'show']);
                Route::post('/{id}/approve', [PPKTelaah::class, 'approve']);
                Route::post('/{id}/reject', [PPKTelaah::class, 'reject']);
                Route::post('/{id}/revise', [PPKTelaah::class, 'revise']);
            });

            Route::get('/monitoring', [PPKMonitoring::class, 'index']);
            Route::get('/riwayat', [PPKRiwayat::class, 'index']);
        });

        // ===============================
        // WADIR
        // ===============================
        Route::middleware('role:Wadir')->prefix('wadir')->group(function () {
            Route::get('/dashboard', [WadirDashboard::class, 'index']);
            Route::get('/akun', [WadirAkun::class, 'show']);
            Route::put('/akun', [WadirAkun::class, 'update']);

            Route::prefix('telaah')->group(function () {
                Route::get('/', [WadirTelaah::class, 'index']);
                Route::get('/{id}', [WadirTelaah::class, 'show']);
                Route::post('/{id}/approve', [WadirTelaah::class, 'approve']);
                Route::post('/{id}/reject', [WadirTelaah::class, 'reject']);
                Route::post('/{id}/revise', [WadirTelaah::class, 'revise']);
            });

            Route::get('/monitoring', [WadirMonitoring::class, 'index']);
            Route::get('/riwayat', [WadirRiwayat::class, 'index']);
        });

        // ===============================
        // BENDAHARA
        // ===============================
        Route::middleware('role:Bendahara')->prefix('bendahara')->group(function () {
            Route::get('/dashboard', [BendaharaDashboard::class, 'index']);
            Route::get('/akun', [BendaharaAkun::class, 'show']);
            Route::put('/akun', [BendaharaAkun::class, 'update']);

            Route::prefix('pencairan')->group(function () {
                Route::get('/', [PencairanDanaController::class, 'index']);
                Route::get('/{id}', [PencairanDanaController::class, 'show']);
                Route::post('/proses', [PencairanDanaController::class, 'proses']);
            });

            Route::prefix('lpj')->group(function () {
                Route::get('/', [BendaharaLpj::class, 'index']);
                Route::get('/{id}', [BendaharaLpj::class, 'show']);
                Route::post('/proses', [BendaharaLpj::class, 'proses']);
            });

            Route::prefix('riwayat')->group(function () {
                Route::get('/', [BendaharaRiwayat::class, 'index']);
                Route::get('/{id}', [BendaharaRiwayat::class, 'show']);
            });
        });

        // ===============================
        // SUPERADMIN
        // ===============================
        Route::middleware('role:SuperAdmin')->prefix('superadmin')->group(function () {
            Route::get('/dashboard', [SuperAdminDashboard::class, 'index']);
            Route::get('/akun', [SuperAdminAkun::class, 'show']);
            Route::put('/akun', [SuperAdminAkun::class, 'update']);

            Route::apiResource('users', KelolaAkunController::class);
            Route::apiResource('iku', BuatIkuController::class);

            Route::get('/monitoring', [SuperAdminMonitoring::class, 'index']);

            // AI Monitoring
            Route::prefix('ai-monitoring')->group(function () {
                Route::get('/stats', [AiMonitoringController::class, 'stats']);
                Route::get('/log-summaries', [AiMonitoringController::class, 'logSummaries']);
                Route::get('/security-alerts', [AiMonitoringController::class, 'securityAlerts']);
            });
        });

        // ===============================
        // DIREKTUR
        // ===============================
        Route::middleware('role:Direktur')->prefix('direktur')->group(function () {
            Route::get('/dashboard', [DirekturDashboard::class, 'index']);
            Route::get('/dashboard/usulan-per-jurusan', [DirekturDashboard::class, 'usulanPerJurusan']);
            Route::get('/dashboard/dana-per-jurusan', [DirekturDashboard::class, 'danaPerJurusan']);
            Route::get('/dashboard/pengajuan', [DirekturDashboard::class, 'pengajuanTerbaru']);
            Route::get('/akun', [DirekturAkun::class, 'show']);
            Route::put('/akun', [DirekturAkun::class, 'update']);
            Route::get('/monitoring', [DirekturMonitoring::class, 'index']);
            Route::get('/integritas', [DirekturIntegritas::class, 'index']);
        });
    });
});
