<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IkuController extends Controller
{
    public function index()
    {
        $list_iku = [
            // IKU Section (Based on KAK Choices)
            [
                'id' => 1, 
                'type' => 'IKU',
                'nama' => 'Mendapat Pekerjaan Layak', 
                'target' => '80%', 
                'capaian' => '75%', 
                'status' => 'Aktif',
                'kategori' => 'IKU 1',
                'tahun_periode' => '2024',
                'deskripsi' => 'Lulusan yang berhasil mendapatkan pekerjaan dengan penghasilan di atas standar.'
            ],
            [
                'id' => 2, 
                'type' => 'IKU',
                'nama' => 'Melanjutkan Studi', 
                'target' => '15%', 
                'capaian' => '12%', 
                'status' => 'Aktif',
                'kategori' => 'IKU 1',
                'tahun_periode' => '2024',
                'deskripsi' => 'Lulusan yang melanjutkan pendidikan ke jenjang yang lebih tinggi.'
            ],
            [
                'id' => 3, 
                'type' => 'IKU',
                'nama' => 'Menjadi Wiraswasta', 
                'target' => '5%', 
                'capaian' => '8%', 
                'status' => 'Aktif',
                'kategori' => 'IKU 1',
                'tahun_periode' => '2024',
                'deskripsi' => 'Lulusan yang membuka lapangan usaha sendiri.'
            ],
            [
                'id' => 4, 
                'type' => 'IKU',
                'nama' => 'Kegiatan Luar Prodi', 
                'target' => '20%', 
                'capaian' => '18%', 
                'status' => 'Aktif',
                'kategori' => 'IKU 2',
                'tahun_periode' => '2024',
                'deskripsi' => 'Mahasiswa yang menghabiskan waktu belajar di luar program studi.'
            ],
            [
                'id' => 5, 
                'type' => 'IKU',
                'nama' => 'Prestasi Mahasiswa', 
                'target' => '10%', 
                'capaian' => '15%', 
                'status' => 'Aktif',
                'kategori' => 'IKU 2',
                'tahun_periode' => '2024',
                'deskripsi' => 'Mahasiswa yang meraih prestasi tingkat nasional atau internasional.'
            ],
            // Renstra Section
            [
                'id' => 6, 
                'type' => 'RENSTRA',
                'nama' => 'Akreditasi Internasional', 
                'target' => '50%', 
                'capaian' => '30%', 
                'status' => 'Aktif',
                'kategori' => 'Kualitas Prodi',
                'tahun_periode' => '2020-2024',
                'deskripsi' => 'Jumlah program studi yang terakreditasi oleh lembaga internasional.'
            ],
            [
                'id' => 7, 
                'type' => 'RENSTRA',
                'nama' => 'Publikasi Ilmiah Bereputasi', 
                'target' => '100', 
                'capaian' => '78', 
                'status' => 'Aktif',
                'kategori' => 'Penelitian',
                'tahun_periode' => '2020-2024',
                'deskripsi' => 'Jumlah publikasi pada jurnal internasional bereputasi (Scopus/Sinta 1).'
            ],
        ];

        return view('superadmin.iku.index', compact('list_iku'));
    }
}
