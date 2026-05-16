<?php

namespace App\Services;

use App\Exceptions\BusinessLogicException;
use App\Models\kegiatan\KegiatanModel;
use App\Models\VerifikatorModel;
use App\Services\LogStatusService;
use App\Services\ValidationService;
use App\Services\WorkflowService;
use Throwable;

class VerifikatorService
{
    private VerifikatorModel $verifikatorModel;
    private LogStatusService $logStatusService;
    private ValidationService $validationService;
    private KegiatanModel $kegiatanModel;
    private WorkflowService $workflowService;

    public function __construct($db)
    {
        $this->verifikatorModel = new VerifikatorModel($db);
        $this->logStatusService = new LogStatusService($db);
        $this->validationService = new ValidationService();
        $this->kegiatanModel = new KegiatanModel($db);
        $this->workflowService = new WorkflowService($db);
    }

    public function getDashboardStats()
    {
        return $this->verifikatorModel->getDashboardStats();
    }

    public function getDashboardKAK()
    {
        return $this->verifikatorModel->getDashboardKAK();
    }

    public function getListJurusan()
    {
        return $this->verifikatorModel->getListJurusan();
    }

    public function getRiwayat($userJurusan = null)
    {
        return $this->verifikatorModel->getRiwayat($userJurusan);
    }

    public function getDetailKegiatan($kegiatanId)
    {
        return $this->verifikatorModel->getDetailKegiatan($kegiatanId);
    }

    public function getIndikatorByKAK($kakId)
    {
        return $this->verifikatorModel->getIndikatorByKAK($kakId);
    }

    public function getTahapanByKAK($kakId)
    {
        return $this->verifikatorModel->getTahapanByKAK($kakId);
    }

    public function getRABByKAK($kakId)
    {
        return $this->verifikatorModel->getRABByKAK($kakId);
    }

    public function getProposalMonitoring()
    {
        return $this->verifikatorModel->getProposalMonitoring();
    }

    /**
     * Menyetujui usulan.
     * Moved from VerifikatorModel to VerifikatorService for business logic and notification trigger.
     */
    public function approveUsulan(int $kegiatanId, string $kodeMak, float $danaDisetujui, ?string $catatan = null): bool
    {
        $additionalData = [
            'kodeMak' => $kodeMak,
            'danaDisetujui' => $danaDisetujui,
            'umpanBalik' => $catatan
        ];

        $result = $this->workflowService->moveToNextPosition(
            $kegiatanId,
            WorkflowService::POSITION_VERIFIKATOR,
            WorkflowService::STATUS_DISETUJUI,
            $additionalData
        );

        if ($result) {
            try {
                $kegiatan = $this->verifikatorModel->getDetailKegiatan($kegiatanId);
                if ($kegiatan && isset($kegiatan['userId'])) {
                    $this->logStatusService->createNotification(
                        (int) $kegiatan['userId'],
                        'APPROVAL',
                        "Proposal kegiatan \"{$kegiatan['namaKegiatan']}\" Anda telah disetujui oleh Verifikator dan diteruskan ke PPK.",
                        $kegiatanId
                    );
                }
            } catch (Throwable $e) {
                error_log("Gagal membuat notifikasi persetujuan untuk kegiatan ID {$kegiatanId}: " . $e->getMessage());
            }
        }
        return $result;
    }

    /**
     * Menolak usulan.
     */
    public function rejectUsulan(int $kegiatanId, string $alasanPenolakan = ''): bool
    {
        return $this->workflowService->reject(
            $kegiatanId,
            WorkflowService::POSITION_VERIFIKATOR,
            $alasanPenolakan
        );
    }

    /**
     * Mengirim usulan untuk direvisi oleh Verifikator.
     */
    public function reviseUsulan(int $kegiatanId, array $komentarRevisi): bool
    {
        $this->validationService->validate(['kegiatan_id' => $kegiatanId, 'komentar' => $komentarRevisi], [
            'kegiatan_id' => 'required|numeric',
            'komentar' => 'required|array'
        ]);

        $comments = [];
        foreach($komentarRevisi as $komentar) {
            $comments[] = "Field '{$komentar['targetKolom']}': {$komentar['komentar']}";
        }
        $commentString = implode("\n", $comments);

        return $this->workflowService->requestRevision(
            $kegiatanId,
            WorkflowService::POSITION_VERIFIKATOR,
            $commentString
        );
    }
}
