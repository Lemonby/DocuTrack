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
            ->whereIn('status_utama_id', [\App\Services\WorkflowService::STATUS_DANA_DIBERIKAN, 6])
            ->latest()
            ->get();

        $list_lpj = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = 'menunggu_upload';
            if ($kegiatan->lpj) {
                if ($kegiatan->lpj->status_id == 1) {
                    $statusLabel = $kegiatan->lpj->submitted_at ? 'menunggu' : 'menunggu_upload';
                } elseif ($kegiatan->lpj->status_id == 2) {
                    $statusLabel = 'revisi';
                } elseif ($kegiatan->lpj->status_id == 3) {
                    $statusLabel = 'disetujui';
                } elseif ($kegiatan->lpj->status_id == 4) {
                    $statusLabel = 'ditolak';
                }
            }

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => 'LPJ ' . $kegiatan->nama_kegiatan,
                'nama_mahasiswa' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->lpj ? ($kegiatan->lpj->submitted_at ?? $kegiatan->lpj->created_at ?? $kegiatan->created_at) : $kegiatan->created_at,
                'tenggatLpj' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->copy()->addDays(14) : now()->addDays(14),
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
        if ($kegiatan->lpj) {
            if ($kegiatan->lpj->status_id == 1) {
                $status = $kegiatan->lpj->submitted_at ? 'menunggu' : 'menunggu_upload';
            } elseif ($kegiatan->lpj->status_id == 2) {
                $status = 'revisi';
            } elseif ($kegiatan->lpj->status_id == 3) {
                $status = 'disetujui';
            } elseif ($kegiatan->lpj->status_id == 4) {
                $status = 'ditolak';
            }
        }
        
        $kegiatan_nama = $kegiatan->nama_kegiatan;
        $prodi = $kegiatan->prodi_penyelenggara;
        $kode_mak = $kegiatan->bukti_mak ?? '-';

        $lpjItemMap = collect();
        if ($kegiatan->lpj && $kegiatan->lpj->items) {
            $lpjItemMap = $kegiatan->lpj->items->keyBy(function ($item) {
                return implode('|', [
                    trim((string) $item->uraian),
                    trim((string) $item->rincian),
                    (float) $item->vol1,
                    (float) $item->vol2,
                    (float) $item->harga
                ]);
            });
        }

        $rab_items = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $cat = $rab->kategori->nama_kategori ?? 'Lainnya';
                
                $lpjItem = null;
                if ($kegiatan->lpj) {
                    $key = implode('|', [
                        trim((string) $rab->uraian),
                        trim((string) $rab->rincian),
                        (float) $rab->vol1,
                        (float) $rab->vol2,
                        (float) $rab->harga
                    ]);
                    $lpjItem = $lpjItemMap->get($key);
                }

                $rab_items[$cat][] = [
                    'id' => 'it-' . $rab->rab_item_id,
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => $rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => $rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => $rab->harga,
                    'realisasi' => $lpjItem ? $lpjItem->realisasi : ($rab->harga * $rab->vol1 * $rab->vol2),
                    'catatan_item' => $lpjItem ? $lpjItem->komentar : null,
                    'file_bukti' => $lpjItem ? $lpjItem->file_bukti : null
                ];
            }
        }

        $catatan_revisi = $kegiatan->lpj ? $kegiatan->lpj->komentar_revisi : null;

        return view('admin.lpj.detail', compact('id', 'status', 'rab_items', 'kegiatan_nama', 'catatan_revisi', 'from', 'prodi', 'kode_mak'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,kegiatan_id',
            'realisasi' => 'nullable|array',
            'bukti' => 'nullable|array',
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
                $realisasi = $request->input("realisasi.{$kategori}.{$index}") ?? 0;
                $file = $request->file("bukti.{$kategori}.{$index}");

                // Find existing lpj_item or match by details
                $lpjItem = \App\Models\LpjItem::where('lpj_id', $lpj->lpj_id)
                    ->where('uraian', $rab->uraian)
                    ->where('rincian', $rab->rincian)
                    ->where('vol1', $rab->vol1)
                    ->where('vol2', $rab->vol2)
                    ->where('harga', $rab->harga)
                    ->first();

                $filePath = $lpjItem ? $lpjItem->file_bukti : null;
                if ($file) {
                    $filePath = $file->store('lpj-bukti', 'public');
                }

                $itemData = [
                    'lpj_id' => $lpj->lpj_id,
                    'kategori_id' => $rab->kategori_id,
                    'jenis_belanja' => $kategori,
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'total_harga' => $rab->total_harga ?? ($rab->vol1 * $rab->vol2 * $rab->harga),
                    'realisasi' => $realisasi,
                    'sub_total' => $realisasi,
                    'sat1' => $rab->sat1,
                    'sat2' => $rab->sat2,
                    'vol1' => $rab->vol1,
                    'vol2' => $rab->vol2,
                    'harga' => $rab->harga,
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
            'submitted_at' => now(),
            'status_id' => 1, // Menunggu Verifikasi
        ]);

        return redirect()->route('admin.lpj.index')->with('success', 'LPJ berhasil diajukan ke Bendahara.');
    }
}
