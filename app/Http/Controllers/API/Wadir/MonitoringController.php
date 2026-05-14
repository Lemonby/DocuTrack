<?php

namespace App\Http\Controllers\API\Wadir;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use Illuminate\Http\JsonResponse;

class MonitoringController extends Controller
{
    public function index(): JsonResponse
    {
        $data = [
            'per_jurusan' => Kegiatan::selectRaw('jurusan_penyelenggara, COUNT(*) as total')
                ->groupBy('jurusan_penyelenggara')->get(),
            'per_status' => Kegiatan::selectRaw('status_utama_id, COUNT(*) as total')
                ->groupBy('status_utama_id')->with('statusUtama')->get(),
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }
}
