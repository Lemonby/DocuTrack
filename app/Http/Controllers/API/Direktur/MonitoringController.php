<?php

namespace App\Http\Controllers\API\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\JsonResponse;

class MonitoringController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'per_jurusan' => Kegiatan::selectRaw('jurusan_penyelenggara, COUNT(*) as total, SUM(CASE WHEN status_utama_id = 5 THEN 1 ELSE 0 END) as disetujui')
                    ->groupBy('jurusan_penyelenggara')->get(),
                'per_bulan' => Kegiatan::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
                    ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
                    ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m') ASC")->get(),
            ],
        ]);
    }
}
