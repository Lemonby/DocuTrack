<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kegiatan;
use App\Models\Kak;
use App\Models\Rab;
use App\Models\KategoriRab;
use App\Services\WorkflowService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class CetakTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kegiatan $kegiatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::create([
            'nama'         => 'Admin Cetak',
            'email'        => 'admincetak@example.com',
            'password'     => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status'       => 'Aktif',
        ]);
        $this->admin->assignRole('Admin');

        $this->kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Cetak Test',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Admin Cetak',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->admin->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_DISETUJUI,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_ADMIN,
        ]);

        $kak = Kak::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'gambaran_umum' => 'Deskripsi KAK',
            'penerima_manfaat' => 'Mahasiswa',
            'metode_pelaksanaan' => 'Offline',
        ]);

        $kategori = KategoriRab::firstOrCreate(['nama_kategori' => 'Belanja Barang']);
        Rab::create([
            'kak_id' => $kak->kak_id,
            'kategori_id' => $kategori->kategori_rab_id,
            'uraian' => 'Kertas A4',
            'rincian' => '1 Rim',
            'vol1' => 1,
            'vol2' => 1,
            'harga' => 50000,
        ]);
    }

    /**
     * Memastikan Admin dapat mengekspor KAK ke dokumen PDF (Cetak KAK).
     */
    #[Test]
    #[TestDox('Memastikan Admin dapat mengekspor KAK ke dokumen PDF')]
    public function test_admin_can_export_kak_to_pdf(): void
    {
        $response = $this->withSession([
            'user_id' => $this->admin->user_id,
            'role' => 'admin'
        ])->get("/cetak-kak/{$this->kegiatan->kegiatan_id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertSee('%PDF', false); // Dokumen PDF dimulai dengan signature %PDF
    }
}
