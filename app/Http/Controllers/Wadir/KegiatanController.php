<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $list_kegiatan = \App\Models\Kegiatan::where('posisi_id', 4)
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
                'Akuntansi'
            ];
        }

        return view('wadir.kegiatan.index', compact('list_kegiatan', 'jurusan_list'));
    }

    public function show($id)
    {
        $kegiatan = \App\Models\Kegiatan::with([
            'kak.rabs.kategori', 
            'kak.indikators', 
            'kak.tahapans', 
            'wadir', 
            'statusUtama'
        ])->findOrFail($id);

        $status = $kegiatan->statusUtama ? $kegiatan->statusUtama->nama_status_usulan : 'Menunggu';

        $iku_data = array_filter(explode(',', $kegiatan->kak->iku ?? ''));

        $rab_data = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $catName = $rab->kategori ? $rab->kategori->nama_kategori : 'Belanja Barang';
                $rab_data[$catName][] = [
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => (float)$rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => (float)$rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => (float)$rab->harga,
                    'total_harga' => (float)$rab->total_harga,
                ];
            }
        }

        $kegiatan_data = [
            'nama_pengusul' => $kegiatan->pemilik_kegiatan,
            'nim_nip' => $kegiatan->nim_pelaksana ?? $kegiatan->nip,
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'mak_code' => $kegiatan->bukti_mak,
            'wadir_tujuan' => $kegiatan->wadir ? ('Wakil Direktur Bidang ' . $kegiatan->wadir->nama_wadir) : 'Wakil Direktur',
            'penerima_manfaat' => $kegiatan->kak ? $kegiatan->kak->penerima_manfaat : '',
            'gambaran_umum' => $kegiatan->kak ? $kegiatan->kak->gambaran_umum : '',
            'metode_pelaksanaan' => $kegiatan->kak ? $kegiatan->kak->metode_pelaksanaan : '',
        ];

        $tahapan_pelaksanaan = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->tahapans as $index => $t) {
                $tahapan_pelaksanaan[$index + 1] = $t->nama_tahapan;
            }
        }

        $indikator_keberhasilan = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->indikators as $index => $ind) {
                $indikator_keberhasilan[$index + 1] = [
                    'target_persen' => $ind->target_persen,
                    'deskripsi' => $ind->indikator_keberhasilan
                ];
            }
        }

        $catatan_revisi = $kegiatan->umpan_balik_verifikator;

        return view('wadir.kegiatan.show', compact(
            'id', 
            'status', 
            'iku_data', 
            'rab_data', 
            'kegiatan_data', 
            'tahapan_pelaksanaan', 
            'indikator_keberhasilan', 
            'catatan_revisi'
        ));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $kegiatan = \App\Models\Kegiatan::findOrFail($id);

        if ($request->action === 'approve') {
            // Wadir approves and moves proposal to Bendahara (posisi_id = 5)
            $kegiatan->update([
                'posisi_id' => 5, // Bendahara
                'status_utama_id' => 3, // Disetujui (Wadir is the last approval before Bendahara payout)
                'umpan_balik_verifikator' => $request->notes,
            ]);
            $message = 'Usulan #' . $id . ' berhasil disetujui dan diteruskan ke Bendahara.';
        } else {
            // Wadir rejects usulan (status_utama_id = 4 [Ditolak]) and returns to Admin (posisi_id = 1)
            $kegiatan->update([
                'posisi_id' => 1, // Admin
                'status_utama_id' => 4, // Ditolak
                'umpan_balik_verifikator' => $request->notes,
            ]);
            $message = 'Usulan #' . $id . ' berhasil ditolak.';
        }

        return redirect()->route('wadir.dashboard')->with('success', $message);
    }
}
