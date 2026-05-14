<?php

namespace App\Http\Controllers\API\Verifikator;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Models\Kegiatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $kegiatans = Kegiatan::with(['statusUtama', 'user'])
            ->whereHas('progressHistories', function ($q) {
                $q->whereHas('changedBy', function ($q2) {
                    $q2->role('Verifikator');
                });
            })
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => KegiatanResource::collection($kegiatans),
        ]);
    }
}
