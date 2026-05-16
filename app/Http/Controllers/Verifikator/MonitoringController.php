<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
        
        // Mock data for now
        $list_proposal = [
            [
                'id' => 1401,
                'nama' => 'Seminar Nasional Teknologi Informasi 2026',
                'pengusul' => 'John Doe',
                'nim' => '2407411001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tahap_sekarang' => 'Verifikasi',
                'status' => 'In Process'
            ],
            [
                'id' => 1402,
                'nama' => 'Lomba Karya Tulis Ilmiah Mahasiswa',
                'pengusul' => 'Jane Smith',
                'nim' => '2407411002',
                'jurusan' => 'Teknik Elektro',
                'tahap_sekarang' => 'Dana Cair',
                'status' => 'Approved'
            ],
            [
                'id' => 1403,
                'nama' => 'Workshop Desain Grafis Dasar',
                'pengusul' => 'Bob Wilson',
                'nim' => '2407411003',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tahap_sekarang' => 'Pengajuan',
                'status' => 'Ditolak'
            ],
            [
                'id' => 1404,
                'nama' => 'Pelatihan Kepemimpinan Mahasiswa',
                'pengusul' => 'Siti Aminah',
                'nim' => '2407411004',
                'jurusan' => 'Akuntansi',
                'tahap_sekarang' => 'ACC WD',
                'status' => 'In Process'
            ],
            [
                'id' => 1405,
                'nama' => 'Pekan Olahraga Mahasiswa',
                'pengusul' => 'Rizky Pratama',
                'nim' => '2407411005',
                'jurusan' => 'Administrasi Niaga',
                'tahap_sekarang' => 'LPJ',
                'status' => 'Approved'
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

        return view('verifikator.monitoring.index', compact('list_proposal', 'tahapan_all', 'jurusan_list'));
    }
}
