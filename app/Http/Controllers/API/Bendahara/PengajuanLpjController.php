<?php

namespace App\Http\Controllers\API\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Resources\LpjResource;
use App\Models\Lpj;
use App\Services\LpjService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanLpjController extends Controller
{
    public function __construct(
        private readonly LpjService $lpjService
    ) {}

    public function index(): JsonResponse
    {
        $lpjs = Lpj::with(['kegiatan', 'status', 'items'])
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => LpjResource::collection($lpjs)]);
    }

    public function show(int $id): JsonResponse
    {
        $lpj = Lpj::with(['kegiatan', 'status', 'items.kategori'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => new LpjResource($lpj)]);
    }

    /**
     * Process LPJ: verify, reject, or request revision.
     */
    public function proses(Request $request): JsonResponse
    {
        $request->validate([
            'lpj_id' => 'required|integer|exists:lpjs,lpj_id',
            'aksi' => 'required|in:verify,reject,revise',
            'komentar' => 'required_unless:aksi,verify|string',
        ]);

        $lpj = match ($request->input('aksi')) {
            'verify' => $this->lpjService->verifikasi($request->input('lpj_id')),
            'reject' => $this->lpjService->tolak($request->input('lpj_id'), $request->input('komentar')),
            'revise' => $this->lpjService->requestRevision($request->input('lpj_id'), $request->input('komentar')),
        };

        return response()->json([
            'success' => true,
            'message' => 'LPJ berhasil diproses.',
            'data' => new LpjResource($lpj->fresh(['status', 'items'])),
        ]);
    }
}
