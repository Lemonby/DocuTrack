<?php

namespace App\Services;

use App\Models\WadirModel;
use App\Services\WorkflowService;
use App\Services\LogStatusService;
use Exception;
use Throwable;

class WadirService
{
    private $model;
    private $logStatusService;
    private WorkflowService $workflowService;

    public function __construct($db)
    {
        $this->model = new WadirModel($db);
        $this->logStatusService = new LogStatusService($db);
        $this->workflowService = new WorkflowService($db);
    }

    // Explicit proxy methods to resolve "Method not found" errors
    public function getDashboardStats()
    {
        return $this->model->getDashboardStats();
    }

    public function getDashboardKAK()
    {
        return $this->model->getDashboardKAK();
    }

    public function getListJurusanDistinct()
    {
        return $this->model->getListJurusanDistinct();
    }

    public function getRiwayat()
    {
        return $this->model->getRiwayat();
    }

    public function getDetailKegiatan($kegiatanId)
    {
        return $this->model->getDetailKegiatan($kegiatanId);
    }

    public function getIndikatorByKAK($kakId)
    {
        return $this->model->getIndikatorByKAK($kakId);
    }

    public function getTahapanByKAK($kakId)
    {
        return $this->model->getTahapanByKAK($kakId);
    }

    public function getRABByKAK($kakId)
    {
        return $this->model->getRABByKAK($kakId);
    }

    /**
     * Menyetujui usulan dan mengirim notifikasi.
     *
     * @param int $kegiatanId
     * @param string $rekomendasi
     * @return bool
     */
    public function approveUsulan($kegiatanId, $rekomendasi = '')
    {
        $result = $this->workflowService->moveToNextPosition(
            $kegiatanId,
            WorkflowService::POSITION_WADIR
        );

        if ($result) {
            try {
                // Ambil detail kegiatan untuk mendapatkan userId pengusul
                $kegiatan = $this->model->getDetailKegiatan($kegiatanId);
                if ($kegiatan && isset($kegiatan['userId'])) {
                    $this->logStatusService->createNotification(
                        (int) $kegiatan['userId'],
                        'APPROVAL',
                        "Usulan kegiatan \"{$kegiatan['namaKegiatan']}\" telah disetujui oleh Wakil Direktur dan diteruskan ke Bendahara.",
                        $kegiatanId
                    );
                }
            } catch (Throwable $e) {
                // Jangan gagalkan proses approval hanya karena notifikasi gagal
                error_log("Gagal membuat notifikasi approval Wadir untuk kegiatan ID {$kegiatanId}: " . $e->getMessage());
            }
        }

        return $result;
    }

    public function rejectUsulan(int $kegiatanId, string $alasanPenolakan = ''): bool
    {
        return $this->workflowService->reject(
            $kegiatanId,
            WorkflowService::POSITION_WADIR,
            $alasanPenolakan
        );
    }

    public function reviseUsulan(int $kegiatanId, string $komentarRevisi): bool
    {
        return $this->workflowService->requestRevision(
            $kegiatanId,
            WorkflowService::POSITION_WADIR,
            $komentarRevisi
        );
    }

    // Fallback for any other methods not explicitly defined
    public function __call($name, $arguments)
    {
        if (method_exists($this->model, $name)) {
            return call_user_func_array([$this->model, $name], $arguments);
        }
        throw new Exception("Method {$name} not found in WadirModel or WadirService");
    }
}
