<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Status Utama
        DB::table('status_utamas')->insert([
            ['status_id' => 1, 'nama_status_usulan' => 'Menunggu'],
            ['status_id' => 2, 'nama_status_usulan' => 'Revisi'],
            ['status_id' => 3, 'nama_status_usulan' => 'Disetujui'],
            ['status_id' => 4, 'nama_status_usulan' => 'Ditolak'],
            ['status_id' => 5, 'nama_status_usulan' => 'Dana diberikan'],
            ['status_id' => 6, 'nama_status_usulan' => 'LPJ Disetujui'],
            ['status_id' => 7, 'nama_status_usulan' => 'Telah Diverifikasi'],
            ['status_id' => 8, 'nama_status_usulan' => 'Selesai'],
        ]);

        // Wadir
        DB::table('wadirs')->insert([
            ['wadir_id' => 1, 'nama_wadir' => 'Wadir 1'],
            ['wadir_id' => 2, 'nama_wadir' => 'Wadir 2'],
            ['wadir_id' => 3, 'nama_wadir' => 'Wadir 3'],
            ['wadir_id' => 4, 'nama_wadir' => 'Wadir 4'],
        ]);

        // Jurusan
        $jurusans = [
            'Administrasi Niaga', 'Akuntansi', 'Pascasarjana', 'Teknik Elektro',
            'Teknik Grafika dan Penerbitan', 'Teknik Informatika dan Komputer',
            'Teknik Mesin', 'Teknik Sipil',
        ];
        foreach ($jurusans as $j) {
            DB::table('jurusans')->insert(['nama_jurusan' => $j]);
        }

        // Prodi
        $prodis = [
            ['D3 Administrasi Bisnis', 'Administrasi Niaga'],
            ['D4 Administrasi Bisnis Terapan', 'Administrasi Niaga'],
            ['D4 Bahasa Inggris untuk Komunikasi Bisnis dan Prof', 'Administrasi Niaga'],
            ['D4 Meeting, Incentive, Convention, and Exhibition ', 'Administrasi Niaga'],
            ['D3 Akuntansi', 'Akuntansi'],
            ['D3 Keuangan dan Perbankan', 'Akuntansi'],
            ['D4 Akuntansi Keuangan', 'Akuntansi'],
            ['D4 Keuangan dan Perbankan Syariah', 'Akuntansi'],
            ['D4 Manajemen Keuangan', 'Akuntansi'],
            ['S2 Magister Terapan Rekayasa Teknologi Manufaktur', 'Pascasarjana'],
            ['S2 Magister Terapan Teknik Elektro', 'Pascasarjana'],
            ['D3 Teknik Elektronika Industri', 'Teknik Elektro'],
            ['D3 Teknik Listrik', 'Teknik Elektro'],
            ['D3 Teknik Telekomunikasi', 'Teknik Elektro'],
            ['D4 Broadband Multimedia', 'Teknik Elektro'],
            ['D4 Teknik Instrumentasi dan Kontrol Industri', 'Teknik Elektro'],
            ['D4 Teknik Otomasi Listrik Industri', 'Teknik Elektro'],
            ['D3 Penerbitan (Jurnalistik)', 'Teknik Grafika dan Penerbitan'],
            ['D3 Teknik Grafika', 'Teknik Grafika dan Penerbitan'],
            ['D4 Desain Grafis', 'Teknik Grafika dan Penerbitan'],
            ['D4 Teknologi Industri Cetak Kemasan', 'Teknik Grafika dan Penerbitan'],
            ['D1 Teknik Komputer dan Jaringan', 'Teknik Informatika dan Komputer'],
            ['D4 Teknik Informatika', 'Teknik Informatika dan Komputer'],
            ['D4 Teknik Multimedia dan Jaringan', 'Teknik Informatika dan Komputer'],
            ['D4 Teknik Multimedia Digital', 'Teknik Informatika dan Komputer'],
            ['D3 Alat Berat', 'Teknik Mesin'],
            ['D3 Teknik Konversi Energi', 'Teknik Mesin'],
            ['D3 Teknik Mesin', 'Teknik Mesin'],
            ['D4 Pembangkit Tenaga Listrik', 'Teknik Mesin'],
            ['D4 Teknologi Rekayasa Konversi Energi', 'Teknik Mesin'],
            ['D4 Teknologi Rekayasa Manufaktur', 'Teknik Mesin'],
            ['D4 Teknologi Rekayasa Perawatan Alat Berat', 'Teknik Mesin'],
            ['D3 Konstruksi Gedung', 'Teknik Sipil'],
            ['D3 Konstruksi Sipil', 'Teknik Sipil'],
            ['D4 Manajemen Konstruksi', 'Teknik Sipil'],
            ['D4 Perancangan Jalan dan Jembatan', 'Teknik Sipil'],
        ];
        foreach ($prodis as [$nama, $jurusan]) {
            DB::table('prodis')->insert(['nama_prodi' => $nama, 'nama_jurusan' => $jurusan]);
        }

        // Kategori RAB
        DB::table('kategori_rabs')->insert([
            ['kategori_rab_id' => 4, 'nama_kategori' => 'Belanja Barang'],
            ['kategori_rab_id' => 5, 'nama_kategori' => 'Belanja Perjalanan'],
            ['kategori_rab_id' => 6, 'nama_kategori' => 'Belanja Jasa'],
        ]);

        // Seed IKUs
        DB::table('ikus')->insert([
            [
                'kode_iku' => 'IKU_1',
                'indikator_kinerja' => 'Mendapat Pekerjaan',
                'deskripsi' => 'Mendapat Pekerjaan',
                'target' => '80%',
                'realisasi' => '85%',
                'tahun' => 2020,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_iku' => 'IKU_2',
                'indikator_kinerja' => 'Melanjutkan studi',
                'deskripsi' => 'Melanjutkan studi',
                'target' => '50%',
                'realisasi' => '55%',
                'tahun' => 2020,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_iku' => 'IKU_3',
                'indikator_kinerja' => 'Menjadi Wiraswasta',
                'deskripsi' => 'Menjadi Wiraswasta',
                'target' => '60%',
                'realisasi' => '62%',
                'tahun' => 2020,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_iku' => 'IKU_4',
                'indikator_kinerja' => 'Kegiatan luar prodi',
                'deskripsi' => 'Kegiatan luar prodi',
                'target' => '70%',
                'realisasi' => '75%',
                'tahun' => 2020,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_iku' => 'IKU_5',
                'indikator_kinerja' => 'Prestasi',
                'deskripsi' => 'Prestasi',
                'target' => '90%',
                'realisasi' => '92%',
                'tahun' => 2020,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_iku' => 'IKU_6',
                'indikator_kinerja' => 'Pengabdian Masyarakat',
                'deskripsi' => 'Pengabdian Masyarakat',
                'target' => '80%',
                'realisasi' => '85%',
                'tahun' => 2020,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
