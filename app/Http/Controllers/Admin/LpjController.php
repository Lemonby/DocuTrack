<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Session::get('user_id') ?? 1;
        $jurusan = \Illuminate\Support\Facades\Session::get('jurusan');

        $query = \App\Models\Kegiatan::with(['statusUtama', 'user', 'lpj'])
            ->whereIn('status_utama_id', [
                \App\Services\WorkflowService::STATUS_DANA_DIBERIKAN,
                6,
                8
            ]);

        if (!empty($jurusan)) {
            $query->where('jurusan_penyelenggara', $jurusan);
        } else {
            $query->where('user_id', $userId);
        }

        $kegiatanList = $query->latest()->get();

        $list_lpj = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = 'menunggu_upload';
            if ($kegiatan->status_utama_id == 8) {
                $statusLabel = 'selesai';
            } elseif ($kegiatan->lpj) {
                if ($kegiatan->lpj->status_id == 1) {
                    $statusLabel = $kegiatan->lpj->submitted_at ? 'menunggu' : 'menunggu_upload';
                } elseif ($kegiatan->lpj->status_id == 2) {
                    $statusLabel = 'revisi';
                } elseif ($kegiatan->lpj->status_id == 3) {
                    $statusLabel = 'disetujui';
                } elseif ($kegiatan->lpj->status_id == 4) {
                    $statusLabel = 'ditolak';
                } elseif ($kegiatan->lpj->status_id == 8) {
                    $statusLabel = 'selesai';
                }
            }

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => 'LPJ ' . $kegiatan->nama_kegiatan,
                'nama_mahasiswa' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->lpj ? ($kegiatan->lpj->submitted_at ?? $kegiatan->lpj->created_at ?? $kegiatan->created_at) : $kegiatan->created_at,
                'tenggatLpj' => $kegiatan->lpj && $kegiatan->lpj->tenggat_lpj ? $kegiatan->lpj->tenggat_lpj->toDateString() : ($kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->copy()->addDays(14)->toDateString() : now()->addDays(14)->toDateString()),
                'status' => $statusLabel
            ];
        })->toArray();

        return view('admin.lpj.index', compact('list_lpj'));
    }

    public function detail(Request $request, $id)
    {
        $from = $request->query('from', 'index');
        $kegiatan = (new \App\Services\KegiatanService())->getDetailLengkap($id);
        
        $status = 'menunggu_upload';
        if ($kegiatan->status_utama_id == 8) {
            $status = 'selesai';
        } elseif ($kegiatan->lpj) {
            if ($kegiatan->lpj->status_id == 1) {
                $status = $kegiatan->lpj->submitted_at ? 'menunggu' : 'menunggu_upload';
            } elseif ($kegiatan->lpj->status_id == 2) {
                $status = 'revisi';
            } elseif ($kegiatan->lpj->status_id == 3) {
                $status = 'disetujui';
            } elseif ($kegiatan->lpj->status_id == 4) {
                $status = 'ditolak';
            } elseif ($kegiatan->lpj->status_id == 8) {
                $status = 'selesai';
            }
        }
        
        $kegiatan_nama = $kegiatan->nama_kegiatan;
        $prodi = $kegiatan->prodi_penyelenggara;
        $kode_mak = $kegiatan->bukti_mak ?? '-';

        $lpjItemsGrouped = collect();
        if ($kegiatan->lpj && $kegiatan->lpj->items) {
            $lpjItemsGrouped = $kegiatan->lpj->items->groupBy('jenis_belanja');
        }

        $rab_items = [];
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

                $rab_items[$cat][] = [
                    'id' => 'it-' . $rab->rab_item_id,
                    'rab_item_id' => $rab->rab_item_id,
                    'lpj_item_id' => $lpjItem ? $lpjItem->lpj_item_id : null,
                    'uraian' => $lpjItem ? $lpjItem->uraian : $rab->uraian,
                    'rincian' => $lpjItem ? $lpjItem->rincian : $rab->rincian,
                    'vol1' => $lpjItem ? $lpjItem->vol1 : $rab->vol1,
                    'sat1' => $lpjItem ? $lpjItem->sat1 : $rab->sat1,
                    'vol2' => $lpjItem ? $lpjItem->vol2 : $rab->vol2,
                    'sat2' => $lpjItem ? $lpjItem->sat2 : $rab->sat2,
                    'harga' => $lpjItem ? $lpjItem->harga : $rab->harga,
                    'realisasi' => $lpjItem ? $lpjItem->realisasi : ($rab->harga * $rab->vol1 * $rab->vol2),
                    'catatan_item' => $lpjItem ? $lpjItem->komentar : null,
                    'file_bukti' => $lpjItem ? $lpjItem->file_bukti : null,
                    'anggaran_original' => $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga,
                ];

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

        $catatan_revisi = $kegiatan->lpj ? $kegiatan->lpj->komentar_revisi : null;
        
        $tanggal_mulai = $kegiatan->tanggal_mulai;
        $tanggal_selesai = $kegiatan->tanggal_selesai;
        $realisasi_tanggal_mulai = $kegiatan->lpj && $kegiatan->lpj->realisasi_tanggal_mulai ? $kegiatan->lpj->realisasi_tanggal_mulai->format('Y-m-d') : null;
        $realisasi_tanggal_selesai = $kegiatan->lpj && $kegiatan->lpj->realisasi_tanggal_selesai ? $kegiatan->lpj->realisasi_tanggal_selesai->format('Y-m-d') : null;

        return view('admin.lpj.detail', compact(
            'id', 'status', 'rab_items', 'kegiatan_nama', 'catatan_revisi', 'from', 'prodi', 'kode_mak',
            'tanggal_mulai', 'tanggal_selesai', 'realisasi_tanggal_mulai', 'realisasi_tanggal_selesai',
            'grand_total_anggaran'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,kegiatan_id',
            'uraian' => 'nullable|array',
            'rincian' => 'nullable|array',
            'vol1' => 'nullable|array',
            'sat1' => 'nullable|array',
            'vol2' => 'nullable|array',
            'sat2' => 'nullable|array',
            'harga' => 'nullable|array',
            'realisasi' => 'nullable|array',
            'bukti' => 'nullable|array',
            'lpj_item_id' => 'nullable|array',
            'realisasi_tanggal_mulai' => 'required|date',
            'realisasi_tanggal_selesai' => 'required|date|after_or_equal:realisasi_tanggal_mulai',
        ]);

        $kegiatan = \App\Models\Kegiatan::with(['kak.rabs.kategori', 'lpj'])->findOrFail($request->kegiatan_id);

        $lpj = $kegiatan->lpj;
        if (!$lpj) {
            $lpj = \App\Models\Lpj::create([
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'status_id' => 1, // Menunggu Verifikasi
                'tenggat_lpj' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->copy()->addDays(14) : now()->addDays(14),
            ]);
        }

        $rabsGrouped = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $cat = $rab->kategori->nama_kategori ?? 'Lainnya';
                $rabsGrouped[$cat][] = $rab;
            }
        }

        $grandTotalRealisasi = 0;
        foreach ($rabsGrouped as $kategori => $rabs) {
            foreach ($rabs as $index => $rab) {
                // Get possibly edited fields, falling back to original RAB values
                $uraian = $request->input("uraian.{$kategori}.{$index}") ?? $rab->uraian;
                $rincian = $request->input("rincian.{$kategori}.{$index}") ?? $rab->rincian;
                $vol1 = $request->input("vol1.{$kategori}.{$index}") ?? $rab->vol1;
                $sat1 = $request->input("sat1.{$kategori}.{$index}") ?? $rab->sat1;
                $vol2 = $request->input("vol2.{$kategori}.{$index}") ?? $rab->vol2;
                $sat2 = $request->input("sat2.{$kategori}.{$index}") ?? $rab->sat2;
                $harga = $request->input("harga.{$kategori}.{$index}") ?? $rab->harga;
                
                $realisasi = $request->input("realisasi.{$kategori}.{$index}") ?? 0;
                $lpjItemId = $request->input("lpj_item_id.{$kategori}.{$index}");
                $file = $request->file("bukti.{$kategori}.{$index}");

                // Find existing lpj_item
                $lpjItem = null;
                if ($lpjItemId) {
                    $lpjItem = \App\Models\LpjItem::where('lpj_id', $lpj->lpj_id)
                        ->where('lpj_item_id', $lpjItemId)
                        ->first();
                }

                // Fallback to match by original rab details if lpj_item_id is not provided
                if (!$lpjItem) {
                    $lpjItem = \App\Models\LpjItem::where('lpj_id', $lpj->lpj_id)
                        ->where('uraian', $rab->uraian)
                        ->where('rincian', $rab->rincian)
                        ->where('vol1', $rab->vol1)
                        ->where('vol2', $rab->vol2)
                        ->where('harga', $rab->harga)
                        ->first();
                }

                $filePath = $lpjItem ? $lpjItem->file_bukti : null;
                if ($file) {
                    $filePath = $file->store('lpj-bukti', 'public');
                }

                $anggaran_disetujui = $rab->total_harga ?? ($rab->vol1 * $rab->vol2 * $rab->harga);

                $itemData = [
                    'lpj_id' => $lpj->lpj_id,
                    'kategori_id' => $rab->kategori_id,
                    'jenis_belanja' => $kategori,
                    'uraian' => $uraian,
                    'rincian' => $rincian,
                    'total_harga' => $anggaran_disetujui, // original approved budget as read-only baseline
                    'realisasi' => $realisasi,
                    'sub_total' => $realisasi,
                    'sat1' => $sat1,
                    'sat2' => $sat2,
                    'vol1' => $vol1,
                    'vol2' => $vol2,
                    'harga' => $harga,
                ];
                if ($filePath !== null) {
                    $itemData['file_bukti'] = $filePath;
                }

                if ($lpjItem) {
                    $lpjItem->update($itemData);
                } else {
                    \App\Models\LpjItem::create($itemData);
                }

                $grandTotalRealisasi += $realisasi;
            }
        }

        $lpj->update([
            'grand_total_realisasi' => $grandTotalRealisasi,
            'realisasi_tanggal_mulai' => $request->realisasi_tanggal_mulai,
            'realisasi_tanggal_selesai' => $request->realisasi_tanggal_selesai,
            'submitted_at' => now(),
            'status_id' => 1, // Menunggu Verifikasi
        ]);

        return redirect()->route('admin.lpj.index')->with('success', 'LPJ berhasil diajukan ke Bendahara.');
    }
}
