<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KakResource;
use App\Http\Resources\KegiatanDetailResource;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetailKakController extends Controller
{
    public function __construct(
        private readonly KegiatanService $kegiatanService
    ) {}

    public function show(int $id): JsonResponse
    {
        $kegiatan = $this->kegiatanService->getDetailLengkap($id);

        return response()->json([
            'success' => true,
            'data' => new KegiatanDetailResource($kegiatan),
        ]);
    }

    /**
     * Resubmit after revision.
     */
    public function resubmit(Request $request, int $id): JsonResponse
    {
        $kegiatan = Kegiatan::findOrFail($id);

        if ((int) $kegiatan->status_utama_id !== WorkflowService::STATUS_REVISI) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya kegiatan berstatus Revisi yang dapat di-resubmit.',
            ], 422);
        }

        $kegiatan->update([
            'posisi_id' => WorkflowService::POSITION_VERIFIKATOR,
            'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil di-resubmit.',
            'data' => new KegiatanDetailResource($kegiatan->fresh()->load(['statusUtama', 'user', 'kak'])),
        ]);
    }
}
