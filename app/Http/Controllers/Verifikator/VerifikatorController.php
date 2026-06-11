<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;

class VerifikatorController extends Controller
{
    public function dashboard()
    {
        $kegiatanService = new KegiatanService;
        $statsData = $kegiatanService->getDashboardStats();
        $stats = [
            'total' => $statsData['total'] ?? 0,
            'disetujui' => $statsData['disetujui'] ?? 0,
            'ditolak' => $statsData['ditolak'] ?? 0,
            'pending' => $statsData['menunggu'] ?? 0,
        ];

        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->where('posisi_id', '>=', WorkflowService::POSITION_VERIFIKATOR)
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

        $jurusan_list = [
            'Teknik Informatika dan Komputer',
            'Teknik Grafika dan Penerbitan',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Administrasi Niaga',
            'Akuntansi',
        ];

        return view('verifikator.dashboard', compact('stats', 'list_usulan', 'jurusan_list'));
    }
}
