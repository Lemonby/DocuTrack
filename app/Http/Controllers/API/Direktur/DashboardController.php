<?php

namespace App\Http\Controllers\API\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_kegiatan' => Kegiatan::count(),
                'total_disetujui' => Kegiatan::withStatus(5)->count(),
                'total_ditolak' => Kegiatan::withStatus(4)->count(),
                'total_menunggu' => Kegiatan::whereNotIn('status_utama_id', [3, 4, 5])->count(),
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
            'data' => \App\Http\Resources\KegiatanResource::collection(
                Kegiatan::with(['statusUtama', 'user'])->latest()->take(10)->get()
            ),
        ]);
    }
}
