<?php

namespace App\Services;

use App\Model\KakModel;
use App\Exceptions\ValidationException;
use Exception;

/**
 * KakService - Business logic untuk KAK (Kerangka Acuan Kegiatan)
 *
 * Service layer untuk KAK business rules dan data orchestration.
 *
 * @category Service
 * @package  DocuTrack\Services
 * @version  2.0.0
 */
class KakService
{
    /**
     * @var mysqli Database connection
     */
    private $db;

    /**
     * @var KakModel
     */
    private $kakModel;

    /**
     * Constructor
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->kakModel = new KakModel($db);
    }

    /**
     * Get indikator by KAK ID
     *
     * @param int $kakId
     * @return array
     */
    public function getIndikatorByKAK($kakId)
    {
        $query = "SELECT 
                    bulan, 
                    indikatorKeberhasilan as nama, 
                    targetPersen as target 
                FROM tbl_indikator_kak 
                WHERE kakId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    /**
     * Get tahapan pelaksanaan by KAK ID
     *
     * @param int $kakId
     * @return array
     */
    public function getTahapanByKAK($kakId)
    {
        $query = "SELECT namaTahapan FROM tbl_tahapan_pelaksanaan WHERE kakId = ? ORDER BY tahapanId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['namaTahapan'];
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    /**
     * Get komentar revisi terbaru
     *
     * @param int $kegiatanId
     * @return array
     */
    public function getKomentarTerbaru($kegiatanId)
    {
        $queryHistory = "SELECT ph.progressHistoryId 
                         FROM tbl_progress_history ph 
                         WHERE ph.kegiatanId = ? 
                         AND ph.statusId = 2
                         ORDER BY ph.progressHistoryId DESC 
                         LIMIT 1";

        $stmtHistory = mysqli_prepare($this->db, $queryHistory);
        mysqli_stmt_bind_param($stmtHistory, "i", $kegiatanId);
        mysqli_stmt_execute($stmtHistory);
        $resultHistory = mysqli_stmt_get_result($stmtHistory);
        $history = mysqli_fetch_assoc($resultHistory);
        mysqli_stmt_close($stmtHistory);

        if (!$history) {
            return [];
        }

        $historyId = $history['progressHistoryId'];

        $queryKomentar = "SELECT targetKolom, komentarRevisi 
                          FROM tbl_revisi_comment 
                          WHERE progressHistoryId = ?";

        $stmtKomentar = mysqli_prepare($this->db, $queryKomentar);
        mysqli_stmt_bind_param($stmtKomentar, "i", $historyId);
        mysqli_stmt_execute($stmtKomentar);
        $resultKomentar = mysqli_stmt_get_result($stmtKomentar);

        $komentar = [];
        while ($row = mysqli_fetch_assoc($resultKomentar)) {
            if (!empty($row['targetKolom'])) {
                $komentar[$row['targetKolom']] = $row['komentarRevisi'];
            }
        }
        mysqli_stmt_close($stmtKomentar);

        return $komentar;
    }

    /**
     * Get komentar penolakan terbaru
     *
     * @param int $kegiatanId
     * @return string
     */
    public function getKomentarPenolakan($kegiatanId)
    {
        $query = "SELECT rc.komentarRevisi 
                  FROM tbl_revisi_comment rc
                  JOIN tbl_progress_history ph ON rc.progressHistoryId = ph.progressHistoryId
                  WHERE ph.kegiatanId = ? 
                  AND ph.statusId = 4
                  AND rc.targetKolom IS NULL
                  ORDER BY ph.progressHistoryId DESC 
                  LIMIT 1";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row['komentarRevisi'] ?? '';
    }
}
