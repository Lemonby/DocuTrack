<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'per_jurusan' => Kegiatan::selectRaw('jurusan_penyelenggara, COUNT(*) as total, SUM(CASE WHEN status_utama_id = 5 THEN 1 ELSE 0 END) as disetujui')
                    ->groupBy('jurusan_penyelenggara')->get(),
                'per_status' => Kegiatan::selectRaw('status_utama_id, COUNT(*) as total')
                    ->groupBy('status_utama_id')->get(),
                'dana_per_jurusan' => Kegiatan::selectRaw('jurusan_penyelenggara, SUM(jumlah_dicairkan) as total_dicairkan')
                    ->whereNotNull('jumlah_dicairkan')
                    ->groupBy('jurusan_penyelenggara')->get(),
            ],
        ]);
    }
}
