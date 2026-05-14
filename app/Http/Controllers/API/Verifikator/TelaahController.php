<?php

namespace App\Http\Controllers\API\Verifikator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Verifikator\ApproveRequest;
use App\Http\Requests\Workflow\RejectRequest;
use App\Http\Requests\Workflow\ReviseRequest;
use App\Http\Resources\KegiatanDetailResource;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Services\ActivityLogService;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;

class TelaahController extends Controller
{
    public function __construct(
        private readonly WorkflowService $workflowService,
        private readonly KegiatanService $kegiatanService,
        private readonly ActivityLogService $activityLog
    ) {}

    public function index(): JsonResponse
    {
        $kegiatans = Kegiatan::with(['statusUtama', 'user'])
            ->atPosition(WorkflowService::POSITION_VERIFIKATOR)
            ->withStatus(WorkflowService::STATUS_MENUNGGU)
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

    public function approve(ApproveRequest $request, int $id): JsonResponse
    {
        $this->workflowService->moveToNextPosition(
            $id,
            WorkflowService::POSITION_VERIFIKATOR,
            WorkflowService::STATUS_DISETUJUI,
            [
                'kode_mak' => $request->validated('kode_mak'),
                'dana_disetujui' => $request->validated('dana_disetujui'),
                'umpan_balik' => $request->validated('catatan'),
            ]
        );

        $this->activityLog->createNotification(
            Kegiatan::find($id)->user_id,
            'APPROVAL',
            "Proposal telah disetujui Verifikator dan diteruskan ke PPK.",
            $id
        );

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil disetujui dan diteruskan ke PPK.',
        ]);
    }

    public function reject(RejectRequest $request, int $id): JsonResponse
    {
        $this->workflowService->reject(
            $id,
            WorkflowService::POSITION_VERIFIKATOR,
            $request->validated('alasan')
        );

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil ditolak.',
        ]);
    }

    public function revise(ReviseRequest $request, int $id): JsonResponse
    {
        $this->workflowService->requestRevision(
            $id,
            WorkflowService::POSITION_VERIFIKATOR,
            $request->validated('komentar'),
            $request->validated('field_comments', [])
        );

        return response()->json([
            'success' => true,
            'message' => 'Usulan dikembalikan untuk revisi.',
        ]);
    }
}
