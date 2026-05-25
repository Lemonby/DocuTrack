<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
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
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('beranda');
    }

    /**
     * Test validasi input saat formulir login dikirim kosong.
     */
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
            'captcha_code'   => 'WRONG_CAPTCHA',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('login_error', 'Kode keamanan tidak valid.');
        $this->assertFalse(Session::has('user_id'));
    }

    /**
     * Test login gagal jika email atau password salah.
     */
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
    public function test_login_succeeds_and_redirects_correctly(): void
    {
        // 1. Buat user dengan role Admin
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
    public function test_logout_clears_session_and_redirects(): void
    {
        // 1. Buat user dan simpan data ke session seolah-olah sudah login
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

        // 2. Kirim request logout
        $response = $this->post('/logout');

        // Cek redirect ke halaman utama
        $response->assertRedirect('/');

        // Cek session telah dibersihkan
        $this->assertFalse(Session::has('user_id'));
    }
}
