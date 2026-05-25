<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Jurusan;

class KegiatanController extends Controller
{
    public function index()
    {
        $list_kegiatan = Kegiatan::where('posisi_id', 3)
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
                'Teknik Elektro',
                'Administrasi Niaga',
                'Akuntansi',
                'Teknik Mesin',
                'Teknik Grafika dan Penerbitan'
            ];
        }

        return view('ppk.kegiatan.index', compact('list_kegiatan', 'jurusan_list'));
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
        
        $kegiatan_data = [
            'nama_pengusul' => $kegiatan->pemilik_kegiatan,
            'nim_nip' => $kegiatan->nim_pelaksana ?? $kegiatan->nip,
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_penanggung_jawab' => $kegiatan->nama_pj ?? $kegiatan->pemilik_kegiatan,
            'nip_penanggung_jawab' => $kegiatan->nip ?? $kegiatan->nim_pelaksana,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'mak_code' => $kegiatan->bukti_mak,
            'gambaran_umum' => $kegiatan->kak ? $kegiatan->kak->gambaran_umum : '',
            'penerima_manfaat' => $kegiatan->kak ? $kegiatan->kak->penerima_manfaat : '',
            'metode_pelaksanaan' => $kegiatan->kak ? $kegiatan->kak->metode_pelaksanaan : '',
            'tahapan_kegiatan' => $kegiatan->kak ? implode("\n", $kegiatan->kak->tahapans->pluck('nama_tahapan')->toArray()) : '',
            'surat_pengantar' => $kegiatan->surat_pengantar,
            'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : '',
            'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : '',
        ];

        $iku_data = array_filter(explode(',', $kegiatan->kak->iku ?? ''));
        
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

        return view('ppk.kegiatan.show', compact('id', 'status', 'kegiatan_data', 'iku_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'rab_data'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve',
        ]);

        $kegiatan = Kegiatan::findOrFail($id);

        // PPK Approves proposal and moves it to Wadir
        $kegiatan->update([
            'posisi_id' => 4, // Wadir
            'status_utama_id' => 1, // Menunggu (waiting for Wadir approval)
            'umpan_balik_verifikator' => $request->catatan,
        ]);

        return redirect()->route('ppk.dashboard')->with('success', 'Usulan #' . $id . ' berhasil disetujui.');
    }
}

