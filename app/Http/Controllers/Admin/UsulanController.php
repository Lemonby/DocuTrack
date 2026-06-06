<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreUsulanRequest;

class UsulanController extends Controller
{
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Session::get('user_id') ?? 1;
        $jurusan = \Illuminate\Support\Facades\Session::get('jurusan');

        $query = \App\Models\Kegiatan::with(['statusUtama', 'user']);
        if (!empty($jurusan)) {
            $query->where('jurusan_penyelenggara', $jurusan);
        } else {
            $query->where('user_id', $userId);
        }

        $kegiatanList = $query->latest()->get();

        $list_usulan = $kegiatanList->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'nama_mahasiswa' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->created_at,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu'
            ];
        })->toArray();

        $all_ikus = \App\Models\Iku::orderBy('id', 'asc')->get();
        return view('admin.usulan.index', compact('list_usulan', 'all_ikus'));
    }

    public function show($id)
    {
        $kegiatan = (new \App\Services\KegiatanService())->getDetailLengkap($id);
        $status = $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu';
        
        $iku_data = $kegiatan->kak ? explode(',', $kegiatan->kak->iku ?? '') : [];

        $tahapan_pelaksanaan = [];
        $indikator_keberhasilan = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->indikators as $ind) {
                if ($ind->bulan) {
                    $indikator_keberhasilan[$ind->bulan] = [
                        'deskripsi' => $ind->indikator_keberhasilan,
                        'target_persen' => $ind->target_persen
                    ];
                }
            }
            foreach ($kegiatan->kak->tahapans as $key => $tahap) {
                $tahapan_pelaksanaan[$key] = $tahap->nama_tahapan;
            }
        }

        $rab_data = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $cat = $rab->kategori->nama_kategori ?? 'Lainnya';
                $rab_data[$cat][] = [
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => $rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => $rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => $rab->harga
                ];
            }
        }

        $payout_status = $kegiatan->jumlah_dicairkan > 0 ? 'Sudah Cair' : 'Belum Ada';
        $lpj_status = $kegiatan->lpj ? $kegiatan->lpj->status : 'Belum Ada';

        $kegiatan_data = [
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_nip' => $kegiatan->nim_pelaksana,
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_pj' => $kegiatan->nip ?? '-',
            'wadir_tujuan' => $kegiatan->wadir->nama_wadir ?? 'Wadir',
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'gambaran_umum' => $kegiatan->kak->gambaran_umum ?? '-',
            'metode_pelaksanaan' => $kegiatan->kak->metode_pelaksanaan ?? '-',
            'kode_mak' => $kegiatan->bukti_mak ?? null,
            'payout_status' => $payout_status,
            'lpj_status' => $lpj_status,
            'total_cair' => $kegiatan->jumlah_dicairkan ?? 0,
            'surat_pengantar' => $kegiatan->surat_pengantar ?? null,
            'tanggal_mulai' => $kegiatan->tanggal_mulai ?? null,
            'tanggal_selesai' => $kegiatan->tanggal_selesai ?? null,
            'metode_pencairan' => $kegiatan->metode_pencairan ?? null
        ];

        $catatan_revisi = null;
        $revisi_comments = [];
        if ($kegiatan->status_utama_id == \App\Services\WorkflowService::STATUS_REVISI) {
            $latestRevisi = $kegiatan->progressHistories()
                ->where('status_id', \App\Services\WorkflowService::STATUS_REVISI)
                ->latest()
                ->first();
            if ($latestRevisi && $latestRevisi->revisiComments->isNotEmpty()) {
                // Keep the general comment where target_tabel is null for the main welcome banner
                $mainComment = $latestRevisi->revisiComments->whereNull('target_tabel')->first();
                $catatan_revisi = $mainComment ? $mainComment->komentar_revisi : null;

                foreach ($latestRevisi->revisiComments as $rc) {
                    if ($rc->target_tabel) {
                        $key = $rc->target_tabel . '.' . $rc->target_kolom;
                        $revisi_comments[$key] = $rc->komentar_revisi;
                    }
                }
            }
        }

        return view('admin.usulan.detail', compact('id', 'status', 'iku_data', 'rab_data', 'kegiatan_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'catatan_revisi', 'revisi_comments', 'kegiatan'));
    }

    public function edit($id)
    {
        $kegiatan = (new \App\Services\KegiatanService())->getDetailLengkap($id);
        $status = $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu';
        
        $iku_data = $kegiatan->kak ? explode(',', $kegiatan->kak->iku ?? '') : [];

        $tahapan_pelaksanaan = [];
        $indikator_keberhasilan = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->indikators as $ind) {
                if ($ind->bulan) {
                    $indikator_keberhasilan[$ind->bulan] = [
                        'deskripsi' => $ind->indikator_keberhasilan,
                        'target_persen' => $ind->target_persen
                    ];
                }
            }
            foreach ($kegiatan->kak->tahapans as $key => $tahap) {
                $tahapan_pelaksanaan[$key] = $tahap->nama_tahapan;
            }
        }

        $rab_data = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $cat = $rab->kategori->nama_kategori ?? 'Lainnya';
                $rab_data[$cat][] = [
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => $rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => $rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => $rab->harga
                ];
            }
        }

        $kegiatan_data = [
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_nip' => $kegiatan->nim_pelaksana,
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_pj' => $kegiatan->nip ?? '-',
            'wadir_tujuan' => $kegiatan->wadir->nama_wadir ?? 'Wadir',
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'gambaran_umum' => $kegiatan->kak->gambaran_umum ?? '-',
            'metode_pelaksanaan' => $kegiatan->kak->metode_pelaksanaan ?? '-',
            'kode_mak' => $kegiatan->bukti_mak ?? null
        ];

        $catatan_revisi = null;
        if ($kegiatan->status_utama_id == \App\Services\WorkflowService::STATUS_REVISI) {
            $latestRevisi = $kegiatan->progressHistories()
                ->where('status_id', \App\Services\WorkflowService::STATUS_REVISI)
                ->latest()
                ->first();
            if ($latestRevisi && $latestRevisi->revisiComments->isNotEmpty()) {
                $catatan_revisi = $latestRevisi->revisiComments->pluck('komentar_revisi')->implode("\n");
            }
        }

        $all_ikus = \App\Models\Iku::orderBy('id', 'asc')->get();
        return view('admin.usulan.edit', compact('id', 'status', 'iku_data', 'rab_data', 'kegiatan_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'catatan_revisi', 'kegiatan', 'all_ikus'));
    }

    public function store(StoreUsulanRequest $request)
    {
        $userId = \Illuminate\Support\Facades\Session::get('user_id') ?? 1;
        try {
            (new \App\Services\KegiatanService())->createKegiatan($request->all(), $userId);
            return redirect()->route('admin.usulan.index')->with('success_message', 'Usulan berhasil diajukan!');
        } catch (\Exception $e) {
            // Handle error, fallback for now if structure doesn't match
            return redirect()->route('admin.usulan.index')->with('success_message', 'Terdapat kesalahan: ' . $e->getMessage());
        }
    }

    public function update(StoreUsulanRequest $request, $id)
    {
        $userId = \Illuminate\Support\Facades\Session::get('user_id') ?? 1;
        try {
            (new \App\Services\KegiatanService())->updateKegiatan($id, $request->all(), $userId);
            return redirect()->route('admin.usulan.index')->with('success_message', 'Revisi usulan berhasil disimpan dan diajukan ulang!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error_message', 'Gagal memperbarui usulan: ' . $e->getMessage());
        }
    }
}
