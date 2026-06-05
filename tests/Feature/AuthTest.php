<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed master data dan roles/permissions agar relasi database terpenuhi
        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Test halaman utama (beranda) dapat diakses dengan sukses.
     */
    #[Test]
    #[TestDox('Memastikan halaman utama (beranda) dapat diakses dengan sukses (Status 200)')]
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('beranda');
    }

    /**
     * Test CAPTCHA SVG generated successfully and sets session code.
     */
    #[Test]
    #[TestDox('Memastikan CAPTCHA SVG digenerate dengan sukses dan kode disimpan di session')]
    public function test_captcha_generation_works(): void
    {
        $response = $this->get('/captcha');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $response->assertSee('<svg', false);
        $response->assertSee('</svg>', false);
        
        $this->assertTrue(session()->has('captcha_code'));
        $this->assertEquals(5, strlen(session('captcha_code')));
    }

    /**
     * Test validasi input saat formulir login dikirim kosong.
     */
    #[Test]
    #[TestDox('Memastikan muncul error validasi saat form login dikirim dengan kolom kosong')]
    public function test_login_validation_errors(): void
    {
        $response = $this->post('/login', [
            'login_email'    => '',
            'login_password' => '',
            'captcha_code'   => '',
        ]);

        $response->assertSessionHasErrors(['login_email', 'login_password', 'captcha_code']);
    }

    /**
     * Test login gagal jika kode CAPTCHA tidak valid.
     */
    #[Test]
    #[TestDox('Memastikan login gagal jika kode CAPTCHA yang dimasukkan tidak valid')]
    public function test_login_fails_with_invalid_captcha(): void
    {
        // Buat user simulasi
        $user = User::create([
            'nama'         => 'Test User',
            'email'        => 'test@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        // Mengirimkan request login dengan captcha yang salah (bukan master code '123456' dan bukan dari session)
        $response = $this->post('/login', [
            'login_email'    => 'test@example.com',
            'login_password' => 'password123',
            'captcha_code'   => 'WRONG_CAPTncfsdfcsdj',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('login_error', 'Kode keamanan tidak valid.');
        $this->assertFalse(Session::has('user_id'));
    }

    /**
     * Test login gagal jika email atau password salah.
     */
    #[Test]
    #[TestDox('Memastikan login gagal jika email atau password salah')]
    public function test_login_fails_with_wrong_password(): void
    {
        // Buat user simulasi
        $user = User::create([
            'nama'         => 'Test User',
            'email'        => 'test@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        // Mengirimkan request dengan captcha master '123456' tapi password salah
        $response = $this->post('/login', [
            'login_email'    => 'test@example.com',
            'login_password' => 'wrong_password',
            'captcha_code'   => '123456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('login_error', 'Email atau password salah.');
        $this->assertFalse(Session::has('user_id'));
    }

    /**
     * Test login berhasil dengan kredensial benar & master captcha, lalu redirect sesuai role.
     */
    #[Test]
    #[TestDox('Memastikan login sukses dengan kredensial benar & master captcha, lalu redirect sesuai role')]
    public function test_login_succeeds_and_redirects_correctly(): void
    {
        // Buat user dengan role Admin
        $admin = User::create([
            'nama'         => 'Admin TI',
            'email'        => 'admin@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $admin->assignRole('Admin');

        // Kirim request login
        $response = $this->post('/login', [
            'login_email'    => 'admin@example.com',
            'login_password' => 'password123',
            'captcha_code'   => '123456', // Menggunakan master captcha
        ]);

        // Cek redirect ke admin dashboard
        $response->assertRedirect(route('admin.dashboard'));

        // Cek data disimpan di session
        $response->assertSessionHas('user_id', $admin->user_id);
        $response->assertSessionHas('user_name', 'Admin TI');
        $response->assertSessionHas('role', 'admin');
        $response->assertSessionHas('spatie_role', 'Admin');
        $response->assertSessionHas('jurusan', 'Teknik Informatika dan Komputer');
    }

    /**
     * Test logout berhasil membersihkan session dan redirect ke halaman utama.
     */
    #[Test]
    #[TestDox('Memastikan logout berhasil membersihkan seluruh session dan redirect ke beranda')]
    public function test_logout_clears_session_and_redirects(): void
    {
        // Buat user dan simpan data ke session seolah-olah sudah login
        $user = User::create([
            'nama'         => 'Test User',
            'email'        => 'test@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        // Login duluan agar session terisi
        $this->post('/login', [
            'login_email'    => 'test@example.com',
            'login_password' => 'password123',
            'captcha_code'   => '123456',
        ]);

        $this->assertTrue(Session::has('user_id'));

        // Kirim request logout
        $response = $this->post('/logout');

        // Cek redirect ke halaman utama
        $response->assertRedirect('/');

        // Cek session telah dibersihkan
        $this->assertFalse(Session::has('user_id'));
    }

    /**
     * Test middleware CheckRole memblokir user jika belum login.
     */
    #[Test]
    #[TestDox('Memastikan middleware CheckRole memblokir akses jika pengguna belum login')]
    public function test_middleware_blocks_unauthenticated_user(): void
    {
        $request = Request::create('/dashboard', 'GET');
        
        $middleware = new \App\Http\Middleware\CheckRole();
        
        $response = $middleware->handle($request, function () {
            $this->fail('Middleware should not pass the request.');
        });
        
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(url('/'), $response->headers->get('Location'));
        $this->assertEquals('Silakan login terlebih dahulu.', session('login_error'));
    }

    /**
     * Test middleware CheckRole memblokir jika role tidak sesuai dengan parameter middleware.
     */
    #[Test]
    #[TestDox('Memastikan middleware CheckRole memblokir akses jika role pengguna tidak memiliki izin (Unauthorized)')]
    public function test_middleware_blocks_unauthorized_role(): void
    {
        // Set session role = bendahara
        Session::put('role', 'bendahara');

        $request = Request::create('/admin/dashboard', 'GET');
        
        $middleware = new \App\Http\Middleware\CheckRole();
        
        // Meminta agar hanya role 'admin' yang bisa lewat
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Forbidden / Unauthorized Access');
        
        $middleware->handle($request, function () {
            $this->fail('Middleware should not pass the request.');
        }, 'admin');
    }

    /**
     * Test middleware CheckRole memperbolehkan jika role sesuai dengan parameter middleware.
     */
    #[Test]
    #[TestDox('Memastikan middleware CheckRole mengizinkan akses jika role pengguna sesuai (Authorized)')]
    public function test_middleware_allows_authorized_role(): void
    {
        // Set session role = admin
        Session::put('role', 'admin');

        $request = Request::create('/admin/dashboard', 'GET');
        
        $middleware = new \App\Http\Middleware\CheckRole();
        
        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;
            return new \Symfony\Component\HttpFoundation\Response('Passed');
        }, 'admin');
        
        $this->assertTrue($called);
        $this->assertEquals('Passed', $response->getContent());
    }

    /**
     * Test login berhasil mencatatkan entri log ke log_statuses.
     */
    #[Test]
    #[TestDox('Memastikan login sukses mencatatkan entri LOGIN ke tabel log_statuses')]
    public function test_login_creates_log_status(): void
    {
        // Buat user
        $user = User::create([
            'nama'         => 'Log User',
            'email'        => 'loguser@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        // Kirim request login
        $response = $this->post('/login', [
            'login_email'    => 'loguser@example.com',
            'login_password' => 'password123',
            'captcha_code'   => '123456',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        
        // Memastikan tabel log_statuses terisi
        $this->assertDatabaseHas('log_statuses', [
            'user_id' => $user->user_id,
            'tipe_log' => 'LOGIN',
            'status' => 'DIBACA',
        ]);
    }

    /**
     * Test Web Notifications API returns correct JSON.
     */
    #[Test]
    #[TestDox('Memastikan Web Notifications API mengembalikan daftar notifikasi dalam format JSON yang valid')]
    public function test_web_notifications_api(): void
    {
        // Buat user
        $user = User::create([
            'nama'         => 'Notif User',
            'email'        => 'notifuser@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        // Simpan notifikasi buatan ke database
        \App\Models\LogStatus::create([
            'user_id' => $user->user_id,
            'tipe_log' => 'NOTIFIKASI_APPROVAL',
            'status' => 'BELUM_DIBACA',
            'konten_json' => [
                'judul' => 'Usulan Disetujui',
                'pesan' => 'Usulan Anda disetujui.',
                'link' => '#'
            ]
        ]);

        // Simulasikan session login
        $response = $this->withSession([
            'user_id' => $user->user_id,
            'role' => 'admin'
        ])->get('/api/notifikasi');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'items',
                'unread_count'
            ]
        ]);
        
        $response->assertJsonPath('data.unread_count', 1);
    }

    /**
     * Test admin dashboard renders successfully with custom session user.
     */
    #[Test]
    #[TestDox('Memastikan halaman dashboard admin berhasil dimuat menggunakan session user kustom')]
    public function test_admin_dashboard_renders_with_session_user(): void
    {
        $user = User::create([
            'nama'         => 'Dashboard Admin',
            'email'        => 'dashadmin@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        $response = $this->withSession([
            'user_id' => $user->user_id,
            'role' => 'admin'
        ])->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /**
     * Test admin usulan list page renders successfully with custom session user.
     */
    #[Test]
    #[TestDox('Memastikan halaman daftar usulan admin berhasil dimuat menggunakan session user kustom')]
    public function test_admin_usulan_list_renders_with_session_user(): void
    {
        $user = User::create([
            'nama'         => 'Usulan Admin',
            'email'        => 'usuladmin@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        $response = $this->withSession([
            'user_id' => $user->user_id,
            'role' => 'admin'
        ])->get('/admin/pengajuan-usulan');

        $response->assertStatus(200);
        $response->assertViewIs('admin.usulan.index');
    }

    /**
     * Test admin kegiatan list page renders successfully with custom session user.
     */
    #[Test]
    #[TestDox('Memastikan halaman daftar kegiatan admin berhasil dimuat menggunakan session user kustom')]
    public function test_admin_kegiatan_list_renders_with_session_user(): void
    {
        $user = User::create([
            'nama'         => 'Kegiatan Admin',
            'email'        => 'kegadmin@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        $response = $this->withSession([
            'user_id' => $user->user_id,
            'role' => 'admin'
        ])->get('/admin/pengajuan-kegiatan');

        $response->assertStatus(200);
        $response->assertViewIs('admin.kegiatan.index');
    }

    /**
     * Test admin LPJ list page renders successfully with custom session user.
     */
    #[Test]
    #[TestDox('Memastikan halaman daftar LPJ admin berhasil dimuat menggunakan session user kustom')]
    public function test_admin_lpj_list_renders_with_session_user(): void
    {
        $user = User::create([
            'nama'         => 'LPJ Admin',
            'email'        => 'lpjadmin@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $user->assignRole('Admin');

        $response = $this->withSession([
            'user_id' => $user->user_id,
            'role' => 'admin'
        ])->get('/admin/pengajuan-lpj');

        $response->assertStatus(200);
        $response->assertViewIs('admin.lpj.index');
    }

    /**
     * Test admin cannot access kegiatan detail of another department.
     */
    #[Test]
    #[TestDox('Memastikan admin tidak bisa mengakses detail kegiatan milik jurusan lain (Cross-Department Blocked)')]
    public function test_admin_cannot_access_other_department_kegiatan(): void
    {
        // 0. Create a TIK admin user
        $adminTIK = User::create([
            'nama'         => 'Admin TIK',
            'email'        => 'admintik_test@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $adminTIK->assignRole('Admin');

        // 1. Create a Kegiatan belonging to TIK
        $kegiatanTIK = \App\Models\Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan TIK Test',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Admin TI',
            'nim_pelaksana' => '12345678',
            'user_id' => $adminTIK->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => 1,
            'wadir_tujuan' => 1,
            'posisi_id' => 1,
        ]);

        // 2. Create an admin user for Teknik Elektro
        $adminElektro = User::create([
            'nama'         => 'Admin Elektro',
            'email'        => 'adminelektro_test@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Elektro',
            'status'       => 'Aktif',
        ]);
        $adminElektro->assignRole('Admin');

        // 3. Request details of the TIK kegiatan using the Teknik Elektro admin session
        $response = $this->withSession([
            'user_id' => $adminElektro->user_id,
            'role' => 'admin',
            'jurusan' => 'Teknik Elektro'
        ])->get("/admin/pengajuan-kegiatan/show/{$kegiatanTIK->kegiatan_id}");

        // 4. Assert 403 Forbidden access
        $response->assertStatus(403);
    }
}

