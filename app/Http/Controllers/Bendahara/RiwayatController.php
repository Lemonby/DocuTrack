<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;

class RiwayatController extends Controller
{
    public function index()
    {
        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->whereIn('status_utama_id', [
                WorkflowService::STATUS_DANA_DIBERIKAN,
                WorkflowService::STATUS_REVISI,
                WorkflowService::STATUS_DITOLAK,
                WorkflowService::STATUS_LPJ_DISETUJUI,
                WorkflowService::STATUS_SELESAI,
            ])
            ->latest()
            ->get();

        $list_riwayat = $kegiatanList->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tgl' => $kegiatan->tanggal_pencairan
                    ? $kegiatan->tanggal_pencairan->format('Y-m-d')
                    : ($kegiatan->updated_at ? $kegiatan->updated_at->format('Y-m-d') : null),
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'Dana Diberikan',
            ];
        })->toArray();
        return view('bendahara.riwayat.index', compact('list_riwayat'));
    }

    public function detail($id)
    {
        $kegiatan = (new KegiatanService())->getDetailLengkap((int) $id);
        $status = $kegiatan->statusUtama->nama_status_usulan ?? 'Dana Diberikan';

        $kegiatan_data = [
            'id' => $kegiatan->kegiatan_id,
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_pengusul' => $kegiatan->nim_pelaksana,
            'nim_nip' => $kegiatan->nim_pelaksana,
            'nama_pelaksana' => $kegiatan->pemilik_kegiatan ?? '-',
            'nama_penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_penanggung_jawab' => $kegiatan->nip ?? '-',
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'wadir_tujuan' => $kegiatan->wadir->nama_wadir ?? $kegiatan->wadir_tujuan,
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'gambaran_umum' => $kegiatan->kak->gambaran_umum ?? '-',
            'metode_pelaksanaan' => $kegiatan->kak->metode_pelaksanaan ?? '-',
            'tahapan_kegiatan' => $kegiatan->kak ? $kegiatan->kak->tahapans->pluck('nama_tahapan')->implode("\n") : '',
            'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : null,
            'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : null,
        ];

        $rab_data = [];
        $anggaran_disetujui = 0;
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
                    'harga' => $rab->harga,
                ];
                $anggaran_disetujui += $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga;
            }
        }

        $jumlah_dicairkan = (float) ($kegiatan->jumlah_dicairkan ?? 0);
        $tanggal_pencairan = $kegiatan->tanggal_pencairan ? $kegiatan->tanggal_pencairan->format('Y-m-d') : null;
        $metode_pencairan = $kegiatan->metode_pencairan ?? '-';
        $kode_mak = $kegiatan->bukti_mak ?? '-';

        $riwayat_pencairan = $kegiatan->tahapanPencairans->map(function ($tahap) {
            return [
                'tanggal_pencairan' => $tahap->tgl_pencairan ? $tahap->tgl_pencairan->format('Y-m-d') : null,
                'termin' => $tahap->termin,
                'nominal' => $tahap->nominal,
                'catatan' => $tahap->catatan,
            ];
        })->toArray();

        $iku_data = $kegiatan->kak ? array_filter(array_map('trim', explode(',', $kegiatan->kak->iku ?? ''))) : [];

        $catatan_revisi = null;
        $revisiHistory = $kegiatan->progressHistories
            ->where('status_id', WorkflowService::STATUS_REVISI)
            ->sortByDesc('created_at')
            ->first();
        if ($revisiHistory && $revisiHistory->revisiComments->isNotEmpty()) {
            $catatan_revisi = $revisiHistory->revisiComments->first()->komentar_revisi ?? null;
        }

        return view('bendahara.riwayat.detail', compact(
            'id', 'status', 'kegiatan_data', 'rab_data', 'catatan_revisi',
            'anggaran_disetujui', 'jumlah_dicairkan', 'tanggal_pencairan',
            'metode_pencairan', 'kode_mak', 'riwayat_pencairan', 'iku_data', 'kegiatan'
        ));
    }

    public function selesai($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->update([
            'status_utama_id' => WorkflowService::STATUS_SELESAI, // 8
        ]);

        // Add progress history entry for status 8 (Selesai)
        \App\Models\ProgressHistory::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'status_id' => WorkflowService::STATUS_SELESAI, // 8
            'changed_by_user_id' => \Illuminate\Support\Facades\Session::get('user_id') ?? 1,
            'created_at' => now(),
        ]);

        // Create log status for Admin (actor) who completes the kegiatan
        \App\Models\LogStatus::create([
            'user_id' => $kegiatan->user_id,
            'tipe_log' => 'APPROVAL',
            'id_referensi' => $kegiatan->kegiatan_id,
            'status' => 'DIBACA',
            'konten_json' => [
                'judul' => 'Kegiatan Selesai',
                'pesan' => "Kegiatan \"{$kegiatan->nama_kegiatan}\" telah dinyatakan Selesai.",
                'link' => "/admin/pengajuan-kegiatan"
            ]
        ]);

        return redirect()->back()->with('success_message', 'Kegiatan telah berhasil dinyatakan Selesai!');
    }
}
