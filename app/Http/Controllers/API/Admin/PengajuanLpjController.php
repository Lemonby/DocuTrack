<?php

namespace App\Http\Controllers\API\Admin;

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

    public function index(Request $request): JsonResponse
    {
        $jurusan = $request->user()->nama_jurusan;

        $lpjs = Lpj::with(['kegiatan', 'status', 'items'])
            ->whereHas('kegiatan', function ($q) use ($jurusan) {
                if ($jurusan) {
                    $q->where('jurusan_penyelenggara', $jurusan);
                }
            })
            ->latest('lpj_id')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => LpjResource::collection($lpjs),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $lpj = Lpj::with(['kegiatan', 'status', 'items.kategori'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new LpjResource($lpj),
        ]);
    }

    public function uploadBukti(Request $request): JsonResponse
    {
        $request->validate([
            'lpj_id' => 'required|integer|exists:lpjs,lpj_id',
            'rab_item_id' => 'required|integer|exists:rabs,rab_item_id',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $item = $this->lpjService->uploadBukti(
            $request->input('lpj_id'),
            $request->input('rab_item_id'),
            $request->file('file')
        );

        return response()->json([
            'success' => true,
            'message' => 'Bukti berhasil diupload.',
            'data' => ['file_bukti' => $item->file_bukti],
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'kegiatan_id' => 'required|integer|exists:kegiatans,kegiatan_id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.realisasi' => 'required|numeric|min:0',
        ]);

        $lpj = $this->lpjService->submitLpj(
            $request->input('kegiatan_id'),
            $request->input('items')
        );

        return response()->json([
            'success' => true,
            'message' => 'LPJ berhasil diajukan ke Bendahara.',
            'data' => new LpjResource($lpj),
        ]);
    }
}
