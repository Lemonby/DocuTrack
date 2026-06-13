<?php

namespace App\Services;

use App\Models\Kegiatan;
use App\Models\LogStatus;
use App\Models\Lpj;
use App\Models\ProgressHistory;
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
            
            $metode = $data['metode_pencairan'] ?? 'penuh';
            $catatan = $data['catatan_bendahara'] ?? 'defaulth nya jir ini (service)';
            $jumlahCair = (float) ($data['jumlah_cair'] ?? 0);
            $isFinal = filter_var($data['is_final'] ?? true, FILTER_VALIDATE_BOOLEAN);

            $tahapKe = $kegiatan->tahapanPencairans()->count() + 1;
            
            TahapanPencairan::create([
                'kegiatan_id' => $kegiatanId,
                'tgl_pencairan' => now()->toDateString(),
                'termin' => $metode === 'bertahap' ? "Termin $tahapKe" : 'Pencairan Penuh',
                'nominal' => $jumlahCair,
                'catatan' => $catatan,
                'created_by' => $userId,
            ]);

            $totalDicairkan = (float) ($kegiatan->jumlah_dicairkan ?? 0) + $jumlahCair;

            $updateData = [
                'tanggal_pencairan' => now()->toDateString(),
                'jumlah_dicairkan' => $totalDicairkan,
                'metode_pencairan' => $metode,
                'catatan_bendahara' => $catatan,
            ];

            if ($metode === 'penuh') {
                $updateData['status_utama_id'] = WorkflowService::STATUS_DANA_DIBERIKAN;
                $updateData['posisi_id'] = WorkflowService::POSITION_ADMIN; // Lanjut ke Admin/Pengusul
            } 

            if ($metode === 'bertahap') {
                $updateData['status_utama_id'] = WorkflowService::STATUS_DANA_DIBERIKAN_SEBAGIAN;
                $updateData['posisi_id'] = WorkflowService::POSITION_ADMIN; // Lanjut ke Admin/Pengusul
            }

            $kegiatan->update($updateData);

            if ($isFinal || $metode === 'penuh') {
                // Record history
                ProgressHistory::create([
                    'kegiatan_id' => $kegiatanId,
                    'status_id' => WorkflowService::STATUS_DANA_DIBERIKAN,
                    'changed_by_user_id' => $userId,
                    'created_at' => now(),
                ]);

                // Create/update LPJ placeholder with deadline
                $tenggatLpj = $this->calculateLpjDeadline(now()->toDateString());
                Lpj::updateOrCreate(
                    ['kegiatan_id' => $kegiatanId],
                    ['tenggat_lpj' => $tenggatLpj, 'status_id' => 1]
                );

                // Create notification log in log_statuses for the owner
                LogStatus::create([
                    'user_id' => $kegiatan->user_id,
                    'tipe_log' => 'APPROVAL',
                    'id_referensi' => $kegiatanId,
                    'status' => 'BELUM_DIBACA',
                    'konten_json' => [
                        'judul' => 'Dana Kegiatan Cair',
                        'pesan' => "Dana untuk kegiatan \"{$kegiatan->nama_kegiatan}\" telah dicairkan oleh Bendahara. Silakan upload LPJ sebelum tenggat waktu.",
                        'link' => '/admin/pengajuan-lpj',
                    ],
                ]);
            } else {
                // Not final, just log partial
                LogStatus::create([
                    'user_id' => $kegiatan->user_id,
                    'tipe_log' => 'APPROVAL',
                    'id_referensi' => $kegiatanId,
                    'status' => 'BELUM_DIBACA',
                    'konten_json' => [
                        'judul' => 'Pencairan Dana Tahap',
                        'pesan' => "Dana untuk kegiatan \"{$kegiatan->nama_kegiatan}\" telah dicairkan (Termin $tahapKe).",
                        'link' => "/bendahara/pencairan-dana/show/{$kegiatanId}",
                    ],
                ]);
            }

            // Create notification log in log_statuses for the actor (Bendahara)
            if ($userId && $userId !== $kegiatan->user_id) {
                LogStatus::create([
                    'user_id' => $userId,
                    'tipe_log' => 'APPROVAL',
                    'id_referensi' => $kegiatanId,
                    'status' => 'DIBACA',
                    'konten_json' => [
                        'judul' => 'Pencairan Dana Berhasil',
                        'pesan' => "Dana untuk kegiatan \"{$kegiatan->nama_kegiatan}\" berhasil dicairkan.",
                        'link' => "/bendahara/pencairan-dana/show/{$kegiatanId}",
                    ],
                ]);
            }

            // Create activity log
            app(ActivityLogService::class)->log(
                userId: $userId,
                action: 'DISBURSE_FUND',
                category: 'financial',
                entityType: 'Kegiatan',
                entityId: $kegiatanId,
                description: "Bendahara mencairkan dana kegiatan: \"{$kegiatan->nama_kegiatan}\" dengan metode {$metode}.",
                request: request()
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
