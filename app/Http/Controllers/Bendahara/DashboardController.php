<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'         => 12,
            'danaDiberikan' => 8,
            'ditolak'       => 1,
            'menunggu'      => 3,
        ];
        
        $list_kak = [
            [
                'id' => 1101,
                'nama' => 'Workshop UI/UX Design Modern',
                'pengusul' => 'Rizki Pratama',
                'nim' => '2407411050',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-12',
                'status' => 'Belum Dicairkan'
            ],
            [
                'id' => 1102,
                'nama' => 'Seminar Internasional Blockchain',
                'pengusul' => 'Ahmad Fauzi',
                'nim' => '2407411052',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-13',
                'status' => 'Sudah Dicairkan'
            ],
            [
                'id' => 1103,
                'nama' => 'Pengadaan Alat Praktikum Elektro',
                'pengusul' => 'Bambang Sudarsono',
                'nim' => '2407411055',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Belum Dicairkan'
            ],
            [
                'id' => 1104,
                'nama' => 'Lomba Karya Tulis Ilmiah Nasional',
                'pengusul' => 'Dewi Lestari',
                'nim' => '2407411051',
                'prodi' => 'Akuntansi',
                'jurusan' => 'Akuntansi',
                'tanggal_pengajuan' => '2026-05-16',
                'status' => 'Belum Dicairkan'
            ],
        ];

        $list_lpj = [
            [
                'id' => 1301,
                'nama' => 'LPJ - Bakti Sosial Mahasiswa 2026',
                'pengusul' => 'Andi Wijaya',
                'nim' => '2407411060',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2026-05-11',
                'status' => 'Menunggu Verifikasi'
            ],
            [
                'id' => 1302,
                'nama' => 'LPJ - Kunjungan Industri PT. Digital Jaya',
                'pengusul' => 'Santi Kurnia',
                'nim' => '2407411061',
                'prodi' => 'Administrasi Niaga',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => '2026-05-13',
                'status' => 'Revisi'
            ],
            [
                'id' => 1303,
                'nama' => 'LPJ - Workshop Mobile Development',
                'pengusul' => 'Rizky Pratama',
                'nim' => '2407411062',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Telah Direvisi'
            ],
            [
                'id' => 1305,
                'nama' => 'LPJ - Seminar Nasional Teknologi 4.0',
                'pengusul' => 'Dewi Lestari',
                'nim' => '2407411064',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-10',
                'status' => 'Telah Direvisi'
            ],
        ];

        return view('bendahara.dashboard', compact('stats', 'list_kak', 'list_lpj'));
    }
}
