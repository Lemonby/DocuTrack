<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Services\WorkflowService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $bendahara;
    private User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::create([
            'nama'         => 'Admin User',
            'email'        => 'admin@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $this->admin->assignRole('Admin');

        $this->bendahara = User::create([
            'nama'         => 'Bendahara User',
            'email'        => 'bendahara@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $this->bendahara->assignRole('Bendahara');

        $this->superadmin = User::create([
            'nama'         => 'SuperAdmin User',
            'email'        => 'superadmin@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $this->superadmin->assignRole('SuperAdmin');
    }

    /**
     * Memastikan DashboardController@index mengalihkan pengguna sesuai role dalam session.
     */
    #[Test]
    #[TestDox('Memastikan DashboardController@index mengalihkan pengguna sesuai role dalam session')]
    public function test_dashboard_redirects_correctly_based_on_role(): void
    {
        // 1. Unauthenticated redirect to /
        $this->get('/dashboard')->assertRedirect('/');

        // 2. Admin redirect
        $this->withSession(['role' => 'admin', 'user_id' => $this->admin->user_id])
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));

        // 3. Bendahara redirect
        $this->withSession(['role' => 'bendahara', 'user_id' => $this->bendahara->user_id])
            ->get('/dashboard')
            ->assertRedirect(route('bendahara.dashboard'));

        // 4. SuperAdmin redirect
        $this->withSession(['role' => 'superadmin', 'user_id' => $this->superadmin->user_id])
            ->get('/dashboard')
            ->assertRedirect(route('superadmin.dashboard'));
    }

    /**
     * Memastikan dashboard Admin dapat dimuat dan mengembalikan data list_kak dan list_lpj.
     */
    #[Test]
    #[TestDox('Memastikan dashboard Admin dapat dimuat dan mengembalikan data list_kak dan list_lpj')]
    public function test_admin_dashboard_loads_data(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Dashboard KAK Test',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Admin User',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->admin->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_DANA_DIBERIKAN,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_BENDAHARA,
        ]);

        $lpj = Lpj::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'status_id' => 1,
            'tenggat_lpj' => now()->addDays(14),
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->user_id,
            'role' => 'admin'
        ])->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('list_kak');
        $response->assertViewHas('list_lpj');
        $response->assertViewHas('stats');
    }

    /**
     * Memastikan status tenggat waktu LPJ (getDeadlineStatusAttribute) terhitung dengan benar (reminder 3 hari/status tenggat).
     */
    #[Test]
    #[TestDox('Memastikan status tenggat waktu LPJ terhitung dengan benar')]
    public function test_lpj_deadline_status_calculation(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Deadline Status KAK',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Admin User',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->admin->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_DANA_DIBERIKAN,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_BENDAHARA,
        ]);

        // 1. LPJ PENDING (tenggat_lpj di masa depan)
        $lpjPending = Lpj::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'status_id' => 1,
            'tenggat_lpj' => now()->addDays(5)->toDateString(),
        ]);
        $this->assertEquals('PENDING', $lpjPending->deadline_status);

        // 2. LPJ DUE_TODAY (tenggat_lpj hari ini)
        $lpjPending->update(['tenggat_lpj' => now()->toDateString()]);
        $this->assertEquals('DUE_TODAY', $lpjPending->deadline_status);

        // 3. LPJ OVERDUE (tenggat_lpj di masa lalu)
        $lpjPending->update(['tenggat_lpj' => now()->subDays(1)->toDateString()]);
        $this->assertEquals('OVERDUE', $lpjPending->deadline_status);

        // 4. LPJ COMPLETED (status_id !== 1, e.g. disetujui lunas = 3)
        $lpjPending->update(['status_id' => 3]);
        $this->assertEquals('COMPLETED', $lpjPending->deadline_status);
    }

    /**
     * Memastikan dashboard Admin menyaring data sesuai jurusan jika jurusan diatur dalam session.
     */
    #[Test]
    #[TestDox('Memastikan dashboard Admin menyaring data sesuai jurusan')]
    public function test_admin_dashboard_filters_by_jurusan(): void
    {
        // Kegiatan milik user lain di jurusan yang sama dengan admin
        $otherUserSameJurusan = User::create([
            'nama'         => 'Other User Same Jurusan',
            'email'        => 'other.same@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);

        $kegiatanSameJurusan = Kegiatan::create([
            'nama_kegiatan' => 'Same Jurusan KAK',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Other User Same Jurusan',
            'nim_pelaksana' => '12345678',
            'user_id' => $otherUserSameJurusan->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_VERIFIKATOR,
        ]);

        // Kegiatan di jurusan yang berbeda
        $otherUserDiffJurusan = User::create([
            'nama'         => 'Other User Diff Jurusan',
            'email'        => 'other.diff@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Elektro',
            'status'       => 'Aktif',
        ]);

        $kegiatanDiffJurusan = Kegiatan::create([
            'nama_kegiatan' => 'Different Jurusan KAK',
            'prodi_penyelenggara' => 'D4 Teknik Elektro',
            'pemilik_kegiatan' => 'Other User Diff Jurusan',
            'nim_pelaksana' => '87654321',
            'user_id' => $otherUserDiffJurusan->user_id,
            'jurusan_penyelenggara' => 'Teknik Elektro',
            'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_VERIFIKATOR,
        ]);

        // Request dashboard admin dengan session jurusan 'Teknik Informatika dan Komputer'
        $response = $this->withSession([
            'user_id' => $this->admin->user_id,
            'role' => 'admin',
            'jurusan' => 'Teknik Informatika dan Komputer'
        ])->get('/admin/dashboard');

        $response->assertStatus(200);
        $stats = $response->viewData('stats');
        $listKak = $response->viewData('list_kak');

        // Statistik total harusnya hanya menghitung kegiatan di Teknik Informatika dan Komputer
        // Yaitu $kegiatanSameJurusan (kegiatanDiffJurusan tidak masuk)
        $this->assertEquals(1, $stats['total']);

        // List KAK harus ada Same Jurusan KAK, tapi Different Jurusan KAK tidak ada
        $kakNames = collect($listKak)->pluck('nama')->toArray();
        $this->assertContains('Same Jurusan KAK', $kakNames);
        $this->assertNotContains('Different Jurusan KAK', $kakNames);
    }
}
