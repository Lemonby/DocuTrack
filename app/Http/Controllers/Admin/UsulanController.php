<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Kegiatan;
use App\Models\Kak;
use App\Models\Rab;
use App\Models\TahapanPelaksanaan;
use App\Models\IndikatorKak;

class UsulanController extends Controller
{
    public function index()
    {
        return view('admin.usulan.index');
    }

    public function show($id)
    {
        $kegiatan = Kegiatan::with([
            'kak.rabs.kategori', 
            'kak.indikators', 
            'kak.tahapans', 
            'wadir', 
            'statusUtama', 
            'lpj.status'
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
            'penanggung_jawab' => $kegiatan->nama_pj ?? $kegiatan->pemilik_kegiatan,
            'nip_pj' => $kegiatan->nip ?? $kegiatan->nim_pelaksana,
            'wadir_tujuan' => $kegiatan->wadir ? ('Wakil Direktur Bidang ' . $kegiatan->wadir->nama_wadir) : 'Wakil Direktur',
            'penerima_manfaat' => $kegiatan->kak ? $kegiatan->kak->penerima_manfaat : '',
            'gambaran_umum' => $kegiatan->kak ? $kegiatan->kak->gambaran_umum : '',
            'metode_pelaksanaan' => $kegiatan->kak ? $kegiatan->kak->metode_pelaksanaan : '',
            'kode_mak' => $kegiatan->bukti_mak,
            'payout_status' => $kegiatan->metode_pencairan ?? 'Belum Ada',
            'lpj_status' => $kegiatan->lpj ? ($kegiatan->lpj->statusUtama ? $kegiatan->lpj->statusUtama->nama_status_usulan : 'Belum Ada') : 'Belum Ada',
            'total_cair' => (float)($kegiatan->jumlah_dicairkan ?? 0)
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

        return view('admin.usulan.detail', compact(
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

    public function edit($id)
    {
        $kegiatan = Kegiatan::with([
            'kak.rabs.kategori', 
            'kak.indikators', 
            'kak.tahapans', 
            'wadir', 
            'statusUtama'
        ])->findOrFail($id);

        $status = $kegiatan->statusUtama ? $kegiatan->statusUtama->nama_status_usulan : 'Revisi';
        
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
            'penanggung_jawab' => $kegiatan->nama_pj ?? $kegiatan->pemilik_kegiatan,
            'nip_pj' => $kegiatan->nip ?? $kegiatan->nim_pelaksana,
            'wadir_tujuan' => $kegiatan->wadir_tujuan,
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

        return view('admin.usulan.edit', compact(
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

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan_step1' => 'required',
            'nama_pengusul_step1' => 'required',
            'nim_nip' => 'required',
            'jurusan' => 'required',
            'prodi' => 'required',
            'wadir_tujuan' => 'required',
            'gambaran_umum' => 'required',
            'penerima_manfaat' => 'required',
            'metode_pelaksanaan' => 'required',
        ]);

        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => $request->nama_kegiatan_step1,
            'prodi_penyelenggara' => $request->prodi,
            'pemilik_kegiatan' => $request->nama_pengusul_step1,
            'nim_pelaksana' => $request->nim_nip,
            'nip' => $request->nim_nip,
            'nama_pj' => $request->nama_pengusul_step1,
            'user_id' => Session::get('user_id'),
            'jurusan_penyelenggara' => $request->jurusan,
            'status_utama_id' => 1, // Menunggu
            'wadir_tujuan' => $request->wadir_tujuan,
            'posisi_id' => 2, // Verifikator
        ]);

        $kak = Kak::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'iku' => $request->indikator_kinerja,
            'penerima_manfaat' => $request->penerima_manfaat,
            'gambaran_umum' => $request->gambaran_umum,
            'metode_pelaksanaan' => $request->metode_pelaksanaan,
            'tgl_pembuatan' => now(),
        ]);

        if ($request->has('tahapan')) {
            foreach ($request->tahapan as $nama_tahapan) {
                if (!empty($nama_tahapan)) {
                    TahapanPelaksanaan::create([
                        'kak_id' => $kak->kak_id,
                        'nama_tahapan' => $nama_tahapan
                    ]);
                }
            }
        }

        if ($request->has('indikator_nama')) {
            foreach ($request->indikator_nama as $index => $nama_indikator) {
                if (!empty($nama_indikator)) {
                    IndikatorKak::create([
                        'kak_id' => $kak->kak_id,
                        'bulan' => $request->indikator_bulan[$index] ?? null,
                        'indikator_keberhasilan' => $nama_indikator,
                        'target_persen' => $request->indikator_target[$index] ?? null,
                    ]);
                }
            }
        }

        if ($request->has('rab_data')) {
            $rab_json = json_decode($request->rab_data, true);
            if (is_array($rab_json)) {
                $kategoriMap = [
                    'Belanja Barang' => 4,
                    'Belanja Perjalanan' => 5,
                    'Belanja Jasa' => 6
                ];
                foreach ($rab_json as $categoryName => $items) {
                    $kategori_id = $kategoriMap[$categoryName] ?? 4;
                    foreach ($items as $item) {
                        Rab::create([
                            'kak_id' => $kak->kak_id,
                            'kategori_id' => $kategori_id,
                            'uraian' => $item['uraian'] ?? '',
                            'rincian' => $item['rincian'] ?? '',
                            'sat1' => $item['sat1'] ?? '',
                            'sat2' => $item['sat2'] ?? '',
                            'vol1' => $item['vol1'] ?? 1,
                            'vol2' => $item['vol2'] ?? 1,
                            'harga' => $item['harga'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.dashboard')->with('success_message', 'Usulan KAK berhasil diajukan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan_step1' => 'required',
            'nama_pengusul_step1' => 'required',
            'nim_nip' => 'required',
            'jurusan' => 'required',
            'prodi' => 'required',
            'wadir_tujuan' => 'required',
            'gambaran_umum' => 'required',
            'penerima_manfaat' => 'required',
            'metode_pelaksanaan' => 'required',
        ]);

        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->update([
            'nama_kegiatan' => $request->nama_kegiatan_step1,
            'prodi_penyelenggara' => $request->prodi,
            'pemilik_kegiatan' => $request->nama_pengusul_step1,
            'nim_pelaksana' => $request->nim_nip,
            'nip' => $request->nim_nip,
            'nama_pj' => $request->nama_pengusul_step1,
            'jurusan_penyelenggara' => $request->jurusan,
            'status_utama_id' => 1, // Reset to Menunggu
            'wadir_tujuan' => $request->wadir_tujuan,
            'posisi_id' => 2, // Back to Verifikator
            'umpan_balik_verifikator' => null, // Clear review comments
        ]);

        $kak = Kak::where('kegiatan_id', $kegiatan->kegiatan_id)->first();
        if ($kak) {
            $kak->update([
                'iku' => $request->indikator_kinerja,
                'penerima_manfaat' => $request->penerima_manfaat,
                'gambaran_umum' => $request->gambaran_umum,
                'metode_pelaksanaan' => $request->metode_pelaksanaan,
            ]);

            TahapanPelaksanaan::where('kak_id', $kak->kak_id)->delete();
            IndikatorKak::where('kak_id', $kak->kak_id)->delete();
            Rab::where('kak_id', $kak->kak_id)->delete();

            if ($request->has('tahapan')) {
                foreach ($request->tahapan as $nama_tahapan) {
                    if (!empty($nama_tahapan)) {
                        TahapanPelaksanaan::create([
                            'kak_id' => $kak->kak_id,
                            'nama_tahapan' => $nama_tahapan
                        ]);
                    }
                }
            }

            if ($request->has('indikator_nama')) {
                foreach ($request->indikator_nama as $index => $nama_indikator) {
                    if (!empty($nama_indikator)) {
                        IndikatorKak::create([
                            'kak_id' => $kak->kak_id,
                            'bulan' => $request->indikator_bulan[$index] ?? null,
                            'indikator_keberhasilan' => $nama_indikator,
                            'target_persen' => $request->indikator_target[$index] ?? null,
                        ]);
                    }
                }
            }

            if ($request->has('rab_data')) {
                $rab_json = json_decode($request->rab_data, true);
                if (is_array($rab_json)) {
                    $kategoriMap = [
                        'Belanja Barang' => 4,
                        'Belanja Perjalanan' => 5,
                        'Belanja Jasa' => 6
                    ];
                    foreach ($rab_json as $categoryName => $items) {
                        $kategori_id = $kategoriMap[$categoryName] ?? 4;
                        foreach ($items as $item) {
                            Rab::create([
                                'kak_id' => $kak->kak_id,
                                'kategori_id' => $kategori_id,
                                'uraian' => $item['uraian'] ?? '',
                                'rincian' => $item['rincian'] ?? '',
                                'sat1' => $item['sat1'] ?? '',
                                'sat2' => $item['sat2'] ?? '',
                                'vol1' => $item['vol1'] ?? 1,
                                'vol2' => $item['vol2'] ?? 1,
                                'harga' => $item['harga'] ?? 0,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.dashboard')->with('success_message', 'Revisi usulan berhasil disimpan dan diajukan ulang!');
    }
}

