<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat_list = [
            [
                'id' => 801,
                'nama' => 'Peralatan Robotika Semester Genap',
                'pengusul' => 'Randi Kurnia',
                'nim' => '2407411059',
                'tanggal_proses' => '2026-05-10',
                'status' => 'Disetujui',
                'catatan' => 'Usulan sesuai dengan kebutuhan laboratorium.'
            ],
            [
                'id' => 802,
                'nama' => 'Studi Ekskursi Industri IT',
                'pengusul' => 'Maya Sari',
                'nim' => '2407411052',
                'tanggal_proses' => '2026-05-12',
                'status' => 'Disetujui',
                'catatan' => 'Lanjutkan ke tahap verifikasi Wadir.'
            ],
            [
                'id' => 803,
                'nama' => 'Lomba Inovasi Mahasiswa',
                'pengusul' => 'Dedy Pratama',
                'nim' => '2407411003',
                'tanggal_proses' => '2026-05-13',
                'status' => 'Disetujui',
                'catatan' => 'Sesuai dengan pagu anggaran yang tersedia.'
            ],
        ];
        return view('ppk.riwayat.index', compact('riwayat_list'));
    }
}
