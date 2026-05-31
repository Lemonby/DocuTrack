<?php

namespace App\Services;

use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Models\LpjItem;
use App\Models\TahapanPencairan;
use Illuminate\Support\Facades\DB;

class PencairanService
{
    /**
     * Process fund disbursement (full or staged).
     */
    public function cairkanDana(int $kegiatanId, array $data, int $userId): Kegiatan
    {
        return DB::transaction(function () use ($kegiatanId, $data, $userId) {
            $kegiatan = Kegiatan::lockForUpdate()->findOrFail($kegiatanId);
            $metode = $data['metode'] ?? 'penuh';
            $catatan = $data['catatan'] ?? '';

            if ($metode === 'bertahap') {
                $totalDicairkan = 0;

                foreach ($data['tahapan'] as $index => $tahap) {
                    $nominal = (float) $tahap['nominal'];
                    if ($nominal <= 0 || empty($tahap['tanggal'])) {
                        throw new \InvalidArgumentException("Data tahap " . ($index + 1) . " tidak valid");
                    }

                    TahapanPencairan::create([
                        'kegiatan_id' => $kegiatanId,
                        'tgl_pencairan' => $tahap['tanggal'],
                        'termin' => $tahap['termin'] ?? 'Termin ' . ($index + 1),
                        'nominal' => $nominal,
                        'catatan' => $catatan,
                        'created_by' => $userId,
                    ]);

                    $totalDicairkan += $nominal;
                }

                $kegiatan->update([
                    'tanggal_pencairan' => now()->toDateString(),
                    'jumlah_dicairkan' => $totalDicairkan,
                    'metode_pencairan' => 'bertahap',
                    'catatan_bendahara' => $catatan,
                    'status_utama_id' => WorkflowService::STATUS_DANA_DIBERIKAN,
                    'posisi_id' => WorkflowService::POSITION_ADMIN,
                ]);

                $tanggalTerakhir = now()->toDateString();
            } else {
                $jumlah = (float) ($data['jumlah'] ?? 0);
                $tanggalCair = $data['tanggal'] ?? now()->toDateString();

                $kegiatan->update([
                    'tanggal_pencairan' => now()->toDateString(),
                    'jumlah_dicairkan' => $jumlah,
                    'metode_pencairan' => 'penuh',
                    'catatan_bendahara' => $catatan,
                    'status_utama_id' => WorkflowService::STATUS_DANA_DIBERIKAN,
                    'posisi_id' => WorkflowService::POSITION_ADMIN,
                ]);

                TahapanPencairan::create([
                    'kegiatan_id' => $kegiatanId,
                    'tgl_pencairan' => $tanggalCair,
                    'termin' => 'Pencairan Penuh',
                    'nominal' => $jumlah,
                    'catatan' => $catatan,
                    'created_by' => $userId,
                ]);

                $tanggalTerakhir = now()->toDateString();
            }

            // Record history
            \App\Models\ProgressHistory::create([
                'kegiatan_id' => $kegiatanId,
                'status_id' => WorkflowService::STATUS_DANA_DIBERIKAN,
                'changed_by_user_id' => $userId,
                'created_at' => now(),
            ]);

            // Create/update LPJ placeholder with deadline
            $tenggatLpj = $this->calculateLpjDeadline($tanggalTerakhir);
            Lpj::updateOrCreate(
                ['kegiatan_id' => $kegiatanId],
                ['tenggat_lpj' => $tenggatLpj, 'status_id' => 1]
            );

            return $kegiatan->fresh();
        });
    }

    /**
     * Calculate LPJ deadline: 14 calendar days from start date.
     */
    private function calculateLpjDeadline(string $startDate): string
    {
        $date = new \DateTime($startDate);
        $date->modify('+14 days');
        return $date->format('Y-m-d');
    }
}
