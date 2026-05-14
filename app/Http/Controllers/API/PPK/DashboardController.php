<?php

namespace App\Http\Controllers\API\PPK;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workflow\RejectRequest;
use App\Http\Requests\Workflow\ReviseRequest;
use App\Http\Resources\KegiatanDetailResource;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = (new KegiatanService)->getDashboardStats();
        $pending = Kegiatan::with(['statusUtama', 'user'])
            ->atPosition(WorkflowService::POSITION_PPK)
            ->withStatus(WorkflowService::STATUS_MENUNGGU)
            ->latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'data' => ['stats' => $stats, 'pending' => KegiatanResource::collection($pending)],
        ]);
    }
}
