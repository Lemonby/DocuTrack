<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubmitRincianRequest;
use App\Http\Resources\KegiatanDetailResource;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanKegiatanController extends Controller
{
    public function __construct(
        private readonly KegiatanService $kegiatanService
    ) {}

    /**
     * List kegiatan at Admin position (awaiting rincian submission).
     */
    public function index(Request $request): JsonResponse
    {
        $jurusan = $request->user()->nama_jurusan;

        $kegiatans = Kegiatan::with(['statusUtama', 'user'])
            ->when($jurusan, fn ($q) => $q->byJurusan($jurusan))
            ->atPosition(WorkflowService::POSITION_ADMIN)
            ->withStatus(WorkflowService::STATUS_DISETUJUI)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => KegiatanResource::collection($kegiatans),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $kegiatan = $this->kegiatanService->getDetailLengkap($id);

        return response()->json([
            'success' => true,
            'data' => new KegiatanDetailResource($kegiatan),
        ]);
    }

    public function submitRincian(SubmitRincianRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $kegiatan = $this->kegiatanService->submitRincian(
            $validated['kegiatan_id'],
            $validated,
            $request->file('surat_pengantar')
        );

        return response()->json([
            'success' => true,
            'message' => 'Rincian kegiatan berhasil disubmit.',
            'data' => new KegiatanDetailResource($kegiatan->load(['statusUtama', 'user'])),
        ]);
    }
}
