<?php

namespace App\Http\Controllers\Wadir;

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
        return view('wadir.monitoring.index', compact('list_jurusan'));
    }

    public function getData(Request $request)
    {
        // Mock data logic for monitoring
        $proposals = [
            [
                'id' => 301,
                'nama' => 'Peningkatan Kompetensi SDM melalui Sertifikasi IT',
                'nama_lengkap' => 'Ahmad Fauzi',
                'nim' => '2407411001',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tahap_sekarang' => 'ACC WD',
                'status' => 'In Process'
            ],
            [
                'id' => 302,
                'nama' => 'Penyediaan Laboratorium AI Terpadu',
                'nama_lengkap' => 'Siti Aminah',
                'nim' => '2407411002',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tahap_sekarang' => 'LPJ',
                'status' => 'Approved'
            ],
            [
                'id' => 303,
                'nama' => 'Seminar Internasional Digital Transformation',
                'nama_lengkap' => 'Budi Santoso',
                'nim' => '2407411003',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tahap_sekarang' => 'Verifikasi',
                'status' => 'Menunggu'
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
