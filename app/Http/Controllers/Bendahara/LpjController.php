<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    public function index()
    {
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
                    'deadline' => $l->tenggat_lpj ? $l->tenggat_lpj->format('Y-m-d') : '-',
                    'status' => $status
                ];
            })->toArray();

        return view('bendahara.lpj.index', compact('list_lpj'));
    }

    public function detail(Request $request, $id)
    {
        $lpj = \App\Models\Lpj::with([
            'kegiatan.kak.rabs.kategori',
            'kegiatan.statusUtama',
            'kegiatan.wadir',
            'items',
            'status'
        ])->findOrFail($id);

        $from = $request->query('from', 'index');
        
        $status = 'Menunggu Verifikasi';
        if ($lpj->status_id == 2) $status = 'Revisi';
        elseif ($lpj->status_id == 3) $status = 'Disetujui';

        $kegiatan_data = [
            'id'                    => $lpj->kegiatan_id,
            'nama_pengusul'         => $lpj->kegiatan->pemilik_kegiatan,
            'nim_pengusul'          => $lpj->kegiatan->nim_pelaksana,
            'nama_pelaksana'        => $lpj->kegiatan->pemilik_kegiatan,
            'nama_penanggung_jawab' => $lpj->kegiatan->nama_pj ?? $lpj->kegiatan->pemilik_kegiatan,
            'nip_penanggung_jawab'  => $lpj->kegiatan->nip ?? $lpj->kegiatan->nim_pelaksana,
            'jurusan'               => $lpj->kegiatan->jurusan_penyelenggara,
            'prodi'                 => $lpj->kegiatan->prodi_penyelenggara,
            'nama_kegiatan'         => $lpj->kegiatan->nama_kegiatan,
            'wadir_tujuan'          => $lpj->kegiatan->wadir ? ('Wakil Direktur Bidang ' . $lpj->kegiatan->wadir->nama_wadir) : 'Wakil Direktur',
            'penerima_manfaat'      => $lpj->kegiatan->kak ? $lpj->kegiatan->kak->penerima_manfaat : '',
            'tanggal_mulai'         => $lpj->kegiatan->tanggal_mulai ? $lpj->kegiatan->tanggal_mulai->format('Y-m-d') : '',
            'tanggal_selesai'       => $lpj->kegiatan->tanggal_selesai ? $lpj->kegiatan->tanggal_selesai->format('Y-m-d') : '',
        ];

        $kode_mak = $lpj->kegiatan->bukti_mak ?? 'MAK-PENDING-REV';
        
        $rab_items = [];
        if ($lpj->items) {
            foreach ($lpj->items as $item) {
                $catName = $item->jenis_belanja ?? ($item->kategori ? $item->kategori->nama_kategori : 'Belanja Barang');
                $rab_items[$catName][] = [
                    'id' => $item->lpj_item_id,
                    'uraian' => $item->uraian,
                    'rincian' => $item->rincian,
                    'vol1' => (float)($item->vol1 ?? 0),
                    'sat1' => $item->sat1,
                    'vol2' => (float)($item->vol2 ?? 1),
                    'sat2' => $item->sat2,
                    'harga' => (float)($item->harga ?? 0),
                    'realisasi' => (float)($item->realisasi ?? 0),
                    'keterangan' => $item->file_bukti ? 'File Terlampir' : '-',
                    'catatan_item' => $item->komentar
                ];
            }
        }

        $anggaran_disetujui = 0;
        if ($lpj->kegiatan->kak) {
            foreach ($lpj->kegiatan->kak->rabs as $rab) {
                $anggaran_disetujui += $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga;
            }
        }
        $anggaran_realisasi = (float)($lpj->grand_total_realisasi ?? 0);

        $iku_data = array_filter(explode(',', $lpj->kegiatan->kak->iku ?? ''));
        $catatan_revisi = $lpj->komentar_revisi;

        return view('bendahara.lpj.detail', compact(
            'id', 'status', 'rab_items', 'kegiatan_data', 'catatan_revisi', 
            'from', 'kode_mak', 'anggaran_disetujui', 'anggaran_realisasi', 'iku_data'
        ));
    }

    public function proses(Request $request, $id)
    {
        $lpj = \App\Models\Lpj::findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,revise',
        ]);

        if ($request->action === 'approve') {
            $lpj->update([
                'status_id' => 3, // Disetujui
                'approved_at' => now(),
                'komentar_revisi' => $request->notes,
            ]);
            $message = 'Laporan Pertanggungjawaban (LPJ) berhasil disetujui.';
        } else {
            $lpj->update([
                'status_id' => 2, // Revisi
                'komentar_revisi' => $request->notes,
            ]);
            
            if ($request->has('item_feedback')) {
                foreach ($request->item_feedback as $itemId => $feedback) {
                    if (!empty($feedback)) {
                        \App\Models\LpjItem::where('lpj_item_id', $itemId)->update([
                            'komentar' => $feedback
                        ]);
                    }
                }
            }
            $message = 'Permintaan revisi LPJ berhasil dikirim.';
        }

        return redirect()->route('bendahara.lpj.index')->with('success', $message);
    }
}
