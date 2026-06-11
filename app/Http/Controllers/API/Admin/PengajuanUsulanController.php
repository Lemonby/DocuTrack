<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsulanRequest;
use App\Http\Resources\KegiatanDetailResource;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Models\ProgressHistory;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanUsulanController extends Controller
{
    public function __construct(
        private readonly KegiatanService $kegiatanService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $jurusan = $request->user()->nama_jurusan;

        $kegiatans = Kegiatan::with(['statusUtama', 'user'])
            ->when($jurusan, fn ($q) => $q->byJurusan($jurusan))
            ->atPosition(WorkflowService::POSITION_VERIFIKATOR)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => KegiatanResource::collection($kegiatans),
            'meta' => ['total' => $kegiatans->total()],
        ]);
    }

    public function store(StoreUsulanRequest $request): JsonResponse
    {
        $kegiatan = $this->kegiatanService->createKegiatan(
            $request->validated(),
            $request->user()->user_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dibuat.',
            'data' => new KegiatanDetailResource($kegiatan),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $kegiatan = $this->kegiatanService->getDetailLengkap($id);

        return response()->json([
            'success' => true,
            'data' => new KegiatanDetailResource($kegiatan),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $kegiatan = Kegiatan::findOrFail($id);

        if ($kegiatan->status_utama_id >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan yang sudah diproses tidak dapat dihapus.',
            ], 422);
        }

        $kegiatan->delete();

        return response()->json(['success' => true, 'message' => 'Usulan berhasil dihapus.']);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $kegiatan = $this->kegiatanService->updateKegiatan(
                $id,
                $request->all(),
                $request->user()->user_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Revisi usulan berhasil disimpan dan diajukan ulang.',
                'data' => new KegiatanDetailResource($kegiatan),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui usulan: '.$e->getMessage(),
            ], 422);
        }
    }

    public function selesai(Request $request, int $id): JsonResponse
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->update([
            'status_utama_id' => 8, // Selesai
        ]);

        // Add progress history entry for status 8 (Selesai)
        ProgressHistory::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'status_id' => 8,
            'changed_by_user_id' => $request->user()->user_id,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan telah berhasil dinyatakan Selesai!',
            'data' => new KegiatanDetailResource($kegiatan->fresh()),
        ]);
    }
}
