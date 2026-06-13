<?php

namespace App\Http\Controllers\API\Wadir;

use App\Http\Controllers\Controller;
use App\Models\ProgressHistory;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->user_id;

        $historyQuery = ProgressHistory::with(['kegiatan.user', 'status'])
            ->when($userId, fn ($q) => $q->where('changed_by_user_id', $userId))
            ->whereHas('kegiatan', function ($q) {
                $q->where('posisi_id', '>', WorkflowService::POSITION_WADIR)
                    ->orWhereIn('status_utama_id', [
                        WorkflowService::STATUS_DANA_DIBERIKAN,
                        WorkflowService::STATUS_LPJ_DISETUJUI,
                        WorkflowService::STATUS_SELESAI,
                        WorkflowService::STATUS_DANA_DIBERIKAN_SEBAGIAN,
                    ]);
            })
            ->latest('created_at')
            ->get()
            ->groupBy('kegiatan_id')
            ->map(function ($items) {
                return $items->first();
            });

        $list_riwayat = $historyQuery->map(function ($history) {
            $kegiatan = $history->kegiatan;

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tgl' => $history->created_at ? $history->created_at->format('Y-m-d') : null,
                'status' => 'Disetujui',
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $list_riwayat,
        ]);
    }
}
