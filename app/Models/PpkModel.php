<?php

namespace App\Models;

use Exception;
use mysqli;
/**
 * PpkModel - PPK Management Model
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */

class PpkModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     *
     * @param mysqli|null $db Database connection (optional for backward compatibility)
     */
    public function __construct($db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            // Fallback to global db() helper function from bootstrap.php
            if (function_exists('db')) {
                $this->db = db();
            } else {
                throw new \Exception("Database connection not provided to PpkModel. Ensure bootstrap.php is loaded.");
            }
        }
    }

    /**
     * Mengambil data statistik untuk dashboard.
     */
    public function getDashboardStats()
    {
       $query = "SELECT
                    COUNT(*) as total,
                    SUM(CASE
                        WHEN posisiId IN (3, 5) AND statusUtamaId != 4 OR statusUtamaId IN (5, 6) AND posisiId IN (1) THEN 1
                        ELSE 0
                    END) as disetujui,
                    SUM(CASE
                        WHEN statusUtamaId = 4 THEN 1
                        ELSE 0
                    END) as ditolak,
                    SUM(CASE
                        WHEN posisiId = 4 THEN 1
                        ELSE 0
                    END) as pending
                FROM tbl_kegiatan";

        $result = mysqli_query($this->db, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return [
                'total' => $row['total'] ?? 0,
                'disetujui' => $row['disetujui'] ?? 0,
                'menunggu' => $row['menunggu'] ?? 0
            ];
        }
        return ['total' => 0, 'disetujui' => 0, 'menunggu' => 0];
    }

    /**
     * Mengambil daftar usulan untuk tabel dashboard.
     */
    public function getDashboardKAK()
    {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.jurusanPenyelenggara as jurusan,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as tanggal_pengajuan,
                    k.posisiId as posisi,
                    s.namaStatusUsulan as status
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.posisiId = 4
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($row['status'])) {
                    $row['status'] = ucfirst($row['status']);
                } else {
                    $row['status'] = 'Menunggu';
                }

                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';

                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Mengambil detail utama kegiatan.
     */
    public function getDetailKegiatan($kegiatanId)
    {
        $query = "SELECT 
                    k.*, 
                    kak.*,
                    k.tanggalMulai as tanggal_mulai,
                    k.tanggalSelesai as tanggal_selesai,
                    k.suratPengantar as file_surat_pengantar,
                    k.pemilikKegiatan as nama_pengusul,
                    k.namaPJ as nama_pj,
                    k.nip as nim_pj,
                    k.nimPelaksana as nim_pelaksana,
                    k.pemilikKegiatan as nama_pelaksana,
                    s.namaStatusUsulan as status_text
                FROM tbl_kegiatan k
                JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                LEFT JOIN tbl_user u ON u.userId = k.userId
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.kegiatanId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    /**
     * Mengambil indikator KAK.
     */
    public function getIndikatorByKAK($kakId)
    {
        $query = "SELECT bulan, indikatorKeberhasilan as nama, targetPersen as target FROM tbl_indikator_kak WHERE kakId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Mengambil tahapan pelaksanaan.
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
        return $data;
    }

    /**
     * Mengambil RAB (dikelompokkan berdasarkan kategori).
     */
    public function getRABByKAK($kakId)
    {
        $query = "SELECT r.*, cat.namaKategori 
                  FROM tbl_rab r
                  JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId
                  WHERE r.kakId = ?
                  ORDER BY cat.kategoriRabId ASC, r.rabItemId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[$row['namaKategori']][] = $row;
        }
        return $data;
    }

    /**
     * Menyetujui usulan dan meneruskan ke Wadir (PRIVATE METHOD).
     * Logika bisnis dan notifikasi akan ditangani oleh PpkService.
     */
    public function approveUsulan(int $kegiatanId, string $rekomendasi = ''): bool
    {
        $nextPosisi = 3;  // WADIR (setelah PPK, harus ke Wadir dulu sebelum Bendahara)
        $currentPosisi = 4; // PPK
        $statusProses = 1; // Disetujui (oleh PPK) dan lanjut ke Wadir sebagai status menunggu
        $userId = $_SESSION['user_id'] ?? null;

        mysqli_begin_transaction($this->db);

        try {
            // Update status kegiatan
            $query = "UPDATE tbl_kegiatan SET posisiId = ?, statusUtamaId = ? WHERE kegiatanId = ?";
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "iii", $nextPosisi, $statusProses, $kegiatanId);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update kegiatan");
            }
            mysqli_stmt_close($stmt);

            // Insert History
            $historyQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, changedByUserId, timestamp) VALUES (?, ?, ?, NOW())";
            $stmtHistory = mysqli_prepare($this->db, $historyQuery);
            mysqli_stmt_bind_param($stmtHistory, "iii", $kegiatanId, $statusProses, $userId);

            if (!mysqli_stmt_execute($stmtHistory)) {
                 throw new Exception("Gagal catat history");
            }

            $historyId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmtHistory);

            if (!empty($rekomendasi)) {
                $commentQuery = "INSERT INTO tbl_revisi_comment (progressHistoryId, komentarRevisi, targetTabel) VALUES (?, ?, 'ppk_rekomendasi')";
                $stmtComment = mysqli_prepare($this->db, $commentQuery);
                mysqli_stmt_bind_param($stmtComment, "is", $historyId, $rekomendasi);
                mysqli_stmt_execute($stmtComment);
                mysqli_stmt_close($stmtComment);
            }

            mysqli_commit($this->db);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("PPK _approveUsulan (Model) Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil data riwayat PPK.
     * Menampilkan kegiatan yang sudah pernah melalui tahap PPK (posisiId >= 4)
     * atau sudah mencapai status dana cair (statusUtamaId IN (5, 6))
     */
    public function getRiwayat()
    {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as tanggal_pengajuan,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.posisiId >= 4 OR k.statusUtamaId IN (5, 6) THEN 'Disetujui'
                        ELSE 'Diproses'
                    END as status
                  FROM tbl_kegiatan k
                  WHERE 
                    k.posisiId >= 3 OR k.statusUtamaId = 4 OR k.statusUtamaId IN (5, 6)
                  ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Mengambil data monitoring untuk PPK dengan filtering dan pagination.
     *
     * Method ini mengambil data kegiatan untuk monitoring dengan berbagai filter:
     * - 'menunggu': Hanya usulan yang posisiId = 4 DAN statusUtamaId = 1 (menunggu approval PPK)
     * - 'approved': Usulan yang sudah disetujui (posisiId >= 5)
     * - 'ditolak': Usulan yang ditolak (statusUtamaId = 4)
     * - 'in process': Usulan yang masih dalam proses
     *
     * @param int $page Halaman saat ini untuk pagination
     * @param int $perPage Jumlah item per halaman
     * @param string $search Kata kunci pencarian (nama kegiatan atau pengusul)
     * @param string $statusFilter Filter status: 'semua', 'menunggu', 'approved', 'ditolak', 'in process'
     * @param string $jurusanFilter Filter jurusan: 'semua' atau nama jurusan spesifik
     * @return array Array dengan key 'data' (list kegiatan) dan 'totalItems' (total records)
     */
    public function getMonitoringData($page, $perPage, $search, $statusFilter, $jurusanFilter)
    {
        $offset = ($page - 1) * $perPage;

        // Base Where Clause & Params Construction
        $whereClause = " WHERE 1=1";
        $types = "";
        $params = [];

        // Filter pencarian
        if (!empty($search)) {
            $whereClause .= " AND (k.namaKegiatan LIKE ? OR k.pemilikKegiatan LIKE ?)";
            $searchTerm = "%{$search}%";
            $types .= "ss";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Filter status
        if ($statusFilter !== 'semua') {
            if ($statusFilter === 'ditolak') {
                $whereClause .= " AND k.statusUtamaId = 4";
            } elseif ($statusFilter === 'approved') {
                $whereClause .= " AND k.posisiId >= 5 AND k.statusUtamaId != 4";
            } elseif ($statusFilter === 'menunggu') {
                // FIXED: Menunggu = posisi di PPK (4) DAN statusUtama = 1 (menunggu approval)
                $whereClause .= " AND k.posisiId = 4 AND k.statusUtamaId = 1";
            } elseif ($statusFilter === 'in process') {
                $whereClause .= " AND k.statusUtamaId != 4 AND k.posisiId < 5";
            } elseif ($statusFilter === 'lpj') {
                // Filter untuk LPJ: posisi di 1 atau 6, status 5 atau 6, dan dana sudah cair
                $whereClause .= " AND k.posisiId IN (1, 6) AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL";
            }
        }

        // Filter jurusan
        if ($jurusanFilter !== 'semua') {
            $whereClause .= " AND k.jurusanPenyelenggara = ?";
            $types .= "s";
            $params[] = $jurusanFilter;
        }

        // 1. Execute Count Query
        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k" . $whereClause;
        $stmtCount = mysqli_prepare($this->db, $countQuery);

        if ($types !== "") {
            mysqli_stmt_bind_param($stmtCount, $types, ...$params);
        }

        mysqli_stmt_execute($stmtCount);
        $totalResult = mysqli_stmt_get_result($stmtCount);
        $totalItems = ($totalResult) ? mysqli_fetch_assoc($totalResult)['total'] : 0;
        mysqli_stmt_close($stmtCount);


        // 2. Execute Data Query
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.createdAt as tanggal,
                    k.posisiId,
                    k.statusUtamaId,
                    l.statusId as lpj_status_id,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak' 
                        WHEN k.posisiId = 1 AND k.statusUtamaId NOT IN (4, 5, 6) THEN 'Pengajuan'
                        WHEN k.posisiId = 2 AND k.statusUtamaId != 4 THEN 'Verifikasi'
                        WHEN k.posisiId = 4 THEN 'ACC PPK'
                        WHEN k.posisiId = 3 THEN 'ACC WD'
                        WHEN k.posisiId IN (1) AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL AND l.statusId = 1 THEN 'LPJ'
                        WHEN k.posisiId = 5 OR (k.posisiId = 1 AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NULL) THEN 'Dana Cair'
                        ELSE 'Unknown'
                    END as tahap_sekarang,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN (k.posisiId >= 5 OR (k.posisiId = 1 AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL)) AND l.statusId = 3 THEN 'Approved'
                        WHEN k.posisiId = 4 AND k.statusUtamaId = 1 OR l.statusId = 1 THEN 'Menunggu'
                        ELSE 'In Process'
                    END as status
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_lpj l ON k.kegiatanId = l.kegiatanId" . $whereClause;
                  

        // Add ORDER BY and LIMIT
        $query .= " ORDER BY k.createdAt DESC LIMIT ? OFFSET ?";
        
        // Add limit/offset to params
        $typesWithLimit = $types . "ii";
        $paramsWithLimit = $params;
        $paramsWithLimit[] = $perPage;
        $paramsWithLimit[] = $offset;

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, $typesWithLimit, ...$paramsWithLimit);
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        mysqli_stmt_close($stmt);

        return [
            'data' => $data,
            'totalItems' => $totalItems
        ];
    }

    public function getListJurusanDistinct()
    {
        $query = "SELECT DISTINCT jurusanPenyelenggara as jurusan FROM tbl_kegiatan WHERE jurusanPenyelenggara IS NOT NULL AND jurusanPenyelenggara != '' ORDER BY jurusanPenyelenggara ASC";
        $result = mysqli_query($this->db, $query);
        $list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row['jurusan'];
            }
        }
        return $list;
    }
}
