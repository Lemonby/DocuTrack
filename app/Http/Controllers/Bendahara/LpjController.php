<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Services\KegiatanService;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    public function index()
    {
        $lpjList = Lpj::with(['kegiatan.user'])
            ->latest('submitted_at')
            ->get();

        $list_lpj = $lpjList->map(function ($lpj) {
            return [
                'id' => $lpj->kegiatan_id,
                'nama' => 'LPJ - ' . ($lpj->kegiatan->nama_kegiatan ?? 'Kegiatan'),
                'pengusul' => $lpj->kegiatan->user->nama ?? $lpj->kegiatan->pemilik_kegiatan,
                'nim' => $lpj->kegiatan->nim_pelaksana,
                'prodi' => $lpj->kegiatan->prodi_penyelenggara,
                'jurusan' => $lpj->kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $lpj->submitted_at ? $lpj->submitted_at->format('Y-m-d') : null,
                'deadline' => $lpj->tenggat_lpj ? $lpj->tenggat_lpj->format('Y-m-d') : null,
                'status' => $this->mapLpjStatusLabel($lpj),
            ];
        })->toArray();
        return view('bendahara.lpj.index', compact('list_lpj'));
    }

    public function detail(Request $request, $id)
    {
        $id = (int) $id;
        $from = $request->query('from', 'index');
        $kegiatan = (new KegiatanService())->getDetailLengkap($id);
        $status = $kegiatan->lpj ? $this->mapLpjStatusLabel($kegiatan->lpj) : 'Menunggu Verifikasi';

        $kegiatan_data = [
            'id' => $id,
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_pengusul' => $kegiatan->nim_pelaksana,
            'nama_pelaksana' => $kegiatan->pemilik_kegiatan ?? '-',
            'nama_penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_penanggung_jawab' => $kegiatan->nip ?? '-',
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'wadir_tujuan' => $kegiatan->wadir->nama_wadir ?? $kegiatan->wadir_tujuan,
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : null,
            'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : null,
            'realisasi_tanggal_mulai' => $kegiatan->lpj && $kegiatan->lpj->realisasi_tanggal_mulai ? $kegiatan->lpj->realisasi_tanggal_mulai->format('Y-m-d') : null,
            'realisasi_tanggal_selesai' => $kegiatan->lpj && $kegiatan->lpj->realisasi_tanggal_selesai ? $kegiatan->lpj->realisasi_tanggal_selesai->format('Y-m-d') : null,
        ];

        $kode_mak = $kegiatan->bukti_mak ?? '-';

        $rab_items = [];
        $anggaran_disetujui = 0;
        $anggaran_realisasi = 0;

        $lpjItemsGrouped = collect();
        if ($kegiatan->lpj && $kegiatan->lpj->items) {
            $lpjItemsGrouped = $kegiatan->lpj->items->groupBy('jenis_belanja');
        }

        $categoryCounters = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $cat = $rab->kategori->nama_kategori ?? 'Lainnya';
                if (!isset($categoryCounters[$cat])) {
                    $categoryCounters[$cat] = 0;
                }
                $index = $categoryCounters[$cat];

                $lpjItem = null;
                if (isset($lpjItemsGrouped[$cat]) && isset($lpjItemsGrouped[$cat][$index])) {
                    $lpjItem = $lpjItemsGrouped[$cat][$index];
                }

                $realisasi = $lpjItem ? (float) $lpjItem->realisasi : 0;

                $rab_items[$cat][] = [
                    'id' => $lpjItem ? $lpjItem->lpj_item_id : $rab->rab_item_id,
                    'uraian' => $lpjItem ? $lpjItem->uraian : $rab->uraian,
                    'rincian' => $lpjItem ? $lpjItem->rincian : $rab->rincian,
                    'vol1' => $lpjItem ? $lpjItem->vol1 : $rab->vol1,
                    'sat1' => $lpjItem ? $lpjItem->sat1 : $rab->sat1,
                    'vol2' => $lpjItem ? $lpjItem->vol2 : $rab->vol2,
                    'sat2' => $lpjItem ? $lpjItem->sat2 : $rab->sat2,
                    'harga' => $lpjItem ? $lpjItem->harga : $rab->harga,
                    'realisasi' => $realisasi,
                    'file_bukti' => $lpjItem ? $lpjItem->file_bukti : null,
                    'keterangan' => $lpjItem->komentar ?? '',
                    'catatan_item' => $lpjItem->komentar ?? '',
                    'anggaran_original' => $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga,
                ];

                $anggaran_realisasi += $realisasi;

                $categoryCounters[$cat]++;
            }
        }

        $grand_total_anggaran = (float) ($kegiatan->jumlah_dicairkan ?? 0);
        if ($grand_total_anggaran <= 0) {
            if ($kegiatan->kak) {
                foreach ($kegiatan->kak->rabs as $rab) {
                    $grand_total_anggaran += $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga;
                }
            }
        }

        $iku_data = $kegiatan->kak ? array_filter(array_map('trim', explode(',', $kegiatan->kak->iku ?? ''))) : [];
        $catatan_revisi = $kegiatan->lpj ? $kegiatan->lpj->komentar_revisi : null;

        return view('bendahara.lpj.detail', compact(
            'id', 'status', 'rab_items', 'kegiatan_data', 'catatan_revisi', 
            'from', 'kode_mak', 'grand_total_anggaran', 'anggaran_realisasi', 'iku_data'
        ));
    }

    private function mapLpjStatusLabel(Lpj $lpj): string
    {
        return match ((int) $lpj->status_id) {
            2 => 'Revisi',
            3 => 'LPJ Disetujui',
            4 => 'Ditolak',
            default => $lpj->komentar_revisi ? 'Telah Direvisi' : 'Menunggu Verifikasi',
        };
    }

    private function makeItemKey($uraian, $rincian, $vol1, $vol2, $harga): string
    {
        return implode('|', [trim((string) $uraian), trim((string) $rincian), $vol1, $vol2, $harga]);
    }

    public function proses(Request $request, $id)
    {
        $id = (int) $id;
        $action = $request->input('action');
        $notes = $request->input('notes');
        $itemFeedback = $request->input('item_feedback') ?? [];

        $kegiatan = Kegiatan::findOrFail($id);
        $lpj = Lpj::where('kegiatan_id', $kegiatan->kegiatan_id)->firstOrFail();

        \Illuminate\Support\Facades\DB::transaction(function () use ($kegiatan, $lpj, $action, $notes, $itemFeedback) {
            // 1. Save item feedback
            foreach ($itemFeedback as $itemId => $feedback) {
                // Try finding by lpj_item_id directly
                $lpjItem = \App\Models\LpjItem::where('lpj_id', $lpj->lpj_id)
                    ->where('lpj_item_id', $itemId)
                    ->first();
                
                if ($lpjItem) {
                    $lpjItem->update(['komentar' => $feedback]);
                } else {
                    // Backward compatibility safety net
                    $rab = \App\Models\Rab::find($itemId);
                    if ($rab) {
                        $lpjItem = \App\Models\LpjItem::where('lpj_id', $lpj->lpj_id)
                            ->where('uraian', $rab->uraian)
                            ->where('rincian', $rab->rincian)
                            ->where('vol1', $rab->vol1)
                            ->where('vol2', $rab->vol2)
                            ->where('harga', $rab->harga)
                            ->first();
                        if ($lpjItem) {
                            $lpjItem->update(['komentar' => $feedback]);
                        }
                    }
                }
            }

            // 2. Perform action
            if ($action === 'approve') {
                $lpj->update([
                    'status_id' => 3, // Disetujui
                    'approved_at' => now(),
                    'komentar_revisi' => null,
                    'komentar_penolakan' => null,
                ]);

                // Update Kegiatan table: status_utama_id = 6 ("LPJ Disetujui")
                $kegiatan->update([
                    'status_utama_id' => 6, // LPJ Disetujui
                ]);

                // Add progress history entry for status 6 (LPJ Disetujui)
                \App\Models\ProgressHistory::create([
                    'kegiatan_id' => $kegiatan->kegiatan_id,
                    'status_id' => 6,
                    'changed_by_user_id' => \Illuminate\Support\Facades\Session::get('user_id') ?? 1,
                    'created_at' => now(),
                ]);

                // Send notification log in log_statuses
                \App\Models\LogStatus::create([
                    'user_id' => $kegiatan->user_id,
                    'tipe_log' => 'APPROVAL',
                    'id_referensi' => $kegiatan->kegiatan_id,
                    'status' => 'BELUM_DIBACA',
                    'konten_json' => [
                        'judul' => 'LPJ Disetujui',
                        'pesan' => "Laporan pertanggungjawaban kegiatan \"{$kegiatan->nama_kegiatan}\" telah disetujui lunas oleh Bendahara.",
                        'link' => "/admin/pengajuan-lpj"
                    ]
                ]);

                // Create log status for the actor (Bendahara)
                $actorUserId = \Illuminate\Support\Facades\Session::get('user_id') ?? 1;
                if ($actorUserId && $actorUserId !== $kegiatan->user_id) {
                    \App\Models\LogStatus::create([
                        'user_id' => $actorUserId,
                        'tipe_log' => 'APPROVAL',
                        'id_referensi' => $kegiatan->kegiatan_id,
                        'status' => 'DIBACA',
                        'konten_json' => [
                            'judul' => 'Persetujuan LPJ Berhasil',
                            'pesan' => "Anda telah menyetujui LPJ untuk kegiatan \"{$kegiatan->nama_kegiatan}\".",
                            'link' => "/bendahara/lpj/show/{$kegiatan->kegiatan_id}"
                        ]
                    ]);
                }

            } elseif ($action === 'revise') {
                $lpj->update([
                    'status_id' => 2, // Revisi
                    'komentar_revisi' => $notes,
                    'approved_at' => null,
                ]);

                // Send notification log in log_statuses
                \App\Models\LogStatus::create([
                    'user_id' => $kegiatan->user_id,
                    'tipe_log' => 'REVISION',
                    'id_referensi' => $kegiatan->kegiatan_id,
                    'status' => 'BELUM_DIBACA',
                    'konten_json' => [
                        'judul' => 'LPJ Perlu Revisi',
                        'pesan' => "LPJ kegiatan \"{$kegiatan->nama_kegiatan}\" memerlukan revisi. Catatan: {$notes}",
                        'link' => "/admin/pengajuan-lpj"
                    ]
                ]);

                // Create log status for the actor (Bendahara)
                $actorUserId = \Illuminate\Support\Facades\Session::get('user_id') ?? 1;
                if ($actorUserId && $actorUserId !== $kegiatan->user_id) {
                    \App\Models\LogStatus::create([
                        'user_id' => $actorUserId,
                        'tipe_log' => 'REVISION',
                        'id_referensi' => $kegiatan->kegiatan_id,
                        'status' => 'DIBACA',
                        'konten_json' => [
                            'judul' => 'Revisi LPJ Berhasil Dikirim',
                            'pesan' => "Permintaan revisi LPJ untuk kegiatan \"{$kegiatan->nama_kegiatan}\" berhasil dikirim. Catatan: {$notes}",
                            'link' => "/bendahara/lpj/show/{$kegiatan->kegiatan_id}"
                        ]
                    ]);
                }
            }
        });

        $message = $action === 'approve' ? 'LPJ berhasil disetujui lunas.' : 'Permintaan revisi LPJ berhasil dikirim.';
        return redirect()->route('bendahara.lpj.index')->with('success', $message);
    }
}
