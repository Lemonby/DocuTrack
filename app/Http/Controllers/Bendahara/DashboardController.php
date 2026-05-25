<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $total_kak = \App\Models\Kegiatan::where('posisi_id', '>=', 5)->count();
        $dana_diberikan = \App\Models\Kegiatan::where('status_utama_id', 5)->count();
        $ditolak = \App\Models\Kegiatan::where('status_utama_id', 4)->count();
        $menunggu = \App\Models\Kegiatan::where('posisi_id', 5)->where('status_utama_id', 3)->count();

        $stats = [
            'total'         => $total_kak,
            'danaDiberikan' => $dana_diberikan,
            'ditolak'       => $ditolak,
            'menunggu'      => $menunggu,
        ];
        
        $list_kak = \App\Models\Kegiatan::where('posisi_id', '>=', 5)
            ->with('statusUtama')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($k) {
                $status = ($k->status_utama_id == 5) ? 'Sudah Dicairkan' : 'Belum Dicairkan';
                return [
                    'id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'pengusul' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'nim' => $k->nim_pelaksana ?? $k->nip ?? '-',
                    'prodi' => $k->prodi_penyelenggara ?? '-',
                    'jurusan' => $k->jurusan_penyelenggara ?? '-',
                    'tanggal_pengajuan' => $k->created_at ? $k->created_at->format('Y-m-d') : '-',
                    'status' => $status
                ];
            })->toArray();

        $list_lpj = \App\Models\Lpj::with(['kegiatan.statusUtama', 'status'])
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function ($l) {
                $status = 'Menunggu Verifikasi';
                if ($l->status_id == 2) $status = 'Revisi';
                elseif ($l->status_id == 3) $status = 'Disetujui';

                return [
                    'id' => $l->lpj_id,
                    'nama' => 'LPJ - ' . ($l->kegiatan ? $l->kegiatan->nama_kegiatan : 'Kegiatan'),
                    'pengusul' => $l->kegiatan ? ($l->kegiatan->pemilik_kegiatan ?? $l->kegiatan->nama_pj ?? '-') : '-',
                    'nim' => $l->kegiatan ? ($l->kegiatan->nim_pelaksana ?? $l->kegiatan->nip ?? '-') : '-',
                    'prodi' => $l->kegiatan ? ($l->kegiatan->prodi_penyelenggara ?? '-') : '-',
                    'jurusan' => $l->kegiatan ? ($l->kegiatan->jurusan_penyelenggara ?? '-') : '-',
                    'tanggal_pengajuan' => $l->submitted_at ? $l->submitted_at->format('Y-m-d') : '-',
                    'status' => $status
                ];
            })->toArray();

        return view('bendahara.dashboard', compact('stats', 'list_kak', 'list_lpj'));
    }
}
