<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;

class PpkController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total' => Kegiatan::where(function ($q) {
                $q->where('posisi_id', '>=', WorkflowService::POSITION_PPK)
                  ->orWhere('status_utama_id', WorkflowService::STATUS_DANA_DIBERIKAN);
            })->count(),
            'disetujui' => Kegiatan::where(function ($q) {
                $q->where('posisi_id', '>', WorkflowService::POSITION_PPK)
                  ->orWhere('status_utama_id', WorkflowService::STATUS_DANA_DIBERIKAN);
            })->count(),
            'menunggu' => Kegiatan::atPosition(WorkflowService::POSITION_PPK)
                ->withStatus(WorkflowService::STATUS_MENUNGGU)
                ->count(),
        ];

        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->where(function ($q) {
                $q->where('posisi_id', '>=', WorkflowService::POSITION_PPK)
                  ->orWhere('status_utama_id', WorkflowService::STATUS_DANA_DIBERIKAN);
            })
            ->latest()
            ->take(5)
            ->get();

        $list_usulan = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu';
            if ($kegiatan->posisi_id > WorkflowService::POSITION_PPK || $kegiatan->status_utama_id === WorkflowService::STATUS_DANA_DIBERIKAN) {
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

        $jurusan_list = [
            'Teknik Informatika dan Komputer',
            'Teknik Grafika dan Penerbitan',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Administrasi Niaga',
            'Akuntansi',
        ];
        
        return view('ppk.dashboard', compact('stats', 'list_usulan', 'jurusan_list'));
    }
}
