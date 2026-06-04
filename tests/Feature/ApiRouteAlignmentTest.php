<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kegiatan;
use App\Models\Kak;
use App\Services\WorkflowService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class ApiRouteAlignmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $ppk;
    private User $wadir;
    private User $direktur;
    private Kegiatan $kegiatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        // Admin User
        $this->admin = User::create([
            'nama'         => 'Admin API',
            'email'        => 'adminapi@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $this->admin->assignRole('Admin');

        // PPK User
        $this->ppk = User::create([
            'nama'         => 'PPK API',
            'email'        => 'ppkapi@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $this->ppk->assignRole('PPK');

        // Wadir User
        $this->wadir = User::create([
            'nama'         => 'Wadir API',
            'email'        => 'wadirapi@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $this->wadir->assignRole('Wadir');

        // Direktur User
        $this->direktur = User::create([
            'nama'         => 'Direktur API',
            'email'        => 'direkturapi@example.com',
            'password'     => Hash::make('password123'),
            'status'       => 'Aktif',
        ]);
        $this->direktur->assignRole('Direktur');

        // Kegiatan
        $this->kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan API Test',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Admin API',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->admin->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_DISETUJUI,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_ADMIN,
        ]);

        Kak::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'gambaran_umum' => 'Deskripsi KAK API',
            'penerima_manfaat' => 'Mahasiswa',
            'metode_pelaksanaan' => 'Offline',
        ]);
    }

    /**
     * Memastikan Admin dapat memperbarui usulan melalui API.
     */
    #[Test]
    #[TestDox('Memastikan Admin dapat memperbarui usulan melalui API')]
    public function test_admin_can_update_usulan_via_api(): void
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $payload = [
            'nama_kegiatan' => 'Nama Kegiatan API Baru',
            'gambaran_umum' => 'Gambaran umum yang diperbarui',
            'penerima_manfaat' => 'Mahasiswa Baru',
            'metode_pelaksanaan' => 'Online',
            'wadir_tujuan' => 1,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson("/api/v1/admin/usulan/{$this->kegiatan->kegiatan_id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('kegiatans', [
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'nama_kegiatan' => 'Nama Kegiatan API Baru'
        ]);
    }

    /**
     * Memastikan Admin dapat menyelesaikan kegiatan (status 8) melalui API.
     */
    #[Test]
    #[TestDox('Memastikan Admin dapat menyelesaikan kegiatan (status 8) melalui API')]
    public function test_admin_can_complete_kegiatan_via_api(): void
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson("/api/v1/admin/usulan/{$this->kegiatan->kegiatan_id}/selesai");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('kegiatans', [
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'status_utama_id' => 8
        ]);
    }

    /**
     * Memastikan PPK dapat melihat riwayat telaah melalui API.
     */
    #[Test]
    #[TestDox('Memastikan PPK dapat melihat riwayat telaah melalui API')]
    public function test_ppk_can_view_riwayat_via_api(): void
    {
        $token = $this->ppk->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson("/api/v1/ppk/riwayat");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    /**
     * Memastikan Wadir dapat melihat riwayat telaah melalui API.
     */
    #[Test]
    #[TestDox('Memastikan Wadir dapat melihat riwayat telaah melalui API')]
    public function test_wadir_can_view_riwayat_via_api(): void
    {
        $token = $this->wadir->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson("/api/v1/wadir/riwayat");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    /**
     * Memastikan Direktur dapat melihat integritas jurusan melalui API.
     */
    #[Test]
    #[TestDox('Memastikan Direktur dapat melihat integritas jurusan melalui API')]
    public function test_direktur_can_view_integritas_via_api(): void
    {
        $token = $this->direktur->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson("/api/v1/direktur/integritas");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
