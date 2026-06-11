<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Services\WorkflowService;

class MonitoringController extends Controller
{
    public function index()
    {
        $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC PPK', 'ACC WD', 'Dana Cair', 'LPJ'];

        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->latest()
            ->get();

        $list_proposal = $kegiatanList->map(function ($kegiatan) {
            $tahap = 'Pengajuan';
            if ($kegiatan->status_utama_id == WorkflowService::STATUS_SELESAI || $kegiatan->status_utama_id == WorkflowService::STATUS_LPJ_DISETUJUI) {
                $tahap = 'LPJ';
            } elseif ($kegiatan->status_utama_id == WorkflowService::STATUS_DANA_DIBERIKAN) {
                $tahap = 'Dana Cair';
            } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_BENDAHARA) {
                $tahap = 'Dana Cair';
            } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_WADIR) {
                $tahap = 'ACC WD';
            } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_PPK) {
                $tahap = 'ACC PPK';
            } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_VERIFIKATOR) {
                $tahap = 'Verifikasi';
            }

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tahap_sekarang' => $tahap,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'In Process',
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

        return view('ppk.monitoring.index', compact('list_proposal', 'tahapan_all', 'jurusan_list'));
    }
}
