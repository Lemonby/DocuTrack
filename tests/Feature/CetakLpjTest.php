<?php

namespace Tests\Feature;

use App\Models\Kak;
use App\Models\KategoriRab;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Models\LpjItem;
use App\Models\Rab;
use App\Models\User;
use App\Services\WorkflowService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class CetakLpjTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Kegiatan $kegiatan;

    private KategoriRab $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::create([
            'nama' => 'Admin Cetak LPJ',
            'email' => 'admincetaklpj@example.com',
            'password' => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Aktif',
        ]);
        $this->admin->assignRole('Admin');

        $this->kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Cetak LPJ Test',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Admin Cetak LPJ',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->admin->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_DISETUJUI,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_ADMIN,
        ]);

        $kak = Kak::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'gambaran_umum' => 'Deskripsi KAK untuk LPJ',
            'penerima_manfaat' => 'Mahasiswa',
            'metode_pelaksanaan' => 'Offline',
        ]);

        $this->kategori = KategoriRab::firstOrCreate(['nama_kategori' => 'Belanja Barang']);

        Rab::create([
            'kak_id' => $kak->kak_id,
            'kategori_id' => $this->kategori->kategori_rab_id,
            'uraian' => 'Kertas A4 LPJ',
            'rincian' => '1 Rim',
            'vol1' => 1,
            'vol2' => 1,
            'harga' => 50000,
        ]);
    }

    /**
     * Memastikan route Cetak LPJ memerlukan autentikasi.
     */
    #[Test]
    #[TestDox('Memastikan route Cetak LPJ memerlukan autentikasi')]
    public function test_cetak_lpj_requires_authentication(): void
    {
        $response = $this->get("/cetak-lpj/{$this->kegiatan->kegiatan_id}");

        // redirect to login or forbidden depends on CheckRole middleware
        $response->assertRedirect('/');
    }

    /**
     * Memastikan route Cetak LPJ mengembalikan 404 jika LPJ belum ada.
     */
    #[Test]
    #[TestDox('Memastikan route Cetak LPJ mengembalikan 404 jika LPJ belum ada')]
    public function test_cetak_lpj_returns_404_if_no_lpj(): void
    {
        $response = $this->withSession([
            'user_id' => $this->admin->user_id,
            'role' => 'admin',
        ])->get("/cetak-lpj/{$this->kegiatan->kegiatan_id}");

        $response->assertStatus(404);
    }

    /**
     * Memastikan Admin dapat mengunduh LPJ ke dokumen PDF.
     */
    #[Test]
    #[TestDox('Memastikan Admin dapat mengunduh LPJ ke dokumen PDF')]
    public function test_admin_can_export_lpj_to_pdf(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('kwitansi.png', 100);
        $filePath = $file->store('bukti_lpj', 'public');

        $lpj = Lpj::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'status_id' => 3, // disetujui / lunas
            'tenggat_lpj' => now()->addDays(14),
            'grand_total_realisasi' => 45000,
            'realisasi_tanggal_mulai' => now()->toDateString(),
            'realisasi_tanggal_selesai' => now()->addDays(2)->toDateString(),
        ]);

        LpjItem::create([
            'lpj_id' => $lpj->lpj_id,
            'kategori_id' => $this->kategori->kategori_rab_id,
            'jenis_belanja' => 'Belanja Barang',
            'uraian' => 'Kertas A4 LPJ',
            'rincian' => '1 Rim',
            'vol1' => 1,
            'vol2' => 1,
            'harga' => 50000,
            'realisasi' => 45000,
            'file_bukti' => $filePath,
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->user_id,
            'role' => 'admin',
        ])->get("/cetak-lpj/{$this->kegiatan->kegiatan_id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertSee('%PDF', false);
    }
}
