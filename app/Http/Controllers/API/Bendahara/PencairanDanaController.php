<?php

namespace App\Http\Controllers\API\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\CairkanDanaRequest;
use App\Http\Resources\KegiatanDetailResource;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\PencairanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;

class PencairanDanaController extends Controller
{
    public function __construct(
        private readonly PencairanService $pencairanService,
        private readonly KegiatanService $kegiatanService
    ) {}

    public function index(): JsonResponse
    {
        $kegiatans = Kegiatan::with(['statusUtama', 'user', 'kak'])
            ->atPosition(WorkflowService::POSITION_BENDAHARA)
            ->withStatus(WorkflowService::STATUS_DISETUJUI)
            ->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => KegiatanResource::collection($kegiatans),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new KegiatanDetailResource($this->kegiatanService->getDetailLengkap($id)),
        ]);
    }

    public function proses(CairkanDanaRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $kegiatan = $this->pencairanService->cairkanDana(
            $validated['kegiatan_id'],
            $validated,
            $request->user()->user_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Dana berhasil dicairkan.',
            'data' => new KegiatanDetailResource($kegiatan->load(['statusUtama', 'tahapanPencairans'])),
        ]);
    }
}
