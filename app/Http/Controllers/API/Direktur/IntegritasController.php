<?php

namespace App\Http\Controllers\API\Direktur;

use App\Http\Controllers\Controller;
use App\Services\SpkMautService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegritasController extends Controller
{
    protected SpkMautService $spkService;

    public function __construct(SpkMautService $spkService)
    {
        $this->spkService = $spkService;
    }

    public function index(Request $request): JsonResponse
    {
        $rankings = $this->spkService->getJurusanRankings();

        $selectedJurusanName = $request->query('jurusan');
        if (!$selectedJurusanName && $rankings->isNotEmpty()) {
            $selectedJurusanName = $rankings->first()['jurusan'];
        }

        $selectedRankData = $rankings->firstWhere('jurusan', $selectedJurusanName);
        $selectedKegiatans = $selectedRankData ? $selectedRankData['kegiatans'] : collect();

        return response()->json([
            'success' => true,
            'data' => [
                'rankings' => $rankings,
                'selected_jurusan' => $selectedJurusanName,
                'selected_rank_data' => $selectedRankData,
                'selected_kegiatans' => $selectedKegiatans,
            ]
        ]);
    }
}
