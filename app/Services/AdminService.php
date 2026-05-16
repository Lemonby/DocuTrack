<?php

namespace App\Services;

use App\Models\Admin\AdminModel;
use App\Models\Lpj\LpjModel; // Tambahkan LpjModel jika diperlukan untuk operasi LPJ
use App\Exceptions\BusinessLogicException;
use Exception;

class AdminService
{
    private $adminModel;
    private $lpjModel; // Tambahkan ini jika AdminService akan berinteraksi langsung dengan LPJModel
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->adminModel = new AdminModel($db);
        $this->lpjModel = new LpjModel($db); // Inisialisasi LpjModel
    }

    // Metode eksplisit yang dibutuhkan oleh controller
    public function getDashboardLPJ()
    {
        return $this->adminModel->getDashboardLPJ();
    }

    public function getDetailLPJ(int $lpjId)
    {
        return $this->adminModel->getDetailLPJ($lpjId);
    }

    public function getRABForLPJ(int $kakId, int $lpjId = null)
    {
        return $this->adminModel->getRABForLPJ($kakId, $lpjId);
    }

    // Metode untuk aksi LPJ yang perlu dipindahkan dari AdminPengajuanLpjController
    public function verifikasiLpj(int $lpjId): bool
    {
        // Logika verifikasi LPJ
        // Contoh: Update status LPJ di LpjModel
        // Asumsi LpjModel memiliki metode approve/verify
        $result = $this->lpjModel->updateLpjStatus($lpjId, 'Verified'); // Asumsi 'Verified' adalah status baru
        if (!$result) {
            throw new BusinessLogicException("Gagal memverifikasi LPJ.");
        }
        return $result;
    }

    public function tolakLpj(int $lpjId, string $komentar): bool
    {
        // Use specific method in LpjModel that handles status and comment
        $result = $this->lpjModel->tolakLpj($lpjId, $komentar);
        if (!$result) {
            throw new BusinessLogicException("Gagal menolak LPJ.");
        }
        return $result;
    }

    public function submitRevisiLpj(int $lpjId, array $komentarRevisi): bool
    {
        // Convert array comments to string because LpjModel expects string for simple LPJ revision
        $komentarStr = implode("\n", $komentarRevisi);

        $result = $this->lpjModel->submitRevisiLpj($lpjId, $komentarStr);
        if (!$result) {
            throw new BusinessLogicException("Gagal mengirim revisi LPJ.");
        }
        return $result;
    }

    // Metode lain dari AdminModel yang mungkin digunakan
    public function getAllUsers()
    {
        return $this->adminModel->getAllUsers();
    }

    public function getListJurusan()
    {
        return $this->adminModel->getListJurusan();
    }

    public function getDashboardKAK()
    {
        return $this->adminModel->getDashboardKAK();
    }

    public function getDetailKegiatan($kegiatanId)
    {
        return $this->adminModel->getDetailKegiatan($kegiatanId);
    }

    public function getRABByKAK($kakId)
    {
        return $this->adminModel->getRABByKAK($kakId);
    }

    public function getIndikatorByKAK($kakId)
    {
        return $this->adminModel->getIndikatorByKAK($kakId);
    }

    public function getTahapanByKAK($kakId)
    {
        return $this->adminModel->getTahapanByKAK($kakId);
    }

    public function simpanPengajuan($data)
    {
        return $this->adminModel->simpanPengajuan($data);
    }

    // ... tambahkan metode lain sesuai kebutuhan dari AdminModel
}
