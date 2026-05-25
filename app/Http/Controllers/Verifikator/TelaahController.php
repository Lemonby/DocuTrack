<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Jurusan;

class TelaahController extends Controller
{
    public function index()
    {
        $list_usulan = Kegiatan::where('posisi_id', 2)
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

        return view('verifikator.telaah.index', compact('list_usulan', 'jurusan_list'));
    }

    public function show($id)
    {
        $kegiatan = Kegiatan::with([
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
            'wadir_tujuan' => $kegiatan->wadir ? ('Wakil Direktur Bidang ' . $kegiatan->wadir->nama_wadir) : 'Wakil Direktur',
            'penerima_manfaat' => $kegiatan->kak ? $kegiatan->kak->penerima_manfaat : '',
            'gambaran_umum' => $kegiatan->kak ? $kegiatan->kak->gambaran_umum : '',
            'metode_pelaksanaan' => $kegiatan->kak ? $kegiatan->kak->metode_pelaksanaan : '',
            'kode_mak' => $kegiatan->bukti_mak
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

        return view('verifikator.telaah.show', compact(
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
            'action' => 'required|in:approve,revise,reject',
        ]);

        $kegiatan = Kegiatan::findOrFail($id);

        if ($request->action === 'approve') {
            $kegiatan->update([
                'posisi_id' => 1, // Move back to Admin to fill details
                'status_utama_id' => 1, // Keep status as Menunggu for next step
                'bukti_mak' => $request->kode_mak,
                'umpan_balik_verifikator' => $request->notes,
            ]);
            $message = 'Usulan berhasil disetujui dan diteruskan ke Admin.';
        } elseif ($request->action === 'revise') {
            $kegiatan->update([
                'posisi_id' => 1, // Back to Admin
                'status_utama_id' => 2, // Revisi
                'umpan_balik_verifikator' => $request->notes,
            ]);
            $message = 'Catatan revisi berhasil dikirim ke Admin.';
        } else {
            $kegiatan->update([
                'posisi_id' => 1, // Back to Admin
                'status_utama_id' => 4, // Ditolak
                'umpan_balik_verifikator' => $request->reason,
            ]);
            $message = 'Usulan berhasil ditolak.';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('verifikator.telaah.index')->with('success', $message);
    }
}

