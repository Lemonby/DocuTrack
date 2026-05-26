<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Session::get('user_id') ?? 1;
        $kegiatanList = \App\Models\Kegiatan::with(['statusUtama', 'user', 'lpj'])
            ->where('user_id', $userId)
            ->whereNotNull('tanggal_selesai')
            ->latest()
            ->get();

        $list_lpj = $kegiatanList->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => 'LPJ ' . $kegiatan->nama_kegiatan,
                'nama_mahasiswa' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->lpj->created_at ?? $kegiatan->created_at,
                'tenggatLpj' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->copy()->addDays(14) : now()->addDays(14),
                'status' => $kegiatan->lpj ? $kegiatan->lpj->status : 'menunggu_upload'
            ];
        })->toArray();

        return view('admin.lpj.index', compact('list_lpj'));
    }

    public function detail(Request $request, $id)
    {
        $from = $request->query('from', 'index');
        $kegiatan = (new \App\Services\KegiatanService())->getDetailLengkap($id);
        $status = $kegiatan->lpj ? $kegiatan->lpj->status : 'Draft';
        
        $kegiatan_nama = $kegiatan->nama_kegiatan;
        $prodi = $kegiatan->prodi_penyelenggara;
        $kode_mak = $kegiatan->bukti_mak ?? '-';

        $rab_items = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $cat = $rab->kategori->nama_kategori ?? 'Lainnya';
                $lpjItem = null;
                if ($kegiatan->lpj && $kegiatan->lpj->items) {
                    $lpjItem = $kegiatan->lpj->items->where('rab_id', $rab->rab_id)->first();
                }

                $rab_items[$cat][] = [
                    'id' => 'it-' . $rab->rab_id,
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => $rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => $rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => $rab->harga,
                    'realisasi' => $lpjItem ? $lpjItem->realisasi : ($status != 'Draft' ? ($rab->harga * $rab->vol1 * $rab->vol2) : 0),
                    'catatan_item' => $lpjItem ? $lpjItem->catatan_revisi : null
                ];
            }
        }

        $catatan_revisi = $kegiatan->lpj ? $kegiatan->lpj->catatan_revisi : null;

        return view('admin.lpj.detail', compact('id', 'status', 'rab_items', 'kegiatan_nama', 'catatan_revisi', 'from', 'prodi', 'kode_mak'));
    }

    public function store(Request $request)
    {
        // Placeholder for LPJ storage/submission to Bendahara
        return redirect()->route('admin.lpj.index')->with('success', 'LPJ berhasil diajukan ke Bendahara.');
    }
}
