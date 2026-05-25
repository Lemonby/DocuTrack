<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat_list = \App\Models\Kegiatan::where('posisi_id', '>=', 4)
            ->with('statusUtama')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'pengusul' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'nim' => $k->nim_pelaksana ?? $k->nip ?? '-',
                    'tanggal_proses' => $k->updated_at ? $k->updated_at->format('Y-m-d') : '-',
                    'status' => $k->statusUtama ? $k->statusUtama->nama_status_usulan : 'Disetujui',
                    'catatan' => $k->umpan_balik_verifikator ?? '-'
                ];
            })->toArray();
        return view('ppk.riwayat.index', compact('riwayat_list'));
    }
}
