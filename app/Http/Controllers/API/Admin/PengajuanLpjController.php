<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Http\Resources\LpjResource;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Models\LpjItem;
use App\Models\Rab;
use App\Services\LpjService;
use App\Services\WorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanLpjController extends Controller
{
    public function __construct(
        private readonly LpjService $lpjService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->user_id;
        $jurusan = $request->user()->nama_jurusan;

        // Follow web logic from LpjController::index
        $query = Kegiatan::with(['statusUtama', 'user', 'lpj.status'])
            ->whereIn('status_utama_id', [
                WorkflowService::STATUS_DANA_DIBERIKAN,
                6, // LPJ Disetujui
                8, // Selesai
            ]);

        // For "admin" (Mahasiswa), only show their own LPJs.
        $query->where('user_id', $userId);

        $kegiatans = $query->latest()->paginate(15);

        // Map Kegiatans to a format that the mobile app can understand as "Lpj"
        $mapped = $kegiatans->getCollection()->map(function ($kegiatan) {
            $lpj = $kegiatan->lpj;
            
            // Status labeling consistent with web logic
            $statusLabel = 'menunggu_upload';
            if ($kegiatan->status_utama_id == 8) {
                $statusLabel = 'selesai';
            } elseif ($lpj) {
                if ($lpj->status_id == 1) {
                    $statusLabel = $lpj->submitted_at ? 'menunggu' : 'menunggu_upload';
                } elseif ($lpj->status_id == 2) {
                    $statusLabel = 'revisi';
                } elseif ($lpj->status_id == 3) {
                    $statusLabel = 'disetujui';
                } elseif ($lpj->status_id == 4) {
                    $statusLabel = 'ditolak';
                }
            }

            return [
                'id' => $lpj ? $lpj->lpj_id : $kegiatan->kegiatan_id,
                'lpj_id' => $lpj ? $lpj->lpj_id : $kegiatan->kegiatan_id,
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'kegiatan' => [
                    'id' => $kegiatan->kegiatan_id,
                    'nama_kegiatan' => $kegiatan->nama_kegiatan,
                    'prodi_penyelenggara' => $kegiatan->prodi_penyelenggara,
                    'pemilik_kegiatan' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                ],
                'status' => [
                    'nama' => $statusLabel,
                ],
                'submitted_at' => $lpj ? $lpj->submitted_at?->toISOString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $mapped,
            'meta' => [
                'current_page' => $kegiatans->currentPage(),
                'last_page' => $kegiatans->lastPage(),
                'total' => $kegiatans->total(),
            ]
        ]);
    }

    public function show(int $id): JsonResponse
    {
        // $id could be lpj_id or kegiatan_id
        $lpj = Lpj::with(['kegiatan.user', 'status', 'items.kategori'])->find($id);
        
        if ($lpj) {
            return response()->json([
                'success' => true, 
                'data' => new LpjResource($lpj->load(['kegiatan.user', 'status', 'items.kategori']))
            ]);
        }

        // Try finding by kegiatan_id if not found by lpj_id
        $kegiatan = Kegiatan::with(['statusUtama', 'user', 'kak.rabs.kategori', 'lpj.status', 'lpj.items.kategori'])->find($id);
        if (!$kegiatan) {
             return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // If it has LPJ but we didn't find it by $id (meaning $id was kegiatan_id)
        if ($kegiatan->lpj) {
            return response()->json([
                'success' => true, 
                'data' => new LpjResource($kegiatan->lpj->load(['kegiatan.user', 'status', 'items.kategori']))
            ]);
        }

        // Return a virtual LPJ object for activities that haven't started their LPJ yet
        $statusLabel = 'menunggu_upload';
        if ($kegiatan->status_utama_id == 8) {
            $statusLabel = 'selesai';
        }

        $items = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $items[] = [
                    'id' => $rab->rab_item_id, // Virtual ID using rab_item_id
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'nominal' => 0,
                    'file_bukti' => null,
                    'kategori' => [
                        'nama_kategori' => $rab->kategori->nama_kategori ?? 'Lainnya',
                    ],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $kegiatan->kegiatan_id,
                'lpj_id' => $kegiatan->kegiatan_id,
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'kegiatan' => new KegiatanResource($kegiatan),
                'status' => ['nama' => $statusLabel],
                'submitted_at' => null,
                'items' => $items,
            ]
        ]);
    }

    public function uploadBukti(Request $request): JsonResponse
    {
        $request->validate([
            'lpj_id' => 'required|integer', 
            'rab_item_id' => 'required|integer', 
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $lpjId = $request->input('lpj_id');
        $itemId = $request->input('rab_item_id');
        
        $lpj = Lpj::where('lpj_id', $lpjId)->orWhere('kegiatan_id', $lpjId)->first();

        if (!$lpj) {
            $kegiatan = Kegiatan::findOrFail($lpjId);
            $lpj = Lpj::create([
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'status_id' => 1, // Menunggu Verifikasi (Draft)
                'tenggat_lpj' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->copy()->addDays(14) : now()->addDays(14),
            ]);
        }

        $lpjItem = LpjItem::where('lpj_id', $lpj->lpj_id)->where('lpj_item_id', $itemId)->first();
        $path = $request->file('file')->store('lpj-bukti', 'public');

        if ($lpjItem) {
            $lpjItem->update(['file_bukti' => $path]);
        } else {
            $rabItem = Rab::findOrFail($itemId);
            $lpjItem = LpjItem::updateOrCreate(
                [
                    'lpj_id' => $lpj->lpj_id, 
                    'uraian' => $rabItem->uraian, 
                    'rincian' => $rabItem->rincian
                ],
                [
                    'kategori_id' => $rabItem->kategori_id,
                    'total_harga' => $rabItem->total_harga,
                    'sat1' => $rabItem->sat1,
                    'sat2' => $rabItem->sat2,
                    'vol1' => $rabItem->vol1,
                    'vol2' => $rabItem->vol2,
                    'harga' => $rabItem->harga,
                    'file_bukti' => $path,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Bukti berhasil diupload.',
            'data' => ['file_bukti' => $lpjItem->file_bukti, 'lpj_id' => $lpj->lpj_id],
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'kegiatan_id' => 'required|integer|exists:kegiatans,kegiatan_id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.realisasi' => 'required|numeric|min:0',
        ]);

        $kegiatanId = $request->input('kegiatan_id');
        $lpj = Lpj::where('kegiatan_id', $kegiatanId)->first();

        if (!$lpj) {
             $kegiatan = Kegiatan::findOrFail($kegiatanId);
             $lpj = Lpj::create([
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'status_id' => 1,
                'tenggat_lpj' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->copy()->addDays(14) : now()->addDays(14),
            ]);
        }

        // Ensure all items are synchronized from virtual IDs (rab_item_id) to LPJ items
        $processedItems = [];
        foreach ($request->input('items') as $item) {
            $lpjItem = LpjItem::where('lpj_id', $lpj->lpj_id)
                ->where('lpj_item_id', $item['id'])
                ->first();

            if (!$lpjItem) {
                // If not found by lpj_item_id, it might be a virtual ID (rab_item_id)
                $rab = Rab::find($item['id']);
                if ($rab) {
                    $lpjItem = LpjItem::updateOrCreate(
                        ['lpj_id' => $lpj->lpj_id, 'uraian' => $rab->uraian, 'rincian' => $rab->rincian],
                        [
                            'kategori_id' => $rab->kategori_id,
                            'total_harga' => $rab->total_harga,
                            'sat1' => $rab->sat1,
                            'sat2' => $rab->sat2,
                            'vol1' => $rab->vol1,
                            'vol2' => $rab->vol2,
                            'harga' => $rab->harga,
                            'realisasi' => $item['realisasi'],
                        ]
                    );
                }
            } else {
                $lpjItem->update(['realisasi' => $item['realisasi']]);
            }
            
            if ($lpjItem) {
                $processedItems[] = [
                    'id' => $lpjItem->lpj_item_id,
                    'realisasi' => $item['realisasi'],
                ];
            }
        }

        $lpj = $this->lpjService->submitLpj(
            $kegiatanId,
            $processedItems
        );

        return response()->json([
            'success' => true,
            'message' => 'LPJ berhasil diajukan ke Bendahara.',
            'data' => new LpjResource($lpj->load(['kegiatan.user', 'status', 'items.kategori'])),
        ]);
    }
}
