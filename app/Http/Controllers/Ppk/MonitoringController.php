<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
        $list_proposal = [
            [
                'id' => 1,
                'nama' => 'Penyediaan Alat Lab Komputer',
                'pengusul' => 'Andi Wijaya',
                'tahap_sekarang' => 'ACC WD',
                'status' => 'In Process'
            ],
            [
                'id' => 2,
                'nama' => 'Pelatihan Jaringan CISCO',
                'pengusul' => 'Budi Santoso',
                'tahap_sekarang' => 'Dana Cair',
                'status' => 'In Process'
            ],
            [
                'id' => 3,
                'nama' => 'Seminar Cyber Security',
                'pengusul' => 'Siti Aminah',
                'tahap_sekarang' => 'ACC PPK',
                'status' => 'In Process'
            ],
            [
                'id' => 4,
                'nama' => 'Workshop Multimedia',
                'pengusul' => 'Ahmad Fauzi',
                'tahap_sekarang' => 'LPJ',
                'status' => 'Approved'
            ],
        ]; 
        return view('ppk.monitoring.index', compact('list_proposal', 'tahapan_all'));
    }
}
