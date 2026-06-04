<?php

namespace App\Http\Controllers\API\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Resources\LpjResource;
use App\Models\Lpj;
use App\Services\LpjService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanLpjController extends Controller
{
    public function __construct(
        private readonly LpjService $lpjService
    ) {}

    public function index(): JsonResponse
    {
        $lpjs = Lpj::with(['kegiatan', 'status', 'items'])
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => LpjResource::collection($lpjs)]);
    }

    public function show(int $id): JsonResponse
    {
        $lpj = Lpj::with(['kegiatan', 'status', 'items.kategori'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => new LpjResource($lpj)]);
    }

    /**
     * Process LPJ: verify, reject, or request revision.
     */
    public function proses(Request $request): JsonResponse
    {
        $request->validate([
            'lpj_id' => 'required|integer|exists:lpjs,lpj_id',
            'aksi' => 'required|in:verify,approve,reject,revise',
            'komentar' => 'required_unless:aksi,verify,approve|string',
            'notes' => 'nullable|string',
            'item_feedback' => 'nullable|array',
        ]);

        $lpjId = $request->input('lpj_id');
        $action = $request->input('aksi');
        $notes = $request->input('notes') ?? $request->input('komentar');
        $itemFeedback = $request->input('item_feedback') ?? [];

        $lpj = Lpj::findOrFail($lpjId);
        $kegiatan = \App\Models\Kegiatan::findOrFail($lpj->kegiatan_id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($kegiatan, $lpj, $action, $notes, $itemFeedback, $request) {
            // 1. Save item feedback
            foreach ($itemFeedback as $itemId => $feedback) {
                $lpjItem = \App\Models\LpjItem::where('lpj_id', $lpj->lpj_id)
                    ->where('lpj_item_id', $itemId)
                    ->first();
                if ($lpjItem) {
                    $lpjItem->update(['komentar' => $feedback]);
                }
            }

            // 2. Perform action
            if ($action === 'approve' || $action === 'verify') {
                $lpj->update([
                    'status_id' => 3, // Disetujui
                    'approved_at' => now(),
                    'komentar_revisi' => null,
                    'komentar_penolakan' => null,
                ]);

                $kegiatan->update([
                    'status_utama_id' => 6, // LPJ Disetujui
                ]);

                \App\Models\ProgressHistory::create([
                    'kegiatan_id' => $kegiatan->kegiatan_id,
                    'status_id' => 6,
                    'changed_by_user_id' => $request->user()->user_id,
                    'created_at' => now(),
                ]);

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

            } elseif ($action === 'revise') {
                $lpj->update([
                    'status_id' => 2, // Revisi
                    'komentar_revisi' => $notes,
                    'approved_at' => null,
                ]);

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
            } elseif ($action === 'reject') {
                $lpj->update([
                    'status_id' => 4, // Ditolak
                    'komentar_penolakan' => $notes,
                    'approved_at' => null,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'LPJ berhasil diproses.',
            'data' => new LpjResource($lpj->fresh(['status', 'items'])),
        ]);
    }
}
