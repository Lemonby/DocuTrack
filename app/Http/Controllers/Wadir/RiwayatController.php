<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $list_riwayat = [
            [
                'id' => 201,
                'nama' => 'Pembangunan Infrastruktur Jaringan Kampus',
                'pengusul' => 'Dedi Wijaya',
                'nim' => '2407411010',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tgl' => '10 Mei 2026',
                'status' => 'Disetujui'
            ],
            [
                'id' => 202,
                'nama' => 'Pengadaan Lisensi Software Design Grafis',
                'pengusul' => 'Rina Kartika',
                'nim' => '2407411011',
                'prodi' => 'Teknik Grafika',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tgl' => '12 Mei 2026',
                'status' => 'Disetujui'
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

        return view('wadir.riwayat.index', compact('list_riwayat', 'jurusan_list'));
    }
}
