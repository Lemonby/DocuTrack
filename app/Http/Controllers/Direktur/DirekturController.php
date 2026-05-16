<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DirekturController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total'     => 45,
            'disetujui' => 28,
            'revisi'    => 5,
            'ditolak'   => 2,
            'menunggu'  => 10,
        ];

        $budget = [
            'total_allocated' => 1500000000,
            'total_realized'  => 850000000,
            'remaining'       => 650000000,
            'percentage'      => 56.6,
        ];

        $iku_achievements = [
            ['nama' => 'Lulusan Bekerja Layak', 'target' => 80, 'capaian' => 72, 'status' => 'On Track'],
            ['nama' => 'Mahasiswa MBKM', 'target' => 20, 'capaian' => 18, 'status' => 'Warning'],
            ['nama' => 'Dosen Praktisi', 'target' => 15, 'capaian' => 16, 'status' => 'Exceeded'],
            ['nama' => 'Akreditasi Internasional', 'target' => 5, 'capaian' => 3, 'status' => 'On Track'],
        ];

        $approval_queue = [
            ['id' => 101, 'nama' => 'Pelatihan Cloud Computing', 'pengusul' => 'TIK', 'dana' => 45000000, 'prioritas' => 'High'],
            ['id' => 102, 'nama' => 'Seminar Literasi Digital', 'pengusul' => 'TGP', 'dana' => 15000000, 'prioritas' => 'Medium'],
            ['id' => 103, 'nama' => 'Lomba Inovasi Mahasiswa', 'pengusul' => 'Elektro', 'dana' => 25000000, 'prioritas' => 'Low'],
        ];

        $list_jurusan = [
            'TIK', 'TGP', 'Elektro', 'Mesin', 'Sipil', 'AN', 'Akuntansi',
        ];

        $monthly_trend = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [5, 12, 8, 15, 20, 18]
        ];

        return view('direktur.dashboard', compact(
            'stats', 
            'budget', 
            'iku_achievements', 
            'approval_queue', 
            'list_jurusan', 
            'monthly_trend'
        ));
    }

    public function getDanaPerJurusan()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['TIK', 'TGP', 'Elektro', 'Mesin', 'Sipil', 'AN', 'Akuntansi'],
                'data' => [175000000, 145000000, 160000000, 130000000, 140000000, 155000000, 150000000]
            ]
        ]);
    }
}
