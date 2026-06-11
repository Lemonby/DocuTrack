<?php

namespace Database\Seeders;

use App\Models\Iku;
use App\Models\IndikatorKak;
use App\Models\Kak;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Models\Rab;
use App\Models\TahapanPelaksanaan;
use App\Models\TahapanPencairan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class AcademicActivitiesSeeder
 *
 * Seeder lengkap untuk mensimulasikan lingkungan DocuTrack yang kaya data.
 * Untuk setiap jurusan (8 jurusan), seeder ini menghasilkan:
 * - 5 usulan baru yang menunggu di antrean Verifikator (belum cair, posisi_id = 2, status_utama_id = 1).
 * - 3 usulan yang telah disetujui penuh & dicairkan oleh Bendahara, siap bagi Admin Jurusan untuk mengajukan LPJ
 *   (posisi_id = 1, status_utama_id = 5 [Dana diberikan], memiliki kode MAK, log pencairan, dan draf LPJ kosong).
 */
class AcademicActivitiesSeeder extends Seeder
{
    /**
     * Menjalankan pengisian data ke database.
     */
    public function run(): void
    {
        // Daftar 8 Jurusan akademis
        $jurusans = [
            'Teknik Informatika dan Komputer',
            'Teknik Elektro',
            'Teknik Sipil',
            'Teknik Mesin',
            'Teknik Grafika dan Penerbitan',
            'Akuntansi',
            'Administrasi Niaga',
            'Pascasarjana',
        ];

        // Nama-nama kegiatan realistis untuk variasi data
        $templatesMenunggu = [
            'Workshop Penguatan Metodologi Riset Terapan',
            'Seminar Nasional Teknologi dan Inovasi Industri',
            'Pelatihan Sertifikasi Kompetensi Profesi Mahasiswa',
            'Penyusunan Kurikulum Berbasis Outcome-Based Education (OBE)',
            'Pengembangan Lab Praktikum Berstandar Industri',
        ];

        $templatesSiapLpj = [
            'Penerapan IoT untuk Efisiensi Energi Rumah Tangga',
            'Pelatihan Pengolahan Limbah Industri Ramah Lingkungan',
            'Kunjungan Industri Strategis Mahasiswa Tingkat Akhir',
        ];

        // Ambil Bendahara sebagai pencatat log pencairan jika ada
        $bendahara = User::whereHas('roles', function ($q) {
            $q->where('name', 'Bendahara');
        })->first();
        $bendaharaId = $bendahara ? $bendahara->user_id : null;

        foreach ($jurusans as $jurusan) {
            // Cari user Admin yang mewakili jurusan tersebut
            $user = User::where('nama_jurusan', $jurusan)->first();
            $userId = $user ? $user->user_id : 1;
            $userNama = $user ? $user->nama : 'Pengusul Default';

            // Dapatkan salah satu program studi (prodi) di jurusan tersebut
            $prodi = DB::table('prodis')->where('nama_jurusan', $jurusan)->first();
            $prodiName = $prodi ? $prodi->nama_prodi : $jurusan;

            // =========================================================================
            // KATEGORI 1: 5 Usulan Menunggu Verifikator (Awaiting MAK)
            // =========================================================================
            for ($i = 0; $i < 5; $i++) {
                $namaKegiatan = $templatesMenunggu[$i].' '.$jurusan;

                // 1. Insert kegiatan
                $kegiatan = Kegiatan::create([
                    'nama_kegiatan' => $namaKegiatan,
                    'prodi_penyelenggara' => $prodiName,
                    'pemilik_kegiatan' => $userNama,
                    'nip' => '199001012020121001',
                    'nama_pj' => 'PJ Kegiatan '.$jurusan,
                    'user_id' => $userId,
                    'jurusan_penyelenggara' => $jurusan,
                    'status_utama_id' => 1, // Menunggu
                    'posisi_id' => 2,       // Verifikator
                    'wadir_tujuan' => 1,
                    'tanggal_mulai' => '2026-07-01',
                    'tanggal_selesai' => '2026-07-05',
                    'jumlah_dicairkan' => 12000000.00,
                ]);

                // 2. Insert KAK
                $kak = Kak::create([
                    'kegiatan_id' => $kegiatan->kegiatan_id,
                    'penerima_manfaat' => 'Dosen dan Mahasiswa Jurusan '.$jurusan,
                    'gambaran_umum' => 'Meningkatkan pemahaman praktis akademis di lingkungan '.$jurusan,
                    'metode_pelaksanaan' => 'Metode meliputi pemaparan materi ahli dan diskusi terfokus.',
                    'tgl_pembuatan' => '2026-06-01',
                ]);

                // Hubungkan IKU secara acak (1 s.d 3 IKU)
                $ikus = Iku::inRandomOrder()->take(rand(1, 3))->get();
                $kak->ikus()->sync($ikus->pluck('id')->toArray());

                // 3. Indikator Keberhasilan (3 bulan)
                for ($m = 1; $m <= 3; $m++) {
                    IndikatorKak::create([
                        'kak_id' => $kak->kak_id,
                        'bulan' => $m,
                        'indikator_keberhasilan' => 'Indikator Keberhasilan Target Bulan ke-'.$m,
                        'target_persen' => 100.00,
                    ]);
                }

                // 4. Tahapan Pelaksanaan
                TahapanPelaksanaan::create([
                    'kak_id' => $kak->kak_id,
                    'nama_tahapan' => 'Tahap 1: Persiapan administrasi dan koordinasi internal',
                ]);
                TahapanPelaksanaan::create([
                    'kak_id' => $kak->kak_id,
                    'nama_tahapan' => 'Tahap 2: Eksekusi program pelatihan / lokakarya utama',
                ]);

                // 5. RAB
                Rab::create([
                    'kak_id' => $kak->kak_id, 'kategori_id' => 4,
                    'uraian' => 'Konsumsi dan Snack Panitia', 'rincian' => 'Snack harian rapat',
                    'sat1' => 'Orang', 'sat2' => 'Hari', 'vol1' => 20.00, 'vol2' => 2.00, 'harga' => 25000.00,
                ]);
                Rab::create([
                    'kak_id' => $kak->kak_id, 'kategori_id' => 6,
                    'uraian' => 'Honorarium Narasumber Teknis', 'rincian' => 'Honor pemateri eksternal',
                    'sat1' => 'Orang', 'sat2' => 'Sesi', 'vol1' => 1.00, 'vol2' => 2.00, 'harga' => 3500000.00,
                ]);

                // Update Kegiatan dengan total RAB aktual
                $kegiatan->update([
                    'jumlah_dicairkan' => $kak->total_rab,
                    'dana_di_setujui' => $kak->total_rab,
                ]);
            }

            // =========================================================================
            // KATEGORI 2: 3 Usulan Siap Ajukan LPJ (Dana Diberikan / Disbursed)
            // =========================================================================
            for ($j = 0; $j < 3; $j++) {
                $namaKegiatanSiap = $templatesSiapLpj[$j].' '.$jurusan;

                // 1. Insert kegiatan dengan posisi Admin (1) & status Dana Diberikan (5)
                $kegiatanCair = Kegiatan::create([
                    'nama_kegiatan' => $namaKegiatanSiap,
                    'prodi_penyelenggara' => $prodiName,
                    'pemilik_kegiatan' => $userNama,
                    'nip' => '199001012020121001',
                    'nama_pj' => 'PJ Pelaksana '.$jurusan,
                    'user_id' => $userId,
                    'jurusan_penyelenggara' => $jurusan,
                    'status_utama_id' => 5, // Status: Dana diberikan
                    'posisi_id' => 1,       // Posisi: Admin (kembali ke pengusul untuk unggah LPJ)
                    'wadir_tujuan' => 1,
                    'tanggal_mulai' => '2026-06-01',
                    'tanggal_selesai' => '2026-06-05',
                    'bukti_mak' => 'MAK-'.strtoupper(substr(md5($jurusan.$j), 0, 8)), // Kode MAK Terisi
                    'tanggal_pencairan' => '2026-06-02 10:00:00', // Tanggal pencairan terisi
                    'jumlah_dicairkan' => 10000000.00,
                    'dana_di_setujui' => 10000000.00,
                ]);

                // 2. Insert KAK
                $kakCair = Kak::create([
                    'kegiatan_id' => $kegiatanCair->kegiatan_id,
                    'penerima_manfaat' => 'Dosen dan Mahasiswa Jurusan '.$jurusan,
                    'gambaran_umum' => 'Meningkatkan kompetensi aplikatif akademis pasca-pencairan dana untuk jurusan '.$jurusan,
                    'metode_pelaksanaan' => 'Metode meliputi implementasi lapangan dan penyusunan berkas LPJ.',
                    'tgl_pembuatan' => '2026-05-15',
                ]);

                // Hubungkan IKU secara acak (1 s.d 3 IKU)
                $ikusCair = Iku::inRandomOrder()->take(rand(1, 3))->get();
                $kakCair->ikus()->sync($ikusCair->pluck('id')->toArray());

                // 3. Indikator Keberhasilan (3 bulan)
                for ($m = 1; $m <= 3; $m++) {
                    IndikatorKak::create([
                        'kak_id' => $kakCair->kak_id,
                        'bulan' => $m,
                        'indikator_keberhasilan' => 'Realisasi Target Indikator Bulan ke-'.$m,
                        'target_persen' => 100.00,
                    ]);
                }

                // 4. Tahapan Pelaksanaan
                TahapanPelaksanaan::create([
                    'kak_id' => $kakCair->kak_id,
                    'nama_tahapan' => 'Tahap 1: Eksekusi lapangan dan belanja logistik awal',
                ]);
                TahapanPelaksanaan::create([
                    'kak_id' => $kakCair->kak_id,
                    'nama_tahapan' => 'Tahap 2: Penyelesaian laporan pertanggungjawaban akhir',
                ]);

                // 5. RAB
                Rab::create([
                    'kak_id' => $kakCair->kak_id, 'kategori_id' => 4,
                    'uraian' => 'Bahan Praktikum Lapangan', 'rincian' => 'Komponen elektronika / ATK pendukung',
                    'sat1' => 'Paket', 'sat2' => 'Kali', 'vol1' => 1.00, 'vol2' => 1.00, 'harga' => 3000000.00,
                ]);
                Rab::create([
                    'kak_id' => $kakCair->kak_id, 'kategori_id' => 5,
                    'uraian' => 'Sewa Bus Transportasi Lapangan', 'rincian' => 'Sewa kendaraan operasional tim',
                    'sat1' => 'Unit', 'sat2' => 'Hari', 'vol1' => 1.00, 'vol2' => 2.00, 'harga' => 3500000.00,
                ]);

                // Hitung total RAB aktual untuk pencairan penuh
                $totalRabVal = $kakCair->total_rab;
                $kegiatanCair->update([
                    'jumlah_dicairkan' => $totalRabVal,
                    'dana_di_setujui' => $totalRabVal,
                ]);

                // 6. Catat Log Tahapan Pencairan (Simulasi pencairan dana oleh Bendahara)
                TahapanPencairan::create([
                    'kegiatan_id' => $kegiatanCair->kegiatan_id,
                    'tgl_pencairan' => '2026-06-02',
                    'termin' => 'Pencairan Tunggal (100%)',
                    'nominal' => $totalRabVal,
                    'catatan' => 'Dana dicairkan sepenuhnya secara tunai/transfer oleh bendahara.',
                    'created_by' => $bendaharaId,
                ]);

                // 7. Insert Draf LPJ kosong (Belum submit, submitted_at = null)
                // - Menunggu upload LPJ oleh Admin Jurusan
                // - Batas tenggat: 14 hari kalender murni dari tanggal pencairan (2 Juni + 14 hari = 16 Juni 2026)
                Lpj::create([
                    'kegiatan_id' => $kegiatanCair->kegiatan_id,
                    'grand_total_realisasi' => null,  // Belum realisasi
                    'submitted_at' => null,           // Belum diajukan
                    'approved_at' => null,
                    'tenggat_lpj' => '2026-06-16',   // +14 hari kalender dari 2 Juni
                    'status_id' => 1,                 // Status LPJ: Menunggu
                    'realisasi_tanggal_mulai' => null,
                    'realisasi_tanggal_selesai' => null,
                ]);
            }
        }
    }
}
