<?php

namespace App\Http\Controllers\API\Direktur;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Models\Iku;
use App\Models\Jurusan;
use App\Models\Kegiatan;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalAllocated = 500000000; // Example Pagu
        $totalRealized = Kegiatan::whereNotNull('jumlah_dicairkan')->sum('jumlah_dicairkan');
        $percentage = $totalAllocated > 0 ? round(($totalRealized / $totalAllocated) * 100, 1) : 0;

        $stats = [
            'total_kegiatan' => Kegiatan::count(),
            'total_disetujui' => Kegiatan::whereIn('status_utama_id', [5, 6, 8])->count(),
            'total_ditolak' => Kegiatan::where('status_utama_id', 4)->count(),
            'total_menunggu' => Kegiatan::whereIn('status_utama_id', [1, 2, 7])->count(),
        ];

        $ikuAchievements = Iku::all()->map(function ($iku) {
            return [
                'nama' => $iku->indikator_kinerja,
                'target' => $iku->target,
                'capaian' => $iku->realisasi ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total' => $stats['total_kegiatan'],
                    'disetujui' => $stats['total_disetujui'],
                    'ditolak' => $stats['total_ditolak'],
                    'menunggu' => $stats['total_menunggu'],
                ],
                'list_jurusan' => Jurusan::pluck('nama_jurusan'),
                'budget' => [
                    'total_realized' => (float) $totalRealized,
                    'total_allocated' => (float) $totalAllocated,
                    'remaining' => (float) ($totalAllocated - $totalRealized),
                    'percentage' => $percentage,
                ],
                'iku_achievements' => $ikuAchievements,
                'recent_kegiatan' => KegiatanResource::collection(
                    Kegiatan::with(['statusUtama', 'user'])->latest()->take(5)->get()
                ),
            ],
        ]);
    }

    public function usulanPerJurusan(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Kegiatan::selectRaw('jurusan_penyelenggara, COUNT(*) as total')
                ->groupBy('jurusan_penyelenggara')->get(),
        ]);
    }

    public function danaPerJurusan(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Kegiatan::selectRaw('jurusan_penyelenggara, SUM(jumlah_dicairkan) as total_dicairkan')
                ->whereNotNull('jumlah_dicairkan')
                ->groupBy('jurusan_penyelenggara')->get(),
        ]);
    }

    public function pengajuanTerbaru(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => KegiatanResource::collection(
                Kegiatan::with(['statusUtama', 'user'])->latest()->take(10)->get()
            ),
        ]);
    }
}
