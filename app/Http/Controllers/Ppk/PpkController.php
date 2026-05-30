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
        $stats = (new \App\Services\KegiatanService())->getDashboardStats();

        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->where(function ($q) {
                $q->where('posisi_id', '>=', WorkflowService::POSITION_PPK)
                  ->orWhereIn('status_utama_id', [
                      WorkflowService::STATUS_DANA_DIBERIKAN,
                      WorkflowService::STATUS_LPJ_DISETUJUI,
                      WorkflowService::STATUS_SELESAI
                  ]);
            })
            ->latest()
            ->get();

        $list_usulan = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = 'Menunggu';
            if ($kegiatan->posisi_id > WorkflowService::POSITION_PPK || 
                in_array($kegiatan->status_utama_id, [
                    WorkflowService::STATUS_DANA_DIBERIKAN,
                    WorkflowService::STATUS_LPJ_DISETUJUI,
                    WorkflowService::STATUS_SELESAI
                ])) {
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
