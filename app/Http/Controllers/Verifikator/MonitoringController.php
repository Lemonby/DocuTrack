<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
        
        $kegiatanList = \App\Models\Kegiatan::with(['statusUtama', 'user'])
            ->latest()
            ->get();

        $list_proposal = $kegiatanList->map(function ($kegiatan) {
            $tahap = 'Pengajuan';
            if ($kegiatan->posisi_aktif == \App\Services\WorkflowService::POSITION_VERIFIKATOR) $tahap = 'Verifikasi';
            elseif ($kegiatan->posisi_aktif == \App\Services\WorkflowService::POSITION_WADIR) $tahap = 'ACC WD';
            elseif ($kegiatan->posisi_aktif == \App\Services\WorkflowService::POSITION_PPK) $tahap = 'ACC PPK';
            elseif ($kegiatan->posisi_aktif == \App\Services\WorkflowService::POSITION_BENDAHARA) $tahap = 'Dana Cair';

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tahap_sekarang' => $tahap,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'In Process'
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

        return view('verifikator.monitoring.index', compact('list_proposal', 'tahapan_all', 'jurusan_list'));
    }
}
