<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kegiatan;
use App\Models\Kak;
use App\Models\Iku;
use App\Models\Lpj;
use App\Services\SpkMautService;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

/**
 * Class IntegritasTest
 * 
 * Pengujian fitur pemeringkatan Integritas Jurusan menggunakan Metode SPK MAUT.
 * Menguji akurasi matematika kriteria C1, C2, C3, C4, pengurutan ranking, dan otorisasi halaman Direktur.
 */
class IntegritasTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var SpkMautService Instance dari layanan SPK MAUT
     */
    protected SpkMautService $spkService;

    /**
     * Set up pengujian dengan melakukan seeding master data dasar.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Jalankan seeder agar role, permission, dan status dasar terbuat di database pengujian
        $this->seed(MasterDataSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\KriteriaSeeder::class);

        $this->spkService = new SpkMautService();
    }

    /**
     * Menguji akurasi perhitungan matematika kriteria C1, C2, C3, C4, dan hasil MAUT akhir.
     */
    #[Test]
    #[TestDox('Memastikan perhitungan detail kriteria C1-C4 dan skor akhir MAUT berjalan 100% akurat')]
    public function test_maut_score_calculations_are_accurate(): void
    {
        // 1. Buat User Pemilik Kegiatan
        $user = User::create([
            'nama' => 'Dosen TI',
            'email' => 'dosenti@example.com',
            'password' => bcrypt('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Aktif',
        ]);

        // 2. Buat Kegiatan dengan Pagu Rencana (Target 4 hari: 1 Juni - 5 Juni)
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Peningkatan Kompetensi Web',
            'prodi_penyelenggara' => 'Teknik Informatika dan Komputer',
            'pemilik_kegiatan' => 'Dosen TI',
            'user_id' => $user->user_id,
            'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-06-05',
            'jumlah_dicairkan' => 10000000.00, // Pencairan 10 juta
            'status_utama_id' => 3, // Disetujui
            'posisi_id' => 5, // Bendahara
            'wadir_tujuan' => 1,
        ]);

        // 3. Buat KAK dan hubungkan ke 2 IKU (Kriteria C3: 2 IKU -> Skor 60)
        $kak = Kak::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'penerima_manfaat' => 'Mahasiswa',
            'gambaran_umum' => 'Deskripsi KAK',
            'metode_pelaksanaan' => 'Metode',
            'tgl_pembuatan' => '2026-05-20',
        ]);

        $ikus = Iku::take(2)->get(); // Ambil 2 IKU default dari master seeder
        $kak->ikus()->sync($ikus->pluck('id')->toArray());

        // 4. Buat LPJ dengan data realisasi:
        // - Realisasi 4 hari: 1 Juni - 5 Juni (Selisih 0 hari dengan target -> Kriteria C1: 100)
        // - Realisasi dana 9.8 juta dari 10 juta (Penyerapan 98% -> Kriteria C2: 100)
        // - Submit 12 Juni dengan Tenggat 15 Juni (Tepat waktu -> Kriteria C4: 100)
        $lpj = Lpj::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'grand_total_realisasi' => 9800000.00,
            'submitted_at' => '2026-06-12',
            'tenggat_lpj' => '2026-06-15',
            'realisasi_tanggal_mulai' => '2026-06-01',
            'realisasi_tanggal_selesai' => '2026-06-05',
            'status_id' => 1,
        ]);

        // 5. Lakukan perhitungan skor menggunakan Service
        $kegiatan->refresh();
        $kegiatan->load(['kak.ikus', 'lpj']);
        $scores = $this->spkService->calculateScores($kegiatan);

        // 6. Validasi nilai matematika masing-masing kriteria (skala desimal 0.0 - 1.0)
        // C1 (Durasi): Selisih 0 hari -> Skor 1.0
        $this->assertEquals(1.0, $scores['c1']);
        // C2 (Anggaran): Penyerapan 98% -> Skor 1.0
        $this->assertEquals(1.0, $scores['c2']);
        // C3 (IKU): Mendukung 2 IKU -> Skor 1.0
        $this->assertEquals(1.0, $scores['c3']);
        // C4 (Ketepatan LPJ): Diajukan sebelum tenggat -> Skor 1.0
        $this->assertEquals(1.0, $scores['c4']);

        // Skor MAUT Akhir = (0.25 * 1.0) + (0.25 * 1.0) + (0.25 * 1.0) + (0.25 * 1.0) = 1.0
        // Karena hanya ada 1 kegiatan di jurusan ini, min/max akan sama sehingga normalisasinya menghasilkan utilitas 1.0 untuk semua kriteria
        $this->assertEquals(1.0, $scores['final_score']);
    }

    /**
     * Menguji pengurutan ranking per jurusan secara descending (nilai tertinggi di posisi pertama).
     */
    #[Test]
    #[TestDox('Memastikan pemeringkatan jurusan terurut secara Descending (skor rata-rata tertinggi di paling atas)')]
    public function test_jurusan_rankings_are_sorted_descending(): void
    {
        // 1. Buat User Dosen TI & Jurusan Elektro
        $userTi = User::create([
            'nama' => 'Dosen TI',
            'email' => 'ti@example.com',
            'password' => bcrypt('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Aktif',
        ]);
        $userElektro = User::create([
            'nama' => 'Dosen Elektro',
            'email' => 'el@example.com',
            'password' => bcrypt('password123'),
            'nama_jurusan' => 'Teknik Elektro',
            'status' => 'Aktif',
        ]);

        // 2. Buat Kegiatan & LPJ Sempurna untuk Jurusan TI (2 kegiatan identik dan sempurna agar rata-rata = 1.0)
        $kegiatanTi1 = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan TI 1', 'prodi_penyelenggara' => 'TI', 'pemilik_kegiatan' => 'Dosen TI',
            'user_id' => $userTi->user_id, 'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'tanggal_mulai' => '2026-06-01', 'tanggal_selesai' => '2026-06-02', 'jumlah_dicairkan' => 5000000.00,
            'wadir_tujuan' => 1,
        ]);
        Lpj::create([
            'kegiatan_id' => $kegiatanTi1->kegiatan_id, 'grand_total_realisasi' => 5000000.00,
            'submitted_at' => '2026-06-10', 'tenggat_lpj' => '2026-06-15',
            'realisasi_tanggal_mulai' => '2026-06-01', 'realisasi_tanggal_selesai' => '2026-06-02',
        ]);

        $kegiatanTi2 = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan TI 2', 'prodi_penyelenggara' => 'TI', 'pemilik_kegiatan' => 'Dosen TI',
            'user_id' => $userTi->user_id, 'jurusan_penyelenggara' => 'Teknik Informatika dan Komputer',
            'tanggal_mulai' => '2026-06-01', 'tanggal_selesai' => '2026-06-02', 'jumlah_dicairkan' => 5000000.00,
            'wadir_tujuan' => 1,
        ]);
        Lpj::create([
            'kegiatan_id' => $kegiatanTi2->kegiatan_id, 'grand_total_realisasi' => 5000000.00,
            'submitted_at' => '2026-06-10', 'tenggat_lpj' => '2026-06-15',
            'realisasi_tanggal_mulai' => '2026-06-01', 'realisasi_tanggal_selesai' => '2026-06-02',
        ]);

        // 3. Buat Kegiatan & LPJ untuk Jurusan Elektro (1 kegiatan sempurna, 1 kegiatan kurang baik agar rata-rata = 0.5)
        $kegiatanEl1 = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan El 1', 'prodi_penyelenggara' => 'Elektro', 'pemilik_kegiatan' => 'Dosen El',
            'user_id' => $userElektro->user_id, 'jurusan_penyelenggara' => 'Teknik Elektro',
            'tanggal_mulai' => '2026-06-01', 'tanggal_selesai' => '2026-06-02', 'jumlah_dicairkan' => 5000000.00,
            'wadir_tujuan' => 1,
        ]);
        Lpj::create([
            'kegiatan_id' => $kegiatanEl1->kegiatan_id, 'grand_total_realisasi' => 5000000.00,
            'submitted_at' => '2026-06-10', 'tenggat_lpj' => '2026-06-15',
            'realisasi_tanggal_mulai' => '2026-06-01', 'realisasi_tanggal_selesai' => '2026-06-02',
        ]);

        $kegiatanEl2 = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan El 2', 'prodi_penyelenggara' => 'Elektro', 'pemilik_kegiatan' => 'Dosen El',
            'user_id' => $userElektro->user_id, 'jurusan_penyelenggara' => 'Teknik Elektro',
            'tanggal_mulai' => '2026-06-01', 'tanggal_selesai' => '2026-06-02', 'jumlah_dicairkan' => 5000000.00,
            'wadir_tujuan' => 1,
        ]);
        Lpj::create([
            'kegiatan_id' => $kegiatanEl2->kegiatan_id, 'grand_total_realisasi' => 1000000.00, // serapan 20%
            'submitted_at' => '2026-06-25', 'tenggat_lpj' => '2026-06-15', // terlambat
            'realisasi_tanggal_mulai' => '2026-06-01', 'realisasi_tanggal_selesai' => '2026-06-10', // terlambat pelaksanaan
        ]);

        // 4. Ambil Peringkat dari Service
        $rankings = $this->spkService->getJurusanRankings();

        // 5. Validasi:
        // - Terdapat 2 Jurusan yang terdaftar
        // - Jurusan dengan rata-rata tertinggi (Teknik Informatika dan Komputer) berada di peringkat 1 (index 0)
        // - Teknik Elektro berada di peringkat 2 (index 1)
        $this->assertCount(2, $rankings);
        $this->assertEquals('Teknik Informatika dan Komputer', $rankings->get(0)['jurusan']);
        $this->assertEquals('Teknik Elektro', $rankings->get(1)['jurusan']);
        $this->assertGreaterThan($rankings->get(1)['average_score'], $rankings->get(0)['average_score']);
    }

    /**
     * Menguji halaman Integritas Jurusan di bawah role Direktur dapat diakses dengan sukses.
     */
    #[Test]
    #[TestDox('Memastikan halaman Integritas Jurusan di Direktur memuat data sukses (Status 200)')]
    public function test_direktur_integritas_page_renders_successfully(): void
    {
        // 1. Buat User Direktur
        $direktur = User::create([
            'nama' => 'Bapak Direktur',
            'email' => 'direktur@example.com',
            'password' => bcrypt('password123'),
            'nama_jurusan' => 'Akuntansi',
            'status' => 'Aktif',
        ]);
        $direktur->assignRole('Direktur');

        // 2. Akses halaman Integritas Jurusan sebagai Direktur (menggunakan session)
        $response = $this->withSession([
            'user_id' => $direktur->user_id,
            'role' => 'direktur',
        ])->get('/direktur/integritas');

        // 3. Pastikan halaman termuat dengan sukses & mengirim view yang benar
        $response->assertStatus(200);
        $response->assertViewIs('direktur.integritas.index');
        $response->assertViewHas('rankings');
    }

    /**
     * Menguji pemeringkatan jurusan dihitung dengan formula MAUT yang benar dan menghasilkan skor yang sesuai dengan contoh riil.
     */
    #[Test]
    #[TestDox('Memastikan pemeringkatan jurusan dihitung dengan formula MAUT yang benar dan menghasilkan skor yang sesuai dengan contoh riil')]
    public function test_exact_maut_calculation_matches_user_example(): void
    {
        $mockService = new class extends SpkMautService {
            public function calculateRawScores(Kegiatan $kegiatan): array
            {
                $name = $kegiatan->nama_kegiatan;
                
                if ($name === 'TIKGAMES2024_1') {
                    return ['c1' => 1.00, 'c2' => 1.00, 'c3' => 1.00, 'c4' => 1.00];
                }
                if ($name === 'TIKGAMES2025') {
                    return ['c1' => 1.00, 'c2' => 1.00, 'c3' => 1.00, 'c4' => 1.00];
                }
                if ($name === 'TIKGAMES2024_2') {
                    return ['c1' => 1.00, 'c2' => 0.10, 'c3' => 1.00, 'c4' => 1.00];
                }
                if ($name === 'TIKGAMES2023') {
                    return ['c1' => 0.40, 'c2' => 0.10, 'c3' => 1.00, 'c4' => 1.00];
                }
                
                if ($name === 'ELEKTROGAMES2023') {
                    return ['c1' => 0.80, 'c2' => 0.90, 'c3' => 1.00, 'c4' => 1.00];
                }
                if ($name === 'ELEKTROGAMES2022') {
                    return ['c1' => 0.10, 'c2' => 1.00, 'c3' => 1.00, 'c4' => 1.00];
                }
                if ($name === 'ELEKTROGAMES2024') {
                    return ['c1' => 0.60, 'c2' => 0.10, 'c3' => 1.00, 'c4' => 1.00];
                }
                if ($name === 'ELEKTROGAMES2021') {
                    return ['c1' => 0.40, 'c2' => 1.00, 'c3' => 0.00, 'c4' => 1.00];
                }
                
                if ($name === 'TGPGAMES2020') {
                    return ['c1' => 1.00, 'c2' => 1.00, 'c3' => 1.00, 'c4' => 1.00];
                }
                if ($name === 'TGPGAMES2021') {
                    return ['c1' => 0.80, 'c2' => 1.00, 'c3' => 1.00, 'c4' => 1.00];
                }
                if ($name === 'TGPGAMES2022') {
                    return ['c1' => 1.00, 'c2' => 0.10, 'c3' => 0.00, 'c4' => 1.00];
                }
                
                return parent::calculateRawScores($kegiatan);
            }
        };

        $this->app->instance(SpkMautService::class, $mockService);

        $user = User::create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'nama_jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Aktif',
        ]);

        $activities = [
            // JTIK
            ['name' => 'TIKGAMES2024_1', 'jurusan' => 'Teknik Informatika dan Komputer'],
            ['name' => 'TIKGAMES2025', 'jurusan' => 'Teknik Informatika dan Komputer'],
            ['name' => 'TIKGAMES2024_2', 'jurusan' => 'Teknik Informatika dan Komputer'],
            ['name' => 'TIKGAMES2023', 'jurusan' => 'Teknik Informatika dan Komputer'],

            // JTE
            ['name' => 'ELEKTROGAMES2023', 'jurusan' => 'Teknik Elektro'],
            ['name' => 'ELEKTROGAMES2022', 'jurusan' => 'Teknik Elektro'],
            ['name' => 'ELEKTROGAMES2024', 'jurusan' => 'Teknik Elektro'],
            ['name' => 'ELEKTROGAMES2021', 'jurusan' => 'Teknik Elektro'],

            // JTGP
            ['name' => 'TGPGAMES2020', 'jurusan' => 'Teknik Grafika dan Penerbitan'],
            ['name' => 'TGPGAMES2021', 'jurusan' => 'Teknik Grafika dan Penerbitan'],
            ['name' => 'TGPGAMES2022', 'jurusan' => 'Teknik Grafika dan Penerbitan'],
        ];

        foreach ($activities as $act) {
            $keg = Kegiatan::create([
                'nama_kegiatan' => $act['name'],
                'prodi_penyelenggara' => 'Prodi',
                'pemilik_kegiatan' => 'PJ',
                'user_id' => $user->user_id,
                'jurusan_penyelenggara' => $act['jurusan'],
                'tanggal_mulai' => '2026-06-01',
                'tanggal_selesai' => '2026-06-05',
                'jumlah_dicairkan' => 1000000.00,
                'wadir_tujuan' => 1,
                'status_utama_id' => 3,
                'posisi_id' => 5,
            ]);

            Lpj::create([
                'kegiatan_id' => $keg->kegiatan_id,
                'grand_total_realisasi' => 1000000.00,
                'submitted_at' => '2026-06-12',
                'tenggat_lpj' => '2026-06-15',
                'realisasi_tanggal_mulai' => '2026-06-01',
                'realisasi_tanggal_selesai' => '2026-06-05',
                'status_id' => 1,
            ]);
        }

        $rankings = $mockService->getJurusanRankings();

        $this->assertCount(3, $rankings);

        $jtik = $rankings->firstWhere('jurusan', 'Teknik Informatika dan Komputer');
        $jte = $rankings->firstWhere('jurusan', 'Teknik Elektro');
        $jtgp = $rankings->firstWhere('jurusan', 'Teknik Grafika dan Penerbitan');

        $this->assertNotNull($jtik);
        $this->assertNotNull($jte);
        $this->assertNotNull($jtgp);

        $this->assertEquals(0.7046, $jtik['average_score']);
        $this->assertEquals(0.5625, $jte['average_score']);
        $this->assertEquals(0.6875, $jtgp['average_score']);
    }
}
