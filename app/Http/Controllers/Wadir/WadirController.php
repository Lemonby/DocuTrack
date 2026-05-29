<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kegiatan;
use App\Services\WorkflowService;

class WadirController extends Controller
{
    public function dashboard()
    {
        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->where(function ($q) {
                $q->where('posisi_id', '>=', WorkflowService::POSITION_WADIR)
                  ->orWhere('status_utama_id', WorkflowService::STATUS_DANA_DIBERIKAN);
            })
            ->latest()
            ->get();

        $list_usulan = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu';
            if ($kegiatan->posisi_id > WorkflowService::POSITION_WADIR || $kegiatan->status_utama_id === WorkflowService::STATUS_DANA_DIBERIKAN) {
                $statusLabel = 'Disetujui';
            }

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

        $stats = (new \App\Services\KegiatanService())->getDashboardStats();

        $jurusan_list = Jurusan::orderBy('nama_jurusan')->pluck('nama_jurusan')->toArray();
        
        return view('wadir.dashboard', compact('stats', 'list_usulan', 'jurusan_list'));
    }
}
