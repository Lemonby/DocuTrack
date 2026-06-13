<?php

namespace App\Services;

use App\Models\Kegiatan;
use App\Models\LogStatus;
use App\Models\ProgressHistory;
use App\Models\RevisiComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class WorkflowService
{
    // Workflow positions (maps to role ordering)
    const POSITION_ADMIN = 1;

    const POSITION_VERIFIKATOR = 2;

    const POSITION_PPK = 3;

    const POSITION_WADIR = 4;

    const POSITION_BENDAHARA = 5;

    // Status constants
    const STATUS_MENUNGGU = 1;

    const STATUS_REVISI = 2;

    const STATUS_DISETUJUI = 3;

    const STATUS_DITOLAK = 4;

    const STATUS_DANA_DIBERIKAN = 5;

    const STATUS_LPJ_DISETUJUI = 6;

    const STATUS_TELAH_DIVERIFIKASI = 7;

    const STATUS_SELESAI = 8;

    const STATUS_DANA_DIBERIKAN_SEBAGIAN = 9;

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

        // Under Opsi 2: When moving along PPK, Wadir, or Bendahara desks,
        // reset status to STATUS_MENUNGGU (1) so it shows as pending for the next user.
        if (in_array($currentPosition, [self::POSITION_PPK, self::POSITION_WADIR, self::POSITION_BENDAHARA])) {
            $newStatus = self::STATUS_MENUNGGU;
        }

        return DB::transaction(function () use ($kegiatanId, $nextPosition, $newStatus, $currentPosition, $additionalData) {
            $kegiatan = Kegiatan::lockForUpdate()->findOrFail($kegiatanId); // ini adalah update data dari verifikator

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

            // Create notification log in log_statuses for the owner
            $roleName = $this->getPositionName($currentPosition);
            LogStatus::create([
                'user_id' => $kegiatan->user_id,
                'tipe_log' => 'APPROVED',
                'id_referensi' => $kegiatanId,
                'status' => 'BELUM_DIBACA',
                'konten_json' => [
                    'judul' => 'Usulan Disetujui',
                    'pesan' => "Usulan \"{$kegiatan->nama_kegiatan}\" telah disetujui oleh {$roleName}.",
                    'link' => '/admin/pengajuan-kegiatan',
                ],
            ]);

            // Create log status for the actor (Verifikator/PPK/Wadir)
            $actorUserId = Session::get('user_id') ?? auth()->id();
            if ($actorUserId && $actorUserId !== $kegiatan->user_id) {
                $actorLink = match ($currentPosition) {
                    self::POSITION_VERIFIKATOR => "/verifikator/telaah/show/{$kegiatanId}",
                    self::POSITION_PPK => "/ppk/kegiatan/show/{$kegiatanId}",
                    self::POSITION_WADIR => "/wadir/kegiatan/show/{$kegiatanId}",
                    default => '#'
                };
                LogStatus::create([
                    'user_id' => $actorUserId,
                    'tipe_log' => 'APPROVAL',
                    'id_referensi' => $kegiatanId,
                    'status' => 'DIBACA',
                    'konten_json' => [
                        'judul' => 'Persetujuan Berhasil',
                        'pesan' => "Anda telah menyetujui usulan \"{$kegiatan->nama_kegiatan}\" sebagai {$roleName}.",
                        'link' => $actorLink,
                    ],
                ]);
            }

            // Create activity log
            app(ActivityLogService::class)->log(
                userId: $actorUserId ?? $kegiatan->user_id,
                action: 'APPROVE_KEGIATAN',
                category: 'workflow',
                entityType: 'Kegiatan',
                entityId: $kegiatanId,
                description: "Menyetujui usulan kegiatan: \"{$kegiatan->nama_kegiatan}\" sebagai {$roleName}.",
                request: request()
            );

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

            // Create notification log in log_statuses for the owner
            $roleName = $this->getPositionName($currentPosition);
            LogStatus::create([
                'user_id' => $kegiatan->user_id,
                'tipe_log' => 'REJECTION',
                'id_referensi' => $kegiatanId,
                'status' => 'BELUM_DIBACA',
                'konten_json' => [
                    'judul' => 'Usulan Ditolak',
                    'pesan' => "Usulan \"{$kegiatan->nama_kegiatan}\" telah ditolak oleh {$roleName}. Alasan: {$reason}",
                    'link' => '/admin/pengajuan-kegiatan',
                ],
            ]);

            // Create log status for the actor (Verifikator/PPK/Wadir)
            $actorUserId = Session::get('user_id') ?? auth()->id();
            if ($actorUserId && $actorUserId !== $kegiatan->user_id) {
                $actorLink = match ($currentPosition) {
                    self::POSITION_VERIFIKATOR => "/verifikator/telaah/show/{$kegiatanId}",
                    self::POSITION_PPK => "/ppk/kegiatan/show/{$kegiatanId}",
                    self::POSITION_WADIR => "/wadir/kegiatan/show/{$kegiatanId}",
                    default => '#'
                };
                LogStatus::create([
                    'user_id' => $actorUserId,
                    'tipe_log' => 'REJECTION',
                    'id_referensi' => $kegiatanId,
                    'status' => 'DIBACA',
                    'konten_json' => [
                        'judul' => 'Penolakan Berhasil',
                        'pesan' => "Anda telah menolak usulan \"{$kegiatan->nama_kegiatan}\" sebagai {$roleName}.",
                        'link' => $actorLink,
                    ],
                ]);
            }

            // Create activity log
            app(ActivityLogService::class)->log(
                userId: $actorUserId ?? $kegiatan->user_id,
                action: 'REJECT_KEGIATAN',
                category: 'workflow',
                entityType: 'Kegiatan',
                entityId: $kegiatanId,
                description: "Menolak usulan kegiatan: \"{$kegiatan->nama_kegiatan}\" sebagai {$roleName}. Alasan: {$reason}",
                request: request()
            );

            return true;
        });
    }

    /**
     * Request revision — sends kegiatan back to Admin with REVISI status.
     */
    public function requestRevision(int $kegiatanId, int $currentPosition, string $comments, array $fieldComments = []): bool
    {
        return DB::transaction(function () use ($kegiatanId, $currentPosition, $comments, $fieldComments) {
            $kegiatan = Kegiatan::lockForUpdate()->findOrFail($kegiatanId);

            $kegiatan->update([
                'posisi_id' => self::POSITION_ADMIN,
                'status_utama_id' => self::STATUS_REVISI,
            ]);

            $history = $this->recordHistory($kegiatanId, self::STATUS_REVISI, auth()->id());

            // Store field-level revision comments
            if (!empty($fieldComments)) {
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

            // Create notification log in log_statuses for the owner
            $roleName = $this->getPositionName($currentPosition);
            LogStatus::create([
                'user_id' => $kegiatan->user_id,
                'tipe_log' => 'REVISION',
                'id_referensi' => $kegiatanId,
                'status' => 'BELUM_DIBACA',
                'konten_json' => [
                    'judul' => 'Revisi Diperlukan',
                    'pesan' => "Usulan \"{$kegiatan->nama_kegiatan}\" memerlukan revisi dari {$roleName}. Catatan: {$comments}",
                    'link' => '/admin/pengajuan-kegiatan',
                ],
            ]);

            // Create activity log for the verifikator/reviewer who requested the revision
            $actorUserId = Session::get('user_id') ?? auth()->id();
            if ($actorUserId && $actorUserId !== $kegiatan->user_id) {
                $actorLink = match ($currentPosition) {
                    self::POSITION_VERIFIKATOR => "/verifikator/telaah/show/{$kegiatanId}",
                    self::POSITION_PPK => "/ppk/kegiatan/show/{$kegiatanId}",
                    self::POSITION_WADIR => "/wadir/kegiatan/show/{$kegiatanId}",
                    default => '#'
                };
                LogStatus::create([
                    'user_id' => $actorUserId,
                    'tipe_log' => 'REVISION',
                    'id_referensi' => $kegiatanId,
                    'status' => 'DIBACA',
                    'konten_json' => [
                        'judul' => 'Revisi KAK Berhasil Dikirim',
                        'pesan' => "Permintaan revisi untuk usulan \"{$kegiatan->nama_kegiatan}\" berhasil dikirim. Catatan: {$comments}",
                        'link' => $actorLink,
                    ],
                ]);
            }

            // Create activity log
            app(ActivityLogService::class)->log(
                userId: $actorUserId ?? $kegiatan->user_id,
                action: 'REQUEST_REVISION',
                category: 'workflow',
                entityType: 'Kegiatan',
                entityId: $kegiatanId,
                description: "Meminta revisi usulan kegiatan: \"{$kegiatan->nama_kegiatan}\" sebagai {$roleName}. Catatan: {$comments}",
                request: request()
            );

            return true;
        });
    }

    private function recordHistory(int $kegiatanId, int $statusId, ?int $userId): ProgressHistory
    {
        $realUserId = Session::get('user_id') ?? $userId ?? 1;

        return ProgressHistory::create([
            'kegiatan_id' => $kegiatanId,
            'status_id' => $statusId,
            'changed_by_user_id' => $realUserId,
            'created_at' => now(),
        ]);
    }

    public function getProgress(Kegiatan $kegiatan): int
    {
        return $kegiatan->workflow_progress;
    }
}
