<?php

namespace Tests\Feature;

use App\Models\Kak;
use App\Models\KategoriRab;
use App\Models\Kegiatan;
use App\Models\Rab;
use App\Models\User;
use App\Services\WorkflowService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class PencairanTest extends TestCase
{
    use RefreshDatabase;

    private User $bendahara;

    private User $pengusul;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->bendahara = User::create([
            'nama' => 'Bendahara Test',
            'email' => 'bendahara@example.com',
            'password' => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Aktif',
        ]);
        $this->bendahara->assignRole('Bendahara');

        $this->pengusul = User::create([
            'nama' => 'Pengusul Test',
            'email' => 'pengusul@example.com',
            'password' => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Aktif',
        ]);
        $this->pengusul->assignRole('Admin');
    }

    /**
     * Memastikan Bendahara dapat mengakses halaman index pencairan dana.
     */
    #[Test]
    #[TestDox('Memastikan Bendahara dapat mengakses halaman index pencairan dana')]
    public function test_bendahara_can_access_pencairan_index(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Pencairan KAK 1',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Pengusul Test',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->pengusul->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_BENDAHARA,
        ]);

        $response = $this->withSession([
            'user_id' => $this->bendahara->user_id,
            'role' => 'bendahara',
        ])->get('/bendahara/pencairan-dana');

        $response->assertStatus(200);
        $response->assertViewIs('bendahara.pencairan-dana.index');
        $response->assertViewHas('list_kak');

        $listKak = $response->viewData('list_kak');
        $this->assertNotEmpty($listKak);
        $this->assertEquals('Pencairan KAK 1', $listKak[0]['nama']);
    }

    /**
     * Memastikan Bendahara dapat melihat detail usulan untuk pencairan dana.
     */
    #[Test]
    #[TestDox('Memastikan Bendahara dapat melihat detail usulan untuk pencairan dana')]
    public function test_bendahara_can_view_pencairan_detail(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Detail Pencairan KAK',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Pengusul Test',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->pengusul->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_BENDAHARA,
        ]);

        $kak = Kak::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
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

        $response = $this->withSession([
            'user_id' => $this->bendahara->user_id,
            'role' => 'bendahara',
        ])->get("/bendahara/pencairan-dana/show/{$kegiatan->kegiatan_id}");

        $response->assertStatus(200);
        $response->assertViewIs('bendahara.pencairan-dana.detail');
        $response->assertViewHas('kegiatan');
        $response->assertViewHas('anggaran_disetujui', 50000.0);
    }

    /**
     * Memastikan Bendahara berhasil mencairkan dana secara penuh.
     */
    #[Test]
    #[TestDox('Memastikan Bendahara berhasil mencairkan dana secara penuh')]
    public function test_bendahara_can_process_pencairan_penuh(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Pencairan Penuh KAK',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Pengusul Test',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->pengusul->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_BENDAHARA,
        ]);

        $payload = [
            'kegiatanId' => $kegiatan->kegiatan_id,
            'nominalTahapan' => ['100.000'],
            'tanggalTahapan' => [now()->toDateString()],
            'terminTahapan' => ['Pencairan Penuh'],
            'catatan' => 'Catatan pencairan penuh',
        ];

        $response = $this->withSession([
            'user_id' => $this->bendahara->user_id,
            'role' => 'bendahara',
        ])->postJson('/bendahara/pencairan-dana/proses', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Assert database updates
        $kegiatan->refresh();
        $this->assertEquals(100000.00, $kegiatan->jumlah_dicairkan);
        $this->assertEquals('penuh', $kegiatan->metode_pencairan);
        $this->assertEquals(WorkflowService::STATUS_DANA_DIBERIKAN, $kegiatan->status_utama_id);
        $this->assertEquals(WorkflowService::POSITION_ADMIN, $kegiatan->posisi_id);

        $this->assertDatabaseHas('tahapan_pencairans', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'termin' => 'Pencairan Penuh',
            'nominal' => 100000.00,
        ]);

        $this->assertDatabaseHas('lpjs', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'status_id' => 1,
        ]);
    }

    /**
     * Memastikan Bendahara berhasil mencairkan dana secara bertahap.
     */
    #[Test]
    #[TestDox('Memastikan Bendahara berhasil mencairkan dana secara bertahap')]
    public function test_bendahara_can_process_pencairan_bertahap(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Pencairan Bertahap KAK',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Pengusul Test',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->pengusul->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_BENDAHARA,
        ]);

        $payload = [
            'kegiatanId' => $kegiatan->kegiatan_id,
            'nominalTahapan' => ['40.000', '60.000'],
            'tanggalTahapan' => [now()->toDateString(), now()->addDays(7)->toDateString()],
            'terminTahapan' => ['Termin 1', 'Termin 2'],
            'catatan' => 'Catatan pencairan bertahap',
        ];

        $response = $this->withSession([
            'user_id' => $this->bendahara->user_id,
            'role' => 'bendahara',
        ])->postJson('/bendahara/pencairan-dana/proses', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Assert database updates
        $kegiatan->refresh();
        $this->assertEquals(100000.00, $kegiatan->jumlah_dicairkan);
        $this->assertEquals('bertahap', $kegiatan->metode_pencairan);
        $this->assertEquals(WorkflowService::STATUS_DANA_DIBERIKAN, $kegiatan->status_utama_id);

        $this->assertDatabaseHas('tahapan_pencairans', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'termin' => 'Termin 1',
            'nominal' => 40000.00,
        ]);

        $this->assertDatabaseHas('tahapan_pencairans', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'termin' => 'Termin 2',
            'nominal' => 60000.00,
        ]);
    }
}
