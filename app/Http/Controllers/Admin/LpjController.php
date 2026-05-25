<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Models\LpjItem;

class LpjController extends Controller
{
    public function index()
    {
        $jurusan = Session::get('jurusan');
        $list_lpj = Kegiatan::where('jurusan_penyelenggara', $jurusan)
            ->whereIn('status_utama_id', [3, 5]) // Approved
            ->with(['lpj.status', 'statusUtama'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($k) {
                $status = 'menunggu_upload';
                $id = $k->kegiatan_id; // Fallback to kegiatan_id if no LPJ exists yet
                if ($k->lpj) {
                    $id = $k->lpj->lpj_id;
                    $statusMap = [
                        1 => 'menunggu',
                        2 => 'revisi',
                        3 => 'disetujui',
                        4 => 'ditolak',
                        5 => 'telah_direvisi',
                        6 => 'siap_submit'
                    ];
                    $status = $statusMap[$k->lpj->status_id] ?? 'menunggu';
                }
                return [
                    'id' => $id,
                    'kegiatan_id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'nama_mahasiswa' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'jurusan' => $k->jurusan_penyelenggara,
                    'tanggal_pengajuan' => $k->lpj && $k->lpj->submitted_at ? $k->lpj->submitted_at->format('Y-m-d') : ($k->created_at ? $k->created_at->format('Y-m-d') : '-'),
                    'tenggatLpj' => $k->lpj && $k->lpj->tenggat_lpj ? $k->lpj->tenggat_lpj->format('Y-m-d') : '-',
                    'status' => $status
                ];
            })->toArray();

        return view('admin.lpj.index', compact('list_lpj'));
    }

    public function detail(Request $request, $id)
    {
        $from = $request->query('from', 'index');
        
        // Flexibly handle lookup by LPJ ID or Kegiatan ID
        $lpj = Lpj::with('items')->find($id);
        if (!$lpj) {
            $kegiatan = Kegiatan::with('lpj')->findOrFail($id);
            $lpj = $kegiatan->lpj;
        } else {
            $kegiatan = Kegiatan::findOrFail($lpj->kegiatan_id);
        }

        $status = 'menunggu_upload';
        if ($lpj) {
            $statusMap = [
                1 => 'menunggu',
                2 => 'revisi',
                3 => 'disetujui',
                4 => 'ditolak',
                5 => 'telah_direvisi',
                6 => 'siap_submit'
            ];
            $status = $statusMap[$lpj->status_id] ?? 'menunggu';
        }

        $kegiatan_nama = $kegiatan->nama_kegiatan;
        
        // Group all Rabs
        $kegiatan = Kegiatan::with('kak.rabs.kategori')->findOrFail($kegiatan->kegiatan_id);
        $rabsGrouped = $kegiatan->kak ? $kegiatan->kak->rabs->groupBy(function($item) {
            return $item->kategori ? $item->kategori->nama_kategori : 'Belanja Barang';
        }) : collect();

        $rab_items = [];
        foreach ($rabsGrouped as $kategoriName => $items) {
            foreach ($items as $index => $rab) {
                $realisasi = 0;
                $catatan_item = null;
                if ($lpj) {
                    $lpjItem = $lpj->items()
                        ->where('kategori_id', $rab->kategori_id)
                        ->skip($index)->first();
                    if ($lpjItem) {
                        $realisasi = (float)$lpjItem->realisasi;
                        $catatan_item = $lpjItem->komentar;
                    }
                }
                $rab_items[$kategoriName][] = [
                    'id' => $rab->rab_item_id,
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => (float)$rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => (float)$rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => (float)$rab->harga,
                    'total' => (float)$rab->total_harga,
                    'realisasi' => $realisasi,
                    'catatan_item' => $catatan_item
                ];
            }
        }

        $catatan_revisi = $lpj ? $lpj->komentar_revisi : null;
        $prodi = $kegiatan->prodi_penyelenggara;
        $kode_mak = $kegiatan->bukti_mak;

        // Ensure the ID passed to form is the kegiatan_id so store() can connect correctly
        $id = $kegiatan->kegiatan_id;

        return view('admin.lpj.detail', compact('id', 'status', 'rab_items', 'kegiatan_nama', 'catatan_revisi', 'from', 'prodi', 'kode_mak'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required',
            'realisasi' => 'required|array',
        ]);

        $kegiatan = Kegiatan::with('kak.rabs.kategori')->findOrFail($request->kegiatan_id);
        $rabsGrouped = $kegiatan->kak ? $kegiatan->kak->rabs->groupBy(function($item) {
            return $item->kategori ? $item->kategori->nama_kategori : 'Belanja Barang';
        }) : collect();

        $grandTotalRealisasi = 0;
        foreach ($request->realisasi as $kategoriName => $items) {
            foreach ($items as $realVal) {
                $grandTotalRealisasi += (float)$realVal;
            }
        }

        $lpj = Lpj::updateOrCreate(
            ['kegiatan_id' => $kegiatan->kegiatan_id],
            [
                'grand_total_realisasi' => $grandTotalRealisasi,
                'submitted_at' => now(),
                'status_id' => 1, // Menunggu Verifikasi
                'komentar_revisi' => null
            ]
        );

        $kategoriMap = [
            'Belanja Barang' => 4,
            'Belanja Perjalanan' => 5,
            'Belanja Jasa' => 6
        ];

        // Backup existing items to preserve unchanged attachments if needed
        $oldItems = $lpj->items()->get();
        $lpj->items()->delete();

        foreach ($request->realisasi as $kategoriName => $items) {
            $kategori_id = $kategoriMap[$kategoriName] ?? 4;
            foreach ($items as $index => $realVal) {
                $filePath = null;

                // Handle file upload
                if ($request->hasFile("bukti.{$kategoriName}.{$index}")) {
                    $file = $request->file("bukti.{$kategoriName}.{$index}");
                    $filePath = $file->store('lpj_bukti', 'public');
                } else {
                    // Retain old file path if existed
                    $oldItem = $oldItems->where('kategori_id', $kategori_id)->skip($index)->first();
                    if ($oldItem) {
                        $filePath = $oldItem->file_bukti;
                    }
                }

                $rabItem = ($rabsGrouped[$kategoriName] ?? collect())[$index] ?? null;

                LpjItem::create([
                    'lpj_id' => $lpj->lpj_id,
                    'kategori_id' => $kategori_id,
                    'uraian' => $rabItem ? $rabItem->uraian : '',
                    'rincian' => $rabItem ? $rabItem->rincian : '',
                    'sat1' => $rabItem ? $rabItem->sat1 : '',
                    'sat2' => $rabItem ? $rabItem->sat2 : '',
                    'vol1' => $rabItem ? $rabItem->vol1 : 1,
                    'vol2' => $rabItem ? $rabItem->vol2 : 1,
                    'harga' => $rabItem ? $rabItem->harga : 0,
                    'total_harga' => $rabItem ? $rabItem->total_harga : 0,
                    'realisasi' => (float)$realVal,
                    'file_bukti' => $filePath,
                ]);
            }
        }

        // Update kegiatan position to Bendahara (posisi_id = 5)
        $kegiatan->update([
            'posisi_id' => 5
        ]);

        return redirect()->route('admin.lpj.index')->with('success_message', 'LPJ berhasil diajukan ke Bendahara.');
    }
}

