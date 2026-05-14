<?php

namespace App\Http\Controllers\API\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;

class RiwayatController extends Controller
{
    public function index(): JsonResponse
    {
        $kegiatans = Kegiatan::with(['statusUtama', 'user', 'tahapanPencairans'])
            ->withStatus(WorkflowService::STATUS_DANA_DIBERIKAN)
            ->latest()
            ->paginate(15);

        return response()->json(['success' => true, 'data' => KegiatanResource::collection($kegiatans)]);
    }

    public function show(int $id): JsonResponse
    {
        $kegiatan = (new KegiatanService)->getDetailLengkap($id);
        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\KegiatanDetailResource($kegiatan),
        ]);
    }
}
