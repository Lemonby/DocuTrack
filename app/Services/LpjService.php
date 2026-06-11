<?php

namespace App\Services;

use App\Models\Lpj;
use App\Models\LpjItem;
use App\Models\Rab;
use Illuminate\Support\Facades\DB;

class LpjService
{
    /**
     * Submit LPJ with item realisasi values.
     */
    public function submitLpj(int $kegiatanId, array $items): Lpj
    {
        return DB::transaction(function () use ($kegiatanId, $items) {
            $lpj = Lpj::where('kegiatan_id', $kegiatanId)->firstOrFail();

            // Prevent re-submit unless it's a revision
            if ($lpj->submitted_at && $lpj->status_id !== 2) {
                throw new \RuntimeException('LPJ sudah pernah disubmit. Tidak dapat submit ulang.');
            }

            // Verify all evidence files uploaded
            $uploadStatus = $this->getUploadStatus($lpj);
            if ($uploadStatus['total'] > 0 && $uploadStatus['uploaded'] < $uploadStatus['total']) {
                throw new \RuntimeException(
                    "Upload semua bukti terlebih dahulu ({$uploadStatus['uploaded']}/{$uploadStatus['total']})"
                );
            }

            $totalRealisasi = 0;
            foreach ($items as $item) {
                $lpjItem = LpjItem::where('lpj_id', $lpj->lpj_id)
                    ->where('lpj_item_id', $item['id'])
                    ->firstOrFail();

                $realisasi = (float) ($item['realisasi'] ?? 0);
                if ($realisasi < 0) {
                    throw new \InvalidArgumentException('Realisasi tidak boleh negatif.');
                }

                $lpjItem->update(['realisasi' => $realisasi]);
                $totalRealisasi += $realisasi;
            }

            $lpj->update([
                'grand_total_realisasi' => $totalRealisasi,
                'submitted_at' => now(),
                'status_id' => 1, // Menunggu verifikasi Bendahara
            ]);

            return $lpj->fresh(['items']);
        });
    }

    /**
     * Upload evidence file for an LPJ item.
     */
    public function uploadBukti(int $lpjId, int $rabItemId, $file): LpjItem
    {
        $lpj = Lpj::findOrFail($lpjId);
        $rabItem = Rab::findOrFail($rabItemId);

        $path = $file->store('lpj-bukti', 'public');

        $lpjItem = LpjItem::updateOrCreate(
            ['lpj_id' => $lpjId, 'kategori_id' => $rabItem->kategori_id],
            [
                'uraian' => $rabItem->uraian,
                'rincian' => $rabItem->rincian,
                'total_harga' => $rabItem->total_harga,
                'sat1' => $rabItem->sat1,
                'sat2' => $rabItem->sat2,
                'vol1' => $rabItem->vol1,
                'vol2' => $rabItem->vol2,
                'harga' => $rabItem->harga,
                'file_bukti' => $path,
            ]
        );

        return $lpjItem;
    }

    /**
     * Verify/approve an LPJ (Bendahara action).
     */
    public function verifikasi(int $lpjId): Lpj
    {
        $lpj = Lpj::findOrFail($lpjId);
        $lpj->update(['status_id' => 3, 'approved_at' => now()]);

        return $lpj;
    }

    /**
     * Reject an LPJ (Bendahara action).
     */
    public function tolak(int $lpjId, string $komentar): Lpj
    {
        $lpj = Lpj::findOrFail($lpjId);
        $lpj->update(['status_id' => 4, 'komentar_penolakan' => $komentar]);

        return $lpj;
    }

    /**
     * Request revision on LPJ.
     */
    public function requestRevision(int $lpjId, string $komentar): Lpj
    {
        $lpj = Lpj::findOrFail($lpjId);
        $lpj->update(['status_id' => 2, 'komentar_revisi' => $komentar]);

        return $lpj;
    }

    private function getUploadStatus(Lpj $lpj): array
    {
        $total = $lpj->items()->count();
        $uploaded = $lpj->items()->whereNotNull('file_bukti')->count();

        return ['total' => $total, 'uploaded' => $uploaded];
    }
}
