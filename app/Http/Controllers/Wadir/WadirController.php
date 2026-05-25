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
            ->atPosition(WorkflowService::POSITION_WADIR)
            ->latest()
            ->get();

        $list_usulan = $kegiatanList->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->created_at ? $kegiatan->created_at->format('Y-m-d') : null,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu',
            ];
        })->toArray();

        $statsQuery = Kegiatan::query()->atPosition(WorkflowService::POSITION_WADIR);
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'disetujui' => (clone $statsQuery)->withStatus(WorkflowService::STATUS_DISETUJUI)->count(),
            'menunggu' => (clone $statsQuery)->withStatus(WorkflowService::STATUS_MENUNGGU)->count(),
        ];

        $jurusan_list = Jurusan::orderBy('nama_jurusan')->pluck('nama_jurusan')->toArray();
        
        return view('wadir.dashboard', compact('stats', 'list_usulan', 'jurusan_list'));
    }
}
