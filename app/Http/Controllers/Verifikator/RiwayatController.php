<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Services\WorkflowService;

class RiwayatController extends Controller
{
    public function index()
    {
        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->where(function ($q) {
                $q->where('posisi_id', '>', WorkflowService::POSITION_VERIFIKATOR)
                    ->orWhere(function ($q2) {
                        $q2->where('posisi_id', WorkflowService::POSITION_VERIFIKATOR)
                            ->where('status_utama_id', '!=', WorkflowService::STATUS_MENUNGGU);
                    })
                    ->orWhereIn('status_utama_id', [
                        WorkflowService::STATUS_DANA_DIBERIKAN,
                        WorkflowService::STATUS_LPJ_DISETUJUI,
                        WorkflowService::STATUS_SELESAI,
                        WorkflowService::STATUS_DANA_DIBERIKAN_SEBAGIAN,
                    ]);
            })
            ->latest()
            ->get();

        $list_riwayat = $kegiatanList->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_verifikasi' => $kegiatan->updated_at ? $kegiatan->updated_at->format('Y-m-d') : null,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'Disetujui',
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

        return view('verifikator.riwayat.index', compact('list_riwayat', 'jurusan_list'));
    }
}
