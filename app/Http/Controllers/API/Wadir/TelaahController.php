<?php

namespace App\Http\Controllers\API\Wadir;

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

class TelaahController extends Controller
{
    public function __construct(
        private readonly WorkflowService $workflowService,
        private readonly KegiatanService $kegiatanService
    ) {}

    public function index(): JsonResponse
    {
        $kegiatans = Kegiatan::with(['statusUtama', 'user'])
            ->atPosition(WorkflowService::POSITION_WADIR)
            ->withStatus(WorkflowService::STATUS_MENUNGGU)
            ->latest()->paginate(15);

        return response()->json(['success' => true, 'data' => KegiatanResource::collection($kegiatans)]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new KegiatanDetailResource($this->kegiatanService->getDetailLengkap($id)),
        ]);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $this->workflowService->moveToNextPosition($id, WorkflowService::POSITION_WADIR, WorkflowService::STATUS_DISETUJUI);
        return response()->json(['success' => true, 'message' => 'Disetujui Wadir, diteruskan ke Bendahara.']);
    }

    public function reject(RejectRequest $request, int $id): JsonResponse
    {
        $this->workflowService->reject($id, WorkflowService::POSITION_WADIR, $request->validated('alasan'));
        return response()->json(['success' => true, 'message' => 'Usulan ditolak Wadir.']);
    }

    public function revise(ReviseRequest $request, int $id): JsonResponse
    {
        $this->workflowService->requestRevision($id, WorkflowService::POSITION_WADIR, $request->validated('komentar'), $request->validated('field_comments', []));
        return response()->json(['success' => true, 'message' => 'Dikembalikan untuk revisi.']);
    }
}
