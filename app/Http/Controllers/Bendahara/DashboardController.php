<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Services\KegiatanService;
use App\Services\WorkflowService;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = (new KegiatanService)->getBendaharaDashboardStats();

        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->whereIn('posisi_id', [WorkflowService::POSITION_ADMIN, WorkflowService::POSITION_BENDAHARA])
            ->whereIn('status_utama_id', [WorkflowService::STATUS_DANA_DIBERIKAN, WorkflowService::STATUS_DANA_DIBERIKAN_SEBAGIAN])
            ->latest()
            ->get();

        $list_kak = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = match ($kegiatan->status_utama_id) {
                WorkflowService::STATUS_DANA_DIBERIKAN => 'Sudah Dicairkan',
                WorkflowService::STATUS_DANA_DIBERIKAN_SEBAGIAN => 'Sudah Dicairkan Sebagian',
                default => 'Belum Dicairkan',
            };

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->created_at ? $kegiatan->created_at->format('Y-m-d') : null,
                'status' => $statusLabel,
            ];
        })->toArray();

        $lpjList = Lpj::with(['kegiatan.user'])
            ->whereNotNull('submitted_at')
            ->where('status_id', 1)
            ->latest('submitted_at')
            ->get();

        $list_lpj = $lpjList->map(function ($lpj) {
            $statusLabel = $this->mapLpjStatusLabel($lpj);

            return [
                'id' => $lpj->kegiatan_id,
                'nama' => 'LPJ - '.($lpj->kegiatan->nama_kegiatan ?? 'Kegiatan'),
                'pengusul' => $lpj->kegiatan->user->nama ?? $lpj->kegiatan->pemilik_kegiatan,
                'nim' => $lpj->kegiatan->nim_pelaksana,
                'prodi' => $lpj->kegiatan->prodi_penyelenggara,
                'jurusan' => $lpj->kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $lpj->submitted_at ? $lpj->submitted_at->format('Y-m-d') : null,
                'status' => $statusLabel,
            ];
        })->toArray();

        return view('bendahara.dashboard', compact('stats', 'list_kak', 'list_lpj'));
    }

    private function mapLpjStatusLabel(Lpj $lpj): string
    {
        return match ((int) $lpj->status_id) {
            2 => 'Revisi',
            3 => 'Disetujui',
            4 => 'Ditolak',
            default => $lpj->komentar_revisi ? 'Telah Direvisi' : 'Menunggu Verifikasi',
        };
    }
}
