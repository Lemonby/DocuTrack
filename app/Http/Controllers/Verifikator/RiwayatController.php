<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Jurusan;

class RiwayatController extends Controller
{
    public function index()
    {
        // Fetch proposals that have been reviewed by Verifikator (either approved, returned for revision, or rejected)
        $list_riwayat = Kegiatan::where(function($q) {
                $q->where('posisi_id', '>', 2)
                  ->orWhereIn('status_utama_id', [2, 4])
                  ->orWhereNotNull('bukti_mak');
            })
            ->with('statusUtama')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'pengusul' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'nim' => $k->nim_pelaksana ?? $k->nip ?? '-',
                    'jurusan' => $k->jurusan_penyelenggara ?? '-',
                    'tanggal_verifikasi' => $k->updated_at ? $k->updated_at->format('Y-m-d') : '-',
                    'status' => $k->statusUtama ? $k->statusUtama->nama_status_usulan : 'Disetujui'
                ];
            })->toArray();

        $jurusan_list = Jurusan::pluck('nama_jurusan')->toArray();
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

        return view('verifikator.riwayat.index', compact('list_riwayat', 'jurusan_list'));
    }
}

