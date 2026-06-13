<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Services\WorkflowService;

class RiwayatController extends Controller
{
    public function index()
    {
        $kegiatanList = Kegiatan::with(['statusUtama', 'user', 'progressHistories.revisiComments', 'progressHistories.changedBy'])
            ->where(function ($q) {
                // Approved by PPK: currently beyond PPK desk
                $q->where('posisi_id', '>', WorkflowService::POSITION_PPK)
                // Or has reached final/cair stages (posisi_id might be 1 but status is 5, 6, 8, or 9)
                  ->orWhereIn('status_utama_id', [
                      WorkflowService::STATUS_DANA_DIBERIKAN,
                      WorkflowService::STATUS_LPJ_DISETUJUI,
                      WorkflowService::STATUS_SELESAI,
                      WorkflowService::STATUS_DANA_DIBERIKAN_SEBAGIAN,
                  ]);
            })
            ->latest()
            ->get();

        $riwayat_list = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = 'Disetujui';

            // Find the comment left by PPK during approval
            $catatan = 'Usulan disetujui.';
            $ppkHistory = $kegiatan->progressHistories->filter(function ($h) {
                return $h->changedBy && (
                    $h->changedBy->nama === 'PPK' ||
                    (method_exists($h->changedBy, 'hasRole') && $h->changedBy->hasRole('PPK'))
                );
            })->sortByDesc('created_at')->first();

            if ($ppkHistory) {
                $comment = $ppkHistory->revisiComments->first();
                if ($comment && ! empty($comment->komentar_revisi)) {
                    $catatan = $comment->komentar_revisi;
                }
            }

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'tanggal_proses' => $kegiatan->updated_at ? $kegiatan->updated_at->format('Y-m-d') : null,
                'status' => $statusLabel,
                'catatan' => $catatan,
            ];
        })->toArray();

        return view('ppk.riwayat.index', compact('riwayat_list'));
    }
}
