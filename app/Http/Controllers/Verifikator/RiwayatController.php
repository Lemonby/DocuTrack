<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $list_riwayat = [
            [
                'id' => 601,
                'nama' => 'Peningkatan Kompetensi AI Mahasiswa TI',
                'pengusul' => 'Yovana Ibnu Sina',
                'nim' => '2407411059',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_verifikasi' => '2026-05-14',
                'status' => 'Disetujui'
            ],
            [
                'id' => 602,
                'nama' => 'Workshop UI/UX Design Modern',
                'pengusul' => 'Ahmad Fauzi',
                'nim' => '2407411050',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_verifikasi' => '2026-05-15',
                'status' => 'Disetujui'
            ],
            [
                'id' => 603,
                'nama' => 'Seminar Internasional Digital Transformation',
                'pengusul' => 'Budi Santoso',
                'nim' => '2407411003',
                'jurusan' => 'Teknik Elektro',
                'tanggal_verifikasi' => '2026-05-15',
                'status' => 'Revisi'
            ],
            [
                'id' => 604,
                'nama' => 'Lomba Karya Tulis Ilmiah Nasional',
                'pengusul' => 'Dewi Lestari',
                'nim' => '2407411051',
                'jurusan' => 'Akuntansi',
                'tanggal_verifikasi' => '2026-05-16',
                'status' => 'Disetujui'
            ],
            [
                'id' => 605,
                'nama' => 'Pengadaan Alat Praktikum Jaringan',
                'pengusul' => 'Rizky Pratama',
                'nim' => '2407411088',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_verifikasi' => '2026-05-16',
                'status' => 'Ditolak'
            ],
            [
                'id' => 606,
                'nama' => 'Pelatihan Sertifikasi Mikrotik MTCNA',
                'pengusul' => 'Siti Aminah',
                'nim' => '2407411099',
                'jurusan' => 'Teknik Elektro',
                'tanggal_verifikasi' => '2026-05-17',
                'status' => 'Revisi'
            ],
            [
                'id' => 607,
                'nama' => 'Kunjungan Industri ke Silicon Valley',
                'pengusul' => 'Kevin Sanjaya',
                'nim' => '2407411012',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_verifikasi' => '2026-05-17',
                'status' => 'Disetujui'
            ],
            [
                'id' => 608,
                'nama' => 'Bootcamp Fullstack Developer',
                'pengusul' => 'Maya Sari',
                'nim' => '2407411015',
                'jurusan' => 'Teknik Elektro',
                'tanggal_verifikasi' => '2026-05-18',
                'status' => 'Disetujui'
            ],
            [
                'id' => 609,
                'nama' => 'Webinar Cyber Security 2026',
                'pengusul' => 'Andi Wijaya',
                'nim' => '2407411022',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_verifikasi' => '2026-05-18',
                'status' => 'Ditolak'
            ],
            [
                'id' => 610,
                'nama' => 'Pengembangan Sistem Smart Campus',
                'pengusul' => 'Putra Ramadhan',
                'nim' => '2407411033',
                'jurusan' => 'Teknik Elektro',
                'tanggal_verifikasi' => '2026-05-19',
                'status' => 'Revisi'
            ]
        ];

        $jurusan_list = [
            'Teknik Informatika dan Komputer',
            'Teknik Grafika dan Penerbitan',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Administrasi Niaga',
            'Akuntansi',
        ];

        return view('verifikator.riwayat.index', compact('list_riwayat', 'jurusan_list'));
    }
}
