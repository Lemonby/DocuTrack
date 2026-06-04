<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\LogStatus;
use App\Services\ActivityLogService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private ActivityLogService $activityLogService;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed master data dan roles/permissions agar relasi database terpenuhi
        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->activityLogService = $this->app->make(ActivityLogService::class);
    }

    /**
     * Memastikan ActivityLogService::log() berhasil mencatatkan log aktivitas ke database.
     */
    #[Test]
    #[TestDox('Memastikan ActivityLogService::log() berhasil mencatatkan log aktivitas ke database')]
    public function test_service_can_log_activity_successfully(): void
    {
        $user = User::create([
            'nama'         => 'Test Log User',
            'email'        => 'testlog@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);

        $oldVal = ['status' => 'DRAFT'];
        $newVal = ['status' => 'PENDING_VERIFIKATOR'];

        // Mock request to verify IP and User Agent extraction
        $request = Request::create('/test-route', 'POST', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_USER_AGENT' => 'Mozilla/Test-Browser',
        ]);

        $log = $this->activityLogService->log(
            userId: $user->user_id,
            action: 'SUBMIT_PROPOSAL',
            category: 'workflow',
            entityType: 'Kegiatan',
            entityId: 42,
            description: 'Pengusul mengajukan proposal kegiatan baru',
            oldValue: $oldVal,
            newValue: $newVal,
            request: $request
        );

        $this->assertInstanceOf(ActivityLog::class, $log);
        $this->assertDatabaseHas('activity_logs', [
            'log_id' => $log->log_id,
            'user_id' => $user->user_id,
            'action' => 'SUBMIT_PROPOSAL',
            'category' => 'workflow',
            'entity_type' => 'Kegiatan',
            'entity_id' => 42,
            'description' => 'Pengusul mengajukan proposal kegiatan baru',
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Mozilla/Test-Browser',
        ]);

        // Verifikasi casting JSON ke array
        $this->assertEquals($oldVal, $log->old_value);
        $this->assertEquals($newVal, $log->new_value);
    }

    /**
     * Memastikan ActivityLogService::createNotification() berhasil mencatatkan notifikasi ke database.
     */
    #[Test]
    #[TestDox('Memastikan ActivityLogService::createNotification() berhasil mencatatkan notifikasi ke database')]
    public function test_service_can_create_notification_successfully(): void
    {
        $user = User::create([
            'nama'         => 'Test Notif User',
            'email'        => 'testnotif@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);

        $notif = $this->activityLogService->createNotification(
            userId: $user->user_id,
            type: 'APPROVAL',
            message: 'Dokumen Anda disetujui.',
            referenceId: 99
        );

        $this->assertInstanceOf(LogStatus::class, $notif);
        $this->assertDatabaseHas('log_statuses', [
            'id' => $notif->id,
            'user_id' => $user->user_id,
            'tipe_log' => 'APPROVAL',
            'id_referensi' => 99,
            'status' => 'BELUM_DIBACA',
        ]);

        // Cek struktur konten_json
        $konten = $notif->konten_json;
        $this->assertEquals('Dokumen Anda disetujui.', $konten['message']);
        $this->assertArrayHasKey('created_at', $konten);
    }

    /**
     * Memastikan login via API mencatat log aktivitas dan log status secara otomatis.
     */
    #[Test]
    #[TestDox('Memastikan login via API mencatat log aktivitas dan log status secara otomatis')]
    public function test_api_login_records_activity_log_and_log_status(): void
    {
        $user = User::create([
            'nama'         => 'API Log User',
            'email'        => 'apiloguser@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        // Buat captcha palsu di Cache
        $captchaKey = 'test-captcha-key';
        Cache::put("captcha:{$captchaKey}", 'ABCDE', now()->addMinutes(5));

        $response = $this->postJson('/api/v1/login', [
            'email'        => 'apiloguser@example.com',
            'password'     => 'password123',
            'captcha_key'  => $captchaKey,
            'captcha_code' => 'ABCDE',
        ]);

        $response->assertStatus(200);

        // Memastikan tersimpan di tabel activity_logs
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->user_id,
            'action'  => 'LOGIN',
            'category' => 'authentication',
            'entity_type' => 'User',
            'entity_id' => $user->user_id,
        ]);

        // Memastikan tersimpan di tabel log_statuses
        $this->assertDatabaseHas('log_statuses', [
            'user_id' => $user->user_id,
            'tipe_log' => 'LOGIN',
            'status'  => 'DIBACA',
        ]);
    }

    /**
     * Memastikan logout via API mencatat log aktivitas logout.
     */
    #[Test]
    #[TestDox('Memastikan logout via API mencatat log aktivitas logout')]
    public function test_api_logout_records_activity_log(): void
    {
        $user = User::create([
            'nama'         => 'API Logout User',
            'email'        => 'apilogoutuser@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200);

        // Memastikan tersimpan di tabel activity_logs
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->user_id,
            'action'  => 'LOGOUT',
            'category' => 'authentication',
        ]);
    }

    /**
     * Memastikan Web Notification API endpoints dapat mengembalikan data dan memperbarui status baca.
     */
    #[Test]
    #[TestDox('Memastikan Web Notification API endpoints dapat mengembalikan data dan memperbarui status baca')]
    public function test_web_notifications_api_endpoints(): void
    {
        $user = User::create([
            'nama'         => 'Web Notif User',
            'email'        => 'webnotifuser@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        // Buat 2 notifikasi
        $notif1 = LogStatus::create([
            'user_id' => $user->user_id,
            'tipe_log' => 'NOTIFIKASI_TEST1',
            'status' => 'BELUM_DIBACA',
            'konten_json' => ['judul' => 'Judul 1', 'pesan' => 'Pesan 1', 'link' => '/link-1']
        ]);

        $notif2 = LogStatus::create([
            'user_id' => $user->user_id,
            'tipe_log' => 'NOTIFIKASI_TEST2',
            'status' => 'BELUM_DIBACA',
            'konten_json' => ['judul' => 'Judul 2', 'pesan' => 'Pesan 2', 'link' => '/link-2']
        ]);

        // 1. GET /api/notifikasi (unauthenticated) harus dialihkan ke halaman login/utama (Status 302)
        $this->getJson('/api/notifikasi')->assertRedirect('/');

        // 2. GET /api/notifikasi (authenticated via session)
        $response = $this->withSession(['user_id' => $user->user_id, 'role' => 'admin'])
            ->getJson('/api/notifikasi');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.unread_count', 2)
            ->assertJsonCount(2, 'data.items');

        // 3. Mark satu sebagai dibaca: POST /api/notifikasi/baca/{id}
        $bacaResponse = $this->withSession(['user_id' => $user->user_id, 'role' => 'admin'])
            ->postJson("/api/notifikasi/baca/{$notif1->id}");

        $bacaResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('DIBACA', $notif1->refresh()->status);
        $this->assertEquals('BELUM_DIBACA', $notif2->refresh()->status);

        // 4. Mark semua sebagai dibaca: POST /api/notifikasi/baca-semua
        $bacaSemuaResponse = $this->withSession(['user_id' => $user->user_id, 'role' => 'admin'])
            ->postJson('/api/notifikasi/baca-semua');

        $bacaSemuaResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('DIBACA', $notif1->refresh()->status);
        $this->assertEquals('DIBACA', $notif2->refresh()->status);
    }

    /**
     * Memastikan API v1 Notification endpoints dapat mengembalikan data dan memperbarui status baca via Sanctum.
     */
    #[Test]
    #[TestDox('Memastikan API v1 Notification endpoints dapat mengembalikan data dan memperbarui status baca via Sanctum')]
    public function test_api_notifications_endpoints(): void
    {
        $user = User::create([
            'nama'         => 'API Notif User',
            'email'        => 'apinotiuser@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');
        $token = $user->createToken('test-token')->plainTextToken;

        // Buat 2 notifikasi
        $notif1 = LogStatus::create([
            'user_id' => $user->user_id,
            'tipe_log' => 'NOTIFIKASI_TEST1',
            'status' => 'BELUM_DIBACA',
            'konten_json' => ['judul' => 'Judul 1', 'pesan' => 'Pesan 1', 'link' => '/link-1']
        ]);

        $notif2 = LogStatus::create([
            'user_id' => $user->user_id,
            'tipe_log' => 'NOTIFIKASI_TEST2',
            'status' => 'BELUM_DIBACA',
            'konten_json' => ['judul' => 'Judul 2', 'pesan' => 'Pesan 2', 'link' => '/link-2']
        ]);

        // 1. GET /api/v1/notifikasi (authenticated via token)
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/v1/notifikasi');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.unread', 2);

        // 2. Mark satu sebagai dibaca: POST /api/v1/notifikasi/{id}/baca
        $bacaResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson("/api/v1/notifikasi/{$notif1->id}/baca");

        $bacaResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('DIBACA', $notif1->refresh()->status);
        $this->assertEquals('BELUM_DIBACA', $notif2->refresh()->status);

        // 3. Mark semua sebagai dibaca: POST /api/v1/notifikasi/baca-semua
        $bacaSemuaResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/v1/notifikasi/baca-semua');

        $bacaSemuaResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('DIBACA', $notif1->refresh()->status);
        $this->assertEquals('DIBACA', $notif2->refresh()->status);
    }

    /**
     * Memastikan dashboard SuperAdmin memuat log aktivitas terbaru dengan format yang benar.
     */
    #[Test]
    #[TestDox('Memastikan dashboard SuperAdmin memuat log aktivitas terbaru dengan format yang benar')]
    public function test_superadmin_dashboard_receives_formatted_recent_logs(): void
    {
        $superAdmin = User::create([
            'nama'         => 'Super Admin User',
            'email'        => 'superadmin@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $superAdmin->assignRole('SuperAdmin');

        // Buat log aktivitas buatan
        ActivityLog::create([
            'user_id' => $superAdmin->user_id,
            'action' => 'DELETE_USER',
            'category' => 'user_management',
            'description' => 'Super Admin menghapus user',
        ]);

        $response = $this->withSession([
            'user_id' => $superAdmin->user_id,
            'role' => 'superadmin'
        ])->get('/superadmin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('superadmin.dashboard');
        $response->assertViewHas('recent_logs');

        // Memastikan log yang dibuat ada di recent_logs
        $recentLogs = $response->viewData('recent_logs');
        $this->assertNotEmpty($recentLogs);
        
        $matched = false;
        foreach ($recentLogs as $log) {
            if ($log['event'] === 'Super Admin menghapus user') {
                $matched = true;
                $this->assertEquals('Super Admin User', $log['user']);
                $this->assertEquals('user_management', $log['status']);
                break;
            }
        }
        $this->assertTrue($matched, 'Log aktivitas buatan tidak ditemukan di recent_logs dashboard.');
    }
}
