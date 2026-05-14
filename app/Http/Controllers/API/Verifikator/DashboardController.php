<?php

namespace App\Http\Controllers\API\Verifikator;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly KegiatanService $kegiatanService
    ) {}

    public function index(): JsonResponse
    {
        $stats = $this->kegiatanService->getDashboardStats();

        $pending = Kegiatan::with(['statusUtama', 'user'])
            ->atPosition(WorkflowService::POSITION_VERIFIKATOR)
            ->withStatus(WorkflowService::STATUS_MENUNGGU)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'pending_telaah' => KegiatanResource::collection($pending),
            ],
        ]);
    }
}
