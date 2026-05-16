<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifikatorController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total' => 45,
            'disetujui' => 20,
            'ditolak' => 5,
            'pending' => 20,
        ];
        $list_usulan = [
            [
                'id' => 601,
                'nama' => 'Peningkatan Kompetensi AI Mahasiswa TI',
                'pengusul' => 'Yovana Ibnu Sina',
                'nim' => '2407411059',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-14',
                'status' => 'Menunggu'
            ],
            [
                'id' => 602,
                'nama' => 'Workshop UI/UX Design Modern',
                'pengusul' => 'Ahmad Fauzi',
                'nim' => '2407411050',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Menunggu'
            ],
            [
                'id' => 603,
                'nama' => 'Seminar Internasional Digital Transformation',
                'pengusul' => 'Budi Santoso',
                'nim' => '2407411003',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Review'
            ],
            [
                'id' => 604,
                'nama' => 'Lomba Karya Tulis Ilmiah Nasional',
                'pengusul' => 'Dewi Lestari',
                'nim' => '2407411051',
                'prodi' => 'Akuntansi',
                'jurusan' => 'Akuntansi',
                'tanggal_pengajuan' => '2026-05-16',
                'status' => 'Disetujui'
            ],
            [
                'id' => 605,
                'nama' => 'Pengadaan Alat Praktikum Jaringan',
                'pengusul' => 'Rizky Pratama',
                'nim' => '2407411088',
                'prodi' => 'Teknik Komputer',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-16',
                'status' => 'Ditolak'
            ],
            [
                'id' => 606,
                'nama' => 'Pelatihan Sertifikasi Mikrotik MTCNA',
                'pengusul' => 'Siti Aminah',
                'nim' => '2407411099',
                'prodi' => 'Telekomunikasi',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2026-05-17',
                'status' => 'Revisi'
            ],
            [
                'id' => 607,
                'nama' => 'Kunjungan Industri ke Silicon Valley',
                'pengusul' => 'Kevin Sanjaya',
                'nim' => '2407411012',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-17',
                'status' => 'Menunggu'
            ],
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
        
        return view('verifikator.dashboard', compact('stats', 'list_usulan', 'jurusan_list'));
    }
}
