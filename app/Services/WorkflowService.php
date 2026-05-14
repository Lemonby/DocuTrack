<?php

namespace App\Services;

use App\Models\Kegiatan;
use App\Models\ProgressHistory;
use App\Models\RevisiComment;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    // Workflow positions (maps to role ordering)
    const POSITION_ADMIN = 1;
    const POSITION_VERIFIKATOR = 2;
    const POSITION_WADIR = 3;
    const POSITION_PPK = 4;
    const POSITION_BENDAHARA = 5;

    // Status constants
    const STATUS_MENUNGGU = 1;
    const STATUS_REVISI = 2;
    const STATUS_DISETUJUI = 3;
    const STATUS_DITOLAK = 4;
    const STATUS_DANA_DIBERIKAN = 5;

    private const WORKFLOW_ROUTING = [
        self::POSITION_ADMIN => self::POSITION_VERIFIKATOR,
        self::POSITION_VERIFIKATOR => self::POSITION_ADMIN,
        self::POSITION_PPK => self::POSITION_WADIR,
        self::POSITION_WADIR => self::POSITION_BENDAHARA,
        self::POSITION_BENDAHARA => self::POSITION_BENDAHARA,
    ];

    private const ROLE_NAMES = [
        self::POSITION_ADMIN => 'Admin',
        self::POSITION_VERIFIKATOR => 'Verifikator',
        self::POSITION_WADIR => 'Wakil Direktur',
        self::POSITION_PPK => 'PPK',
        self::POSITION_BENDAHARA => 'Bendahara',
    ];

    public function getNextPosition(int $currentPosition): int
    {
        return self::WORKFLOW_ROUTING[$currentPosition]
            ?? throw new \InvalidArgumentException("Invalid workflow position: {$currentPosition}");
    }

    public function getPositionName(int $positionId): string
    {
        return self::ROLE_NAMES[$positionId] ?? 'Unknown';
    }

    /**
     * Advance kegiatan to next workflow position.
     */
    public function moveToNextPosition(
        int $kegiatanId,
        int $currentPosition,
        int $newStatus = self::STATUS_DISETUJUI,
        array $additionalData = []
    ): bool {
        $nextPosition = $this->getNextPosition($currentPosition);

        return DB::transaction(function () use ($kegiatanId, $nextPosition, $newStatus, $currentPosition, $additionalData) {
            $kegiatan = Kegiatan::lockForUpdate()->findOrFail($kegiatanId);

            $updateData = [
                'posisi_id' => $nextPosition,
                'status_utama_id' => $newStatus,
            ];

            // Verifikator-specific fields
            if ($currentPosition === self::POSITION_VERIFIKATOR) {
                if (isset($additionalData['kode_mak'])) {
                    $updateData['bukti_mak'] = $additionalData['kode_mak'];
                }
                if (isset($additionalData['dana_disetujui'])) {
                    $updateData['dana_di_setujui'] = $additionalData['dana_disetujui'];
                }
                if (isset($additionalData['umpan_balik'])) {
                    $updateData['umpan_balik_verifikator'] = $additionalData['umpan_balik'];
                }
            }

            $kegiatan->update($updateData);

            $this->recordHistory($kegiatanId, $newStatus, auth()->id());

            return true;
        });
    }

    /**
     * Reject kegiatan — status becomes DITOLAK, stays at current position.
     */
    public function reject(int $kegiatanId, int $currentPosition, string $reason): bool
    {
        return DB::transaction(function () use ($kegiatanId, $currentPosition, $reason) {
            $kegiatan = Kegiatan::lockForUpdate()->findOrFail($kegiatanId);

            $kegiatan->update([
                'posisi_id' => $currentPosition,
                'status_utama_id' => self::STATUS_DITOLAK,
            ]);

            $history = $this->recordHistory($kegiatanId, self::STATUS_DITOLAK, auth()->id());

            RevisiComment::create([
                'progress_history_id' => $history->progress_history_id,
                'komentar_revisi' => $reason,
                'target_tabel' => 'tbl_kegiatan',
                'target_kolom' => 'statusUtamaId',
            ]);

            return true;
        });
    }

    /**
     * Request revision — sends kegiatan back to Admin with REVISI status.
     */
    public function requestRevision(int $kegiatanId, int $currentPosition, string $comments, array $fieldComments = []): bool
    {
        return DB::transaction(function () use ($kegiatanId, $comments, $fieldComments) {
            $kegiatan = Kegiatan::lockForUpdate()->findOrFail($kegiatanId);

            $kegiatan->update([
                'posisi_id' => self::POSITION_ADMIN,
                'status_utama_id' => self::STATUS_REVISI,
            ]);

            $history = $this->recordHistory($kegiatanId, self::STATUS_REVISI, auth()->id());

            // Store field-level revision comments
            if (! empty($fieldComments)) {
                foreach ($fieldComments as $comment) {
                    RevisiComment::create([
                        'progress_history_id' => $history->progress_history_id,
                        'komentar_revisi' => $comment['komentar'] ?? $comments,
                        'target_tabel' => $comment['target_tabel'] ?? null,
                        'target_kolom' => $comment['target_kolom'] ?? null,
                    ]);
                }
            } else {
                RevisiComment::create([
                    'progress_history_id' => $history->progress_history_id,
                    'komentar_revisi' => $comments,
                ]);
            }

            return true;
        });
    }

    private function recordHistory(int $kegiatanId, int $statusId, ?int $userId): ProgressHistory
    {
        return ProgressHistory::create([
            'kegiatan_id' => $kegiatanId,
            'status_id' => $statusId,
            'changed_by_user_id' => $userId,
            'created_at' => now(),
        ]);
    }

    public function getProgress(Kegiatan $kegiatan): int
    {
        return $kegiatan->workflow_progress;
    }
}
