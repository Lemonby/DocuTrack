<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly KegiatanService $kegiatanService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $jurusan = $request->user()->nama_jurusan;

        $stats = $this->kegiatanService->getDashboardStats($jurusan);

        $recentKegiatan = Kegiatan::with(['statusUtama', 'user'])
            ->when($jurusan, fn ($q) => $q->byJurusan($jurusan))
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_kegiatan' => KegiatanResource::collection($recentKegiatan),
            ],
        ]);
    }
}
