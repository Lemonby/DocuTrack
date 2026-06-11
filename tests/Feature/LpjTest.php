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

class LpjTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Kegiatan $kegiatan;

    private Kak $kak;

    private Rab $rabItem;

    private KategoriRab $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::create([
            'nama' => 'Admin Pengusul',
            'email' => 'adminpengusul@example.com',
            'password' => Hash::make('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Aktif',
        ]);
        $this->admin->assignRole('Admin');

        $this->kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan LPJ Test',
            'prodi_penyelenggara' => 'D4 Teknik Informatika',
            'pemilik_kegiatan' => 'Admin Pengusul',
            'nim_pelaksana' => '12345678',
            'user_id' => $this->admin->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'status_utama_id' => WorkflowService::STATUS_DANA_DIBERIKAN,
            'wadir_tujuan' => 1,
            'posisi_id' => WorkflowService::POSITION_BENDAHARA,
        ]);

        $this->kak = Kak::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'gambaran_umum' => 'Deskripsi KAK',
            'penerima_manfaat' => 'Mahasiswa',
            'metode_pelaksanaan' => 'Offline',
        ]);

        $this->kategori = KategoriRab::firstOrCreate(['nama_kategori' => 'Belanja Barang']);
        $this->rabItem = Rab::create([
            'kak_id' => $this->kak->kak_id,
            'kategori_id' => $this->kategori->kategori_rab_id,
            'uraian' => 'Kertas A4',
            'rincian' => '1 Rim',
            'vol1' => 1,
            'vol2' => 1,
            'harga' => 50000,
        ]);
    }

    /**
     * Memastikan Admin dapat mengakses halaman index LPJ.
     */
    #[Test]
    #[TestDox('Memastikan Admin dapat mengakses halaman index LPJ')]
    public function test_admin_can_access_lpj_index(): void
    {
        Lpj::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'status_id' => 1,
            'tenggat_lpj' => now()->addDays(14),
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->user_id,
            'role' => 'admin',
        ])->get('/admin/pengajuan-lpj');

        $response->assertStatus(200);
        $response->assertViewIs('admin.lpj.index');
        $response->assertViewHas('list_lpj');
    }

    /**
     * Memastikan Admin dapat melihat detail LPJ dan RAB item-nya.
     */
    #[Test]
    #[TestDox('Memastikan Admin dapat melihat detail LPJ dan RAB item-nya')]
    public function test_admin_can_view_lpj_detail(): void
    {
        Lpj::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'status_id' => 1,
            'tenggat_lpj' => now()->addDays(14),
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->user_id,
            'role' => 'admin',
        ])->get("/admin/pengajuan-lpj/show/{$this->kegiatan->kegiatan_id}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.lpj.detail');
        $response->assertViewHas('rab_items');
    }

    /**
     * Memastikan Admin berhasil menyimpan dan mengajukan LPJ melalui form web.
     */
    #[Test]
    #[TestDox('Memastikan Admin berhasil menyimpan dan mengajukan LPJ melalui form web')]
    public function test_admin_can_submit_lpj_via_web(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('receipt.pdf', 100);

        $lpj = Lpj::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'status_id' => 1,
            'tenggat_lpj' => now()->addDays(14),
        ]);

        $payload = [
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'realisasi_tanggal_mulai' => now()->toDateString(),
            'realisasi_tanggal_selesai' => now()->addDays(2)->toDateString(),
            'realisasi' => [
                'Belanja Barang' => [
                    0 => 45000,
                ],
            ],
            'bukti' => [
                'Belanja Barang' => [
                    0 => $file,
                ],
            ],
        ];

        $response = $this->withSession([
            'user_id' => $this->admin->user_id,
            'role' => 'admin',
        ])->post('/admin/pengajuan-lpj/store', $payload);

        $response->assertRedirect(route('admin.lpj.index'));
        $response->assertSessionHas('success', 'LPJ berhasil diajukan ke Bendahara.');

        $lpj->refresh();
        $this->assertEquals(45000.00, $lpj->grand_total_realisasi);
        $this->assertEquals(1, $lpj->status_id); // Menunggu Verifikasi

        $lpjItem = LpjItem::where('lpj_id', $lpj->lpj_id)->first();
        $this->assertNotNull($lpjItem);
        $this->assertEquals(45000.00, $lpjItem->realisasi);
        $this->assertNotNull($lpjItem->file_bukti);
        Storage::disk('public')->assertExists($lpjItem->file_bukti);
    }

    /**
     * Memastikan API v1 LPJ dapat melisting, mengupload bukti, dan mensubmit LPJ.
     */
    #[Test]
    #[TestDox('Memastikan API v1 LPJ dapat melisting, mengupload bukti, dan mensubmit LPJ')]
    public function test_api_endpoints_lpj(): void
    {
        Storage::fake('public');
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $lpj = Lpj::create([
            'kegiatan_id' => $this->kegiatan->kegiatan_id,
            'status_id' => 1,
            'tenggat_lpj' => now()->addDays(14),
        ]);

        $lpjItem = LpjItem::create([
            'lpj_id' => $lpj->lpj_id,
            'kategori_id' => $this->kategori->kategori_rab_id,
            'uraian' => $this->rabItem->uraian,
            'rincian' => $this->rabItem->rincian,
            'total_harga' => $this->rabItem->total_harga ?? 50000,
            'sat1' => $this->rabItem->sat1,
            'vol1' => $this->rabItem->vol1,
            'harga' => $this->rabItem->harga,
        ]);

        // 1. GET index API
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->getJson('/api/v1/admin/lpj');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 2. GET show API
        $showResponse = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->getJson("/api/v1/admin/lpj/{$lpj->lpj_id}");

        $showResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // 3. POST upload bukti API
        $file = UploadedFile::fake()->create('receipt.png', 100);
        $uploadResponse = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->postJson('/api/v1/admin/lpj/upload-bukti', [
                'lpj_id' => $lpj->lpj_id,
                'rab_item_id' => $this->rabItem->rab_item_id,
                'file' => $file,
            ]);

        $uploadResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $lpjItem->refresh();
        $this->assertNotNull($lpjItem->file_bukti);
        Storage::disk('public')->assertExists($lpjItem->file_bukti);

        // 4. POST submit LPJ API
        $submitResponse = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->postJson('/api/v1/admin/lpj/submit', [
                'kegiatan_id' => $this->kegiatan->kegiatan_id,
                'items' => [
                    [
                        'id' => $lpjItem->lpj_item_id,
                        'realisasi' => 50000,
                    ],
                ],
            ]);

        $submitResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $lpj->refresh();
        $this->assertEquals(50000.00, $lpj->grand_total_realisasi);
        $this->assertEquals(1, $lpj->status_id); // Menunggu verifikasi Bendahara
    }
}
