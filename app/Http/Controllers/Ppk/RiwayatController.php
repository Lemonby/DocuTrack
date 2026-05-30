<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $kegiatanList = \App\Models\Kegiatan::with(['statusUtama', 'user', 'progressHistories.revisiComments'])
            ->where(function($q) {
                // Approved / passed PPK
                $q->where('posisi_id', '>', \App\Services\WorkflowService::POSITION_PPK)
                  // Or completed (approved and finished back at position 1)
                  ->orWhere(function ($q2) {
                      $q2->where('status_utama_id', \App\Services\WorkflowService::STATUS_SELESAI)
                         ->where('posisi_id', \App\Services\WorkflowService::POSITION_ADMIN);
                  })
                  // Or rejected at PPK desk
                  ->orWhere(function($q2) {
                      $q2->where('posisi_id', \App\Services\WorkflowService::POSITION_PPK)
                         ->where('status_utama_id', \App\Services\WorkflowService::STATUS_DITOLAK);
                  })
                  // Or if it was revised at PPK desk (status is STATUS_REVISI)
                  ->orWhere(function($q2) {
                      $q2->where('status_utama_id', \App\Services\WorkflowService::STATUS_REVISI);
                  });
            })
            ->latest()
            ->get();

        $riwayat_list = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = 'Disetujui';
            if ($kegiatan->status_utama_id == \App\Services\WorkflowService::STATUS_DITOLAK) {
                $statusLabel = 'Ditolak';
            } elseif ($kegiatan->status_utama_id == \App\Services\WorkflowService::STATUS_REVISI) {
                $statusLabel = 'Revisi';
            }

            // Find the latest comment left generally or in revision/rejection history
            $catatan = 'Tanpa catatan';
            $history = $kegiatan->progressHistories->sortByDesc('created_at')->first();
            if ($history) {
                $comment = $history->revisiComments->first();
                if ($comment && !empty($comment->komentar_revisi)) {
                    $catatan = $comment->komentar_revisi;
                } else {
                    $catatan = $statusLabel === 'Disetujui' ? 'Usulan disetujui.' : 'Tanpa catatan';
                }
            } else {
                $catatan = $statusLabel === 'Disetujui' ? 'Usulan disetujui.' : 'Tanpa catatan';
            }

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'tanggal_proses' => $kegiatan->updated_at ? $kegiatan->updated_at->format('Y-m-d') : null,
                'status' => $statusLabel,
                'catatan' => $catatan
            ];
        })->toArray();

        return view('ppk.riwayat.index', compact('riwayat_list'));
    }
}
