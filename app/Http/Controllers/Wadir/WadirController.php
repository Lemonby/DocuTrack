<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WadirController extends Controller
{
    public function dashboard()
    {
        $pending = \App\Models\Kegiatan::where('posisi_id', 4)->where('status_utama_id', 1)->count();
        $disetujui = \App\Models\Kegiatan::where('posisi_id', '>', 4)->count();
        
        $stats = [
            'total' => $pending + $disetujui,
            'disetujui' => $disetujui,
            'menunggu' => $pending,
        ];

        $list_usulan = \App\Models\Kegiatan::where('posisi_id', 4)
            ->where('status_utama_id', 1)
            ->with('statusUtama')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'pengusul' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'nim' => $k->nim_pelaksana ?? $k->nip ?? '-',
                    'prodi' => $k->prodi_penyelenggara ?? '-',
                    'jurusan' => $k->jurusan_penyelenggara ?? '-',
                    'tanggal_pengajuan' => $k->created_at ? $k->created_at->format('Y-m-d') : '-',
                    'status' => $k->statusUtama ? $k->statusUtama->nama_status_usulan : 'Menunggu'
                ];
            })->toArray();

        $jurusan_list = \App\Models\Jurusan::pluck('nama_jurusan')->toArray();
        if (empty($jurusan_list)) {
            $jurusan_list = [
                'Teknik Informatika dan Komputer',
                'Teknik Grafika dan Penerbitan',
                'Teknik Elektro',
                'Teknik Mesin',
                'Teknik Sipil',
                'Administrasi Niaga',
                'Akuntansi',
            ];
        }
        
        return view('wadir.dashboard', compact('stats', 'list_usulan', 'jurusan_list'));
    }
}
