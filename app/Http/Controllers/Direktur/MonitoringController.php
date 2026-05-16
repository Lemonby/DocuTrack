<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $list_jurusan = [
            'Teknik Informatika dan Komputer',
            'Teknik Grafika dan Penerbitan',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Administrasi Niaga',
            'Akuntansi',
        ];
        return view('direktur.monitoring.index', compact('list_jurusan'));
    }

    public function getData(Request $request)
    {
        // Mock data logic for monitoring
        $proposals = [
            [
                'id' => 1201,
                'nama' => 'Pengembangan Kurikulum Berbasis Industri',
                'pengusul' => 'Dr. Ir. Heru Santoso',
                'nim' => '197508122000121001',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tahap_sekarang' => 'Dana Cair',
                'status' => 'In Process'
            ],
            [
                'id' => 1202,
                'nama' => 'Pembangunan Smart Classroom',
                'pengusul' => 'Dra. Maria Ulfa, M.Hum',
                'nim' => '198005152005012002',
                'prodi' => 'Administrasi Niaga',
                'jurusan' => 'Administrasi Niaga',
                'tahap_sekarang' => 'LPJ',
                'status' => 'Approved'
            ],
            [
                'id' => 1203,
                'nama' => 'Workshop UI/UX Design Modern',
                'pengusul' => 'Rizky Pratama',
                'nim' => '2407411050',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tahap_sekarang' => 'Review',
                'status' => 'In Process'
            ],
            [
                'id' => 1204,
                'nama' => 'Seminar Internasional Blockchain',
                'pengusul' => 'Ahmad Fauzi',
                'nim' => '2407411052',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tahap_sekarang' => 'Selesai',
                'status' => 'Approved'
            ],
            [
                'id' => 1205,
                'nama' => 'Lomba Karya Tulis Ilmiah Nasional',
                'pengusul' => 'Dewi Lestari',
                'nim' => '2407411051',
                'prodi' => 'Akuntansi',
                'jurusan' => 'Akuntansi',
                'tahap_sekarang' => 'Verifikasi',
                'status' => 'In Process'
            ],
        ];

        // Filter logic (mock)
        $status = $request->status;
        $jurusan = $request->jurusan;
        $search = $request->search;

        if ($status && $status !== 'semua') {
            $proposals = array_filter($proposals, fn($p) => strtolower($p['status']) === $status);
        }

        return response()->json([
            'proposals' => array_values($proposals),
            'pagination' => [
                'totalItems' => count($proposals),
                'totalPages' => 1,
                'currentPage' => 1,
                'showingFrom' => 1,
                'showingTo' => count($proposals)
            ]
        ]);
    }
}
