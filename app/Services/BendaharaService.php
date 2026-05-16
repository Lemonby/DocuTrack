<?php

namespace App\Services;

use App\Models\Bendahara\BendaharaModel;
use App\Services\WorkflowService;
use Exception;

class BendaharaService
{
    private $model;
    private WorkflowService $workflowService;

    public function __construct($db)
    {
        $this->model = new BendaharaModel($db);
        $this->workflowService = new WorkflowService($db);
    }

    public function getDashboardStatistik()
    {
        return $this->model->getDashboardStats();
    }

    public function getListKegiatanDashboard($limit = 10)
    {
        $data = $this->model->getListKegiatanDashboard($limit);
        
        // ✅ FIX: Map database field names to JavaScript expected field names
        foreach ($data as &$item) {
            // Map ID field for JavaScript compatibility
            $item['id'] = $item['kegiatanId'] ?? null;
            
            // Determine disbursement status based on the new total_dicairkan field from the model
            if (isset($item['total_dicairkan']) && $item['total_dicairkan'] > 0) {
                $item['status'] = 'Sudah Dicairkan';
            } else {
                $item['status'] = 'Belum Dicairkan';
            }
            
            // Map other expected fields for consistency
            $item['nama'] = $item['namaKegiatan'] ?? 'N/A';
            $item['nama_kegiatan'] = $item['namaKegiatan'] ?? 'N/A'; // Pass original too
            $item['nama_mahasiswa'] = $item['pemilikKegiatan'] ?? 'N/A';
            $item['pengusul'] = $item['pemilikKegiatan'] ?? 'N/A'; // Pass original/alias
            $item['nim'] = $item['nimPelaksana'] ?? '-';
            $item['prodi'] = $item['prodiPenyelenggara'] ?? '-';
            $item['jurusan'] = $item['jurusanPenyelenggara'] ?? '-';
            $item['tanggal_pengajuan'] = $item['createdAt'] ?? null;
        }
        
        return $data;
    }

    public function getAntrianLPJ()
    {
        $data = $this->model->getAntrianLPJ();
        
        // ✅ FIX: Map database field names to JavaScript expected field names
        foreach ($data as &$item) {
            // Map status field
            if (!isset($item['status']) && isset($item['status_text'])) {
                $item['status'] = $item['status_text'];
            }
            if (empty($item['status'])) {
                $item['status'] = 'Menunggu';
            }
            
            // Map field names for JavaScript compatibility
            $item['id'] = $item['lpjId'] ?? null;
            $item['nama'] = $item['namaKegiatan'] ?? 'N/A';
            $item['nama_kegiatan'] = $item['namaKegiatan'] ?? 'N/A'; // Pass original too
            $item['nama_mahasiswa'] = $item['pemilikKegiatan'] ?? 'N/A';
            $item['pengusul'] = $item['pemilikKegiatan'] ?? 'N/A'; // Pass original/alias
            $item['nim'] = $item['nimPelaksana'] ?? '-';
            $item['prodi'] = $item['prodiPenyelenggara'] ?? '-';
            $item['jurusan'] = $item['jurusanPenyelenggara'] ?? '-';
            $item['tanggal_pengajuan'] = $item['submittedAt'] ?? null;
            $item['tenggat_lpj'] = $item['tenggatLpj'] ?? null;
        }
        
        return $data;
    }

    public function getAllLPJHistory()
    {
        return $this->model->getAntrianLPJ(); // Currently using same for history or implement specific in model
    }

    public function getRiwayatVerifikasi()
    {
        $data = $this->model->getRiwayatVerifikasi();
        
        // Map fields if necessary for frontend
        foreach ($data as &$item) {
            $item['tanggal'] = $item['tanggal_verifikasi'] ?? null;
            // Ensure ID is present
            $item['id'] = $item['id'] ?? $item['kegiatanId'] ?? null;
            // Map prodi if not already set (though query aliases it as 'prodi')
            $item['prodi'] = $item['prodi'] ?? $item['prodiPenyelenggara'] ?? '-';
            // Map nim
            $item['nim'] = $item['nim'] ?? $item['nimPelaksana'] ?? '-';
        }
        
        return $data;
    }

    public function getRiwayatVerifikasiLPJ()
    {
        return []; // Keep as empty array if model doesn't have it yet, but remove 'Stub' comment
    }

    public function getRiwayatPencairanDana()
    {
        return $this->model->getRiwayatPencairan(); 
    }

    public function getListJurusan()
    {
        return $this->model->getListJurusan();
    }

    public function getDetailLPJ($lpjId)
    {
        return $this->model->getDetailLPJ($lpjId);
    }

    public function getLPJItems($lpjId)
    {
        return $this->model->getLPJItems($lpjId);
    }

    public function approveLPJ($lpjId)
    {
        return $this->model->approveLPJ($lpjId);
    }

    public function reviseLPJ($lpjId, $komentar, $catatan)
    {
        return $this->model->reviseLPJ($lpjId, $komentar, $catatan);
    }

    public function rejectLPJ($lpjId, $alasan)
    {
        return $this->model->rejectLPJ($lpjId, $alasan);
    }

    // Pencairan Dana methods
    public function getDetailPencairan($kegiatanId)
    {
        return $this->model->getDetailPencairan($kegiatanId);
    }

    public function cairkanDana($kegiatanId, $dataPencairan)
    {
        return $this->model->cairkanDana($kegiatanId, $dataPencairan);
    }
}