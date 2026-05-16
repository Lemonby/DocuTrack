<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PpkController extends Controller
{
    public function dashboard()
    {
        $list_usulan = [
            [
                'id' => 901,
                'nama' => 'Workshop UI/UX Design 2026',
                'pengusul' => 'Siti Aminah',
                'nim' => '2407411059',
                'prodi' => 'D4 Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-14',
                'status' => 'Menunggu'
            ],
            [
                'id' => 902,
                'nama' => 'Seminar Internasional Blockchain',
                'pengusul' => 'Ahmad Fauzi',
                'nim' => '2407411052',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Menunggu'
            ],
            [
                'id' => 903,
                'nama' => 'Peningkatan Kompetensi AI',
                'pengusul' => 'Budi Santoso',
                'nim' => '2407411003',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Disetujui'
            ],
        ];

        $stats = [
            'total' => count($list_usulan),
            'disetujui' => 1,
            'menunggu' => 2,
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
        
        return view('ppk.dashboard', compact('stats', 'list_usulan', 'jurusan_list'));
    }
}
