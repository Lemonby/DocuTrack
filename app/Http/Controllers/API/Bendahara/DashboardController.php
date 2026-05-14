<?php

namespace App\Http\Controllers\API\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = (new KegiatanService)->getDashboardStats();

        $antrian = Kegiatan::with(['statusUtama', 'user'])
            ->atPosition(WorkflowService::POSITION_BENDAHARA)
            ->withStatus(WorkflowService::STATUS_DISETUJUI)
            ->latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'data' => ['stats' => $stats, 'antrian_pencairan' => KegiatanResource::collection($antrian)],
        ]);
    }
}
